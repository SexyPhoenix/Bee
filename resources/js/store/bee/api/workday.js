/*
* @Author: Sexy Phoenix
* @Date:   2019-04-26 17:37:29
* @Last Modified by:   Sexy Phoenix
* @Last Modified time: 2019-07-04 14:37:25
*/

function fetchWeekDayList() 
{
	return $F.apiFetch('getweekdays').then((response) => {
		return response
	})
}

function fetchMonthDayList()
{
	return $F.apiFetch('getmonthdays').then((response) => {
		return response
	})
}

function fetchDateList(params)
{
	return $F.apiFetch('getdates', params).then((response) => {
		return response
	})
}

export default {

  fetchWeekDayList,
  fetchMonthDayList,
  fetchDateList

}
