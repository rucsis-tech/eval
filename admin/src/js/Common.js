import {ElLoading} from 'element-plus'
import * as define from './Define'
import {APP_NAME} from "./Define";

let loadingInstance
export const Define = define;
export default {
    // mail_status の種類
    mailDisables: [
        {value: 0, label: "配信可"},
        {value: 1, label: "停止処理"},
        {value: 2, label: "ユーザー不明"},
        {value: 3, label: "受信拒否"},
        {value: 4, label: "ドメイン不明"},
        {value: 5, label: "タイムアウト"},
        {value: 99, label: "その他のエラー"},
    ],
    dateFormat(date, format) {
        date = new Date(date)
        if (!date || date === 'Thu Jan 01 1970 09:00:00 GMT+0900 (日本標準時)') return null

        if (!format) format = 'YYYY-MM-DD hh:mm:ss'
        format = format.replace(/YYYY/g, date.getFullYear())
        format = format.replace(/MM/g, ('0' + (date.getMonth() + 1)).slice(-2))
        format = format.replace(/DD/g, ('0' + date.getDate()).slice(-2))
        format = format.replace(/hh/g, ('0' + date.getHours()).slice(-2))
        format = format.replace(/mm/g, ('0' + date.getMinutes()).slice(-2))
        format = format.replace(/ss/g, ('0' + date.getSeconds()).slice(-2))
        if (format.match(/S/g)) {
            var milliSeconds = ('00' + date.getMilliseconds()).slice(-3)
            var length = format.match(/S/g).length
            for (var i = 0; i < length; i++) format = format.replace(/S/, milliSeconds.substring(i, i + 1))
        }
        if (format.indexOf('00:00:00') > -1) format = format.substring(0, 10)


        return format
    },
    wDayFormat(date) {
        var WeekChars = ['日', '月', '火', '水', '木', '金', '土']
        var dateObj = new Date(Date.parse(date))
        return this.dateFormat(date, 'YYYY-MM-DD') + '(' + WeekChars[dateObj.getDay()] + ')'
    },
    numberFormat(number) {
        return String(number).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, '$1,')
    },
    getToday() {
        var today = new Date()
        return new Date(today.getFullYear() + '/' + (today.getMonth() + 1) + '/' + today.getDate() + ' 00:00:00')
    },
    getLastWeekDay() {
        var lastWeekDay = new Date(new Date().getTime() - 1000 * 60 * 60 * 24 * 7)
        return new Date(lastWeekDay.getFullYear() + '/' + (lastWeekDay.getMonth() + 1) + '/' + lastWeekDay.getDate())
    },
    isType(type, obj) {
        var clas = Object.prototype.toString.call(obj).slice(8, -1)
        return obj !== undefined && obj !== null && clas === type
    },
    // 値の設定判定
    isset(value) {
        if (value === 0) return true;
        if (value === false) return true;
        if (value === '') return true;
        if (value === null) return true;
        return value;
    },
    createCheckList(count) {
        let cArr = []
        for (let i = 0; i < count; i++) {
            cArr.push(i + 1)
        }
        return cArr
    },

    loadStart() {
        loadingInstance = ElLoading.service({fullscreen: true})
    },
    loadStop() {
        if (loadingInstance) {
            loadingInstance.close()
        }
    },
    mailCheck(mail) {
        if (!mail.match(/.+@.+\..+/)) {
            return false
        }
        return true
    },
    lpad(str, len, chr) {
        if (!chr || chr.length === 0) {
            return str
        } else if (chr.length > 1) {
            chr = chr.charAt(0)
        }
        for (; str.length < len; str = chr + str) ;
        return str
    },

    getItem(key, defaultValue) {
        key = Define.APP_NAME + '-' + key;
        let json = window.localStorage.getItem(key);
        try {
            let value = JSON.parse(json)
            if (value.value) {
                return value.value
            } else {
                return defaultValue;
            }
        } catch (e) {
            return defaultValue;
        }

    },
    setItem(key, item) {
        key = Define.APP_NAME + '-' + key;
        item = {value: item}
        let json = JSON.stringify(item);
        window.localStorage.setItem(key, json);

    },

    // キーでソート
    sortObjectKey(obj) {
        const sortedKeys = Object.keys(obj).sort();

        // ソートされたキーを使って新しい連想配列を作成
        const sortedObj = {};
        for (const key of sortedKeys) {
            sortedObj[key] = obj[key];
        }
        return sortedObj;
    },
    arrayObjectKey(obj) {
        const sortedKeys = Object.keys(obj).sort();
        // ソートされたキーを使って新しい連想配列を作成
        const sortedArray = [];
        let index = 0;
        for (const key of sortedKeys) {
            obj[key].index = index++;
            sortedArray.push(obj[key]);
        }
        //console.log(sortedArray);
        return sortedArray;
    },

}
