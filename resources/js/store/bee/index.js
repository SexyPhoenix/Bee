/*
* @Author: Sexy Phoenix
* @Date:   2019-04-19 14:04:50
* @Last Modified by:   Sexy Phoenix
* @Last Modified time: 2019-08-28 14:15:59
*/
import Vue from 'vue'
import Vuex from 'vuex'
import types from './modules/types'
import workday from './modules/workday'
import user from './modules/user'
import apply from './modules/apply'
import flow from './modules/flow'
import permission from './modules/permission'
import login from './modules/login'

Vue.use(Vuex)

export default new Vuex.Store({
	modules: {
	  types,
	  workday,
	  user,
	  apply,
	  flow,
	  permission,
	  login
	}
})
