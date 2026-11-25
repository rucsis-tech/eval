<template>
  <div style="height:100%;">

    <el-row style="margin-bottom: 10px;width:95%;">
      <el-col :span="12">
      </el-col>

      <el-col :span="10" class="right">
        <el-select ref="selCheckBox" style="width:10rem;" v-model="checkFlg.mode">
          <el-option label="完全一致" :value="1"></el-option>
          <el-option label="いずれかに一致" :value="2"></el-option>
        </el-select>
      </el-col>

      <el-col :span="2" class="right">
        <el-button :type=" (targetCondition && targetCondition.key === 'check_flag') ? 'default' : 'primary'" @click="onClickRegister('check_flag')">
          {{ (targetCondition && targetCondition.key === 'check_flag') ? '更新' : '追加' }}
        </el-button>
      </el-col>

    </el-row>




    <div style="padding:10px 10px 50px 10px;background-color:#f0f8ff;text-align:center;height:100%;overflow:auto;">

      <div style="float:left;width:110px;text-align:left;" v-for="(name, i) in checkNames">
        <el-tooltip effect="dark" :content="checkNames[i].name" placement="top-end" :disabled="checkNames[i].name == ''">
          <div class="checkBoxBlock">
            <el-checkbox v-model="masks[i]" ref="cbx1" style="margin-right:8px;"></el-checkbox>
            <el-checkbox v-model="checks[i]" ref="cbx2" style="margin-right:4px;"></el-checkbox>
            {{ i + 1 }}
          </div>
        </el-tooltip>
      </div>
      <br style="clear:both;"/>
      <hr>

    </div>

  </div>
</template>

<script>
import AppVue from "../../mixins/AppVue.js";
import AppLoader from "../../mixins/AppLoader.js";
import NarrowMixin from "./NarrowMixin.js";

const date = new Date()

export default {
  name: 'nrwItemCon',
  mixins: [AppVue, AppLoader, NarrowMixin],
  components: {
  },

  created() {
    this.reset()
  },
  mounted() {

  },
  methods: {

    /**
     * パネルの状態からcondition を取得する
     * @returns {{key: string, mode: number}}
     */

    getCheckFlagCondition() {
      // チェックフラグ
      let checkData = []
      let checkDataMask = []
      let check = ''
      let mask = ''
      let flag_check = false

      for (let i = 0; i < this.$store.state.consts.flags.count; i++) {
        if (this.masks[i]) {
          flag_check = true
          mask = '1' + mask
          if (this.checks[i]) {
            check = '1' + check
          } else {
            check = '0' + check
          }
        } else {
          check = '0' + check
          mask = '0' + mask
        }
        if (check.length === 32) {
          checkData.push(check)
          checkDataMask.push(mask)
          check = ''
          mask = ''
        }
      }

      const condition = {
        key: 'check_flag',
        mode: this.checkFlg.mode,
      }


      if (condition.mode > 0 && flag_check) {
        for (let i = 0; i < checkData.length; i++) {
          condition['check' + (i + 1)] = checkData[i];
          condition['checkMask' + (i + 1)] = checkDataMask[i];
        }
      }

      return condition;

    },
    setStatus(condition) {
      this.reset()
      for (let i = 0; i < this.$store.state.consts.flags.count; i++) {
        let segment = Math.trunc(i / 32) + 1
        let check = condition['check' + segment] ?? ''
        let checkMask = condition['checkMask' + segment] ?? ''
        let bit = 31 - (i % 32)
        this.checks[i] = (check.substring(bit, bit + 1) === '1')
        this.masks[i] = (checkMask.substring(bit, bit + 1) === '1')
      }
      this.checkFlg.mode = condition.mode;

    },


    reset() {
      this.checkFlg = {mode: 1};
      this.checks = this.getChecks();
      this.masks = this.getChecks();
    },

    setCondition(condition) {
      if (condition.key === 'check_flag') {
        this.setStatus(condition)
        return true
      } else {
        return false
      }
    }

  },

  props: ['addCondition', 'resetTarget', 'targetCondition'],
  data() {
    return {
      status: {},
      checkNames: [],
      checkFlg: {mode: 1},
      checks: this.getChecks(),
      masks: this.getChecks(),

      checked_name: {},
      selector: {},
      sub_selector: {},

    }
  },
}
</script>

<style>
.item-condition-row {
  border-bottom: solid 1px #555;
  padding: 8px 0px;
}
</style>
