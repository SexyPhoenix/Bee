/*
* @Author: Sexy Phoenix
* @Date:   2019-05-07 15:52:23
* @Last Modified by:   Sexy Phoenix
* @Last Modified time: 2019-08-22 11:15:10
*/

import * as Type from '../../store/bee/constant/types'

function handleFood(type) {

  return Type.TYPE_SHOW_FOOD.includes(type)
}

function handleTeam(type) {

  return Type.TYPE_SHOW_TEAM.includes(type)
}

function handleDestination(type) {

  return Type.TYPE_SHOW_CITY.includes(type)
}

function handleBudget(type) {

  return Type.TYPE_SHOW_BUDGET.includes(type)
}

function handleHalfDay(type) {

  return Type.TYPE_SHOW_HALF_DAY.includes(type)
}

function handleAllDay(type) {

  return Type.TYPE_SHOW_ALL_DAY.includes(type)
}

function isOuter(type) {

  return Type.TYPE_OUTER.includes(type)
}

function isWork(type) {

  return Type.TYPE_WORK.includes(type)
}

function isChuChai(type) {

  return Type.TYPE_CHU_CHAI == type
}

function needAttach(type) {

  return Type.TYPE_NEED_ATTACH.includes(type)
}

function getTypeRefer(type) {

  return Type.TYPE_REFER.get(type)
}

function getFilterType() {

 let type = []

  for (let [key, value] of Type.TYPE_REFER.entries()) {
    
    type.push({text: value, value: key})
  }

  return type
}

function getWork() {

  return Type.TYPE_JIA_BAN
}

export default {
	
  handleFood,
  handleTeam,
  handleDestination,
  handleBudget,
  handleHalfDay,
  handleAllDay,
  getTypeRefer,
  getFilterType,
  isOuter,
  isWork,
  isChuChai,
  needAttach,
  getWork
}