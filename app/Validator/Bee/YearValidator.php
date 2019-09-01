<?php
/**
 * @Author: Sexy Phoenix
 * @Date:   2019-05-24 13:33:08
 * @Last Modified by:   Sexy Phoenix
 * @Last Modified time: 2019-06-17 09:59:35
 */
namespace App\Validator\Bee;

use Carbon\Carbon;
use App\Contracts\Bee\ApplyValidatorInterface;
use App\Repositories\Bee\ApplyRepository;
use App\Models\Bee\Contracts\TypeConstantInterface as Type;
use App\Models\Bee\Contracts\ApplyConstantInterface as Apply;

class YearValidator implements ApplyValidatorInterface
{
	private $apply;
    
    private $item;
    
    private $error;

    private $year;

    private $updated;

    const TOTAL_HOURS_DAY = 8;

    const ALL_DAY  = 1;
    const HALF_DAY = 0.5;

    public function __construct(ApplyRepository $apply) 
    {
        $this->apply = $apply;
    }
    
    public function validate($item, $updated)
    {  
        $this->item     = $item;
        $this->updated = $updated;

        if(Type::TYPE_YEAR_REST == current($this->item)['type_id']) {

            $this->getYearApplyInfo(current($this->item)['user_id']);

            if($this->isYearEnough()) return $this->error;
        }

        return true;
    }
    
    /**
     * @Author   Sexy Phoenix
     * @DateTime 2019-05-31T09:47:15+0800
     * @param    [type] $userid
     * @return   [type] get user year
     */
    private function getYearApplyInfo($userid)
    {
        $_year_info = $this->apply->getYearApplyInfo([$userid]);

        $this->year = isset($_year_info[$userid]) ? $_year_info[$userid] : [];
    }
    
    /**
     * @Author   Sexy Phoenix
     * @DateTime 2019-05-28T14:02:58+0800
     * @return   boolean true
     */
    private function isYearEnough()
    {
        if($this->year) {

           $left_total = $this->leftYearTotal() + $this->getUpdatedTotal();
           
           if($left_total < $this->dayCalculation()) {

                $this->error = $this->year['show_desc'];
           }

        } else {

            $this->error = trans('bee.year.no');
        }

        return $this->error ? true : false;
    } 
    
    /**
     * @Author   Sexy Phoenix
     * @DateTime 2019-05-28T14:02:33+0800
     * @return   last year + this year
     */
    public function leftYearTotal()
    {
        $total = 0;
        if($this->year['show_last']
            && Carbon::now()->between(
                    Carbon::parse($this->year['last_start_date']), 
                    Carbon::parse($this->year['last_end_date']
                ))
        ) {
            $total += $this->year['last_vacation_left'];
        }

        if($this->year['show_vacation']
            && Carbon::now()->between(
                    Carbon::parse($this->year['start_date']), 
                    Carbon::parse($this->year['end_date']
                ))
        ) {
            $total += $this->year['vacation_left'];
        }

        return $total;     
    }

    /**
     * @Author   Sexy Phoenix
     * @DateTime 2019-06-14T13:57:12+0800
     * @return   exclude update's total
     */
    public function getUpdatedTotal()
    {
        return $this->apply->getUpdatedYearDuration(

            $this->item[0]['user_id'], 
            array_pluck($this->updated, 'id')
        ) / self::TOTAL_HOURS_DAY;
    }

    /**
     * @Author   Sexy Phoenix
     * @DateTime 2019-05-24T13:57:36+0800
     * @return   [type] day count
     */
    private function dayCalculation()
    {
        $result = 0;
        foreach ($this->item as $item) {
            
            $result += (Apply::opt_all == $item['time_section'] ? self::ALL_DAY : self::HALF_DAY);
        }

        return $result;
    }
}