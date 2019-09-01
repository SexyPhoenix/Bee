<template>
  <div class="row gutter-xs">
    <div class="col-md-12">
      <el-dialog
        title="销假"
        :visible.sync="dialogFormVisible"
        :close-on-click-modal="false"
        width="25%"
        :before-close="handleClose"
      >
        <el-form :model="form">
          <el-form-item label="备注" required :label-width="formLabelWidth">
            <el-input type="textarea" v-model="form.remark"></el-input>
          </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="handleClose">取 消</el-button>
          <el-button type="primary" @click="handleCancel">确 定</el-button>
        </div>          
      </el-dialog>
    </div>
  </div>
</template>
<script>

import Fremark from '../../components/bee/flow/Fremark'

import { mapState, mapActions } from 'vuex'

export default {
  name: 'Cancel',
  inject: ['leaveList'],
  props: {
    value: Object,
    show: Boolean

  },
  components: {
    Fremark
  },
  computed: {
    ...mapState({
      flow: state => state.flow.data
    }),    
  },
  data() {
    return {
      formLabelWidth: '80px',
      dialogFormVisible: this.show,
      form: {
        applyIds: [],
        remark: ''
      }
    }
  },
  watch: {
    value: function (newValue) { 

      this.form.applyIds = 0 < Object.keys(newValue).length ? [newValue.id] : []
      this.form.remark = ''
    },
    show: function(newValue){

      this.dialogFormVisible = newValue
    }
  },
  methods: {
    ...mapActions([
      'POST_CANCEL_FORM_DATA'
    ]),
    handleClose() {
      this.resetCancel()
    },
    handleCancel() {

      if(this.form.remark == '') {

          this.$message.error('请填写备注');
          return false
      }

      this.POST_CANCEL_FORM_DATA(this.form).then(() => {

        if(this.flow.status < 400) {

          this.$message.success('耶~销假成功');

          this.$emit('pick');
          this.resetCancel()

        } else {

          this.$message.error('销假失败');
        }

      })
    },
    resetCancel() {

      this.leaveList.cancelShow = false
      this.leaveList.cancelItem = {}    
    }
  }
}
</script>
<style>

</style>
