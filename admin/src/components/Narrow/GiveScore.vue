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
          付与スコア
        </el-col>
        <el-col :span="18">
          <el-input v-model="score" type="number" style="width:10rem;"></el-input>
        </el-col>
      </el-row>

      <el-row class="form-row" style="margin:8px 0;">
        <el-col :span="6">
          付与ID
        </el-col>
        <el-col :span="18">
          <el-input v-model="rid" type="number" style="width:10rem;"></el-input>
        </el-col>
      </el-row>


    </div>

    <hr>

    <div style="text-align:center;margin-top:1rem;">
      <el-button type="primary" @click="giveScore" :disabled="score===0">スコアを付与</el-button>
    </div>

  </div>

</template>

<script>
import * as Define from '../../js/Define'
import AppVue from "../../mixins/AppVue.js";
import Common from "../../js/Common.js";

export default {
  name: 'give_score',
  mixins: [AppVue],
  methods: {
    // -------------------------------------------------
    // スコアを付与
    giveScore() {
      this.confirm('確認:' + this.rid, '本当に ' + this.count + '名に ' + Common.numberFormat(this.point) + 'スコア を付与しますか？', null, this.postScore);
    },
    postScore() {
      const data = {
        members: this.memberList,
        score: this.score,
        rid: this.rid,
      }
      this.post(Define.API_GIVE_SCORE, data).then((response) => {
        console.log('API_GIVE_SCORE');
        this.message('一覧の会員に対し、指定のスコアを付与しました', 'success')
        this.closeDialog()
      })
    },
  },
  props: ['memberList', 'closeDialog', 'checkNames'],
  data() {
    const rid = Common.dateFormat((new Date()), 'YYYYMMDD')
    return {
      count: this.memberList.length,
      score: 0,
      rid: rid,
    }
  },

}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

</style>
