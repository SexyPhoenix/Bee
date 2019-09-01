<template>
  <div class="row gutter-xs">
    <div class="col-md-12">
  		<el-card shadow="never">
  		  <div slot="header" class="clearfix">
          	<span class="icon icon-info-circle"></span>
  		    <span><b>{{ title }}</b></span>
  		  </div>
        <component v-bind:is="item.component"></component>
        <div class="notice" v-html="item.desc"></div>
  		</el-card>
    </div>
  </div>
</template>
<script>
import Year from './type/Year'
import Rest from './type/Rest'

export default {
  name: 'Notice',
  props: {
  	title: String,
    item: Object
  },
  components: {
    Year,
    Rest
  }
}
</script>
<style>
  .notice .item {
  	margin-bottom: 10px;
  	line-height: 20px;
  }
  .notice .item .desc{
    padding: 3px 15px;
  }
  .icon { 
    font-size: 16px; 
    margin-right: 5px;
  }
</style>