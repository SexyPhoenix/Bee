<?php 

namespace App\Services\Bee;

use App\Repositories\Bee\ApplyRepository;
use App\Services\Bee\Traits\ApplyTrait;

class ApplyMetaService
{
    use ApplyTrait;

    public $apply; //apply repository

    public $decorators; //decorators
    
    public $items;

    public $user;

    public $operator;

    public $updated;

    public function __construct(ApplyRepository $apply, array $decorator = []) 
    {

        $this->apply      = $apply;
        $this->decorators = $decorator;
    }
    
    /**
     * @Author   Sexy Phoenix
     * @DateTime 2019-05-22T16:44:49+0800
     * @param    [type] $post
     * @return   handle apply
     */
    public function handleApply($items, $user, $operator, array $updated)
    {

        $this->items    = $items;
        $this->updated  = $updated;
        $this->user     = $user;
        $this->operator = $operator;

    //$this->beforeProcess();

        $this->process();
        
        //$this->transOldData(); 老数据生成

        return true;
    }
    
    protected function transOldData(){

        app()
        ->make('App\Repositories\Bee\TimecardRepository')
        ->transOldData(
            //Overload for Decorator updated data
            $this->apply->getApplyListById(array_pluck($this->items, 'id')),
            $this->user
        );
    }  
    
    /**
     * @Author   Sexy Phoenix
     * @DateTime 2019-06-03T16:34:21+0800
     * @return   [type] one group
     */
    public function beforeProcess()
    {   
        if(!$this->updated) $this->createParentItem();
    }
    
    public function createParentItem() {

        $this->handleParentId($this->getDiffItems(array_pluck($this->items, 'id')));
    }

    /**
     * @Author   Sexy Phoenix
     * @DateTime 2019-05-23T11:13:41+0800
     * @return   validate request
     */
    private function process()
    {
        foreach ($this->decorators as $decorator) {
            
            $decorator->handle($this->items, $this->operator, $this->updated);
        }
    }
}