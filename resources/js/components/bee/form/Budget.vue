<template>
  <el-form-item :label="label" required>
    <el-button type="primary" icon="el-icon-plus" @click="handleBudget"></el-button>
    <div class="list-group list-group-divided budget-list" v-show="items.length">
      <div class="list-group-item" href="#" v-for="(item, index) in items" :key="index">
        <div class="media">
          <div class="media-middle media-body">
            <h4 class="media-heading">
              <span class="desc">{{ item.total }}￥</span>
              <small>
                <el-popover
                  placement="top-start"
                  title="费用"
                  width="350"
                  trigger="hover"
                >
                  <p v-for="(fee, i) in item.fees" :key="i">用途：{{ fee.type == '自定义' ? fee.tname : fee.type}} 金额：{{ fee.money }}￥ 备注：{{ fee.desc }} </p>
                  <i slot="reference" class="el-icon-view"></i>
                </el-popover>     
              </small>
            </h4>
          </div>
          <div class="media-middle media-right">
            <el-button plain icon="el-icon-edit" v-on:click="editBudget(item, index)"/>
            <el-button plain icon="el-icon-minus" v-on:click="delBudget(index)"/>
          </div>            
        </div>
      </div>
    </div>
    <el-dialog
      title="预算"
      :visible.sync="dialogFormVisible"
      :close-on-click-modal="false"
      width="40%"
      :before-close="handleBudgetClose"
      append-to-body
    >
      <form>
        <div class="row gutter-xs">
          <div class="col-sm-12">
            <div class="form-group">
              <label for="desc">预算费用事项</label>
              <input id="desc" class="form-control" v-model="form.desc" autocomplete="off" placeholder="如：参加2019年美国XXX展会/会见XXX客户">
            </div>
          </div>
          <div class="col-sm-12">
            <div class="form-group">
              <div>
                <label>费用</label>
              </div>
              <fee 
                v-for="(fee, index) in form.fees" 
                :key="index" 
                :index="index" 
                :item="fee"
                @del="delFee"
              >
              </fee>
            </div>
          </div>
          <div class="col-sm-12">
            <div class="fee_add">
              <el-button icon="el-icon-plus" @click="addFee"></el-button>
            </div>
          </div>
          <div class="col-sm-12">
            <div class="form-group">
              <label>合计 <span class="badge badge-danger">{{ isNaN(form.total) ? 0 : form.total }}</span> 元</label>
            </div>
          </div>                    
        </div>
      </form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="handleBudgetClose">取 消</el-button>
        <el-button type="primary" @click="submitForm">确 定</el-button>
      </div>
    </el-dialog>    
  </el-form-item>
</template>
<script>
import Fee from './Fee'
export default {
  name: 'Budget',
  provide() {
    return {
      budget: this
    };
  },  
  inject: ['leaveForm'],
  components: {
    Fee
  },
  props: {
    label: String,
    items: Array,
    init: {
      type: Object,
      default: function() {
        return {    
          total: 0, 
          desc: '', 
          fees: [{type: '', money: '', desc: '', tname: ''}]
        }
      }
    }
  },  
  data() {
    return {
      dialogFormVisible: false,
      budgetIndex: '',
      form: {}
    }
  }, 
  methods: {
    handleBudget() {

      this.dialogFormVisible = true
      this.initBudgetForm()
    },
    delBudget(index) {

      this.$confirm('此操作将删除该整条记录, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {

        this.leaveForm.form.meta.budget.splice(index, 1)
        this.$message({ type: 'success', message: '删除成功!'});
      }).catch(() => {

        this.$message({ type: 'info', message: '已取消删除' });          
      });
      
    },
    editBudget(item, index) {

      this.dialogFormVisible = true
      this.budgetIndex = index
      this.form = item
    },
    addFee() {

      this.form.fees.push({type: '', money: '', desc: '', tname: ''})
    },
    delFee(index) {

      this.form.fees.splice(index, 1)
    },
    submitForm() {
      
      //add or edit
      '' === this.budgetIndex
        ? this.leaveForm.form.meta.budget.push(this.form)
        : this.leaveForm.form.meta.budget.splice(this.budgetIndex,1,this.form)

      this.dialogFormVisible = false

      this.initBudgetForm()
    },
    initBudgetForm() {
       
      this.form = {total: 0, desc: '', fees: [{type: '', money: '', desc: '', tname: ''}]}
      this.budgetIndex = ''
    },
    handleBudgetClose() {

      this.dialogFormVisible = false
      this.initBudgetForm()
    }
  }
}
</script>
<style>
  .fee_add { margin-bottom: 15px }
  .budget-list .media-body{ width: 70% }
  .budget-list .media-body .desc{ font-size: 16px;}
</style>