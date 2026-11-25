<template>
  <el-select placeholder="Select" size="small" :style="innerStyle" style="margin-right: 5px;">
    <template v-for="item in options">
      <el-option :label="item.label" :value="item.value">
      </el-option>
    </template>
  </el-select>
</template>

<script>
export default {
  name: 'narrow-select',
  data() {
    return {
      innerStyle: {
        color: '#f00',
        width: '100px'
      },
      options: [
        {
          value: 1,
          label: '一致'
        },
        {
          value: 2,
          label: '不一致'
        }],
      value: 0
    }
  },
  methods: {}
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
.el-input__inner.red {
  color: #ff0000;
}
</style>
