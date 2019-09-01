<?php

/**
 * @Author: Sexy Phoenix
 * @Date:   2019-05-30 15:46:27
 * @Last Modified by:   Sexy Phoenix
 * @Last Modified time: 2019-08-31 15:30:32
 */
namespace App\Decorator\Bee;

use App\Repositories\Bee\ApplyRepository;
use App\Contracts\Bee\ApplyDecoratorInterface;
use App\Repositories\Bee\UserRepository as User;
use App\Models\Bee\Contracts\TypeConstantInterface as TypeConstant;
use App\Models\Bee\Contracts\TypeAttrConstantInterface as TypeAttrConstant;

class BirthDecorator implements ApplyDecoratorInterface
{
	protected $items;

	protected $apply;

    protected $operator;

    protected $updated;

    public function __construct(ApplyRepository $apply) 
    {
        $this->apply = $apply;
    }

    public function handle($items, $operator, $updated)
    {  
        $this->items    = $items;
        $this->operator = $operator;
        $this->updated  = $updated;

        if(TypeConstant::TYPE_CHAN_JIA == current($this->items)['type_id']) {

            $this->handleBirthMeta();
        }
    }

    public function handleBirthMeta()
    {
        // foreach ($this->items as $item) {

        //     if(empty($this->updated)) {

        //         $meta = serialize(array_merge($item['meta'], ['birth' => TypeAttrConstant::ATTR_HAS_ALLOWANCE]));

        //     } else {
        //         $meta = $item['meta']
        //     }
           
        // }        
    }

    private function canAdmin()
    {   
        if(!$this->updated) return false;

        $User = app()->make('App\Models\Bee\User')->find($this->operator->id);

        return User::canAttend($User) || User::canAffairs($User);
    }    	
}