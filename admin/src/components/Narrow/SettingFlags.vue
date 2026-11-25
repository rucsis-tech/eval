<template>
  <div style="text-align:center">

    <div style="padding-left:1rem;">

      <div style="float:left;width:110px;height:2rem;text-align:left;overflow: hidden; text-overflow: ellipsis;" v-for="(name, i) in checkNames">
        <el-tooltip effect="dark" :content="checkNames[i].name" placement="top-end" :disabled="checkNames[i].name == ''">
          <label class="checkBoxBlock" style="height:2rem;">
            <el-checkbox v-model="checks[i]" ref="cbx2" style="margin-right:4px;"></el-checkbox>
            {{ i + 1 }}: <small style="color:#AAA;">{{ checkNames[i].name }}</small>
          </label>
        </el-tooltip>
      </div>

    </div>

    <div style="clear:both;"></div>
    <hr style="margin-top:1rem;">

    <div style="margin-top:1rem;">
      <el-button type="primary" @click="addCheckFlags">チェックフラグを付与</el-button>
      <el-button type="danger" @click="delCheckFlags">チェックフラグを削除</el-button>
    </div>

  </div>

</template>

<script>
import * as Define from '../../js/Define'
import AppVue from "../../mixins/AppVue.js";

export default {
  name: 'cbxset',
  mixins: [AppVue],
  props: ['memberList', 'closeDialog', 'checkNames'],
  data() {
    return {
      checks: this.getChecks(),
    }
  },
  methods: {
    // -------------------------------------------------
    // チェックフラグデータ取得
    createCheckData() {

      let checkExists = false
      let data = {}
      let check = ''
      let index = 1;

      for (let i = 0; i < this.$store.state.consts.flags.count; i++) {
        if (this.checks[i]) {
          checkExists = true
          check = '1' + check
        } else {
          check = '0' + check
        }
        if (check.length === 32) {
          data['check' + index] = check
          check = ''
          index++
        }
      }

      if (checkExists) {

        data.uidList = [];
        this.memberList.forEach(function (member, i) {
          data.uidList.push(member.uid);
        })

        return data

      } else {
        this.message('チェックが入っていません')
        return false
      }
    },
    // -------------------------------------------------
    // チェックフラグを付与
    addCheckFlags() {
      let data = this.createCheckData()
      if (data !== false) {
        data.mode = 'ADD';
        this.post(Define.API_MODIFY_CHECKS, data).then((response) => {
          this.message('一覧の会員に対し、指定のチェックフラグを付与しました', 'success')
          this.closeDialog()
        })
      }
    },
    // -------------------------------------------------
    // チェックフラグを削除
    delCheckFlags() {
      let data = this.createCheckData()
      if (data !== false) {
        data.mode = 'DEL';
        this.post(Define.API_MODIFY_CHECKS, data).then((response) => {
          this.message('一覧の会員に対し、指定のチェックフラグを削除しました', 'success')
          this.closeDialog()
        })
      }
    },
    reset() {
      this.checks = this.getChecks()
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

</style>
