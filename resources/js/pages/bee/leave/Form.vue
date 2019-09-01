<template>
  <el-form :model="form" :rules="rules" ref="leaveForm" label-width="80px">
    <name
      :value="form.user"
      :label="labels.name_label"
    >
    </name>
    <type 
      :value="form.type"
      :label="labels.type_label"
      :types="typeItems"
      @pick="getChangeType"
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
      :value="attrValue" 
      :items="attrItems"
      :name="attrName"
      v-if="showAttrs"
    >
    </Attrs>
    <el-form-item>
      <el-button type="primary" @click="submitForm('leaveForm')">立即修改</el-button>
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
import Attrs from '../../../components/bee/form/Attrs'
import UtilType from '../../../utils/bee/type'

import { 
  TYPE_YEAR_REST,
  TYPE_CHAN_JIA
} from '../../../store/bee/constant/types'

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
    Attrs
  },
  data() {
    return {
      labels: OUTER_LABEL,
      form: this.item,
      rules: COMMON_RULE,
      attrItems: [],
      attrName: '',
      attrValue: '',
      typeItems: [],
      timeSectionItems: [],
      showTime: false,
      showAttrs: false
    }
  },
  watch: {
    item: function(newValue) {

      this.form = newValue

      if(newValue.type) this.getChangeType(newValue.type)
    }
  },
  computed: {
    ...mapState({
      types: state => state.types.data,
      apply: state => state.apply.data,
      permission: state => state.permission.items
    }),
  }, 
  methods: {
    ...mapActions([
      'GET_TYPE_LIST_DATA',
      'POST_UPDATE_APPLY_FORM_DATA'
    ]),
    getChangeType(type) {

      this.showAttrs = false
      this.attrItems = []
      if(this.permission.has_affairs || this.permission.has_attend) {

        if(TYPE_YEAR_REST == type) {

          this.initAttrItems([...$G.ATTR_YEAR_REFER])
          this.attrName = 'year'
          this.attrValue = this.form.meta.year

        } else if (TYPE_CHAN_JIA == type) {

          this.initAttrItems([...$G.ATTR_BIRTH_REFER])
          this.attrName = 'birth'
          this.attrValue = this.form.meta.birth
        }
      }
    },
    filterTypeItems() {
 
      const _types = this.types
      for(let [idx, type] of Object.entries(_types)) {

        if(type.classify == $G.TYPE_LEAVE && type.is_manage == 0) {

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
    initAttrItems(items){
      
      let attr = []
      for(let item of items.values()) {
        attr.push({id: item[0], name: item[1]})
      }

      this.attrItems = attr
      this.showAttrs = true       
    },
    submitForm(formName) {
      this.$refs[formName].validate((valid) => {
        if (valid) {

          if('' == this.form.desc) {
            this.$message.error('请填写事由')
            return false
          }
          
          if(UtilType.needAttach(this.form.type) && '' == this.form.attach) {

            this.$message.error('请参照考勤提醒上传相应附件')
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
    },
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
