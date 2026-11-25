<template>
  <div>
    <el-table ref="salesTbl"
      :data="listItem"
      border
      style="width: 100%;margin-top:5px"
      :highlight-current-row="true"
      @row-click="questionFunc"
      @sort-change="onSort">
      <el-table-column
        prop="question_num"
        width="90"
        sortable
        align="center"
        label="No.">
      </el-table-column>
      <el-table-column
        prop="title"
        width="354"
        header-align="center"
        align="left"
        label="タイトル">
        <template #default="scope">
          <el-button
            v-popover:name
            style="color:#4169e1;font-weight:bold"
            type="text">
            {{scope.row.title}}
            <el-popover
              ref="name"
              placement="top"
              trigger="hover">
              <slot name="content">
                <span style="font-size:14px;">{{scope.row.title}}</span>
              </slot>
            </el-popover>
          </el-button>
        </template>
      </el-table-column>
      <el-table-column
        prop="choice_cnt"
        width="115"
        sortable
        align="center"
        label="選択肢数">
      </el-table-column>
      <el-table-column
        prop="pay_coin"
        width="110"
        headerAlign="center"
        align="center"
        sortable
        label="報酬">
        <template #default="scope">
          <span>{{scope.row.pay_coin}}コイン</span>
        </template>
      </el-table-column>
      <el-table-column
        prop="end_date"
        width="350"
        align="center"
        sortable
        label="アンケート期間">
        <template #default="scope">
          <span>{{scope.row.start_date.slice(0, 16)}} ～ {{scope.row.end_date.slice(0, 16)}}</span>
        </template>
      </el-table-column>

      <el-table-column
        prop="multi_flag"
        width="120"
        header-align="center"
        align="center"
        label="複数選択">
        <template #default="scope">
          <span v-if="scope.row.multi_flag === '0'">-</span>
          <span v-else>〇</span>
        </template>
      </el-table-column>

      <el-table-column
        prop="answer_cnt"
        width="120"
        header-align="center"
        align="center"
        sortable
        label="回答数">
      </el-table-column>
    </el-table>
    <el-pagination style="text-align:center;margin-top:10px;"
      layout="prev, pager, next"
      :total="total"
      :page-size="perPage"
      :current-page="currentPage"
      @current-change="onPageChange">
    </el-pagination>
  </div>
</template>
<script>
import Common from '../../js/Common'
// import * as Define from '../../js/Define'

export default {
  props: ['gridData', 'questionFunc'],
  data: function () {
    return {
      auth: this.$store.state.marketingDivision,
      currentPage: 1,
      perPage: 5,
      total: 0,
      sort: null,
      sortDesc: true,
      sortTypes: {
        'question_num': {sortKey: 'question_num', type: 'num'},
        'start_date': {sortKey: 'start_date', type: 'date'},
        'end_date': {sortKey: 'end_date', type: 'date'},
        'choice_cnt': {sortKey: 'choice_cnt', type: 'num'},
        'answer_cnt': {sortKey: 'answer_cnt', type: 'num'},
        'pay_coin': {sortKey: 'pay_coin', type: 'num'}
      }
    }
  },
  computed: {
    listItem: function () {
      var data = [].concat(this.gridData)
      // let total = this.total
      if (data) {
        const fix = v => {
          if (v instanceof Object) {
            return Object.keys(v).map(k => fix(v[k])).join(' ')
          }
          return String(v)
        }

        if (this.sort) {
          let sortTypes = this.sortTypes
          var field = this.sort
          var order = this.sortDesc ? 1 : -1
          data = data.sort((a, b) => {
            a = a[sortTypes[field].sortKey]
            b = b[sortTypes[field].sortKey]
            if (sortTypes[field].type === 'num') {
              if (!a) {
                a = 0
              } else {
                a = String(a).replace(/,/g, '')
                a = +a
              }
              if (!b) {
                b = 0
              } else {
                b = String(b).replace(/,/g, '')
                b = +b
              }
              return (a === b ? 0 : a < b ? 1 : -1) * order
            } else if (sortTypes[field].type === 'str') {
              if (!a) a = 'a'
              if (!b) b = 'a'
              const r = fix(a).localeCompare(fix(b))
              return !this.sortDesc ? r : r * -1
            } else if (sortTypes[field].type === 'date') {
              a = +Date.parse(a)
              b = +Date.parse(b)
              return (a === b ? 0 : a < b ? 1 : -1) * order
            } else {
              return 0
            }
          })
        }
        this.total = data.length
        this.filterData = data
        // Apply pagination
        if (this.perPage) {
          data = data.slice((this.currentPage - 1) * this.perPage, this.currentPage * this.perPage)
        }
      }
      return data
    }
  },
  methods: {
    onPageChange (val) {
      this.currentPage = val
    },
    onSort (sortObj) {
      if (!sortObj.order) {
        this.sortDesc = false
      } else if (sortObj.order === 'descending') {
        this.sortDesc = true
      } else if (sortObj.order === 'ascending') {
        this.sortDesc = false
      }
      this.sort = !sortObj.prop ? null : sortObj.prop
    },
    numberFormatter (row, column) {
      if (!row[column.property]) return ''
      return Common.numberFormat(row[column.property])
    },
    dateFormatter (row, column) {
      return Common.dateFormat(row[column.property], 'YYYY-MM-DD')
    },
    weekdateFormatter (date) {
      return Common.wDayFormat(date)
    }
  }
}
</script>
<style scope>

</style>
