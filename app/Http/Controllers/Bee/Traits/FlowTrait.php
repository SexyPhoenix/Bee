<?php

/**
 * @Author: Sexy Phoenix
 * @Date:   2019-06-19 10:36:24
 * @Last Modified by:   Sexy Phoenix
 * @Last Modified time: 2019-08-31 11:40:17
 */
namespace App\Http\Controllers\Bee\Traits;

use Illuminate\Http\Request;
use App\Services\Bee\ApplyService;
use App\Services\Bee\ApplyFlowService;
use App\Repositories\Bee\UserRepository as User;

trait FlowTrait {

    protected $apply;

    protected $flow;

    protected $request;

    protected $toExecute = false;
    
    public function __construct(Request $request, ApplyService $apply, ApplyFlowService $flow) 
    {
    	$this->request     = $request;
        $this->apply       = $apply;
        $this->flow        = $flow;
    }

    public function saveWorkFlow($action, $status)
    {
    	return $this->flow->storeWorkFlow(
    		$this->request->all(),
    		auth('api')->user()->id,
    		$action,
    		$status
    	);
    }
    
    public function setApplyId($actions)
    {
       // if(!$this->toExecute) {

            $this->request->offsetSet('apply_id', $this->flow->getApplyList(
                    auth('api')->user()->id,
                    $actions
                )
                ->pluck('apply_id')
                ->unique()
                ->all()
            );     
        //}
    }

    public function setUserByDept()
    {
        if($this->request->depts) {
            
            $user_ids = User::getUserByDept($this->request->depts);
            
            $this->request->offsetSet(
                'user_id', 
                array_unique(
                    $this->request->has('user_id')
                        ? array_merge($this->request->user_id, $user_ids)
                        : $user_ids
                )
            );
        }
    }

    public function setUser()
    {
        if($this->request->users) {
            
            $this->request->offsetSet(
                'user_id',
                array_unique(
                    $this->request->has('user_id')
                        ? array_merge($this->request->user_id, $this->request->users)
                        : $this->request->users
                )
            );
        }
    }

    public function setFlowUserId()
    {   
        $this->request->offsetSet('flow_user_id', auth('api')->user()->id);     
    }

    public function setType($type)
    {
        $this->request->offsetSet('type', $type);      
    }

    public function setStatus($status)
    {
        $this->request->offsetSet('status', $status);      
    }

    public function setFlowAction($action)
    {

        $this->request->offsetSet('flow_action', $action);     

    }

    public function setRegion()
    {
        $regions = isset($this->request->address) ? $this->request->address : [auth('api')->user()->region];
        
        if($regions) $this->request->offsetSet('region', $regions);      
    }

    public function setCrop()
    {
        $this->request->offsetSet('crop', [auth('api')->user()->crop_id]);      
    }

    public function setToExecute($status)
    {        
        $this->toExecute = (1 == count($this->request->status)) 
            && (head($this->request->status) == $status)
                ? true : false;
    }

    public function setApplyDate()
    {
        if($this->request->dates) {
            
            $this->request->offsetSet(
                'dates', 
                json_decode($this->request->dates, true)
            );
        }
    }
}