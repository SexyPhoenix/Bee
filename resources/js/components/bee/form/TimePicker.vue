<template>
  <el-form-item :label="label" v-show="isShow" required>
    <el-time-select
      placeholder="起始时间"
      v-model="start_time"
      :picker-options="timeOptions"
      @change="handleChange"
    >
    </el-time-select>
    <el-time-select
      placeholder="结束时间"
      v-model="end_time"
      :picker-options="timeOptions"
      @change="handleChange"
    >
    </el-time-select>            
  </el-form-item>
</template>
<script>
export default {
  name: 'TimePicker',
  inject: ['leaveForm'],
  props: {
    label: String,
    start: String,
    end: String,
    show: Boolean
  },
  data() {
    return {
      timeOptions: {
        start: '00:00',
        step: '00:05',
        end: '23:55'
      },
      start_time: this.start,
      end_time: this.end,
      isShow: this.show
    }
  },
  watch: {
    start : function(newValue, oldValue) {
      this.start_time = newValue
    },
    end : function(newValue, oldValue) {
      this.end_time = newValue
    },
    show : function(newValue, oldValue) {
      this.isShow = newValue
    }
  },
  methods: {
    handleChange() {

      this.leaveForm.form.start_time = this.start_time
      this.leaveForm.form.end_time = this.end_time
    },
  }
}
</script>
<style>
</style>