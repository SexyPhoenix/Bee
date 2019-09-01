<?php
namespace App\Repositories\Bee;

use Carbon\Carbon;
use App\Models\Bee\Corporation;
use App\Models\Bee\Dept;
use App\Models\Bee\User;
use App\Models\Bee\Region;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class UserRepository {

    const Prefix_Corporation = 'C_';
    const AUTH_CACHE_KEY     = 'bee-auth-token';

    public function login($request) {

        return [

            'user'  => User::where('email', trim($request->email))->first(),
            'token' => $this->getAccessToken($request)
        ];
    }

    //主管权限
    public static function canCharge($user)
    {
        return true;
    }	

    //考勤员权限
    public static function canAttend($user)
    {
        return true;
    }

    //人事权限
    public static function canAffairs($user)
    {
        return true;
    }

    //公司权限
    public static function canCorp($user)
    {
        return true;
    }

    public static function getFields()
    {
        return User::select(

            'id as user_id',
            'name', 
            'job_no', 
            'email', 
            'manager', 
            'crop_id',
            'region',
            'join_date',
            'positive_date',
            'work_begin_date'              
        );
    }

    public static function getUserById($id)
    {	

    	$query = self::getFields();

    	if(is_array($id)) {

    		return $query->whereIn('id', $id)->get();
    	}

    	return $query->find($id);
    } 

    public static function getUserByCondition($item, $field = 'job_no')
    {   

        $query = self::getFields();

        if(is_array($item)) {

            return $query->whereIn($field, $item)->get();
        }

        return $query->where($field, $item)->first();
    }

    public static function getChargeRegion()
    {	
        return ['南京市'];
    }

    public static function getChargeCrop()
    {	 
    	return array_pluck(getmyinfo('global_corps'), 'corp_id');
    }

    public static function getUserByDept($depts)
    {   
        return User::select('employee_id as user_id')
            ->whereIn('dept_id', $depts)
            ->OnDuty()
            ->get()
            ->pluck('user_id')
            ->toArray();

    }

    public static function getChargeUsers($users, $dept_id)
    {
        $dept_list = Dept::find($dept_id)
            ->getChildren(true)
            ->pluck('dept_id')
            ->toArray();

        return User::select('id')
            ->whereIn('id', $users)
            ->whereIn('dept_id', $dept_list)
            ->get()
            ->pluck('id')
            ->toArray(); 
    }

    public static function getUserPermission($user)
    {   
        $has_attend   = self::canAttend($user);
        $has_affairs  = self::canAffairs($user);
        $has_charge   = self::canCharge($user);
        $has_corp     = self::canCorp($user);

        return [

            'has_charge'  => $has_charge,
            'has_attend'  => $has_attend,
            'has_affairs' => $has_affairs,
            'has_corp'    => $has_corp
        ];
    }

    public static function getRegionByPreName($area_id)
    {   
        $region = Region::PrevName($area_id)->first();
        return $region ? $region->region_name : 'cn-320000-320100';
    }

    public static function getRegion($user)
    {   
        return Region::select(['region_name as value', 'region_value as text'])
            ->whereIn('region_name', self::getChargeRegion())
            ->get()
            ->toArray();
    }

	public static function filterUser($request)
	{

        $keyword = $request->input('keyword', '');

	    return User::select('id as user_id', 'name', 'job_no', 'dept_name')
				->where(function($query) use ($keyword){

				    $query->whereRaw('REPLACE(name,"　","") LIKE "%'.$keyword.'%"');
				        // ->orWhere('employee.pinyin', 'like', '%'.$keyword.'%')
				        // ->orWhere('ringleader', $keyword)
				        // ->orWhere('duty', 'like', '%'.$keyword.'%');
				})
				->where('active_flag', '<>', 0)
                ->leftJoin('v2_dept', 'v2_dept.dept_id', '=', 'users.dept_id')
				->limit(10)
				->get()
				->toArray();
	}

    public static function filterDept($user)
    {
        $manager_corpid = [1, 2];
        $corps          = Corporation::get();
        $user_id        = $user->id;
        $has_corp       = self::canCorp($user);
        $corp_list = [];
        foreach ($corps as $corp) {

            $corp_id = $corp->id;

            if(in_array($corp_id, $manager_corpid)){

                //公司下的部门
                $depts = Dept::select(
                    'dept_id', 'dept_name', 'up_dept', 'dept_key', 'tenant_id', 'assess_hook'
                )
                ->where('assess_hook', '<>', 0)
                ->ofCorporation($corp_id)
                ->orderBy('order_no')
                ->get();
                $root_depts = $root_dept_ids = [];

                foreach ($depts as $key => $value) {

                    if($value->isRoot() && $has_corp){

                        $root_depts[] = $value;
                        continue;
                    }

                    if($value->assess_hook == $user_id) {

                        $root_depts[] = $value;
                        $root_dept_ids[] = $value->dept_id;
                    }                
                }

                if($root_dept_ids) {

                    $charge_depts = [];
                    foreach ($root_depts as $root) {

                        if(in_array($root->up_dept, $root_dept_ids))  continue;
                        $charge_depts[] = $root;
                    }

                    $root_depts = $charge_depts;
                }

                foreach ($root_depts as $key => &$dept) {

                    $children         = self::getChildren($depts, $dept);
                    $dept->children   = $children;
                    $root_depts[$key] = $dept->toArray();
                }

                unset($depts);
                //公司下的数据统计
                if(!empty($root_depts)){

                    $corp_list[] = [
                        'dept_id'   => self::Prefix_Corporation.$corp_id,
                        'dept_name' => $corp->name,
                        'corp_id'   => $corp_id,
                    ];                    
                    $corp_list[(count($corp_list)-1)]['children'] = $root_depts; 
                }
            }
        }

        unset($root_depts);

        return collect($corp_list)->sortBy('corp_id',SORT_REGULAR,false)->values();
    }

    public static function regionTransform($regions)
    {
        $result = [];
        foreach ($regions as $region) {

            $result[$region] = Region::getBreadcrumb($region);
        }
        return $result;
    }

	public static function fileUpload($request) 
	{
		
		$upload = $request->file('attach');

		if($upload->isValid()) {

			$ext      = $upload->getClientOriginalExtension();
			$filename = uniqid() . '.' . $ext;
			$realPath = $upload->getRealPath();

			return Storage::disk('uploads_bee')
			           ->put($filename, file_get_contents($realPath))
			         ? $filename
			         : false;
		}

		return false;
	}

    protected static function getChildren($allDepts,$dept)
    {
        $parent_key = $dept->dept_key;
        $key_count  = count(explode('-', $parent_key));

        $dir_depts = $allDepts->filter(function($item) use($parent_key, $key_count){
            // $parent_key的"-"避免如48-1 匹配到 48-123-*
            return starts_with($item->dept_key, $parent_key."-") && count(explode('-', $item->dept_key)) == $key_count + 1;
        });

        foreach ($dir_depts as $key => &$val) {

            $children = self::getChildren($allDepts, $val);
            $val->children = $children;
        }

        $result = [];
        if($dir_depts->count()) {

            $direct_dept              = $dept->toArray();
            $direct_dept['dept_name'] = last(explode(' ', $direct_dept['dept_name']));
            $result[] = self::getDirectlyDept($direct_dept);
            foreach ($dir_depts->toArray() as $dept) {

               $dept['dept_name'] = last(explode(' ', $dept['dept_name']));
               $result[] = $dept;
               
            }
        }
        return $result;
    }

    protected static function getDirectlyDept($dept)
    {
        $dept['dept_name'] = $dept['dept_name'].'直属';
        return $dept;
    }

    public function getAccessToken($request) 
    {
        $token_data = Cache::get(self::AUTH_CACHE_KEY, null); 

        if($token_data){

            return $token_data;
        }else{
            try{

                $client = new Client();

                $response = $client->post(config('app.url').'/oauth/token', [
                    'form_params' => [
                        'grant_type'    => config('app.grant_type'),
                        'client_id'     => config('app.client_id'),
                        'client_secret' => config('app.client_secret'),
                        'scope'         => config('app.scope'),
                        'username'      => $request->email,
                        'password'      => $request->password
                    ]
                ]);

                $body = $response->getBody();
                $token_data = json_decode($body, true);

                Cache::put(self::AUTH_CACHE_KEY, $token_data, Carbon::now()->addSeconds($token_data['expires_in']));

                return $token_data;

            }catch (\Exception $e){

                info('get bee token error:'.$e->getMessage());
                return null;
            }
        }
    }   
}
