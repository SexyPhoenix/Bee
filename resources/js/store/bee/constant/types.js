/*
* @Author: Sexy Phoenix
* @Date:   2019-04-26 16:18:22
* @Last Modified by:   Sexy Phoenix
* @Last Modified time: 2019-08-01 16:32:03
*/
export const GET_TYPE_LIST_DATA = 'GET_TYPE_LIST_DATA'
export const GET_YEAR_DATA      = 'GET_YEAR_DATA'
export const GET_YEST_DATA      = 'GET_YEST_DATA'

export const TYPE_BING_JIA      = 1 //病假
export const TYPE_SHI_JIA       = 2 //事假
export const TYPE_YEAR_REST     = 3 //年休假
export const TYPE_GONG_CHU      = 4 //公出
export const TYPE_CHU_CHAI      = 5 //出差
export const TYPE_TIAO_XIU      = 6 //调休休息
export const TYPE_GONG_JIA      = 7 //公假
export const TYPE_HUN_JIA       = 8 //婚假
export const TYPE_CHI_DAO       = 9 //迟到
export const TYPE_ZAO_TUI       = 10 //早退
export const TYPE_KUANG_GONG    = 11 //旷工    
export const TYPE_CHAN_JIA      = 12 //产假
export const TYPE_HU_LI_JIA     = 13 //护理假
export const TYPE_SANG_JIA      = 14 //丧假
export const TYPE_JIA_BAN       = 15 //加班
export const TYPE_CHAN_JIAN_JIA = 16 //产检假
export const TYPE_BU_RU_JIA     = 17 //哺乳假
export const TYPE_JIA_BAN_GONG  = 18 //在家办公

export const TYPE_REFER = new Map([
	                                       [TYPE_GONG_CHU, '公出'], 
	                                       [TYPE_CHU_CHAI, '出差'],
	                                       [TYPE_JIA_BAN, '加班'],
	                                       [TYPE_TIAO_XIU, '调休'],
	                                       [TYPE_YEAR_REST, '年休假'],	
	                                       [TYPE_SHI_JIA, '事假'],                                       
	                                       [TYPE_BING_JIA, '病假'],
	                                       [TYPE_HUN_JIA, '婚假'], 
                                           [TYPE_CHAN_JIAN_JIA, '产检假'], 
                                           [TYPE_CHAN_JIA, '产假'],
 										   [TYPE_HU_LI_JIA, '护理假'],
 										   [TYPE_BU_RU_JIA, '哺乳假'],
 										   [TYPE_SANG_JIA, '丧假'],
	                                       [TYPE_GONG_JIA, '公假'], 
	                                       [TYPE_CHI_DAO, '迟到'], 
	                                       [TYPE_ZAO_TUI, '早退'], 
	                                       [TYPE_KUANG_GONG, '旷工'], 
	                                       [TYPE_JIA_BAN_GONG, '在家办公']
	                                  ])


//误餐补助
export const TYPE_SHOW_FOOD = [TYPE_GONG_CHU, TYPE_JIA_BAN] //公出、周末加班

//城市
export const TYPE_SHOW_CITY = [TYPE_CHU_CHAI] //出差

//团队
export const TYPE_SHOW_TEAM = [TYPE_CHU_CHAI] //出差

//预算
export const TYPE_SHOW_BUDGET = [TYPE_CHU_CHAI] //出差

//半天假
export const TYPE_SHOW_HALF_DAY = [TYPE_YEAR_REST, TYPE_HUN_JIA, TYPE_HU_LI_JIA] //年休假、婚假、护理假

//全天假
export const TYPE_SHOW_ALL_DAY  = [TYPE_CHAN_JIA] //产假

//外勤
export const TYPE_OUTER = [TYPE_GONG_CHU, TYPE_CHU_CHAI]

//外勤
export const TYPE_WORK = [TYPE_JIA_BAN]

export const TYPE_NEED_ATTACH = [TYPE_HUN_JIA] //TYPE_BING_JIA,  TYPE_HU_LI_JIA