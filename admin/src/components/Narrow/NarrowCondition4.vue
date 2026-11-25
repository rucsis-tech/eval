<template>
  <div style="border-top:1px solid #ccc">

    <el-row v-for="(item,keyIndex) in status" @click="onClickStatus(item)" :class="(target===item)?'condition-target':'condition-row'" :id="'status_'+keyIndex">


      <narrow-conditions :index="keyIndex" :item="item" :options="options[keyIndex]??{}"></narrow-conditions>

      <el-col :span="2" style="text-align: center;">
        <el-button v-if="(targetCondition && keyIndex === targetCondition.key)" size="small" type="default" @click="onClickRegister(keyIndex)">
          更新
        </el-button>
        <el-button v-if="!(targetCondition && keyIndex === targetCondition.key)" size="small" type="primary" @click="onClickRegister(keyIndex)">
          追加
        </el-button>
      </el-col>


    </el-row>

  </div>
</template>


<script>
import AppLoader from "../../mixins/AppLoader.js";
import NarrowMixin from "./NarrowMixin.js";
import NarrowConditions from "./NarrowConditions.vue";
import AppVue from "../../mixins/AppVue.js";

const POINT_CONTENT_TYPES = [
  {value: 'none', label: '-'},
  {value: 'page_all', label: '記事まとめて'},
  {value: 'content', label: '見解のみ'},
  {value: 'kaime', label: '買い目のみ'},
  {value: 'mark_all', label: '印まとめて'},
  {value: 'mark_n_', label: '印個別'},
];
export default {
  name: 'nrwcon4',
  mixins: [AppVue, AppLoader, NarrowMixin],
  components: {
    NarrowConditions,
  },
  methods: {
    logined() {
      this.loadAuthors().then((list) => {
        for (let i = 0; i < list.length; i++) this.options.point_author.selector.list.push({value: list[i].id, 'label': list[i].name});
      })
    },

    reset() {
      this.status = {
        rank: {name: 'ランク', mode: 1, min: 1, max: 20},       //「ランク」・・・「数値」個～「数値」個
        score: {name: 'スコア', mode: 1, term: [null, null], min: 0, max: null},       //「スコア」・・・「数値」個～「数値」個
        point_contents: {name: 'ポイント記事', mode: 1, term: [null, null], value: '', sub: 'none'},
        point_author: {name: 'ポイント著者', mode: 1, term: [null, null], value: '', sub: 'none'},
        point_spoiler: {name: 'ポイントスポイラー', mode: 1, term: [null, null], value: ''},
        point_windex: {name: 'ポイントWINDEX', mode: 1, term: [null, null], value: ''},
        point_pack: {name: 'ポイントパック商品', mode: 1, term: [null, null], value: ''},
      }
    },

    clickInputClear(event) {
      let name = event.target.parentElement.id
      this[name] = ''
    }

  },

  created() {
    this.reset()
  },
  props: ['addCondition', 'resetTarget', 'targetCondition'],
  data() {
    return {
      status: {},
      options:{
        point_contents: {
          value: {caption: '記事ID', type: 'number'},
          sub_selector: {caption: 'タイプ', list: POINT_CONTENT_TYPES},
        },
        point_author: {
          selector: {caption: '著者', list: []},
          sub_selector: {caption: 'タイプ', list: POINT_CONTENT_TYPES},
        },
        point_spoiler: {
          value: {caption: 'コメントID'},
        },
        point_windex: {
          value: {caption: 'レースコード'},
        },
        point_pack:{
          value: {caption: 'パック商品ID'},
          sub_selector: {caption: '早割/通常', list: [{value: 'all', label: '全て'},{value: 'start', label: '早割'},{value: 'publish', label: '通常'},]},
        },

      },

      RANKS: this.$store.state.consts.ranks,

    }
  },
}
</script>

<style>
.item-condition-row {
  border-bottom: solid 1px #555;
  padding: 8px 0;
}
</style>
