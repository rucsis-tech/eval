<template>
  <div class="input-group date m-0" :id="thisId" data-target-input="nearest">
    <label class="pt-1">{{ caption }}</label>
    <input type="text" name="start" class="form-control datetimepicker-input py-0 px-2" :value="value" @change.td="change"/>
    <div class="input-group-text" data-toggle="datetimepicker">
      <i class="far fa-calendar-alt"></i>
    </div>
  </div>
</template>

<script>
export default {
  name: 'DatetimePick',
  props: {
    modelValue: {
      type: String,
      default: ""
    },
    caption: {
      type: String
    },
    clock: {
      type: Number,
    },
    sec: {
      type: Number,
    }
  },
  data() {
    return {
      value: this.modelValue,
      thisId: 'picker-' + this.getUniqueStr(),
    }
  },
  watch: {
    modelValue: function (newVal, oldVal) {
      this.value = newVal;
    }
  },
  computed: {},
  mounted() {
    this.createDatetimepicker({id: this.thisId, clock: this.clock, sec: this.sec})
  },

  methods: {
    change(event) {
      this.value = event.currentTarget.value;
      let value = this.value;
      this.$emit('update:modelValue', this.value);
      this.$emit('change', this.value);
    }
    ,
    getUniqueStr() {
      return new Date().getTime().toString(16) + Math.floor(1000 * Math.random()).toString(16)
    }
    ,

    createDatetimepicker(options) {
      const id = options.id;
      const clock = !!options.clock
      const sec = !!options.sec
      const group = document.getElementById(id);
      const input = group.getElementsByTagName('input');
      const div = group.getElementsByTagName('div');

      const format = clock ? ('yyyy-MM-dd HH:mm' + (sec ? ':ss' : '')) : 'yyyy-MM-dd'

      input[0].setAttribute('data-target', '#' + id);
      div[0].setAttribute('data-target', '#' + id);

      return new tempusDominus.TempusDominus(group, {
        localization: {
          format: format,
          dayViewHeaderFormat: {year: 'numeric', month: 'long'},
          today: '本日',
          close: '閉じる',
          selectMonth: '月を選択',
          previousMonth: '前月',
          nextMonth: '次月',
          selectYear: '年を選択',
          previousYear: '前年',
          nextYear: '次年',
          selectDecade: '期間を選択',
          previousDecade: '前期間',
          nextDecade: '次期間',
          previousCentury: '前世紀',
          nextCentury: '次世紀',
          pickHour: '時間を取得',
          incrementHour: '時間を増加',
          decrementHour: '時間を減少',
          pickMinute: '分を取得',
          incrementMinute: '分を増加',
          decrementMinute: '分を減少',
          pickSecond: '秒を取得',
          incrementSecond: '秒を増加',
          decrementSecond: '秒を減少',
          toggleMeridiem: '午前/午後切替',
          selectTime: '時間を選択',
        },
        display: {
          components: {clock: clock},
          buttons: {close: true,},
        }
      });

    }
  }
}

</script>