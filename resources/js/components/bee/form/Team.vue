<template>
  <el-form-item :label="label" required>
    <el-switch v-model="team" @change="handleChange"></el-switch>
  </el-form-item>
</template>
<script>
export default {
  name: 'Team',
  inject: ['leaveForm'],
  props: {
    label: String,
    value: Boolean
  },
  data() {
    return {
      team: this.value
    }
  },
  watch: {
    value : function(newValue, oldValue) {
      this.team = newValue
    }
  },
  methods: {
    handleChange() {

      this.leaveForm.form.meta.team = this.team
    },
  }
}
</script>
<style>
</style>