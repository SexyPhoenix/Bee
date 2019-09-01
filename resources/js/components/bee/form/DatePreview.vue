<template>
  <el-dialog 
    title="申请日期以勾选为准"
    :visible.sync="dialogTableVisible"
    :close-on-click-modal="false"
    width="30%"
    :before-close="handleClose"
  >
    <el-checkbox v-model="checkHoliday" @change="handleCheckHoliday">节假日全选</el-checkbox>
    <span class="note">（未勾选的为节假日，请视实际情况勾选）</span>
    <el-checkbox-group v-model="dateData.checked_options" @change="handleChange">
      <el-checkbox v-for="date in dateData.options" :label="date" :key="date">{{date}}</el-checkbox>
    </el-checkbox-group>
    <span slot="footer">
      <el-button type="primary" @click="handleClose">确 定</el-button>
    </span>        
  </el-dialog>
</template>
<script>

export default {
  name: 'DatePreview',
  inject: ['leaveForm', 'Dates'],
  props: {
    value: Object,
    show: Boolean
  },
  data() {
    return {
      dialogTableVisible: this.show,
      dateData: JSON.parse(JSON.stringify(this.value)),
      checkedDate:[],
      checkHoliday: false
    }
  },
  watch: {
    value: {
      handler: function (val) { 

        this.dateData = JSON.parse(JSON.stringify(val))   
      },
      deep: true
    },   
    show: function(newValue){

      this.dialogTableVisible = newValue
    }
  },
  methods: {
    handleChange() {

      this.checkHoliday = this.dateData.options.length == this.dateData.checked_options.length
      this.setFormDate()
    },
    handleClose() {

      this.Dates.showDatePreview = false
    },
    handleCheckHoliday() {

      this.dateData.checked_options = this.checkHoliday 
        ? [...this.dateData.checked_options, ...this.dateData.holiday_options]
        : this.removeHoliday()

      this.setFormDate()
    },
    removeHoliday() {

      return this.dateData.checked_options.filter(date => {

        if(!this.dateData.holiday_options.includes(date)) return date
      })
    },
    setFormDate() {
      this.leaveForm.form.date = this.dateData.checked_options.sort()
    }
  }
}
</script>
<style>
</style>