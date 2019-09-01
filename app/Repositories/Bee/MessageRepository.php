<?php
namespace App\Repositories\Bee;

use App\Services\FocusMessage;
use App\Repositories\Bee\ApplyRepository;
use App\Repositories\Bee\UserRepository as User;
use App\Models\Bee\Contracts\FlowConstantInterface as FlowConstant;

class MessageRepository {

	protected $apply;

	protected $dept_url;

	public function __construct(){

		$this->apply    = new ApplyRepository();
 		$this->dept_url = config('app.url').'/bee#/approval/dept';
 		$this->list_url   = config('app.url').'/bee#/list';
	}

	public function chargeMessage($operator, $name, $step = FlowConstant::STEP_DEFAULT, array $list = [])
	{

		$content = $this->getContent($name, $step);

		if($step != FlowConstant::STEP_KNOW) return FocusMessage::send($operator, $this->dept_url, $content);

		if($list) {

			foreach ($list as $id) {
				
				FocusMessage::send($operator, $this->list_url.'?id='.$id, $content);
			}
		}
	}

	public function knowMessage($operator, $name, $step = FlowConstant::IMPORT_KNOW, array $list = [])
	{

		$content = $this->getContent($name, $step);

		if($step == FlowConstant::IMPORT_KNOW) return FocusMessage::send($operator, $this->list_url, $content);

		if($list) {

			foreach ($list as $id) {
				
				FocusMessage::send($operator, $this->list_url.'?id='.$id, $content);
			}
		}
	}

	public function chargeRejectMessage($applies, $name)
	{
		$user_ids = array_unique(
			array_pluck(
				$this->apply->getApplyListById($applies),
				'user_id'
			)
		);
        
		foreach ($user_ids as $id) {

	        FocusMessage::send(

	            $id,
	            $this->list_url,
	            '考勤：'.$name.'驳回了您的申请，请知晓'
	        );
		}		
	}

	public function getContent($name, $step)
	{

		$content = [

			FlowConstant::STEP_CANCEL         => '考勤：'.$name.'有一条销假申请，请审批',
			FlowConstant::STEP_DEFAULT        => '考勤：'.$name.'添加了一条申请，请审批',
			FlowConstant::STEP_APPROVAL       => '考勤：'.$name.'推送了一条申请，请审批',
			FlowConstant::STEP_KNOW           => '考勤：'.$name.'推送了一条申请，请知晓',
			FlowConstant::IMPORT_KNOW         => '考勤：您有一条申请，请知晓',
			FlowConstant::IMPORT_CHARGE_KNOW  => '考勤：'.$name.'有一条申请，请知晓',
			FlowConstant::DESTROY_CHARGE_KNOW => '考勤：'.$name.'删除了一条申请，请知晓'
		];

		return $content[$step];
	}
}
