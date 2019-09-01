<?php
namespace App\Repositories\Bee;

use App\Models\Bee\Type;
use Illuminate\Support\Facades\Redis;
use App\Repositories\Bee\ApplyRepository;

class TypeRepository {

	public $user;

	public function __construct()
	{
		$this->user = auth('api')->user();
	}

	public function getTypes()
	{
		return Type::getTypeList();
	}

	public function getYear()
	{
		$year = app()
			->make(ApplyRepository::class)
			->getYearApplyInfo([$this->user->id]);

		return $year ? current($year) : $year;
	}

	public function getRest()
	{
		$rest = app()
			->make(ApplyRepository::class)
			->getRestApplyInfo([$this->user->id]);
			
		return $rest ? current($rest) : $rest;
	}
}
