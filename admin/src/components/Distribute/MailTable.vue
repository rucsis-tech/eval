<template>
  <div style="margin-top:10px;">
    <el-table ref="mailTbl" :data="listItem" border style="width: 100%" :highlight-current-row="true" @row-click="changeFunction" @sort-change="onSort" height="241">
      <el-table-column prop="id" width="80" sortable label="No">
      </el-table-column>

      <el-table-column prop="name" width="300" sortable label="メール管理名">
      </el-table-column>

      <el-table-column prop="subject" width="400" sortable label="メール件名">
      </el-table-column>

      <el-table-column width="200" sortable label="状態">
        <template #default="scope">
          {{ scope.row.status0 !== null ? ' 予約中 ' : '' }}
          {{ scope.row.status1 !== null ? ' 配信済 ' : '' }}
          {{ scope.row.status2 !== null ? ' 取消 ' : '' }}
        </template>
      </el-table-column>

      <el-table-column prop="updated_at" width="200" sortable label="更新日">
      </el-table-column>

    </el-table>
    <el-pagination style="text-align:center;margin-top:10px;" layout="prev, pager, next" :total="total" :page-size="perPage" :current-page="currentPage" @current-change="onPageChange">
    </el-pagination>
  </div>
</template>

<script>
import Common from '../../js/Common.js'
import TableWork from "../../mixins/TableWork.js";

export default {
  props: ['gridData', 'changeFunction'],
  mixins: [TableWork],
  data: function () {
    return {

      filters: [{field: 'distribute_status', filterLabel: '-1'}],
      sort : 'updated_at',
      sortDesc : true,
      sortTypes: {
        'id': {sortKey: 'id', type: 'num'},
        'name': {sortKey: 'name', type: 'str'},
        'subject': {sortKey: 'mail_subject', type: 'str'},
        'updated_at': {sortKey: 'updated_at', type: 'date'},
      }
    }
  },
  computed: {},
  methods: {

    selectCancel() {
      this.$refs.mailTbl.setCurrentRow()
    },
    onFilter(val) {
      this.filters = [{field: 'distribute_status', filterLabel: String(val)}]
    }
  }
}
</script>
<style scope>

</style>
