/*
* @Author: Sexy Phoenix
* @Date:   2019-04-26 15:38:17
* @Last Modified by:   Sexy Phoenix
* @Last Modified time: 2019-08-29 10:08:11
*/
import api from '../api/user' 
import { 

  GET_USER_DATA,
  FILTER_USER_DATA,
  FILTER_DEPT_DATA,
  FILTER_ADDRESS_DATA

} from '../constant/user'

const state = {
  data: {},
  filterUser: [],
  filterDept: [],
  filterAddress: [],
}

const actions = {

  GET_USER_DATA: ({commit}) => {

  	return api.fetchUser()
  	         .then(data => {
  	         	commit(GET_USER_DATA, { item: data })
  	         })
  },
  FILTER_USER_DATA: ({commit}, keyword) => {

    return api.filterUser(keyword)
             .then(data => {
              commit(FILTER_USER_DATA, { item: data })
             })
  },
  FILTER_DEPT_DATA: ({commit}, keyword) => {

    return api.filterDept(keyword)
             .then(data => {
              commit(FILTER_DEPT_DATA, { item: data })
             })
  },
  FILTER_ADDRESS_DATA: ({commit}) => {

    return api.filterAddress()
             .then(data => {
              commit(FILTER_ADDRESS_DATA, { item: data })
             })
  }
}

const mutations = {

  [GET_USER_DATA] (state, payload) {

  	state.data = payload.item
  },
  [FILTER_USER_DATA] (state, payload) {
    
    state.filterUser = payload.item
  },
  [FILTER_DEPT_DATA] (state, payload) {
    
    state.filterDept = payload.item
  },
  [FILTER_ADDRESS_DATA] (state, payload) {
    
    state.filterAddress = payload.item
  }
}

export default {

  state,
  actions,
  mutations
}