<template>
  <el-form-item :label="label" prop="handover">
    <el-select 
      v-model="handover" 
      clearable
      filterable
      remote
      :remote-method="remoteUser"
      :loading="loading"
      placeholder="请搜索交接人"
      @change="handleChange"
      no-data-text="加载中"
    >
      <el-option
        v-for="item in handovers"
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
  name: 'Handover',
  inject: ['leaveForm'],
  props: {
    label: String,
    value: [String, Number],
    items: {
      type: Array,
      default: function () {
        return []
      }
    }
  },
  data() {
    return {    
      handover: this.value,
      handovers: this.items,      
      loading: false
    }
  },
  watch: {
    value : function(newValue, oldValue) {
      this.handover = newValue
    },
    items : function(newValue, oldValue) {
      this.handovers = newValue
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

      this.leaveForm.form.handover = this.handover
    },
    remoteUser(query) {

      if(query !== '') {

        this.loading = true

        setTimeout(() => {

          this.loading = false
          this.FILTER_USER_DATA(query).then((data) => {

            this.handovers = this.filterUser
             //this.handovers = this.filterUser.map(user => {

               //return {

                //  user_id: user.user_id,
                 // job_no: user.dept_name
                //  label: user.name, 
                //  dept_name: user.dept_name
                //}

               //if(this.leaveForm.form.user.user_id != user.user_id) 
             //})
          })
        }, 500);

      } else {

        this.handovers= []
      }
    }
  }
}
</script>
<style>
</style>