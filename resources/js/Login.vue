<template>
  <div class="login">
    <div class="login-body">
      <div class="login-form">
        <form data-toggle="validator">
          <div class="form-group">
            <label for="email">Email</label>
            <input id="email" class="form-control" v-model="form.email" spellcheck="false" autocomplete="off" data-msg-required="Please enter your email address." required>
          </div>
          <div class="form-group">
            <label for="password">Password</label>
            <input id="password" class="form-control" type="password" v-model="form.password" minlength="6" data-msg-minlength="Password must be 6 characters or more." data-msg-required="Please enter your password." required>
          </div>
          <div class="form-group">
            <label class="custom-control custom-control-primary custom-checkbox">
              <input class="custom-control-input" type="checkbox" checked="checked">
              <span class="custom-control-indicator"></span>
              <span class="custom-control-label">Keep me signed in</span>
            </label>
            <span aria-hidden="true"> · </span>
            <a href="#">Forgot password?</a>
          </div>
          <button class="btn btn-primary btn-block" type="submit" @click="submitForm()">Sign in</button>
        </form>
      </div>
    </div>
  </div>
</template>
<script>

import { mapState, mapActions } from 'vuex'

export default {
  name: 'Login',
  data() {
    return {
      form: {
        email: '',
        password: ''
      }    
    }
  },
  computed: {
    ...mapState({
      login: state => state.login.data
    })
  },
  methods: {
    ...mapActions([
      'POST_LOGIN_FORM_DATA'
    ]),
    submitForm() {

      this.POST_LOGIN_FORM_DATA(this.form).then(() => {
        if(this.login.status < 400) {

          $COOKIE.setCookie('token', this.login.data.token)
          $COOKIE.setCookie('user', this.login.data.user)
          this.$message.success('登录成功')

          this.$router.push({ path: '/apply/leave' })

        } else {

          this.$message.error(this.login.data.message)
        }
      })    
    }    
  } 
}
</script>
<style>
.login {
  -webkit-animation-duration: 1s;
       -o-animation-duration: 1s;
          animation-duration: 1s;
  -webkit-animation-fill-mode: both;
       -o-animation-fill-mode: both;
          animation-fill-mode: both;
  -webkit-animation-name: fadeInUp;
       -o-animation-name: fadeInUp;
          animation-name: fadeInUp;
  margin-left: auto;
  margin-right: auto;
  max-width: 360px;
  margin-top: 200px;
}
.login-body {
  padding: 30px;
}

@media (min-width: 768px) {
  .login-body {
    background-color: #fff;
    border: 1px solid #eee;
    border-radius: 0;
    -webkit-box-shadow: 0px 0px 1px 1px rgba(0, 0, 0, 0.1);
            box-shadow: 0px 0px 1px 1px rgba(0, 0, 0, 0.1);
    margin-bottom: 15px;
    margin-top: 15px;
  }
}
</style>