<template>
  <div class="row gutter-xs">
    <div class="col-md-12 bee-lock">
      <el-form ref="form" :model="form" label-width="80px">
        <el-form-item label="月份">
          <el-date-picker 
            type="month" 
            placeholder="选择月份"
            v-model="form.date"
            value-format="yyyy-MM"
          >
          </el-date-picker>
        </el-form-item>      
        <el-form-item>
          <el-button type="primary" @click="onSubmit">立即锁定</el-button>
        </el-form-item>      
      </el-form>
    </div>
  </div>
</template>

<script>

import { mapState, mapActions } from 'vuex'

export default {
  name: 'Lock',
  data() {
    return {
      form: {
        date: ''
      }    
    }
  },
  computed: {
    ...mapState({
      lock: state => state.apply.data
    })  
  },
  methods: {
    ...mapActions([
      'POST_APPLY_LOCK_DATA'
    ]),
    onSubmit() {
      this.POST_APPLY_LOCK_DATA(this.form).then(() => {

        if(this.lock.status < 400) {

          this.$message.success(this.lock.data.message)

        } else {

          this.$message.error(this.lock.data.message.join('、'))
        }
      })
    }     
  },
}
</script>
<style>
.icon { 
  font-size: 16px; 
  margin-right: 5px;
}
.clearfix:before,
.clearfix:after {
  display: table;
  content: "";
}
.clearfix:after {
  clear: both;
}
.bee-lock {
  background: #fff;
  padding: 50px 0;
}
</style>