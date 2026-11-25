import Common from "../../js/Common.js";
import * as Define from "../../js/Define.js";
import {API_M_DATA} from "../../js/Define.js";

export default {
    data() {
        return {
            target: null,
            condition: null,
        }
    },
    methods: {

        isset(value) {
            return Common.isset(value);
        },

        // 条件ステータスを選択
        onClickStatus(status) {
            if (this.target !== status) {
                this.target = status
                this.resetTarget()
            }
        },

        // 設定条件に登録追加する
        onClickRegister(add_key) {
            let status = null;
            if (add_key === 'check_flag') {
                status = this.getCheckFlagCondition();
            } else {
                status = this.status[add_key]
            }
            status.key = add_key
            this.addCondition(status);
        },
        getSelectorLabel(selector, value) {
            for (let i = 0; i < selector.length; i++) {
                if (selector[i].value === value) return selector[i].label
            }
            return '';
        },
        // 設定詳細テキスト取得
        setConditionText(condition) {

            if (condition.conditions) {
                for (let i = 0; i < condition.conditions.length; i++) this.setConditionText(condition.conditions[i])
            }


            const option = this.getOption(condition.key)
            const key = condition.key
            if (key === 'check_flag') {
                let line = ''
                if (condition.mode === 1) line = '<b>' + "チェックリスト(完全一致)</b>"
                if (condition.mode === 2) line = '<b>' + "チェックリスト(いずれかに一致)</b>"
                condition.text = '<div class="condition">' + line + '<p>' + this.getFlagConditionText(condition) + '</p></div>';
                return
            }

            let line = ''
            let values = ''
            if (condition.mode === 1) line = '<b>(一致)' + condition.name + "</b>"
            if (condition.mode === 2) line = '<b>(不一致)' + condition.name + "</b>"

            if (Common.isset(condition.term)) {
                if (condition.term[0] || condition.term[1]) {
                    values += "<p>期間: "
                    if (Common.isset(condition.term[0]) && condition.term[0]) values += Common.dateFormat(condition.term[0], condition.datetype == 'date' ? 'YYYY/MM/DD' : 'YYYY/MM/DD hh:mm')
                    values += " ～ "
                    if (Common.isset(condition.term[1]) && condition.term[1]) values += Common.dateFormat(condition.term[1], condition.datetype == 'date' ? 'YYYY/MM/DD' : 'YYYY/MM/DD hh:mm')
                    values += "</p>"
                }
            }

            if (Common.isset(condition.value)) {
                let val = condition.value;
                if (option.selector) {
                    let caption = option.selector.caption ?? '値'
                    val = this.getSelectorLabel(option.selector.list, val)
                    values += "<p>" + caption +": " + val + "</p>"
                } else {
                    values += "<p>値: " + val + "</p>"
                }
            }

            if (Common.isset(condition.sub)) {
                let val = condition.sub;
                if (option.sub_selector) {
                    let caption = option.sub_selector.caption ?? '値'
                    val = this.getSelectorLabel(option.sub_selector.list, val)
                    values += "<p>" + caption +": " + val + "</p>"
                } else {
                    values += "<p>値: " + val + "</p>"
                }
            }



            if (Common.isset(condition.min)) {
                if (condition.min !== null || condition.max !== null) {
                    console.log(condition.min, condition.max)
                    values += "<p>値: " + (condition.min !== null ? condition.min : '') + " ～ " + (condition.max !== null ? condition.max : '') + "</p>"
                }
            }



            if (Common.isset(condition.checked)) values += "<p>" + option.checked.name + ": " + condition.checked + "</p>"

            condition.disabled = false
            if (!values) {
                condition.disabled = true
                values = '<p style="color:red">設定無し</p>'
            }

            // アンケート用サブテキスト
            condition.subtext = "";
            if (condition.key === 'enquete') {
                const ids = condition.value.split('-');
                this.post(Define.API_M_DATA, {enquete_id: ids[0]}).then((response) => {
                    const Enquete = response.Enquete
                    try {
                        condition.subtext = 'ID' + ids[0] + '. ' + Enquete.title
                        if (ids.length > 1) {
                            const q = ids[1]
                            condition.subtext += '<p>Q' + q + '. ' + Enquete.questions[q - 1].caption + '</p>'
                            if (ids.length > 2) {
                                const s = ids[2]
                                condition.subtext += '<p>回答' + s + ': ' + Enquete.questions[q - 1].selects[s - 1].option + '</p>'
                            }
                        }
                    } catch (e) {
                        condition.subtext += '<p style="color:red">指定エラー</p>'
                    }
                    this.setTarget(condition)
                })
            }

            condition.text = line + values;
        },

        getFlagConditionText(condition) {
            let text = ''

            for (let i = 0; i < this.$store.state.consts.flags.count; i++) {
                let segment = Math.trunc(i / 32) + 1
                let checks = condition['check' + segment] ?? ''
                let checkMasks = condition['checkMask' + segment] ?? ''
                let bit = 31 - (i % 32)
                const mask = (checkMasks.substring(bit, bit + 1) === '1')
                if (mask) {
                    const check = (checks.substring(bit, bit + 1) === '1')
                    text += '<p>' + (+i + 1) + '番に'
                    if (check) {
                        text += 'チェックあり'
                    } else {
                        text += 'チェックなし'
                    }
                    const name = this.checkNames[i]['name']
                    if (name) text += ' (' + name + ')</p>'
                }
            }

            if (!text) {
                text = '<p style="color:red">設定無し</p>'
            }

            return text;


        },
        //--------------------------------------------------------------
        // 条件の値をステータスにバック設定

        setCondition(condition) {
            let t = 0;
            for (let key in this.status) {
                t += document.getElementById('status_' + key).getBoundingClientRect().height;
                if (key === condition.key) {
                    // 複製して戻る
                    const clone = JSON.parse(JSON.stringify(condition))
                    clone.enabled = null
                    this.status[key] = clone
                    this.target = clone
                    // 要素を特定/スクロール
                    setTimeout(() => {
                        const stat = document.getElementById('status_' + key)
                        stat.parentElement.parentElement.scrollTo({top: this.getTop(key), behavior: "smooth"});
                    }, 100)
                    return true
                }
            }
            this.target = null
            return false
        },
        // Vue のせいか普通に位置が取れないので
        getTop(target) {
            let t = 0;
            for (let key in this.status) {
                if (key === target) {
                    return t
                }
                t += document.getElementById('status_' + key).getBoundingClientRect().height;
            }
            return 0
        },
    },
}