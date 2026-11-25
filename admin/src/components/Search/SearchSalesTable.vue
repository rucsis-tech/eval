<template>
  <div class="r-white">
    <el-table ref="listTbl" :data="listItem" border height="455" style="width:100%;margin-top:5px" :highlight-current-row="true" @sort-change="onSort">

      <el-table-column prop="created_at" width="180" sortable align="center" label="申請日時">
      </el-table-column>

      <el-table-column prop="name" width="300" align="left" sortable label="商品名">
      </el-table-column>

      <el-table-column prop="amount" :formatter="numberFormatter" sortable width="110" headerAlign="center" align="right" label="取引金額">
      </el-table-column>

      <el-table-column prop="order_id" width="280" header-align="left" sortable align="left" label="伝票番号">
      </el-table-column>

      <el-table-column prop="purchased_at" sortable width="180" header-align="center" align="center" label="決済日時">
      </el-table-column>
    </el-table>

    <el-row>
      <el-col :span="2" class="px-2 pt-3">
        全{{ total }}件
      </el-col>

      <el-col :span="18">
        <el-pagination style="text-align:center;margin-top:10px;" layout="prev, pager, next" :total="total" :page-size="perPage" :current-page="currentPage" @current-change="onPageChange">
        </el-pagination>
      </el-col>

      <el-col :span="4" class="px-2 pt-3">
        <el-checkbox @change="onFilter" label="決済完了のみ" v-model="purchasedOnly"></el-checkbox>
      </el-col>

    </el-row>

  </div>

  <el-dialog title="購入商品一覧" v-model="dialogVisible" size="tiny">

    <el-row style="margin-bottom:5px;">
      <el-col :span="24" style="font-size:16px;">
        点数：{{ itemData.length }}点
      </el-col>
    </el-row>

    <el-table :data="itemData" border style="width: 100%" highlight-current-row>
      <el-table-column property="name" label="商品名" align="center"></el-table-column>
      <el-table-column property="payment" label="金額(円)" :formatter="numberFormatter" header-align="center" align="right"></el-table-column>
    </el-table>

    <el-row style="margin-bottom:5px;">
      <el-col :span="24" style="font-size:14px;text-align:right">
        合計：{{ selectTotal }}円
      </el-col>
    </el-row>

    <div style="text-align: center;margin-top: 15px;">
      <el-button type="primary" style="width:150px;" @click="closeDialog()">閉じる
      </el-button>
    </div>
  </el-dialog>

</template>
<script>
import Common from '../../js/Common.js'
import * as Define from '../../js/Define.js'
import TableWork from "../../mixins/TableWork.js";

export default {
  mixins: [TableWork],
  methods: {
    onEvent(event) {
      if (event.refresh) this.refresh()
    },
    refresh() {
      this.gridData = this.memberDetail.payments;
    },
    onFilter(e) {
      // filtersに要素名と値のオブジェクトをプッシュ
      this.filters = []
      if (this.purchasedOnly) {
        this.filters.push({field: 'purchased_at', filterLabel: null })
      }
      this.gridData = this.memberDetail.payments;
    },
    selectCancel() {
      this.$refs.listTbl.setCurrentRow()
    },
    selectRow(row) {
      if (!this.sort) {
        this.$refs.listTbl.setCurrentRow(row)
      }
    },
    getItems(row) {
      this.selectTotal = Common.numberFormat(row.payment_pay)
      var data = new FormData()
      data.append('payment_num', row.payment_num)
      this.post(Define.API_SALES_ITEM, data).then(this.getItemsRes)
    },
    getItemsRes(response) {
      if (typeof (response.data) === 'string' || response.data.length === 0) {
        this.alert('購入商品の取得に失敗しました:' + response.data)
      } else {
        this.itemData = response.data
        this.dialogVisible = true
      }
    },
    closeDialog() {
      this.dialogVisible = false
    },

  },
  watch: {
    selectedMember: function (newVal, oldVal) {
      this.selectRow(newVal)
    }
  },

  mounted: function () {
    this.refresh();
    this.parentEvent({event: this.onEvent});
  },
  props: ['memberDetail', 'parentEvent'],
  data: function () {
    return {
      gridData: [],
      perPage: 10,
      dialogVisible: false,
      purchasedOnly: false,
      sortTypes: {
        'created_at': {sortKey: 'created_at', type: 'date'},
        'name': {sortKey: 'name', type: 'str'},
        'quantity': {sortKey: 'quantity', type: 'num'},
        'amount': {sortKey: 'amount', type: 'num'},
        'order_id': {sortKey: 'order_id', type: 'str'},
        'purchased_at': {sortKey: 'purchased_at', type: 'str'},
      }
    }
  },
}
</script>
<style scope>
</style>
