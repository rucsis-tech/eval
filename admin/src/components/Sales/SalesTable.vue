<template>
  <div>
    <span style="margin-right:2rem;">利用総額：￥{{ listSumAmount }}</span>
    <span>発行ポイント：{{ listSumPoint }} pt</span>
    <span>（有償：{{ listSumPremium }} pt / ギフト：{{ listSumGift }} pt）</span>
    <hr>

    <el-row>
      <el-col :span="3">
        件数：{{ gridData.length }}
      </el-col>

      <el-col :span="15" class="left-border">
        <el-pagination style="text-align:center;" layout="prev, pager, next" :total="total" :page-size="perPage" :current-page="currentPage" @current-change="onPageChange">
        </el-pagination>
      </el-col>

      <el-col :span="6" class="left-border">
        <el-button size="small" :disabled="!auth.SalesToMember" @click="createMemberList">会員検索に反映</el-button>
        <el-button size="small" :disabled="!auth.SalesCb" @click="onCbCopy">クリップボードコピー</el-button>
      </el-col>
    </el-row>

    <el-table ref="salesTbl" :data="listItem" border style="width: 100%;margin-top:5px" :highlight-current-row="true" @row-click="" @sort-change="onSort">

      <el-table-column prop="purchased_at" width="170" sortable align="center" label="決済日時">
      </el-table-column>
      <el-table-column prop="order_id" sortable width="210" header-align="center" align="center" label="伝票番号">
        <template #default="scope">
          {{ scope.row.order_id }}
          <!--el-button @click="api(scope.row.order_id)" style="margin:0">{{scope.row.order_id}}</el-button-->
        </template>
      </el-table-column>

      <el-table-column prop="nickname" width="160" align="left" sortable label="ニックネーム">
      </el-table-column>
      <el-table-column prop="name" width="240" align="left" sortable label="商品名">
      </el-table-column>
      <el-table-column prop="type" width="80" align="left" sortable label="種別">
      </el-table-column>

      <el-table-column prop="premium_point" width="80" align="right" sortable label="有償">
      </el-table-column>
      <el-table-column prop="gift_point" width="95" align="right" sortable label="ギフト">
      </el-table-column>

      <el-table-column prop="amount" :formatter="numberFormatter" sortable width="100" headerAlign="center" align="right" label="金額">
      </el-table-column>

      <el-table-column prop="surplus" sortable width="100" headerAlign="center" align="right" label="過不足">
        <template #default="scope">
          <span style="color:red;">{{ scope.row.surplus !== 0 ? common.numberFormat(scope.row.surplus) : '' }}</span>
        </template>
      </el-table-column>

      <el-table-column prop="method" width="120" align="center" sortable label="決済">
      </el-table-column>

      <el-table-column prop="memo" header-align="left" align="left" label="備考">
      </el-table-column>

    </el-table>
  </div>
</template>

<script>
import AppVue from "../../mixins/AppVue.js"
import TableWork from "../../mixins/TableWork.js"
import * as Define from '../../js/Define'
import Common from '../../js/Common'
import {API_SALES_UPDATE_GMO} from "../../js/Define";

export default {
  mixins: [AppVue, TableWork],
  computed: {},
  methods: {
    logined() {
      this.setAuth('sales')
      this.onListItemList = this.onListed
    },
    selectCancel() {
      this.$refs.userTbl.setCurrentRow()
    },

    api(order_id) {
      this.post(Define.API_SALES_UPDATE_GMO, {order_id: order_id}).then((response) => {
        console.log(response)
      })
    },
    // -------------------------------------------------------------------------------
    // フィルタソート結果を計上
    onListed(list) {
      let sumAmount = 0;
      let sumPremium = 0;
      let sumGift = 0;
      for (let i = 0; i < list.length; i++) {
        const item = list[i];
        sumAmount += item.amount;
        sumPremium += item.premium_point;
        sumGift += item.gift_point;

        //item.surplus = Common.numberFormat(item.surplus);
      }
      this.listSumAmount = Common.numberFormat(sumAmount);
      this.listSumPoint = Common.numberFormat(sumPremium + sumGift);
      this.listSumPremium = Common.numberFormat(sumPremium);
      this.listSumGift = Common.numberFormat(sumGift);
    },

    //--------------------------------------------------------------
    // 会員検索結果に反映

    createMemberList() {
      var list = []
      this.gridData.forEach(function (item, i) {
        if (list.indexOf(item.uid) === -1) {
          list.push(item.uid)
        }
      })
      this.post(Define.API_SEARCH, {uid: {mode: 1, value: list}}).then((response) => {
        this.$store.commit('setMemberList', response.list)
        this.message('会員検索に反映しました', 'success')
      })

    },

    //--------------------------------------------------------------
    // クリップボードコピー

    onCbCopy() {
      this.copyToClipboard(this.generateData())
    },
    generateData() {
      let texts = []
      texts.push('決済日時\t伝票番号\tニックネーム\t商品名\t種別\t有償\tギフト\t金額\t過不足\t決済\t備考')
      for (let i = 0; i < this.gridData.length; i++) {
        let line = []
        const item = this.gridData[i];

        line.push(this.qrt(item.purchased_at))
        line.push(item.order_id)
        line.push(this.qrt(item.nickname))
        line.push(this.qrt(item.name))
        line.push(item.type)

        line.push(item.premium_point)
        line.push(item.gift_point)
        line.push(item.amount)

        line.push(item.surplus !== 0 ? item.surplus : '')
        line.push(item.method)
        line.push(this.qrt(item.memo))

        texts.push(line.join('\t'))
      }

      return texts.join('\n')

    },
  },

  props: ['gridData', 'openDialogFunc'],
  data:
      function () {
        return {
          common: Common,
          isIndeterminate: true,
          listSumAmount: 0,
          listSumPoint: 0,
          listSumPremium: 0,
          listSumGift: 0,

          perPage: 10,
          checkAll: true,
          filters: [],
          filterItems: [
            {
              field: 'corp',
              cbxData: Define.paymentCorpList,
              cbxChecked: Define.paymentCorpList
            }
          ],
          filterData: [],
          sort: null,
          sortDesc: true,
          sortTypes: {
            'approved_at': {sortKey: 'approved_at', type: 'date'},
            'uid': {sortKey: 'uid', type: 'num'},
            'nickname': {sortKey: 'nickname', type: 'str'},
            'name': {sortKey: 'name', type: 'num'},
            'quantity': {sortKey: 'quantity', type: 'num'},
            'payment': {sortKey: 'payment', type: 'num'},
            'corp': {sortKey: 'corp', type: 'num'},
            'slip': {sortKey: 'slip', type: 'str'},
          }
        }
      }
}
</script>
<style scope>

</style>

