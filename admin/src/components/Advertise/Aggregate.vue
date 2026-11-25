<template>
  <h3>広告集計</h3>
  <div class="contents">
    <aggregate-table ref="adAggTbl" v-loading.body="loading" :gridData="adAggData" :loadFunction="loadAggregate" :auth="auth">
    </aggregate-table>
  </div>
</template>

<script>

import AggregateTable from './AggregateTable.vue'
import * as Define from '../../js/Define'
import Common from '../../js/Common'
import AppVue from "../../mixins/AppVue.js";

export default {
  name: 'aggregate',
  mixins: [AppVue],
  components: {
    'aggregate-table': AggregateTable
  },
  data() {
    return {
      auth: this.$store.state.advertiseDivision,
      loading: false,
      adAggData: []
    }
  },
  methods: {
    logined() {
      this.setAuth('advertise')
      this.loadAggregate()
    },
    loadAggregate(term = 0) {
      this.adAggData = []
      var data = new FormData()
      if (!this.$refs.adAggTbl) {
        term = 0
      } else {
        term = this.$refs.adAggTbl.term
        data.append('since', Common.dateFormat(this.$refs.adAggTbl.since, 'YYYY-MM-DD'))
        data.append('until', Common.dateFormat(this.$refs.adAggTbl.until, 'YYYY-MM-DD'))
      }
      data.append('term', term)
      this.post(Define.API_AD_AGGREGATE, data).then(this.aggregateRes)
    },
    aggregateRes: function (response) {

      if (response.list.length === 0) {
        this.message('該当期間の広告コードはありません')
      } else {
        this.adAggData = response.list
        this.adAggData.forEach(function (item, i) {
          item.site_visit = +item.site_visit
          item.empty_mail = +item.empty_mail
          item.form_send = +item.form_send
          item.reg_member = +item.reg_member
          item.leave_member = +item.leave_member
          if (item.recov_rate === undefined) {
            item.recov_rate = 0
          }
        })
      }
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

</style>
