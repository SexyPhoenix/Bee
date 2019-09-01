<template>
  <div class="rest" v-if="rest.show_rest">
    <div class="rest-head"><b>调休</b></div>
    <div class="rest-content">
      <p>您还有<span class="badge badge-outline-success">{{ rest.left_rest}}</span>小时可调休</p>
    </div>
  </div>
</template>
<script>

import { mapState, mapActions } from 'vuex'

export default {
  name: 'Rest',
  data() {
    return {
    }
  },
  computed: {
    ...mapState({
      rest: state => state.types.rest 
    })
  }, 
  methods: {
    ...mapActions([
      'GET_YEST_DATA'
    ]),  
  },
  created: function () {

    this.GET_YEST_DATA()
  } 
}
</script>
<style>
  .rest .rest-content {
    margin-top: 10px;
  }
</style>