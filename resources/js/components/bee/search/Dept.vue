<template>
  <div class="row" v-if="depts.length">
    <p style="margin-left: 15px;">搜索具体员工考勤请勿勾选部门</p>
    <el-tree
      :data="depts"
      node-key="dept_id" 
      ref="tree"
      :props="defaultProps"
      show-checkbox
      check-on-click-node
      @check-change="handleChange"
    >
    </el-tree>  
  </div>
</template>
<script>
import { mapState, mapActions } from 'vuex'

export default {
  name: 'Dept',
  inject: ['Search'],
  props: {
    value: Array
  },
  watch: {
    value: function(newValue) {

      if(0 == newValue.length) this.resetChecked()
    }
  },
  data() {
    return {
      defaultProps: {
        children: 'children',
        label: 'dept_name'
      }      
    }
  },
  computed: {
    ...mapState({
      depts: state => state.user.filterDept,
    }),  
  },
  methods: {
    ...mapActions([
      'FILTER_DEPT_DATA'
    ]),
    handleChange() {

      this.Search.form.depts = this.$refs.tree.getCheckedKeys()
    },
    resetChecked() {

      if(0 < Object.keys(this.$refs).length) {
      
        this.$refs.tree.setCheckedKeys([])
      }
    }
  },
  created: function () {

    this.FILTER_DEPT_DATA()
  }
}
</script>
<style>
</style>