<template>
  <div>
    <span>件数：{{ total }}</span>&emsp;
    <el-checkbox @change="onFilter" label="スタッフを除く" v-model="staffFilter"></el-checkbox>
    <el-checkbox @change="onFilter" label="退会者を除く" v-model="leaveFilter"></el-checkbox>

    <el-pagination style="text-align:center;" layout="prev, pager, next" :total="total" :page-size="perPage" :current-page="currentPage" @current-change="onPageChange">
    </el-pagination>

    <el-table :data="listItem" :row-class-name="tableRowClassName" border style="width: 100%" @sort-change="onSort">
      <el-table-column label="認証ID" prop="uid" align="right" headerAlign="center" sortable width="100"></el-table-column>
      <el-table-column label="ニックネーム" prop="nickname" align="left" headerAlign="center" sortable width="150"></el-table-column>
      <el-table-column label="広告コード" prop="ad_code" align="left" headerAlign="center" sortable width="120"></el-table-column>
      <el-table-column label="入会日時" prop="created_at" align="center" headerAlign="center" sortable width="160"></el-table-column>

      <el-table-column label="VIP保有" prop="vip_count" align="right" headerAlign="center" sortable width="60"></el-table-column>
      <el-table-column label="VIP空数" prop="vip_empty" align="right" headerAlign="center" sortable width="60"></el-table-column>

      <el-table-column label="Lite保有" prop="lite_count" align="right" headerAlign="center" sortable width="60"></el-table-column>
      <el-table-column label="Lite空数" prop="lite_empty" align="right" headerAlign="center" sortable width="60"></el-table-column>

      <el-table-column label="期間指名数" prop="registered" align="right" headerAlign="center" sortable width="80"></el-table-column>

      <el-table-column label="総獲得スコア" prop="acquired_score" align="right" headerAlign="center" sortable width="100"></el-table-column>

      <el-table-column label="VIP申込数" prop="apply_quantity" align="right" headerAlign="center" sortable width="65" :formatter="numberFormatter"></el-table-column>
      <el-table-column label="VIP申込額" prop="apply_payment" align="right" headerAlign="center" sortable width="100" :formatter="numberFormatter"></el-table-column>
      <el-table-column label="VIP購入数" prop="approved_quantity" align="right" headerAlign="center" sortable width="65" :formatter="numberFormatter"></el-table-column>
      <el-table-column label="VIP購入額" prop="approved_payment" align="right" headerAlign="center" sortable width="100" :formatter="numberFormatter"></el-table-column>

      <el-table-column label="Lite申込数" prop="lite_apply_quantity" align="right" headerAlign="center" sortable width="65" :formatter="numberFormatter"></el-table-column>
      <el-table-column label="　Lite　　　申込額" prop="lite_apply_payment" align="right" headerAlign="center" sortable width="100" :formatter="numberFormatter"></el-table-column>
      <el-table-column label="Lite購入数" prop="lite_approved_quantity" align="right" headerAlign="center" sortable width="65" :formatter="numberFormatter"></el-table-column>
      <el-table-column label="　Lite　　　購入額" prop="lite_approved_payment" align="right" headerAlign="center" sortable width="100" :formatter="numberFormatter"></el-table-column>

      <el-table-column label="スタッフ" prop="staff_flag" align="right" headerAlign="center" sortable width="80" :formatter="numberFormatter"></el-table-column>
      <el-table-column label="退会" prop="locked_at" align="right" headerAlign="center" sortable width="80" :formatter="numberFormatter"></el-table-column>
    </el-table>

  </div>
</template>

<script>
import Common from '../../js/Common'
import TableWork from "../../mixins/TableWork.js";

export default {
  props: ['gridData', 'setNumsFunction'],
  mixins: [TableWork],
  data: function () {
    return {
      staffFilter: true,
      leaveFilter: true,
      currentPage: 1,
      perPage: 18,
      total: 0,
      filters: [{field: 'staff_flag', filterLabel: '1'}, {field: 'locked_at', filterLabel: '1'}],
      sort: null,
      sortDesc: true,
      sortTypes: {
        'uid': {sortKey: 'uid', type: 'num'},
        'nickname': {sortKey: 'nickname', type: 'str'},
        'ad_code': {sortKey: 'ad_code', type: 'str'},
        'created_at': {sortKey: 'created_at', type: 'str'},

        'vip_count': {sortKey: 'vip_count', type: 'num'},
        'vip_empty': {sortKey: 'vip_empty', type: 'num'},
        'lite_count': {sortKey: 'lite_count', type: 'num'},
        'lite_empty': {sortKey: 'lite_empty', type: 'num'},

        'registered': {sortKey: 'registered', type: 'num'},

        'acquired_score': {sortKey: 'acquired_score', type: 'num'},

        'acquired': {sortKey: 'acquired', type: 'num'},

        'apply_quantity': {sortKey: 'apply_quantity', type: 'num'},
        'apply_payment': {sortKey: 'apply_payment', type: 'num'},
        'approved_quantity': {sortKey: 'approved_quantity', type: 'num'},
        'approved_payment': {sortKey: 'approved_payment', type: 'num'},


        'lite_apply_quantity': {sortKey: 'lite_apply_quantity', type: 'num'},
        'lite_apply_payment': {sortKey: 'lite_apply_payment', type: 'num'},
        'lite_approved_quantity': {sortKey: 'lite_approved_quantity', type: 'num'},
        'lite_approved_payment': {sortKey: 'lite_approved_payment', type: 'num'},

        'staff_flag': {sortKey: 'staff_flag', type: 'num'},
        'locked_at': {sortKey: 'locked_at', type: 'num'},
      }
    }
  },
  computed: {},
  methods: {
    onFilter(e) {
      // filtersに要素名と値のオブジェクトをプッシュ
      this.filters = []
      if (this.staffFilter) {
        this.filters.push({field: 'staff_flag', filterLabel: '1'})
      }
      if (this.leaveFilter) {
        this.filters.push({field: 'locked_at', filterLabel: '1'})
      }
    },
    tableRowClassName(row, index) {
      console.log(row.row)
      // 退会者?
      if (row.row.locked_at) {
        return 'del-row'
      } else if (row.row.staff_flag) {
        return 'staff-row'
      }
      return ''
    }
  }
}
</script>
<style scope>
.el-table .del-row {
  background: #ffe4e1;
  color: #802525;
}

.el-table .staff-row {
  background: #f0f8ff
}
</style>
