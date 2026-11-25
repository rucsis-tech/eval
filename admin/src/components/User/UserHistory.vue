<template>
  <div class="contents bottom-border">
    <h3>管理履歴</h3>
  </div>
  <div class="contents">
    <user-history-table ref="uhTbl" :gridData="historyList" :group="historyGroup" :loadFunction="loadUserHistory">
    </user-history-table>
    <br/>
    <br/>
  </div>
</template>

<script>
import UserHistoryTable from './UserHistory_Table.vue'
import Common, {Define} from '../../js/Common'
import AppVue from "../../mixins/AppVue.js";

export default {
  name: 'uhis',
  props: ['id'],
  mixins: [AppVue],
  components: {
    'user-history-table': UserHistoryTable
  },
  data() {
    return {
      historyList: [],
      historyGroup: 0,
      historyNum: 0
    }
  },

  methods: {
    logined() {
      this.setAuth('user')
    },
    loadUserHistory() {
      Common.loadStart()
      let group
      let hisNum
      switch (this.id) {
        case 'Member':
          group = HistoryDefine.UHGP_MEMBER
          hisNum = HistoryDefine.UHN_MEMBER_UH_LOAD
          break
        case 'Narrow':
          group = HistoryDefine.UHGP_NARROW
          hisNum = HistoryDefine.UHN_NARROW_UH_LOAD
          break
        case 'Item':
          group = HistoryDefine.UHGP_ITEM
          hisNum = HistoryDefine.UHN_ITEM_UH_LOAD
          break
        case 'Sales':
          group = HistoryDefine.UHGP_SALES
          hisNum = HistoryDefine.UHN_SALES_UH_LOAD
          break
        case 'Advertise':
          group = HistoryDefine.UHGP_ADVERTISE
          hisNum = HistoryDefine.UHN_ADVERTISE_UH_LOAD
          break
        case 'Marketing':
          group = HistoryDefine.UHGP_MARKETING
          hisNum = HistoryDefine.UHN_MARKETING_UH_LOAD
          break
        case 'Mail':
          group = HistoryDefine.UHGP_MAIL
          hisNum = HistoryDefine.UHN_MAIL_UH_LOAD
          break
        case 'Distribute':
          group = HistoryDefine.UHGP_DISTRIBUTE
          hisNum = HistoryDefine.UHN_MAIL_UH_LOAD
          break
        case 'ALL':
          group = HistoryDefine.UHGP_OTHER
          hisNum = HistoryDefine.UHN_USER_UH_LOAD
          break
      }
      this.historyGroup = group
      this.historyNum = hisNum
      const data = new FormData()
      data.append('group', group)
      data.append('user_name', this.$refs.uhTbl.userName)
      data.append('division', this.$refs.uhTbl.division)
      data.append('since', Common.dateFormat(this.$refs.uhTbl.since, 'YYYY-MM-DD'))
      data.append('until', Common.dateFormat(this.$refs.uhTbl.until, 'YYYY-MM-DD'))
      this.post(Define.API_USER_HISTORY, data).then((response) => {
        this.historyList = response.HistoryList
      })
    },

  },
  watch: {
    id: function (val) {
      console.log(val)
      this.loadUserHistory()
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

</style>
