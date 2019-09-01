<?php

/**
 * @Author: Sexy Phoenix
 * @Date:   2019-05-22 15:00:17
 * @Last Modified by:   Sexy Phoenix
 * @Last Modified time: 2019-08-16 14:38:49
 */
namespace App\Models\Bee\Contracts;

interface TypeConstantInterface
{
    const TYPE_BING_JIA      = 1; //病假
    const TYPE_SHI_JIA       = 2; //事假
    const TYPE_YEAR_REST     = 3; //年休假
    const TYPE_GONG_CHU      = 4; //公出
    const TYPE_CHU_CHAI      = 5; //出差
    const TYPE_TIAO_XIU      = 6; //调休休息
    const TYPE_GONG_JIA      = 7; //公假
    const TYPE_HUN_JIA       = 8; //婚假
    const TYPE_CHI_DAO       = 9; //迟到
    const TYPE_ZAO_TUI       = 10; //早退
    const TYPE_KUANG_GONG    = 11; //旷工    
    const TYPE_CHAN_JIA      = 12; //产假
    const TYPE_HU_LI_JIA     = 13; //护理假
    const TYPE_SANG_JIA      = 14; //丧假
    const TYPE_JIA_BAN       = 15; //加班
    const TYPE_CHAN_JIAN_JIA = 16; //产检假
    const TYPE_BU_RU_JIA     = 17; //哺乳假
    const TYPE_JIA_BAN_GONG  = 18; //在家办公

    const OLD_BING_JIA       = 1; //病假
    const OLD_SHI_JIA        = 2; //事假
    const OLD_YEAR_REST      = 3; //单年年休假
    const OLD_EVEN_YEAR_REST = 30; //双年年休假
    const OLD_GONG_CHU       = 4; //公出
    const OLD_CHU_CHAI       = 5; //出差
    const OLD_WU_CAN_BU_ZHU  = 24; //误餐补助
    const OLD_TIAO_XIU       = 6; //调休休息
    const OLD_GONG_JIA       = 29; //公假
    const OLD_HUN_JIA        = 8; //婚假
    const OLD_HUN_JIA_FULL   = 9; //婚假(有全勤)
    const OLD_CHI_DAO        = 10; //迟到
    const OLD_ZAO_TUI        = 11; //早退
    const OLD_KUANG_GONG     = 12; //旷工
    const OLD_CHAN_JIA       = 14; //产假
    const OLD_HU_LI_JIA      = 15; //护理假
    const OLD_SANG_JIA       = 18; //丧假
    const OLD_JIA_BAN        = 20; //加班
    const OLD_CHAN_JIAN_JIA  = 31; //产检假
    const OLD_BU_RU_JIA      = 32; //哺乳假
    const OLD_JIA_BAN_GONG   = 33; //在家办公

    const OLD_JIA_BAN_WORK_RI       = 20;  //工作日
    const OLD_JIA_BAN_REST_RI       = 21;  //休息日
    const OLD_JIA_BAN_LAW_RI        = 22;  //法定假日
    const OLD_JIA_BAN_GONG_ZUO_RI   = 27;  //工作日换调休
    const OLD_JIA_BAN_ZHOU_MO       = 28; //周末换调休
    const OLD_CHAN_JIA_NO_ALLOWANCE = 34;  //无津贴
    // const OLD_YEAR_ODD              = 3;   //单年
    // const OLD_YEAR_EVEN             = 30   //双年    
}