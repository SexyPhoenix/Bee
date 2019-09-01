<template>
  <el-dialog 
    title="审批" 
    :visible.sync="dialogFormVisible"
    :close-on-click-modal="false"
    width="30%"
    :before-close="handleClose"    
  >
    <el-form :model="form">
      <el-form-item label="备 注" :label-width="formLabelWidth">
        <el-input type="textarea" placeholder="请输入内容" v-model="form.remark" maxlength="30"></el-input>
      </el-form-item> 
    </el-form>
    <div slot="footer" class="dialog-footer">
      <el-button  @click="handleReject">驳回</el-button>
      <el-button type="primary" @click="handlePass">同意</el-button>
    </div>
  </el-dialog>
</template>
<script>
import { mapState, mapActions } from 'vuex'

export default {
  name: 'AffairsApproval',
  inject: ['Flow'],
  props: {
    item: Array,
    show: Boolean
  },
  data() {
    return {
      dialogFormVisible: this.show,
      formLabelWidth: '80px',
      form: {
        applyIds: '',
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
      flow: state => state.flow.data
    })
  },
  methods: {
    ...mapActions([
      'POST_AFFAIRS_AGREE_FORM_DATA',
      'POST_AFFAIRS_REJECT_FORM_DATA'
    ]),
    handleClose() {

      this.Flow.showApproval = false
    },
    handlePass() {

      if(this.form.applyIds) {
        
        this.POST_AFFAIRS_AGREE_FORM_DATA(this.form).then(() => {

          if(this.flow.status < 400) {
            
            this.$message.success('已审批')
            this.form.remark = ''
            
            this.$emit('pick')
          } else {

            this.$message.error('审批失败')
          }
        })        
      } else {

        this.$message.error('请选择内容')
      }

      this.Flow.showApproval = false      
    },
    handleReject() {

      if(this.form.applyIds) {

        if(this.form.remark == '') {

            this.$message.error('请填写备注')
            return false
        }

        this.POST_AFFAIRS_REJECT_FORM_DATA(this.form).then(() => {

          if(this.flow.status < 400) {

            this.$message.success('已审批')
            this.form.remark = ''

            this.$emit('pick')

          } else {

            this.$message.error('审批失败');
          }
        })
      } else {

        this.$message.error('请选择内容');
      }

      this.Flow.showApproval = false 
    }
  }
}
</script>
<style>
</style>