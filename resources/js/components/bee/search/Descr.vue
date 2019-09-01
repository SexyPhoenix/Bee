<template>
  <div class="type-search" v-if="showDesc">
    <el-input type="textarea" v-model="desc" @change="handleChange" :rows="3" placeholder="请输入事由"></el-input>
  </div>
</template>
<script>

import { mapState, mapActions } from 'vuex'

export default {
  name: 'Descr',
  inject: ['Search'],
  props: {
    value: String
  },
  data() {
    return {
      desc: this.value
    }
  },
  watch: {
    value: function(newValue) {
      this.desc = newValue
    }
  },
  computed: {
    ...mapState({
      permission: state => state.permission.items
    }),
    showDesc() {
      return this.permission.has_affairs 
        || this.permission.has_attend
    }
  }, 
  methods: {
    handleChange() {

      this.Search.form.desc = this.desc
    }
  }
}
</script>
<style>

</style>