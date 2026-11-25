<template>
  <div class="bg-gray" style="padding:0 0 10px 0">
    <el-row>
      <el-col>
        <el-menu theme="dark" :default-active="activeIndex" mode="horizontal" @select="handleSelect" style="padding:0 5px;">

          <el-sub-menu index="2">
            <template #title>会員管理</template>
            <el-menu-item index="search" v-if="divisions.member.MemberExec">会員検索</el-menu-item>
            <el-menu-item index="narrow" v-if="divisions.narrow.NarrowExec">会員絞込み</el-menu-item>
            <el-menu-item index="member" v-if="divisions.member.ListExec">全会員一覧</el-menu-item>
            <el-menu-item index="export" v-if="divisions.member.Export">エクスポート</el-menu-item>
          </el-sub-menu>

          <el-sub-menu index="4" v-if="divisions.payment.PaymentItem">
            <template #title>商品管理</template>
            <el-menu-item index="shop/point" v-if="divisions.payment.PaymentItem">ポイント商品</el-menu-item>
            <el-menu-item index="shop/subscription" v-if="divisions.payment.PaymentItem">サブスクリプション</el-menu-item>
          </el-sub-menu>

          <el-sub-menu index="5" v-if="divisions.sales.Sales || divisions.sales.Approval">
            <template #title>売上管理</template>
            <el-menu-item index="sales" v-if="divisions.sales.Sales">売上・取引履歴</el-menu-item>
            <el-menu-item index="subscription_history" v-if="divisions.sales.Subscription">サブスク履歴</el-menu-item>
            <el-menu-item index="kpi" v-if="divisions.sales.Sales">KPI</el-menu-item>
            <hr>
            <el-menu-item index="point/purchased" v-if="divisions.sales.PointPurchased">ポイント購入・付与履歴</el-menu-item>
            <el-menu-item index="point/spend" v-if="divisions.sales.PointSpend">ポイント消費履歴</el-menu-item>
            <!--el-menu-item index="point/incremental" v-if="divisions.sales.PointHistory">ポイント増減集計</el-menu-item-->
          </el-sub-menu>

          <el-sub-menu index="6" v-if="divisions.advertise.Advertise ||  divisions.advertise.AdAggregate">
            <template #title>広告管理</template>
            <el-menu-item index="advertise" v-if="divisions.advertise.Advertise">広告コード一覧</el-menu-item>
            <el-menu-item index="aggregate" v-if="divisions.advertise.Aggregate">広告集計</el-menu-item>
          </el-sub-menu>

          <el-sub-menu index="9" v-if="divisions.distribute.Distribute || divisions.mail.Mail || divisions.distribute.Banner">
            <template #title>配信管理</template>
            <el-menu-item index="mail" v-if="divisions.mail.Mail">メール管理</el-menu-item>
            <el-menu-item index="distribute" v-if="divisions.distribute.Distribute">メール配信予約</el-menu-item>
          </el-sub-menu>

          <el-sub-menu index="10">
            <template #title>管理者</template>
            <el-menu-item index="top">ログイン情報</el-menu-item>
            <el-menu-item index="user" v-if="divisions.user.UserList">管理者一覧</el-menu-item>
            <el-menu-item index="userHistory" v-if="divisions.user.UserHistory">操作履歴</el-menu-item>
          </el-sub-menu>


        </el-menu>

        <router-view ref="view"></router-view>

      </el-col>
    </el-row>
  </div>

</template>
<script>

import * as Define from '../src/js/Define.js'
import AppVue from "../src/mixins/AppVue.js";

export default {
  name: 'systems',
  mixins: [AppVue],
  data() {
    return {
      activeIndex: 'systems',
      searchData: null,
      globalIdList: [],
      divisions: this.$store.state.divisions
    }
  },
  created() {
    if (this.isLogin()) {
      this.onLogin()
    } else {
      this.session()
    }
  },

  methods: {
    logined() {
      this.divisions = this.$store.state.divisions
    },
    session() {
      this.post(Define.API_SESSION, {}).then(this.loginRes).catch(() => {
        this.$router.replace({path: '/'})
      })
    },

    handleSelect(key, keyPath) {
      let divisions = this.$store.state.divisions
      switch (key) {
        case 'search':
          if (!divisions.member.MemberExec) {
            this.message('会員検索権限が付与されていません')
            return
          }
          break
        case 'bundle':
          if (!divisions.member.Bundle) {
            this.message('一括処理権限が付与されていません')
            return
          }
          break
        case 'ticketBundle':
          if (!divisions.member.TicketBundle) {
            this.message('コインの一括処理権限が付与されていません')
            return
          }
          break
        case 'licenseBundle':
          if (!divisions.member.Bundle) {
            this.message('一括処理権限が付与されていません')
            return
          }
          break
        case 'userHistoryMember':
          if (!divisions.member.MemberUH) {
            this.message('会員管理利用履歴の閲覧権限が付与されていません')
            return
          }
          break
        case 'narrow':
          if (!divisions.narrow.NarrowExec) {
            this.message('会員絞込み権限が付与されていません')
            return
          }
          break
        case 'userHistoryNarrow':
          if (!divisions.narrow.NarrowUH) {
            this.message('会員管理利用履歴の閲覧権限が付与されていません')
            return
          }
          break
        case 'coin':
          if (!divisions.item.ItemCube) {
            this.message('コイン商品の閲覧権限が付与されていません')
            return
          }
          break
        case 'ticket':
          if (!divisions.item.TicketItem) {
            this.message('チケット商品の閲覧権限が付与されていません')
            return
          }
          break
        case 'userHistoryItem':
          if (!divisions.item.ItemUH) {
            this.message('商品利用履歴の閲覧権限が付与されていません')
            return
          }
          break
        case 'sales':
          if (!divisions.sales.Sales) {
            this.message('売上一覧閲覧権限が付与されていません')
            return
          }
          break
        case 'coinHistory':
          if (!divisions.sales.Coin) {
            this.message('コイン履歴閲覧権限が付与されていません')
            return
          }
          break
        case 'ticketHistory':
          if (!divisions.sales.Coin) {
            this.message('チケット履歴閲覧権限が付与されていません')
            return
          }
          break
        case 'userHistorySales':
          if (!divisions.sales.SalesUH) {
            this.message('会員管理利用履歴の閲覧権限が付与されていません')
            return
          }
          break
        case 'advertise':
          if (!divisions.advertise.Advertise) {
            this.message('広告一覧閲覧権限が付与されていません')
            return
          }
          break
        case 'adAggregate':
          if (!divisions.advertise.AdAggregate) {
            this.message('広告集計閲覧権限が付与されていません')
            return
          }
          break
        case 'userHistoryAdvertise':
          if (!divisions.advertise.AdvertiseUH) {
            this.message('広告管理利用履歴の閲覧権限が付与されていません')
            return
          }
          break
        case 'kpi':
          if (!divisions.marketing.Kpi) {
            this.message('KPI閲覧権限が付与されていません')
            return
          }
          break
        case 'license':
          if (!divisions.marketing.License) {
            this.message('ライセンス集計権限が付与されていません')
            return
          }
          break
        case 'userHistoryMarketing':
          if (!divisions.marketing.MarketingUH) {
            this.message('マーケティング利用履歴の閲覧権限が付与されていません')
            return
          }
          break
        case 'mail':
          if (!divisions.mail.Mail) {
            this.message('メール一覧権限が付与されていません')
            return
          }
          break
        case 'userHistoryMail':
          if (!divisions.mail.MailUH) {
            this.message('メール管理利用履歴の閲覧権限が付与されていません')
            return
          }
          break
        case 'distribute':
          if (!divisions.distribute.Distribute) {
            this.message('配信予約権限が付与されていません')
            return
          }
          break
        case 'userHistoryDistribute':
          if (!divisions.distribute.DistributeUH) {
            this.message('配信管理利用履歴の閲覧権限が付与されていません')
            return
          }
          break
        case 'user':
          if (!divisions.user.UserList) {
            this.message('ユーザー一覧閲覧権限が付与されていません')
            return
          }
          break
        case 'userHistoryALL':
          if (!divisions.user.UserHistory) {
            this.message('会員管理利用履歴の閲覧権限が付与されていません')
            return
          }
          break
      }

      // ナビゲーションからのページ遷移
      this.$router.replace({path: '/' + key, props: 'true'})
    },

  },


}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
</style>
