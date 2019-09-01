<template>
  <el-form-item :label="label" prop="type">
    <el-select 
      v-model="type"
      @change="handleChange"
      placeholder="请选择类别"
      no-data-text="加载中"
    >
      <el-option v-for="item in items" :key="item.id" :value="item.id" :label="item.name"></el-option>
    </el-select>
  </el-form-item> 
</template>
<script>
import Utils from '../../../utils/bee/type'

export default {
  name: 'Type',
  inject: ['leaveForm'],
  props: {
    value: [String, Number],
    types: Array,
    label: String
  },
  data() {
    return {
      type: this.value,
      items: this.types
    }
  },
  watch: {
    value: function(newValue) {
      this.type = newValue
    },
    types: function(newValue) {
      this.items = newValue
    }
  },
  methods: {
    handleChange() {
    
      this.leaveForm.form.type = this.type
      this.$emit('pick', this.type)
      this.handleSection()
    },
    handleSection() {
      
      let section_list = [
        $G.TIME_SECTION_ALL, 
        $G.TIME_SECTION_AM,
        $G.TIME_SECTION_PM,
        $G.TIME_SECTION_OTHER
      ]
      if(Utils.handleHalfDay(this.type)) {
        section_list.splice(3, 1)
      } else if(Utils.handleAllDay(this.type)) {
        section_list.splice(1, 3)
      }

      this.initTimeSection(section_list)
    },
    initTimeSection(items) {
      let section_list = []
      for(let item of items.values()) {
        section_list.push({id: item, name: $G.TIME_SECTION_REFER.get(item)})
      }
      this.leaveForm.timeSectionItems = section_list
    }
  },
  updated: function () {
    if(this.type) this.handleSection()
  }
}
</script>
<style>
</style>