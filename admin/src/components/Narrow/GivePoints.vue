<template>
  <div style="text-align:left">

    <div style="padding:1rem;font-size:1rem;" ref="formArea">

      <el-row class="form-row" style="margin:8px 0;">
        <el-col :span="6">
          対象
        </el-col>
        <el-col :span="18">
          {{ count }} 名
        </el-col>
      </el-row>

      <el-row class="form-row" style="margin:8px 0;">
        <el-col :span="6">
          付与ポイント
        </el-col>
        <el-col :span="18">
          <el-input v-model="point" type="number" style="width:10rem;"></el-input>
          ポイント
        </el-col>
      </el-row>

      <el-row class="form-row" style="margin:8px 0;">
        <el-col :span="6">
          有効日数
        </el-col>
        <el-col :span="18">
          <el-input v-model="days" type="number" style="width:10rem;"></el-input>
          日
        </el-col>
      </el-row>

      <el-row class="form-row" style="margin:8px 0;">
        <el-col :span="6">
          付与理由
        </el-col>
        <el-col :span="18">
          <el-input v-model="reason" type="text"></el-input>
        </el-col>
      </el-row>

    </div>

    <hr>

    <div style="text-align:center;margin-top:1rem;">
      <el-button type="primary" @click="givePoints" :disabled="point===0 || reason===''">ポイントを付与</el-button>
    </div>

  </div>

</template>

<script>
import * as Define from '../../js/Define'
import AppVue from "../../mixins/AppVue.js";
import {API_GIVE_POINTS} from "../../js/Define";
import Common from "../../js/Common.js";

export default {
  name: 'give_points',
  mixins: [AppVue],
  methods: {
    // -------------------------------------------------
    // ポイントを付与
    givePoints() {
      this.confirm('確認:' + this.reason, '本当に ' + this.count + '名に ' + Common.numberFormat(this.point) + 'ポイント を付与しますか？', null, this.postPoints);
    },
    postPoints() {
      const data = {
        members: this.memberList,
        point: this.point,
        reason: this.reason,
        days: this.days,
      }
      this.post(Define.API_GIVE_POINTS, data).then((response) => {
        console.log('API_GIVE_POINTS');
        this.message('一覧の会員に対し、指定のポイントを付与しました', 'success')
        this.closeDialog()
      })
    },
  },
  props: ['memberList', 'closeDialog', 'checkNames'],
  data() {
    return {
      count: this.memberList.length,
      point: 0,
      days: 30,
      reason: '',
    }
  },

}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

</style>
