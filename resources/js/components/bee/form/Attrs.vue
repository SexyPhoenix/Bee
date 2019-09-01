<template>
  <el-form-item>
    <el-radio-group v-model="attr" @change="handleChange">
      <el-radio v-for="item in attrItems" :key="item.id" :label="item.id"> {{ item.name }}</el-radio>
    </el-radio-group>
  </el-form-item>
</template>
<script>
export default {
  name: 'Attrs',
  inject: ['leaveForm'],
  props: {
    value: [Number, String],
    items: Array,
    name: String
  },
  data() {
    return {
      attr: Number.parseInt(this.value),
      attrItems: this.items,
      type: this.name
    }
  },
  watch: {
    value : function(newValue) {
      this.attr = Number.parseInt(newValue)
    },
    name : function(newValue) {
      this.type = newValue
    },
    items : function(newValue) {
      this.attrItems = newValue
    }
  }, 
  methods: {
    handleChange() {

      if(this.type == 'work') {

        this.leaveForm.form.meta.work = this.attr
      } else if(this.type == 'year') {

        this.leaveForm.form.meta.year = this.attr
      } else if(this.type == 'birth') {

        this.leaveForm.form.meta.birth = this.attr
      }
      
    },
  }
}
</script>
<style>
</style>