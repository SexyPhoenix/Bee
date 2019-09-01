<?php

namespace App\Contracts;
/**
 * workflow instance, created by workflow apply activity
 *
 * Created by PhpStorm.
 * User: caifenglei
 * Date: 2017/4/13
 * Time: 9:04
 */
interface WorkflowInstance
{
    /**
     * get the ID of the workflow instance
     * @return mixed
     */
    public function getInstanceId();
}