<template>
  <div class="row gutter-xs">
    <div class="col-md-12">
  		<el-card shadow="never">
  		  <div slot="header" class="clearfix">
          <span :class="iconClass"></span>
  		    <span><b>{{ title }}</b></span>
  		  </div>
        <slot></slot>        
  		</el-card>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Right',
  props: {
    title: String,
    icon: {
      type: String,
      default: 'icon-info-circle'
    }
  },
  computed: {
    iconClass() {

      return 'icon ' + this.icon
    },
  }
  
}
</script>
<style scoped>
  .icon { 
      font-size: 16px; 
      margin-right: 5px;
  }
</style>
