/*
* @Author: Sexy Phoenix
* @Date:   2019-05-07 15:52:23
* @Last Modified by:   Sexy Phoenix
* @Last Modified time: 2019-06-27 14:49:52
*/

import * as Apply from '../../store/bee/constant/apply'

function getStatusRefer(status) {

  return Apply.STATUS_REFER.get(status)
}

function getStatus()
{
	return getFilterStatus(Apply.STATUS_DELETED)
}

function getChargeStatus()
{
	return getFilterStatus(Apply.STATUS_DEPT_ADJECT)
}

function getChargeDefaultStatus()
{
	return [Apply.STATUS_DEPT_ADJECT]
}

function getAttendStatus()
{
	return getFilterStatus(Apply.STATUS_ATTEND_ADJECT)
}

function getAttendDefaultStatus()
{
	return [Apply.STATUS_ATTEND_ADJECT]
}

function getAffairsStatus()
{
	return getFilterStatus(Apply.STATUS_ADMIN_ADJECT)
}

function getAffairsDefaultStatus()
{
	return [Apply.STATUS_ADMIN_ADJECT]
}

function canEdit(status) {

	return Apply.EDIT_APPLY.includes(status)
}

function toApplyPrompt(status) {

	return Apply.TO_APPLY_PROMPT.includes(status)
}

function applingPrompt(status) {

	return Apply.APPLING_PROMPT.includes(status)
}

function rejectPrompt(status) {

	return Apply.REJECT_PROMPT.includes(status)
}

function getFilterStatus(limit) {

	let status = []

	for (let [key, value] of Apply.STATUS_REFER.entries()) {
	  
	  if(key >= limit) {

	  	status.push({text: value, value: key})
	  }
	}

	return status
}

export default {

	getStatusRefer,
	getStatus,
	getChargeStatus,
	getChargeDefaultStatus,
	getAttendStatus,
	getAttendDefaultStatus,
	getAffairsStatus,
	getAffairsDefaultStatus,
	canEdit,
	toApplyPrompt,
	applingPrompt,
	rejectPrompt
}