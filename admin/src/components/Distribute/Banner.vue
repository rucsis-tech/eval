<template>
  <h3>バナー管理</h3>

  <div class="contents" style="padding-top: 0">
    <el-row>
      <el-col :span="16" style="padding:0.5rem;">
        <span style="padding:0 1rem;">現在の表示状態変更</span>
        <el-button v-for="banner,index in bannerList" :type="banner.enable?'primary':''" :disabled="!auth.BannerEdit || banner.enable?true:false" @click="changeBannerConfirm(banner)">{{ TYPES[banner.type].caption }}</el-button>
        <hr>

        <!-- バナー管理 ----------------------------------------------------------->

        <el-card v-for="banner in bannerList" style="margin:1rem;">
          <template #header>
            <div class="card-header">
              <span style="padding:0 0.5rem;">{{ TYPES[banner.type].caption }}</span>
              <el-date-picker v-model="banner.countdowned_at" v-if="banner.type==='COUNTDOWN'" type="datetime" @change="onModify(banner.type)"></el-date-picker>
              <el-button type="success" style="float:right;" :disabled="!auth.BannerEdit || !TYPES[banner.type].modify" @click="onModifyBanner(banner)">設定更新</el-button>
            </div>
          </template>
          <el-row>

            <el-col :span="12">
              TOP表示
              <el-select v-model="banner.top_image_id" placeholder="Select" size="large" @change="onModify(banner.type)">
                <el-option v-for="img in imageList" :key="img.id" :label="img.id" :value="img.id"/>
              </el-select>
              <el-input v-model="banner.top_link" style="width:80%;margin:4px 18px;" placeholder="TOPリンク" @change="onModify(banner.type)"></el-input>
              <div style="width:100%;padding:1rem;">
                <img :src="imageList[banner.top_image_id] ? imageList[banner.top_image_id].url : '/sample-banner.png'" style="width:80%;">
              </div>
            </el-col>

            <el-col :span="12">
              Popup表示
              <el-select v-model="banner.popup_image_id" placeholder="Select" size="large" @change="onModify(banner.type)">
                <el-option v-for="img in imageList" :key="img.id" :label="img.id" :value="img.id"/>
              </el-select>
              <el-input v-model="banner.popup_link" style="width:80%;margin:4px 18px;" placeholder="POPUPリンク" @change="onModify(banner.type)"></el-input>
              <div style="width:100%;padding:1rem;">
                <img :src="imageList[banner.popup_image_id] ? imageList[banner.popup_image_id].url : '/sample-banner.png'" style="width:80%;">
              </div>

            </el-col>

          </el-row>
        </el-card>

      </el-col>

      <!-- 登録画像一覧 ----------------------------------------------------------->

      <el-col :span="8" style="padding:0.5rem;">

        <el-card>
          <h3 style="margin: 0 0.5rem">登録画像一覧({{ Object.keys(imageList).length }})</h3>

          <el-button type="primary" style="" :diabled="!auth.BannerEdit" @click="openImageDialogNew">新規画像登録</el-button>

          <el-scrollbar height="600px" style="margin:1rem;">
            <div v-for="image in imageList" :key="image" class="image" style="padding:0.5rem;" @click="onClickImage(image)">
              ID: {{ image.id }}
              <div style="width:80%;padding:1rem;">
                <img :src="image.url" style="width:80%;">
              </div>
            </div>
          </el-scrollbar>
        </el-card>


      </el-col>

    </el-row>
  </div>
  <!-- ----------------------------------------------------------->

  <el-dialog title="【トップページ用LQプレセール画像CMS】画像登録／編集" v-model="imageDialogVisible" size="small">
    <el-row style="margin-bottom:5px;">
      <el-col :span="6" style="font-size:18px;">
        ID
      </el-col>
      <el-col :span="6" style="font-size:18px;">
        {{ selectedImage ? selectedImage.id : '新規登録' }}
      </el-col>
      <el-col :span="12" style="font-size:18px;">
      </el-col>
    </el-row>

    <el-row style="margin-bottom:5px;">
      <el-col :span="6" style="font-size:18px;">
        Image
      </el-col>
      <el-col :span="6" style="font-size:18px;height:150px;">
        <img :src="selectedImage ? selectedImage.url : ''" height="100">
      </el-col>
      <el-col :span="12" style="font-size:18px;">
        UPLOAD:<input type="file" @change="onCheckUploaded">
      </el-col>
    </el-row>

    <el-row style="margin-bottom:5px;">
      <el-col :span="24" style="font-size:18px;">
        <el-button type="default" @click="cancelImage">
          戻る
        </el-button>
        <el-button type="primary" :disabled="selectedImage !== null" @click="addImage">
          新規登録
        </el-button>
        <el-button type="danger" :disabled="selectedImage === null" @click="delImageConfirm">
          削除
        </el-button>
        <el-button type="success" :disabled="selectedImage === null" @click="modImage">
          更新
        </el-button>
      </el-col>
    </el-row>


  </el-dialog>


</template>

<script>
import AdvertiseTable from '../Advertise/AdvertiseTable.vue'
import AppVue from "../../mixins/AppVue.js";
import * as Define from '../../js/Define.js'

export default {
  name: 'banner',
  mixins: [AppVue],
  components: {
    'ad-table': AdvertiseTable
  },
  data() {
    return {
      TYPES: {
        COUNTDOWN: {index: 0, modify: false, caption: 'カウントダウン', selected: false},
        SALE: {index: 1, modify: false, caption: 'セール中/基本', selected: false},
        SOLDOUT: {index: 23, modify: false, caption: '完売', selected: false},
        FREE: {index: 3, modify: false, caption: 'フリー', selected: false},
      },
      bannerList: [],
      imageList: {},

      since: new Date().setTime(new Date().getTime() - 3600 * 1000 * 24 * 7),
      until: new Date().setTime(new Date().getTime()),

      value: 0,
      options: [],


      advertiseNewCheck: false,
      errorDialogVisible: false,
      errorMsg: '',
      editMode: 1,
      dialogTitle: '',
      mediaVal: '',
      selectedMediaCode: '',
      adChildCode: '',
      adName: '',
      adDate: '',
      adPrice: '',
      adNotes: '',
      selectedBanner: null,
      addBtnShow: false,
      mediaCategorys: ['新聞', '雑誌', 'ブログ', 'メルマガ', 'WEB広告', 'DM', '提携先'],


      imageDialogVisible: false,
      selectedImage: null,
      imageFile: null,


      selectedMedia: null,
      mediaCode: '',
      categoryVal: '',
      mediaName: '',
      mediaUrl: '',
      mediaNote: '',
      mediaNewCheck: false
    }
  },
  methods: {
    logined() {
      this.setAuth('distribute')
      this.loadBanner()
    },
    loadBanner() {
      this.bannerList = []
      this.imageList = []
      this.selectedBanner = null
      const data = new FormData()
      data.append('type', 'LIST')
      this.post(Define.API_BANNER_IMAGE, data).then((response) => {


            console.log(response.imageList)
            console.log(Object.keys(response.imageList))
        this.bannerList = response.bannerList
        this.imageList = response.imageList
          }
      )
    },
    //-------------------------------------------------------
    // 表示バナーの変更
    changeBannerConfirm(banner) {
      this.confirm('表示バナー', '[' + this.TYPES[banner.type].caption + ']に変更してもよろしいですか？', 'info', () => {
        this.changeBanner(banner)
      })
    },
    changeBanner(banner) {
      console.log(banner)
      this.post(Define.API_BANNER_IMAGE, {type: 'CHANGE', id: banner.id}).then((response) => {
        this.loadBanner()
      })

    },
    //-------------------------------------------------------
    onModify(type) {
      this.TYPES[type].modify = true

    },
    onModifyBanner(banner) {
      this.post(Define.API_BANNER_IMAGE, {type: 'MOD', banner: banner}).then((response) => {
        this.loadBanner()
      })
    },

    //-------------------------------------------------------
    // 画像変更ダイアログ
    onClickImage(item) {
      if (!this.auth.BannerEdit) {
        return;
      }
      this.selectedImage = item
      this.imageDialogVisible = true
    },

    //-------------------------------------------------------
    // 画像追加ダイアログ
    openImageDialogNew() {
      console.log(this.imageList)
      this.selectedImage = null
      this.imageDialogVisible = true
    },
    //-------------------------------------------------------
    postImage() {
      // Formデータ作成
      const formData = new FormData();

      formData.append("type", "SET");
      formData.append("id", this.selectedImage ? this.selectedImage.id : 0);
      formData.append("file", this.imageFile);

      // POST送信
      this.post(Define.API_BANNER_IMAGE, formData).then((response) => {
        this.imageDialogVisible = false
        this.loadBanner()
      })

    },

    cancelImage() {
    },
    addImage() {
      this.postImage()
    },
    modImage() {
      this.postImage()
    },
    delImageConfirm() {
      this.confirm('画像コード削除', '[' + this.selectedImage.id + ']を削除してよろしいですか？', 'info', this.delImage)
    },
    delImage() {
      this.post(Define.API_BANNER_IMAGE, {type: 'DEL', id: this.selectedImage.id}).then(() => {
        this.loadBanner();
      })
    },
    onCheckUploaded(e) {
      // event(=e)から画像データを取得する
      this.imageFile = e.target.files[0];
    },

  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

.image:hover {
  background: #FED;
}
</style>
