/*
* @Author: Sexy Phoenix
* @Date:   2019-04-26 17:37:29
* @Last Modified by:   Sexy Phoenix
* @Last Modified time: 2019-07-31 13:36:33
*/

function fetchItemList() {

	return $F.apiFetch('type').then((response) => {
		return response
	})
}

function fetchYear() {

	return $F.apiFetch('year').then((response) => {
		return response
	})
}

function fetchRest() {

	return $F.apiFetch('rest').then((response) => {
		return response
	})
}

export default {
  fetchItemList,
  fetchYear,
  fetchRest
}
