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
          <el-table-column label="事由" prop="desc" width="225" show-overflow-tooltip></el-table-column>
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
          <el-table-column 
            label="办公地点"
            prop="address"
            width="160"
            column-key="address"
            :filters="filterAddress"            
            show-overflow-tooltip
          >
          </el-table-column>
          <el-table-column label="操作" width="50" fixed="right">
            <template slot-scope="props">
               <el-button type="text" @click="handleEdit(props.row)" size="small" v-if="showEdit(props.row)">调整</el-button>
            </template>
          </el-table-column>     
        </el-table>
        <div class="row bee-page">
          <div class="col-md-12 text-right">
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
      <edit :item="editItem" :show="editShow" :tab="editTab"></edit>
      <attend-approval :item="form.applyIds" :show="showApproval" @pick="handleSubmit"></attend-approval> 
    </div>
  </div>
</template>
<script>
import WorkFlow from '../WorkFlow'
import AttendApproval from '../../../components/bee/flow/AttendApproval'
import Fremark from '../../../components/bee/flow/Fremark'
import UtilType from '../../../utils/bee/type'
import UtilApply from '../../../utils/bee/apply'
import Edit from '../Edit'
import { OUTER_FORM } from '../../../store/bee/constant/apply'

import { mapState, mapActions } from 'vuex'

export default {
  name: 'AttendFlow',
  props: {
    search: Object,
  },
  provide() {
    return {
      Flow: this,
      leaveList: this
    };
  },
  inject: ['Bee'],
  components: {
    Fremark,
    Edit,
    WorkFlow,
    AttendApproval
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
      applies: state => state.flow.attendItems.data,
      flow: state => state.flow.data,
      filterAddress: state => state.user.filterAddress.data,
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
    },
    showEdit() {
      return function(row) {

        return row.can_edit  
      }
    }
  },
  data() {
    return {
      tableData: [],
      editShow: false,
      showApproval: false,
      editItem: OUTER_FORM,
      editTab: 'APPLY_LEAVE',
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
      filterStatus: [],
      filterDefaultStatus: [],
      filterType: []
    }
  },
  methods: {
    ...mapActions([
      'GET_ATTEND_APPLY_DATA',
      'FILTER_ADDRESS_DATA'
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
    approvalClick() {

      this.showApproval = true
    },    
    handleSelect(selection) {

      this.form.applyIds = []
      selection.forEach(item => this.form.applyIds.push(item.id))    
    },
    handleEdit(row) {
    
      this.editItem = row
      this.editShow = true

      this.initActiveTab(row.type);
    },
    initActiveTab(type) {

      if(UtilType.isOuter(type)) {

        this.editTab = 'APPLY_OUTER'
      } else if(UtilType.isWork(type)) {

        this.editTab = 'APPLY_WORK'
      } else {
      
        this.editTab = 'APPLY_LEAVE'
      }

    },
    handleSizeChange(size) {

      this.query.pagesize = size
      this.getApplyData()
    },
    canSelected(row, index) {

      return row.can_attend
    },
    handleSubmit() {
      this.getApplyData()
    },
    handleCurrentChange(page) {

      this.query.page = page;
      this.getApplyData()
    },
    getApplyData() {

      this.GET_ATTEND_APPLY_DATA(this.query).then(() => {

        this.tableData = this.applies.data
        this.total     = this.applies.total
      })
    },
    filterChange(filters) {

      if('status' in filters) this.query.status = filters.status
      if('type' in filters)  this.query.type = filters.type
      if('address' in filters)  this.query.address = filters.address

      this.getApplyData()
    },
    initFilterStatus() {

      this.filterStatus = UtilApply.getAttendStatus()

      this.query.status = UtilApply.getAttendDefaultStatus()   
    },
    initFilterType() {

      this.filterType = UtilType.getFilterType()   
    }
  },
  created: function () {

    this.initFilterStatus()
    this.initFilterType()
    this.FILTER_ADDRESS_DATA()
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
  .flow-form .el-select .el-input {
    width: 75px;
  }
  .el-timeline {
    max-height: 600px;
    overflow: auto;
  }
</style>