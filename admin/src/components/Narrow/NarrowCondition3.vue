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
import AppLoader from "../../mixins/AppLoader.js";
import NarrowMixin from "./NarrowMixin.js";
import AppVue from "../../mixins/AppVue.js";
import NarrowConditions from "./NarrowConditions.vue";

export default {
  name: 'nrwcon3',
  mixins: [AppVue, AppLoader, NarrowMixin],
  components: {
    NarrowConditions,
  },
  created() {
    this.reset()
    this.onLogin()
  },

  methods: {
    logined() {
    },

    reset() {
      this.status = {

        payment_subscription: {name: 'サブスク購入回数', mode: 1, sub: 'all', min: 0, max: null, term: [null, null], checked: false, datetype: 'datetime',},
        subscription: {name: '現サブスク加入会員', mode: 1, sub: 'all', value: 'ALL'},
        subscription_continue: {name: '現サブスク継続設定', mode: 1, sub: 'all', value: 'ON'},
        subscription_expire: {name: 'サブスク期限日', mode: 1, value: 'all', sub: 'ALL', term: [], datetype: 'date',},

      }

    },
  },

  props: ['addCondition', 'resetTarget', 'targetCondition'],
  data() {
    return {
      status: {},
      options: {
        payment_subscription: {
          checked: {name: '継続を含まない'},
          sub_selector: {
            caption:'種類',
            list: [{value: 'all', label: 'すべてのプラン'}, {value: 'yoso', label: 'WinSalon'}, {value: 'windex_std', label: 'WINDEXベーシック'}, {value: 'windex_prm', label: 'WINDEXプレミアム'},]
          },
        },
        subscription: {
          selector: {
            caption:'加入状況',
            list: [{value: 'ALL', label: '全ての加入会員'}, {value: 'CREDIT', label: 'クレジット'}, {value: 'BANK', label: '銀行振込'}, {value: 'NONE', label: '加入していない'}]
          },
          sub_selector: {
            caption:'種類',
            list: [{value: 'all', label: 'すべてのプラン'}, {value: 'yoso', label: 'WinSalon'}, {value: 'windex_std', label: 'WINDEXベーシック'}, {value: 'windex_prm', label: 'WINDEXプレミアム'},]
          },
        },
        subscription_continue: {
          selector: {
            caption:'継続設定',
            list: [{value: 'ON', label: 'ON'}, {value: 'OFF', label: 'OFF'}],
          },
          sub_selector: {
            caption:'種類',
            list: [{value: 'all', label: 'すべてのプラン'}, {value: 'yoso', label: 'WinSalon'}, {value: 'windex_std', label: 'WINDEXベーシック'}, {value: 'windex_prm', label: 'WINDEXプレミアム'},]
          },
        },
        subscription_expire: {
          selector: {
            caption:'種類',
            list: [{value: 'all', label: 'すべてのプラン'}, {value: 'yoso', label: 'WinSalon'}, {value: 'windex_std', label: 'WINDEXベーシック'}, {value: 'windex_prm', label: 'WINDEXプレミアム'},]
          },
          sub_selector: {
            caption: '決済手段',
            list: [{value: 'ALL', label: '全て'}, {value: 'CREDIT', label: 'クレジット'}, {value: 'BANK', label: '銀行振込'},]
          },
        },
      },
    }

  },

}
</script>
<style>
.el-autocomplete-suggestion .el-scrollbar {
  width: 800px;
}
</style>
