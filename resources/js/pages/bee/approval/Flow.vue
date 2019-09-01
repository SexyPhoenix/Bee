<template>
  <div class="row gutter-xs">
    <div class="col-md-12">
      <el-card shadow="never">
        <div slot="header" class="clearfix">
          <span class="icon icon-check"></span>
          <span><b>审批</b></span>
        </div>
        <div class="row gutter-xs flow-form">
          <div class="col-md-12">
            <el-button type="primary" @click="approvalClick">审批</el-button>
            <el-button type="primary" plain @click="pushClick">同意并推送相关知情人</el-button>
            <el-tooltip 
              class="item" 
              effect="light" 
              content="如员工连续请假3天以上或情况特殊的，可视情况判断是否需要推送给知情人审批或知晓" 
              placement="right-start"
              popper-class=""
            >
              <i class="icon el-icon-question"></i>
            </el-tooltip>
          </div>   
        </div> 
        <el-table
          border
          ref="flowTable"
          :data="tableData"
          tooltip-effect="light"
          :row-class-name="handleRowClass"
          row-key="id"
          @select="rowSelect"
          @select-all="allSelect"
          @filter-change="filterChange"                      
        >
          <el-table-column
            type="selection"
            width="40"
            :selectable="canSelected"
          >
          </el-table-column>
          <el-table-column label="日期" prop="date" fixed width="415">
            <template slot-scope="props">
              {{ dateHtml(props.row.date) }} 
              <el-tag type="success" style="margin-left: 3px">{{ timeSectionHtml(props.row) }}</el-tag>
              <el-tag 
                type="success"
                style="margin-left: 3px"
                v-for="(tag, index) in props.row.meta_tag"
                :key="index"
              >
                {{ tag }}
              </el-tag>
              <el-tag type="danger" style="margin-left: 3px" v-if="props.row.is_cancel">销假</el-tag>
              <el-tag type="warning" style="margin-left: 3px" v-if="props.row.push_tag">{{ props.row.push_tag }}</el-tag>
              <a style="margin-left: 3px" v-if="props.row.attach" :href="props.row.attach" target="_blank"><i class="el-icon-picture"></i></a>
            </template>
          </el-table-column>
          <el-table-column label="姓名" prop="name" width="90" fixed>
            <template slot-scope="props">
              <el-popover trigger="hover" placement="right-start" :title="'日期：' + dateHtml(props.row.date)">
                <el-timeline>
                  <el-timeline-item v-for="item in  props.row.flows" :timestamp="item.created_at" placement="top" :key="item.flow_id">
                    <work-flow :item="item"></work-flow> 
                  </el-timeline-item>
                </el-timeline>                                                   
                <div slot="reference">
                  {{ props.row.user.name }} <i class="el-icon-view"></i>
                </div>
              </el-popover>
            </template>
          </el-table-column>
          <el-table-column
            label="类别"
            prop="type" 
            width="80"
            column-key="type"
            :filters="filterType"            
          >
            <template slot-scope="props">
              {{ typeHtml(props.row.type) }}
            </template>
          </el-table-column>
          <el-table-column label="事由" prop="desc" show-overflow-tooltip></el-table-column>
          <el-table-column 
            label="状态" 
            prop="status_id"
            width="120"
            column-key="status"
            :filters="filterStatus"
            :filtered-value="query.status"
          >
            <template slot-scope="props">
              <el-tag>{{ statusHtml(props.row.status_id) }}</el-tag>
            </template>
          </el-table-column>         
        </el-table> 
        <div class="row">
          <div class="bee-page col-md-12 text-right">
            <el-pagination
              background
              layout="sizes, prev, pager, next"
              :total="total"
              :page-size="query.pagesize"
              :page-sizes="[15, 50, 100, 200]"
              @current-change="handleCurrentChange"
              @size-change="handleSizeChange"
            >
            </el-pagination>        
          </div>
        </div>                
      </el-card>
      <push :item="form.applyIds" :show="showPush" @pick="handleSubmit"></push>
      <approval :item="form.applyIds" :show="showApproval" @pick="handleSubmit"></approval>      
    </div>
  </div>
</template>
<script>
import WorkFlow from '../WorkFlow'
import Push from '../../../components/bee/flow/Push'
import Approval from '../../../components/bee/flow/Approval'
import Fremark from '../../../components/bee/flow/Fremark'
import UtilType from '../../../utils/bee/type'
import UtilApply from '../../../utils/bee/apply'

import { mapState, mapActions } from 'vuex'

export default {
  name: 'Flow',
  props: {
    search: Object,
  },
  provide() {
    return {
      Flow: this
    };
  },
  inject: ['Bee'],
  components: {
    Push,
    Fremark,
    WorkFlow,
    Approval
  },
  watch: {
    search: {
      handler: function (val) { 

        Object.assign(this.query, val)
        this.getApplyData()
      },
      deep: true
    }
  },
  computed: {
    ...mapState({
      applies: state => state.flow.chargeItems.data,
      flow: state => state.flow.data,
    }),
    dateHtml() {
      return function (date) {

        let length = date.length
        let first  = date[0].replace(/-/g, '/')
        return length > 1 ? first + ' - ' + date[length -1 ].replace(/-/g, '/') : first
      }
    },
    timeSectionHtml() {
      return function (row) {
        return $G.TIME_SECTION_OTHER == row.time_section 
                ? (row.start_time + '-' + row.end_time)
                : $G.TIME_SECTION_REFER.get(row.time_section)
      }
    },
    typeHtml() {
      return function (type) {
        return UtilType.getTypeRefer(type)
      }      
    },
    statusHtml() {
      return function (status) {
        return UtilApply.getStatusRefer(status)
      }
    }
  },
  data() {
    return {
      tableData: [],
      query: {
        page: 1,
        pagesize: 15,
        status: [],
        type: []
      },
      form: {
        applyIds: [],
        remark: ''
      },
      total: 0,
      showPush: false,
      showApproval: false,
      filterStatus: [],
      filterDefaultStatus: [],
      filterType: []
    }
  },
  methods: {
    ...mapActions([
      'GET_CHARGE_APPLY_DATA',
      'POST_CHARGE_AGREE_FORM_DATA',
      'POST_CHARGE_REJECT_FORM_DATA'
    ]),
    handleRowClass({row, rowIndex}) {

      const status = row.status_id
      if(UtilApply.toApplyPrompt(status)) {
        return 'warning-row'
      } else if(UtilApply.applingPrompt(status)) {
        return 'appling-row'
      } else if(UtilApply.rejectPrompt(status)) {
        return 'error-row'
      }
    },
    rowSelect(selection, row) {

      if(row.children) {
  
        row.children.forEach(item => {
          this.$refs.flowTable.toggleRowSelection(item);
        });
      }

      this.handleSelect(selection)
    },
    allSelect(selection) {

      this.handleSelect(selection)

    },
    pushClick() {

      this.showPush = true
    },
    approvalClick() {

      this.showApproval = true
    },
    handleSelect(selection) {

      this.form.applyIds = []
      selection.forEach(item => this.form.applyIds.push(item.id))    
    },
    handleCurrentChange(page) {

      this.query.page = page
      this.getApplyData()
    },
    handleSizeChange(size) {

      this.query.pagesize = size
      this.getApplyData()
    },
    canSelected(row, index) {

      return row.can_charge
    },
    handleSubmit() {
      this.getApplyData()
    },
    getApplyData() {

      this.GET_CHARGE_APPLY_DATA(this.query).then(() => {

        this.tableData = this.applies.data
        this.total     = this.applies.total
      })
    },
    filterChange(filters) {

      if('status' in filters) this.query.status = filters.status
      if('type' in filters)  this.query.type = filters.type

      this.getApplyData()
    },
    initFilterStatus() {

      this.filterStatus = UtilApply.getChargeStatus()

      this.query.status = UtilApply.getChargeDefaultStatus()      
    },
    initFilterType() {

      this.filterType = UtilType.getFilterType()   
    }
  },
  created: function () {

    this.initFilterStatus()
    this.initFilterType()
    this.Bee.searchForm = {
      dates: null,
      users: [],
      depts: [],
      desc: ''
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
  .el-table {
    width: 100%;
    margin-top: 20px;
  }
  .el-table .warning-row {
    background: oldlace;
  }
  .el-table .appling-row {
    background: #f0f9eb;
  }
  .el-table .error-row {
    background: #fef0f0;
  }
  .el-table th>.cell {
    text-align: center;
  }
  .bee-page {
    margin-top: 20px;
  }
  .el-timeline {
    max-height: 600px;
    overflow: auto;
  }
</style>