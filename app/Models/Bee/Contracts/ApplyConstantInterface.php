<?php

/**
 * @Author: Sexy Phoenix
 * @Date:   2019-05-22 15:00:17
 * @Last Modified by:   Sexy Phoenix
 * @Last Modified time: 2019-07-24 15:41:37
 */
namespace App\Models\Bee\Contracts;

interface ApplyConstantInterface
{
    const opt_all   = 0; //全天
    const opt_am    = 1; //上午
    const opt_pm    = 2; //下午
    const opt_other = 3; //其他
    
    const stage_deleted   = -1; //删除申请      
    const stage_init      = 0;  //等待申请
    const stage_filtered  = 1;  //审批阶段
    const stage_finished  = 2;  //审批结束
    
    //stage_deleted
    const status_deleted              = -1;

    //stage_init
    const status_toapply              = 0;
    
    //stage_filtered
    //const status_appling              = 1;
    //const status_modify               = 2;
    const status_dept_adject          = 3;
    const status_dept_adject_reject   = 4;

    const status_push_adject          = 5;
    const status_push_adject_reject   = 6;

    const status_attend_adject        = 7;
    const status_attend_adject_reject = 8;  
    const status_admin_adject         = 9;
    const status_admin_adject_reject  = 10;

    //stage_finished
    const status_finished             = 11;	
	
	const Action_DeleteApply    = 'deay';
	const Action_CancelApply    = 'clay';
    const Action_ApplyQuick     = 'ayqk';
    const Action_EditApply      = 'etay';
    const Action_WaitApply      = 'wtay'; 
    const Action_DeptAdject     = 'dtat';
    const Action_PushAdject     = 'phat';
    const Action_DeptReject     = 'dtrt';
	const Action_AttendAdject   = 'adat';
	const Action_AdminAdject    = 'anat';
	const Action_ApplyFinished  = 'alfh';
}