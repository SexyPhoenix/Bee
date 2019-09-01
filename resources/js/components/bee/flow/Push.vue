<template>
  <el-dialog 
    title="同意并推送上级" 
    :visible.sync="dialogFormVisible"
    :close-on-click-modal="false"
    width="30%"
    :before-close="handleClose"    
  >
    <el-form :model="form">
      <el-form-item :label-width="formLabelWidth">
        <el-radio v-model="form.step" label="1">审批</el-radio>
        <el-radio v-model="form.step" label="2">知晓</el-radio>
      </el-form-item>
      <el-form-item label="参与人" :label-width="formLabelWidth" required>
        <el-autocomplete
          v-model="form.name" 
          placeholder="搜索审批或知晓人" 
          :fetch-suggestions="querySearch"
          @select="handleSelect"
        >
        </el-autocomplete>          
      </el-form-item>
      <el-form-item label="备 注" :label-width="formLabelWidth" required>
        <el-input type="textarea" placeholder="请输入推送原因" v-model="form.remark" maxlength="30"></el-input>
      </el-form-item> 
    </el-form>
    <div slot="footer" class="dialog-footer">
      <el-button type="primary" @click="submitForm()">提 交</el-button>
    </div>
  </el-dialog>

</template>
<script>
import { mapState, mapActions } from 'vuex'

export default {
  name: 'Push',
  inject: ['Flow'],
  props: {
    item: Array,
    show: Boolean
  },
  data() {
    return {
      dialogFormVisible: this.show,
      formLabelWidth: '120px',
      steps: [
        {id: 1, name: '审批'},
        {id: 2, name: '知晓'}
      ],
      users: [],
      form: {
        applyIds: '',
        step: '1',
        user_id: '',
        name: '',
        remark: ''
      }
    }
  },
  watch: {
    item: function(newValue) {

      this.form.applyIds = newValue
    },
    show: function(newValue){

      this.dialogFormVisible = newValue
    }
  },
  computed: {
    ...mapState({
      filterUser: state => state.user.filterUser.data,
      flow: state => state.flow.data
    })
  },
  methods: {
    ...mapActions([
      'FILTER_USER_DATA',
      'POST_CHARGE_AGREE_FORM_DATA',
    ]),
    querySearch(query, cb) {

      if(query !== '') {

        setTimeout(() => {

          this.FILTER_USER_DATA(query).then((data) => {

             this.users = this.filterUser.map(user => {
                   
               let name = user.name + ' [' + user.job_no + ']'
               return {user_id: user.user_id, value: name}
             })

             cb(this.users);
          })
        }, 500);
      } else {

        this.users = []
      }
    },
    handleSelect(item) {

      this.form.user_id = item.user_id
      this.form.name = item.value
    },
    handleClose() {

      this.Flow.showPush = false
    },
    submitForm() {

      if(this.form.applyIds) {

        if(this.form.user_id == '') {

            this.$message.error('请选择参与人');
            return false
        }

        if(this.form.remark == '') {

            this.$message.error('请填写备注');
            return false
        }

        this.POST_CHARGE_AGREE_FORM_DATA(this.form).then(() => {

          if(this.flow.status < 400) {

            this.$message.success('已同意并推送')

            this.form = {
              applyIds: '',
              step: '1',
              user_id: '',
              name: '',
              remark: ''
            }
            
            this.$emit('pick')
            
          } else {

            if(this.flow.status == 401) {
            
              this.$message.error(this.flow.data.message)
            } else {
              this.$message.error('推送失败')
            }            
          }

        })        
      } else {

        this.$message.error('请选择内容')
      }

      this.Flow.showPush = false
    }
  }
}
</script>
<style>
</style>