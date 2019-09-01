/*
* @Author: Sexy Phoenix
* @Date:   2019-04-26 17:37:29
* @Last Modified by:   Sexy Phoenix
* @Last Modified time: 2019-08-07 16:58:18
*/

function postApplyData(params) {

	return $F.apiPost('apply/save', params).then((response) => {

	  return response
	}, (err) => {
	  return err
	})
}

function postImportData(params) {

	return $F.apiPost('apply/saveimport', params).then((response) => {

	  return response
	}, (err) => {
	  return err
	})
}

function postLockData(params) {

	return $F.apiPost('apply/lock', params).then((response) => {

	  return response
	}, (err) => {
	  return err
	})
}

function postUpdateApplyData(params) {

	return $F.apiPost('apply/edit', params).then((response) => {

	  return response
	}, (err) => {
	  return err
	})
}

function fetchItemList(params) {
	
	return $F.apiFetch('apply/list', params).then((response) => {
		return response
	})
}

function deleteItem(params) {

	return $F.apiDelete('apply/delete', params).then((response) => {

	  return response
	}, (err) => {
	  return err
	})
}

export default {
  postApplyData,
  postImportData,
  postUpdateApplyData,
  fetchItemList,
  deleteItem,
  postLockData
}
