<template>
  <el-form :model="form" :rules="rules" ref="workForm" label-width="80px">
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
    <Attrs 
      :value="form.meta.work" 
      :items="attrItems"
      :name="attrName"
      v-if="showAttr"
    >
    </Attrs>
    <el-form-item>
      <el-button type="primary" @click="submitForm('workForm')">立即修改</el-button>
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
import Attrs from '../../../components/bee/form/Attrs'
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
    Attrs
  },
  data() {
    return {
      labels: OUTER_LABEL,
      form: this.item,
      rules: COMMON_RULE,
      attrItems: [],
      attrName: 'work',
      typeItems: [],
      timeSectionItems: [],
      showTime: false,
      showAttr: false
    }
  },
  watch: {
    item: function(newValue) {

      this.form = newValue

      if(newValue.type) this.setShowAttr()
    }
  },
  computed: {
    ...mapState({
      types: state => state.types.data,
      apply: state => state.apply.data,
      permission: state => state.permission.items
    }),
    showFood: function() {

      return Utils.handleFood(this.form.type)
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

        if(type.classify == $G.TYPE_WORK && type.is_manage == 0) {

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
    initAttrs() {

      let attr = []
      for(let item of [...$G.ATTR_WORK_REFER].values()) {
        attr.push({id: item[0], name: item[1]})
      }

      this.attrItems = attr
    },
    setShowAttr() {

      this.showAttr = this.permission.has_affairs || this.permission.has_attend ? true : false
    },
    submitForm(formName) {
      this.$refs[formName].validate((valid) => {
        if (valid) {

          if('' == this.form.desc) {
            this.$message.error('请填写事由')
            return false
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

    this.initAttrs()
  } 
}
</script>
<style>
</style>
