<?php
namespace App\Repositories\Bee;

use App\Models\Bee\ApplyWorkflow as Flow;

class ApplyFlowRepository {

	public static function storeWorkFlow($item)
	{
		return Flow::insert($item);
	}

	public static function getFlowByApplyId($id)
	{
		$query = Flow::Apply($id);

		$query->orderBy('created_at', 'desc');
		
		return $query->get();
	}

	public static function getFlowByUserAndAction($userId, $actions)
	{

		$query = Flow::select('apply_id')
			->user($userId)
			->action($actions);

		return $query->get();

	}
}
