<template>
  <div class="type-search">
    <el-select
      v-model="type_ids"
      multiple
      filterable
      placeholder="请输入假种"
      :loading="loading"
      @change="handleChange"
    >
      <el-option
        v-for="item in types"
        :key="item.id"
        :value="item.id"
        :label="item.name"
      >
      </el-option>
    </el-select>
  </div>
</template>
<script>
export default {
  name: 'Type',
  inject: ['Search'],
  props: {
    value: Array
  },
  data() {
    return {
      type_ids: this.value,
      loading: false,
      types: [{
        id: 1,
        name: '病假'
      }, {
        id: 2,
        name: '事假'
      }]
    }
  },
  methods: {
    handleChange() {

      this.Search.form.types = this.type_ids
    }
  }
}
</script>
<style>
 .type-search .el-select{ width: 100% }
</style>