<?php

/**
 * @Author: Sexy Phoenix
 * @Date:   2019-06-11 09:31:28
 * @Last Modified by:   Sexy Phoenix
 * @Last Modified time: 2019-08-31 15:43:36
 */
namespace App\Http\Response\Bee;

use App\Models\Bee\Type;
use App\Repositories\Bee\ApplyRepository;
use App\Repositories\Bee\ApplyLockRepository as ApplyLock;
use App\Repositories\Bee\UserRepository as User;
use App\Repositories\Bee\ApplyFlowRepository as ApplyFlow;
use App\Models\Bee\Contracts\ApplyConstantInterface as ApplyConstant;
use App\Models\Bee\Contracts\TypeConstantInterface as TypeConstant;
use App\Models\Bee\Contracts\TypeAttrConstantInterface as TypeAttrConstant;
use Illuminate\Support\Facades\Storage;

class ApplyResponse 
{
	protected $apply;

	protected $request;

	protected $filter;

	protected $items; //has page

	protected $users;

    protected $regions;

    protected $lock;

    protected $hasAttend;

    protected $hasAffairs;

    public function __construct(ApplyRepository $apply, $request) 
    {
    	$this->apply   = $apply;
        $this->request = $request;
    }

    public function getResponseList()
    {
    	$this->filterRequest();

        $this->hasAttend  = User::canAttend(auth('api')->user());
        $this->hasAffairs = User::canAffairs(auth('api')->user());

    	$this->setItems($this->getApplyData());

    	return $this->handleItemData();
    }

    protected function handleItemData()
    {
    	if($this->items['data']) {

    		$this->getUserMappingWithId(); //use & user_id mapping

    		$children_data = $this->getChildByParent();

            $children_dates = $this->getFieldList($children_data, 'apply_date');
            $this->lock = ApplyLock::getLockWithDate(
                $this->handleDateLock(
                    array_merge(
                        $this->getFieldList($this->items['data'], 'apply_date'),
                        $children_dates
                    )
                )
            )
            ->pluck('status_id', 'lock_date')
            ->all();

            $this->regions = User::regionTransform($this->getRegionMappingWithUser());

    		$apply_id_list = array_merge(
    			$this->getFieldList($this->items['data']),
    			$this->getFieldList($children_data)
    		);

    		$flows = $this->getFlowById(
    			$apply_id_list
    		)
    		->groupBy('apply_id')
    		->toArray();

    		$children = collect(
    			$this->transform($children_data, $flows)
    		)
    		->groupBy('parent_id')
    		->toArray();

    		$this->items['data']     =  $this->transform($this->items['data'], $flows, $children_dates, $children);
    	}
    	
    	return $this->items;
    }
    
    protected function transform($items, array $flows, array $children_date = [], array $children = [])
    {
   		$result = [];
        if($items) {
            
            $upload_url = url('uploads/bee');
	    	foreach ($items as $item) {

	    		$id         = $item['id'];
                $apply_date = $item['apply_date'];
                $parent_id  = $item['parent_id'];

                if($children_date && $parent_id > 0 && in_array($apply_date, $children_date)) continue;
                $isChildren = $children && isset($children[$id]) ? true : false;

                $user          = $this->getUserObject($item['user_id']);
                $meta          = $this->handleMeta(unserialize($item['meta']));

                $handovers     = []; 
                if($item['handover']) {

                    $handovers[] = $this->getUserObject($item['handover']);
                }

                $peers = [];
                if(isset($meta['team']) && $meta['team'] && $meta['peer']) {

                    foreach ($meta['peer'] as $peer) {

                        $peers = array_merge($peers, [$this->getUserObject($peer)]);
                    } 
                }

                $year_month  = str_replace('-', '', mb_substr($apply_date, 0, 7));

                $attach      = $item['attach'] ? $upload_url.'/'.$item['attach'] : '';
                $_flows      = isset($flows[$id]) ? $flows[$id] : [];
                $_item       = [
		            'id'           => $id,
		            'user'         => $user,
		            'date'         => [$apply_date],
		            'time_section' => $item['time_section'],
                    'start_time'   => $item['start_time'],
                    'end_time'     => $item['end_time'],
                    'duration'     => $item['duration'],
		            'type'         => $item['type_id'],
		            'desc'         => $item['desc'],
		            'status_id'    => $item['status_id'],
                    'handover'     => $item['handover'] ? $item['handover'] : '',
                    'handovers'    => $handovers,
                    'peers'        => $peers,
                    'attach'       => $attach,
		            'flows'        => $_flows,
		            'parent_id'    => $item['parent_id'],
                    'meta'         => $meta,
                    'meta_tag'     => $this->handleMetaTag($item['type_id'], $meta, $year_month, $item),
                    'push_tag'     => $this->handlePushTag($_flows),
                    'address'      => $this->regions[$item['region']],
                    'is_cancel'    => $item['is_cancel'],
                    'can_cancel'   => $this->canCancel($item['user_id'], $year_month, $item['is_cancel'], $item['status_id']),
                    'can_edit'     => $this->canEdit($item['user_id'], $year_month, $item['status_id']),
                    'can_delete'   => $this->canDelete($year_month, $item['status_id'], $item['is_cancel'], $_flows),
                    'can_charge'   => $this->canCharge($item['flow_user_id'], $year_month, $item['status_id']),
                    'can_attend'   => $this->canAttend($year_month, $item['status_id']),
                    'can_affairs'   => $this->canAffairs($year_month, $item['status_id']),
                    'updated'      => [$apply_date => ['id' => $id, 'type_id' => $item['type_id']]]
                ];

                if($isChildren) {

                    $_children         = $children[$id];
                    $_temp_item        = $_item;

                	$_item['children'] = $this->itemSortById(array_merge([array_set($_temp_item, 'id', -$id)], $_children));
                    $_item['updated']  = array_merge($this->getChildUpdated($_children), $_item['updated']);

                    // [date1, date2]
                    $_item['date'] = array_merge(
                        $_item['date'],
                        array_map(function($item){
                           return current($item['date']); 
                       }, $_children)
                    );
                    sort($_item['date']);
                }

                $result[] = $_item;
	    	}       	
        }

    	return $result;
    }

    protected function getFlowById($id)
    {   
        $flow_data = ApplyFlow::getFlowByApplyId($id);

        if($flow_data->count()) {

            $flow_data->transform(function ($item, $key) {

                $action_user_id = $item['action_user_id'];

                if(isset($this->users[$action_user_id])) {
                    $item->action_user_name = $this->users[$action_user_id];
                } else {

                    $item->action_user_name = $this->users[$action_user_id] = User::getUserById($action_user_id)->name;
                }
                $item->action_data = unserialize($item->action_data);
                return $item;
            });
        }
        return $flow_data;
    }

    protected function filterRequest()
    {
        $this->setFilterParentId(0);

        if($this->request->has('user_id') ) {

            $this->setFilterUserId($this->request->user_id);
        }

        if($this->request->has('apply_id')) {

            $this->setFilterApplyId($this->request->apply_id);
        }

        if($this->request->has('flow_user_id')) {

            $this->setFilterFlowUserId($this->request->flow_user_id);
        }

        if($this->request->has('status')) {

            $this->setFilterStatus($this->request->status);
        }

        if($this->request->has('flow_action')) {

            $this->setFilterFlowAction($this->request->flow_action);
        }

        if($this->request->has('region')) {

            $this->setFilterRegion($this->request->region);
        }

        if($this->request->has('crop')) {

            $this->setFilterCrop($this->request->crop);
        }

        if($this->request->has('dates')) {

            $this->setFilterDates($this->request->dates);
        }

        if($this->request->has('type')) {

            $this->setFilterType($this->request->type);
        }

        if($this->request->has('desc')) {

            $this->setFilterDesc($this->request->desc);
        }

        $this->setPageSize($this->request->pagesize);
    }

    protected function getApplyData($page = true)
    {
    	return $this->apply->getApplyList($this->filter, $page);
    }

    protected function getChildByParent()
    {
    	$this->setFilterParentId($this->getFieldList($this->items['data']));

        //array_forget($this->filter, 'apply_id');

    	return $this->getApplyData(false);
    }

    protected function getUserMappingWithId()
    {

    	$this->users = User::getUserById(
    		$this->getAllUserId()
    	)
    	->pluck('name', 'user_id')
    	->all();
    }

    protected function getRegionMappingWithUser()
    {
        return array_unique(array_pluck($this->items['data'], 'region'));
    }

    protected function getAllUserId()
    {
        $result = [];
        foreach ($this->items['data'] as $item) {
            
            $meta = unserialize($item['meta']);
            
            $result[] = $item['user_id'];
            if($item['handover']) $result[] = $item['handover'];
            if(isset($meta['team']) && $meta['team'] && $meta['peer']) $result = array_merge($result, $meta['peer']); 
        }

        return array_unique($result);
    }

    protected function getFieldList($item, $field = 'id')
    {
    	return array_pluck($item, $field);
    }

    protected function getUserObject($user_id)
    {
        $user          = new \stdClass;
        $user->user_id = $user_id;
        $user->name    = $this->users[$user_id];

        return $user;
    }
    
    protected function getChildUpdated($items)
    {
        $updated = [];
        foreach ($items as $item) {

           $updated[current($item['date'])] = [

                'id'      => $item['id'], 
                'type_id' => $item['type']
            ];
        }

        return $updated;
    }

    protected function itemSortById($items) 
    {
        return array_values(array_sort($items, function ($item) {

            return $item['id'];
        }));
    }

    protected function handleDateLock($date) 
    {
        
        return array_unique(
            array_map(function($v) {

                return str_replace('-', '', mb_substr($v, 0, 7));
            }, $date)
        );
    }

    public function handleMeta($meta)
    {
        $_meta = $meta;

        if(!isset($_meta['food'])) $_meta['food'] = false;

        if(!isset($_meta['destination'])) $_meta['destination'] = '';

        if(!isset($_meta['team'])) $_meta['team'] = true;

        if(!isset($_meta['peer'])) $_meta['peer'] = [];

        if(!isset($_meta['budget'])) $_meta['budget'] = [];

        if(!isset($_meta['work'])) $_meta['work'] = TypeAttrConstant::ATTR_GONG_ZUO_RI;

        if(!isset($_meta['year'])) $_meta['year'] = '';

        if(!isset($_meta['birth'])) $_meta['birth'] = TypeAttrConstant::ATTR_HAS_ALLOWANCE;

        return $_meta;
    }

    public function handleMetaTag($type, $meta, $date, $item)
    {
        $tag = [];

        $tag[] = in_array($type, array(TypeConstant::TYPE_CHI_DAO, TypeConstant::TYPE_ZAO_TUI))
            ? $item['duration'].'次'
            : $item['duration'].'小时';

        if($meta['food']) $tag[] = '误餐补助';

        if($type == TypeConstant::TYPE_JIA_BAN && $meta['work']) {

            $tag[] = Type::getMappingTypeRefer($meta['work']);
        }

        if($type == TypeConstant::TYPE_YEAR_REST && $meta['year']) {

            $tag[] = $meta['year'];
        }

        if($type == TypeConstant::TYPE_CHAN_JIA && $meta['birth']) {

            $tag[] = Type::getMappingTypeRefer($meta['birth']);
        }

        if(isset($this->lock[$date]) && 1 == $this->lock[$date]) $tag[] = '已锁定';

        if(1 == $item['is_expired']) $tag[] = '补';

        return $tag;
    }

    public function handlePushTag($flows)
    {
        foreach ($flows as $flow) {
            
            if(ApplyConstant::Action_DeptAdject == $flow['action_name'] 
                && isset($flow['action_data']['step'])
                && $flow['action_data']['user_id'] == auth('api')->user()->id
            ) {
                return 1 == $flow['action_data']['step'] ? '审批' : '知晓';
            }
        }

        return '';
    }

    protected function canEdit($user, $date, $status) 
    {   
        if(isset($this->lock[$date]) && 1 == $this->lock[$date]) return false;

        if($this->hasAttend || $this->hasAffairs) return true;

        if(in_array($status, [ 
                ApplyConstant::status_toapply,
                ApplyConstant::status_dept_adject,
                ApplyConstant::status_dept_adject_reject,
                ApplyConstant::status_attend_adject_reject,
                ApplyConstant::status_admin_adject_reject
            ]) && $user == auth('api')->user()->id
        ) return true;
        
        return false;
    }

    protected function canDelete($date, $status, $cancel, $flows) 
    {   
        if(1 == $cancel || $status == ApplyConstant::status_deleted) return false;
        
        if(isset($this->lock[$date]) && 1 == $this->lock[$date])  return false;

        if($this->hasAffairs || $this->hasAttend) return true;
        
        $action = ApplyConstant::Action_ApplyQuick;
        $flow   = array_first($flows, function($k, $v) use ($action) {

            return $k['action_name'] == $action;
        });

        if($status == ApplyConstant::status_dept_adject 
            && ($flow['action_user_id'] == auth('api')->user()->id)
        ) return true;
        
        return false;
    }

    protected function canCancel($user, $date, $cancel, $status)
    {
        if($cancel) return false;

        if($user != auth('api')->user()->id) return false;

        if(isset($this->lock[$date]) && 1 == $this->lock[$date]) return false;

        if($status < ApplyConstant::status_dept_adject) return false;

        return true;
    }

    protected function canCharge($user, $date, $status)
    {
        if(isset($this->lock[$date]) && 1 == $this->lock[$date]) return false;

        if($user == auth('api')->user()->id && $status == ApplyConstant::status_dept_adject) return true;

        return false;
    }

    protected function canAttend($date, $status)
    {
        if(isset($this->lock[$date]) && 1 == $this->lock[$date]) return false;

        if($status == ApplyConstant::status_attend_adject) return true;

        return false;
    }

    protected function canAffairs($date, $status)
    {
        if(isset($this->lock[$date]) && 1 == $this->lock[$date]) return false;

        if($status == ApplyConstant::status_admin_adject) return true;

        return false;
    }

    protected function setItems($items)
    {
    	$this->items  = $items;
    }

    protected function setFilterUserId($userId)
    {
    	$this->filter['user_id']  = $userId;
    }

    protected function setFilterParentId($parent)
    {
    	$this->filter['parent']  = $parent;
    } 

    protected function setFilterApplyId($applyId)
    {
        if($applyId) $this->filter['apply_id']  = $applyId;
    }

    protected function setFilterFlowUserId($flowUserId)
    {
        $this->filter['flow_user_id']  = $flowUserId;  
    }

    protected function setFilterStatus($status)
    {
        $this->filter['status']  = $status;  
    }

    protected function setFilterFlowAction($action)
    {
        $this->filter['flow_action']  = $action;  
    }

    protected function setFilterRegion($region)
    {
        $this->filter['region']  = $region;  
    }

    protected function setFilterCrop($crop)
    {
        $this->filter['crop']  = $crop;  
    }

    protected function setFilterDates($dates)
    {
        $this->filter['dates']  = $dates;  
    }

    protected function setFilterType($type)
    {
        $this->filter['type']  = $type;  
    }

    protected function setPageSize($size)
    {
        $this->filter['pagesize']  = $size;  
    }

    protected function setFilterDesc($desc)
    {
        if($desc) $this->filter['desc']  = $desc;  
    }
}