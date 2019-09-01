<template>
  <el-form-item :label="label" required>
    <el-input type="textarea" v-model="desc" @change="handleChange" :rows="3"></el-input>
  </el-form-item>
</template>
<script>
export default {
  name: 'Descr',
  inject: ['leaveForm'],
  props: {
    label: String,
    value: String
  },
  data() {
    return {
      desc: this.value
    }
  },
  watch: {
    value : function(newValue, oldValue) {
      this.desc = newValue
    }
  },
  methods: {
    handleChange() {

      this.leaveForm.form.desc = this.desc
    },
  }
}
</script>
<style>
</style>