<template>
  <div class="r-white">
    <el-table ref="listTbl" :data="listItem" border height="480" style="" :highlight-current-row="true" :row-class-name="tableRowClassName" @row-click="rowClick" @sort-change="onSort">

      <el-table-column prop="uid" width="120" headerAlign="center" sortable align="center" label="UID">
      </el-table-column>

      <el-table-column prop="email" width="350" headerAlign="center" sortable label="メールアドレス">
      </el-table-column>

      <el-table-column prop="nickname" width="240" headerAlign="center" sortable label="ニックネーム">
      </el-table-column>

      <el-table-column prop="created_at" sortable width="180" headerAlign="center" align="center" label="入会日">
      </el-table-column>

      <!--el-table-column prop="payment_sum" sortable :formatter="numberFormatter" headerAlign="center" label="利用総額" align="right">
      </el-table-column-->

    </el-table>


    <el-pagination style="text-align:center;margin-top:10px;" layout="prev, pager, next" :total="total" :page-size="perPage" :current-page="currentPage" @current-change="onPageChange">
    </el-pagination>


  </div>
</template>
<script>
import Common from '../../js/Common.js'
import AppVue from "../../mixins/AppVue.js";
import TableWork from "../../mixins/TableWork.js";

export default {
  mixins: [AppVue, TableWork],
  methods: {
    onEvent(event) {
      if (event.refresh) this.refresh()
    },
    refresh() {
      this.gridData = this.$store.state.memberList
    },

    selectCancel() {
      this.$refs.listTbl.setCurrentRow()
    },
    selectRow(row) {
      if (!this.sort) {
        this.$refs.listTbl.setCurrentRow(row)
      }
    },
    tableRowClassName(row, index) {
      console.log(row.row)
      if (row.row.locked_at) {
        // 退会者?
        return 'del-row'
      } else if (row.row.staff_flag) {
        // STAFF
        return 'staff-row'
      }
      return ''
    },
    rowClick(row) {
      this.parentEvent({loadMember: row.uid})
    }
  },
  watch: {
    selectedMember: function (newVal, oldVal) {
      this.selectRow(newVal)
    }
  },

  mounted() {
    this.parentEvent({event: this.onEvent});
    this.selectRow(this.selectedMember)
  },
  props: ['memberDetail', 'parentEvent'],
  data: function () {
    return {
      gridData: this.$store.state.memberList,
      delCheck: false,
      perPage: 10,
      sortTypes: {
        'uid': {sortKey: 'uid', type: 'num'},
        'email': {sortKey: 'email', type: 'str'},
        'nickname': {sortKey: 'nickname', type: 'str'},
        'created_at': {sortKey: 'created_at', type: 'date'},
        'payment_sum': {sortKey: 'payment_sum', type: 'num'},
        'item_count': {sortKey: 'item_count', type: 'num'}
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
