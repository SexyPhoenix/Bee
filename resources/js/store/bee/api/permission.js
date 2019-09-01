/*
* @Author: Sexy Phoenix
* @Date:   2019-07-01 14:18:11
* @Last Modified by:   Sexy Phoenix
* @Last Modified time: 2019-07-01 14:18:58
*/

function fetchItemList() {
	
	return $F.apiFetch('getpermission').then((response) => {
		return response
	})
}

export default {
  fetchItemList
}