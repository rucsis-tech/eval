<template>
  <div class="contents bottom-border">
    <h3>管理者一覧</h3>
  </div>

  <div class="contents">
    <div class="r-white">
      <user-table ref="userTbl" :gridData="userList" :loadFunction="loadUser" :changeFunction="selectedRow">
      </user-table>
    </div>

    <hr/>

    <el-checkbox v-model="newCheck" @change="onChangeNewMode" :disabled="!auth.UserAdd"><span style="font-size:18px;">新規登録</span></el-checkbox>
    <div class="form-area" ref="formArea">


      <el-row>
        <el-col :span="4" class="form-cell">
          <span style="font-size:0.8rem">UID</span>
          <el-input v-model="uid" type="text" placeholder="UID" disabled></el-input>
        </el-col>
        <el-col :span="8" class="form-cell">
          <span style="font-size:0.8rem">メールアドレス</span>
          <el-input v-model="email" type="text" placeholder="メールアドレス"></el-input>
        </el-col>
        <el-col :span="4" class="form-cell">
          <span style="font-size:0.8rem">名前</span>
          <el-input v-model="admin_name" type="text" placeholder="名前"></el-input>
        </el-col>
        <el-col :span="4" class="form-cell">
          <span style="font-size:0.8rem">所属</span>
          <el-input v-model="role" type="text" placeholder="所属"></el-input>
        </el-col>
        <el-col :span="4" class="form-cell">
          <span style="font-size:0.8rem">緊急連絡先</span>
          <el-input v-model="tel" type="text" placeholder="緊急連絡先"></el-input>
        </el-col>

        <el-col :span="8" class="form-cell">
          <span style="font-size:0.8rem">パスワード</span>
          <el-input v-model="password" type="text" placeholder="パスワード"></el-input>
        </el-col>
      </el-row>

      <hr>

      <p>ユーザー権限</p>

      <el-row v-for="(define,key) in this.$store.state.authDefine" style="padding:2px 0 8px 0;margin:0;border-bottom: dashed 2px #AAA;">

        <el-col :span="4">
          <el-checkbox :indeterminate="isIndeter[key]" v-model="CheckAll[key]" @change="onCheckAll2(key)">
            <span class="bold">{{ define.label }}</span>
          </el-checkbox>
        </el-col>

        <el-col :span="20">
          <el-checkbox-group v-model="Checked[key]" @change="onCheck2(key)">
            <template v-for="item in define.auth">
              <el-checkbox :label="item.label" style="margin:2px 5px;width:20%;">{{ item.label }}</el-checkbox>
            </template>
          </el-checkbox-group>
        </el-col>

      </el-row>


      <el-row style="margin-top:1rem;">
        <el-col :span="3">

        </el-col>
        <el-col :span="21" style="text-align:right;">
          <el-button @click="onEditUser" type="primary" class="edit-button" v-if="newCheck" :disabled="!auth.UserAdd">
            追加
          </el-button>
          <el-button @click="onEditUser" type="primary" class="edit-button" v-else-if="!newCheck" :disabled="!auth.UserUpdate || selectedUser === null ? true : false">
            修正
          </el-button>

          <!--el-button style="width:150px;" type="primary" size="small" :disabled="!auth.UserReset || selectedUser === null || newCheck" @click="onResetConfirm">
            パスワード再発行
          </el-button-->
          <el-button style="width:150px;" type="danger" :disabled="!auth.UserDelete || selectedUser === null || newCheck" @click="onDelConfirm">
            削除
          </el-button>
        </el-col>
      </el-row>

    </div>
  </div>
</template>

<script>
import Common, {Define} from '../../js/Common'
import UserTable from './User_Table.vue'
import AppVue from "../../mixins/AppVue.js";
import {API_USER_ITEM} from "../../js/Define.js";

export default {
  name: 'user',
  mixins: [AppVue],
  components: {
    'user-table': UserTable
  },
  data() {
    return {
      auth: this.$store.state.divisions.user,

      userList: [],
      newCheck: false,

      selectedUser: null,
      uid: '',
      admin_name: '',
      email: '',
      password: '',
      role: '',
      tel: '',

      CheckAll: {},
      Checked: {},
      isIndeter: {},


    }
  },
  methods: {
    logined() {
      this.setAuth('user')
      console.log(this.auth)
      this.loadUser()

    },
    //-----------------------------------------------------
    // 一覧取得
    loadUser() {
      this.userList = []
      this.post(Define.API_USER_LIST).then((response) => {
        this.userList = response.userList
        if (this.selectedUser !== null) {
          let selUser = this.selectedUser
          this.userList.forEach(function (item, i) {
            if (selUser.uid === item.uid) {
              selUser = item
            }
          })
        }
      });
    },

    //-----------------------------------------------------
    // 新規・編集

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
      if (!this.newCheck) {
        if (this.selectedUser === null) {
          this.message('ユーザーが選択されていません')
          return
        }
        data.uid = this.selectedUser.uid
      }

      data.admin_name = this.admin_name
      data.email = this.email
      data.role = this.role
      data.tel = this.tel
      data.password = this.password
      data.divisions = this.createCheckData()

      if (this.newCheck) {
        data.mode = 'add';
        this.post(Define.API_USER_ITEM, data).then(this.userCreateRes)
      } else {
        data.mode = 'update';
        this.post(Define.API_USER_ITEM, data).then(this.userEditRes)
      }
    },
    userCreateRes: function (response) {
      this.message('ユーザー登録を実施しました:', 'success')
      this.loadUser()
      this.newCheck = false
      this.onChangeNewMode()
      this.selectedUser = []
    },
    userEditRes: function (response) {

      this.message('ユーザー情報を修正しました:', 'success')
      this.loadUser()
    },

    //-----------------------------------------------------
    // ユーザー削除

    onDelConfirm() {
      if (this.selectedUser === null) {
        this.message('ユーザーが選択されていません')
        return
      }
      this.confirm('ユーザー削除', this.selectedUser.admin_name + 'を削除してよろしいですか？', 'info', this.onDelUser)
    },
    onDelUser() {
      this.post(Define.API_USER_ITEM, {uid: this.selectedUser.uid, mode: 'delete'}).then((response) => {
        this.message('ユーザーを削除しました:', 'success')
        this.selectedUser = []
        this.loadUser()
      });
    },

    //-----------------------------------------------------
    // パス再発行

    onResetConfirm() {
      if (this.selectedUser === null) {
        this.message('ユーザーが選択されていません')
        return
      }
      this.confirm('ユーザパスワード再設定', this.selectedUser.admin_name + 'のパスワードの再設定メールを送信してよろしいですか？', 'info', this.onResetUser)
    },
    onRepassUser() {
      const data = {
        mode: 'reset',
        uid: this.selectedUser.uid,
      }
      this.post(Define.API_USER_ITEM, data).then((response) => {
        this.message('パスワードの再設定メールを送信しました', 'success')
      })
    },

    //-----------------------------------------------------
    arrSort(arr) {
      arr.sort(function (a, b) {
        if (a.value < b.value) return -1
        if (a.value > b.value) return 1
        return 0
      })
    },
    selectedRow(item, index) {
      this.selectedUser = item
      this.selectedUser.index = index
      if (this.newCheck) {
        return
      }
      this.setUserDetail()
    },
    // 詳細セット
    setUserDetail() {
      if (!this.selectedUser) {
        return
      }
      this.uid = this.selectedUser.uid
      this.admin_name = this.selectedUser.admin_name
      this.email = this.selectedUser.email
      this.role = this.selectedUser.role
      this.tel = this.selectedUser.tel
      this.password = ''

      if (this.uid) {
        this.post(Define.API_USER_ITEM, {uid: this.uid, mode: 'detail'}).then((response) => {
          const divisions = response.divisions
          for (let key in this.$store.state.authDefine) {
            this.setCheckedList2(key, divisions[key])
            this.onCheck2(key)
          }
        })
      }

    },
    // 詳細クリア
    clearUserDetail() {
      this.uid = ''
      this.admin_name = ''
      this.email = ''
      this.role = ''
      this.tel = ''
      this.password = ''

      for (let key in this.$store.state.authDefine) {
        this.setCheckedList2(key, {})
        this.onCheck2(key)
      }
    },

    onChangeNewMode() {
      if (this.newCheck) {
        this.$refs.formArea.style.backgroundColor = '#ffffe0'
        this.clearUserDetail()
      } else {
        this.$refs.formArea.style.backgroundColor = '#f0f8ff'
        this.setUserDetail()
      }
    },

    //-----------------------------------------------------
    // 権限チェックボックス管理

    // 権限チェックデータ収集

    createCheckData() {
      const divisions = {}
      for (let key in this.$store.state.authDefine) {
        const division = {}
        const authList = this.$store.state.authDefine[key].auth
        console.log(this.Checked[key])
        for (let auth in authList) {
          if (this.Checked[key].indexOf(authList[auth].label) > -1) {
            division[auth] = 1
          }
        }
        divisions[key] = division;
      }
      return divisions
    },

    // チェックをセット
    setCheckedList2(key, auths) {
      let Checked = []
      const defines = this.$store.state.authDefine[key].auth
      for (let auth in auths) {
        if (auths[auth] && defines[auth]) {
          Checked.push(defines[auth].label)
        }
      }
      this.Checked[key] = Checked
    },
    // 権限項目
    onCheckAll2(key) {
      let options = []
      const authList = this.$store.state.authDefine[key].auth
      if (this.CheckAll[key]) {
        for (let auth in authList) {
          options.push(authList[auth].label)
        }
      }
      this.Checked[key] = options
      this.isIndeter[key] = false
    },
    // 項目整合性反映
    onCheck2(key) {
      const authCount = Object.keys(this.$store.state.authDefine[key].auth).length
      const checkedCount = (this.Checked[key] ?? []).length
      this.CheckAll[key] = checkedCount === authCount
      this.isIndeter[key] = checkedCount > 0 && checkedCount < authCount
    },

  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
.form-area {
  padding: 10px;
  background-color: #f0f8ff;
  padding-bottom: 20px;
}

.form-cell {
  padding: 5px;
}

.bold {
  font-size: 18px;
  font-weight: bold;
  color: #494949;
}

.edit-button {
  width: 150px;
  margin: 10px auto;
}
</style>
