<template>
  <div style="border-top:1px solid #ccc">

    <el-row v-for="(item,keyIndex) in status" @click="onClickStatus(item)" :class="(target===item)?'condition-target':'condition-row'" :id="'status_'+keyIndex">

      <narrow-conditions :index="keyIndex" :item="item" :options="options[keyIndex]??{}"></narrow-conditions>

      <el-col :span="2" style="text-align: center;">
        <el-button v-if="(targetCondition && keyIndex === targetCondition.key)" size="small" type="default" @click="onClickRegister(keyIndex)">
          更新
        </el-button>
        <el-button v-if="!(targetCondition && keyIndex === targetCondition.key)" size="small" type="primary" @click="onClickRegister(keyIndex)">
          追加
        </el-button>
      </el-col>

    </el-row>

  </div>
</template>

<script>
import Common from "../../js/Common.js";
import AppLoader from "../../mixins/AppLoader.js";
import NarrowMixin from './NarrowMixin.js'
import NarrowConditions from "./NarrowConditions.vue";

export default {
  name: 'nrwcon1',
  mixins: [AppLoader, NarrowMixin],
  components: {
    NarrowConditions,
  },

  created() {
    this.reset()
  },

  methods: {

    reset() {
      this.status = {
        created_at: {name: '登録日時', mode: 1, term: []},   // 登録日
        staff_flag: {name: '会員ステータス', mode: 1, value: 0}, // スタッフ
        mail_status: {name: 'メール配信状況', mode: 1, value: 0},   // 配信停止
        mail_accept: {name: '配信許諾', mode: 1, value: 1},   // 配信許諾
        accessed_at: {name: '最終アクセス', mode: 1, term: []},    // 最終ログイン
        locked_at: {name: '退会日時', mode: 1, term: []},      // 退会日
        ad_code: {name: '広告コード', mode: 1, value: ''},  // 広告コード
        mid: {name: '会員番号', mode: 1, min: 1, max: null},    // 認証ID
        email: {name: 'メールアドレス', mode: 1, value: ''}, // メールアドレス
        nickname: {name: 'ニックネーム', mode: 1, value: ''}, // ニックネーム
        uid_list: {name: 'UID', mode: 1, value: ''}, // UID指定
        enquete: {name: 'アンケートID-Q-R', mode: 1, value: '1'}, // アンケートID
      }
    },

  },

  props: ['addCondition', 'resetTarget', 'targetCondition'],
  data() {
    return {
      status: {},
      options: {
        staff_flag: {
          selector: {
            list: [{value: 0, label: '本登録'}, {value: 1, label: 'スタッフ'}, {value: 2, label: '退会者'}]
          }
        },
        mail_status: {
          selector: {
            list: Common.mailDisables
          }
        },
        mail_accept: {
          selector: {
            list: [{value: 0, label: '拒否'}, {value: 1, label: '可'}]
          },
        },
      },

    }
  },


}
</script>

<style>

</style>