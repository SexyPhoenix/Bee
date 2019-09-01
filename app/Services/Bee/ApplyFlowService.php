<?php 

namespace App\Services\Bee;

use Carbon\Carbon;
use App\Repositories\Bee\ApplyFlowRepository;
use App\Models\Bee\Contracts\ApplyConstantInterface as ApplyConstant;

class ApplyFlowService
{
    protected $flow;

    public function __construct(ApplyFlowRepository $flow) 
    {
        $this->flow       = $flow;
    }

    public function getApplyList($userId, $actions)
    {
        return $this->flow->getFlowByUserAndAction(
            $userId,
            $actions            
        );
    }

    public function getChargeActionName()
    {
        return ApplyConstant::Action_DeptAdject;
    }

    /**
     * @Author   Sexy Phoenix
     * @DateTime 2019-06-18T17:09:57+0800
     * @param    [type] $post
     * @param    [type] $operator
     * @param    [type] $status
     * @return   store flow
     */
    public function storeWorkFlow($post, $operator, $action, $status = 0)
    {
        $flow = [];
        
        foreach ($post['applyIds'] as $id) {
            
            $flow[] = $this->transform(

                $id, 
                $operator, 
                $action,
                $status,
                $post
            );
        }
        
        return $this->flow->storeWorkFlow($flow);    
    }

    public function transform($id, $operator, $action_name, $status, $post)
    {

        $now  = Carbon::now()->toDateTimeString();

        $item = [

            'apply_id'        => $id,
            'action_user_id'  => $operator,
            'status_id'       => $status,
            'action_name'     => $action_name,
            'based_flow_id'   => 0,
            'created_at'      => $now,
            'updated_at'      => $now
        ];

        $item['action_data']  = serialize($post);

        return $item;
    }
}