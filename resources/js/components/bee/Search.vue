<template>
  <div class="theme">
    <div class="theme-panel theme-panel-expanded bee-search" aria-expanded="true">
      <div class="theme-panel-controls">
        <button class="theme-panel-toggler bee-search-button" title="搜索" type="button">
          <span class="icon icon-search icon-spin" aria-hidden="true"></span>
        </button>
      </div>
      <div class="theme-panel-body">
        <div class="custom-scrollbar">
          <div class="custom-scrollbar-inner">
            <form class="form form-horizontal" ref="searchForm">
              <div class="form-group">
                <div class="col-sm-12">
                  <date-picker :value="form.dates"/>
                </div>
              </div>
              <div class="form-group">
                <div class="col-sm-12">
                  <user :value="form.users"/>
                </div>
              </div>
              <div class="form-group">
                <div class="col-sm-12">
                  <Descr :value="form.desc"/>
                </div>
              </div>
              <div class="form-group">
                <div class="col-sm-12">
                  <dept :value="form.depts"/>
                </div>
              </div>
              <div class="form-group">
                <div class="col-sm-12 center">
                  <el-button @click="resetForm()">重置</el-button>
                </div>
              </div>                         
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import DatePicker from './search/DatePicker'
import User from './search/User'
import Dept from './search/Dept'
import Descr from './search/Descr'
//import Type from './search/Type'

export default {
  name: 'Search',
  props: {
    item: Object
  },
  provide() {
    return {
      Search: this
    };
  },
  inject: ['Bee'],
  components: {
    DatePicker,
    User,
    Dept,
    Descr
    //Type
  },
  watch: {
    item: function(newValue) {

      this.form = newValue
    }
  },
  data() {
    return {
      form: this.item
    }
  },
  methods: {
  
    submitForm(formName) {
      
      this.updateSearch()
    },
    resetForm() {

      this.Bee.searchForm = {
        dates: null,
        users: [],
        depts: [],
        desc: ''
      }
    },
    updateSearch() {

      //this.$emit('pick', this.form)
      //this.$emit('pick', JSON.parse(JSON.stringify(this.form)))
    }
  }
}
</script>
<style>
.bee-search {
  width: 320px;
}
.bee-search-button {
  border-color: #409eff;
  background: #409eff;
  top: 50%;
  color: #fff;
}
.bee-search .col-sm-12.center {
  text-center: center;
}
</style>