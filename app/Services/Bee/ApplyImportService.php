<?php 

namespace App\Services\Bee;

use Carbon\Carbon;
use App\Models\Bee\Region;
use App\Models\Bee\Type;
use App\Models\Bee\Apply;
use App\Validator\Bee\ClashValidator;
use App\Validator\Bee\YearValidator;
use App\Validator\Bee\RestValidator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Repositories\Bee\ApplyLockRepository as ApplyLock;
use App\Repositories\Bee\WorkDayRepository;
use App\Repositories\Bee\ApplyRepository;
use App\Repositories\Bee\TimecardRepository;
use App\Repositories\Bee\UserRepository as User;
use App\Repositories\Bee\MessageRepository as Message;
use App\Models\Bee\Contracts\TypeConstantInterface as TypeConstant;
use App\Models\Bee\Contracts\TypeAttrConstantInterface as TypeAttrConstant;
use App\Models\Bee\Contracts\FlowConstantInterface as FlowConstant;

class ApplyImportService
{   
    protected $items;

    protected $work;

    protected $userMap;

    protected $lock;

    protected $lockDate;

    protected $error = [];

    protected $validators;

    protected $ext = ['csv','xls','xlsx'];

    public function __construct(WorkDayRepository $work, ApplyRepository $apply, TimecardRepository $timecard) 
    {
        $this->work       = $work;
        $this->apply      = $apply;
        $this->timecard   = $timecard;
        $this->validators = [

            app()->make(ClashValidator::class),
            app()->make(YearValidator::class),
            app()->make(RestValidator::class)
        ];
    }

    public function import($request)
    {
        $upload = $request->file('attach');
        if($upload->isValid()) {

            $ext      = $upload->getClientOriginalExtension();
            if(!in_array($ext, $this->ext)) return ['result' => false, 'error' => trans('bee.import.ext')];

            $PHPExcel    = IOFactory::load($upload->path());
            $import_data = $PHPExcel->getActiveSheet()->toArray();
            array_shift($import_data);

            if(empty($import_data)) return ['result' => false, 'error' => trans('bee.import.content')];
            
            $this->handleImportData($import_data);

            $this->getLockList();

            $this->setItemInfo();

            $this->validate();

            $this->handle();

            return $this->error 
                ? ['result' => false, 'error' => implode('、', $this->error), 'data' => $this->handle()] 
                : ['result' => true, 'data' => $this->handle()];
        }

        return ['result' => false, 'error' => trans('bee.import.upload')];
    }

    public function saveImport($items)
    {
        if($items) {

            $this->getUserMapById(array_unique(array_pluck($items, 'user_id')));

            $Message = new Message();
            $users   = [];
            foreach ($items as $item) {
                
                $apply_data  = [];
                $user_id     = $users[] = $item['user_id'];

                $_item = [

                    'user_id'      => $user_id,
                    'type_id'      => $item['type_id'],
                    'apply_date'   => $item['apply_date'],
                    'time_section' => $item['time_section'],
                    'start_time'   => $item['start_time'],
                    'end_time'     => $item['end_time'],
                    'duration'     => $item['duration'],
                    'stage_id'     => $item['stage_id'],
                    'status_id'    => $item['status_id'],
                    'flow_user_id' => $item['flow_user_id'],
                    'flow_action'  => $item['flow_action'],
                    'handover'     => 0,
                    'attach'       => '',
                    'meta'         => $item['meta'],
                    'desc'         => $item['desc'],
                    'region'       => $item['region'],
                    'crop_id'      => $item['crop_id'],
                    'parent_id'    => 0,
                    'is_expired'   => $item['is_expired']
                ];

                $apply_data[] = $data = $this->apply->storeApply($_item);

                if(isset($item['children']) && $item['children']) {

                    foreach ($item['children'] as $children) {
                        
                        $_children = $_item;
                        $_children['apply_date'] = $children['apply_date'];
                        $_children['parent_id']  = $data['id'];
                        $_children['is_expired'] = $children['is_expired'];

                        $apply_data[] = $this->apply->storeApply($_children);
                    }
                }

                if($apply_data) {

                    //$this->timecard->transOldData($apply_data, $this->userMap[$user_id]);
                    //charge message
                    $operator = $this->getOperator($this->userMap[$user_id]);
                    $Message->knowMessage($operator, $item['name'], FlowConstant::IMPORT_CHARGE_KNOW, [$data['id']]);
                }
            }

            $users = array_unique($users);
            if($users) {

                foreach ($users as $user) {
                    
                    $Message->knowMessage($user, '');
                }
            }
        }

        return true;
    }

    public function handle()
    {
        $result = [];
        foreach ($this->items as $item) {
            
            $_item    = $item[0];
            $meta     = unserialize($_item['meta']);
            $meta_tag = $this->handleMetaTag($_item['type_id'], $meta, $_item['is_expired']);
            $_item['date']       = [$_item['apply_date']];
            $_item['meta_tag']   = $meta_tag;

            if(1 < count($item)) {

                array_shift($item);
                $_children = [];
                foreach ($item as $m) {
                    
                    $_m = $m;
                    $_m['date']       = [$m['apply_date']];
                    $_m['meta_tag']   = $meta_tag;

                    $_children[] = $_m;
                }

                $_item['children'] = $_children;
                $_item['date'] = array_merge(
                    $_item['date'],
                    array_map(function($item){
                       return current($item['date']); 
                   }, $_children)
                );
                sort($_item['date']);                
            }

            $result[] = $_item;
        }
        
        return $result;
    }

    public function validate()
    {
        foreach ($this->items as $item) {

            foreach ($this->validators as $validator) {
                
                $result = $validator->validate($item, []);

                if(true !== $result) $this->setError($result);
            }
        }
    }

    public function setItemInfo()
    {
        foreach ($this->items as &$item) {

            $_item  = $item[0];
            $job_no = $_item['job_no'];

            if(false === $_item['type_id'] 
                || false === $_item['time_section']
                || '' === $_item['start_time']
                || '' === $_item['end_time']
                || '' === $_item['duration']
            ) $this->setError(trans('bee.import.required'));
            
            if(isset($this->userMap[$job_no])) {

                $user        = $this->userMap[$job_no];

                $_region     = $user['region'] > 200 ? $user['region'] : 200;

                $region_name = User::getRegionByPreName($_region);
                foreach ($item as &$m) {
                    
                    $lock_date       = date('Ym', strtotime($m['apply_date']));

                    $m['user_id']    = $user['user_id'];
                    $m['name']       = $user['name'];
                    $m['region']     = $region_name;
                    $m['crop_id']    = $user['crop_id'];
                    $m['is_expired'] = in_array($lock_date, $this->lock) ? 1 : 0;
                    unset($m['job_no']);
                }                
            }
        }
    }

    public function handleImportData($import_data)
    {
        $type_refer    = Type::getMappingTypeRefer();
        $section_refer = Apply::getOptionRefer();
        $job_no_list   = [];
        $i             = 1;
        foreach ($import_data as $k => $data) {
            
            $type_id  = array_search(trim($data[1]), $type_refer);
            $job_no   = str_pad($data[0], 6, 0, STR_PAD_LEFT);
            $job_no_list[] = $job_no;

            $_item = [
                'job_no'       => $job_no,
                'type_id'      => $type_id,
                'time_section' => array_search(trim($data[5]), $section_refer),
                'start_time'   => $data[6] ? Carbon::parse($data[6])->format('H:i') : '',
                'end_time'     => $data[7] ? Carbon::parse($data[7])->format('H:i') : '',
                'duration'     => $data[8] ? $data[8] : '',
                'desc'         => $data[9],
                'meta'         => $type_id ? $this->initMeta($type_id, $data[2]) : '',
                'stage_id'     => Apply::stage_finished,
                'status_id'    => Apply::status_finished,
                'flow_action'  => Apply::Action_ApplyFinished,
                'flow_user_id' => 0
            ];

            $this->generateApplies($_item, $data[3], $data[4], $i);
        }

        $this->getUserMap(array_unique($job_no_list));
    }

    public function generateApplies($item, $start, $end, &$i)
    {
        $start     = $start ? Carbon::parse($start)->format('Y-m-d') : '';
        $end       = $end ? Carbon::parse($end)->format('Y-m-d') : $start;
        $date_list = $this->work->getDateBetweenList(['start' => $start, 'end' => $end]);

        $this->items[] = array_map(function($v) use ($item, &$i){

            $item['index']      = $i++;
            $item['apply_date'] = $v;
            $this->lockDate[]   = date('Ym', strtotime($v));

            return $item;
        }, $date_list);
    }

    public function getOperator($user)
    {

        //if(!isset($this->userMap[$user_id])) $this->userMap[$user_id] = User::getUserById($user_id); 

        return $user->manager;
    }

    public function getUserMap($job_no)
    {
        $this->userMap = User::getUserByCondition($job_no)->keyBy('job_no')->toArray();
    }

    public function getUserMapById($list)
    {
        $this->userMap = User::getUserById($list)->keyBy('user_id');
    }

    public function getLockList()
    {
        $this->lock = ApplyLock::getLockWithDate(array_unique($this->lockDate))
            ->pluck('lock_date')
            ->all();
    }

   public function handleMetaTag($type, $meta, $is_expired)
    {
        $tag = [];

        if(isset($meta['food']) && $meta['food']) $tag[] = '误餐补助';

        if(isset($meta['work']) && $type == TypeConstant::TYPE_JIA_BAN && $meta['work']) {

            $tag[] = Type::getMappingTypeRefer($meta['work']);
        }

        if(isset($meta['year']) && $type == TypeConstant::TYPE_YEAR_REST && $meta['year']) {

            $tag[] = $meta['year'];
        }

        if(isset($meta['birth']) && $type == TypeConstant::TYPE_CHAN_JIA && $meta['birth']) {

            $tag[] = Type::getMappingTypeRefer($meta['birth']);
        }

        if(1 == $is_expired) $tag[] = '补';

        return $tag;
    }

    public function initMeta($type, $meta)
    {
        $type_refer     = Type::getMappingTypeRefer();
        $old_type_refer = Type::getMappingType();
        $_type          = $old_type_refer[$type];
        $_meta          = $meta ? explode('、', $meta) : '';
        
        $attr  = ['type' => $_type];
        if(TypeConstant::TYPE_GONG_CHU == $type) {

            $attr['food'] = false;
        } else if(TypeConstant::TYPE_CHU_CHAI == $type) {

            $attr['team']        = false;
            $attr['peer']        = $attr['budget'] = [];
            $attr['destination'] = '';
            
        } else if(TypeConstant::TYPE_JIA_BAN == $type) {

            $attr['work'] = TypeAttrConstant::ATTR_GONG_ZUO_RI;
            $attr['food'] = false;
        }  else if(TypeConstant::TYPE_YEAR_REST == $type) {

            $attr['year']   = date('Y');
        }  else if(TypeConstant::TYPE_CHAN_JIA == $type) {

            $attr['birth']   = TypeAttrConstant::ATTR_HAS_ALLOWANCE;
        } 

        if($_meta) {

            foreach ($_meta as $m) {
                
                $m = trim($m);
                if(false !== strpos($m, '误餐')) {

                    $attr['food'] = true;
                } else if (false !== strpos($m, '20')) {

                    $attr['year'] = $m;
                    $attr['type'] = $m % 2 == 0 ? TypeConstant::OLD_EVEN_YEAR_REST : TypeConstant::OLD_YEAR_REST;
                } else if (false !== strpos($m, '无津贴')) {
                    
                    $attr['birth'] = TypeAttrConstant::ATTR_NO_ALLOWANCE;
                    $attr['type']  = TypeConstant::OLD_CHAN_JIA_NO_ALLOWANCE;
                } else if ($work = array_search(trim($m), $type_refer)) {
                    
                    $attr['work'] = $work;
                    $attr['type'] = $old_type_refer[$work];
                }
            }
        }

        return serialize($attr);
    }

    /**
     * @Author   Sexy Phoenix
     * @DateTime 2019-05-23T11:13:04+0800
     * @param    [type] set $error info
     */
    private function setError($error)
    {
        if(!in_array($error, $this->error)) $this->error[] = $error;
    }
}