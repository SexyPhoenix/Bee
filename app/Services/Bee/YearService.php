<?php 

namespace App\Services\Bee;

use Carbon\Carbon;
use App\Services\Date\DateProcess;
use App\Repositories\Bee\ApplyRepository;

class YearService
{   

    const ALL_YEAR_DAYS = 365;
    const YEAR_LIMIT    = 1;
    const HALF_OF_DAY   = 0.5;

    const END_MONTH       = 2;
    const END_DAY         = 1;

    const TOTAL_HOURS_DAY = 8;

    //work days
    const WORK_YEAR_ONE   = 10;
    const WORK_YEAR_TWO   = 20;

    //base days
    const BASE_DAYS_ONE   = 0;
    const BASE_DAYS_TWO   = 5;
    const BASE_DAYS_THREE = 10;
    const BASE_DAYS_FOUR  = 15;

    protected $user_id;

    protected $join_date;

    protected $positive_date;

    protected $work_date;

    protected $today_date;
    
    protected $not_year_user = [1, 3, 20, 2, 32, 122, 1350, 26, 2288, 3119, 172, 399, 4506, 10507];
    
    protected $base_days;

    protected $start_date;

    protected $end_date;

    // protected $is_has; //是否有年假

    // protected $desc; //错误提示

    // protected $start_date; //开始日期

    // protected $end_date;//结束日期

    // protected $total_day; //总天数

    protected $cost_days; //使用天数
    
    protected $left_days; //剩余天数

    protected $updated_id; //更新id

    public function __construct(ApplyRepository $apply) 
    {
        $this->apply      = $apply;
    }
    
    public function initialize($user, array $updated_id = [])
    {
        $this->user_id       = $user['user_id'];
        $this->updated_id    = $updated_id;

        //date handle
        $this->join_date     = $this->handleDate($user['join_date']);
        $this->positive_date = $this->handleDate($user['positive_date']);
        $this->work_date     = $this->handleDate($user['work_begin_date']);

        $this->setTodayDate(Carbon::now()->toDateString());

        return $this;
    }

    /**
     * @Author   Sexy Phoenix
     * @DateTime 2019-05-28T09:31:05+0800
     * @param    set this date
     */
    public function setTodayDate($date)
    {
        $this->today_date = $date;
    }

    // public function isHas()
    // {
    //     return $this->is_has;
    // }

    public function getUserYear($lastYear = false)
    {

        $show_this_vacation = $show_last_vacation = 0;
        $render_data = [];

        $desc = $this->getDesc();
        if(is_null($desc)) {   

            $this->getYearInfo();

            $render_data['vacation_total'] = $this->base_days;
            $render_data['vacation_cost']  = $this->cost_days;   
            $render_data['vacation_left']  = $this->left_days;
            $render_data['start_date']     = $this->start_date;
            $render_data['end_date']       = $this->end_date;
            $desc                          = '';
            $show_this_vacation            = 1;

            if($lastYear) {

                $this_year = Carbon::parse($this->today_date)->year;
                $last_date = Carbon::create($this_year)->subYear()->endOfYear();

                $this->setTodayDate($last_date->toDateString());
                
                $this->getYearInfo();

                $render_data['last_year']               = $last_date->year;
                $render_data['last_vacation_total']     = $this->base_days;
                $render_data['last_vacation_cost']      = $this->cost_days;
                $render_data['last_vacation_left']      = $this->left_days;
                $render_data['last_vacation_completed'] = 0 == $this->left_days ? 1: 0;
                $render_data['last_start_date']         = $this->start_date;
                $render_data['last_end_date']           = $this->end_date;
                $show_last_vacation                     = 1;
            }
        }

        $render_data['user_id']         = $this->user_id;
        $render_data['show_vacation']   = $show_this_vacation;
        $render_data['show_last']       = $show_last_vacation;  
        $render_data['show_desc']       = $desc;

        return $render_data;
    }
    
    /**
     * @Author   Sexy Phoenix
     * @DateTime 2019-05-28T09:29:27+0800
     * @return   year info
     */
    protected function getYearInfo()
    {
        //base days
        $this->getBaseDays();

        //date section 
        $this->getStartDate();
        $this->getEndDate();

        //cost days
        $this->getCostYearDays();

        //left days
        $this->leftDays();
    }
    
    /**
     * @Author   Sexy Phoenix
     * @DateTime 2019-05-28T09:30:07+0800
     * @return   apply total days
     */
    protected function getCostYearDays()
    {
        $this->cost_days = $this->apply->getCostYearDuration(
            $this->user_id,
            $this->start_date,
            $this->end_date,
            $this->updated_id
        ) / self::TOTAL_HOURS_DAY;
    }
    
    /**
     * @Author   Sexy Phoenix
     * @DateTime 2019-05-28T09:29:53+0800
     * @return   year limit
     */
    private function getDesc()
    {
        if(!$this->work_date) return trans('bee.year.no'); //$this->notYearUser()

        if($this->notWorkOneYear()) return trans('bee.year.no');

        if($this->notPositive()) return trans('bee.year.positive');

        return null;
    }
    
    private function notWorkOneYear()
    {
        return $this->getDiffYears($this->today_date, $this->work_date) < self::YEAR_LIMIT ? true : false;
    }

    private function notPositive()
    {
        return empty($this->positive_date) || $this->positive_date > $this->today_date; 
    }
    
    /**
     * @Author   Sexy Phoenix
     * @DateTime 2019-05-28T09:30:30+0800
     * @return   executive no year
     */
    private function notYearUser()
    {
        return in_array($this->user_id, $this->not_year_user);
    }

    /**
     * @Author   Sexy Phoenix
     * @DateTime 2019-05-28T09:28:59+0800
     * @return   year days
     */
    private function getBaseDays()
    {
        $this_day      = Carbon::parse($this->today_date);
        $join_day      = Carbon::parse($this->join_date);
        $work_day      = Carbon::parse($this->work_date);
        $join_year_diff = floor($this->getDiffYears($this->join_date, $this->today_date));

        $this->base_days = $this->getYearBaseDays();

        if($join_year_diff < self::YEAR_LIMIT) {

            $next_year_work_day = $work_day->copy()->addYear();

            $cal_start = $this_day->isSameYear($join_day) //同年或者去年以及更早
                ? max($next_year_work_day, $join_day) 
                : max($next_year_work_day, $this_day->copy()->startOfYear());

            $cal_end   = $this_day->copy()->addYear()->startOfYear();

            if(self::BASE_DAYS_ONE == $this->base_days) {

                $this->base_days = self::BASE_DAYS_ONE;
            } else {

                $cal_val = $this->getDiffYears($cal_start, $cal_end) * $this->base_days;   
                $int_val = floor($cal_val);

                $this->base_days = ($cal_val - $int_val) >= self::HALF_OF_DAY 
                    ? ($int_val + self::HALF_OF_DAY) 
                    : $int_val;
            }
        }
    }

    /**
     * @Author   Sexy Phoenix
     * @DateTime 2019-05-28T09:28:36+0800
     * @return   base of year 
     */
    private function getYearBaseDays()
    {
        $year_diff      = floor($this->getDiffYears($this->today_date, $this->work_date));

        return $year_diff >= self::WORK_YEAR_TWO 
            ? self::BASE_DAYS_FOUR 
            : ($year_diff >= self::WORK_YEAR_ONE 
                ? self::BASE_DAYS_THREE 
                : ($year_diff ? self::BASE_DAYS_TWO : self::BASE_DAYS_ONE)
              );
    }
    
    /**
     * @Author   Sexy Phoenix
     * @DateTime 2019-05-28T09:28:24+0800
     * @return   start date
     */
    private function getStartDate()
    {
        
        if(0 == $this->base_days) return '';

        $this_day      = Carbon::parse($this->today_date);
        $join_day      = Carbon::parse($this->join_date);
        $work_day      = Carbon::parse($this->work_date);

        $next_year_work_day = $work_day->copy()->addYear();

        $date = $this_day->isSameYear($join_day)
           ? max($next_year_work_day, $join_day)
           : ($work_day->year == $this_day->copy()->subYear()->year
                ? $next_year_work_day
                : $this_day->startOfYear()
              );
        
        $this->start_date = $date->format('Y-m-01');
    }
    
    /**
     * @Author   Sexy Phoenix
     * @DateTime 2019-05-28T09:27:27+0800
     * @return   default last day of February
     */
    private function getEndDate()
    {
        
        $this_year = Carbon::parse($this->today_date)->year;
        
        $this->end_date = Carbon::create($this_year, self::END_MONTH, self::END_DAY)
            ->addYear()
            ->endOfMonth()
            ->toDateString();
    }
    
    /**
     * @Author   Sexy Phoenix
     * @DateTime 2019-05-28T09:27:10+0800
     * @return   left year 
     */
    private function leftDays()
    {
        $_left = $this->base_days - $this->cost_days;

        $this->left_days =  $_left <= 0 ? 0 : $_left;
    }

    /**
     * @Author   Sexy Phoenix
     * @DateTime 2019-05-28T09:26:18+0800
     * @param    [type] $start_year
     * @param    [type] $end_year
     * @return   diff between dates
     */
    private function getDiffYears($start_year, $end_year)
    {
        return Carbon::parse($start_year)->diffInYears($end_year);
    }
    
    /**
     * @Author   Sexy Phoenix
     * @DateTime 2019-05-28T09:25:53+0800
     * @param    [type] $date
     * @return   fomat date
     */
    private function handleDate($date)
    {
        return $date ? Carbon::parse($date)->toDateString() : '';
    }   

}