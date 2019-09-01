/*
* @Author: Sexy Phoenix
* @Date:   2019-05-22 09:47:55
* @Last Modified by:   Sexy Phoenix
* @Last Modified time: 2019-06-25 17:23:41
*/

export const POST_CHARGE_AGREE_FORM_DATA  = 'POST_CHARGE_AGREE_FORM_DATA'
export const POST_CHARGE_REJECT_FORM_DATA = 'POST_CHARGE_REJECT_FORM_DATA'
export const GET_CHARGE_APPLY_DATA        = 'GET_CHARGE_APPLY_DATA'

export const POST_ATTEND_AGREE_FORM_DATA  = 'POST_ATTEND_AGREE_FORM_DATA'
export const POST_ATTEND_REJECT_FORM_DATA = 'POST_ATTEND_REJECT_FORM_DATA'
export const GET_ATTEND_APPLY_DATA        = 'GET_ATTEND_APPLY_DATA'

export const POST_AFFAIRS_AGREE_FORM_DATA  = 'POST_AFFAIRS_AGREE_FORM_DATA'
export const POST_AFFAIRS_REJECT_FORM_DATA = 'POST_AFFAIRS_REJECT_FORM_DATA'
export const GET_AFFAIRS_APPLY_DATA        = 'GET_AFFAIRS_APPLY_DATA'

export const POST_CANCEL_FORM_DATA         = 'POST_CANCEL_FORM_DATA'

export const STATUS_AGREE  = 10
export const STATUS_REJECT = -10
export const STATUS_REFER  = new Map([
	                                       [STATUS_AGREE, '同意'], 
	                                       [STATUS_REJECT, '驳回']
	                                  ])
