<template>
  <el-form-item :label="label" prop="dates">
    <el-date-picker 
      type="dates" 
      placeholder="选择一个或多个日期" 
      v-model="dates"
      @change="handleDate"
      value-format="yyyy-MM-dd"
      :disabled="available"
    >
    </el-date-picker>
  </el-form-item>
</template>
<script>
import { mapState, mapActions } from 'vuex'

export default {
  name: 'Date',
  inject: ['leaveForm'],
  props: {
    value: Array,
    label: String,
    disabled: {
      type: Boolean,
      default: false
    }
  },
  data() {
    let _this = this
    return {
      pickerOptions: {
        shortcuts: [{
          text: '本周工作日',
          onClick(picker) {
            
            picker.$emit('pick', _this.weekDaysItems);
          }
        }, {
          text: '本月工作日',
          onClick(picker) {
            
            picker.$emit('pick', _this.monthDaysItems);
          }
        }]
      },
      dates: this.value,
      available: this.disabled
    }
  },
  watch: {
    value : function(newValue) {
      this.dates = newValue
    },
    disabled : function(newValue) {
      this.available = newValue
    }
  },
  computed: {
    ...mapState({
      weekDaysItems: state => state.workday.weekDaysItems,
      monthDaysItems: state => state.workday.monthDaysItems
    })
  }, 
  methods: {
    ...mapActions(['GET_WEEK_WORK_DAYS_DATA', 'GET_MONTH_WORK_DAYS_DATA']),
    handleDate () {
      this.leaveForm.form.date = this.dates
    }
  },
  created() {

    //this.GET_WEEK_WORK_DAYS_DATA()
    //this.GET_MONTH_WORK_DAYS_DATA()
  }
}
</script>
<style>
</style>