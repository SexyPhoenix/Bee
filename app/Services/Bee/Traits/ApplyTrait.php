<?php

/**
 * @Author: Sexy Phoenix
 * @Date:   2019-06-19 10:36:24
 * @Last Modified by:   Sexy Phoenix
 * @Last Modified time: 2019-07-23 11:16:12
 */
namespace App\Services\Bee\Traits;

trait ApplyTrait {

    public function positive(array $items)
    {
        return array_unique(array_map(function($v) {
            return abs($v);
        }, $items));
    }

    public function groupItems(array $items, $kfield, $vfield = 'id')
    {   
        $result = [];
        foreach ($items as $item) {

            $result[$item[$kfield]][] = $item[$vfield];
        }

        return  $result;      
    }

    public function parentGroup(array $compare, array $compared)
    {
        $diff  = array_diff($compared, $compare);

        return $this->getDiffItems($diff);
    }

    public function getDiffItems($items)
    {
        $result = [];

        sort($items);
        $first     = array_shift($items);
        $result[0] = [$first]; 

        if($items) $result[$first] = $items;

        return $result;
    }
    
    //===============================reset parent id==========================================
    
    public function singleId($id) {

        return $id > 0 ? array_merge(
                            [intval($id)], 
                            $this->apply->getChildrenData($id, ['id'])
                            ->pluck('id')
                            ->toArray()
                         )
                       : [abs(intval($id))];

    }

    /**
     * @Author   Sexy Phoenix
     * @DateTime 2019-07-22T16:50:26+0800
     * reset parent id
     * @param    [type] $applies
     * @return   [type]
     */
    public function resetParentId($applies) {

        //selected tranform parent-child
        $group = $this->groupItems(
            $this->apply->getApplyListById($applies, ['id', 'parent_id']),
            'parent_id'
        );
        
        $parents = array_filter(array_keys($group), function($v){

            if($v > 0) return $v;
        });

        //selected's child of parent  
        $group_parent = $this->groupItems(
            $this->apply->getChildrenData(
                array_unique(array_merge($applies, $parents)), ['id', 'parent_id']
            )->toArray(),
            'parent_id'
        );

        foreach ($group as $k => $v) {
            
            $update = [];

            if(0 == $k) {

                foreach ($v as $pid) {
                   
                   if(!array_key_exists($pid, $group) && isset($group_parent[$pid])) {

                        $update = $this->getDiffItems($group_parent[$pid]);
                   }
                }
            } else {
                
                $exisit_parent = in_array($k, $applies);
                $same_count    = count($v) == count($group_parent[$k]);

                if($exisit_parent) {

                    //no handle for same count
                    if($same_count) continue;

                    $update = $this->parentGroup($v, $group_parent[$k]);
                } else {

                    $update = $same_count ? $v : $this->getDiffItems($v);
                }
            }

            if($update) $this->handleParentId($update);
        }
    }

    public function handleParentId($items)
    {
        foreach ($items as $idx => $item) {

            $_map = is_array($item) ? $item : [$item];
            $this->apply->commonOperate($_map, ['parent_id' => $idx]);
        }
    }
}