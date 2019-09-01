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

class AffairsController extends Controller
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

            ApplyConstant::Action_AdminAdject,
            FlowConstant::status_agree 
        );

        if($result) {

            $this->apply->passAffairsSuggestion(
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

            ApplyConstant::Action_AdminAdject,
            FlowConstant::status_reject 
        );

        if($result) {

            $this->apply->rejectAffairsSuggestion(
                $this->request->all()
            );

            return $this->withNotContent();
        }

       return $this->withNotImplemented($result);
    }

    public function setFilter()
    {
        $this->setStatus(
            $this->request->status ?: [ApplyConstant::status_admin_adject]
        );

        $this->setToExecute(ApplyConstant::status_admin_adject);

        $this->setApplyId([

            ApplyConstant::Action_AdminAdject
        ]);

        $this->setFlowAction(ApplyConstant::Action_AdminAdject);

        $this->setRegion();

        $this->setCrop();

        $this->setApplyDate();

        $this->setUserByDept();

        $this->setUser();
    }
}