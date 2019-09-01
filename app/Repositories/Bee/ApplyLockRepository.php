<?php
namespace App\Repositories\Bee;

use App\Models\Bee\ApplyLock;

class ApplyLockRepository {
  
	public function getLockCount(array $date)
	{
		return ApplyLock::where('status_id', 1)
		         ->whereIn('lock_date', $date)
		         ->count();
	}

	public static function getLockWithDate(array $date)
	{
		return ApplyLock::where('status_id', 1)
		         ->whereIn('lock_date', $date)
		         ->get();		
	}

	public static function storeLock($item)
	{
		return ApplyLock::create($item);
	}
}
