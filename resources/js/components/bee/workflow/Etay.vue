<template>
  <el-card>
  	<h5>调整假单</h5>
  	<p>提交人： {{ item.action_user_name }}</p>
    <p>日期： {{ item.action_data.apply_date}}  {{ item.action_data.start_time }} - {{ item.action_data.end_time }}</p>
    <p :title="item.action_data.desc">事由： {{ item.action_data.desc.slice(0,10) }}</p>
  </el-card>
</template>
<script>
export default {
  name: 'Etay',
  props: {
    item: Object
  }
}
</script>
<style>
</style>