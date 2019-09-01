<?php

namespace App\Jobs\Bee;

use Carbon\Carbon;
use App\Repositories\Bee\UserRepository;
use App\Repositories\Bee\ApplyFlowRepository;
use App\Models\Bee\Contracts\ApplyConstantInterface as ApplyConstant;
use App\Services\Bee\ApplyMetaService;
use App\Services\FocusMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

//php artisan queue:work bee --queue=apply --sleep=3 --tries=3
class ApplyFlow implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $operator;

    protected $items;

    protected $updated;

    protected $apply_url;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($operator, array $items, array $updated)
    {
        $this->onQueue('apply');

        $this->items     = $items;
        $this->operator  = $operator;
        $this->updated   = $updated;
        $this->apply_url = config('app.url').'/bee#/approval/dept';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ApplyFlowRepository $applyFlow, UserRepository $user, ApplyMetaService $applyMeta)
    {
        if($this->items) {

            $this->storeWorkflow($applyFlow);
            
            $user_info = $user->getUserById($this->items[0]['user_id']);
            $applyMeta->handleApply(

                $this->items,
                $user_info,
                $this->operator,                
                $this->updated
            );

            $content = $this->updated 
                ? $user_info->name.'调整了一条申请，请知晓'
                : $user_info->name.'添加了一条申请，请审批';

            FocusMessage::send(

                $this->items[0]['flow_user_id'],
                $this->apply_url,
                '考勤：'.$content
            );

        }
    }

    /**
     * @Author   Sexy Phoenix
     * @DateTime 2019-05-30T14:58:30+0800
     * @param    [type] $flow
     * @return   [type] store apply flow
     */
    public function storeWorkflow($applyFlow)
    {
        $now  = Carbon::now()->toDateTimeString();
        $action_name = $this->updated ? ApplyConstant::Action_EditApply : ApplyConstant::Action_ApplyQuick;

        $flow = [];
        foreach ($this->items as $item) {
            
            $flow[] = [

                'apply_id'       => $item['id'],
                'action_user_id' => $this->operator->id,
                'action_name'    => $action_name,
                'status_id'      => 0,
                'action_data'    => serialize($item),
                'based_flow_id'  => 0,
                'created_at'     => $now,
                'updated_at'     => $now
            ];
        }

        $applyFlow->storeWorkflow($flow);        
    }
}
