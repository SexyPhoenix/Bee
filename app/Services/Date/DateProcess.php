<?php 

namespace App\Services\Date;

use Carbon\Carbon;

//date class by zps
class DateProcess{

	//two date diff
	public static function datediff($startDate, $endDate){

        $diff_time = strtotime($endDate) - strtotime($startDate);
        
//        if($diff_time < 0) return '-';
        
        $day = $diff_time/(3600*24);
        
        return $day;		
	}

    //get two date years
    public static function getdateyears($startDate, $endDate, $bool = true){

        $start_date    = strtotime($startDate);
        $end_date      = strtotime($endDate);

        $month_count = 0;
        while(($start_date = strtotime('+1 month', $start_date)) <= $end_date){  
            
            $month_count++;
        }  

        return $bool ? (float)($month_count/12) : $month_count;
    }

	//get week dates
	public static function getweekdates($stringDate){

		$stamp          = strtotime($stringDate);

		$this_year 		= date('Y', $stamp);
		$this_month 	= date('m', $stamp);
		$this_day   	= date('d', $stamp);
		$this_wday   	= date('w', $stamp);

		$_day          = $this_day;
	    for($i = $this_wday; $i > 0; $i--){
	    	
	    	$_day        = $_day - 1;
	    	$days_list[] = date('Ymd', mktime(0, 0, 0, $this_month, $_day, $this_year));	//add this day
	    }  

	    $_day          = $this_day;
	    for($i = $this_wday; $i < 7; $i++){
	    	
	    	$days_list[] = date('Ymd', mktime(0, 0, 0, $this_month, $_day, $this_year));	//add this day
	    	$_day        = $_day + 1;
	    }

	    sort($days_list);

	    return $days_list;	
	}


    /**
     * deal with the event datetime
     * @param $today
     * @param $dttm
     * @return array
     */
    public static function formatEventDttm($today, $dttm)
    {
        if(empty($today) || empty($dttm)){

            return ['position' => '', 'label' => ''];
        }

        $stamp = strtotime($dttm);
        $time = date('H:i', $stamp);

        $days = DateProcess::datediff(date('Y-m-d', strtotime($today)), date('Y-m-d', $stamp));

        if($days > 0){

            $position = 'future';
            if(1 == $days){

                $label = trans('common.tomorrow').' '.$time;
            }elseif(2 == $days){

                $label = trans('common.thedayaftertomorrow').' '.$time;
            }else{

                $label = date('m/d', $stamp).' '.trans('common.weekday.'.date('w', $stamp)).' '.$time;
            }
        }elseif($days == 0){

            $position = 'current';
            $label = trans('common.today').' '.$time;
        }else{

            $position = 'past';
            if(-1 == $days){

                $label = trans('common.yesterday').' '.$time;
            }else{

                $label = date('m/d', $stamp).' '.trans('common.weekday.'.date('w', $stamp)).' '.$time;
            }
        }

        return ['position' => $position, 'label' => $label];
    }

    public static function dateFormat($date, $format = 'Y-m-d H:i:s')
    {

        $dates = is_array($date) ? $date : [$date];

        $date_list = [];
        foreach ($dates as $idx => $date) {

            $date_list[] = Carbon::parse($dates[$idx])->format($format);
        }

        return $date_list;
    }
}