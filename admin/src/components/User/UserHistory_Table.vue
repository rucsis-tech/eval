<template>
  <div style="margin-top:10px;">
    <el-row>
      <el-col :span="7">
        <el-date-picker v-model="since" type="date" placeholder="前" :clearable="false" :picker-options="pickerOptions" style="width:150px">
        </el-date-picker>
        ～
        <el-date-picker v-model="until" type="date" placeholder="後" :clearable="false" :picker-options="pickerOptions" style="width:150px">
        </el-date-picker>
      </el-col>

      <el-col :span="6"> ユーザーフィルタ&nbsp;
        <el-input style="width:150px" icon="close" v-model="userName" :on-icon-click="userIconClick" placeholder="UID or ユーザー名"></el-input>
      </el-col>

      <el-col :span="8">
        利用項目フィルタ&nbsp;
        <el-select v-model="division" placeholder="利用項目" style="width:150px">
          <template v-for="(division,key) in this.$store.state.authDefine">
            <el-option :label="division.label" :value="key"></el-option>
          </template>
        </el-select>

      </el-col>
      <el-col :span="3">
        <el-button @click="loadFunction">再読み込み</el-button>
      </el-col>
    </el-row>


    <el-table ref="uhTbl" :data="listItem" border style="width: 100%;margin-top:10px;" :highlight-current-row="true" @sort-change="onSort">
      <el-table-column prop="id" width="120" align="right" headerAlign="center" sortable label="履歴No">
      </el-table-column>
      <el-table-column prop="created_at" width="200" align="center" headerAlign="center" sortable label="利用日時">
      </el-table-column>
      <el-table-column prop="uid" headerAlign="center" width="150" sortable label="ユーザーID">
      </el-table-column>
      <el-table-column prop="user_name" headerAlign="center" width="200" sortable label="ユーザー名">
      </el-table-column>
      <el-table-column prop="auth" headerAlign="center" width="300" sortable label="利用項目">
      </el-table-column>
      <el-table-column prop="result" headerAlign="center" width="120" align="center" sortable label="利用結果">
      </el-table-column>
      <el-table-column align="center">
        <template #default="scope">
          <el-button type="text" style="color:#4169e1" @click.native.prevent="loadTarget(scope.row.id)">
            操作内容
          </el-button>
        </template>
      </el-table-column>
    </el-table>


    <el-pagination style="text-align:center;margin-top:10px;" layout="prev, pager, next" :total="total" :page-size="perPage" :current-page="currentPage" @current-change="onPageChange">
    </el-pagination>


    <el-dialog :title="selectedTitle" v-model="dialogTableVisible" size="small">
      <div style="margin: 4px auto;font-size:16px;">
        <h5>[{{ $store.state.authDefine[respoData.division].label }}] {{ $store.state.authDefine[respoData.division].auth[respoData.auth].label }}</h5>

      </div>

      <div style="margin: 4px auto;font-size:16px;word-break: break-all;">
        <h6>操作内容</h6>
        {{ respoData.params }}
      </div>
      <div v-if="respoData.operate_id" style="margin: 20px auto;font-size:16px;">
        <h6>操作対象</h6>
        認証ID {{ respoData.operate_id }}
        ニックネーム {{ respoData.nickname }}
      </div>
    </el-dialog>
  </div>

</template>

<script>
import Common from '../../js/Common'
import * as Define from '../../js/Define'
import AppVue from "../../mixins/AppVue.js";
import TableWork from "../../mixins/TableWork.js";

export default {
  props: ['gridData', 'changeFunction', 'loadFunction'],
  mixins: [AppVue, TableWork],
  data: function () {
    return {
      currentPage: 1,
      perPage: 15,
      total: 0,
      filters: [],
      sort: null,
      sortDesc: true,
      userName: '',
      division: '',
      since: new Date().setTime(new Date().getTime() - 3600 * 1000 * 24 * 7),
      until: new Date().setTime(new Date().getTime()),
      pickerOptions: {
        shortcuts: [{
          text: '今日',
          onClick(picker) {
            picker.$emit('pick', new Date())
          }
        },
          {
            text: '先月',
            onClick(picker) {
              let date = new Date()
              let lastMonth = new Date(date.getYear() + 1900, date.getMonth() - 1, date.getDate())
              picker.$emit('pick', lastMonth)
            }
          },
          {
            text: '去年',
            onClick(picker) {
              let lastYear = new Date().setTime(new Date().getTime() - 3600 * 1000 * 24 * 365)
              picker.$emit('pick', lastYear)
            }
          }]
      },
      dialogTableVisible: false,
      respoData: [],
      detailData: [],
      selectedTitle: '',
      perPageDetail: 10,
      currentPageDetail: 1,
      sortTypes: {
        'user_his_num': {sortKey: 'user_his_num', type: 'num'},
        'use_his_date': {sortKey: 'use_his_date', type: 'date'},
        'user_id': {sortKey: 'user_id', type: 'str'},
        'user_name': {sortKey: 'user_name', type: 'str'},
        'uc_item': {sortKey: 'uc_item', type: 'str'},
        'status_label': {sortKey: 'status_label', type: 'str'}
      }
    }
  },
  methods: {
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
    numberFormatter(row, column) {
      return Common.numberFormat(row[column.property])
    },
    dateFormatter(row, column) {
      return Common.dateFormat(row[column.property], 'YYYY-MM-DD')
    },
    selectCancel() {
      this.$refs.uhTbl.setCurrentRow()
    },

    userIconClick() {
      this.userFilter = ''
      this.onFilter()
    },
    loadTarget(log_id) {
      this.selectedTitle = '履歴No:' + String(log_id)
      var data = new FormData()
      data.append('log_id', log_id)
      this.post(Define.API_USER_HISTORY, data).then(this.uHisDetailRes)
    },
    uHisDetailRes(response) {
      console.log(this.$store.state.authDefine)
      console.log(response);
      if (response.log.params) {
        this.respoData = response.log
        this.onPageChangeDetail(1)
        this.dialogTableVisible = true
      } else {
        this.alert('ユーザー履歴の対象者取得に失敗しました。' + response.log.params)
      }
    },
    onPageChangeDetail(cPage) {
      /*
      this.detailData = []
      let init = (cPage - 1) * this.perPageDetail
      let count = init + this.perPageDetail
      for (let i = init; i < count; i++) {
        if (!this.respoData[i]) break
        this.detailData.push(this.respoData[i])
      }

       */
    }
  }
}
</script>
<style scope>

</style>
