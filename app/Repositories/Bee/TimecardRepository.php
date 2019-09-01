<?php
namespace App\Repositories\Bee;

use App\Models\Bee\Fee;
use App\Models\Bee\Apply;
use App\Models\Rain\UserAttendance;
use App\Repositories\Bee\UserRepository as User;
use App\Models\Bee\Contracts\TypeConstantInterface as TypeConstant;
use App\Models\Bee\Contracts\ApplyConstantInterface as ApplyConstant;

class TimecardRepository {
  
  protected $peer;

  //后期可废弃，不做优化
  public function transOldData($items, $user)
  {
  	$uniqid        = uniqid();
  	$chu_chai_data = $budget_data = $budget_item = $budget_apply = []; 
  	foreach ($items as $item) {

      $id         = $item['id'];
      $meta       = unserialize($item['meta']);
      $type       = $meta['type'];

      $_item      = $this->fieldTransform($item, $user, $uniqid);
      $apply_date = $_item['time_sect'];
      
      $_item['vacation_type'] = $type;
    	
    	$apply = UserAttendance::where('apply_id', $id)->first();

    	$_item['p_id'] = $this->handleWuCan($_item, isset($meta['food']) ? $meta['food'] : false, $apply);//误餐补助

    	if($apply) {

    		UserAttendance::where('tid', $apply->tid)->update($_item);
    		$timecard = UserAttendance::where('tid', $apply->tid)->first();
    	} else {

    		$timecard = UserAttendance::create($_item);
    	}
    	
    	if(TypeConstant::OLD_CHU_CHAI == $type) {

    		if(empty($budget_data)) {

    			$budget_data  = $meta['budget'];
    			$budget_item  = $_item;
    			$budget_apply = $apply;
    		}

    		$this->handlePeer($_item, $meta, $timecard->tid);
    	}
  	}

  	$this->handleBudget($budget_item, $budget_data, $user, $budget_apply);

  	if(empty($budget_apply)) $this->handleChuChai();

  	return true;
  }

  public static function delete($id)
  {
    $pids = [];
    foreach ($id as $d) {
      
      $attendance = UserAttendance::where('apply_id', $d)->first();
      if($attendance->p_id) $pids[] = $attendance->p_id;
    }

    $update = [
      'del_flag' => 1,
      'deler'    => 'sync'
    ];

    UserAttendance::whereIn('apply_id', $id)->update($update);
    
    if($pids) UserAttendance::whereIn('tid', $pids)->update($update);
  }

  public static function saveWorkFlow($id, $status){

    $update = [

      'chk_flag' => self::statusTransForm($status)
    ];

    $attendance = UserAttendance::where('apply_id', $id)->first();

    if($attendance->p_id) UserAttendance::where('tid', $attendance->p_id)->update($update);
    
    return UserAttendance::where('apply_id', $id)->update($update);

  }

  public function fieldTransform($item, $user, $uniqid)
  {
  	return [

  		'employee_id'    => $item['user_id'],
  		'emp_no'         => $user->job_no,
  		'time_sect'      => $item['apply_date'],
  		'day_option'     => $item['time_section'],
  		'time_start'     => $item['start_time'],
  		'time_end'       => $item['end_time'],
  		'time_cost'      => $item['duration'],
  		'remark'         => $item['desc'],
  		'handover_id'    => $item['handover'],
  		'image_ids'      => $item['attach'],
  		'card_no'        => 1,
  		'pub_flag'       => 1,
  		'adder'          => 'queue',
  		'chk_flag'       => 0,
  		'batch_id'       => $uniqid,
  		'm_charge_id'    => $item['flow_user_id'],
  		'apply_id'       => $item['id']		
  	];  	
  }

  public function handleWuCan($item, $food, $apply) 
  {

	$is_wu_can = in_array($item['vacation_type'], [TypeConstant::OLD_GONG_CHU, TypeConstant::OLD_JIA_BAN_ZHOU_MO]) && $food;

    $save = $item;
    $save['time_cost']     = 1;
    $save['vacation_type'] = TypeConstant::OLD_WU_CAN_BU_ZHU; 	
    unset($save['apply_id']);

    if($is_wu_can && (empty($apply) || 0 == $apply->p_id)) return UserAttendance::create($save)->tid;

    if($is_wu_can && !empty($apply) && $apply->p_id) {

    	$save['del_flag'] = 0;
    	UserAttendance::where('tid', $apply->p_id)->update($save);

    	return $apply->p_id;
    }

    if(false == $is_wu_can && !empty($apply) && $apply->p_id) {

    	UserAttendance::where('tid', $apply->p_id)->update(['del_flag' => 1]);
    }

    return 0;
  }

  public function handlePeer($item, $meta, $tid)
  {
  	$result = [];
    if($meta['team'] && 0 < count($meta['peer'])) {
        
      foreach ($meta['peer'] as $peer) {
          
        $_apply = Apply::user($peer)
                    ->type(TypeConstant::TYPE_CHU_CHAI)
                    ->where('apply_date', $item['time_sect'])
                    ->Appling()
                    ->first();
        if($_apply) {

          $_chu_chai = [

            'employee_id' => $_apply->user_id,
            'emp_no'      => '',
            'handover_id' => $_apply->handover,
            'm_charge_id' => $_apply->flow_user_id,
            'quote_tid'   => $tid,
            'apply_id'    => $_apply->id,
            'card_no'     => 0
          ];

          $this->peer[$peer][] = array_merge($item, $_chu_chai);
        }
      }            
    }	
  }

  public function handleBudget($item, $budget_data, $user, $apply)
  {

  	if(!empty($apply) && TypeConstant::OLD_CHU_CHAI == $apply->vacation_type) {

  		Fee::where('fee_uid', $apply->employee_id)
  			->where('fee_unqid', $apply->batch_id)
  			->update([
					'fee_isdel' => 1,
					'fee_updated' => date('Y-m-d H:i:s')
  				]);
  	}

    //预算
    if($budget_data) {

        $budget_format = [];
        foreach ($budget_data as $budget) {

          $parse = '';
          if($budget['fees']) {

            foreach ($budget['fees'] as $fee) {
              
              $parse[] = [

                'fee_type' => [
                  'key'      => $fee['type'], 
                  'val'      => $fee['type'] == '自定义' ? $fee['tname'] : $fee['type']
                ],
                'fee_money'  => $fee['money'],
                'fee_remark' => $fee['desc']
              ];
            }
          }

          $budget_format[] = [

            'total'        => $budget['total'],
            'name'         => $budget['desc'],
            'parse'        => $parse
          ];
        }

        if($budget_format) {

          $fee_data = [];
          $now      = date('Y-m-d H:i:s');
          foreach ($budget_format as $bf) {
            
            $fee_data[] = [

              'fee_uid'     => $user->user_id,
              'fee_name'    => $bf['name'],
              'fee_contxt'  => serialize($bf['parse']),
              'fee_unqid'   => $item['batch_id'],
              'fee_type'    => 'vacation',
              'fee_total'   => $bf['total'],
              'fee_created' => $now,
              'fee_updated' => $now
            ];
          }

          Fee::insert($fee_data);
        }
    } 	
  }

  public function handleChuChai() 
  {

    if($this->peer) {

      $data = [];
      foreach ($this->peer as $uid => $items) {
        
        $peer_user = User::getUserById($uid);
        $uniqid    = uniqid();
        foreach ($items as $item) {
            
            $_item              = $item;
            $_item['batch_id']  = $uniqid;
            $_item['emp_no']    = $peer_user->job_no;
            $_item['vacation_is_team'] = $_item['vacation_team_uid'] = '';
            $data[] = $_item;
        }
      }

      UserAttendance::storeTimecards($data);
    }
  }

  public static function statusTransForm($status)
  {
    return [

      ApplyConstant::status_dept_adject           => UserAttendance::TO_WAITING,
      ApplyConstant::status_dept_adject_reject    => UserAttendance::CHARGE_REJECT,
      ApplyConstant::status_attend_adject         => UserAttendance::CHARGE_AGREE,
      ApplyConstant::status_attend_adject_reject  => UserAttendance::ATTEND_REJECT,
      ApplyConstant::status_admin_adject          => UserAttendance::ATTEND_AGREE,
      ApplyConstant::status_admin_adject_reject   => UserAttendance::AFFAIRS_REJECT,
      ApplyConstant::status_finished              => UserAttendance::AFFAIRS_AGREE
    ][$status];
  }
}
