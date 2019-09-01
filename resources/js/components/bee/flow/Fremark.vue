<template>
  <el-input v-model="remark" placeholder="请输入备注" @change="handleChange"></el-input>
</template>
<script>
export default {
  name: 'Fremark',
  props: {
    form: Object
  },
  inject: ['Flow'],
  data() {
    return {
      remark: this.form.remark,
    }
  },
  methods: {
    handleChange(value) {

      this.Flow.form.remark = value
    }
  }
}
</script>
<style>
</style>