<?php

/**
 * @Author: Sexy Phoenix
 * @Date:   2019-05-30 15:46:27
 * @Last Modified by:   Sexy Phoenix
 * @Last Modified time: 2019-08-31 15:30:36
 */
namespace App\Decorator\Bee;

use Carbon\Carbon;
use App\Services\Bee\YearService;
use Illuminate\Support\Facades\Redis;
use App\Repositories\Bee\ApplyRepository;
use App\Repositories\Bee\UserRepository as User;
use App\Models\Bee\Contracts\TypeConstantInterface as Type;
use App\Models\Bee\Contracts\ApplyConstantInterface as Apply;
use App\Contracts\Bee\ApplyDecoratorInterface;

class YearDecorator implements ApplyDecoratorInterface
{
	protected $item;

    protected $operator;

	protected $apply;

	protected $year;

    protected $redis;

    protected $updated;

	const HALF_DAY = 0.5;

	const TOTAL_HOURS_DAY = 8;

	const DURATION_HALF        = 4;

    const TIME_MORNING_START   = '08:45';
    const TIME_MORNING_END     = '12:00';
    const TIME_AFTERNOON_START = '13:15';
    const TIME_AFTERNOON_END   = '17:30';

    public function __construct(ApplyRepository $apply) 
    {
        $this->apply = $apply;
    }

    public function handle($items, $operator, $updated)
    {  
        $this->item     = $items;
        $this->operator = $operator;
        $this->updated  = $updated;
        $this->redis    = Redis::connection('cache');

        $this->updatedYear();

        if(Type::TYPE_YEAR_REST == current($this->item)['type_id']) {

            $this->getYearApplyInfo(current($this->item)['user_id']);

            $this->handleYearMeta();

            $this->updateYearMeta();

            $this->resetYearCache();
        }
    }
    
    /**
     * @Author   Sexy Phoenix
     * @DateTime 2019-05-31T14:17:12+0800
     * @return   save meta
     */
    private function updateYearMeta()
    {
        foreach ($this->item as $item) {
            
            $id = $item['id'];
            array_forget($item, 'id');

            $this->apply->updateById($id, $item);
        }
    }

    /**
     * @Author   Sexy Phoenix
     * @DateTime 2019-06-17T11:44:14+0800
     * @return   [type] reset year cache
     */
    private function updatedYear()
    {

        if($this->updated && in_array(
            Type::TYPE_YEAR_REST,
            array_pluck($this->updated, 'type_id')
        )) $this->resetYearCache();
    }

    /**
     * @Author   Sexy Phoenix
     * @DateTime 2019-05-31T14:17:12+0800
     * @return   update year cache
     */
    private function resetYearCache()
    {
        $user_id = $this->item[0]['user_id'];

        $this->redis->zremrangebyscore(ApplyRepository::BEE_USER_YEAR, $user_id, $user_id);

        $service = new YearService(new ApplyRepository);
        $year    = $service->initialize(User::getUserById($user_id), array_pluck($this->updated, 'id'));

        $this->redis->zadd(ApplyRepository::BEE_USER_YEAR, $user_id, serialize($year->getUserYear(true)));
    }

    /**
     * @Author   Sexy Phoenix
     * @DateTime 2019-05-31T09:47:34+0800
     * @param    [type] $userid
     * @return   [type] get user year
     */
    private function getYearApplyInfo($userid)
    {
        $_year_info = $this->apply->getYearApplyInfo([$userid]);

        $this->year = isset($_year_info[$userid]) ? $_year_info[$userid] : [];
    }

    private function handleYearMeta()
    {
        $this_year         = date('Y');
    	$is_rest_last_year = $this->isRestLastYear();

        $list = [];
    	foreach ($this->item as $item) {
            
            $_item           = $item;

            if($this->canAdmin()) {

                $_item['meta'] = $this->addYearToMeta(
                    $item,
                    unserialize($_item['meta'])['year']
                );
                
            } else {

                $duration_to_day = $item['duration'] / self::TOTAL_HOURS_DAY;

                //priority use last year's year
                if($is_rest_last_year && $this->year['last_vacation_left'] >= self::HALF_DAY) {

                    if($this->isSplitItem($duration_to_day)) {

                        $_item = $this->getSplitItem($item);
                        $list  = array_merge($list, $_item);

                        continue;

                    } else {

                        $this->year['last_vacation_left'] -= $duration_to_day;
                        $_item['meta'] = $this->addYearToMeta($item, $this->year['last_year']);
                    }
                } else {

                    $_item['meta'] = $this->addYearToMeta($item, $this_year);
                }

            }

    		$list[] = $_item;
    	}

    	$this->item = $list;
    }

    private function getSplitItem($item)
    {
    	$item['duration']   = self::DURATION_HALF;
        $am_item = $pm_item = $item;

        $am_item['meta']         = $this->addYearToMeta($item, $this->year['last_year']);
		$am_item['time_section'] = Apply::opt_am;
		$am_item['end_time']     = self::TIME_MORNING_END;

        $pm_item['meta']         = $this->addYearToMeta($item, date('Y'));
		$pm_item['time_section'] = Apply::opt_pm;
		$pm_item['start_time']   = self::TIME_AFTERNOON_START;

		$this->year['last_vacation_left'] -= self::DURATION_HALF;

		return [$am_item, $pm_item];
    }

    private function isSplitItem($dayCount)
    {
    	return $dayCount > $this->year['last_vacation_left'];
    }

    private function isRestLastYear()
    {
    	return $this->year['show_last']
            && Carbon::now()->between(
                    Carbon::parse($this->year['last_start_date']), 
                    Carbon::parse($this->year['last_end_date']
                )); 
    }

    /**
     * @Author   Sexy Phoenix
     * @DateTime 2019-05-31T10:49:37+0800
     * @param    [type] $item
     * @param    [type] $year
     */
    private function addYearToMeta($item, $year)
    {
    	$meta = isset($item['meta']) ? unserialize($item['meta']) : [];
        $type = $year % 2 == 0 ? Type::OLD_EVEN_YEAR_REST : Type::OLD_YEAR_REST;

    	return serialize(array_merge($meta, ['year' => $year, 'type' => $type]));
    }

    private function canAdmin()
    {   
        if(!$this->updated) return false;

        $User = app()->make('App\Models\Bee\User')->find($this->operator->id);

        return User::canAttend($User) || User::canAffairs($User);
    }    	
}