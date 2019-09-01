<template>
  <div class="row gutter-xs">
    <div class="col-md-12 bee-import">
      <div v-show="showtable">
        <el-table
          border
          ref="applyTable"
          row-key="index"
          :data="tableData"
        >
          <el-table-column label="日期" prop="date" width="480">
            <template slot-scope="props">
              {{ dateHtml(props.row.date) }} 
              <el-tag type="success" style="margin-left: 3px">{{ timeSectionHtml(props.row) }}</el-tag>
              <el-tag 
                type="success"
                style="margin-left: 3px"
                v-for="(tag, index) in props.row.meta_tag"
                :key="index"
              >
                {{ tag }}
              </el-tag>
            </template>
          </el-table-column>
          <el-table-column label="姓名" prop="name" width="90"></el-table-column>
          <el-table-column
            label="类别"
            prop="type" 
            width="80"            
          >
            <template slot-scope="props">
              {{ typeHtml(props.row.type_id) }}
            </template>
          </el-table-column>
          <el-table-column label="事由" prop="desc" show-overflow-tooltip></el-table-column>
        </el-table>
        <div class="import-button">
          <el-button type="primary" @click="submitImport()">立即提交</el-button>
        </div>
      </div>    
      <el-upload
        drag
        class="bee-upload"
        :action="action"
        :limit="1"
        :multiple="false"
        :headers="headers"
        :http-request="handleUpload"
      >
        <i class="el-icon-upload"></i>
        <div class="el-upload__text">将文件拖到此处，或<em>点击上传</em></div>
        <div class="el-upload__tip" slot="tip">类别属性：误餐、工作日、休息日、法定假日、工作日换调休、周末换调休、无津贴、2019、2020</div>
      </el-upload>   
    </div>
  </div>
</template>

<script>

import axios from 'axios'
import UtilType from '../../utils/bee/type'

import { mapState, mapActions } from 'vuex'

export default {
  name: 'Import',
  data() {
    return {
      action: $C.API_IMPORT,
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      tableData: [],    
    }
  },
  computed: {
    ...mapState({
      import: state => state.apply.data
    }),  
    dateHtml() {
      return function (date) {

        let length = date.length
        let first  = date[0].replace(/-/g, '/')
        return length > 1 ? first + ' - ' + date[length -1 ].replace(/-/g, '/') : first
      }
    },
    timeSectionHtml() {
      return function (row) {
        return $G.TIME_SECTION_OTHER == row.time_section 
                ? (row.start_time + '-' + row.end_time)
                : $G.TIME_SECTION_REFER.get(row.time_section)
      }
    },
    typeHtml() {
      return function (type) {
        return UtilType.getTypeRefer(type)
      }      
    },    
    showtable() {

      return 0 == this.tableData.length ? false : true
    }  
  },
  methods: {
    ...mapActions([
      'POST_APPLY_IMPORT_DATA'
    ]),
    handleUpload(file) {

      let param = new FormData()
      param.append('attach',file.file) 
      let config = {
        headers: {'Content-Type': 'multipart/form-data'}
      }
      axios.post($C.API_IMPORT, param, config)
        .then(response => {

          if(response.data.result) {

            this.tableData = response.data.data
          } else {

            this.$message.error(response.data.error)
          }
      })
    },
    handleSuccess(response, file, fileList) {

      if(response.result) {

        this.tableData = response.data
      } else {

        this.$message.error(response.error)
      }
      
    },
    submitImport() {

      this.POST_APPLY_IMPORT_DATA({import: this.tableData}).then(() => {

        if(this.import.status < 400) {

          this.$message.success(this.import.data.message)
          this.$router.go(0)

        } else {

          this.$message.error(this.import.data.message.join('、'))
        }

      })
    }       
  },
}
</script>
<style>
.icon { 
  font-size: 16px; 
  margin-right: 5px;
}
.clearfix:before,
.clearfix:after {
  display: table;
  content: "";
}
.clearfix:after {
  clear: both;
}
.bee-upload {
  text-align: center;
  padding: 15px 0;
  background: #fff;
}
.bee-upload input[type=file] {
  display: none;
}
.bee-import .import-button {
  text-align: center;
  background: #fff;
  padding-top: 20px;
}
</style>