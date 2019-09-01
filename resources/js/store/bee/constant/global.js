/*
* @Author: Sexy Phoenix
* @Date:   2019-05-01 17:38:21
* @Last Modified by:   Sexy Phoenix
* @Last Modified time: 2019-07-16 15:01:36
*/
export const TIME_SECTION_ALL   = 0  //全天
export const TIME_SECTION_AM    = 1  //上午
export const TIME_SECTION_PM    = 2  //下午
export const TIME_SECTION_OTHER = 3  //其他
export const TIME_SECTION_REFER = new Map([
	                                       [TIME_SECTION_ALL, '全天'], 
	                                       [TIME_SECTION_AM, '上午'],
	                                       [TIME_SECTION_PM, '下午'], 
	                                       [TIME_SECTION_OTHER, '其他']
	                                  ])

export const SWITCH_RIGHT_DEFAULT  = 'APPLY_LEAVE'
export const NOTICE_TITLE          = '考勤提醒'

export const TYPE_LEAVE   = 1  //请假
export const TYPE_OUTER   = 2  //外勤
export const TYPE_WORK    = 3  //加班

export const TYPE_FEE = [
				'车船费',
				'补贴',
				'打车费',
				'过桥过路费',
				'公交地铁费',
				'餐费',
				'招待费',
				'住宿费',
				'通信费',
				'签证费',
				'会务费',
				'制作费',
				'自定义'
			 ]

export const ATTR_WORK_RI      = 101 //工作日
export const ATTR_REST_RI      = 102 //休息日
export const ATTR_LAW_RI       = 103 //法定假日
export const ATTR_GONG_ZUO_RI  = 104 //工作日换调休
export const ATTR_ZHOU_MO      = 105 //周末换调休

export const ATTR_WORK_REFER = new Map([
	                                       [ATTR_WORK_RI, '工作日'], 
	                                       [ATTR_REST_RI, '休息日'],
	                                       [ATTR_LAW_RI, '法定假日'], 
	                                       [ATTR_GONG_ZUO_RI, '工作日换调休'], 
	                                       [ATTR_ZHOU_MO, '周末换调休']
	                                  ])

export const ATTR_ODD_YEAR  = 2019 //单年
export const ATTR_EVEN_YEAR = 2020 //双年

export const ATTR_YEAR_REFER = new Map([
	                                       [ATTR_ODD_YEAR, '2019'], 
	                                       [ATTR_EVEN_YEAR, '2020']
	                                  ])

export const ATTR_HAS_ALLOWANCE = 106 //有津贴
export const ATTR_NO_ALLOWANCE  = 107 //无津贴

export const ATTR_BIRTH_REFER = new Map([
										   [ATTR_HAS_ALLOWANCE, '有津贴'],
	                                       [ATTR_NO_ALLOWANCE, '无津贴']
	                                  ])