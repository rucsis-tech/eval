<template>
  <div style="margin-top:10px;">
    <el-table ref="mailTbl" :data="listItem" border style="width: 100%" :highlight-current-row="true" @row-click="changeFunction" @sort-change="onSort" height="241">
      <el-table-column prop="id" width="80" sortable label="id">
      </el-table-column>

      <el-table-column prop="title" width="350" sortable label="トピック件名">
        <template #default="scope">
          <div style="height:1.5rem;">{{ scope.row.title }}</div>
        </template>
      </el-table-column>

      <el-table-column prop="content" width="350" sortable label="トピック内容">
        <template #default="scope">
          <div style="height:1.5rem;">{{ scope.row.content }}</div>
        </template>
      </el-table-column>

      <el-table-column prop="begin_at" width="200" sortable label="掲載日">
      </el-table-column>

      <el-table-column prop="close_at" width="200" sortable label="終了日">
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
      sort: 'begin_at',
      sortDesc: true,

      filters: [{field: 'distribute_status', filterLabel: '-1'}],
      sortTypes: {
        'title': {sortKey: 'title', type: 'num'},
        'content': {sortKey: 'content', type: 'str'},
        'begin_at': {sortKey: 'begin_at', type: 'date'},
        'close_at': {sortKey: 'close_at', type: 'date'},
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
<style scoped>
</style>
