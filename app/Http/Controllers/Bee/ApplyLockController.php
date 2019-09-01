<?php

namespace App\Http\Controllers\Bee;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Response\Traits\ResponseTrait;
use App\Repositories\Bee\ApplyLockRepository as ApplyLock;

class ApplyLockController extends Controller
{
    use ResponseTrait;

    public function store(Request $request)
    {	
    	$lock_date = str_replace('-', '', $request->date);
        $result    = ApplyLock::storeLock(['lock_date' => $lock_date, 'status_id' => 1]);

        if($result) {

            return $this->withCreated(['message' => trans('bee.lock.success')]);
        }

        return $this->withNotImplemented($result);    	
    }
}
