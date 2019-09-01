<template>
  <el-card>
  	<h4>删除假单</h4>
  	<p>提交人： {{ item.action_user_name }}</p>
  </el-card>
</template>
<script>
export default {
  name: 'Deay',
  props: {
    item: Object
  }
}
</script>
<style>
</style>