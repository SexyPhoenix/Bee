<template>
  <el-form-item :label="label" prop="time_section">
    <el-radio-group 
      v-model="time_section"
      @change="handleChange"
    >
      <el-radio v-for="item in items" :label="item.id" :key="item.id">{{ item.name }}</el-radio>
    </el-radio-group>
  </el-form-item>
</template>
<script>
export default {
  name: 'TimeSection',
  inject: ['leaveForm'],
  props: {
    value: [String, Number],
    label: String,
    items: Array
  },
  data() {
    return {
      time_section: this.value
    }
  },
  watch: {
    value : function(newValue, oldValue) {
      this.time_section = newValue
    }
  },
  methods: {
    handleChange() {

      this.leaveForm.form.time_section = this.time_section

      this.initTimeSection()
    },
    initTimeSection() {

      this.leaveForm.showTime = $G.TIME_SECTION_OTHER == this.time_section ? true : false
    }
  },
  updated: function () {
  
    if(this.time_section) this.initTimeSection()
  }
}
</script>
<style>
</style>