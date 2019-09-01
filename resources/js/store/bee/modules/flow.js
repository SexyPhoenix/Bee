/*
* @Author: Sexy Phoenix
* @Date:   2019-04-26 15:38:17
* @Last Modified by:   Sexy Phoenix
* @Last Modified time: 2019-06-21 14:10:02
*/
import api from '../api/flow' 
import { 
  POST_CHARGE_AGREE_FORM_DATA,
  POST_CHARGE_REJECT_FORM_DATA,
  GET_CHARGE_APPLY_DATA,
  POST_ATTEND_AGREE_FORM_DATA,
  POST_ATTEND_REJECT_FORM_DATA,
  GET_ATTEND_APPLY_DATA,
  POST_AFFAIRS_AGREE_FORM_DATA,
  POST_AFFAIRS_REJECT_FORM_DATA,
  GET_AFFAIRS_APPLY_DATA,
  POST_CANCEL_FORM_DATA
} from '../constant/flow' 

const state = {
  data: {},
  chargeItems: [],
  attendItems: [],
  affairsItems: []
}

const actions = {

  POST_CHARGE_AGREE_FORM_DATA: ({commit}, params) => {

  	return api.postChargeAgreeData(params)
  	         .then(response => {

  	         	 commit(POST_CHARGE_AGREE_FORM_DATA, { data: response })
  	         })
  },
  POST_CHARGE_REJECT_FORM_DATA: ({commit}, params) => {

    return api.postChargeRejectData(params)
             .then(response => {

               commit(POST_CHARGE_REJECT_FORM_DATA, { data: response })
             })
  },
  GET_CHARGE_APPLY_DATA: ({commit}, params) => {

    return api.fetchChargeItemList(params)
             .then(response => {

               commit(GET_CHARGE_APPLY_DATA, { data: response })
             })
  },
  POST_ATTEND_AGREE_FORM_DATA: ({commit}, params) => {

    return api.postAttendAgreeData(params)
             .then(response => {

               commit(POST_ATTEND_AGREE_FORM_DATA, { data: response })
             })
  },
  POST_ATTEND_REJECT_FORM_DATA: ({commit}, params) => {

    return api.postAttendRejectData(params)
             .then(response => {

               commit(POST_ATTEND_REJECT_FORM_DATA, { data: response })
             })
  },
  GET_ATTEND_APPLY_DATA: ({commit}, params) => {

    return api.fetchAttendItemList(params)
             .then(response => {

               commit(GET_ATTEND_APPLY_DATA, { data: response })
             })
  },
  POST_AFFAIRS_AGREE_FORM_DATA: ({commit}, params) => {

    return api.postAffairsAgreeData(params)
             .then(response => {

               commit(POST_AFFAIRS_AGREE_FORM_DATA, { data: response })
             })
  },
  POST_AFFAIRS_REJECT_FORM_DATA: ({commit}, params) => {

    return api.postAffairsRejectData(params)
             .then(response => {

               commit(POST_AFFAIRS_REJECT_FORM_DATA, { data: response })
             })
  },
  GET_AFFAIRS_APPLY_DATA: ({commit}, params) => {

    return api.fetchAffairsItemList(params)
             .then(response => {

               commit(GET_AFFAIRS_APPLY_DATA, { data: response })
             })
  },
  POST_CANCEL_FORM_DATA: ({commit}, params) => {

    return api.postCanceltData(params)
             .then(response => {

               commit(POST_CANCEL_FORM_DATA, { data: response })
             })
  }
}

const mutations = {

  [POST_CHARGE_AGREE_FORM_DATA] (state, payload) {

  	state.data = {status: payload.data.status, data: payload.data.data}
  },
  [POST_CHARGE_REJECT_FORM_DATA] (state, payload) {

    state.data = {status: payload.data.status, data: payload.data.data}
  },
  [GET_CHARGE_APPLY_DATA] (state, payload) {
    
    state.chargeItems = payload.data
  },
  [POST_ATTEND_AGREE_FORM_DATA] (state, payload) {

    state.data = {status: payload.data.status, data: payload.data.data}
  },
  [POST_ATTEND_REJECT_FORM_DATA] (state, payload) {

    state.data = {status: payload.data.status, data: payload.data.data}
  },
  [GET_ATTEND_APPLY_DATA] (state, payload) {
    
    state.attendItems = payload.data
  },
  [POST_AFFAIRS_AGREE_FORM_DATA] (state, payload) {

    state.data = {status: payload.data.status, data: payload.data.data}
  },
  [POST_AFFAIRS_REJECT_FORM_DATA] (state, payload) {

    state.data = {status: payload.data.status, data: payload.data.data}
  },
  [GET_AFFAIRS_APPLY_DATA] (state, payload) {
    
    state.affairsItems = payload.data
  },
  [POST_CANCEL_FORM_DATA] (state, payload) {
    
    state.data = payload.data
  }
}

export default {

  state,
  actions,
  mutations
}