/*
* @Author: Sexy Phoenix
* @Date:   2019-04-26 17:37:29
* @Last Modified by:   Sexy Phoenix
* @Last Modified time: 2019-08-28 14:10:54
*/

function postLoginData(params) {

	return $F.apiPost('sso', params).then((response) => {

	  return response
	}, (err) => {
	  return err
	})
}

export default {
  postLoginData
}
