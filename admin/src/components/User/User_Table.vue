<template>

  <el-row style="background-color: white;margin:0;padding:4px;">
    <el-col :span="2"> ユーザー数：{{ total }}</el-col>
    <el-col :span="2">
      <el-checkbox @change="onDeleteFilter" label="削除済を表示" v-model="delCheck"></el-checkbox>
    </el-col>
    <el-col :span="17">
      <el-pagination style="text-align:center;" layout="prev, pager, next" :total="total" :page-size="perPage" :current-page="currentPage" @current-change="onPageChange"></el-pagination>
    </el-col>
    <el-col :span="3">
      <el-button @click="loadFunction" style="">再読み込み</el-button>
    </el-col>
  </el-row>

  <el-table ref="userTbl" :data="listItem" border style="min-height:240px" :highlight-current-row="true" :row-class-name="tableRowClassName" @row-click="changeFunction" @sort-change="onSort">

    <el-table-column prop="uid" width="150" sortable headerAlign="center" align="center" label="UID"></el-table-column>
    <el-table-column prop="email" width="300" sortable headerAlign="center" label="メールアドレス"></el-table-column>
    <el-table-column prop="admin_name" width="200" sortable headerAlign="center" label="氏名"></el-table-column>
    <el-table-column prop="role" sortable headerAlign="center" width="150" label="所属"></el-table-column>
    <el-table-column prop="tel" sortable headerAlign="center" width="140" label="緊急連絡先"></el-table-column>
    <el-table-column prop="login_at" sortable headerAlign="center" width="200" align="center" label="最終ログイン日時"></el-table-column>

  </el-table>

  <el-row style="background-color: white;">
    <el-col :span="4">
    </el-col>
    <el-col :span="16">
      <el-pagination style="text-align:center;padding:0px;" layout="prev, pager, next" :total="total" :page-size="perPage" :current-page="currentPage" @current-change="onPageChange"></el-pagination>
    </el-col>
    <el-col :span="4">
    </el-col>
  </el-row>


</template>
<script>
import TableWork from "../../mixins/TableWork"
import AppVue from "../../mixins/AppVue"

export default {
  props: ['gridData', 'changeFunction', 'loadFunction'],
  mixins: [AppVue, TableWork],
  data: function () {
    return {
      delCheck: false,
      filters: [{field: 'disabled_at', filterLabel: null, not: true}],
      mailSearch: '',

      perPage: 5,
    }
  },
  computed: {},

  methods: {
    selectCancel() {
      this.$refs.userTbl.setCurrentRow()
    },
    onDeleteFilter(e) {
      // filtersに要素名と値のオブジェクトをプッシュ
      if (!this.delCheck) {
        this.filters.push({field: 'disabled_at', filterLabel: null, not: true})
      } else {
        this.filters = []
      }
      this.$parent.clearUserDetail()
    },
    tableRowClassName(row, index) {
      if (row.row.disable) {
        return 'del-row'
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
</style>
