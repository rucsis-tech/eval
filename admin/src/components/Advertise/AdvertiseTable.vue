<template>
  <div>
    <el-row>

      <el-col :span="4">
        <el-button @click="loadFunction" style="margin-left:20px;">再読み込み</el-button>
      </el-col>
      <el-col :span="10" style="margin-top:5px;">
        広告検索フィルタ
        <el-input v-model="adSearch" style="width:200px" @change="adFilter"></el-input>
      </el-col>

    </el-row>

    <el-table ref="advertiseTbl" :data="listItem" border style="width: 100%;margin-top:10px;" :highlight-current-row="true" @row-click="changeFunction" @sort-change="onSort" height="243">

      <el-table-column prop="ad_code" width="100" sortable label="コード">
      </el-table-column>
      <el-table-column prop="name" width="350" sortable label="名称">
      </el-table-column>
      <el-table-column prop="type" width="100" sortable align="center" label="種別">
      </el-table-column>

      <el-table-column prop="ad_date" width="150" sortable align="center" label="掲載日">
      </el-table-column>
      <el-table-column prop="price" width="200" sortable header-align="center" align="right" :formatter="numberFormatter" label="掲載料金">
      </el-table-column>
      <el-table-column prop="notes" width="500" sortable header-align="center" align="left"  label="備考">
      </el-table-column>

      <!--el-table-column width="200" property="notes" label="備考" header-align="center" align="center">
        <template #default="scope">
          <el-button v-if="scope.row.ad_notes !== ''" v-popover:notePop style="color:#4169e1;font-weight:bold" type="primary">
            備考
            <el-popover ref="notePop" placement="bottom" trigger="click">
              <div style="font-size:14px;">
                {{ scope.row.ad_notes }}
              </div>
            </el-popover>
          </el-button>
          <span v-else>-</span>
        </template>
      </el-table-column-->

      <!--el-table-column prop="user_name" align="center" label="更新情報">
        <template #default="scope">
          <el-button v-popover:updateInfo style="color:#4169e1;font-weight:bold" type="primary">
            更新情報
            <el-popover ref="updateInfo" placement="bottom" width="180" trigger="click">
              <el-row>
                <el-col :span="8">作成日時</el-col>
                <el-col :span="16" class="right">{{ scope.row.create_date }}</el-col>
              </el-row>
              <el-row>
                <el-col :span="8">作成者</el-col>
                <el-col :span="16" class="right">{{ scope.row.cuser_name }}</el-col>
              </el-row>
              <el-row>
                <el-col :span="8">更新日時</el-col>
                <el-col :span="16" class="right">{{ scope.row.update_date }}</el-col>
              </el-row>
              <el-row>
                <el-col :span="8">更新者</el-col>
                <el-col :span="16" class="right">{{ scope.row.uuser_name }}</el-col>
              </el-row>
            </el-popover>
          </el-button>
        </template>
      </el-table-column-->

    </el-table>

    <el-pagination style="text-align:center;margin-top:10px;" layout="prev, pager, next" :total="total" :page-size="perPage" :current-page="currentPage" @current-change="onPageChange">
    </el-pagination>
  </div>
</template>

<script>
import Common from '../../js/Common'
import TableWork from "../../mixins/TableWork.js";
import AppVue from "../../mixins/AppVue.js";

export default {
  props: ['gridData', 'loadFunction', 'changeFunction', 'auth'],
  mixins: [TableWork],
  data: function () {
    return {
      currentPage: 1,
      perPage: 5,
      total: 0,
      filters: [],
      sort: null,
      sortDesc: true,
      sortTypes: {
        'ad_code': {sortKey: 'ad_code', type: 'num'},
        'name': {sortKey: 'name', type: 'str'},
        'type': {sortKey: 'type', type: 'str'},
        'ad_date': {sortKey: 'ad_date', type: 'date'},
        'price': {sortKey: 'price', type: 'num'}
      },
      adSearch: ''
    }
  },
  computed: {

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

    selectCancel() {
      this.$refs.advertiseTbl.setCurrentRow()
    },
    adFilter(val) {
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
