<template>
  <div class="top contents">
    <h3>ログイン情報</h3>
  </div>

  <div class="contents">
    <el-row :gutter="10" style="width:100rem;">
      <el-col :span="6" style="padding-right:10px;color:#666;font-size:14px;">
        <el-row class="condition-row">
          <el-col :span="8" class="left"> UID</el-col>
          <el-col :span="16" class="right"> {{ uid }}</el-col>
        </el-row>

        <el-row class="condition-row">
          <el-col :span="8" class="left">EMAIL</el-col>
          <el-col :span="16" class="right">
            <el-input v-model="email" type="text" placeholder="メールアドレス"></el-input>
          </el-col>
        </el-row>

        <el-row class="condition-row">
          <el-col :span="8" class="left"> 氏名</el-col>
          <el-col :span="16" class="right">
            <el-input v-model="admin_name" type="text" placeholder="名前"></el-input>
          </el-col>
        </el-row>

        <el-row class="condition-row">
          <el-col :span="8" class="left"> 所属</el-col>
          <el-col :span="16" class="right">
            <el-input v-model="role" type="text" placeholder="所属"></el-input>
          </el-col>
        </el-row>

        <el-row class="condition-row">
          <el-col :span="8" class="left"> 緊急連絡先</el-col>
          <el-col :span="16" class="right">
            <el-input v-model="tel" type="text" placeholder="緊急連絡先"></el-input>
          </el-col>
        </el-row>

        <el-row class="condition-row">
          <el-col :span="8" class="left"> パスワード</el-col>
          <el-col :span="16" class="right">
            <el-input v-model="password" type="text" placeholder="パスワード"></el-input>
          </el-col>
        </el-row>

        <el-row class="">
          <el-button @click="onEditUser" type="primary" class="edit-button" v-if="updatable">
            更新
          </el-button>
        </el-row>

      </el-col>


    </el-row>

  </div>
</template>

<script>
import AppVue from "../../mixins/AppVue.js";
import Common, {Define} from "../../js/Common.js";

export default {
  name: 'top',
  mixins: [AppVue],

  methods: {
    logined() {
      this.uid = this.$store.state.userInfo.uid
      this.loadUser();
      this.updatable = this.$store.state.divisions.user.UserUpdate;
    },
    loadUser() {
      this.post(Define.API_USER_ITEM, {uid: this.uid, mode: 'me'}).then((response) => {
        const user = response.user;
        this.admin_name = user.admin_name;
        this.email = user.email;
        this.role = user.role;
        this.tel = user.tel;
        this.login_at = user.login_at;
        this.password = ''
      });
    },
    onEditUser() {

      if (this.email === '') {
        this.message('メールアドレスが入力されていません')
        return
      }
      if (!Common.mailCheck(this.email)) {
        this.message('メールアドレスの書式が間違っています')
        return
      }

      const data = {}
      data.uid = this.uid
      data.admin_name = this.admin_name
      data.email = this.email
      data.role = this.role
      data.tel = this.tel
      data.password = this.password
      data.mode = 'update';
      this.post(Define.API_USER_ITEM, data).then(this.userEditRes)
    },
    userEditRes: function (response) {
      this.message('ユーザー情報を修正しました:', 'success')
      this.loadUser()
    },
  },

  data() {
    return {
      uid: 0,
      admin_name: '',
      email: '',
      password: '',
      role: '',
      tel: '',
      login_at: '',
      updatable: this.$store.state.divisions.UserUpdate,
    }
  },
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

.el-table >>> .el-table__cell {
  padding: 0 0;
}

.info-p {
  margin-bottom: 30px;
}
</style>
