<template>
  <div class="row gutter-xs">
    <div class="col-md-12">
    	<el-card shadow="never">
    	  <div slot="header" class="clearfix">
          <span class="icon icon-search"></span>
    	    <span><b>考勤</b></span>
    	  </div>
<!--        <div class="row gutter-xs flow-form">
          <div class="col-md-12">
            <el-popover
              placement="bottom"
              width="300"
              v-model="visibleCancel"
            >
              <p>已删除、锁定的假单不能销假。</p>
              <p><fremark :form="form"></fremark></p>
              <div style="text-align: right; margin: 0">
                <el-button type="primary" size="mini" @click="handleCancel">确定</el-button>
              </div>
              <el-button slot="reference" type="primary" plain :disabled="cancelDisabled">销假</el-button>
            </el-popover>
          </div> 
        </div> 
-->
    	  <el-table
          border
          ref="applyTable"
          :data="tableData"
          :row-class-name="handleRowClass"
          row-key="id"
          @filter-change="filterChange"
        > 
        <!--
          @select="rowSelect"
          @select-all="allSelect"
          <el-table-column
            type="selection"
            width="40"
            :selectable="canSelected"
          >
          </el-table-column>
        -->
          <el-table-column label="日期" prop="date">
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
          <el-table-column label="姓名" prop="name" width="90">
            <template slot-scope="props">
              <el-popover trigger="hover" placement="right-start" :title="'日期：' + dateHtml(props.row.date)">
                <el-timeline>
                  <el-timeline-item 
                    v-for="item in props.row.flows"
                    :timestamp="item.created_at"
                    placement="top" 
                    :key="item.flow_id"
                  >
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
          <el-table-column label="操作" width="220">
            <template slot-scope="props">
               <el-button size="mini" @click="handleEdit(props.row)" v-if="showEdit(props.row)">调整</el-button>
               <el-button size="mini" @click="handleCancel(props.row)" v-if="showCancel(props.row)">销假</el-button>
               <el-button size="mini" @click="handleDelete(props.row)" v-if="showDelete(props.row)">删除</el-button>
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
      <edit 
        :item="editItem"
        :show="editShow"
        :tab="editTab"
        @pick="handleEndProcess"
      >
      </edit>
      <cancel 
        :value="cancelItem"
        :show="cancelShow"
        @pick="handleEndProcess"
      >
      </cancel>          
    </div>
  </div>
</template>

<script>
import Edit from './Edit'
import Cancel from './Cancel'
import WorkFlow from './WorkFlow'
import UtilType from '../../utils/bee/type'
import UtilApply from '../../utils/bee/apply'
//import Fremark from '../../components/bee/flow/Fremark'
import { OUTER_FORM } from '../../store/bee/constant/apply'

import { mapState, mapActions } from 'vuex'

export default {
  name: 'List',
  props: {
    search: Object,
  },
  components: {
    Edit,
    Cancel,
    WorkFlow
    //Fremark
  },
  provide() {
    return {
      Flow: this,
      leaveList: this
    };
  },
  inject: ['Bee'],
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
      applies: state => state.apply.items.data,
      data: state => state.apply.data
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
    },
    showDelete() {
      return function(row) {

        return row.can_delete  
      }      
    },
    showCancel() {
      return function(row) {

        return row.can_cancel 
      }      
    }
  },
  data() {
    return {     
      tableData: [],
      editShow: false,
      editItem: OUTER_FORM,
      cancelShow: false,
      cancelItem: {},
      editTab: 'APPLY_LEAVE',
      total: 0,
      query: {
        page: 1,
        pagesize: 15,
        status: [],
        type: []
      },
      filterStatus: [],
      filterDefaultStatus: [],
      filterType: [],
      visibleCancel: false,
      cancelDisabled: false
    }
  },
  methods: {
    ...mapActions([
      'GET_APPLY_DATA',
      'DELETE_APPLY_DATA'
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
          this.$refs.applyTable.toggleRowSelection(item);
        });
      }

      this.handleSelect(selection)
    },
    allSelect(selection) {

      this.handleSelect(selection)

    },
    handleSelect(selection) {

      this.form.applyIds = []
      this.cancelDisabled = false

      selection.forEach((item) => {

        this.form.applyIds.push(item.id)
        
        this.setCancelDisabled(item.can_cancel)
      })    
    },
    handleEdit(row) {
    
      this.editItem = row
      this.editShow = true

      this.initActiveTab(row.type);
    },
    handleCancel(row) {
    
      this.cancelItem = row
      this.cancelShow = true
    },
    handleDelete(row) {

      this.$confirm('此操作将永久删除该申请, 是否继续?', '提示', {

        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'

      }).then(() => {

        this.DELETE_APPLY_DATA({id: row.id}).then(() => {

          if(this.data.status < 400) {

            this.getApplyData()
            this.$message({ type: 'success', message: '删除成功!' })

          } else {

            this.$message.error('删除失败!')
          }
        })
        
      }).catch(() => {

        this.$message({ type: 'info', message: '已取消删除' })          
      });
    },
    handleCurrentChange(page) {

      this.query.page = page;
      this.getApplyData()
    },
    handleSizeChange(size) {

      this.query.pagesize = size
      this.getApplyData()
    },
    canSelected(row, index) {

      return row.can_cancel
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
    getApplyData() {

      this.GET_APPLY_DATA(this.query).then(() => {

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

      this.filterStatus = UtilApply.getStatus()    
    },
    initFilterType() {

      this.filterType = UtilType.getFilterType()   
    },
    handleEndProcess() {

      this.getApplyData()
    },
    setCancelDisabled(cancel) {

      if(!cancel && !this.cancelDisabled) this.cancelDisabled = true
    }         
  },
  created: function () {

    this.initFilterStatus()
    this.initFilterType()
    if(this.$route.query.id) this.query.id = this.$route.query.id

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
