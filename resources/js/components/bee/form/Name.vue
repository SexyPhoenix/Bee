<template>
  <el-form-item :label="label">
    {{ name }}
    <input type="hidden" v-model="uid"/>
  </el-form-item> 
</template>
<script>
export default {
  name: 'Name',
  inject: ['leaveForm'],
  props: {
    label: String,
    value: Object
  },
  data() {
    return {
      uid: this.value.user_id,
      name: this.value.name
    }
  },
  watch: {
    value : function(newValue, oldValue) {
      this.uid = newValue.user_id
      this.name = newValue.name
    }
  }
}
</script>
<style>
</style>