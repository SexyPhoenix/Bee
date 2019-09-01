/*
* @Author: Sexy Phoenix
* @Date:   2019-04-26 15:38:17
* @Last Modified by:   Sexy Phoenix
* @Last Modified time: 2019-08-30 14:48:24
*/
import api from '../api/types' 
import { GET_TYPE_LIST_DATA, GET_YEAR_DATA, GET_YEST_DATA} from '../constant/types' 

const state = {
  data: [],
  year: {},
  rest: {}
}

const actions = {

  GET_TYPE_LIST_DATA: ({commit}) => {

  	return api.fetchItemList()
  	         .then(data => {
  	         	commit(GET_TYPE_LIST_DATA, { items: data })
  	         })
  },
  GET_YEAR_DATA: ({commit}) => {

    return api.fetchYear()
             .then(data => {
              commit(GET_YEAR_DATA, { items: data })
             })
  },
  GET_YEST_DATA: ({commit}) => {

    return api.fetchRest()
             .then(data => {
              commit(GET_YEST_DATA, { items: data })
             })
  }
}

const mutations = {

  [GET_TYPE_LIST_DATA] (state, payload) {

  	state.data = payload.items.data
  },
  [GET_YEAR_DATA] (state, payload) {

    state.year = payload.items.data
  },
  [GET_YEST_DATA] (state, payload) {

    state.rest = payload.items.data
  }
}

export default {

  state,
  actions,
  mutations
}