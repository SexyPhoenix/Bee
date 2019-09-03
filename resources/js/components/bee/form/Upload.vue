<template>
  <el-form-item :label="label">
    <el-upload
      class="attach-uploader"
      :action="action"
      :limit="1"
      :multiple="false"
      :headers="headers"
      :on-remove="handleRemove"
      :on-preview="handlePreview"
      :before-remove="beforeRemove"
      :http-request="handleUpload"
      :file-list="preview"  
    >
      <el-button size="small" type="primary">点击上传</el-button>
      <!-- <div slot="tip" class="el-upload__tip">上传数量不超过1个</div> -->
    </el-upload>
  </el-form-item> 
</template>
<script>

import axios from 'axios'

export default {
  name: 'Upload',
  inject: ['leaveForm'],
  props: {
    label: String,
    value: String
  },
  data() {
    return {
      attach: this.value,
      action: $C.API_UPLOAD,
      preview: [],
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    }
  },
  watch: {
    value : function(newValue, oldValue) {
      this.attach = newValue
      if(newValue) {

        this.preview = [{name: newValue, url: $C.UPLOAD_URL + '/' + newValue}]
      }
    }
  },
  methods: {
    handleChange() {

      this.leaveForm.form.attach = this.attach
    },
    handleRemove(file, attach) {
      this.leaveForm.form.attach = ''
    },
    beforeRemove(file, attach) {
      return this.$confirm(`确定移除 ${ file.name }？`)
    },
    handleUpload(file) {

      let param = new FormData()
      param.append('attach',file.file) 
      let config = {
        headers: {'Content-Type': 'multipart/form-data'}
      }
      axios.post($C.API_UPLOAD, param, config)
        .then(response => {

          if(200 == response.status) {

            this.leaveForm.form.attach = response.data 
          } else {

            this.$message.error('上传失败~')
            return false           
          }
      })
    },
    handlePreview(file) {

      window.open(file.url)
    }
  }
}
</script>
<style>
.attach-uploader .el-upload__input{
  display: none;
}
</style>