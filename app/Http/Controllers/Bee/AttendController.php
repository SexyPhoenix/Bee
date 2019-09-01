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

class AttendController extends Controller
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
     * @DateTime 2019-06-19T16:07:32+0800
     * @return   attend agree
     */
    public function agree()
    {

        $this->request->offsetSet(
            'applyIds', 
            $this->positive($this->request->applyIds)
        );

        $result = $this->saveWorkFlow(

            ApplyConstant::Action_AttendAdject,
            FlowConstant::status_agree 
        );

        if($result) {

            $this->apply->passAttendSuggestion(
                $this->request->all()
            );

            return $this->withNotContent();
        }

        return $this->withNotImplemented($result);
    }

    /**
     * @Author   Sexy Phoenix
     * @DateTime 2019-06-19T16:08:13+0800
     * @return   attend reject
     */
    public function reject()
    {   

        $this->request->offsetSet(
            'applyIds', 
            $this->positive($this->request->applyIds)
        );

        $result = $this->saveWorkFlow(

            ApplyConstant::Action_AttendAdject,
            FlowConstant::status_reject 
        );

        if($result) {

            $this->apply->rejectAttendSuggestion(
                $this->request->all()
            );

            return $this->withNotContent();
        }

       return $this->withNotImplemented($result);
    }

    public function setFilter()
    {
        if($this->request->status) $this->setStatus($this->request->status);
        
        //$this->setToExecute(ApplyConstant::status_attend_adject);

        $this->setApplyId([

            ApplyConstant::Action_AttendAdject
        ]);

        $this->setFlowAction(ApplyConstant::Action_AttendAdject);

        $this->setRegion();

        $this->setCrop();

        $this->setApplyDate();

        $this->setUserByDept();

        $this->setUser();
    }
}