<template>
  <div>
    <span style="margin-right:2rem;">消費総ポイント：{{ listSumPoint }} pt</span>
    <span style="margin-right:2rem;">（有償Pt：{{ listSumPremium }} pt</span>
    <span style="margin-right:2rem;">ギフト：{{ listSumGift }} pt）</span>
    <hr>

    <el-row>
      <el-col :span="3">
        件数：{{ gridData.length }}
      </el-col>

      <el-col :span="15" class="left-border">
        <el-pagination style="text-align:center;" layout="prev, pager, next" :total="total" :page-size="perPage" :current-page="currentPage" @current-change="onPageChange">
        </el-pagination>
      </el-col>

      <el-col :span="6" class="left-border">
        <el-button size="small" :disabled="!auth.SalesCb" @click="onCbCopy">クリップボードコピー</el-button>
        <el-button size="small" :disabled="!auth.SalesCb" @click="onDownload">ダウンロード</el-button>
      </el-col>
    </el-row>

    <el-table ref="salesTbl" :data="listItem" border style="width: 100%;margin-top:5px" :highlight-current-row="true" @row-click="" @sort-change="onSort">

      <el-table-column prop="created_at" width="170" sortable align="center" label="利用日時">
      </el-table-column>
      <el-table-column prop="mid" width="100" align="right" sortable label="会員No">
      </el-table-column>
      <el-table-column prop="uid" width="110" align="left" sortable label="UID">
      </el-table-column>
      <el-table-column prop="oid" width="100" align="right" sortable label="事由ID">
      </el-table-column>
      <el-table-column prop="item_name" :formatter="itemFormatter" width="320" align="left" sortable label="適用">
      </el-table-column>

      <el-table-column prop="point" :formatter="numberFormatter" sortable width="110" headerAlign="center" align="right" label="必要Pt">
      </el-table-column>

      <el-table-column prop="premium_point" :formatter="numberFormatter" width="120" headerAlign="center" align="right" sortable label="有償Pt">
      </el-table-column>
      <el-table-column prop="gift_point" :formatter="numberFormatter" width="100" headerAlign="center" align="right" sortable label="ギフト">
      </el-table-column>

      <el-table-column prop="ppb" :formatter="numberFormatter" width="120" headerAlign="center" align="right" sortable label="有償残高">
      </el-table-column>
      <el-table-column prop="gpb" :formatter="numberFormatter" width="120" headerAlign="center" align="right" sortable label="ギフト残高">
      </el-table-column>

    </el-table>
  </div>
</template>

<script>
import AppVue from "../../mixins/AppVue.js"
import TableWork from "../../mixins/TableWork.js"
import * as Define from '../../js/Define'
import Common from '../../js/Common'

export default {
  mixins: [AppVue, TableWork],
  computed: {},
  methods: {
    logined() {
      this.setAuth('sales')
      this.onListItemListed = this.onListed
    },
    selectCancel() {
      this.$refs.userTbl.setCurrentRow()
    },

    // -------------------------------------------------------------------------------
    // フィルタソート結果を計上
    onListed(list) {
      let sumAmount = 0;
      let sumPremium = 0;
      let sumGift = 0;
      for (let i = 0; i < this.gridData.length; i++) {
        const item = this.gridData[i];
        sumAmount += item.amount;
        sumPremium += item.premium_point;
        sumGift += item.gift_point;
      }
      this.listSumPoint = Common.numberFormat(sumPremium + sumGift);
      this.listSumPremium = Common.numberFormat(sumPremium);
      this.listSumGift = Common.numberFormat(sumGift);
    },

    //--------------------------------------------------------------
    // フォーマッター
    itemFormatter(row, column) {
      if (row.kind === 30) {
        return '失効'
      } else if (row.category === 'content') {
        const content = '[記事ID ' + row.rid + '] '
        if (this.identifiers[row.identifier]) {
          return content + this.identifiers[row.identifier];
        }
        return content + row.identifier.replace('mark_n_', '印-');

      } else if (row.category === 'uma_comment') {
        const uc = '[uc:' + row.rid + '] ';

        if (row.identifier === 'spoiler') {
          return uc + 'スポイラー ' + row.registration
        }
        if (row.identifier === 'chat_comment') {
          return uc + 'チャットコメント ' + (row.registration ? row.registration : row.race_code)
        }
      }
    },


    //--------------------------------------------------------------
    // クリップボードコピー

    onCbCopy() {
      this.copyToClipboard(this.generateData())
      this.copyToClipboard(text)
    },
    // ダウンロード
    onDownload() {
      this.downloadContent('point_spend.tsv', this.generateData())
    },

    generateData() {
      let texts = []
      texts.push('利用日時\t会員No\tUID\t事由ID\t適用\t必要Pt\t有償Pt\tギフト\t有償残高\tギフト残高')
      for (let i = 0; i < this.gridData.length; i++) {
        let line = []
        const item = this.gridData[i];

        line.push(this.qrt(item.created_at))
        line.push(item.mid)
        line.push(item.uid)
        line.push(item.oid)

        line.push(this.qrt(this.itemFormatter(item, 0)))
        line.push(item.point)
        line.push(item.premium_point)
        line.push(item.gift_point)

        line.push(item.ppb)
        line.push(item.gpb)

        texts.push(line.join('\t'))
      }
      return texts.join('\n')
    },


  },


  props: ['gridData', 'openDialogFunc', 'identifiers',],
  data:
      function () {
        return {
          isIndeterminate: true,
          listSumPoint: 0,
          listSumPremium: 0,
          listSumGift: 0,

          perPage: 30,
          checkAll: true,
          filters: [],

          filterData: [],
          sort: null,
          sortDesc: true,
          sortTypes: {
            'created_at': {type: 'str'},
            'item_name': {
              type: (a, b) => {
                return this.itemFormatter(a).localeCompare(this.itemFormatter(b))
              }
            },
          }
        }
      }
}
</script>
<style scope>

</style>

