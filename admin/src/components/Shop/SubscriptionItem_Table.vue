<template>

  <!-- ヘッダ ------------------------------------------------------------------------->
  <el-row class="contents">
    <el-col :span="8">
      <span>件数: {{ total }}</span>
      <el-radio-group v-model="nowOnSale" @change="onSaleFilter" style="margin-left: 1rem;">
        <el-radio :label="1">販売中</el-radio>
        <el-radio :label="2">販売停止</el-radio>
      </el-radio-group>
    </el-col>

    <el-col :span="16" v-if="total>perPage">
      <el-pagination style="text-align:center;" layout="prev, pager, next" :total="total" :page-size="perPage" :current-page="currentPage" @current-change="onPageChange">
      </el-pagination>
    </el-col>

  </el-row>

  <!-- リスト ------------------------------------------------------------------------->

  <el-table ref="coinTbl" :data="listItem" :row-class-name="tableRowClassName" border :highlight-current-row="true" @row-click="changeFunction" @sort-change="onSort" @selection-change="selectionChange" style="min-height:480px">

    <!--el-table-column type="selection" align="center" v-model="allCheckBoxFlag" width="55"></el-table-column-->

    <el-table-column prop="id" width="50" header-align="center" align="center" sortable label="id">
    </el-table-column>

    <el-table-column prop="kind" width="120" header-align="center" align="center" :formatter="kindFormatter" sortable label="種類">
    </el-table-column>

    <el-table-column prop="name" header-align="center" align="left" sortable label="商品名">
    </el-table-column>


    <el-table-column prop="amount" width="80" header-align="center" align="right" :formatter="numberFormatter" sortable label="通常価格">
    </el-table-column>

    <el-table-column prop="quantity" width="60" header-align="center" align="right" :formatter="numberFormatter" sortable label="日数">
    </el-table-column>

    <el-table-column prop="gift_point" width="80" header-align="center" align="right" :formatter="numberFormatter" sortable label="ギフト">
    </el-table-column>

    <el-table-column prop="priority" width="70" header-align="center" align="right" :formatter="numberFormatter" sortable label="表示順">
    </el-table-column>

    <el-table-column prop="sales_begin_at" width="100" header-align="center" align="right" :formatter="dateFormatter" sortable label="開始期日">
    </el-table-column>

    <el-table-column prop="sales_close_at" width="100" header-align="center" align="right" :formatter="dateFormatter" sortable label="終了期日">
    </el-table-column>

    <el-table-column prop="description" width="300" header-align="center" align="left" :formatter="numberFormatter" sortable label="価格説明">
    </el-table-column>

  </el-table>

  <!-- フッタ ------------------------------------------------------------------------->

  <el-row class="contents" v-if="total>perPage">
    <el-col :span="8" style="padding-top:4px;padding-left:6px;">
    </el-col>

    <el-col :span="16" class="left-border">
      <el-pagination style="text-align:center;" layout="prev, pager, next" :total="total" :page-size="perPage" :current-page="currentPage" @current-change="onPageChange">
      </el-pagination>
    </el-col>

  </el-row>

</template>

<script>
import Common from '../../js/Common'
import TableWork from "../../mixins/TableWork.js";

const date = new Date()

export default {
  mixins: [TableWork],

  computed: {},
  created: function () {
    this.onSaleFilter()
  },
  methods: {
    onSaleFilter() {
      if (this.nowOnSale == 1) {
        // 販売中
        this.filters = [{field: 'closed', filterLabel: 1, not: false}]
      } else {
        // 停止中
        this.filters = [{field: 'closed', filterLabel: 0, not: false}]
      }
    },

    selectCancel() {
      this.$refs.coinTbl.setCurrentRow()
    },
    selectionChange(val) {
      // console.log('check box:' + JSON.stringify(val))
      if (val.length) {
        this.allCheckBoxFlag = true
        this.checkBoxes = val
      } else {
        this.allCheckBoxFlag = false
        this.checkBoxes = []
      }
    },

    tableRowClassName(row, rowIndex) {
      let currentDate = Common.dateFormat(date, 'YYYY-MM-DD hh:mm:ss')
      if (row.row.sales_close < currentDate) {
        return 'gray-row'
      }
      return ''
    },

    kindFormatter(row, column) {
      return this.KINDS[row[column.property]];
    },
  },

  props: ['gridData', 'changeFunction', 'loadFunction', 'auth' , 'KINDS'],
  data: function () {
    return {
      nowOnSale: 1,
      currentPage: 1,
      perPage: 5,
      total: 0,
      cbxFilter: true,
      filters: [],
      sortTypes: {
        'id': {sortKey: 'id', type: 'num'},
        'name': {sortKey: 'name', type: 'str'},
        'price': {sortKey: 'price', type: 'num'},
        'limit_count': {sortKey: 'limit_count', type: 'num'},
        'sales_begin_at': {sortKey: 'sales_begin_at', type: 'date'},
        'sales_close_at': {sortKey: 'sales_close_at', type: 'date'},
      },
      allCheckBoxFlag: false,
      checkBoxes: []
    }


  }
}
</script>
<style scope>
.el-table .gray-row {
  background: #cccccc;
}
</style>
