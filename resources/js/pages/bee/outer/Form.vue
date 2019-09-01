<template>
  <el-form :model="form" :rules="rules" ref="outerForm" label-width="80px">
    <name
      :value="form.user"
      :label="labels.name_label"
    >
    </name>
    <type 
      :value="form.type"
      :label="labels.type_label"
      :types="typeItems"
    >
    </type>
    <date 
      :value="form.date" 
      :label="labels.date_label"
      :disabled="true"
    >
    </date>
    <time-section 
      :value="form.time_section" 
      :label="labels.time_section_label"
      :items="timeSectionItems"
    >
    </time-section>
    <time-picker 
      :start="form.start_time" 
      :end="form.end_time" 
      :label="labels.time_picker_label" 
      :show="showTime"
    >
    </time-picker>
    <descr 
      :value="form.desc" 
      :label="labels.desc_label"
    >
    </descr>
    <team 
      :value="form.meta.team" 
      :label="labels.meta_label.team"
      v-show="showTeam"
    >
    </team>
    <peer 
      :value="form.meta.peer" 
      :label="labels.meta_label.peer"
      v-show="showPeer"
      :items="form.peers"
    >
    </peer>
    <budget
      :label="labels.meta_label.budget"
      :items="budgetItems"  
      v-show="showBudget"
    >
    </budget>
    <destination 
      :value="form.meta.destination" 
      :label="labels.meta_label.destination"
      v-show="showDestination"
    >
    </destination>
    <food 
      :value="form.meta.food" 
      :label="labels.meta_label.food"
      v-show="showFood"
    >
    </food>
    <handover 
      :value="form.handover" 
      :label="labels.handover_label"
      :items="form.handovers"
    >
    </handover>
    <upload 
      :value="form.attach" 
      :label="labels.upload_label"
    >
    </upload>
    <el-form-item>
      <el-button type="primary" @click="submitForm('outerForm')">立即修改</el-button>
    </el-form-item>     
  </el-form>
</template>

<script>
import Name from '../../../components/bee/form/Name'
import Type from '../../../components/bee/form/Type'
import Date from '../../../components/bee/form/Date'
import TimeSection from '../../../components/bee/form/TimeSection'
import TimePicker from '../../../components/bee/form/TimePicker'
import Descr from '../../../components/bee/form/Descr'
import Handover from '../../../components/bee/form/Handover'
import Upload from '../../../components/bee/form/Upload'
import Food from '../../../components/bee/form/Food'
import Team from '../../../components/bee/form/Team'
import Peer from '../../../components/bee/form/Peer'
import Destination from '../../../components/bee/form/Destination'
import Budget from '../../../components/bee/form/Budget'
import Utils from '../../../utils/bee/type'
import { 
  OUTER_LABEL,
  OUTER_FORM,
  COMMON_RULE 
} from '../../../store/bee/constant/apply'

import { mapState, mapActions } from 'vuex'

export default {
  name: 'Form',
  props: {
    item: Object
  },
  provide() {
    return {
      leaveForm: this
    };
  },
  inject: ['leaveList'],
  components: {
    Name,
    Type,
    Date,
    TimeSection,
    TimePicker,
    Descr,
    Handover,
    Upload,
    Food,
    Team,
    Peer,
    Destination,
    Budget
  },
  data() {
    return {
      labels: OUTER_LABEL,
      form: this.item,
      rules: COMMON_RULE,
      typeItems: [],
      timeSectionItems: [],
      showTime: false
    }
  },
  watch: {
    item: function(newValue) {

      this.form = newValue
    }
  },
  computed: {
    ...mapState({
      types: state => state.types.data,
      apply: state => state.apply.data
    }),
    showFood: function() {

      return Utils.handleFood(this.form.type)
    },
    showTeam: function() {

      return Utils.handleTeam(this.form.type)
    },
    showPeer: function() {

      return Utils.handleTeam(this.form.type) && this.form.meta.team
    },
    showDestination: function() {

      return Utils.handleDestination(this.form.type)
    },
    showBudget: function() {
      return Utils.handleBudget(this.form.type)
    },
    budgetItems: function() {
      return this.form.meta.budget
    }
  }, 
  methods: {
    ...mapActions([
      'GET_TYPE_LIST_DATA',
      'POST_UPDATE_APPLY_FORM_DATA'
    ]),
    filterTypeItems() {
 
      const _types = this.types
      for(let [idx, type] of Object.entries(_types)) {

        if(type.classify == $G.TYPE_OUTER && type.is_manage == 0) {

          this.typeItems = [...this.typeItems, type]
        }
      }
    },
    initTimeSection() {

      let section_list = []
      for(let item of [...$G.TIME_SECTION_REFER].values()) {
        section_list.push({id: item[0], name: item[1]})
      }

      this.timeSectionItems = section_list  
    },
    submitForm(formName) {
      this.$refs[formName].validate((valid) => {
        if (valid) {

          if('' == this.form.desc) {
            this.$message.error('请填写事由')
            return false
          }
          
          if(Utils.isChuChai(this.form.type)) {

            if('' == this.form.meta.destination) {

              this.$message.error('请填写城市') 
              return false
            }

            if(0 === this.form.meta.budget.length) {

              this.$message.error('请填写预算') 
              return false
            }

            if(this.form.meta.team && 0 === this.form.meta.peer.length) {

              this.$message.error('请填写同行人') 
              return false            
            }          
          }
          
          this.POST_UPDATE_APPLY_FORM_DATA(this.form).then(() => {

            if(this.apply.status < 400) {

              this.$message.success('耶~修改成功');

              this.leaveList.editShow = false
              this.leaveList.editItem = OUTER_FORM

              this.$emit('pick')
              
            } else {

              this.$message.error(this.apply.data.message.join('、'));
            }

          })

        } else {
          return false
        }
      });
    }
  },
  created: function () {

    this.GET_TYPE_LIST_DATA().then(() => {
      this.filterTypeItems()
    })

    this.initTimeSection()
  } 
}
</script>
<style>
</style>
