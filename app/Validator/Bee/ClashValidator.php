<?php
/**
 * @Author: Sexy Phoenix
 * @Date:   2019-05-22 15:07:08
 * @Last Modified by:   Sexy Phoenix
 * @Last Modified time: 2019-08-08 17:07:54
 */
namespace App\Validator\Bee;

use App\Services\Date\DateProcess;
use App\Contracts\Bee\ApplyValidatorInterface;
use App\Repositories\Bee\ApplyRepository;
use App\Repositories\Bee\UserRepository as User;
use App\Models\Bee\Contracts\ApplyConstantInterface as ApplyConstant;
use App\Models\Bee\Contracts\TypeConstantInterface as TypeConstant;

class ClashValidator implements ApplyValidatorInterface
{
	private $apply;

    private $items;
    
    private $itemDates;

    private $appling;

    private $updated_id;

    private $errorDate = [];

    private $errorPrefix = [];

    public function __construct(ApplyRepository $apply) 
    {
        $this->apply = $apply;
    }
    
    public function validate($item, $updated)
    {  
        $this->items      = $item;
        $this->updated_id = array_pluck($updated, 'id');
        
        $this->handleItemDate();

        $this->handleAppliesConflict();

        if(!$this->updated_id) $this->handleBusinessMeta();

        if($this->errorDate) {

            return implode('或', array_unique($this->errorPrefix))
                .' '
                .implode('、', $this->errorDate)
                .trans('bee.apply.clash');
        }
        
        return true;
    }

    /**
     * @Author   Sexy Phoenix
     * @DateTime 2019-06-10T09:23:26+0800
     * @return   get apply & handle conflict
     */
    public function handleAppliesConflict()
    {
        $this->getAppliedList();

        $this->applyConflictValidate();
    }   

    public function handleBusinessMeta()
    {
        if(TypeConstant::TYPE_CHU_CHAI == $this->items[0]['type_id']) {

            $this->handlePeer();      
        }
    }

    /**
     * @Author   Sexy Phoenix
     * @DateTime 2019-05-23T14:22:58+0800
     * @return   get appling record
     */
    public function getAppliedList()
    {

        $this->appling = $this->apply->getApplingByDate(

            $this->items[0]['user_id'],
            $this->itemDates,
            $this->updated_id
        );
    }
    
    /**
     * @Author   Sexy Phoenix
     * @DateTime 2019-05-24T10:44:07+0800
     * @return   [type] one apply validate by db applies
     */
    public function applyConflictValidate()
    {
        if($this->appling->count()) {
            
            $date_to_keys = $this->appling->groupBy('apply_date')->toArray();
            foreach ($this->items as $item) {
                
                $_apply_date = $item['apply_date'];
                if(array_key_exists($_apply_date, $date_to_keys)) {
                    
                    $this->isConflict($item, $date_to_keys[$_apply_date]);
                }
            }
        }
    }
    
    /**
     * @Author   Sexy Phoenix
     * @DateTime 2019-05-24T10:48:31+0800
     * @param    [type] $apply
     * @param    array $applyList
     * @return   boolean
     */
    public function isConflict($apply, array $applyList)
    {
        $type_id = $apply['type_id'];

        if($this->getAllNotRules($type_id)) return;

        $name = User::getUserById($applyList[0]['user_id'])->name;

        foreach ($applyList as $item) {

            if($this->getAllNotRules($item['type_id'])) continue;

            if(!$this->applyIntersect($apply, $item)) {

                $this->errorDate[]   = $apply['apply_date'];
                $this->errorPrefix[] = $name;
            }
        }
    }
    
    /**
     * @Author   Sexy Phoenix
     * @DateTime 2019-06-10T10:31:42+0800
     * @return   peer conflict
     */
    protected function handlePeer()
    {
        $item = $this->items[0];
        $meta = unserialize($item['meta']);

        if($meta['team'] && $meta['peer']) {
            
            foreach ($meta['peer'] as $peer) {
                
                foreach ($this->items as &$item) {

                    array_set($item, 'user_id', $peer);
                }

                $this->handleAppliesConflict();                
            }
        }
    }
    
    /**
    * 两两相交检测
    * array(0,0),array(0,1),array(0,2),array(0,3)
    * array(1,0),array(1,1),array(1,2),array(1,3)
    * array(2,0),array(2,1),array(2,2),array(2,3)
    * array(3,0),array(3,1),array(3,2),array(3,3)
    */  
    private function applyIntersect($newApply, $oldApply) 
    {

        $sections  = array($newApply['time_section'], $oldApply['time_section']);
        $sections  = array_unique($sections);

        //除其他外，时段相等必定冲突
        if(1 == count($sections) && (ApplyConstant::opt_other != current($sections))) return false;
        
        sort($sections); //升序排序

        //其他检测（特殊性）
        if(in_array(ApplyConstant::opt_other, $sections) && !$this->timeNoCollision($newApply, $oldApply)) return false;

        //全天检测（与上午、下午冲突）
        if(in_array(ApplyConstant::opt_all, $sections)) return false;

        return true;        
    }
    
    /**
     * @Author   Sexy Phoenix
     * @DateTime 2019-05-24T10:49:42+0800
     * @param    [type] $type
     * @return   [type] 迟到、早退不冲突
     */
    private function getAllNotRules($type)
    {
        return in_array($type, array(TypeConstant::TYPE_CHI_DAO, TypeConstant::TYPE_ZAO_TUI));
    }

    /**
    * 时间无碰撞
    */  
    private function timeNoCollision($new, $old) 
    {

        return $this->timeToNumber($new['start_time']) >= $this->timeToNumber($old['end_time'])
                 || $this->timeToNumber($new['end_time']) <= $this->timeToNumber($old['start_time']);
    }
    
    /**
     * @Author   Sexy Phoenix
     * @DateTime 2019-05-24T10:50:13+0800
     * @param    [type] $time
     * @return   [type] time transform number
     */
    private function timeToNumber($time)
    {
        return intval(str_replace(':', '', $time));
    }

    /**
     * @Author   Sexy Phoenix
     * @DateTime 2019-05-22T17:08:37+0800
     * @param    [type] $item [2019-04-20, 2019-05-20, 2019-05-21]
     * @return   [201904, 201905]
     */
    private function handleItemDate()
    {
        $this->itemDates = array_unique(DateProcess::dateFormat(array_pluck($this->items, 'apply_date'), 'Y-m-d'));
    }
}