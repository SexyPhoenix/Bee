<template>
  <el-form-item :label="label" required>
    <v-date-picker
      mode='range'
      v-model="date_section"
      :popover="inputProps"
      :themeStyles="themeStyles"
      :showDayPopover="showDayPopover"
      :tintColor="tintColor"
      @input="handleChange"
      show-caps   
    >
      <el-input 
        placeholder="选择一个或多个日期"
        v-model="date_input"
        @clear="handleClear"
        clearable
        class="date-input"
      >
      </el-input>
    </v-date-picker>
    <el-button 
      icon="el-icon-edit"
      v-show="showEditButton"
      @click="editDialog"
    >
    </el-button>
    <date-preview :value="previewDate" :show="showDatePreview"></date-preview>
  </el-form-item>
</template>
<script>
import moment from 'moment'
import DatePreview from './DatePreview'

import { mapState, mapActions } from 'vuex'

export default {
  name: 'Dates',
  inject: ['leaveForm'],
  props: {
    label: String
  },
  provide() {
    return {
      Dates: this
    };
  },
  components: {
    DatePreview
  },
  data() {
    return {
      date_section: null,
      date_input: '',
      available: this.disabled,
      themeStyles: {
        wrapper: {

          backgroundColor: '#fff',
          border: '1px solid #E4E7ED'
        }
      },
      tintColor: '#409EFF',
      showDayPopover: false,
      inputProps: {

        readonly: this.available,
        placement: 'bottom',
        visibility: 'click'
      },
      showEditButton: false,
      showDatePreview: false,
      previewDate: {}
    }
  },
  computed: {
    ...mapState({
      items: state => state.workday.items.data
    })
  },
  methods: {
    ...mapActions(['GET_DATES_DATA']),
    handleChange (val) {

      this.setDatesFormat(val)
      this.getPreviewDate(val)
      this.showEditButton = true
    },
    handleClear() {

      this.date_section   = {}
      this.showEditButton = false
    },
    editDialog() {
      this.showDatePreview = true
    },
    getPreviewDate(val) {

      let object = {

        start: moment(val.start).format("YYYY-MM-DD"),
        end : moment(val.end).format("YYYY-MM-DD")
      }

      this.GET_DATES_DATA({date_section: object}).then(() => {

        this.setCheckedDate(this.items)
        this.leaveForm.form.date = this.previewDate.checked_options

        if(this.previewDate.options.length > 1) this.showDatePreview = true
      })    
    },
    setCheckedDate(items) {

      items['checked_options'] = []

      items.options.filter(date => {

        if(!items.holiday_options.includes(date) || items.options.length < 2) items['checked_options'].push(date)    
      })

      this.previewDate = items
    },
    setDatesFormat(val) {

      this.date_input = moment(val.start).format("MM/DD") + ' - ' + moment(val.end).format("MM/DD")      
    }
  }
}
</script>
<style>
  .bee-form .el-input.date-input { width: 200px; border: 1px solid #DCDFE6; border-radius: 4px;}
</style>