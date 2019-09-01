<template>
  <el-card>
  	<h5>审批假单</h5>
    <p>审批人： {{ item.action_user_name }}</p>
    <p>审批结果： {{ statusHtml() }}</p>
    <p>审批备注： {{ action.action_data.remark.slice(0,10) }}</p>
    <div v-if="showStep()">
      <p>推送状态： {{ stepHtml() }} </p>
      <p>已推送知情人： {{ action.action_data.name }}</p>
    </div>    
  </el-card>
</template>
<script>

import UtilFlow from '../../../utils/bee/flow'
export default {
  name: 'Dtat',
  props: {
    item: Object
  },
  data() {
    return {
      action: this.item
    }
  },
  watch: {
    item : function(newValue) {
      this.action = newValue
    }
  },
  computed: {
  	statusHtml() {
      return function () {
        return UtilFlow.getStatusRefer(this.action.status_id)
      }
  	},
    showStep() {
      return function () {

        return 'step' in this.action.action_data ? true : false
      }
    },
    stepHtml() {
      return function () {
        return 1 == this.action.action_data.step ? '审批' : '知晓'
      }
    }
  }
}
</script>
<style>
</style>