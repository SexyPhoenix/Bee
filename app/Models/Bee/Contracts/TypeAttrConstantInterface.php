<?php

/**
 * @Author: Sexy Phoenix
 * @Date:   2019-05-22 15:00:17
 * @Last Modified by:   Sexy Phoenix
 * @Last Modified time: 2019-08-15 17:11:22
 */
namespace App\Models\Bee\Contracts;

interface TypeAttrConstantInterface
{	
	const ATTR_WORK_RI          = 101; //工作日
	const ATTR_REST_RI          = 102; //休息日
	const ATTR_LAW_RI           = 103; //法定假日
    const ATTR_GONG_ZUO_RI      = 104; //工作日换调休
    const ATTR_ZHOU_MO          = 105; //周末换调休
    const ATTR_HAS_ALLOWANCE    = 106; //有津贴
    const ATTR_NO_ALLOWANCE     = 107; //无津贴
    // const ATTR_ODD_YEAR         = 2019; //单年
    // const ATTR_EVEN_YEAR        = 2020; //双年
}