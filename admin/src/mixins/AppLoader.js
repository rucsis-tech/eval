import * as Define from "../js/Define.js";
import Common from "../js/Common.js";

export default {
    data() {
        return {
            itemList: [],
        }
    },
    methods: {
        //----------------------------------------------------------------------------------
        // プリセット取得
        loadPreset() {
            return this.post(Define.API_PRESET, {type: 1}).then((response) => {
                if (response.presetList.length === 0) {
                    this.message('プリセットはありません')
                } else {
                    this.presetList = response.presetList
                }
            })
        },

        //--------------------------------------------------------------
        // 商品リスト取得
        loadItems() {
            return this.post(Define.API_SALES_ITEM, {}).then((response) => {
                this.itemList = response.itemList
            })
        },

        //--------------------------------------------------------------
        // 商品リスト取得
        loadAuthors() {
            return this.post(Define.API_M_DATA, {authors: 1}).then((response) => {
                return response.Authors
            })
        },

    },
}