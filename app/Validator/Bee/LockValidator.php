<?php
/**
 * @Author: Sexy Phoenix
 * @Date:   2019-05-22 15:07:08
 * @Last Modified by:   Sexy Phoenix
 * @Last Modified time: 2019-07-24 11:53:48
 */
namespace App\Validator\Bee;

use App\Services\Date\DateProcess;
use App\Contracts\Bee\ApplyValidatorInterface;
use App\Repositories\Bee\ApplyLockRepository;

class LockValidator implements ApplyValidatorInterface
{
	private $applyLock;

    public function __construct(ApplyLockRepository $applyLock) 
    {
        $this->applyLock = $applyLock;
    }
    
    /**
     * @Author   Sexy Phoenix
     * @DateTime 2019-05-23T11:16:13+0800
     * @param    array $item
     * @return   lock validate
     */
	public function validate($item, $updated)
	{
		$lockCount = $this->getLockCount($this->handleItem($item));

        if($lockCount > 0)  return trans('bee.apply.lock');

        return true;
	}
    
    /**
     * @Author   Sexy Phoenix
     * @DateTime 2019-05-22T17:08:37+0800
     * @param    [type] $item [2019-04-20, 2019-05-20, 2019-05-21]
     * @return   [201904, 201905]
     */
	private function handleItem($item)
	{
    	return array_unique(DateProcess::dateFormat(array_pluck($item, 'apply_date'), 'Ym'));
	}

	private function getLockCount($date)
	{
		return $this->applyLock->getLockCount($date);
	}
}