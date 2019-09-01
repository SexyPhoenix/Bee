<template>
  <div class="row gutter-xs">
    <div class="col-md-12 bee-tip">
      <el-alert
        type="warning"
        center
        show-icon
      >
        <template slot="title">
          考勤注意事项，详情请点击<a href="#" target="_blank">这里</a>（一定要看哦）
        </template>
      </el-alert>
    </div>
    <router-view></router-view>
  </div>
</template>
<script>
export default {
  name: 'Bee'
}
</script>
<style>
.bee-tip{

  margin: 15px 0;
}
</style>