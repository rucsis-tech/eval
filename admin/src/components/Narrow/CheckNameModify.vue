<template>
  <div>
    <div style="float:left;width:18%;margin:5px 5px;" v-for="name in checkNames">
      <el-row>
        <el-col :span="3" class="right">
          <span style="font-size:12px;">{{ name.check_num }}</span>
        </el-col>
        <el-col :span="21">
          <el-input v-model="name.name" size="small" style="width:100%;margin-left:5px" @change="onChange(name.check_num)"></el-input>
        </el-col>
      </el-row>
    </div>
    <hr style="clear:both;margin:20px 0">
    <div style="text-align:center;">
      <el-button type="primary" @click="updateCheckNames" style="width:200px;">更新</el-button>
    </div>
  </div>
</template>

<script>
import * as Define from '../../js/Define'
import AppVue from "../../mixins/AppVue.js";

export default {
  name: 'cbxname',
  mixins: [AppVue],
  props: ['checkNames', 'loadCheckNames'],
  data() {
    return {
      modifyList: []
    }
  },
  methods: {
    // ---------------------------------------------------------------------------
    // 更新実施通信
    updateCheckNames() {
      const data = {
        names: {},
      }

      for (let i = 0; i < this.checkNames.length; i++) {
        const num = this.checkNames[i].check_num;
        if (this.modifyList.indexOf(num) > -1) data.names[num] = this.checkNames[i].name
      }
      this.post(Define.API_CHECK_NAMES, data).then(this.updateCheckNamesRes)
    },
    updateCheckNamesRes(response) {
      this.modifyList = [];
      this.message('チェック名を更新しました', 'success')
      this.loadCheckNames()
    },
    onChange(check_num) {
      this.modifyList.push(check_num);
    },
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

</style>
