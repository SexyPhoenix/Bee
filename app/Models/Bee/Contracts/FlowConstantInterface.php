<?php

/**
 * @Author: Sexy Phoenix
 * @Date:   2019-05-22 15:00:17
 * @Last Modified by:   Sexy Phoenix
 * @Last Modified time: 2019-08-12 15:40:49
 */
namespace App\Models\Bee\Contracts;

interface FlowConstantInterface
{	
	const STEP_CANCEL          = -1;
	const STEP_DEFAULT         = 0;
    const STEP_APPROVAL        = 1;
    const STEP_KNOW            = 2;
    
    const IMPORT_KNOW          = 3;
    const IMPORT_CHARGE_KNOW   = 4;

    const DESTROY_CHARGE_KNOW  = 5;

    const status_agree  = 10;
    const status_reject = -10;
     
}