/*
* @Author: Sexy Phoenix
* @Date:   2019-04-26 15:38:17
* @Last Modified by:   Sexy Phoenix
* @Last Modified time: 2019-07-04 14:37:12
*/
import api from '../api/workday' 
import { 

  GET_WEEK_WORK_DAYS_DATA, 
  GET_MONTH_WORK_DAYS_DATA,
  GET_DATES_DATA

} from '../constant/workday'

const state = {
  weekDaysItems: [],
  monthDaysItems: [],
  items: []
}

const actions = {

  GET_WEEK_WORK_DAYS_DATA: ({commit}) => {

  	return api.fetchWeekDayList()
  	         .then(data => {
  	         	commit(GET_WEEK_WORK_DAYS_DATA, { items: data })
  	         })
  },
  GET_MONTH_WORK_DAYS_DATA: ({commit}) => {

    return api.fetchMonthDayList()
             .then(data => {
              commit(GET_MONTH_WORK_DAYS_DATA, { items: data })
             })
  },
  GET_DATES_DATA: ({commit}, params) => {

    return api.fetchDateList(params)
             .then(data => {
              commit(GET_DATES_DATA, { items: data })
             })
  }
}

const mutations = {

  [GET_WEEK_WORK_DAYS_DATA] (state, payload) {

  	state.weekDaysItems = payload.items
  },
  [GET_MONTH_WORK_DAYS_DATA] (state, payload) {

    state.monthDaysItems = payload.items
  },
  [GET_DATES_DATA] (state, payload) {

    state.items = payload.items
  }
}

export default {

  state,
  actions,
  mutations
}