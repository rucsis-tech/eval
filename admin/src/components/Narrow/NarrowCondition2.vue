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
  name: 'nrwcon2',
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
      this.loadItems().then(() => {
        this.options.payment_item.selector.list= [];
        for (let i = 0; i < this.itemList.length; i++) {
          this.options.payment_item.selector.list.push({value: this.itemList[i].id, 'label': this.itemList[i].name});
        }
      })
    },

    reset() {
      this.status = {
        purchased_at: {name: '最終購入日時', mode: 1, term: []},
        payment_item: {name: '商品別購入経験', mode: 1, value: 1, sub: 'ALL', term: [null, null], checked: false, datetype: 'datetime',},
        payment_sum: {name: '購入総額', mode: 1, min: 0, max: null}, //購入総額

        payment_point: {name: 'ポイント購入回数', mode: 1, min: 0, max: null, term: [null, null], datetype: 'datetime',},
        point_used_at: {name: '最終ポイント使用日時', mode: 1, term: []},
        point_used_count: {name: 'ポイント使用回数', mode: 1, min: 0, max: null},
        point: {name: '総ポイント所有数', mode: 1, min: 0, max: null},
        premium: {name: '有償ポイント所有数', mode: 1, min: 0, max: null},
        gift: {name: 'ギフト所有数', mode: 1, min: 0, max: null},


      }

    },
  },

  props: ['addCondition', 'resetTarget', 'targetCondition'],
  data() {
    return {
      status: {},
      options: {
        payment_item: {
          checked: {name: 'サブスク初回加入のみ'},
          selector: {
            caption:'購入商品',
            list: []
          },
          sub_selector: {
            caption:'決済手段',
            list: [{value: 'ALL', label: '全て'}, {value: 'CREDIT', label: 'クレジット'}, {value: 'BANK', label: '銀行振込'},]
          }
        }
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
