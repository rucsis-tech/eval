<template>

  <div class="r-white">
    <el-table ref="listTbl" :data="listItem" border height="455" style="width:100%;margin-top:5px" :highlight-current-row="true" @sort-change="onSort">

      <el-table-column prop="created_at" width="180" sortable align="center" label="獲得日">
      </el-table-column>

      <el-table-column prop="target" :formatter="compensationFormatter" width="200" align="center" label="獲得事由">
      </el-table-column>

      <el-table-column prop="rid" width="100" align="center" label="関連ID">
      </el-table-column>

      <el-table-column prop="acquire" :formatter="numberFormatter" sortable width="110" headerAlign="center" align="right" label="スコア">
      </el-table-column>


    </el-table>

    <el-row>
      <el-col :span="2" class="px-2 pt-3">
        全{{ total }}件
      </el-col>

      <el-col :span="22">
        <el-pagination style="text-align:center;margin-top:10px;" layout="prev, pager, next" :total="total" :page-size="perPage" :current-page="currentPage" @current-change="onPageChange">
        </el-pagination>
      </el-col>
    </el-row>

  </div>
</template>
<script>
import Common from '../../js/Common.js'
import TableWork from "../../mixins/TableWork.js";

export default {
  mixins: [TableWork],
  computed: {},
  methods: {
    onEvent(event) {
      if (event.refresh) this.refresh()
    },
    refresh() {
      this.gridData = this.memberDetail.scores;
    },
    compensationFormatter(row, column) {
      if (this.$store.state.consts.score_targets[row[column.property]]) {
        return this.$store.state.consts.score_targets[row[column.property]];
      } else {
        return row[column.property];
      }
    },
  },
  watch: {
    selectedMember: function (newVal, oldVal) {
      this.selectRow(newVal)
    }
  },

  mounted() {
    this.refresh();
    this.parentEvent({event: this.onEvent});
  },
  props: ['memberDetail', 'parentEvent'],
  data: function () {
    return {
      gridData: [],
      currentPage: 1,
      perPage: 10,
      total: 0,
      filters: [],
      sort: null,
      sortDesc: true,
      sortTypes: {
        'date': {sortKey: 'date', type: 'str'},
        'bamei': {sortKey: 'bamei', type: 'str'},
        'acquired': {sortKey: 'acquired', type: 'num'},
        'arrival': {sortKey: 'arrival', type: 'str'},
      }
    }
  },
}
</script>
<style scope>
</style>
