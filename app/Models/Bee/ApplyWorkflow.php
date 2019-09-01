<?php

namespace App\Models\Bee;

use Illuminate\Database\Eloquent\Model;

class ApplyWorkflow extends Model
{
    protected $table = 'v2_bee_apply_workflow';

    protected $primaryKey = 'flow_id';

    protected $guarded    = ['flow_id'];

    public function scopeApply($query, $apply) 
    {

      if(is_array($apply)) return $query->whereIn('apply_id', $apply);
      
      return $query->where('apply_id', $apply);
    }

    public function scopeUser($query, $userId) 
    {

      if(is_array($userId)) return $query->whereIn('action_user_id', $userId);
      
      return $query->where('action_user_id', $userId);
    }

    public function scopeAction($query, $action) 
    {

      if(is_array($action)) return $query->whereIn('action_name', $action);
      
      return $query->where('action_name', $action);
    }
}
