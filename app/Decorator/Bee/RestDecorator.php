<?php

/**
 * @Author: Sexy Phoenix
 * @Date:   2019-05-30 15:46:27
 * @Last Modified by:   Sexy Phoenix
 * @Last Modified time: 2019-06-27 11:33:59
 */
namespace App\Decorator\Bee;

use App\Services\Bee\RestService;
use Illuminate\Support\Facades\Redis;
use App\Repositories\Bee\ApplyRepository;
use App\Repositories\Bee\UserRepository as User;
use App\Models\Bee\Contracts\TypeConstantInterface as Type;
use App\Contracts\Bee\ApplyDecoratorInterface;

class RestDecorator implements ApplyDecoratorInterface
{
    
    protected $item;

    protected $operator;

    protected $redis;

    protected $updated;

    public function handle($items, $operator, $updated)
    {  
        $this->item     = current($items);
        $this->operator = $operator;
        $this->updated  = $updated;
        $this->redis    = Redis::connection('cache');

        $this->updatedRest();

        if(Type::TYPE_TIAO_XIU == $this->item['type_id']) {

            $this->resetRestCache();
        }

    }

    /**
     * @Author   Sexy Phoenix
     * @DateTime 2019-06-17T11:44:42+0800
     * @return   reset rest cache
     */
    private function updatedRest()
    {

        if($this->updated && in_array(
            Type::TYPE_TIAO_XIU,
            array_pluck($this->updated, 'type_id')
        )) $this->resetRestCache();
    }

    /**
     * @Author   Sexy Phoenix
     * @DateTime 2019-05-31T14:17:12+0800
     * @return   update year cache
     */
    private function resetRestCache()
    {
        $user_id = $this->item['user_id'];

        $this->redis->zremrangebyscore(ApplyRepository::BEE_USER_REST, $user_id, $user_id);

        $service    = new RestService(new ApplyRepository);
        $rest       = $service->initialize(User::getUserById($user_id), array_pluck($this->updated, 'id'));
        $_rest_info = $rest->getUserRest();

        if($_rest_info['show_rest']) {

            $this->redis->zadd(ApplyRepository::BEE_USER_REST, $user_id, serialize($_rest_info));
        }
    }
}