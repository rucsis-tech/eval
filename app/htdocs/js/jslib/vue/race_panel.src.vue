<template>
  <div>

    <div class="d-flex flex-wrap">

      <datetime-picker v-if="enableCalender" v-model="race_day" caption="開催日" class="rem-18 me-4" @change="getPlacesFromDate"></datetime-picker>

      <div v-if="!enableCalender" class="me-4 mb-2">
        <datetime-picker v-model="race_day" caption="開催日：" class="rem-18" @change="getPlacesFromDate"></datetime-picker>
      </div>

      <div class="me-4 mb-2">
        <!--開催日が決まると自動的に選択肢を取得-->
        開催場所
        <select v-model="race_kaisai_code" name="kaisai_code" class="form-inline rem-12" @change="getRacesFromPlace">
          <option v-for="(name,code) in places" :value="code">{{ name }}</option>
        </select>
      </div>

      <div class="me-4 mb-2">
        <!--開催日が決まると自動的に選択肢を取得-->
        レース名
        <select v-model="race_code" name="race_code" class="form-inline rem-10" @change="getUmasFromRace">
          <option v-for="(race_name,race_code) in races" :value="race_code">{{ race_name }}</option>
        </select>
      </div>

      <div v-if="enableUma" class="me-4 mb-2">
        <!--開催日が決まると自動的に選択肢を取得-->
        馬名
        <select v-model="race_registration" name="registration" class="form-inline rem-10">
          <option v-for="uma in umas" :value="uma.ketto_toroku_bango">{{ uma.bamei }}</option>
        </select>
      </div>


    </div>

  </div>
</template>


<script>

import Datetime from '/cms/jslib/vue/datetime.src.vue'

export default {

  components: {
    'datetime-picker': Datetime,
  },


  computed: {},

  methods: {
    setInfo: function (Info, registration) {
      this.race_day = Info.day
      this.race_kaisai_code = Info.kaisai_code ?? ''
      this.race_code = Info.race_code ?? ''
      //this.race_registration = Info.registration ?? ''
      this.getPlacesFromDate().then(() => {
        this.getRacesFromPlace();
        if (this.enableUma) {
          this.race_registration = registration;
          this.getUmasFromRace();
        }
      })
    },
    getPlacesFromDate: function () {
      return postJsonRequest('/cms/wp/_data.php', {type: 'KaisaiByDate', date: this.race_day,}).then((response) => {
        this.places = {};
        this.KaisaiList = response.KaisaiList;

        for (let code in this.KaisaiList) {
          let Kaisai = this.KaisaiList[code];
          let kaisai_code = Kaisai.kaisai_code
          this.places[kaisai_code] = Kaisai.info.kaisaimei
        }

        this.races = {}
      })
    },
    getRacesFromPlace: function () {
      if (this.KaisaiList[this.race_kaisai_code]) {
        const raceList = this.KaisaiList[this.race_kaisai_code].races
        this.races = {};
        for (let index in raceList) {
          this.races[raceList[index].race_code] = '[' + raceList[index].info.race_bango + '] ' + raceList[index].info.kyosomei
        }
      }
    },
    getUmasFromRace: function () {
      if (this.enableUma) {
        return postJsonRequest('/cms/wp/_data.php', {type: 'umabashira', race_code: this.race_code,}).then((response) => {
          this.umas = response.uma;
        });
      }


    }

  },


  mounted() {
    if (!this.days || this.days.length === 0) {
      this.enableCalender = true;
    }
    this.enableUma = (this.require === 'uma')

    if (this.race_day) {
      this.getPlacesFromDate().then(() => {
        this.getRacesFromPlace()
        if (this.enableUma) {
          this.getUmasFromRace();
        }
      })
    }

  },
  props: ['origin', 'days', 'require', 'day'],
  data() {
    return {
      KaisaiList: [],
      places: [],
      races: [],
      umas: [],
      enableCalender: false,
      enableUma: false,

      race_day: this.origin.day,
      race_kaisai_code: this.origin.kaisai_code ?? '',
      race_code: this.origin.race_code ?? '',
      race_registration: this.origin.registration ?? ''
    }
  },
}
</script>

