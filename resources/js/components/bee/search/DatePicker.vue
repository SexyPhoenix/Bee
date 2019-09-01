<template>
  <v-date-picker
    mode='range'
    v-model='dates'
    is-inline
    @input="handleChange"
  />
</template>
<script>
import moment from 'moment'
export default {
  name: 'DatePicker',
  inject: ['Search'],
  props: {
    value: {
      type: Object,
      default: null
    }
  },
  watch: {
    value: function(newValue) {
      this.dates = newValue
    }
  },  
  data() {
    return {
      dates: this.value,
      themeStyles: {
        wrapper: {

          backgroundColor: 'transparent',
          border: 'none'
        }
      },
      tintColor: '#409EFF',
      showDayPopover: false
    }
  },
  methods: {
    handleChange(val) {

      const start = moment(val.start).format("YYYY-MM-DD")
      const end = moment(val.end).format("YYYY-MM-DD")
      this.Search.form.dates = {start: start, end: end}
    },
  }
}
</script>
<style>
</style>