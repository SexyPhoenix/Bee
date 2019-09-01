<?php 

namespace App\Services\Bee;

use App\Repositories\Bee\ApplyRepository;

class RestService
{   
    protected $user_id;
    
    protected $work;

    protected $rest;

    protected $left_rest;

    protected $updated_id; //更新id

    public function __construct(ApplyRepository $apply) 
    {
        $this->apply      = $apply;
    }
    
    public function initialize($user, array $updated_id = [] )
    {
        $this->user_id       = $user['user_id'];
        $this->updated_id    = $updated_id;

        return $this;
    }

    public function getUserRest()
    {
       
       $this->show_rest = $this->left_rest = 0;

       $this->getWorkDuration();

       if($this->work) {

            $this->getRestDuration();
            $this->getLeftRest();
            $this->getShowRest();
       }

       return ['show_rest' => $this->show_rest, 'left_rest' => $this->left_rest, 'user_id' => $this->user_id];
    }

    public function getWorkDuration()
    {
        $this->work = $this->apply->getWorkDuration($this->user_id);
    }

    public function getRestDuration()
    {
        $this->rest = $this->apply->getRestDuration($this->user_id, $this->updated_id);
    }

    public function getLeftRest()
    {
        $_left_rest = $this->work - $this->rest;

        $this->left_rest = $_left_rest >= 0.08 ? $_left_rest : 0;
    }

    public function getShowRest()
    {
        $this->show_rest =  $this->left_rest ? 1 : 0;
    }
}