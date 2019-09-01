/*
* @Author: Sexy Phoenix
* @Date:   2019-04-26 15:38:17
* @Last Modified by:   Sexy Phoenix
* @Last Modified time: 2019-08-07 16:56:27
*/
import api from '../api/apply' 
import { 
  POST_APPLY_FORM_DATA,
  POST_APPLY_IMPORT_DATA,
  POST_UPDATE_APPLY_FORM_DATA,
  GET_APPLY_DATA,
  DELETE_APPLY_DATA,
  POST_APPLY_LOCK_DATA
} from '../constant/apply' 

const state = {
  data: {},
  items: []
}

const actions = {

  POST_APPLY_FORM_DATA: ({commit}, params) => {

  	return api.postApplyData(params)
  	         .then(response => {

  	         	 commit(POST_APPLY_FORM_DATA, { data: response })
  	         })
  },
  POST_APPLY_IMPORT_DATA: ({commit}, params) => {

    return api.postImportData(params)
             .then(response => {

               commit(POST_APPLY_IMPORT_DATA, { data: response })
             })
  },  
  POST_UPDATE_APPLY_FORM_DATA: ({commit}, params) => {

    return api.postUpdateApplyData(params)
             .then(response => {

               commit(POST_UPDATE_APPLY_FORM_DATA, { data: response })
             })
  },
  GET_APPLY_DATA: ({commit}, params) => {

    return api.fetchItemList(params)
             .then(response => {

               commit(GET_APPLY_DATA, { data: response })
             })
  },
  DELETE_APPLY_DATA: ({commit}, params) => {

    return api.deleteItem(params)
             .then(response => {

               commit(DELETE_APPLY_DATA, { data: response })
             })
  },
  POST_APPLY_LOCK_DATA: ({commit}, params) => {

    return api.postLockData(params)
             .then(response => {

               commit(POST_APPLY_LOCK_DATA, { data: response })
             })
  }
}

const mutations = {

  [POST_APPLY_FORM_DATA] (state, payload) {

  	state.data = {status: payload.data.status, data: payload.data.data}
  },
  [POST_APPLY_IMPORT_DATA] (state, payload) {

    state.data = {status: payload.data.status, data: payload.data.data}
  },  
  [POST_UPDATE_APPLY_FORM_DATA] (state, payload) {

    state.data = {status: payload.data.status, data: payload.data.data}
  },
  [GET_APPLY_DATA] (state, payload) {
    
    state.items = payload.data
  },
  [DELETE_APPLY_DATA] (state, payload) {

    state.data = {status: payload.data.status, data: payload.data.data}
  },
  [POST_APPLY_LOCK_DATA] (state, payload) {

    state.data = {status: payload.data.status, data: payload.data.data}
  }
}

export default {

  state,
  actions,
  mutations
}