<template>
  <el-card>
  	<h5>撤销假单</h5>
    <p>提交人： {{ item.action_user_name }}</p>
    <p :title="item.action_data.remark">销假原因： {{ action.action_data.remark.slice(0,10) }}</p>
  </el-card>
</template>
<script>

export default {
  name: 'Clay',
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
  }
}
</script>
<style>
</style>