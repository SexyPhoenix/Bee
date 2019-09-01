<?php

/**
 * @Author: Sexy Phoenix
 * @Date:   2019-05-30 15:46:27
 * @Last Modified by:   Sexy Phoenix
 * @Last Modified time: 2019-08-31 15:30:15
 */
namespace App\Decorator\Bee;

use Carbon\Carbon;
use App\Services\FocusMessage;
use App\Repositories\Bee\ApplyRepository;
use App\Repositories\Bee\ApplyFlowRepository as ApplyFlow;
use App\Repositories\Bee\UserRepository as User;
use App\Models\Bee\Contracts\ApplyConstantInterface as ApplyConstant;
use App\Models\Bee\Contracts\TypeConstantInterface as TypeConstant;
use App\Contracts\Bee\ApplyDecoratorInterface;

class BusinessDecorator implements ApplyDecoratorInterface
{
	protected $items;
    
    protected $operator;

    protected $meta;

    protected $data;

    protected $apply;

    public function __construct(ApplyRepository $apply) 
    {
        $this->apply = $apply;
    }

    public function handle($items, $operator, $updated)
    {  
        if($updated) return true; //no update peer info 

        $this->items    = $items;
        $this->operator = $operator;

        $this->meta = unserialize($this->items[0]['meta']);

        if(TypeConstant::TYPE_CHU_CHAI == $this->items[0]['type_id']) {

			$this->handlePeer();      
        }
    }

    protected function handlePeer()
    {
    	if($this->meta['team'] && $this->meta['peer']) {

    		$this->setItemOfPeer();

            $flows = [];
    		foreach ($this->items as $idx => $item) {
    			
    			foreach ($this->meta['peer'] as $peer) {

    				$_item           = array_merge($item, $this->data[$peer]);
    				$_item['meta']   = $this->setQuoteIdMeta($_item);

					$flows[] = $_apply = $this->apply->storeApply($_item);

					if(0 == $idx) $this->setParentId($peer, $_apply['id']);
    			}		
    		}
            
            //save flow
            $this->storeWorkflow($flows);

            //notice
            $this->eventNotice();
    	}
    }
    
    /**
     * @Author   Sexy Phoenix
     * @DateTime 2019-06-10T10:52:28+0800
     * @param    [type] $items
     * @return   [type] store apply flow
     */
    public function storeWorkflow($items)
    {
        $now         = Carbon::now()->toDateTimeString();
        $operator_id = $this->items[0]['user_id'];

        $flow = [];
        foreach ($items as $item) {
            
            $flow[] = [

                'apply_id'       => $item['id'],
                'action_user_id' => $operator_id,
                'action_name'    => ApplyConstant::Action_ApplyQuick,
                'status_id'      => 0,
                'action_data'    => serialize($item),
                'based_flow_id'  => 0,
                'created_at'     => $now,
                'updated_at'     => $now
            ];
        }

        ApplyFlow::storeWorkflow($flow);
    }

    public function eventNotice()
    {

        $user        = User::getUserById($this->items[0]['user_id']);
        $url         = config('app.url').'/bee#/list';

        foreach ($this->meta['peer'] as $peer) {

            FocusMessage::send(

                $peer,
                $url,
                '考勤：'.$user->name.'为您添加了一条出差申请，请处理'
            );            
        }
    }

    protected function setQuoteIdMeta($item)
    {
    	return serialize(array_merge(unserialize($item['meta']), ['quote_id' => $item['id']]));
    }

    protected function setParentId($user_id, $id)
    {
    	$this->data[$user_id]['parent_id'] = $id;
    }

    protected function setItemOfPeer()
    {
    	foreach ($this->meta['peer'] as $uid) {

    		if($uid == $this->items[0]['user_id']) continue;

        	$user         = User::getUserById($uid);
        	//$manager_id   = User::getUserManager($user);
        	$region       = $user->region > 200 ? $user->region : 200;

    		$this->data[$uid] = [

    			'user_id'      => $uid,
                'stage_id'     => ApplyConstant::stage_init,
                'status_id'    => ApplyConstant::status_toapply,
                'flow_action'  => ApplyConstant::Action_WaitApply,
    			'flow_user_id' => $uid,
    			'region'       => $user->region,
    			'crop_id'      => $user->crop_id,
    			'handover'     => '',
    			'parent_id'    => 0
    		];
    	}
    }
}