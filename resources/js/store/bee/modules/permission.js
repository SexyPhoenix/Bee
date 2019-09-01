/*
* @Author: Sexy Phoenix
* @Date:   2019-07-01 14:19:56
* @Last Modified by:   Sexy Phoenix
* @Last Modified time: 2019-08-31 15:49:07
*/
import api from '../api/permission'

import { 
  GET_PERMISSION_LIST
} from '../constant/permission'  

const state = {
  items: []
}

const actions = {

  GET_PERMISSION_LIST: ({commit}) => {

  	return api.fetchItemList()
  	         .then(data => {
  	         	commit(GET_PERMISSION_LIST, { items: data })
  	         })
  }
}

const mutations = {

  [GET_PERMISSION_LIST] (state, payload) {

  	state.items = payload.items.data
  }
}

export default {

  state,
  actions,
  mutations
}