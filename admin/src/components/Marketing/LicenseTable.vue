<template>
  <div style="margin-top:10px;">
    <el-row>
      <el-col :span="8">
        <el-date-picker
          v-model="date"
          type="date"
          placeholder="日付週"
          @change="loadFunction"
          :clearable="false"
          :picker-options="pickerOptions">
        </el-date-picker>
        <el-button @click="loadFunction" style="margin-left:20px;">再読み込み</el-button>
      </el-col>
      <el-col :span="4">
        <el-select v-model="dateFilter" placeholder="日付フィルター" @change="onFilter" style="width:150px">
            <el-option
              v-for="item in dateFilters"
              :key="item"
              :label="item"
              :value="item">
            </el-option>
          </el-select>
      </el-col>
      <el-col :span="4">
        <el-select v-model="placeFilter" placeholder="場所フィルター" @change="onFilter" style="width:150px">
            <el-option
              v-for="item in placeFilters"
              :key="item"
              :label="item"
              :value="item">
            </el-option>
          </el-select>
      </el-col>
      <el-col :span="4">
        <el-select v-model="cateFilter" placeholder="種別フィルター" @change="onFilter" style="width:150px">
            <el-option
              v-for="item in cateFilters"
              :key="item"
              :label="item"
              :value="item">
            </el-option>
          </el-select>
      </el-col>
    </el-row>

    <el-table ref="licenseTbl"
      :data="listItem"
      border
      style="width: 100%;margin-top:10px;"
      :highlight-current-row="true"
      :row-class-name="setRowStyle"
      height="650"
      @sort-change="onSort">
      <el-table-column
        prop="date"
        width="140"
        header-align="center"
        align="center"
        label="日付">
      </el-table-column>
      <el-table-column
        prop="place"
        width="70"
        header-align="center"
        align="center"
        label="場所">
      </el-table-column>
      <el-table-column
        prop="bango"
        width="60"
        align="center"
        header-align="center"
        :formatter="bangoFormatter"
        label="R">
      </el-table-column>
      <el-table-column
        prop="race_name"
        width="220"
        header-align="center"
        label="レース名">
        <template #default="scope">
          <span style="white-space: nowrap">
            {{scope.row.race_name}}
          </span>
        </template>
      </el-table-column>
      <el-table-column
        prop="category"
        width="95"
        align="center"
        :formatter="categoryFormatter"
        label="種別">
      </el-table-column>
      <el-table-column
        prop="price"
        width="110"
        align="center"
        :formatter="numberFormatter"
        sortable
        label="金額">
      </el-table-column>
      <el-table-column
        prop="type1"
        width="115"
        align="right"
        header-align="center"
        :formatter="numberFormatter"
        sortable
        label="コイン">
      </el-table-column>
      <el-table-column
        prop="type2"
        width="115"
        align="right"
        header-align="center"
        :formatter="numberFormatter"
        sortable
        label="チケット">
      </el-table-column>
      <el-table-column
        prop="type4"
        width="110"
        align="right"
        header-align="center"
        :formatter="numberFormatter"
        sortable
        label="ガチャ">
      </el-table-column>
      <el-table-column
        prop="sum"
        width="110"
        align="right"
        header-align="center"
        :formatter="numberFormatter"
        sortable
        label="合計">
      </el-table-column>
      <el-table-column
        prop="expect"
        align="right"
        header-align="center"
        :formatter="numberFormatter"
        sortable
        label="閲覧">
      </el-table-column>
    </el-table>
  </div>
</template>

<script>
import Common from '../../js/Common'
import * as Define from '../../js/Define'

export default {
  props: ['gridData', 'changeFunction', 'loadFunction', 'auth'],
  data: function () {
    return {
      dateFilters: [''],
      dateFilter: '',
      placeFilters: [''],
      placeFilter: '',
      cateFilters: Define.itemCategoryList,
      cateFilter: '',
      filters: [],
      sort: null,
      sortDesc: true,
      date: Common.dateFormat(Common.getToday(), 'YYYY-MM-DD'),
      pickerOptions: {
        shortcuts: [{
          text: '今日',
          onClick (picker) {
            picker.$emit('pick', new Date())
          }
        },
        {
          text: '先月',
          onClick (picker) {
            new Date().setTime(new Date().getTime() - 3600 * 1000 * 24 * 30)
            picker.$emit('pick', new Date())
          }
        },
        {
          text: '去年',
          onClick (picker) {
            new Date().setTime(new Date().getTime() - 3600 * 1000 * 24 * 365)
            picker.$emit('pick', new Date())
          }
        }]
      },
      perPageDetail: 10,
      currentPageDetail: 1,
      sortTypes: {
        'type1': {sortKey: 'type1', type: 'num'},
        'type2': {sortKey: 'type2', type: 'num'},
        'type3': {sortKey: 'type3', type: 'num'},
        'type4': {sortKey: 'type4', type: 'num'},
        'sum': {sortKey: 'sum', type: 'num'},
        'expect': {sortKey: 'expect', type: 'num'}
      }
    }
  },
  computed: {
    listItem: function () {
      this.createFilters()
      var data = [].concat(this.gridData)
      if (data) {
        const fix = v => {
          if (v instanceof Object) {
            return Object.keys(v).map(k => fix(v[k])).join(' ')
          }
          return String(v)
        }
        this.filters.forEach(function (fitem, i) {
          data = data.filter(function (item) {
            if (fitem.stringFilter) {
              if (item[fitem.field].indexOf(fitem.stringFilter) !== -1) {
                return true
              } else {
                return false
              }
            } else {
              if (item[fitem.field] === fitem.filter) {
                return true
              } else {
                return false
              }
            }
          })
        })

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
      }
      return data
    }
  },
  methods: {
    createFilters () {
      this.dateFilters = ['']
      this.placeFilters = ['']
      var df = this.dateFilters
      var pf = this.placeFilters
      this.gridData.forEach(function (item, i) {
        if (df.indexOf(item.date) === -1) {
          df.push(item.date)
        }
        if (pf.indexOf(item.place) === -1) {
          pf.push(item.place)
        }
      })
      this.dateFilters = df
      this.placeFilters = pf
    },
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
      if (!row[column.property]) {
        row[column.property] = 0
      }
      return Common.numberFormat(row[column.property])
    },
    bangoFormatter (row, index) {
      return row.bango + 'R'
    },
    categoryFormatter (row, index) {
      return Define.itemCategoryList[row.category]
    },
    selectCancel () {
      this.$refs.uhTbl.setCurrentRow()
    },
    onFilter (val) {
      this.filters = []
      if (this.dateFilter !== '') {
        this.filters.push({field: 'date', stringFilter: this.dateFilter})
      }
      if (this.placeFilter !== '') {
        this.filters.push({field: 'place', stringFilter: this.placeFilter})
      }
      console.log(this.cateFilters.indexOf(this.cateFilter))
      if (this.cateFilter !== '') {
        this.filters.push({field: 'category', filter: this.cateFilters.indexOf(this.cateFilter)})
      }
    },
    setRowStyle (row, index) {
      if (row.category === 1) {
        return 'teki'
      } else if (row.category === 2) {
        return 'hai'
      }
      return ''
    }
  }
}
</script>
<style>
.el-table .teki{
  background-color:#efffff;
}
.el-table .hai{
  background-color:#efffef;
}
</style>
