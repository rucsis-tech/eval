<template>
  <div style="margin-top:30px;text-align:center;">
    <div class="input-box">
      <span>APIサーバ</span>
      <el-input v-model="api_url" class="input-text" type="text" @keyup.enter.native="onLogin" placeholder="API url"></el-input>
      <span>メールアドレス</span>
      <el-input v-model="email" class="input-text" type="mail" @keyup.enter.native="onLogin" placeholder="mail address"></el-input>
      <span>パスワード</span>
      <el-input v-model="pass" class="input-text" type="password" @keyup.enter.native="onLogin" placeholder="Password"></el-input>
      <el-button @click="login">ログイン</el-button>
    </div>
  </div>
</template>

<script>
import Common, {Define} from '../../js/Common'
import AppVue from "../../mixins/AppVue.js";

export default {
  name: 'login',
  components: {},
  mixins: [AppVue],
  data() {
    return {
      api_url: '',
      email: '',
      pass: ''
    }
  },
  created: function () {
    const url = this.$store.state.API_BASE_URL
    if (url) {
      this.api_url = url;
    }
  },
  methods: {
    logined() {

    },

    login: function (event) {
      if (this.email === '' || this.pass === '') {
        this.message('IDもしくはパスワードが入力されていません。')
        return
      }

      this.$store.state.API_BASE_URL = this.api_url;
      Common.setItem('API_BASE_URL', this.api_url);

      /*
      const data = new FormData()
      data.append('mail', this.mail)
      data.append('pass', this.pass)
       */
      const data = {email: this.email, password: this.pass}
      this.setToken('');
      this.post(Define.API_LOGIN, data).then(this.loginRes).then(() => {
        this.$router.replace('top')
      })
    },

  },

}
</script>
<style scope>
.input-box {
  text-align: left;
  width: 300px;
  margin: auto;
}

.input-text {
  margin-top: 5px;
  margin-bottom: 20px;
}

.input-box button {
  width: 100%;
}
</style>
