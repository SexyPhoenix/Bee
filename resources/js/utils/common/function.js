/*
* @Author: Sexy Phoenix
* @Date:   2019-05-17 13:27:29
* @Last Modified by:   Sexy Phoenix
* @Last Modified time: 2019-08-30 13:49:55
*/

import axios from 'axios'

axios.interceptors.request.use(config => {

  let token = $COOKIE.getCookie('token');
  if (token !== undefined) {    

    token = JSON.parse(token)
    config.headers.Authorization = `${token.token_type} ${token.access_token}`;
  }

  return config
}, error => {
  return Promise.reject(error)
})


export function apiFetch(url, params = {}) {

  return new Promise((resolve, reject) => {

  	axios.get( $C.API_DOMAIN + '/' + url, {
        params: params
      })
  	  .then((response) => {
  	  	resolve(response)
  	  }, reject)
  })
}

export function apiPost(url, params) {

  return new Promise((resolve, reject) => {

  	axios.post( $C.API_DOMAIN + '/' + url, params)
  	  .then((response) => {
  	  	 resolve(response)
  	  })
      .catch(({response}) => {
         reject(response)
      })
  })
}

export function apiDelete(url, params) {

  return new Promise((resolve, reject) => {

    axios.get( $C.API_DOMAIN + '/' + url, {
        params: params
      })
      .then((response) => {
         resolve(response)
      })
      .catch(({response}) => {
         reject(response)
      })
  })
}

