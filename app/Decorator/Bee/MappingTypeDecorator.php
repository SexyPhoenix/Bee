<?php

/**
 * @Author: Sexy Phoenix
 * @Date:   2019-05-30 15:46:27
 * @Last Modified by:   Sexy Phoenix
 * @Last Modified time: 2019-06-27 11:34:09
 */
namespace App\Decorator\Bee;

use App\Repositories\Bee\ApplyRepository;
use App\Contracts\Bee\ApplyDecoratorInterface;

class MappingTypeDecorator implements ApplyDecoratorInterface
{
	protected $item;

    protected $operator;

	protected $apply;

    public function __construct(ApplyRepository $apply) 
    {
        $this->apply = $apply;
    }

    public function handle($items, $operator, $updated)
    {  
        $this->item     = $items;
        $this->operator = $operator;
        
        return $this->item;
    }

    /**
     * @Author   Sexy Phoenix
     * @DateTime 2019-05-31T10:49:37+0800
     * @param    [type] $item
     * @param    [type] $year
     */
    // private function addYearToMeta($item, $year)
    // {
    // 	$meta = isset($item['meta']) ? unserialize($item['meta']) : [];

    // 	return serialize(array_merge($meta, ['year' => $year]));
    // }    	
}