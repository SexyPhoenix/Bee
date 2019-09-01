/*
* @Author: Sexy Phoenix
* @Date:   2019-04-19 14:04:28
* @Last Modified by:   Sexy Phoenix
* @Last Modified time: 2019-08-30 17:02:35
*/
import Vue from 'vue'
import Router from 'vue-router'

Vue.use(Router)

const VueRouter =  new Router({
	//mode: 'history',
	//base: '/bee',
	routes: [
		{
			path: '/login',
			name: 'Login',			
			component: () => import('../../Login.vue')
		},	
		{
			path: '/apply',
			name: 'HOME',
			meta: {
				requireAuth: true
			},			
			component: () => import('../../pages/bee/Home.vue'),
			children: [
				{
					path: 'leave',
					name: 'APPLY_LEAVE',
					component: () => import('../../pages/bee/leave/Create.vue')
				}, {
					path: 'outer',
					name: 'APPLY_OUTER',
					component: () => import('../../pages/bee/outer/Create.vue')
				}, {
					path: 'work',
					name: 'APPLY_WORK',
					component: () => import('../../pages/bee/work/Create.vue')
				}, {
					path: '/approval/dept',
					name: 'APPROVAL_DEPT',
					component: () => import('../../pages/bee/approval/Flow.vue')
				}, {
					path: '/approval/attend',
					name: 'APPROVAL_ATTEND',
					component: () => import('../../pages/bee/approval/AttendFlow.vue')
				}, {
					path: '/approval/affairs',
					name: 'APPROVAL_AFFAIRS',
					component: () => import('../../pages/bee/approval/AffairsFlow.vue')
				}, {
					path: '/list',
					name: 'LIST_ATTEND',
					component: () => import('../../pages/bee/List.vue')
				}, {
					path: '/import',
					name: 'MANAGE_IMPORT',
					component: () => import('../../pages/bee/Import.vue')
				}, {
					path: '/lock',
					name: 'MANAGE_LOCK',
					component: () => import('../../pages/bee/Lock.vue')
				}
			]
		}	
	]
})

VueRouter.beforeEach((to, from, next) => {

	if(to.matched.some(r => r.meta.requireAuth) && to.path != '/login') {

		if($COOKIE.getCookie('token')) {

			if(to.path === '/login') {
				next({ path: '/apply/leave' }) 
			} else {
				next()
			}
			
		} else {
			next({
				path: '/login',
				query: {redirect: to.fullPath}
			})
		}
	} else {

		if(to.path == '/login') {

			if($COOKIE.getCookie('token')) {

				next({ path: '/apply/leave' })
			} else {

				next() //Prevent death
			}
		} else {

			next('/login')	
		}
		
	}
})

export default VueRouter