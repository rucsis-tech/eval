<template>
  <div class="contents">
    <license-table ref="licenseTbl"
      v-loading.body="loading"
      :gridData="licenseData"
      :loadFunction="loadLicense"
      :auth="auth">
    </license-table>
  </div>
</template>

<script>

import LicenseTable from './LicenseTable.vue'
import * as Define from '../../js/Define'
import Common from '../../js/Common'

export default {
  name: 'license',
  components: {
    'license-table': LicenseTable
  },
  data () {
    return {
      auth: this.$store.state.marketingDivision,
      loading: false,
      licenseData: []
    }
  },
  created () {
    this.loadLicense()
  },
  methods: {
    loadLicense (term = 0) {
      this.loading = true
      this.licenseData = []
      var data = new FormData()
      if (!this.$refs.licenseTbl) {
        term = 0
        data.append('date', Common.dateFormat(Common.getToday(), 'YYYY-MM-DD'))
      } else {
        term = this.$refs.licenseTbl.term
        data.append('date', Common.dateFormat(this.$refs.licenseTbl.date, 'YYYY-MM-DD'))
        this.$refs.licenseTbl.dateFilter = ''
        this.$refs.licenseTbl.placeFilter = ''
        this.$refs.licenseTbl.cateFilter = ''
      }
      data.append('term', term)
      data.append('type', 1)
      this.post(Define.API_LICENSE, data).then(this.loadLicenseRes)
    },
    loadLicenseRes: function (response) {
      if (typeof (response.data) === 'string') {
        this.alert( 'ライセンス集計の取得に失敗しました。' + response.data)
      } else {
        if (response.data.length === 0) {
          this.message('商品がありません')
        } else {
          this.licenseData = response.data
          this.$refs.licenseTbl.createFilters()
        }
      }
      this.loading = false
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

</style>
