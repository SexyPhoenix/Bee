<template>
  <el-card>
  	<h5>终核假单</h5>
    <p>管理员： {{ item.action_user_name }}</p>
  	<p>审批结果： {{ statusHtml() }}</p>
  	<p :title="item.action_data.remark">审批备注： {{ action.action_data.remark.slice(0,10) }}</p>
  </el-card>
</template>
<script>

import UtilFlow from '../../../utils/bee/flow'
export default {
  name: 'Anat',
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
  	}
  }
}
</script>
<style>
</style>