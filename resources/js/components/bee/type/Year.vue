<template>
  <div class="year">
    <div class="year-head"><b>今年年假</b></div>
    <div class="year-content" v-if="year.show_vacation">
      <p>全年在职可休 <span class="badge badge-outline-danger">{{ year.vacation_total}}</span>天。</p> 
      <p>已休 <span class="badge badge-outline-default">{{ year.vacation_cost}}</span>天, 剩余 <span class="badge badge-outline-success">{{ year.vacation_left}}</span>天。</p>
      <p>起止日期{{ year.start_date}}至{{ year.end_date}}</p>
    </div>
    <div v-else>
      <p>参加工作满一年，并且转正后可休年休假</p>
    </div>
  </div>
</template>
<script>

import { mapState, mapActions } from 'vuex'

export default {
  name: 'Year',
  data() {
    return {
    }
  },
  computed: {
    ...mapState({
      year: state => state.types.year 
    })
  }, 
  methods: {
    ...mapActions([
      'GET_YEAR_DATA'
    ]),  
  },
  created: function () {

    this.GET_YEAR_DATA()
  } 
}
</script>
<style>
  .year .year-content {
    margin-top: 10px;
  }
</style>