import axios from "axios";
import Common from "../js/Common.js";

export default {
    data() {
        return {
            auth: {}
        }
    },
    computed: {
        userInfo() {
            return this.$store.state.userInfo
        },
        loginToken() {
            return this.$store.state.loginToken
        }
    },
    watch: {
        loginToken: function () {
            this.onLogin()
        },
    },
    created() {
        this.onLogin()
    },
    methods: {
        // ログイン判定
        isLogin: function () {
            return this.$store.state.loginToken
        },
        onLogin: function () {
            if (this.logined && this.isLogin()) {
                this.logined()
            }
        },
        setAuth: function (auth) {
            this.auth = this.$store.state.divisions[auth];
        },
        //--------------------------------------------------------------------
        // 通信

        post(url, data) {
            const API_BASE_URL = this.$store.state.API_BASE_URL;
            const objectName = Object.prototype.toString.apply(data);
            if (!data) {
                data = {}
            }
            if (objectName === '[object FormData]') {
                data.append('token', this.getToken());
            } else {
                data.token = this.getToken();
            }

            Common.loadStart()

            return this.axios.post(API_BASE_URL + url, data).then((result) => {
                Common.loadStop()

                if (typeof (result.data) === 'string' || result.data.length === 0) {
                    throw new Error('通信に失敗しました:' + result.data);
                } else if (result.data.status !== 'OK') {
                    throw new Error(result.data.message);
                } else {
                    return result.data.response;
                }
            }).catch((e) => {
                Common.loadStop()
                console.log(e.message)
                const title = e.message.split("\n")[0]
                this.alert(e.message, '', null, title);
                throw new Error(e);
            })
        },

        getToken() {
            const data = sessionStorage.getItem('token');
            if (data == null) return '';
            return data;
        },
        setToken(token) {
            sessionStorage.setItem('token', token);
        },

        /**
         * ログイン時処理
         * @param response
         */
        loginRes: function (response) {
            this.setToken(response.token)
            this.$store.commit('setLogin', response)
        },
        //--------------------------------------------------------------------
        // チェックボックス用の１００コの配列生成
        getChecks() {
            const list = [];
            for (let i = 0; i < this.$store.state.consts.flags.count; i++) {
                list.push(false)
            }
            return list;
        },
        // チェックフラグを収集して文字列作成
        makeCheckData(cGroup, cbxList) {
            let checkData = []
            let checks = '';
            cbxList.forEach(function (item, i) {
                if (cGroup.indexOf(item) !== -1) {
                    checks = '1' + checks
                } else {
                    checks = '0' + checks
                }
                if (checks.length == 32) {
                    checkData.push(checks)
                    checks = '';
                }
            })
            return checkData;
        },


        //--------------------------------------------------------------------
        // メッセージダイアログ
        message(message, type) {
            if (!type) {
                type = 'warning'
            }
            this.$message({
                message: message,
                type: type,
                dangerouslyUseHTMLString: true,
                duration: 1500
            })
        },
        alert(message, type, callback, title) {
            if (!type) {
                type = 'error'
            }
            if (!callback) {
                callback = null
            }
            if (!title) {
                title = 'ALERT'
            }
            this.$alert(message, title, {
                confirmButtonText: 'OK',
                type: type,
                callback: action => callback
            })
        },
        confirm(title, message, type, callback) {
            if (!type) {
                type = 'info'
            }
            if (!callback) {
                callback = null
            }
            this.$confirm(message, title, {
                confirmButtonText: 'OK',
                type: type
            }).then(
                callback
            ).catch((e) => {
                //alert(e.message)
            })
        },

        //---------------------------------------------------------------------
        //

        onClickExport() {
            this.message('ダウンロードを開始します')
            this.waiting = 0;
        },

        //---------------------------------------------------------------------
        // クリップボードコピー

        copyToClipboard(text) {
            const clipboard = document.createElement('textarea');
            clipboard.value = text;
            document.getElementById('app').appendChild(clipboard);
            clipboard.select();
            document.execCommand('copy');
            document.getElementById('app').removeChild(clipboard);

            this.message('一覧をクリップボードにコピーしました', 'success')

        },
        //---------------------------------------------------------------------
        // ダウンロード

        downloadContent(filename, text) {
            var bom = new Uint8Array([0xEF, 0xBB, 0xBF]);
            var blob = new Blob([bom, text], {"type": "text/csv"});

            /*
            if (window.navigator.msSaveBlob) {
                window.navigator.msSaveBlob(blob, filename);

                // msSaveOrOpenBlobの場合はファイルを保存せずに開ける
                window.navigator.msSaveOrOpenBlob(blob, filename);
            } else {
                element.href = window.URL.createObjectURL(blob);
            }
            */


            var element = document.createElement('a');
            element.setAttribute('href', window.URL.createObjectURL(blob));
            element.setAttribute('download', filename);
            document.body.appendChild(element);
            element.click();


        }


    },
}