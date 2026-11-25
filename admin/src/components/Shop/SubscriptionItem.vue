<template>

  <!--  --------------------------------------------------------------------------------------------------->
  <el-row class="contents bottom-border">
    <el-col :span="20">
      <h3>サブスクリプション商品管理</h3>
    </el-col>

    <el-col :span="4">
      <el-button @click="loadItem" style="margin-left:20px;">再読み込み</el-button>
    </el-col>
  </el-row>


  <el-row class="contents">
    <!-- 一覧 --------------------------------------------------------------------------------------------------->
    <el-col :span="16">
      <div class="r-white">
        <item-table ref="cubeTbl" :gridData="itemList" :changeFunction="selectItem" :loadFunction="loadItem" :auth="auth" :KINDS="KINDS"></item-table>
      </div>
    </el-col>

    <!-- 入力フォーム --------------------------------------------------------------------------------------------------->

    <el-col :span="8">

      <div style="margin-left:4px;" ref="formArea">

        <el-row class="contents">
          <el-col :span="8" style="padding:0 8px">
            <!-- 新規入力チェック -->
            <el-checkbox @change="onChangeNewMode" v-model="newCheck">新規入力</el-checkbox>
          </el-col>
        </el-row>

        <el-row class="form-row">
          <el-col :span="5" class="form-label">サブスク種類</el-col>
          <el-col :span="6" class="form-input">
            <el-select v-model="kind" placeholder="種類を選択" style="width:120%;">
              <template v-for="(value,key) in KINDS">
                <el-option :label="value" :value="key"></el-option>
              </template>
            </el-select>
          </el-col>
          <el-col :span="5" class="form-label">表示順</el-col>
          <el-col :span="6" class="form-input">
            <el-input-number v-model="priority" :min="0" style="width:120px"></el-input-number>
          </el-col>


        </el-row>

        <el-row class="form-row">
          <el-col :span="5" class="form-label"> 商品名</el-col>
          <el-col :span="19" class="form-input">
            <el-input v-model="name" placeholder="商品名"></el-input>
          </el-col>
        </el-row>

        <el-row class="form-row">
          <el-col :span="5" class="form-label"> 商品詳細</el-col>
          <el-col :span="19" class="form-input">
            <el-input v-model="detail" placeholder="商品詳細" type="textarea" :rows="2"></el-input>
          </el-col>
        </el-row>

        <el-row class="form-row">
          <el-col :span="5" class="form-label">通常価格</el-col>
          <el-col :span="19" class="form-input">
            <el-input-number v-model="amount" :min="0"></el-input-number>
          </el-col>
        </el-row>


        <el-row class="form-row">
          <el-col :span="5" class="form-label">初期価格</el-col>
          <el-col :span="6" class="form-input">
            <el-input-number v-model="initial_amount" :min="0"></el-input-number>
          </el-col>
          <el-col :span="5" class="form-label">初期サイクル</el-col>
          <el-col :span="8" class="form-input">
            <el-input-number v-model="initial_cycle" :min="0" style="width:120px"></el-input-number>
          </el-col>
        </el-row>


        <el-row class="form-row">
          <el-col :span="5" class="form-label"> 価格説明</el-col>
          <el-col :span="19" class="form-input">
            <el-input v-model="description" :min="0"></el-input>
          </el-col>
        </el-row>

        <el-row class="form-row">
          <el-col :span="5" class="form-label">期間日数</el-col>
          <el-col :span="19" class="form-input">
            <el-input-number v-model="quantity" :min="0"></el-input-number>
            日
          </el-col>
        </el-row>

        <el-row class="form-row">
          <el-col :span="5" class="form-label">継続ギフト</el-col>
          <el-col :span="6" class="form-input">
            <el-input-number v-model="giftPoint" :min="0"></el-input-number>
          </el-col>
          <el-col :span="5" class="form-label">有効日数</el-col>
          <el-col :span="8" class="form-input">
            <el-input-number v-model="giftExpire" :min="0" style="width:120px;"></el-input-number>
          </el-col>
        </el-row>


        <el-row class="form-row">
          <el-col :span="5" class="form-label">特別プラン</el-col>
          <el-col :span="6" class="form-input">
            <el-select v-model="edit_plan" placeholder="プランを選択" style="width:120%;">
              <template v-for="item in PLANS">
                <el-option :label="item.name" :value="item.value"></el-option>
              </template>
            </el-select>
          </el-col>
          <el-col :span="2" class="form-label">値</el-col>
          <el-col :span="8" class="form-input">
            <el-input v-model="edit_value"></el-input>
          </el-col>
          <el-col :span="2">
            <el-button class="edit-button" style="width:3rem;margin:0;" @click="addPlan" :disabled="name===''">追加</el-button>
          </el-col>
        </el-row>

        <el-row class="form-row">
          <el-col :span="5" class="form-label"></el-col>
          <el-col :span="19" class="form-input">
            <div v-for="(plan,key) in plans">
              <div style="display:flex;font-size:0.8rem;margin:0">
                <div style="width:40%;">
                  {{ PLANS[key].name }}
                </div>
                <div style="width:40%;">
                  {{ plan.value }}
                </div>
                <div style="width:20%;">
                  <el-button class="edit-button" style="width:3rem;margin:0;height:24px" @click="delPlan(key)">削除</el-button>
                </div>
              </div>
            </div>
          </el-col>
        </el-row>


        <el-row class="form-row">
          <el-col :span="5" class="form-label">販売開始</el-col>
          <el-col :span="19" class="form-input">
            <el-date-picker v-model="SalesBeginAt" type="datetime"></el-date-picker>
          </el-col>
        </el-row>
        <el-row class="form-row">
          <el-col :span="5" class="form-label"> 販売終了</el-col>
          <el-col :span="19" class="form-input">
            <el-date-picker v-model="SalesCloseAt" type="datetime"></el-date-picker>
          </el-col>
        </el-row>

        <el-row class="form-row">
          <el-col :span="5" class="form-label"> 決済手段</el-col>
          <el-col :span="18" class="form-input">
            <el-checkbox-group v-model="PayMethods" style="margin:-3px 0">
              <el-checkbox v-for="(value,key) in METHODS" :value="key" style="margin:0">{{ value }}　</el-checkbox>
            </el-checkbox-group>
          </el-col>
        </el-row>

        <el-row class="form-row">
          <el-col :span="5" class="form-label"> 販売状態</el-col>
          <el-col :span="19" class="form-input">
            <el-radio-group v-model="closed" style="margin:-3px 2px;">
              <el-radio :label="0">販売</el-radio>
              <el-radio :label="1">販売停止</el-radio>
            </el-radio-group>
          </el-col>
        </el-row>

        <hr>
        <div style="text-align: center;margin-top: 15px;">
          <el-button class="edit-button" type="warning" v-if="newCheck" :disabled="!auth.PaymentItemAdd" @click="addItem">
            追加
          </el-button>

          <el-button class="edit-button" type="primary" v-if="!newCheck" :disabled="!auth.PaymentItemMod || selectedItem === null" @click="modItem">
            修正
          </el-button>
        </div>

      </div>
    </el-col>
  </el-row>

</template>

<script>
import ItemTable from './SubscriptionItem_Table.vue'
import * as Define from '../../js/Define'
import Common from '../../js/Common'
import AppVue from "../../mixins/AppVue.js";
import {API_PAYMENT_ITEM} from "../../js/Define";

const date = new Date()

export default {
  name: 'point',
  mixins: [AppVue],
  components: {
    'item-table': ItemTable
  },

  methods: {
    logined() {
      this.setAuth('payment')
      this.loadItem()
      // this.loadPreset()
    },
    //--------------------------------------------------
    // 商品情報の取得
    loadItem() {
      this.clearItemSelect()
      this.itemList = []
      this.selectedItem = null

      this.post(Define.API_PAYMENT_ITEM, {api_type: 'List', type: 'subsc', data: {}}).then((response) => {
        this.itemList = response.itemList
        this.METHODS = response.METHODS
        this.PLANS = response.PLANS
        this.KINDS = response.KINDS

      })
    },


    //--------------------------------------------------
    // 商品情報の新規追加
    addItem() {
      const data = this.getFormItemData()
      if (!data) return

      this.post(Define.API_PAYMENT_ITEM, {api_type: 'ADD', data: data}).then((response) => {
        this.message('商品を追加しました。', 'success')
        this.loadItem()
      })
    },
    //--------------------------------------------------
    // 商品情報の変更
    modItem() {
      const data = this.getFormItemData()
      if (!data) return

      data.id = this.selectedItem.id
      this.post(Define.API_PAYMENT_ITEM, {api_type: 'MOD', data: data}).then((response) => {
        this.message('商品を修正しました。', 'success')
        this.loadItem()
      })
    },
    //--------------------------------------------------
    // プランの追加
    addPlan() {
      const plan = this.edit_plan
      console.log(plan)
      if (plan === 'need_promo_code' && !this.edit_value) {
        this.alert('クーポンコードを入力してください')
        return
      }
      if (plan === 'flags' && !this.edit_value) {
        this.alert('フラグ番号をカンマ区切りで入力してください')
        return
      }
      if (plan && plan !== 'none') {
        this.plans[plan] = {plan: plan, value: this.edit_value}
      }
    },
    // プランの削除
    delPlan(key) {
      delete this.plans[key]
    },
    //--------------------------------------------------
    // 商品入力の取得
    getFormItemData() {
      if (this.name === '') {
        this.message('商品名が入力されていません。')
        return null
      }
      if (this.SalesBeginAt === '') {
        this.message('販売開始が入力されていません。')
        return null
      }
      if (this.SalesCloseAt === '') {
        this.message('販売終了が入力されていません。')
        return null
      }
      var data = {}
      data.type = 'subsc'
      data.name = this.name
      data.kind = this.kind
      data.amount = this.amount
      data.detail = this.detail
      data.quantity = this.quantity
      data.description = this.description
      data.gift_point = this.giftPoint
      data.gift_expire = this.giftExpire
      data.sales_begin_at = Common.dateFormat(this.SalesBeginAt, 'YYYY-MM-DD hh:mm:ss')
      data.sales_close_at = Common.dateFormat(this.SalesCloseAt, 'YYYY-MM-DD hh:mm:ss')
      data.closed = this.closed
      data.initial_cycle = this.initial_cycle
      data.initial_amount = this.initial_amount
      data.plans = this.plans ?? {}
      data.PayMethods = this.PayMethods ?? []
      data.priority = this.priority

      return data
    },

    //--------------------------------------------------
    // 商品の選択

    selectItem(row, event, column) {
      if (this.newCheck) {
        this.clearItemSelect()
        return
      }
      this.selectedItem = row
      this.id = row.id
      this.kind = row.kind
      this.name = row.name
      this.amount = row.amount
      this.detail = row.detail
      this.description = row.description
      this.quantity = row.quantity
      this.giftPoint = row.gift_point
      this.giftExpire = row.gift_expire
      if (row.sales_begin_at === '0000-00-00 00:00:00') {
        this.SalesBeginAt = ''
      } else {
        this.SalesBeginAt = date.setTime(new Date(row.sales_begin_at))
      }
      if (row.sales_close_at === '0000-00-00 00:00:00') {
        this.SalesCloseAt = ''
      } else {
        this.SalesCloseAt = date.setTime(new Date(row.sales_close_at))
      }
      this.closed = row.closed

      this.initial_cycle = row.initial_cycle
      this.initial_amount = row.initial_amount
      this.plans = row.plans ?? {}
      this.PayMethods = row.PayMethods;
      this.edit_plan = 'none'
      this.edit_value = ''
      this.priority = row.priority

      this.onChangeNewMode()
    },

    // 入力フォームクリア
    clearItemSelect() {
      this.selectedItem = null
      this.id = 0
      this.name = ''
      this.kind = 'unlimited'
      this.detail = ''
      this.amount = 0
      this.description = ''
      this.quantity = 30
      this.giftPoint = 0
      this.giftExire = 0
      this.SalesBeginAt = ''
      this.SalesCloseAt = ''
      this.closed = 0
      this.initial_cycle = 0
      this.initial_amount = 0
      this.plans = {}
      this.PayMethods = [];
      this.edit_plan = 'none'
      this.edit_value = ''
      this.priority = 10


    },
    // 新規入力チェック
    onChangeNewMode() {
      if (this.newCheck) {
        this.$refs.formArea.style.backgroundColor = '#ffffe0'
        this.clearItemSelect()
      } else if (this.id) {
        this.$refs.formArea.style.backgroundColor = '#f0f8ff'
      } else {
        this.$refs.formArea.style.backgroundColor = '#eee'
      }
    }
  },
  data() {
    return {
      METHODS: {},
      PLANS: {},
      KINDS: {},
      itemList: [],

      edit_plan: 'none',
      edit_value: '',


      id: 0,
      kind : 'yoso',
      name: '',
      detail: '',
      amount: 0,
      description: '',
      quantity: 0,
      giftPoint: 0,
      giftExpire: 0,
      SalesBeginAt: '',
      SalesCloseAt: '',
      plans: {},
      PayMethods: [],

      initial_cycle: 0,
      initial_amount: 0,

      priority: 10,
      closed: 0,

      addBtnShow: false,
      selectedItem: null,

      newCheck: false,
      presetList: []
    }
  },

}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
.form-row {
  margin-top: 0;
  margin-bottom: 10px;
}

.form-label {
  text-align: right;

}

.form-input {
  padding: 0 20px;
}

.edit-button {
  width: 150px;
  margin: 10px auto;
}
</style>
