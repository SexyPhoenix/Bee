/*
* @Author: Sexy Phoenix
* @Date:   2019-05-17 13:27:29
* @Last Modified by:   Sexy Phoenix
* @Last Modified time: 2019-08-30 13:47:45
*/

import Cookies from 'js-cookie'

export function getCookie(name) {

  return Cookies.get(name)
}

export function setCookie(name, value) {

  return Cookies.set(name, value)
}

export function removeCookie(name) {

  return Cookies.remove(name)
}
