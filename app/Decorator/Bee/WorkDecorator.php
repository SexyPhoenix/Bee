<?php

/**
 * @Author: Sexy Phoenix
 * @Date:   2019-05-30 15:46:27
 * @Last Modified by:   Sexy Phoenix
 * @Last Modified time: 2019-08-31 15:30:38
 */
namespace App\Decorator\Bee;

use App\Models\Holiday;
use App\Repositories\Bee\ApplyRepository;
use App\Repositories\Bee\WorkDayRepository;
use App\Repositories\Bee\UserRepository as User;
use App\Models\Bee\Contracts\TypeConstantInterface as TypeConstant;
use App\Models\Bee\Contracts\TypeAttrConstantInterface as TypeAttrConstant;
use App\Contracts\Bee\ApplyDecoratorInterface;

class WorkDecorator implements ApplyDecoratorInterface
{
	protected $items;
    
    protected $operator;

    protected $mapping;

    protected $work;

    protected $apply;

    protected $updated;

    public function __construct(ApplyRepository $apply, WorkDayRepository $work) 
    {
        $this->apply = $apply;
        $this->work = $work;
    }

    public function handle($items, $operator, $updated)
    {  

        $this->items    = $items;
        $this->operator = $operator;
        $this->updated  = $updated;

        if(TypeConstant::TYPE_JIA_BAN == current($this->items)['type_id']) {

            $this->getMappingDays();

            $this->handleWorkMeta();
        }        
    }
    
    /**
     * @Author   Sexy Phoenix
     * @DateTime 2019-06-06T14:43:06+0800
     * @return   date and type mapping relation
     */
    public function getMappingDays()
    {
        $this->mapping = $this->work->getMappingByTypeAndDate(array_pluck($this->items, 'apply_date'));
    }

    /**
     * @Author   Sexy Phoenix
     * @DateTime 2019-06-06T14:43:33+0800
     * @return   save work meta
     */
    public function handleWorkMeta()
    {
        foreach ($this->items as $item) {

           $_meta = $this->setWorkMeta($item['apply_date'], unserialize($item['meta']));

           $_meta['food'] = $_meta['work'] == TypeAttrConstant::ATTR_ZHOU_MO ? $_meta['food'] : false;
           $this->apply->updateById($item['id'], ['meta' => serialize($_meta)]);
        }
    }

    public function setWorkMeta($date, $meta)
    {
        //attend & admin
        if($this->canAdmin()) {

            return array_merge(
                $meta, 
                ['type' => $this->attrMappingType($meta['work']), 'work' => $meta['work']]
            );            
        }

        //工作日换调休
        if(!array_key_exists($date, $this->mapping)) 
            return array_merge(
                $meta, 
                ['type' => TypeConstant::OLD_JIA_BAN_GONG_ZUO_RI, 'work' => TypeAttrConstant::ATTR_GONG_ZUO_RI]
            );

        //法定节假日
        if(Holiday::LAW_HOLIDAY == $this->mapping[$date])
            return array_merge(
                $meta, 
                ['type' => TypeConstant::OLD_JIA_BAN_LAW_RI, 'work' => TypeAttrConstant::ATTR_LAW_RI]
            );

        //休息日换调休
        //if(Holiday::REST_HOLIDAY == $this->mapping[$date])
        return array_merge($meta, ['type' => TypeConstant::OLD_JIA_BAN_ZHOU_MO, 'work' => TypeAttrConstant::ATTR_ZHOU_MO]);
    }

    private function canAdmin()
    {   
        if(!$this->updated) return false;

        $User = app()->make('App\Models\Bee\User')->find($this->operator->id);

        return User::canAttend($User) || User::canAffairs($User);
    }   
   
    protected function attrMappingType($attr)
    {
        return [
            TypeAttrConstant::ATTR_WORK_RI     => TypeConstant::OLD_JIA_BAN_WORK_RI,
            TypeAttrConstant::ATTR_REST_RI     => TypeConstant::OLD_JIA_BAN_REST_RI,
            TypeAttrConstant::ATTR_LAW_RI      => TypeConstant::OLD_JIA_BAN_LAW_RI,
            TypeAttrConstant::ATTR_GONG_ZUO_RI => TypeConstant::OLD_JIA_BAN_GONG_ZUO_RI,
            TypeAttrConstant::ATTR_ZHOU_MO     => TypeConstant::OLD_JIA_BAN_ZHOU_MO,
        ][$attr];
    }	
}