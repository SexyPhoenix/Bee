/*
* @Author: Sexy Phoenix
* @Date:   2019-04-26 17:37:29
* @Last Modified by:   Sexy Phoenix
* @Last Modified time: 2019-08-01 17:00:44
*/

function fetchUser() 
{
	return $F.apiFetch('getuserinfo').then((response) => {
		return response
	})
}

function filterUser(keyword) 
{
	return $F.apiFetch('getuserlist?keyword=' + keyword).then((response) => {
		return response
	})
}

function filterDept(keyword) 
{
	return $F.apiFetch('getdeptlist').then((response) => {
		return response
	})
}

function filterAddress() 
{
	return $F.apiFetch('getaddress').then((response) => {
		return response
	})
}

export default {

  fetchUser,
  filterUser,
  filterDept,
  filterAddress
}
