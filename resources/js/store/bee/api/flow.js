/*
* @Author: Sexy Phoenix
* @Date:   2019-04-26 17:37:29
* @Last Modified by:   Sexy Phoenix
* @Last Modified time: 2019-07-03 10:35:01
*/

function postChargeAgreeData(params) {

	return $F.apiPost('flow/charge/agree', params).then((response) => {

	  return response
	}, (err) => {
	  return err
	})
}

function postChargeRejectData(params) {

	return $F.apiPost('flow/charge/reject', params).then((response) => {

	  return response
	}, (err) => {
	  return err
	})
}

function fetchChargeItemList(params) {
	
	return $F.apiFetch('flow/charge', params).then((response) => {
		return response
	})
}

function postAttendAgreeData(params) {

	return $F.apiPost('flow/attend/agree', params).then((response) => {

	  return response
	}, (err) => {
	  return err
	})
}

function postAttendRejectData(params) {

	return $F.apiPost('flow/attend/reject', params).then((response) => {

	  return response
	}, (err) => {
	  return err
	})
}

function fetchAttendItemList(params) {
	
	return $F.apiFetch('flow/attend', params).then((response) => {
		return response
	})
}

function postAffairsAgreeData(params) {

	return $F.apiPost('flow/affairs/agree', params).then((response) => {

	  return response
	}, (err) => {
	  return err
	})
}

function postAffairsRejectData(params) {

	return $F.apiPost('flow/affairs/reject', params).then((response) => {

	  return response
	}, (err) => {
	  return err
	})
}

function fetchAffairsItemList(params) {
	
	return $F.apiFetch('flow/affairs', params).then((response) => {
		return response
	})
}

function postCanceltData(params) {

	return $F.apiPost('flow/apply/cancel', params).then((response) => {

	  return response
	}, (err) => {
	  return err
	})
}

export default {
  postChargeAgreeData,
  postChargeRejectData,
  fetchChargeItemList,
  postAttendAgreeData,
  postAttendRejectData,
  fetchAttendItemList,
  postAffairsAgreeData,
  postAffairsRejectData,
  fetchAffairsItemList,
  postCanceltData
}
