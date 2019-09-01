<?php

namespace App\Http\Controllers\Bee;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Response\Traits\ResponseTrait;
use App\Http\Controllers\Bee\Traits\FlowTrait;
use App\Services\Bee\Traits\ApplyTrait;
use App\Models\Bee\Contracts\FlowConstantInterface as FlowConstant;
use App\Models\Bee\Contracts\ApplyConstantInterface as ApplyConstant;
use App\Repositories\Bee\MessageRepository as Message;
use App\Repositories\Bee\UserRepository as User; 
class ChargeController extends Controller
{
	use ResponseTrait, FlowTrait, ApplyTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    	$this->setFilter();

        return $this->responseJson($this->apply->getApplyList($this->request));
    }
    
    /**
     * @Author   Sexy Phoenix
     * @DateTime 2019-06-18T17:09:33+0800
     * @return   agree
     */
    public function agree()
    {   
        if(isset($this->request->step)) {

            if(!User::canCharge(User::getUserById($this->request->user_id))) {

                return $this->withUnauthorized(trans('bee.no.access'));
            }
        }

        $this->request->offsetSet(
            'applyIds', 
            $this->positive($this->request->applyIds)
        );

        $result = $this->saveWorkFlow(

            ApplyConstant::Action_DeptAdject,
            FlowConstant::status_agree 
        );

        if($result) {

            $this->apply->passChargeSuggestion(
                $this->request->all()
            );

            $apply_list = $this->apply->getFilterApply(
                ['apply_id' => $this->request->applyIds, 'parent' => 0],
                false
            );
        
            $this->stepChargeMessage(array_pluck($apply_list, 'id'));

            return $this->withNotContent();
        }

        return $this->withNotImplemented($result);
    }

    /**
     * @Author   Sexy Phoenix
     * @DateTime 2019-06-18T17:09:42+0800
     * @return   reject
     */
    public function reject()
    {   

        $this->request->offsetSet(
            'applyIds', 
            $this->positive($this->request->applyIds)
        );

        $result = $this->saveWorkFlow(

            ApplyConstant::Action_DeptAdject,
            FlowConstant::status_reject 
        );

        if($result) {

            $this->apply->rejectChargeSuggestion(
                $this->request->all()
            );

            $this->chargeRejectMessage();

            return $this->withNotContent();
        }

       return $this->withNotImplemented($result);
    }
      
    public function setFilter()
    {

        if($this->request->status) $this->setStatus($this->request->status);

        //$this->setToExecute(ApplyConstant::status_dept_adject);

        //$this->setPush(ApplyConstant::status_dept_push);
        $this->setApplyId(ApplyConstant::Action_DeptAdject);
        
        $this->setFlowUserId();

        $this->setFlowAction(ApplyConstant::Action_DeptAdject);

        $this->setApplyDate();

        $this->setUserByDept();

        $this->setUser();
    }
    
    /**
     * @Author   Sexy Phoenix
     * @DateTime 2019-06-19T13:24:49+0800
     * @return   adject and notice message
     */
    public function stepChargeMessage($list)
    {

        if(!isset($this->request->step) || !isset($this->request->user_id)) return;

        //notice
        (new Message())->chargeMessage(

            $this->request->user_id,
            auth('api')->user()->name,
            $this->request->step,
            $list
        );
    }

    /**
     * @Author   Sexy Phoenix
     * @DateTime 2019-06-19T13:25:07+0800
     * @return   reject message
     */
    public function chargeRejectMessage()
    {
        //notice
        (new Message())->chargeRejectMessage(

            $this->request->applyIds,
            auth('api')->user()->name
        );
    }

    // protected function getActionNames()
    // {
    //     $first = head($this->request->status);

    //     if(1 == count($this->request->status)) {

    //         if($first == ApplyConstant::status_push_adject) return ApplyConstant::Action_PushAdject;

    //     } else if (in_array(ApplyConstant::status_push_adject, $this->request->status)){

    //         return [ApplyConstant::Action_DeptAdject, ApplyConstant::Action_PushAdject];
    //     }

    //     return ApplyConstant::Action_DeptAdject;
    // }
}
