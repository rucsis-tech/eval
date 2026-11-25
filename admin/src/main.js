import {createApp} from 'vue'
import App from './App.vue'
import router from './router'
import axios from 'axios'
import VueAxios from 'vue-axios'
import {createStore} from 'vuex'

import ElementPlus from 'element-plus'
import "element-plus/dist/index.css";
import * as ElementPlusIconsVue from '@element-plus/icons-vue'
import japanese from 'element-plus/dist/locale/ja'

// set first day of week to
import dayjs from 'dayjs'
dayjs.Ls.en.weekStart = 0

//import 'bootstrap/dist/css/bootstrap.css' // add
//import 'bootstrap-vue/dist/bootstrap-vue.css' // add

import './css/main.css'
import Common, {Define} from './js/Common'
import VueClipboard from 'vue-clipboard2'

const app = createApp(App);
app.use(router);
app.use(ElementPlus, {locale: japanese});
app.use(VueAxios, axios);
app.use(VueClipboard);
for (const [key, component] of Object.entries(ElementPlusIconsVue)) {
    app.component(key, component)
}

const store = createStore({
    state() {
        let api_base_default = document.location.href + 'admin/';
        api_base_default = api_base_default.replace('5300', '1080');

        return {
            version: '1.0.0',
            build: new Date(document.lastModified),

            // 本番
            API_BASE_URL: Common.getItem('API_BASE_URL', api_base_default),
            authDefine: Common.getItem('authDefine', {}),         // 権限一覧定義
            barStyle: 'background-color:#002060',
            userInfo: [],           // ログインユーザー情報
            loginToken: false,    // ログインフラグ 兼 TOKEN
            divisions: Common.getItem('divisions', {}),// 管理権限


            memberList: [],         // 認証IDリスト（反映に使用）
            searchConditions: null, // 会員検索条件の保存用配列
            narrowConditions: null, // 絞り込み条件の保存用配列

            consts: {flags: []},

        }
    },
    mutations: {

        setLogin(state, response) {

            if (response.consts) {
                console.log('SITE :', response.consts.build ?? '');
            }
            console.log('CACHE:', state.build.getTime() / 1000);


            if (response.authDefine) {
                state.authDefine = response.authDefine
                Common.setItem('authDefine', response.authDefine)
            }

            if (response.divisions) {
                state.divisions = response.divisions
                Common.setItem('divisions', response.divisions)
            }

            if (response.userInfo)
                state.userInfo = response.userInfo

            if (response.token)
                state.loginToken = response.token

            const title = document.getElementsByTagName('title').item(0);
            title.innerText = 'DEV-Eval管理システム';

            if (state.API_BASE_URL.indexOf('local') > -1) {
                state.barStyle = 'background-color:#444'
                title.innerText = 'LOCAL-Eval管理システム';
            }
            if (state.API_BASE_URL.indexOf('13.112.18.166') > -1) {
                state.barStyle = 'background-color:#282'
                title.innerText = 'Eval管理システム';
            }
            if (response.consts)
                state.consts = response.consts;


        },
        setMemberList(state, memberList) {
            memberList.sort(function (a, b) {
                return parseInt(a.uid) > parseInt(b.uid) ? 1 : -1
            })
            state.memberList = memberList
        },
        setSearchConditions(state, data) {
            state.searchConditions = data
        },
        setNarrowConditions(state, data) {
            state.narrowConditions = data
        },
        setAuthority(state, cate1, cate2) {
            state[cate1][cate2] = true
        }
    },

})
app.use(store);

app.mount('#app');
