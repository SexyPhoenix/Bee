<?php
/**
 * @Author: Sexy Phoenix
 * @Date:   2019-05-24 13:33:08
 * @Last Modified by:   Sexy Phoenix
 * @Last Modified time: 2019-06-17 10:15:59
 */
namespace App\Validator\Bee;

use Carbon\Carbon;
use App\Contracts\Bee\ApplyValidatorInterface;
use App\Repositories\Bee\ApplyRepository;
use App\Models\Bee\Contracts\TypeConstantInterface as Type;

class RestValidator implements ApplyValidatorInterface
{
	private $apply;
    
    private $item;
    
    private $error;

    private $user_id;

    private $updated;

    public function __construct(ApplyRepository $apply) 
    {
        $this->apply = $apply;
    }
    
    public function validate($item, $updated)
    {  
        $this->item = $item;
        $this->updated = $updated;

        if(Type::TYPE_TIAO_XIU == current($this->item)['type_id']) {

            $this->user_id = current($this->item)['user_id'];

            $rest_data = $this->apply->getRestApplyInfo([$this->user_id]);

            if($this->getLeftTotal($rest_data) < $this->dayCalculation()
            ) {
                return trans('bee.rest.no');
            }
        }

        return true;
    }
    
    public function getLeftTotal($rest)
    {   
        $left_rest = isset($rest[$this->user_id]['left_rest']) 
            ? $rest[$this->user_id]['left_rest'] 
            : 0;

        return $left_rest + $this->getUpdatedTotal();
    }

    /**
     * @Author   Sexy Phoenix
     * @DateTime 2019-06-14T14:42:12+0800
     * @return   for update's total
     */
    public function getUpdatedTotal()
    {
        return $this->apply->getUpdatedDuration(

            $this->item[0]['user_id'], 
            array_pluck($this->updated, 'id')
        );
    }

    /**
     * @Author   Sexy Phoenix
     * @DateTime 2019-05-24T13:57:36+0800
     * @return   [type] hour count
     */
    private function dayCalculation()
    {
        $result = 0;
        foreach ($this->item as $item) {
            
            $result += $item['duration'];
        }

        return $result;
    }
}