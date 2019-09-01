<template>
  <el-form-item :label="label">
    <el-switch v-model="food" @change="handleChange"></el-switch>
  </el-form-item>
</template>
<script>
export default {
  name: 'Food',
  inject: ['leaveForm'],
  props: {
    label: String,
    value: Boolean
  },
  data() {
    return {
      food: this.value
    }
  },
  watch: {
    value : function(newValue, oldValue) {
      this.food = newValue
    }
  },
  methods: {
    handleChange() {

      this.leaveForm.form.meta.food = this.food
    },
  }
}
</script>
<style>
</style>