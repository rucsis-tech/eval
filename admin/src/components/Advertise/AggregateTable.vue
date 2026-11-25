<template>
  <div style="margin-top:10px;">
    <el-row>
      <el-col :span="15">
        <el-radio class="radio" v-model="term" label="0">全期間</el-radio>
        <el-radio class="radio" v-model="term" label="1" style="margin-right: 10px;">指定期間</el-radio>
        <el-date-picker v-model="since" type="date" placeholder="前" :clearable="false">
        </el-date-picker>
        ～
        <el-date-picker v-model="until" type="date" placeholder="後" :clearable="false">
        </el-date-picker>
        <el-button @click="loadFunction" style="margin-left:20px;">再読み込み</el-button>
      </el-col>
      <el-col :span="9">
        広告コードフィルタ&nbsp;<el-input v-model="adcode" type="text" style="width:150px" @change="onFilter"></el-input>
      </el-col>

    </el-row>
    <el-table ref="aggTbl" :data="listItem" border style="width: 100%;margin-top:10px;" :highlight-current-row="true" @sort-change="onSort">
      <el-table-column prop="ad_code" width="130" header-align="center" sortable label="広告コード">
      </el-table-column>
      <el-table-column prop="name" width="300" header-align="center" sortable label="広告名称">
        <template #default="scope">
          <span style="font-size:12px;">{{ scope.row.name }}</span>
        </template>
      </el-table-column>
      <el-table-column prop="ad_date" width="110" align="center" sortable label="掲載日">
      </el-table-column>
      <el-table-column prop="price" width="119" align="right" header-align="center" :formatter="numberFormatter" sortable label="料金">
      </el-table-column>
      <el-table-column prop="empty_mail" width="90" align="right" header-align="center" :formatter="numberFormatter" sortable label="空メ">
      </el-table-column>
      <el-table-column prop="form_send" width="90" align="right" header-align="center" :formatter="numberFormatter" sortable label="サイト">
      </el-table-column>
      <el-table-column prop="reg_member" width="90" align="right" header-align="center" :formatter="numberFormatter" sortable label="登録">
      </el-table-column>
      <el-table-column prop="reg_line" width="90" align="right" header-align="center" :formatter="numberFormatter" sortable label="LINE">
      </el-table-column>
      <el-table-column prop="leave_member" width="90" align="right" header-align="center" :formatter="numberFormatter" sortable label="退会">
      </el-table-column>
      <el-table-column prop="ad_sales" width="120" align="right" header-align="center" :formatter="numberFormatter" sortable label="売上">
      </el-table-column>
      <el-table-column prop="recov_rate" width="110" align="right" header-align="center" :formatter="rateFormatter" sortable label="回収率">
      </el-table-column>
    </el-table>
    <el-pagination style="text-align:center;margin-top:10px;" layout="prev, pager, next" :total="total" :page-size="perPage" :current-page="currentPage" @current-change="onPageChange">
    </el-pagination>
  </div>
</template>

<script>
import TableWork from "../../mixins/TableWork.js";

export default {
  props: ['gridData', 'changeFunction', 'loadFunction', 'auth'],
  mixins: [TableWork],
  data: function () {
    return {
      currentPage: 1,
      perPage: 15,
      total: 0,
      adcode: '',
      filters: [],
      sort: null,
      sortDesc: true,
      term: '0',
      since: new Date().setTime(new Date().getTime() - 3600 * 1000 * 24 * 7),
      until: new Date().setTime(new Date().getTime()),
      perPageDetail: 10,
      currentPageDetail: 1,
      sortTypes: {
        'ad_code': {sortKey: 'ad_code', type: 'str'},
        'ad_name': {sortKey: 'ad_name', type: 'str'},
        'ad_date': {sortKey: 'ad_date', type: 'date'},
        'ad_price': {sortKey: 'ad_price', type: 'num'},
        'site_visit': {sortKey: 'site_visit', type: 'num'},
        'empty_mail': {sortKey: 'empty_mail', type: 'num'},
        'form_send': {sortKey: 'form_send', type: 'num'},
        'reg_member': {sortKey: 'reg_member', type: 'num'},
        'reg_line': {sortKey: 'reg_line', type: 'num'},
        'leave_member': {sortKey: 'leave_member', type: 'num'},
        'ad_sales': {sortKey: 'ad_sales', type: 'num'},
        'recov_rate': {sortKey: 'recov_rate', type: 'num'}
      }
    }
  },
  computed: {

  },
  methods: {
    rateFormatter(row, column) {
      if (!row.price) {
        return '-'
      }
      return (row.recov_rate) + '%'
    },
    selectCancel() {
      this.$refs.uhTbl.setCurrentRow()
    },
    onFilter(val) {
      if (val === '') {
        this.filters = []
      } else {
        this.filters = [{field: 'ad_code', stringFilter: String(val)}]
      }
    }
  }
}
</script>
<style scope>

</style>
