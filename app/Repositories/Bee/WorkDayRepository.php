<?php
namespace App\Repositories\Bee;

use App\Services\Date\DateProcess;
use App\Models\Bee\Holiday;
use Carbon\Carbon;

class WorkDayRepository {

    /**
     * @Author   Sexy Phoenix
     * @DateTime 2019-05-17T11:45:13+0800
     * @return   current week work day
     */
	public function getWeekWorkDays()
	{
	 	return Holiday::getWorkDays($this->getCurrentWeekDays('Ymd'));
	}
    
    /**
     * @Author   Sexy Phoenix
     * @DateTime 2019-05-17T15:38:54+0800
     * @return   current month work day
     */
	public function getMonthWorkDays()
	{
	 	return Holiday::getWorkDays($this->getMonthWeekDays('Ymd'));
	}

	public function getDateList($request)
	{
		$list = $this->getDateBetweenList(
			json_decode($request->date_section, true)
		);

		$holiday_options = [];
		if($list) {

			$holiday_options = DateProcess::dateFormat(
				Holiday::whereIn(
					'date', 
					DateProcess::dateFormat($list, 'Ymd')
				)
				->get()
				->pluck('date')
				->all()			
			, 'Y-m-d');
		}

		return [

			'options'         => $list,
			'holiday_options' => $holiday_options
		];
	}

	public function diffDays($date)
	{
		return Carbon::parse($date['start'])->diffInDays($date['end']);
	}

	/**
	 * @Author   Sexy Phoenix
	 * @DateTime 2019-06-06T13:38:08+0800
	 * @param    [type] $date Y-m-d
	 * @return   get holiday info
	 */
    public function getMappingByTypeAndDate($date)
    {
		return Holiday::whereIn('date', DateProcess::dateFormat($date, 'Ymd'))
				->get()
				->mapWithKeys(function ($item) {

					return [current(DateProcess::dateFormat($item['date'], 'Y-m-d')) => $item['type']];			
				})->all();
    }

    /**
     * @Author   Sexy Phoenix
     * @DateTime 2019-05-17T15:38:13+0800
     * @param    string $format
     * @return   current week days
     */
	public function getCurrentWeekDays($format)
	{	
		return $this->getDaysList(
			     Carbon::now()->startOfWeek()->subDay(),
			     Carbon::DAYS_PER_WEEK,
			     $format
		       );
	}

	public function getMonthWeekDays($format)
	{	
		return $this->getDaysList(
			     Carbon::now()->startOfMonth()->subDay(),
			     Carbon::now()->endOfMonth()->format('d'),
			     $format
		       );
	}

	public function getDateBetweenList($date)
	{	
		return $this->getDaysList(

			Carbon::parse($date['start']),
			$this->diffDays($date),
			'Y-m-d'
		);
	}

    /**
     * @Author   Sexy Phoenix
     * @DateTime 2019-05-17T15:48:00+0800
     * @param    [type] $startDate
     * @param    [type] $intDay
     * @param    string $format
     * @return   [type] day list
     */
	public function getDaysList($startDate, $intDay, $format = 'Y-m-d H:i:s')
	{

		$result = [$startDate->format($format)];
		for ($i=0; $i < $intDay; $i++) {

		  $result[] = $startDate->addDay()->startOfDay()->copy()->format($format);
		}

		return $result;		
	}


}
