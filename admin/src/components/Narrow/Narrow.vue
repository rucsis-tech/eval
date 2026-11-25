<template>
  <h3>会員絞り込み</h3>
  <div class="contents">

    <el-row style="font-size:15px;">

      <el-col :span="10" style="position:relative">
        <div style="position:absolute;right:0;z-index: 10;">
          <el-button @click="cbxModDialogVisible = true">チェック名編集</el-button>
        </div>

        <el-tabs ref="narrowTab" style="padding:0 8px;height: 600px;" v-model="tabName">

          <!-- 条件1 ---------------------------------------------------------------------------------->
          <el-tab-pane label="条件1" name="条件1" style="height: 100%;overflow: auto;">
            <narrow-condition1 ref="condition1" :addCondition="addCondition" :resetTarget="resetTarget" :targetCondition="targetCondition"></narrow-condition1>
          </el-tab-pane>

          <!-- 条件2 ---------------------------------------------------------------------------------->
          <el-tab-pane label="条件2" name="条件2" style="height: 100%;overflow: auto;">
            <narrow-condition2 ref="condition2" :addCondition="addCondition" :resetTarget="resetTarget" :targetCondition="targetCondition"></narrow-condition2>
          </el-tab-pane>

          <!-- 条件3 ---------------------------------------------------------------------------------->
          <el-tab-pane label="条件3" name="条件3" style="height: 100%;overflow: auto;">
            <narrow-condition3 ref="condition3" :addCondition="addCondition" :resetTarget="resetTarget" :targetCondition="targetCondition"></narrow-condition3>
          </el-tab-pane>

          <!-- 条件4 ---------------------------------------------------------------------------------->
          <!--el-tab-pane label="条件4" name="条件4" style="height: 100%;overflow: auto;">
            <narrow-condition4 ref="condition4" :addCondition="addCondition" :resetTarget="resetTarget" :targetCondition="targetCondition"></narrow-condition4>
          </el-tab-pane-->

          <!-- チェックリスト条件 ---------------------------------------------------------------------------------->
          <el-tab-pane label="チェックリスト" name="チェックリスト" style="height: 100%;">
            <narrow-condition-checklist ref="conditionChecklist" :addCondition="addCondition" :resetTarget="resetTarget" :targetCondition="targetCondition"></narrow-condition-checklist>
          </el-tab-pane>

          <!--  ---------------------------------------------------------------------------------->

        </el-tabs>
      </el-col>

      <el-col :span="7" style="padding:0px 10px 0 10px;position:relative">
        <!-- プリセット処理 ---------------------------------------------------------------------------------->
        <el-row style="margin-bottom: 5px;padding:0 4px;">

          <el-col :span="19" style="display: flex;">
            <el-checkbox style="margin: 0px 4px;" v-model="presetNewChecked" :disabled="!auth.PresetAdd" label="プリセット新規"></el-checkbox>
            <div style="margin: 4px 4px;font-size:1rem;" v-if="!presetNewChecked">{{ presetId ? 'ID:' + presetId : '' }}</div>
            <el-input placeholder="プリセット名を入力してください" :disabled="false" v-model="presetName"></el-input>
          </el-col>

          <el-col :span="5" style="text-align:right">
            <el-button type="primary" @click="savePreset()" v-if="!presetNewChecked" :disabled="!auth.PresetAdd || !presetName || conditionRoot.conditions.length === 0">
              更新保存
            </el-button>

            <el-button type="primary" @click="savePreset()" v-if="presetNewChecked" :disabled="!auth.PresetAdd || !presetName || conditionRoot.conditions.length === 0">
              新規保存
            </el-button>
          </el-col>
        </el-row>

        <!-- 設定条件 ---------------------------------------------------------------------------------->
        <h6 style="margin:5px 10px;">設定条件</h6>


        <div style="width: 100%;height:535px;overflow: auto;" @click="onClickConditionBack()">
          <div v-for="(condition1,index) in conditionRoot.conditions" @click.stop="selectCondition1(condition1)" class="set_condition1" :class="(this.targetCondition===condition1 || this.current===condition1)?'condition_bg_on':'condition_bg_off'">

            <!-- 条件 -->
            <div v-if="condition1.key!=='BIND'">
              <div v-html="condition1.text" :style="condition1.enabled?'':'color:#CCC'"></div>
              <div v-html="condition1.subtext" :style="condition1.enabled?'':'color:#CCC'"></div>
            </div>

            <!-- OR階層 -->
            <div v-if="condition1.key==='BIND'" style="margin:0 0 10px 0;" :style="condition1.enabled?'':'color:#CCC'">
              <div style="padding-bottom:12px;">{{ condition1.combination }}グループ</div>

              <div v-for="(condition2,index) in condition1.conditions" @click="selectCondition2(condition2)" class="set_condition1" :class="(this.targetCondition===condition2)?'condition_bg_on':'condition_bg_off'">
                <!-- 条件 -->
                <div style="position:relative;">
                  <div v-html="condition2.text" :style="condition2.enabled?'':'color:#CCC'"></div>
                  <div v-html="condition2.subtext" :style="condition2.enabled?'':'color:#CCC'"></div>
                  <el-button @click="deleteCondition(condition2)" style="position:absolute;top:0;right:0;height:2rem;">削除</el-button>
                  <el-switch size="small" v-model="condition2.enabled" style="position:absolute;top:0;right:70px;height:2rem;"/>
                </div>
              </div>
            </div>

            <el-button @click="deleteCondition(condition1)" style="position:absolute;top:4px;right:4px;height:2rem;">削除</el-button>
            <el-switch size="small" v-model="condition1.enabled" style="position:absolute;top:0;right:70px;height:2rem;"/>
          </div>

        </div>

        <el-button size="small" @click="addORGroup()" style="position: absolute;left:100px;top:36px;z-index:10;">ORグループ追加</el-button>


      </el-col>


      <!-- プリセット ----------------------------------------------------------------------------------------------------------------------------------------------------->
      <!------------------------------------------------------------------------------------------------------------------------------------------------------------------>

      <el-col :span="7" style="padding:0 10px;">
        <!--el-tabs ref="narrowTab" v-model="presetTabName"-->


        <!--el-tab-pane label="設定条件" name="設定条件" style="padding: 0;"-->
        <!--/el-tab-pane-->


        <!--el-tab-pane label="プリセット一覧" name="プリセット一覧"-->

        <!-- プリセット一覧 ---------------------------------------------------------------------------------->
        <el-table :data="presetList" border style="height:600px;">

          <el-table-column width="50" label="ID" align="center">
            <template #default="scope">{{ scope.row.id }}</template>
          </el-table-column>

          <el-table-column label="プリセット名称">
            <template #default="scope">{{ scope.row.name }}</template>
          </el-table-column>

          <el-table-column label="" width="150" align="center">
            <template #default="scope">
              <el-button type="primary" @click="setPreset(scope.row.id)" :disabled="!auth.PresetAdd" style="padding: 4px">
                条件読込
              </el-button>
              <el-button type="danger" @click="disableConfirmPreset(scope.row.id)" :disabled="!auth.PresetAdd" style="padding: 4px">
                削除
              </el-button>
            </template>
          </el-table-column>

        </el-table>
        <!-------------------------------------------------------------------------------------------------->

        <!--/el-tab-pane-->
        <!--/el-tabs-->

      </el-col>


    </el-row>

    <!------------------------------------------------------------------------------------------------------------------------------------------------------------------>
    <hr>
    <!------------------------------------------------------------------------------------------------------------------------------------------------------------------>

    <el-row class="contents" style="background-color: white;padding:20px 0">
      <el-col :span="17" style="text-align: right">


        <el-button :disabled="memberList.length === 0 || !auth.NarrowToMember" @click="onClickClipboard">
          クリップボードコピー
        </el-button>
        <el-button @click="onClickIdSave" :disabled="memberList.length === 0 || !auth.NarrowToMember">
          会員検索に反映
        </el-button>
        <el-button @click="setCheckFlags" :disabled="memberList.length === 0 || !auth.NarrowCheckAdd">
          チェックフラグを設定
        </el-button>
        <el-button @click="setGivePoints" :disabled="memberList.length === 0 || !auth.GivePoints">
          ポイント付与
        </el-button>
        <el-button @click="setGiveScore" :disabled="memberList.length === 0 || !auth.GiveScore">
          スコア付与
        </el-button>

        <el-button size="large" type="primary" class="" @click="onClickNarrow" style="font-size: 1.2rem;">
          絞り込み実行
        </el-button>

      </el-col>

    </el-row>
    <hr>
    <narrow-table :gridData="memberList"></narrow-table>

    <!-- ---------------------------------------------------------------------------------->
    <el-dialog title="チェックフラグ設定" v-model="cbxDialogVisible" size="tiny">
      <setting-flags ref="checkFlagDialog" :memberList="memberList" :closeDialog="closeDialog" :checkNames="checkNames"></setting-flags>
    </el-dialog>

    <!-- ---------------------------------------------------------------------------------->
    <el-dialog title="ポイントを付与" v-model="givePointsVisible" size="large">
      <give-points :memberList="memberList" :closeDialog="closeDialog"></give-points>
    </el-dialog>

    <!-- ---------------------------------------------------------------------------------->
    <el-dialog title="スコアを付与" v-model="giveScoreVisible" size="large">
      <give-score :memberList="memberList" :closeDialog="closeDialog"></give-score>
    </el-dialog>

    <!-- ---------------------------------------------------------------------------------->
    <el-dialog title="チェック名編集" v-model="cbxModDialogVisible" size="large">
      <chk-name-mod :checkNames="checkNames" :loadCheckNames="loadCheckNames"></chk-name-mod>
    </el-dialog>


  </div>
</template>

<script>
import NarrowTable from './NarrowTable.vue'
import NarrowCondition1 from './NarrowCondition1.vue'
import NarrowCondition2 from './NarrowCondition2.vue'
import NarrowCondition3 from './NarrowCondition3.vue'
import NarrowCondition4 from './NarrowCondition4.vue'
import NarrowConditionChecklist from './NarrowConditionChecklist.vue'
import NarrowMixin from './NarrowMixin.js'

import SettingFlags from './SettingFlags.vue'
import CheckNameModify from './CheckNameModify.vue'
import GivePoints from './GivePoints.vue'
import GiveScore from './GiveScore.vue'

import Common from '../../js/Common'
import * as Define from '../../js/Define'
import AppVue from "../../mixins/AppVue.js";
import AppLoader from "../../mixins/AppLoader.js";

export default {
  name: 'narrow',
  mixins: [AppVue, AppLoader, NarrowMixin],
  components: {
    'narrow-table': NarrowTable,
    'setting-flags': SettingFlags,
    'chk-name-mod': CheckNameModify,
    'give-points': GivePoints,
    'give-score': GiveScore,
    'narrow-condition1': NarrowCondition1,
    'narrow-condition2': NarrowCondition2,
    'narrow-condition3': NarrowCondition3,
    'narrow-condition4': NarrowCondition4,
    'narrow-condition-checklist': NarrowConditionChecklist,
  },


  methods: {

    logined() {
      this.setAuth('narrow')
      this.loadPreset()
      this.loadCheckNames()
    },

    //-----------------------------------------------------------------------------------------------------------------------------------------------------
    // 絞り込み実施
    //-----------------------------------------------------------------------------------------------------------------------------------------------------
    onClickNarrow() {
      // 設定条件取得・確認
      const data = {
        key: this.conditionRoot.key,
        combination: this.conditionRoot.combination,
        conditions: this.conditionRoot.conditions,
      }
      console.log(data)
      if (data.conditions.length > 0) {
        //console.log(data)
        Common.loadStart()
        this.post(Define.API_NARROW, data).then(this.narrowRes)
      } else {
        this.message('条件が入力されていません')
      }
    },

    // -----------------------------------
    narrowRes(response) {
      this.memberList = [];
      response.list.forEach((item, i) => {
        // 値調整あれば
        item.id = +item.mid
        this.memberList.push(item);
      })
      if (response.list.length === 0) {
        this.message('該当者がいません')
      }
      Common.loadStop()
    },

    //-----------------------------------------------------------------------------------------------------------------------------------------------------
    // 各条件
    //-----------------------------------------------------------------------------------------------------------------------------------------------------
    /**
     * 左のステータスを条件として追加・更新
     * @param condition
     */

    addCondition(condition) {
      condition = JSON.parse(JSON.stringify(condition))

      if (this.targetCondition && this.targetCondition.key === condition.key) {
        // 更新
        for (let key in condition) {
          if (key !== 'enabled') this.targetCondition[key] = condition[key]
        }
        this.setConditionText(this.targetCondition);
        this.targetCondition = null
      } else {
        // 追加
        this.setConditionText(condition);
        condition.enabled = true
        this.current.conditions.push(condition)
      }

      this.presetTabName = '設定条件';
    },

    //-----------------------------------------------------------------------------------------------------------------------------------------------------
    // 設定済み条件リスト
    //-----------------------------------------------------------------------------------------------------------------------------------------------------

    /**
     * リストから削除
     * @param condition
     */
    deleteCondition(condition) {
      const index = this.conditionRoot.conditions.indexOf(condition)
      if (index > -1) {
        this.conditionRoot.conditions.splice(index, 1)
        this.current = this.conditionRoot
      } else {
        for (let i = 0; i < this.conditionRoot.conditions.length; i++) {
          const src = this.conditionRoot.conditions[i];
          if (src.key === 'BIND') {
            const index = src.conditions.indexOf(condition)
            if (index > -1) src.conditions.splice(index, 1)
          }
        }
      }

      this.targetCondition = null
      console.log(index)
    },
    /**
     * 条件を選択(ルート）
     * @param condition
     */
    selectCondition1(condition) {
      // 削除済みかチェック
      if (this.conditionRoot.conditions.indexOf(condition) === -1) return

      if (condition.key === 'BIND') {
        // ２階層枠をクリック
        this.current = condition
      } else {
        // ルート条件をクリック
        this.current = this.conditionRoot
        this.setTarget(condition);

      }
    },
    /**
     * ターゲットのセットとステータスを反映
     * @param condition
     */
    setTarget(condition) {
      this.targetCondition = condition
      if (this.$refs.condition1.setCondition(condition)) this.tabName = '条件1'
      if (this.$refs.condition2.setCondition(condition)) this.tabName = '条件2'
      if (this.$refs.condition3.setCondition(condition)) this.tabName = '条件3'
      if (this.$refs.condition4.setCondition(condition)) this.tabName = '条件4'
      if (this.$refs.conditionChecklist.setCondition(condition)) this.tabName = 'チェックリスト'
    },
    getOption(key) {
      if (this.$refs.condition1.status[key]) return this.$refs.condition1.options[key] ?? {}
      if (this.$refs.condition2.status[key]) return this.$refs.condition2.options[key] ?? {}
      if (this.$refs.condition3.status[key]) return this.$refs.condition3.options[key] ?? {}
      if (this.$refs.condition4.status[key]) return this.$refs.condition4.options[key] ?? {}
      return {};
    },
    /**
     * 条件を選択(２階層）
     * @param condition
     */
    selectCondition2(condition) {
      this.setTarget(condition)
    },
    // 選択中条件をなくす
    resetTarget() {
      this.targetCondition = null
    },
    //OR条件の追加
    addORGroup() {
      const OR = {key: 'BIND', combination: 'OR', conditions: [], enabled: true,};
      this.conditionRoot.conditions.push(OR)
      this.current = OR
      this.targetCondition = null
    },
    onClickConditionBack() {
      console.log('onClickConditionBack')
      this.current = this.conditionRoot
      this.targetCondition = null
    },

    //-----------------------------------------------------------------------------------------------------------------------------------------------------
    // プリセット
    //-----------------------------------------------------------------------------------------------------------------------------------------------------
    /**
     * サーバに保存
     */
    savePreset() {
      if (this.presetName === '') {
        this.message('プリセット名を入力してください')
        return
      }
      const condition = this.conditionRoot;
      if (condition.conditions.length === 0) {
        this.message('条件を設定してください')
        return
      }

      const text = this.presetNewChecked ? '設定条件を新規保存してよろしいですか？' : 'プリセット(id:' + this.presetId + ')を更新してよろしいですか？';
      this.confirm('プリセット保存', text, 'info', () => {
        const data = {
          id: this.presetNewChecked ? 0 : this.presetId,
          name: this.presetName,
          conditionRoot: condition
        }
        this.post(Define.API_PRESET, data).then(this.savePresetRes)
      })


    },
    savePresetRes(response) {
      this.loadPreset()
      this.message('プリセットを保存しました', 'success')
    },
    /**
     * プリセット内容を条件にセット
     * @param id
     */
    setPreset(id) {

      for (let i = 0; i < this.presetList.length; i++) {
        if (this.presetList[i].id === id) {
          const condition = JSON.parse(JSON.stringify(this.presetList[i].condition));
          this.setConditionText(condition);
          console.log(condition)
          this.conditionRoot = condition
          this.current = this.conditionRoot

          this.presetName = this.presetList[i].name
          this.presetId = this.presetList[i].id

          this.message('プリセット[' + this.presetList[i].name + ']の条件を設定を設定しました')
          this.presetNewChecked = false
          this.presetTabName = '設定条件'
        }
      }

    },
    /**
     * プリセット無効化
     * @param id
     */
    disableConfirmPreset(id) {
      this.disablePresetId = id;
      this.confirm('プリセット非表示', '指定のプリセット(id:' + id + ')を削除してよろしいですか？', 'info', this.delPreset)
    },
    delPreset() {
      this.post(Define.API_PRESET, {disable_preset_id: this.disablePresetId}).then((response) => {
        this.loadPreset()
        this.presetName = ''
        this.presetId = 0
        this.message('プリセットを非表示にしました', 'success')
      })
    },


    //-----------------------------------------------------------------------------------------------------------------------------------------------------
    //-----------------------------------------------------------------------------------------------------------------------------------------------------

    //---------------------------------------------------------------------
    //　会員検索に反映
    onClickIdSave() {
      if (this.memberList.length > 0) {
        this.memberList.sort(function (a, b) {
          return b.mid - a.mid;
        })
        this.$store.commit('setMemberList', this.memberList)
        this.message('会員検索に反映しました', 'success')
      }
    },
    //---------------------------------------------------------------------
    // チェックフラグの名称取得
    loadCheckNames() {
      this.post(Define.API_CHECK_NAMES, {'type': 1}).then((response) => {
        this.checkNames = response.check_names
        this.$refs.conditionChecklist.checkNames = response.check_names
      })
    },
    //---------------------------------------------------------------------
    // ダイアログ表示

    setCheckFlags() {
      this.cbxDialogVisible = true
      this.$refs.checkFlagDialog.reset()
    },

    setGivePoints() {
      this.givePointsVisible = true
    },
    setGiveScore() {
      this.giveScoreVisible = true
    },
    closeDialog() {
      this.cbxDialogVisible = false
      this.givePointsVisible = false
      this.giveScoreVisible = false
    },

    //---------------------------------------------------------------------
    // クリップボード

    // テキスト生成
    onClickClipboard() {
      let text = ''
      text += '認証ID\t'
      text += '会員番号\t'
      text += '入会日時\t'
      text += '最終アクセス\t'
      text += '利用総額\n'
      this.memberList.forEach(function (item, i) {
        text += item.uid
        text += '\t'
        text += (item.mid ?? '')
        text += '\t'
        text += item.created_at
        text += '\t'
        text += item.accessed_at
        text += '\t'
        text += item.payment_sum
        text += '\n'
      })

      this.copyToClipboard(text)
    },
  },

  //-----------------------------------------------------------------------------------------------------------------------------------------------------
  //-----------------------------------------------------------------------------------------------------------------------------------------------------
  computed: {},
  mounted() {
    this.current = this.conditionRoot;
  },
  data() {
    return {
      auth: this.$store.state.narrowDivision,

      tabName: '条件1',
      presetTabName: '設定条件',

      cbxDialogVisible: false,
      cbxModDialogVisible: false,
      givePointsVisible: false,
      giveScoreVisible: false,
      conditionText: 'NONE',
      targetCondition: null,
      current: {},
      conditionRoot: {key: 'BIND', combination: 'AND', conditions: [],},
      memberList: [],

      presetNewChecked: 0,
      presetId: 0,
      presetName: '',
      presetList: [],
      disablePresetId: 0,

      checkNames: [],
    }
  },
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style>
.checkBoxBlock {
  color: #666;
}

.checkBoxBlock:hover {
  color: #333;
  font-weight: bold;
}

.condition {
  margin-bottom: 8px;
}

.conditionText p {
  font-size: 0.8rem;
  margin: 0 0.5rem;
}

.set_condition1 {
  position: relative;
  border: 1px solid #AAA;
  border-radius: 6px;
  padding: 0.2rem 0.5rem;
  margin: 0.3rem 0.5rem;
}

.condition_bg_on {
  background-color: white;
}

.condition_bg_off {
  background-color: #eee;
}

p {
  margin-bottom: 0.2rem;
}
</style>
