/*
* @Author: Sexy Phoenix
* @Date:   2019-04-26 15:38:17
* @Last Modified by:   Sexy Phoenix
* @Last Modified time: 2019-08-30 13:34:04
*/
import api from '../api/login' 
import { 

  POST_LOGIN_FORM_DATA

} from '../constant/login'

const state = {
  data: {}
}

const actions = {

  POST_LOGIN_FORM_DATA: ({commit}, params) => {

    return api.postLoginData(params)
             .then(response => {

               commit(POST_LOGIN_FORM_DATA, { data: response })
             })
  }
}

const mutations = {

  [POST_LOGIN_FORM_DATA] (state, payload) {

    state.data = { status: payload.data.status, data: payload.data.data }
  }
}

export default {

  state,
  actions,
  mutations
}