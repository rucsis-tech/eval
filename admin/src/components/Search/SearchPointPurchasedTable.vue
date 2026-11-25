<template>
  <div ref="childView" class="r-white">
    <el-table ref="listTbl" :data="listItem" border height="455" style="width:100%;margin-top:5px" :highlight-current-row="true" @sort-change="onSort">

      <el-table-column prop="created_at" width="180" sortable align="center" label="日時">
      </el-table-column>

      <el-table-column prop="type" width="100" align="center" :formatter="typedFormatter" sortable label="タイプ">
      </el-table-column>

      <el-table-column prop="acquire" width="100" align="right" sortable label="入手Pt">
      </el-table-column>

      <el-table-column prop="current" width="120" align="right" sortable label="未使用Pt">
      </el-table-column>

      <el-table-column prop="invalid" width="120" align="right" sortable label="失効Pt">
      </el-table-column>

      <el-table-column prop="expire_at" width="200" align="center" sortable label="期限">
      </el-table-column>

      <el-table-column :formatter="reasonFormatter" width="300" align="left" sortable label="事由">
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
      this.gridData = this.memberDetail.point_purchased;
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
    typedFormatter(row, column) {
      if (row.type === 0) return "有償";
      if (row.category === "give") return "配布";
      if (row.category === "content_cancel") return "特別付与";
      return "ギフト";
    },

  },
  watch: {
    selectedMember: function (newVal, oldVal) {
      this.selectRow(newVal)
    }
  },


  mounted() {
    this.parentEvent({event: this.onEvent})
    this.refresh()
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
        'expire_at': {sortKey: 'expire_at', type: 'str'}
      }
    }
  },
  computed: {},
}
</script>
<style scope>
</style>
