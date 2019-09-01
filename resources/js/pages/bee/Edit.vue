<template>
  <div class="row gutter-xs">
    <div class="bee-form col-md-12">
      <el-dialog
        title="调整申请"
        :visible.sync="dialogFormVisible"
        :close-on-click-modal="false"
        width="40%"
        :before-close="handleClose"
      >
        <div slot="title" class="dialog-title">
          <el-tabs v-model="activeTab" @tab-click="handleClick">
            <el-tab-pane label="请假" name="APPLY_LEAVE">
              <leave-form :item="form" @pick="handleEndProcess"></leave-form>   
            </el-tab-pane>
            <el-tab-pane label="外勤" name="APPLY_OUTER">
              <outer-form :item="form" @pick="handleEndProcess"></outer-form>
            </el-tab-pane>
            <el-tab-pane label="加班" name="APPLY_WORK">
              <work-form :item="form" @pick="handleEndProcess"></work-form>
            </el-tab-pane>
          </el-tabs>
        </div>          
      </el-dialog>
    </div>
  </div>
</template>
<script>

import LeaveForm from './leave/Form'
import OuterForm from './outer/Form'
import WorkForm from './work/Form'
import { OUTER_FORM } from '../../store/bee/constant/apply'

export default {
  name: 'Edit',
  inject: ['leaveList'],
  props: {
    item: Object,
    show: Boolean,
    tab: String
  },
  components: {
    LeaveForm,
    OuterForm,
    WorkForm
  },
  data() {
    return {
      dialogFormVisible: this.show,
      activeTab: this.tab,
      activeType: '',
      form: this.item,
    }
  },
  watch: {
    item: function(newValue) {

      this.form = newValue
    },
    show: function(newValue){

      this.dialogFormVisible = newValue
    },
    tab: function(active){

      this.activeTab = active
    }
  },
  methods: {
    handleClick() {
    
      this.form.type  = this.tab == this.activeTab ? this.activeType : ''
    },
    handleClose() {

      this.leaveList.editShow = false
      this.leaveList.editItem = OUTER_FORM
    },
    handleEndProcess() {

      this.$emit('pick');
    }   
  },
  updated: function (){

    if(this.form.type) {

      this.activeType = JSON.parse(JSON.stringify(this.form.type))
    }
  } 
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
    clear: both
  }
  .bee-form .el-date-editor.el-input, .el-date-editor.el-input__inner {
    width: 200px;
  }
</style>
