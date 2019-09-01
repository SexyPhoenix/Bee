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

class ApplyController extends Controller
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

    public function setFilter()
    {

        if($this->request->has('users') || $this->request->has('depts')) {
            
            $this->filterUser();
            $this->setUser();              
            $this->setUserByDept();

        } else {

            if($this->request->has('id')) {
                $this->request->offsetSet(
                    'apply_id',
                    [$this->request->id]
                );
            } else {

                $this->request->offsetSet(
                    'user_id',
                    [auth('api')->user()->id]
                );                  
            }
          
        }

        $this->setRegion();

        $this->setCrop();

        $this->setApplyDate();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
       // return view('bee.apply.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   
        $result = $this->apply->storeApply($request->all());

        if(true === $result) {

            return $this->withCreated(['message' => trans('bee.create.success')]);
        }

        return $this->withNotImplemented($result);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {

        $result = $this->apply->storeApply($request->all(), $request->get('updated'));

        if(true === $result) {

            return $this->withNotContent();
        }

        return $this->withNotImplemented($result);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {      
        $apply_ids = $this->apply->getApplyIds($this->request->id);
        $this->request->offsetSet('applyIds', $apply_ids);

        $result = $this->saveWorkFlow(ApplyConstant::Action_DeleteApply, 0);

        if($result) {

            $this->apply->deleteApply($apply_ids);

            $apply_list = $this->apply->getFilterApply(
                ['apply_id' => $apply_ids, 'parent' => 0],
                false
            );

            $Message = new Message();
            foreach ($apply_list as $item) {
                
                $name     = User::getUserById($item['user_id'])->name;
                $Message->knowMessage($item['flow_user_id'], $name, FlowConstant::DESTROY_CHARGE_KNOW, [$item['id']]);
            }

            return $this->withGone(trans('bee.delete.success'));
        }

        return $this->withNotImplemented($result);
    }

    public function cancel()
    {
        $this->request->offsetSet(
            'applyIds',
            $this->positive($this->request->applyIds)
        );

        $result = $this->saveWorkFlow(ApplyConstant::Action_CancelApply, 0);

        if($result) {

            $User = auth('api')->user();

            $this->apply->cancelApply(
                $User->manager,
                $this->request->all()
            );

            //notice
            (new Message())->chargeMessage(

                $User->manager,
                $User->name,
                FlowConstant::STEP_CANCEL
            );

            return $this->withNotContent();
        }

        return $this->withNotImplemented($result);      
    }

    protected function filterUser()
    {
        if(!$this->request->has('users')) return true;

        if(User::canAttend(auth('api')->user())) return true;

        if(User::canAffairs(auth('api')->user())) return true;

        $users = User::getChargeUsers($this->request->users, auth('api')->user()->dept_id);

        $this->request->users = $users ? $users : [auth('api')->user()->id];

        return true;

    }
}
