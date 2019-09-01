<?php

namespace App\Models\Bee;

use App\Models\Rain\UserAttendance;
use App\Models\Bee\Contracts\ApplyConstantInterface;
use App\Models\Bee\Contracts\TypeConstantInterface;
use Illuminate\Database\Eloquent\Model;

class Apply extends Model implements ApplyConstantInterface
{
    protected $table = 'v2_bee_apply';

    protected $primaryKey = 'id';

    protected $guarded    = ['id'];

  	public static function getOptionRefer(){

  		$refer = array(

  			self::opt_all      => '全天',
  			self::opt_am       => '上午',
  			self::opt_pm       => '下午',
  			self::opt_other    => '其他'					
  		);

  		return $refer;		
  	}

  	public static function getStatusRefer(){

  		$refer = array(

  			self::status_deleted              => '已销假',
  			self::status_toapply              => '等待申请',
  			//self::status_modify               => '申请修改',
  			self::status_dept_adject          => '部门审批',
  			self::status_dept_adject_reject   => '部门驳回',
  			// self::status_manage_adject        => '推送审批',
  			// self::status_manage_adject_reject => '推送驳回',
  			self::status_attend_adject        => '考勤员审批',
  			self::status_attend_adject_reject => '考勤员驳回',			
  			self::status_admin_adject         => '人事审批',
  			self::status_admin_adject_reject  => '人事审批驳回',
  			self::status_finished             => '已归档',							
  		);

  		return $refer;		
  	}

    public function scopeId($query, $id) 
    {

      if(is_array($id)) return $query->whereIn('id', $id);
      
      return $query->where('id', $id);
    }
    
    public function scopeType($query, $type) 
    {
      if(is_array($type)) return $query->whereIn('type_id', $type);

      return $query->where('type_id', $type);
    }

    public function scopeStatus($query, $status) 
    {
      if(is_array($status)) return $query->whereIn('status_id', $status);
      
      return $query->where('status_id', $status);
    }

    public function scopeAction($query, $action) 
    {
      if(is_array($action)) return $query->whereIn('flow_action', $action);
      
      return $query->where('flow_action', $action);
    }

    public function scopeRegion($query, $region) 
    {
      return $query->whereIn('region', $region);
    }

    public function scopeCrop($query, $crop) 
    {
      return $query->whereIn('crop_id', $crop);
    }
    
    public function scopeUser($query, $user) 
    {

      if(is_array($user)) return $query->whereIn('user_id', $user);
      
      return $query->where('user_id', $user);
    }

    public function scopeAppling($query) 
    {
      return $query->where([
      		 	['stage_id', '>', self::stage_deleted],
      		 	['status_id', '>', self::status_deleted],
             ]);
    }

    public function scopeParent($query, $parent) 
    {

      if(is_array($parent)) return $query->whereIn('parent_id', $parent);
      
      return $query->where('parent_id', $parent);
    }

    public function scopeDate($query, $date) 
    {
      return $query->whereIn('apply_date', $date);
    }

    public function scopeDates($query, $dates) 
    {
      return $query->whereBetween('apply_date', $dates);
    }

    public function scopeDesc($query, $desc) 
    {
      return $query->where('desc', 'like', $desc.'%');
    }
}
