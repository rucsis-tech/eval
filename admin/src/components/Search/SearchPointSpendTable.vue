<template>
  <div class="r-white">
    <el-table ref="listTbl" :data="listItem" border height="455" style="width:100%;margin-top:5px" :highlight-current-row="true" @sort-change="onSort">

      <el-table-column prop="created_at" width="180" sortable align="center" label="日時">
      </el-table-column>

      <el-table-column prop="category" width="140" align="center" :formatter="categoryFormatter" sortable label="事由">
      </el-table-column>

      <el-table-column prop="rid" width="100" align="right" label="関連ID">
      </el-table-column>


      <el-table-column prop="premium_point" width="100" align="right" label="有償">
      </el-table-column>
      <el-table-column prop="gift_point" width="100" align="right" label="ギフト">
      </el-table-column>


      <el-table-column prop="identifier" width="150" align="left" label="詳細">
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
import Common from '../../js/Common'
import TableWork from "../../mixins/TableWork.js";

export default {
  mixins: [TableWork],
  methods: {
    onEvent(event) {
      if (event.refresh) this.refresh()
    },
    refresh() {
      this.gridData = this.memberDetail.points;
    },

    logined() {

    },
    onPageChange(val) {
      this.currentPage = val
    },
    onSort(sortObj) {
      if (!sortObj.order) {
        this.sortDesc = false
      } else if (sortObj.order === 'descending') {
        this.sortDesc = true
      } else if (sortObj.order === 'ascending') {
        this.sortDesc = false
      }
      this.sort = !sortObj.prop ? null : sortObj.prop
    },

    selectCancel() {
      this.$refs.listTbl.setCurrentRow()
    },
    selectRow(row) {
      if (!this.sort) {
        this.$refs.listTbl.setCurrentRow(row)
      }
    },
    categoryFormatter(row, column) {
      if (row.origin_id===0)  return "残高";
      return row[column.property]
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
        'created_at': {sortKey: 'created_at', type: 'date'},
      }
    }
  },
  computed: {},
}
</script>
<style scope>
</style>
