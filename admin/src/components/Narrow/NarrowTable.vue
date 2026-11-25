<template>
  <div style="background-color: white">

    <el-row >
      <el-col :span="3" style="padding:4px 8px;">
        <span>件数：{{ total }}</span>&emsp;
      </el-col>

      <el-col :span="15" class="left-border">
        <el-pagination style="text-align:center;" layout="prev, pager, next" :total="total" :page-size="perPage" :current-page="currentPage" @current-change="onPageChange">
        </el-pagination>
      </el-col>

      <el-col :span="6" class="left-border">
        <el-checkbox @change="onFilter" label="スタッフを除く" v-model="staffFilter"></el-checkbox>
        <el-checkbox @change="onFilter" label="退会者を除く" v-model="leaveFilter"></el-checkbox>
      </el-col>
    </el-row>

    <el-table :data="listItem" :row-class-name="tableRowClassName" border style="width: 100%" @sort-change="onSort">
      <el-table-column label="UID" prop="uid" width="110" align="left" headerAlign="center" sortable>
      </el-table-column>
      <el-table-column label="会員番号" prop="mid" width="110" align="right" headerAlign="center" sortable>
      </el-table-column>
      <el-table-column label="email" prop="email" align="left" headerAlign="center" sortable>
      </el-table-column>
      <el-table-column label="入会日時" prop="created_at" align="center" headerAlign="center" sortable width="170">
      </el-table-column>
      <el-table-column label="最終アクセス" prop="accessed_at" align="right" headerAlign="center" sortable width="170">
      </el-table-column>
      <el-table-column label=" " prop="value" align="right" width="120" headerAlign="center" sortable>
      </el-table-column>
    </el-table>

  </div>
</template>

<script>
import Common from '../../js/Common'
import TableWork from "../../mixins/TableWork.js";

export default {
  mixins: [TableWork],
  computed: {},
  methods: {
    onFilter(e) {
      // filtersに要素名と値のオブジェクトをプッシュ
      this.filters = []
      if (this.staffFilter) {
        this.filters.push({field: 'staff_flag', filterLabel: '1'})
      }
      if (this.leaveFilter) {
        this.filters.push({field: 'locked_at', filterLabel: null, not: true})
      }
    },
    tableRowClassName(row, index) {
      //console.log(row.row)
      // 退会者?
      if (row.row.locked_at) {
        return 'del-row'
      } else if (row.row.staff_flag) {
        return 'staff-row'
      }
      return ''
    }
  },

  mounted: function () {
    this.onFilter()
  },
  props: ['gridData', 'setNumsFunction'],
  data: function () {
    return {
      staffFilter: false,
      leaveFilter: false,
      currentPage: 1,
      perPage: 10,
      total: 0,
      filters: [],
      sort: null,
      sortDesc: true,
      sortTypes: {
        'uid': {sortKey: 'uid', type: 'string'},
        'mid': {sortKey: 'mid', type: 'num'},
        'created_at': {sortKey: 'created_at', type: 'date'},
        'accessed_at': {sortKey: 'last_login_at', type: 'date'},
        'payment_sum': {sortKey: 'payment_sum', type: 'num'}
      }
    }
  },

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
