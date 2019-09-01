/*
* @Author: Sexy Phoenix
* @Date:   2019-05-22 09:47:55
* @Last Modified by:   Sexy Phoenix
* @Last Modified time: 2019-08-22 11:13:27
*/

import { TYPE_JIA_BAN } from './types'

export const POST_APPLY_FORM_DATA        = 'POST_APPLY_FORM_DATA'
export const POST_APPLY_IMPORT_DATA      = 'POST_APPLY_IMPORT_DATA'
export const POST_UPDATE_APPLY_FORM_DATA = 'POST_UPDATE_APPLY_FORM_DATA'
export const GET_APPLY_DATA              = 'GET_APPLY_DATA'
export const DELETE_APPLY_DATA           = 'DELETE_APPLY_DATA'
export const POST_APPLY_LOCK_DATA        = 'POST_APPLY_LOCK_DATA'

export const STATUS_DELETED              = -1
export const STATUS_TOAPPLY              = 0
// export const STATUS_APPLING              = 1
export const STATUS_DEPT_ADJECT          = 3
export const STATUS_DEPT_ADJECT_REJECT   = 4
export const STATUS_DEPT_PUSH            = 5
export const STATUS_ATTEND_ADJECT        = 7
export const STATUS_ATTEND_ADJECT_REJECT = 8
export const STATUS_ADMIN_ADJECT         = 9
export const STATUS_ADMIN_ADJECT_REJECT  = 10
export const STATUS_FINISHED             = 11

export const STATUS_REFER = new Map([
                                         [STATUS_DELETED, '已删除'], 
                                         [STATUS_TOAPPLY, '等待申请'],
                                         //[STATUS_APPLING, '部门审批'], 
                                         [STATUS_DEPT_ADJECT, '主管待审批'], 
                                         [STATUS_DEPT_ADJECT_REJECT, '主管驳回'],
                                         //[STATUS_DEPT_PUSH, '推送申请'], 
                                         [STATUS_ATTEND_ADJECT, '考勤员待复核'], 
                                         [STATUS_ATTEND_ADJECT_REJECT, '考勤员驳回'], 
                                         [STATUS_ADMIN_ADJECT, '管理员待终核'], 
                                         [STATUS_ADMIN_ADJECT_REJECT, '管理员驳回'], 
                                         [STATUS_FINISHED, '已归档']
                                    ])

//edit apply
export const EDIT_APPLY = [

  STATUS_TOAPPLY,
  STATUS_DEPT_ADJECT,
  STATUS_DEPT_ADJECT_REJECT,
  STATUS_ATTEND_ADJECT_REJECT,
  STATUS_ADMIN_ADJECT_REJECT
]

//to apply
export const TO_APPLY_PROMPT = [STATUS_TOAPPLY]

//appling
export const APPLING_PROMPT = [STATUS_DEPT_ADJECT, STATUS_ATTEND_ADJECT, STATUS_ADMIN_ADJECT]

//reject apply
export const REJECT_PROMPT = [STATUS_DEPT_ADJECT_REJECT, STATUS_ATTEND_ADJECT_REJECT, STATUS_ADMIN_ADJECT_REJECT]


export const COMMON_LABEL = {

  name_label: '姓名',
  type_label: '类别',
  date_label: '日期',
  time_section_label: '时段',
  time_picker_label: '时间',
  desc_label: '事由',
  handover_label: '交接人',
  upload_label: '附件'
}

export const OUTER_LABEL = {
  ...COMMON_LABEL, 
  ...{
    meta_label: {
      food: '误餐补助',
      destination: '城市',
      team: '团队',
      peer: '同行人',
      budget: '预算'
    }
  }
}

export const WORK_LABEL = {
  ...COMMON_LABEL, 
  ...{
    meta_label: {
      food: '误餐补助'
    }
  }
}

export const COMMON_FORM = {

  user: {},
  type: '',
  date: [],
  date_section: null,
  time_section: 0,
  start_time: '08:45',
  end_time: '17:30',
  desc: '',
  handover: '',
  attach: ''
}

export const OUTER_FORM = {
  ...COMMON_FORM, 
  ...{
    meta: {
      food: false,
      destination: '',
      team: true,
      peer: [],
      budget: []
    }
  }
}

export const WORK_FORM = {
  ...COMMON_FORM, 
  ...{
    type: '',
    meta: {
      food: false
    }
  }
}

export const COMMON_RULE = {
  
  type: [{
  	required: true,
  	message: '请选择类别',
  	trigger: 'change'
  }],
  time_section: [{ 
  	required: true,
  	message: '请选择时段',
  	trigger: 'change' 
  }]
}
