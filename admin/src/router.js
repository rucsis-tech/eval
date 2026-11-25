import {createRouter, createWebHistory} from 'vue-router'
import Login from './components/User/Login.vue'
import AppSystems from './AppSystems.vue'


import Search from './components/Search/Search.vue'
import Detail from './components/Search/SearchDetail.vue'
import SearchSalesTable from './components/Search/SearchSalesTable.vue'
import SearchPointSpendTable from './components/Search/SearchPointSpendTable.vue'
import SearchPointPurchasedTable from './components/Search/SearchPointPurchasedTable.vue'
import SearchScoreTable from './components/Search/SearchScoreTable.vue'
import SearchListTable from './components/Search/SearchListTable.vue'


import Narrow from './components/Narrow/Narrow.vue'
import MemberList from './components/Member/MemberList.vue'
import MemberExport from './components/Member/MemberExport.vue'

import ShopPointItem from './components/Shop/PointItem.vue'
import ShopSubscriptionItem from './components/Shop/SubscriptionItem.vue'


import Sales from './components/Sales/Sales.vue'
import SubscriptionHistory from './components/Sales/SubscriptionHistory.vue'
import KPI from './components/Sales/KPI.vue'

import PointPurchased from './components/Point/Purchased.vue'
import PointSpend from './components/Point/Spend.vue'
import PointIncremental from './components/Point/Incremental.vue'


import Banner from './components/Distribute/Banner.vue'
import Topic from './components/Distribute/Topic.vue'
import Advertise from './components/Advertise/Advertise.vue'
import Aggregate from './components/Advertise/Aggregate.vue'

import Mail from './components/Distribute/Mail.vue'
import Distribute from './components/Distribute/Distribute.vue'

import Top from './components/User/Top.vue'
import User from './components/User/User.vue'
import UserHistory from './components/User/UserHistory.vue'


const routes = [
    {path: '/', component: Login},
    {
        path: '/systems',
        component: AppSystems,
        children: [

            // 会員管理
            {
                path: '/search', name: 'Search', component: Search,
                children: [
                    {path: '/search/', component: Detail},
                    {path: '/search/payment_history', component: SearchSalesTable},
                    {path: '/search/point_purchased', component: SearchPointPurchasedTable},
                    {path: '/search/point', component: SearchPointSpendTable},
                    {path: '/search/score', component: SearchScoreTable},
                    {path: '/search/list', component: SearchListTable}

                ]
            },
            {path: '/export', component: MemberExport},

            // 絞り込み
            {path: '/narrow', component: Narrow},
            {path: '/member', component: MemberList},

            // 商品管理
            {path: '/shop/point', component: ShopPointItem},
            {path: '/shop/subscription', component: ShopSubscriptionItem},

            //-----------------
            // 売上管理
            {path: '/sales', component: Sales},
            {path: '/subscription_history', component: SubscriptionHistory},
            {path: '/kpi', component: KPI},

            // ポイント管理
            {path: '/point/purchased', component: PointPurchased},
            {path: '/point/spend', component: PointSpend},
            {path: '/point/incremental', component: PointIncremental},


            // 広告管理
            {path: '/advertise', component: Advertise},
            {path: '/aggregate', component: Aggregate},


            // メール管理
            {path: '/mail', component: Mail},
            // 配信管理
            {path: '/distribute', component: Distribute},
            // バナー
            {path: '/banner', component: Banner},
            // トピック
            {path: '/topic', component: Topic},

            // ユーザー管理
            {path: '/top', component: Top},
            {path: '/user', component: User},
            {path: '/userHistory', component: UserHistory},
            /*
            {                path: '/bundle',                component: Bundle            },
            {                path: '/ticketBundle',                component: TicketBundle            },
            {                path: '/licenseBundle',                component: LicenseBundle            },
            {                path: '/ticket',                component: Ticket            },
            {                path: '/coinHistory',                component: CoinHistory            },
            {                path: '/ticketHistory',                component: TicketHistory            },
            {                path: '/kpi',                component: Kpi            },
            {                path: '/license',                component: License            },

            {                path: '/questionnaire',                component: Questionnaire            }

             */
        ]
    }

]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

export default router