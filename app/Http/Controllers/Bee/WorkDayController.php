<?php

namespace App\Http\Controllers\Bee;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Date\DateProcess;
use App\Repositories\Bee\WorkDayRepository;

class WorkDayController extends Controller
{
    public $workday;

    public function __construct(WorkDayRepository $workday) 
    {
        $this->workday = $workday;
    }

    public function getWeekDays()
    {
        return DateProcess::dateFormat($this->workday->getWeekWorkDays(), 'Y-m-d');
    }

    public function getMonthDays()
    {
        return DateProcess::dateFormat($this->workday->getMonthWorkDays(), 'Y-m-d');
    }

    public function getDateList(Request $request)
    {
        return $this->workday->getDateList($request);
    }  
}
