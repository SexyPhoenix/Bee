/*
* @Author: Sexy Phoenix
* @Date:   2019-05-07 15:52:23
* @Last Modified by:   Sexy Phoenix
* @Last Modified time: 2019-06-25 17:24:42
*/

import * as Flow from '../../store/bee/constant/flow'

function getStatusRefer(status) {

  return Flow.STATUS_REFER.get(status)
}

export default {

	getStatusRefer
}