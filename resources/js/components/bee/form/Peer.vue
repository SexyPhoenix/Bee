<template>
  <el-form-item :label="label" required>
    <el-select 
      v-model="peer" 
      multiple
      filterable
      remote
      :remote-method="remoteUser"
      :loading="loading"
      placeholder="请搜索同行人"
      @change="handleChange"
      no-data-text="加载中"
    >
      <el-option
        v-for="item in peers"
        :key="item.user_id"
        :value="item.user_id"
        :label="item.name"
      >
        <span style="float: left">
          {{ item.name }}
          <span style="font-size: 12px;">{{ item.job_no }}</span>
        </span>
        <span style="float: right; color: #8492a6; font-size: 12px; margin-left: 20px;">{{ item.dept_name }}</span>      
      </el-option>
    </el-select>
  </el-form-item>
</template>
<script>
import { mapState, mapActions } from 'vuex'

export default {
  name: 'Peer',
  inject: ['leaveForm'],
  props: {
    label: String,
    value: Array,
    items: {
      type: Array,
      default: function () {
        return []
      }
    }
  },
  data() {
    return {    
      peer: this.value,
      peers: this.items,      
      loading: false
    }
  },
  watch: {
    value : function(newValue, oldValue) {
      this.peer = newValue
    },
    items : function(newValue, oldValue) {
      this.peers = newValue
    }
  },
  computed: {
    ...mapState({
      filterUser: state => state.user.filterUser.data
    })
  }, 
  methods: {
    ...mapActions([
      'FILTER_USER_DATA'
    ]),
    handleChange() {

      this.leaveForm.form.meta.peer = this.peer
    },
    remoteUser(query) {

      if(query !== '') {

        this.loading = true

        setTimeout(() => {

          this.loading = false
          this.FILTER_USER_DATA(query).then((data) => {

            this.peers = this.filterUser.filter(user => {

             if(this.leaveForm.form.user.user_id != user.user_id) {
             
               return user
             }
            })
          })
        }, 500);

      } else {

        this.peers= []
      }
    }
  }
}
</script>
<style>
</style>