/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

//global
import * as globalConstants from './store/bee/constant/global'
import * as globalConfig from './config/bee'
import * as globalFunction from './utils/common/function'
import * as globalCookie from './utils/common/cookie'

//console.log(VCalendar)
window.$G = globalConstants
window.$C = globalConfig
window.$F = globalFunction
window.$COOKIE = globalCookie

import Vue from 'vue'

import 'element-ui/lib/theme-chalk/index.css'
import ElementUI from 'element-ui'
Vue.use(ElementUI);

import VCalendar from 'v-calendar'
//import 'v-calendar/lib/v-calendar.min.css'

Vue.use(VCalendar)
// Vue.use(VCalendar, {
//   datePickerTintColor: '#409EFF',
//   datePickerShowDayPopover: false,
//   themeStyles: {
//     wrapper: { backgroundColor: 'transparent', border: 'none' }
//   }
// })

import Bee from './Bee.vue'
import router from './router/bee'
import store from './store/bee'

Vue.config.productionTip = false

const beeapp = new Vue({
    router,
    store,	
    render: h => h(Bee)
}).$mount('#bee_app')
