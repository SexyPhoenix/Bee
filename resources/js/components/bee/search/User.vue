<template>
  <div class="user-search" v-if="showUser">
    <el-select
      v-model="user_ids"
      multiple
      filterable
      remote
      placeholder="请输入姓名/工号/拼音"
      :remote-method="remoteMethod"
      :loading="loading"
      @change="handleChange"
    >
      <el-option
        v-for="item in users"
        :key="item.user_id"
        :value="item.user_id"
        :label="item.name"
      >
        <span>
          {{ item.name }}
          <span style="font-size: 12px;">{{ item.job_no }}</span>
        </span>
        <span style="color: #8492a6; font-size: 12px; margin-left: 20px;">{{ item.dept_name }}</span>      
      </el-option>
    </el-select>
  </div>
</template>
<script>
import { mapState, mapActions } from 'vuex'

export default {
  name: 'User',
  inject: ['Search'],
  props: {
    value: Array
  },
  data() {
    return {
      user_ids: this.value,
      loading: false,
      users: []
    }
  },
  watch: {
    value: function(newValue) {
      this.user_ids = newValue
    }
  },
  computed: {
    ...mapState({
      filterUser: state => state.user.filterUser.data,
      permission: state => state.permission.items
    }),
    showUser() {
      return this.permission.has_affairs 
        || this.permission.has_attend
        || this.permission.has_charge
    }
  }, 
  methods: {
    ...mapActions([
      'FILTER_USER_DATA'
    ]),
    handleChange() {

      this.Search.form.users = this.user_ids
    },  
    remoteMethod(query) {

      if (query !== '') {

        this.loading = true
        setTimeout(() => {

          this.loading = false
          this.FILTER_USER_DATA(query).then((data) => {

             this.users = this.filterUser
          })

        }, 200)
      } else {
        this.users = []
      }
    }
  }
}
</script>
<style>
 .user-search .el-select{ width: 100% }
</style>