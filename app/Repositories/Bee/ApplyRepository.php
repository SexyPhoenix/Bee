<?php
namespace App\Repositories\Bee;

use Carbon\Carbon;
use App\Models\Bee\Apply;
use App\Models\Bee\Type;
use App\Repositories\Bee\UserRepository as User;
use App\Models\Bee\Contracts\TypeConstantInterface as TypeConstant;
use App\Models\Bee\Contracts\TypeAttrConstantInterface as TypeAttr;
use App\Models\Bee\Contracts\ApplyConstantInterface as ApplyConstant;
use Illuminate\Support\Facades\Redis;
use App\Services\Bee\ApplyMetaService;

class ApplyRepository {
  	
	private $redis;
	
	const BEE_USER_YEAR = 'bee:user:year';
	const BEE_USER_REST = 'bee:user:rest';
  const PER_PAGE      = 15;

	public function __construct()
	{
		$this->redis   = Redis::connection('cache');
	}
  
  /**
   * @Author   Sexy Phoenix
   * @DateTime 2019-06-14T15:16:29+0800
   * @param    [type] $data
   * @param    array $updated
   * @return   [type]
   */
	public function store($data, array $updated)
	{
    // $User = app()->make('App\Repositories\Bee\UserRepository')->getUserById(2);
    // $Operator = app()->make('App\Repositories\Bee\UserRepository')->getUserById(2);
    // dd(app()->make('App\Services\Bee\ApplyMetaService')->handleApply($data, $User, $Operator, $updated));
		$applies = [];
		foreach ($data as $item) {

      if($updated) {

        $id = $updated[$item['apply_date']]['id'];
        Apply::Id($id)->update($item);
      
        $applies[] = Apply::find($id)->toArray();
      } else {

        $applies[] = Apply::create($item)->toArray();
      }

		}
      
		return $applies;
	}

  public function storeApply($item)
  {
    return Apply::create($item)->toArray();
  }

	public function updateById($id, $item)
	{
		return Apply::where('id', $id)->update($item);
	}

  public function getApplyList($filter, $page = true)
  {

    $query = Apply::select(
      'id',
      'user_id',
      'type_id',
      'apply_date',
      'time_section', 
      'start_time',
      'end_time',
      'duration',
      'status_id',
      'flow_user_id',
      'handover',
      'attach',
      'desc',
      'meta',
      'is_expired',
      'parent_id',
      'region',
      'is_cancel'
    );

    if(isset($filter['user_id'])) $query->user($filter['user_id']);

    if(isset($filter['status'])) $query->status($filter['status']);

    if(isset($filter['region'])) $query->region($filter['region']);

    if(isset($filter['crop'])) $query->crop($filter['crop']);

    if(isset($filter['desc'])) $query->desc($filter['desc']);

    if(isset($filter['dates']) && $page) {

      $query->dates($filter['dates']); 
    } else {

      $query->parent($filter['parent']);
    }

    if(isset($filter['type'])) $query->type($filter['type']); 

    $query->where(function($query) use ($filter){

        if(isset($filter['apply_id']) && $filter['apply_id']) {

            $query->orWhere(function($query) use ($filter) {

                $query->whereIn('id', $filter['apply_id']);
            });

            $query->orWhere(function($query) use ($filter) {

                if(isset($filter['flow_user_id'])) $query->whereRaw('FIND_IN_SET('.$filter['flow_user_id'].', flow_user_id)');

                if(isset($filter['flow_action'])) $query->action($filter['flow_action']);
            });
        } else {

            if(isset($filter['flow_user_id'])) $query->whereRaw('FIND_IN_SET('.$filter['flow_user_id'].', flow_user_id)');

            if(isset($filter['flow_action'])) $query->action($filter['flow_action']);          
        }
    });

    $query->orderBy('apply_date', 'desc')->orderBy('status_id', 'asc');

    return $page ? $query->paginate($filter['pagesize'])->toArray() : $query->get()->toArray();
  }

  public function getApplyListById(array $list, array $fields = [])
  {
    $query = Apply::whereIn('id', $list);

    if($fields) $query->select($fields);

    return $query->get()->toArray();
  }

  public function getChildrenData($id, array $fields = [])
  {
    $query = Apply::Parent($id);

    if($fields) $query->select($fields);

    return $query->orderBy('apply_date', 'asc')->get();
  }
  
	public function getApplingByDate($user_id, array $date, array $ids = [])
	{

    $query = Apply::select('id', 'user_id', 'type_id', 'apply_date', 'time_section', 'start_time', 'end_time');

    if($ids) $query->whereNotIn('id', $ids);

		return $query->Appling()->user($user_id)->Date($date)->get();
	}

	public function getCostYearDuration($user_id, $startDate, $endDate, array $ids = [])
	{

    $query = Apply::select('duration', 'meta');

    if($ids) $query->whereNotIn('id', $ids);

		$data = $query->user($user_id)
			->type(TypeConstant::TYPE_YEAR_REST)
			->whereBetween('apply_date', [$startDate, $endDate])
			->Appling()
			->get();
    
    $this_year = Carbon::parse($startDate)->year;
		return $data->sum(function($item) use ($this_year) {

			$meta = unserialize($item['meta']);

			if($this_year == $meta['year']) return $item['duration'];
		});
	}
  
  public function getUpdatedYearDuration($user_id, array $ids)
  {
    $data = Apply::select('duration')
      ->user($user_id)
      ->type(TypeConstant::TYPE_YEAR_REST)
      ->whereIn('id', $ids)
      ->Appling()
      ->get();

    return $data->sum(function($item) {

      return $item['duration'];
    });
  }

	public function getWorkDuration($user_id)
	{

		$data = Apply::select('duration', 'meta')
			->user($user_id)
			->type(TypeConstant::TYPE_JIA_BAN)
			->where('apply_date', '>=', '2009-03-01')
			->Appling()
			->get();

    $works = [TypeAttr::ATTR_GONG_ZUO_RI, TypeAttr::ATTR_ZHOU_MO];
		return $data->sum(function($item) use ($works) {

			$meta = unserialize($item['meta']);

			if(in_array($meta['work'], $works)) return $item['duration'];
		});
	}

	public function getRestDuration($user_id, array $ids = [])
	{

    $query = Apply::user($user_id);

    if($ids) $query->whereNotIn('id', $ids);

		return $query->type(TypeConstant::TYPE_TIAO_XIU)
			->where('apply_date', '>=', '2009-03-01')
			->Appling()
			->sum('duration');
	}

  public function getUpdatedDuration($user_id, array $ids)
  {

    return Apply::user($user_id)
      ->type(TypeConstant::TYPE_TIAO_XIU)
      ->where('apply_date', '>=', '2009-03-01')
      ->whereIn('id', $ids)      
      ->Appling()
      ->sum('duration');
  }

	public function getYearApplyInfo($scores= [])
	{

		return $this->getOrderCollectionData($scores, self::BEE_USER_YEAR);
	}

	public function getRestApplyInfo($scores = [])
	{
		return $this->getOrderCollectionData($scores, self::BEE_USER_REST);
	}

  public function commonOperate($applies, $item){

    return Apply::whereIn('id', $applies)->update($item);      
  }

	protected function getOrderCollectionData($scores, $key)
	{
   		if(empty($scores)) {

   			$start = 0; $end = -1; $cmd = 'zrange';
   		} else {

	   		$start = min($scores); $end = max($scores); $cmd = 'zrangebyscore';
   		}

   		$_data = $this->redis->{$cmd}($key, $start, $end);

		$result = [];
		if($_data) {

			foreach ($_data as $d) {

				$_d   = unserialize($d);
				$user_id = $_d['user_id'];

				if('zrangebyscore' == $cmd && !in_array($user_id, $scores)) continue;
				$result[$user_id] = $_d;			
			}
		}

		return $result;
	}

  public static function treatAsAbsent($user, Carbon $day) {

    $desc = $day->isToday() ? '签到异常' : '签退异常';

    return Apply::create([

      'user_id'      => $user->employee_id,
      'type_id'      => TypeConstant::TYPE_KUANG_GONG,
      'apply_date'   => $day->format('Y-m-d'),
      'time_section' => ApplyConstant::opt_all,
      'start_time'   => '08:45',
      'end_time'     => '17:30',
      'duration'     => 8,
      'stage_id'     => ApplyConstant::stage_init,
      'status_id'    => ApplyConstant::status_toapply,
      'flow_action'  => ApplyConstant::Action_WaitApply,
      'flow_user_id' => $user->employee_id,
      'region'       => 'cn-320000-320100',
      'crop_id'      => $user->company_id,
      'handover'     => '',
      'parent_id'    => 0,
      'meta'         => serialize(['type' => Type::getMappingType(TypeConstant::TYPE_KUANG_GONG)]),
      'desc'         => $desc
    ])->id;
  }

  public static function treatAsLate($user, Carbon $day, Carbon $userTime) {

    $time = $userTime->format('H:i');
    $desc = '签到迟到，签到时间是：'.$time;

    return Apply::create([

      'user_id'      => $user->employee_id,
      'type_id'      => TypeConstant::TYPE_CHI_DAO,
      'apply_date'   => $day->format('Y-m-d'),
      'time_section' => ApplyConstant::opt_am,
      'start_time'   => '08:45',
      'end_time'     => $time,
      'duration'     => 1,
      'stage_id'     => ApplyConstant::stage_init,
      'status_id'    => ApplyConstant::status_toapply,
      'flow_action'  => ApplyConstant::Action_WaitApply,
      'flow_user_id' => $user->employee_id,
      'region'       => 'cn-320000-320100',
      'crop_id'      => $user->company_id,
      'handover'     => '',
      'parent_id'    => 0,
      'meta'         => serialize(['type' => Type::getMappingType(TypeConstant::TYPE_CHI_DAO)]),
      'desc'         => $desc
    ])->id;
  }

  public static function treatAsAhead($user, Carbon $day, Carbon $userTime) {

    $time = $userTime->format('H:i');
    $desc = '提前签退，签退时间是：'.$time;

    return Apply::create([

      'user_id'      => $user->employee_id,
      'type_id'      => TypeConstant::TYPE_ZAO_TUI,
      'apply_date'   => $day->format('Y-m-d'),
      'time_section' => ApplyConstant::opt_pm,
      'start_time'   => $time,
      'end_time'     => '17:30',
      'duration'     => 1,
      'stage_id'     => ApplyConstant::stage_init,
      'status_id'    => ApplyConstant::status_toapply,
      'flow_action'  => ApplyConstant::Action_WaitApply,
      'flow_user_id' => $user->employee_id,
      'region'       => 'cn-320000-320100',
      'crop_id'      => $user->company_id,
      'handover'     => '',
      'parent_id'    => 0,
      'meta'         => serialize(['type' => Type::getMappingType(TypeConstant::TYPE_ZAO_TUI)]),
      'desc'         => $desc
    ])->id;
  }
}
