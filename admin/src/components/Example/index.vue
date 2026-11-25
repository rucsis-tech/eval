<template>
  <div class="member">
    <vk-tabs :index="index" @change="onChangeTab(arguments[0])">
      <vk-tabs-item name="Tab 1"><search-compo></search-compo></vk-tabs-item>
      <vk-tabs-item name="Tab 2">Content Tab 3</vk-tabs-item>
      <vk-tabs-item name="Tab 3">Content Tab 3</vk-tabs-item>
      <vk-tabs-item name="Tab 1">Content Tab 1</vk-tabs-item>
      <vk-tabs-item name="Tab 2">Content Tab 2</vk-tabs-item>
      <vk-tabs-item name="Tab 3">Content Tab 3</vk-tabs-item>
      <vk-tabs-item name="Tab 1">Content Tab 1</vk-tabs-item>
      <vk-tabs-item name="Tab 2">Content Tab 2</vk-tabs-item>
      <vk-tabs-item name="Tab 3">Content Tab 3</vk-tabs-item>
    </vk-tabs>
  </div>
</template>

<script>
import SearchCompo from './components/Member/Search'

export default {
  name: 'member',
  data () {
    return {
      index: 0
    }
  },
  components: {
    SearchCompo
  },
  methods: {
    onChangeTab (tabIndex) {
      this.index = tabIndex
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

</style>
