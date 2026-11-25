<template>

  <div class="app-bar" :style="$store.state.barStyle">
    <el-row style="color:#fff;">
      <el-col class="ttl-area" style="display:flex;flex-wrap:wrap;padding:8px;" :span="16">

        <div  style="font-size:30px;font-family: 'Arial Black';color:#FFF;vertical-align:bottom;">
          <!--img style="width: 150px;margin-right:8px;" :src="mainLogo" alt="logo"-->
          Eval
        </div>

        <div style="font-size:30px;font-family: 'Arial Black';color:#4AA;vertical-align:bottom;">
          Admin
        </div>

        <div class="" style="font-size:0.8rem;padding:4px 8px 0 16px;">
          ver&nbsp;{{ VERSION }}
          [build {{ build.toLocaleDateString() }} {{ build.toLocaleTimeString() }}]
        </div>

        <div class="" style="font-size:0.8rem;padding:4px 8px 0 16px;">
            <span style="font-size:0.8rem;font-weight: bold;color:#CCF;padding-left: 1rem;">
            API: {{ $store.state.API_BASE_URL }}
            </span>
        </div>


      </el-col>

      <el-col style="display:flex;flex-wrap:wrap;" :span="8">
        <div style="padding:10px 8px;font-size: 0.8rem;line-height: 120%;">
          {{ this.$store.state.userInfo.uid }}<br>
          {{ this.$store.state.userInfo.email }}
        </div>
        <el-button style="margin:10px 8px;" v-if="$store.state.loginToken" @click="onLogout">ログアウト</el-button>
      </el-col>

    </el-row>
  </div>

  <router-view/>

</template>

<style scoped>

.app-bar {
  background-color: #002060;
  line-height: 36px;
  padding: 4px;
}

.ttl-area {
  text-align: left;
  padding-left: 20px;
}

.ttl {
  font-size: 18px;
  padding: 0;
  margin: 0;
  float: left;
}

.version {
  font-size: 12px;
  padding: 0;
  margin: 0;
  position: relative;
  top: 3px;
  left: 10px;
}

.logout-button {
  text-align: right;
  padding-right: 30px;
}
</style>

<script>
//import logoUrl from "./assets/img/logo.svg"
//import mainLogo from "./assets/img/logo.svg"

export default {
  name: 'app',
  data() {
    return {
      VERSION: this.$store.state.version,
      build: this.$store.state.build,
      mainLogo: '',
    }
  },
  created() {
  },
  methods: {
    onLogout() {
      document.cookie = "token=;"
      document.cookie = "guid=;"
      this.$store.commit('setLogin', {token: null})
      this.$router.replace('/')
    }
  }
}

</script>