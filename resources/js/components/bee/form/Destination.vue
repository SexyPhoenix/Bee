<template>
  <el-form-item :label="label" required>
    <el-input v-model="destination" @change="handleChange"></el-input>
  </el-form-item>
</template>
<script>
export default {
  name: 'Destination',
  inject: ['leaveForm'],
  props: {
    label: String,
    value: String
  },
  data() {
    return {
      destination: this.value
    }
  },
  watch: {
    value : function(newValue, oldValue) {
      this.destination = newValue
    }
  },
  methods: {
    handleChange() {

      this.leaveForm.form.meta.destination = this.destination
    },
  }
}
</script>
<style>
</style>