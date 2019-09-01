<?php 

namespace App\Services\Bee;

use Carbon\Carbon;
use App\Models\Bee\Region;
use App\Models\Bee\Type;
use App\Http\Response\Bee\ApplyResponse;
use App\Repositories\Bee\ApplyRepository;
use App\Repositories\Bee\TimecardRepository as Timecard;
use App\Repositories\Bee\UserRepository as User;
use App\Jobs\Bee\ApplyFlow;
use App\Services\Bee\Traits\ApplyTrait;
use App\Models\Bee\Contracts\ApplyConstantInterface as ApplyConstant;
use App\Models\Bee\Contracts\TypeConstantInterface as TypeConstant;
use App\Models\Bee\Contracts\FlowConstantInterface as FlowConstant;

class ApplyService
{
    use ApplyTrait;

    //public $request; //post data
    public $apply; //apply repository

    public $items; 

    public $validators; //validator
    
    public $error = [];

    public $updated; //for updated

    const TIME_MORNING_START   = '08:45';
    const TIME_MORNING_END     = '12:00';
    const TIME_AFTERNOON_START = '13:15';
    const TIME_AFTERNOON_END   = '17:30';

    const DURATION_OTHER       = 1;
    const DURATION_HALF        = 4;
    const DURATION_ALL         = 8;

    public function __construct(ApplyRepository $apply, array $validators = []) 
    {

        //$this->request = $request;
        $this->apply      = $apply;
        $this->validators = $validators;
    }
    
    /**
     * @Author   Sexy Phoenix
     * @DateTime 2019-05-22T16:44:49+0800
     * @param    [type] $post
     * @return   handle apply
     */
    public function storeApply($post, array $updated = [])
    {
        $this->updated = $updated;
        $this->generateApplies($post);

        $this->process();

        if($this->error) return $this->error;

        $this->itemSortByDate();

        //queue handle workflow & meta
        dispatch(new ApplyFlow(

            auth('api')->user(), 
            $this->apply->store($this->items, $this->updated),
            $this->updated
        ));

        if($this->updated) $this->resetParentId(array_pluck($this->updated, 'id'));

        return true;
    }

    /**
     * @Author   Sexy Phoenix
     * @DateTime 2019-06-18T13:51:17+0800
     * @param    [type] $request
     * @return   [type] apply list
     */
    public function getApplyList($request)
    {
        return (new ApplyResponse($this->apply, $request))->getResponseList();
    }
    
    public function getFilterApply($filter, $page) 
    {
        return $this->apply->getApplyList($filter, $page);
    }

    public function getApplyIds($id)
    {
        if(!is_array($id)) return $this->singleId($id);
        
        $apply_ids = [];
        foreach ($id as $d) {
            
            $apply_ids = array_merge($apply_ids, $this->singleId($d));
        }

        return array_unique($apply_ids);
    }

    public function deleteApply($id)
    {   
        $result = $this->handleWorkFlow(
            $id,
            ApplyConstant::status_deleted,
            '',
            0,
            ApplyConstant::stage_deleted
        );

        if($result) {

            $this->resetParentId($id);
            //Timecard::delete($id);
        }

        return true;
    }

    public function cancelApply($user, $post)
    {   
        $applies = $post['applyIds'];

        $result  = $this->simpleHandleWorkFlow(
            $applies,
            [
                'stage_id'     => ApplyConstant::stage_filtered,
                'status_id'    => ApplyConstant::status_dept_adject,
                'flow_action'  => ApplyConstant::Action_DeptAdject,
                'flow_user_id' => $user,
                'is_cancel'    => 1
            ]
        );

        if($result) $this->resetParentId($applies);

        return true;
    }

    /**
     * @Author   Sexy Phoenix
     * @DateTime 2019-06-18T17:10:20+0800
     * @param    [type] $post
     * @return   [type] agree
     */
    public function passChargeSuggestion($post)
    {   
        $applies = $post['applyIds'];

        if(!$this->stepFlowPush($post)) {

            $apply_data = $this->apply->getApplyListById($applies);

            $user_regions = User::getUserById(
                array_pluck($apply_data, 'user_id')
            )
            ->pluck('region', 'user_id')
            ->all();

            $id_user = array_pluck($apply_data, 'user_id', 'id'); 
            foreach ($applies as $id) {

                $user_id = $id_user[$id];
                $flow    = $this->getChargeNextStep($user_regions[$user_id]);
                                
                $this->apply->updateById(
                    $id, 
                    $flow
                );

                //Timecard::saveWorkFlow($id, $flow['status_id']);
            }
        }

        $this->resetParentId($applies);
    }

    /**
     * @Author   Sexy Phoenix
     * @DateTime 2019-06-18T17:10:36+0800
     * @return   reject
     */
    public function rejectChargeSuggestion($post)
    {
        $applies = $post['applyIds'];

        $result = $this->handleWorkFlow(

            $applies,
            ApplyConstant::status_dept_adject_reject
        );

        if($result) {

            $this->resetParentId($applies);
            // foreach ($applies as $id) {
                
            //     Timecard::saveWorkFlow($id, ApplyConstant::status_dept_adject_reject);
            // }
        }
    }

    /**
     * @Author   Sexy Phoenix
     * @DateTime 2019-06-20T15:34:20+0800
     * @param    [type] $post
     * @return   [type]
     */
    public function passAttendSuggestion($post)
    {   
        $applies = $post['applyIds'];

        $result = $this->handleWorkFlow(

            $applies,
            ApplyConstant::status_admin_adject,
            ApplyConstant::Action_AdminAdject
        );

        if($result) {

            $this->resetParentId($applies);
            // foreach ($applies as $id) {
                
            //     Timecard::saveWorkFlow($id, ApplyConstant::status_admin_adject);
            // }
        }
    }

    /**
     * @Author   Sexy Phoenix
     * @DateTime 2019-06-18T17:10:36+0800
     * @return   reject
     */
    public function rejectAttendSuggestion($post)
    {
        $applies = $post['applyIds'];

        $result = $this->handleWorkFlow(

            $post['applyIds'],
            ApplyConstant::status_attend_adject_reject
        );

        if($result) {

            $this->resetParentId($applies);
            // foreach ($applies as $id) {
                
            //     Timecard::saveWorkFlow($id, ApplyConstant::status_attend_adject_reject);
            // }
        }       
    }

    /**
     * @Author   Sexy Phoenix
     * @DateTime 2019-06-20T15:35:01+0800
     * @param    [type] $post
     * @return   [type]
     */
    public function passAffairsSuggestion($post)
    {   
        $applies = $post['applyIds'];

        $result = $this->handleWorkFlow(

            $applies,
            ApplyConstant::status_finished,
            ApplyConstant::Action_ApplyFinished,
            0,
            ApplyConstant::stage_finished
        );

        if($result) {

            $this->resetParentId($applies);

            $apply_data = $this->apply->getApplyListById($applies, ['id', 'user_id', 'is_cancel', 'type_id']);
            $apply_cancel = array_pluck($apply_data, 'is_cancel', 'id');

            $cancel_list = [];
            foreach ($applies as $id) {
                
                if(1 == $apply_cancel[$id]) $cancel_list[] = $id;

                //Timecard::saveWorkFlow($id, ApplyConstant::status_finished);
            }

            //if($cancel_list) Timecard::delete($cancel_list);

            app()->make('App\Services\Bee\EmailService')->handle($apply_data);
        }
    }

    /**
     * @Author   Sexy Phoenix
     * @DateTime 2019-06-20T15:35:06+0800
     * @param    [type] $post
     * @return   [type]
     */
    public function rejectAffairsSuggestion($post)
    {
        $applies = $post['applyIds'];

        $result = $this->handleWorkFlow(

            $applies,
            ApplyConstant::status_admin_adject_reject
        );

        if($result) {

            $this->resetParentId($applies);
            // foreach ($applies as $id) {
                
            //     Timecard::saveWorkFlow($id, ApplyConstant::status_admin_adject_reject);
            // }
        }      
    }

    /**
     * @Author   Sexy Phoenix
     * @DateTime 2019-06-18T17:10:46+0800
     * @param    [type] $post
     * @return   [type] push
     */
    public function stepFlowPush($post)
    {   
        if(!isset($post['step']) || !isset($post['user_id']) || !$post['user_id']) return false;

        if(FlowConstant::STEP_APPROVAL == $post['step']) {

            $result = $this->handleWorkFlow(

                $post['applyIds'],
                ApplyConstant::status_dept_adject,
                ApplyConstant::Action_DeptAdject,
                $post['user_id']
            );            
        }

        return FlowConstant::STEP_APPROVAL == $post['step'] ? true : false;
    }

    public function getChargeNextStep($map)
    {
        if(strpos($map, 'cn-320000-320100')) { //驻外由助理处理

            $status_id  = ApplyConstant::status_admin_adject;
            $cur_action = ApplyConstant::Action_AdminAdject;
        } else {

            $status_id  = ApplyConstant::status_attend_adject;
            $cur_action = ApplyConstant::Action_AttendAdject;                    
        }

        return ['status_id' => $status_id, 'flow_action' => $cur_action, 'flow_user_id' => 0];
    }

    public function simpleHandleWorkFlow($applies, $updated)
    {
        return $this->apply->commonOperate(
            $applies,
            $updated
        );        
    }

    public function handleWorkFlow($applies, $status, $action = '', $user = 0, $stage = ApplyConstant::stage_filtered)
    {
        return $this->apply->commonOperate(
            $applies,
            [
                'stage_id'     => $stage,
                'status_id'    => $status,
                'flow_action'  => $action,
                'flow_user_id' => $user
            ]
        );        
    }

    /**
     * @Author   Sexy Phoenix
     * @DateTime 2019-05-22T16:44:59+0800
     * @param    [type] $post
     * @return   generate apply array
     */
    private function generateApplies($data)
    {
        $post       = $data;
        $user_id    = $post['user']['user_id'];

        $time                 =  $this->initPostTime($post);
        $post['start_time']   = $time['start_time'];
        $post['end_time']     = $time['end_time'];
        
        $post['time_section'] = $this->initSectionTime($post);
        $post['duration']     = $this->initDuration($post);

        $user                 = User::getUserById($user_id);
        $manager_id           = $user->manager;
        $region               = $user->region;

        foreach ($post['date'] as $date) {

            $this->items[] = [

                'user_id'      => $user_id,
                'type_id'      => $post['type'],
                'apply_date'   => $date,
                'time_section' => $post['time_section'],
                'start_time'   => $post['start_time'],
                'end_time'     => $post['end_time'],
                'desc'         => $post['desc'],
                'handover'     => $post['handover'],
                'attach'       => $post['attach'],
                'duration'     => $post['duration'],
                'meta'         => $this->initMeta($post['type'], $post),
                'stage_id'     => ApplyConstant::stage_filtered,
                'status_id'    => ApplyConstant::status_dept_adject,
                'flow_user_id' => $user->manager,
                'flow_action'  => ApplyConstant::Action_DeptAdject,
                'region'       => $user->region,
                'crop_id'      => $user->crop_id
            ];
        }
    }
    
    /**
     * @Author   Sexy Phoenix
     * @DateTime 2019-05-23T11:13:41+0800
     * @return   validate request
     */
    private function process()
    {
        foreach ($this->validators as $validator) {
            
            $result = $validator->validate($this->items, $this->updated);

            if(true !== $result) $this->setError($result);
        }
    }
     
    /**
     * @Author   Sexy Phoenix
     * @DateTime 2019-05-23T11:13:04+0800
     * @param    [type] set $error info
     */
    private function setError($error)
    {
        if(!in_array($error, $this->error)) $this->error[] = $error;
    }
    
    /**
     * @Author   Sexy Phoenix
     * @DateTime 2019-05-24T11:50:50+0800
     * @param    [type] $post
     * @return   [type] get time
     */
    private function initPostTime($post)
    {
        $section    = $post['time_section'];
        $start_time = $post['start_time'];
        $end_time   = $post['end_time'];

        if($section != ApplyConstant::opt_other) {

            $start_time = in_array($section, array(ApplyConstant::opt_all, ApplyConstant::opt_am)) 
                ? self::TIME_MORNING_START
                : self::TIME_AFTERNOON_START;

            $end_time = in_array($section, array(ApplyConstant::opt_all, ApplyConstant::opt_pm)) 
                ? self::TIME_AFTERNOON_END
                : self::TIME_MORNING_END;

        }

        return array('start_time' => $start_time, 'end_time' => $end_time);
    }

    /**
     * @Author   Sexy Phoenix
     * @DateTime 2019-05-29T09:13:10+0800
     * @param    [type] $post
     * @return   [type] cost 
     */
    private function initDuration($post)
    {
        if(isset($post['duration'])) return $post['duration'];

        $section    = $post['time_section'];
        $type       = $post['type'];
        $start_time = $post['start_time'];
        $end_time   = $post['end_time'];

        if(in_array($type, array(TypeConstant::TYPE_CHI_DAO, TypeConstant::TYPE_ZAO_TUI))) return self::DURATION_OTHER;

        if(in_array($section, array(ApplyConstant::opt_am, ApplyConstant::opt_pm))) return self::DURATION_HALF;

        if(ApplyConstant::opt_all == $section) return self::DURATION_ALL;  

        if(self::TIME_MORNING_END <= $start_time && self::TIME_AFTERNOON_START >= $start_time) {

            $start_time = self::TIME_AFTERNOON_START;
        }

        if(self::TIME_MORNING_END <= $end_time && self::TIME_AFTERNOON_START >= $end_time) {

            $end_time = self::TIME_MORNING_END;
        }

        $diff_stamp  = strtotime($end_time) - strtotime($start_time); 

        if(self::TIME_MORNING_END > $start_time && self::TIME_AFTERNOON_START < $end_time) $diff_stamp -= 4500;

        return $diff_stamp > 0 ? round($diff_stamp/3600, 2) : 0;
    }
    
    /**
     * @Author   Sexy Phoenix
     * @DateTime 2019-05-29T09:12:56+0800
     * @param    [type] $post
     * @return   [type] time_section
     */ 
    private function initSectionTime($post)
    {
        $section    = $post['time_section'];
        $start_time = $post['start_time'];
        $end_time   = $post['end_time'];

        if(self::TIME_MORNING_START == $start_time 
            && self::TIME_MORNING_END == $end_time
        ) return ApplyConstant::opt_am;

        if(self::TIME_AFTERNOON_START == $start_time 
            && self::TIME_AFTERNOON_END == $end_time
        ) return ApplyConstant::opt_pm;

        if(self::TIME_MORNING_START == $start_time 
            && self::TIME_AFTERNOON_END == $end_time
        ) return ApplyConstant::opt_all;

        return $section;       
    }

    /**
     * @Author   Sexy Phoenix
     * @DateTime 2019-06-04T10:57:37+0800
     * @param    [type] $type
     * @param    [type] $meta
     * @return   [type] init meta by type
     */
    public function initMeta($type, $item)
    {
        if(!isset($item['meta'])) return serialize(['type' => Type::getMappingType($type)]);

        $meta = $item['meta'];

        $attr = [];
        if(TypeConstant::TYPE_GONG_CHU == $type) {

            $attr['food'] = $meta['food'];
        } else if(TypeConstant::TYPE_CHU_CHAI == $type) {

            $attr['team']   = $meta['team'];
            $attr['peer']   = $meta['peer'];
            $attr['budget'] = $meta['budget'];
            $attr['destination']   = $meta['destination'];
            
        } else if(TypeConstant::TYPE_JIA_BAN == $type) {

            if(isset($meta['work']))  $attr['work'] = $meta['work'];

            $attr['food']   = $meta['food'];

        }  else if(TypeConstant::TYPE_YEAR_REST == $type) {

            $attr['year']   = $meta['year'];
        }  else if(TypeConstant::TYPE_CHAN_JIA == $type) {

            $attr['birth']   = $meta['birth'];
        }     

        return serialize(array_merge($attr, ['type' => Type::getMappingType($type)]));
    }

    /**
     * @Author   Sexy Phoenix
     * @DateTime 2019-05-30T10:27:06+0800
     * @return   [type] sort by apply date
     */
    private function itemSortByDate() 
    {
        $this->items = array_values(array_sort($this->items, function ($item) {

            return $item['apply_date'];
        }));
    }
}