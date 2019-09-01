<template>
  <div class="row gutter-xs">
  	<div class="col-md-2">
      <el-row>
        <el-col>
          <el-menu
            :default-active="defaultActive"
            :default-openeds="['APPLY', 'APPROVAL', 'LIST', 'MANAGE']"
            @select="switchRight"
          >
            <el-submenu index="APPLY">
              <template slot="title">
                <span class="icon icon-calendar-times-o"></span>
                <span><b>申请</b></span>
              </template>
              <router-link to="/apply/leave">
                <el-menu-item index="APPLY_LEAVE">请假</el-menu-item>
              </router-link>
              <router-link to="/apply/outer">
                <el-menu-item index="APPLY_OUTER">外勤</el-menu-item>
              </router-link>
              <router-link to="/apply/work">
                <el-menu-item index="APPLY_WORK">加班</el-menu-item>
              </router-link>
            </el-submenu>
            <el-submenu index="APPROVAL" v-show="showApproval">
              <template slot="title">
                <span class="icon icon-check"></span>
                <span><b>审批</b></span>
              </template>
              <router-link to="/approval/dept" v-show="this.permission.has_charge">
                <el-menu-item index="APPROVAL_DEPT">主管</el-menu-item>
              </router-link>
              <router-link to="/approval/attend" v-show="this.permission.has_attend">
                <el-menu-item index="APPROVAL_ATTEND">考勤员</el-menu-item>
              </router-link>
              <router-link to="/approval/affairs" v-show="this.permission.has_affairs">
                <el-menu-item index="APPROVAL_AFFAIRS">考勤管理员</el-menu-item>
              </router-link>                    
            </el-submenu>
            <el-submenu index="LIST">
              <template slot="title">
                <span class="icon icon-search"></span>
                <span><b>查询</b></span>
              </template>
              <router-link to="/list">
                <el-menu-item index="LIST_ATTEND">考勤</el-menu-item>
              </router-link>
            </el-submenu>
            <el-submenu index="MANAGE" v-show="showManage">
              <template slot="title">
                <span class="icon icon-gear"></span>
                <span><b>管理</b></span>
              </template>
              <router-link to="/import">
                <el-menu-item index="MANAGE_IMPORT">考勤导入</el-menu-item>
              </router-link>
              <router-link to="/lock" v-show="this.permission.has_affairs">
                <el-menu-item index="MANAGE_LOCK">考勤锁定</el-menu-item>
              </router-link>              
            </el-submenu>
          </el-menu>
        </el-col>
      </el-row>  	  
  	</div>
    <div :class="routerClass">
      <router-view @pick="handleChange" :search="searchForm"></router-view>
    </div>
    <div class="col-md-3" v-if="showNotice">
      <Notice :title="header" :item="item"></Notice>      
    </div>
    <search v-show="showSearch" :item="searchForm"></search> 
  </div>
</template>
<script>

import Notice from '../../components/bee/Notice'
import Search from '../../components/bee/Search'

import { mapState, mapActions } from 'vuex'

export default {
  name: 'Home',
  data () {
    return {
      item: {},
      defaultActive: $G.SWITCH_RIGHT_DEFAULT,
      searchForm: {},
      showSearch: false
    }
  },
  provide() {
    return {
      Bee: this,
    };
  },
  components: {
    Notice,
    Search
  },
  computed: {
    ...mapState({
      permission: state => state.permission.items
    }),
    header() {
    
      return 0 < Object.keys(this.item).length 
                ? $G.NOTICE_TITLE + '-' + this.item['name']
                : $G.NOTICE_TITLE
    },
    showNotice() {

      return this.defaultActive.includes('APPLY')
    },
    routerClass() {
      return this.showNotice ? 'col-md-7' : 'col-md-10'
    },
    showApproval() {
      return this.permission.has_affairs 
        || this.permission.has_attend
        || this.permission.has_charge
    },
    showManage() {
      return this.permission.has_affairs || this.permission.has_attend
    }
  },
  methods: {
    ...mapActions([
      'GET_PERMISSION_LIST'
    ]),
    handleChange(item) {

      this.item = item
    },
    switchRight(key, path) {

      this.item = {}
      this.defaultActive = key
    },
    setShowSearch() {

      this.showSearch = this.defaultActive.includes('LIST') || this.defaultActive.includes('APPROVAL')
    }
  },
  updated() {

    this.$nextTick(function () {

      this.defaultActive = this.$route.name

      this.setShowSearch()
    })
  },
  created() {

    this.GET_PERMISSION_LIST()
  }

}
</script>
<style>
  .icon { 
      font-size: 16px; 
      margin-right: 5px;
  }
  .el-submenu .el-menu-item {
    min-width: auto;
  }
  .el-menu-item.is-active {
      border-bottom: 2px solid #409eff;
      color: #409eff
  }
  .el-submenu .el-submenu__title {
    color: #409EFF;
  }
</style>