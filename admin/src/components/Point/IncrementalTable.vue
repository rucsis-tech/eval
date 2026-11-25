<template>
  <div>
    <span style="margin-right:2rem;">利用総額：￥{{ listSumAmount }}</span>
    <span>ギフトポイント：{{ listSumPoint }} pt</span>
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

      <el-table-column prop="start_at" width="170" sortable align="center" label="開始日時">
      </el-table-column>
      <el-table-column prop="uid" width="100" align="left" sortable label="UID">
      </el-table-column>
      <el-table-column prop="item_id" width="100" align="center" sortable label="商品ID">
      </el-table-column>
      <el-table-column prop="item_name" width="320" align="left" sortable label="商品名">
      </el-table-column>

      <el-table-column prop="amount" :formatter="numberFormatter" sortable width="110" headerAlign="center" align="right" label="購入金額">
      </el-table-column>

      <el-table-column prop="method" width="120" align="center" sortable label="決済">
      </el-table-column>
      <el-table-column prop="type" width="100" align="center" sortable label="取引">
      </el-table-column>
      <el-table-column prop="amount" :formatter="purchasedFormatter" width="100" align="center" sortable label="支払">
      </el-table-column>

    </el-table>
  </div>
</template>

<script>
import AppVue from "../../mixins/AppVue.js"
import TableWork from "../../mixins/TableWork.js"
import * as Define from '../../js/Define'
import Common from '../../js/Common'

export default {
  mixins: [AppVue, TableWork],
  computed: {},
  methods: {
    logined() {
      this.setAuth('sales')
      this.onListItemListed = this.onListed
    },
    selectCancel() {
      this.$refs.userTbl.setCurrentRow()
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
      }
      this.listSumAmount = Common.numberFormat(sumAmount);
      this.listSumPoint = Common.numberFormat(sumPremium + sumGift);
      this.listSumPremium = Common.numberFormat(sumPremium);
      this.listSumGift = Common.numberFormat(sumGift);
    },

    // -------------------------------------------------------------------------------
    // フィルター系　単一口座につき未使用
    createFilterItem(filterList) {
      var result = []
      filterList.forEach(function (item, i) {
        var tmp = []
        tmp.label = item
        tmp.checked = item
        result.push(tmp)
      })
      return result
    },
    onFilterAllCheck(event) {
      this.filterItems.forEach(function (item, i) {
        item.cbxChecked = event.target.checked ? item.cbxData : []
      })
      this.isIndeterminate = false
      this.currentPage = 1
      this.createFilterList()
    },
    onCbxGroupChange(value, index) {
      let checkedCount = value.length
      this.checkAll = checkedCount === this.filterItems[index].cbxData.length
      this.isIndeterminate = checkedCount > 0 && checkedCount < this.filterItems[index].cbxData.length
      this.currentPage = 1
      this.createFilterList()
    },
    createFilterList() {
      var localFilters = []
      this.filterItems.forEach(function (item, i) {
        item.cbxData.forEach(function (citem, j) {
          if (item.cbxChecked.indexOf(citem) === -1) {
            var tmp = []
            tmp.field = item.field
            tmp.filterLabel = citem
            localFilters.push(tmp)
          }
        })
      })
      this.filters = localFilters
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
      let text = ''
      text += '日付\t'
      text += '認証ID\t'
      text += 'ニックネーム\t'
      text += '商品名\t'
      text += '金額\t'
      text += '決済会社\t'
      text += '伝票ID\n'
      this.gridData.forEach(function (item, i) {
        text += item.approved_at + '\t'
        text += item.uid + '\t'
        text += item.nickname + '\t'
        text += name + '\t'
        text += item.payment + '\t'
        text += item.corp + '\t'
        text += item.slip + '\n'
      })
      this.copyToClipboard(text)
    },

    //--------------------------------------------------------------
    // フォーマッター
    // ー
    purchasedFormatter(row, column) {
      if (row[column.amount] === 0) return '無料';
      return '有償購入';
    },

  },

  props: ['gridData', 'openDialogFunc'],
  data:
      function () {
        return {
          isIndeterminate: true,
          listSumAmount: 0,
          listSumPoint: 0,
          listSumPremium: 0,
          listSumGift: 0,

          perPage: 30,
          checkAll: true,
          filters: [],

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

