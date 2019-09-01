<template>
  <div class="budget-fee clearfix">
    <div class="col-xs-3">
      <div class="form-group">
        <select class="custom-select" v-model="form.type">
          <option value="" disabled="disabled" selected="selected">请选择用途</option>        
          <option v-for="type in types" :key="type.name" :value="type.name">{{ type.name }}</option>
        </select>
      </div>
    </div>
    <div class="col-xs-3" v-show="showCustomName">
      <div class="form-group">
        <input class="form-control" v-model="form.tname" placeholder="请输入用途" autocomplete="off">
      </div>
    </div>
    <div class="col-xs-2">
      <div class="form-group">
        <input class="form-control" v-model="form.money" placeholder="请输入金额" autocomplete="off">
      </div>
    </div>
    <div class="col-xs-3">
      <div class="form-group">
        <input class="form-control" v-model="form.desc" placeholder="请输入备注" autocomplete="off">
      </div>
    </div>
    <div class="col-xs-1">
      <div class="form-group">
        <i class="el-icon-minus" @click="del"></i>
      </div>
    </div>                                    
  </div>
</template>
<script>
export default {
  name: 'Fee',
  props: {
    index: Number,
    item: Object
  },
  inject: ['budget'],
  data() {
    return {
      form: {
        type: this.item.type,
        money: this.item.money,
        desc: this.item.desc,
        tname: this.item.tname,
      },
      types:[]    
    }
  },
  watch: {
    form: {
      handler: function (val, oldVal) { 

        if(val.type.length == 0 || val.money.length == 0) return false

        this.budget.form.fees[this.index] = val
        
        this.handleMoney()   
      },
      deep: true
    },
    item: {
      handler: function (val, oldVal) { 

        this.form = val

        this.handleMoney()
      },
      deep: true
    }
  },
  computed: {
    showCustomName() {

      return this.form.type == '自定义' ? true : false
    }
  },
  methods: {
    del() {
      
      this.$emit('del', this.index)
    },
    handleMoney() {

      let total = 0
      this.budget.form.fees.forEach((item, index) => {
        total += parseInt(item.money)
      })
      this.budget.form.total = total
    }
  },
  created: function() {

    $G.TYPE_FEE.forEach((item, index) => {

      this.types.push({name: item})
    })
  }
}
</script>
<style>
.budget-fee {

  margin-bottom: 15px; 
}
.budget-fee .col-xs-3, .budget-fee .col-xs-2, .budget-fee .col-xs-1 {
  padding-left: 0;
}
.clearfix:before,
.clearfix:after {
  display: table;
  content: "";
}
.clearfix:after {
  clear: both
}
</style>