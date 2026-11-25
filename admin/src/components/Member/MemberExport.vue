<template>
  <h3>会員エクスポート</h3>
  <div class="contents">

    <!-- リクエスト条件 ---------------------------------------------------------------------------------------------->

    <el-row>
      <el-col :span="4">
        <a :href="$store.state.API_BASE_URL + 'export/horse_housing?token=' + this.getToken()" download>
          <el-button @click="onClickExport">全指名格納馬状況</el-button>
        </a>
      </el-col>
    </el-row>


  </div>

  <div class="h-border" style="margin-top:5px;width:100%;"></div>


</template>

<script>

import MemberListTable from './MemberListTable.vue'
import * as Define from '../../js/Define'
import Common from '../../js/Common'
import AppVue from "../../mixins/AppVue.js";
import AppLoader from "../../mixins/AppLoader.js";

const date = new Date()

export default {
  name: 'memberExport',
  mixins: [AppVue, AppLoader],
  components: {},

  data() {
    return {
      waiting: 0,
    }
  },
  methods: {
    logined() {
      this.setAuth('member')
    },


  },

}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
</style>
