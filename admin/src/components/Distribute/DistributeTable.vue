<template>
  <div style="margin-top:10px;">
    <el-row>
      <el-col :span="3">
        件数：{{ total }}
      </el-col>

      <el-col :span="10">
        <el-row>
          <el-col :span="6">
            配信状況フィルタ
          </el-col>
          <el-col :span="1">
          </el-col>
          <el-col :span="17">
            <el-checkbox label="予約中" v-model="checkedFilter0" @change="onFilter"></el-checkbox>
            <el-checkbox label="配信済み" v-model="checkedFilter1" @change="onFilter"></el-checkbox>
            <el-checkbox label="予約取消" v-model="checkedFilter2" @change="onFilter"></el-checkbox>
          </el-col>
        </el-row>
      </el-col>

      <el-col :span="11">
        <el-row>
          <el-col :span="6">
            メール検索フィルタ
          </el-col>
          <el-col :span="1">
          </el-col>
          <el-col :span="17">
            <el-input v-model="mailSearch" style="width:100%" @change="onFilter"></el-input>
          </el-col>
        </el-row>
      </el-col>

    </el-row>

    <el-table ref="distributeTbl" :data="listItem" border style="width: 100%;margin-top:10px;" :highlight-current-row="true" @row-click="changeFunction" @sort-change="onSort" height="241">
      <el-table-column prop="status" width="120" sortable align="center" label="配信状況">
        <template #default="scope">
          <span v-if="scope.row.status === 0" style="color:#4169e1">
            {{ statusLabel[scope.row.status] }}
          </span>
          <span v-else-if="scope.row.status === 1" style="color:#228b22">
            {{ statusLabel[scope.row.status] }}
          </span>
          <span v-else-if="scope.row.status === 2" style="color:#a52a2a">
            {{ statusLabel[scope.row.status] }}
          </span>
        </template>
      </el-table-column>

      <el-table-column prop="distribute_at" width="180" sortable align="center" label="配信日時">
      </el-table-column>

      <el-table-column prop="name" width="250" sortable label="配信予約名">
      </el-table-column>

      <el-table-column prop="mail_name" width="250" sortable label="メール管理名">
        <template #default="scope">
          <el-popover placement="top-start" :title="scope.row.mail_subject" :width="200" trigger="click" :content="scope.row.mail_body">
            <template #reference>
              <el-button class="m-2"> {{ scope.row.mail_name }}</el-button>
            </template>
          </el-popover>
        </template>
      </el-table-column>

      <el-table-column prop="preset_name" width="200" sortable label="プリセット名">
      </el-table-column>

      <el-table-column prop="prospective_count" sortable align="right" label="見込人数">
      </el-table-column>
      <el-table-column prop="distribute_count" sortable align="right" label="配信人数">
      </el-table-column>

    </el-table>
    <el-pagination style="text-align:center;margin-top:10px;" layout="prev, pager, next" :total="total" :page-size="perPage" :current-page="currentPage" @current-change="onPageChange">
    </el-pagination>
  </div>
</template>
<script>
import TableWork from "../../mixins/TableWork.js";

export default {
  props: ['gridData', 'changeFunction'],
  mixins: [TableWork],
  data: function () {
    return {
      statusLabel: ['予約中', '配信済み', '予約取消'],
      checkedFilter0: true,
      checkedFilter1: true,
      checkedFilter2: true,
      currentPage: 1,
      perPage: 5,
      total: 0,
      filters: [],
      mailSearch: '',
      sort: 'distribute_at',
      sortDesc: true,
      sortTypes: {
        'status': {sortKey: 'status', type: 'num'},
        'distribute_at': {sortKey: 'distribute_at', type: 'date'},
        'name': {sortKey: 'name', type: 'str'},
        'mail_name': {sortKey: 'mail_name', type: 'str'},
        'preset_name': {sortKey: 'preset_name', type: 'str'},
        'prospective_count': {sortKey: 'prospective_count', type: 'num'}
      }
    }
  },
  computed: {},
  methods: {

    selectCancel() {
      this.$refs.distributeTbl.setCurrentRow()
    },
    onFilter(val) {
      this.filters = []
      if (!this.checkedFilter0) {
        this.filters.push({field: 'status', filterLabel: '0'})
      }
      if (!this.checkedFilter1) {
        this.filters.push({field: 'status', filterLabel: '1'})
      }
      if (!this.checkedFilter2) {
        this.filters.push({field: 'status', filterLabel: '2'})
      }
      //if (this.mailSearch !== '') {
      //  this.filters.push({field: 'mail_subject', stringFilter: this.mailSearch})
      //  this.filters.push({field: 'mail_name', stringFilter: this.mailSearch})
      //}
    }
  }
}
</script>
<style scope>

</style>
