<template>

  <el-col :span="8" class="left" :style="'padding-left: '+(options.indent ?? 0)+'rem;'">
    <!-- 適用条件 一致or不一致 -->
    <narrow-condition v-model="item.mode" style="float:left;width:4.5rem;"></narrow-condition>
    <div style="float:left;">{{ item.name }}</div>
  </el-col>

  <el-col :span="14" class="right">
    <!-- 日付範囲 -------------------------------------------->
    <div v-if="isset(item.term)" class="condition-item">
      <el-date-picker v-model="item.term[0]" size="small" style="width:160px" :type="item.datetype ?? 'datetime'" placeholder="開始"></el-date-picker>
      ～
      <el-date-picker v-model="item.term[1]" size="small" style="width:160px" :type="item.datetype ?? 'datetime'" placeholder="終了"></el-date-picker>
    </div>


    <!-- 値入力 -------------------------------------------->
    <div v-if="isset(item.value) && !isset(options.selector)" class="condition-item">
      <span v-if="options.value && options.value.caption" style="margin:0 4px;font-size:0.8rem;color:#888;">{{ options.value.caption }}</span>
      <el-input v-model="item.value" size="small" style="width:16rem;"></el-input>
    </div>

    <!-- セレクタ1 -->
    <div v-if="isset(options.selector)" class="condition-item">
      <span v-if="options.selector.caption" style="margin:0 4px;font-size:0.8rem;color:#888;">{{options.selector.caption}}</span>
      <el-select v-model="item.value" size="small" class="form-dst" style="width:12rem;" :placeholder="item.name">
        <el-option v-for="option in options.selector.list" :key="option.value" :label="option.label" :value="option.value"/>
      </el-select>
    </div>


    <!-- sub力　-------------------------------------------->
    <div v-if="isset(item.sub) && !isset(options.sub_selector)" class="condition-item">
      <el-input v-model="item.sub" size="small" style="width:18rem;"></el-input>
    </div>

    <!-- セレクタ2 -->
    <div v-if="isset(options.sub_selector)" class="condition-item">
      <span v-if="options.sub_selector.caption" style="margin:2px 6px;font-size:0.8rem;color:#888;">{{options.sub_selector.caption}}</span>
      <el-select v-model="item.sub" size="small" class="form-dst" style="width:12rem;">
        <el-option v-for="option in options.sub_selector.list" :key="option.value" :label="option.label" :value="option.value"/>
      </el-select>
    </div>


    <!-- 数値範囲 -------------------------------------------->
    <div v-if="isset(item.min)" class="condition-item">
      <el-input-number v-model="item.min" size="small" style=""></el-input-number>
      ～
      <el-input-number v-model="item.max" size="small" style=""></el-input-number>
    </div>

    <!-- チェックボックス -------------------------------------------->
    <div v-if="isset(item.checked) && isset(options.checked)" class="condition-item">
      <label style="color:#888;margin:0 10px;">
        <input type="checkbox" v-model="item.checked">
        {{ options.checked.name }}
      </label>
    </div>

  </el-col>


</template>

<script>

import NarrowConditionsSelect from './NarrowConditionsSelect.vue'
import NarrowMixin from "./NarrowMixin.js";

export default {
  name: 'narrow-conditions',
  components: {
    'narrow-condition': NarrowConditionsSelect
  },
  mixins: [NarrowMixin],
  props: ['index', 'item', 'options'],
  data() {
    return {}
  },
  methods: {}
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
</style>
