<template>
  <label :class="className">
    <input type="checkbox" class="v-switch-input" :name="name" :checked="value" :disabled="disabled" @change.stop="toggle">
    <div class="v-switch-core" :style="coreStyle">
      <div class="v-switch-button" :style="buttonStyle"/>
    </div>
    <template v-if="labels">
    <span class="v-switch-label v-left" :style="labelStyle" v-if="toggled">
      <slot name="checked">
        <template>{{ labelChecked }}</template>
      </slot>
    </span>
      <span class="v-switch-label v-right" :style="labelStyle" v-else>
      <slot name="unchecked">
        <template>{{ labelUnchecked }}</template>
      </slot>
    </span>
    </template>
  </label>
</template>

<script>
const isString = (value) => {
  return typeof value === 'string'
}
const isBoolean = (value) => {
  return typeof value === 'boolean'
}
const isObject = (value) => {
  return typeof value === 'object'
}
const has = (object, key) => {
  return isObject(object) && object.hasOwnProperty(key)
}
const get = (object, key, defaultValue) => {
  return has(object, key) ? object[key] : defaultValue
}
const px = value => {
  return `${value}px`
}
const translate3d = (x, y, z = '0px') => {
  return `translate3d(${x}, ${y}, ${z})`
}


const DEFAULT_COLOR_CHECKED = '#75c791'
const DEFAULT_COLOR_UNCHECKED = '#bfcbd9'
const DEFAULT_LABEL_CHECKED = 'on'
const DEFAULT_LABEL_UNCHECKED = 'off'
const DEFAULT_SWITCH_COLOR = '#fff'

export default {
  name: 'ToggleButton',
  props: {
    modelValue: {type: Boolean, default: false},
    name: {type: String},
    disabled: {type: Boolean, default: false},
    tag: {type: String,},
    speed: {type: Number, default: 300},
    color: {
      type: [String, Object],
      validator(value) {
        return isString(value)
            || has(value, 'checked')
            || has(value, 'unchecked')
            || has(value, 'disabled')
      }
    },
    switchColor: {
      type: [String, Object],
      validator(value) {
        return isString(value)
            || has(value, 'checked')
            || has(value, 'unchecked')
      }
    },
    cssColors: {
      type: Boolean,
      default: false
    },
    labels: {
      type: [Boolean, Object],
      default: false,
      validator(value) {
        return typeof value === 'object'
            ? (value.checked || value.unchecked)
            : typeof value === 'boolean'
      }
    },
    height: {type: Number, default: 22},
    width: {type: Number, default: 50},
    margin: {type: Number, default: 3},
    fontSize: {type: Number}
  },
  computed: {
    className() {
      let {toggled, disabled} = this
      return ['vue-js-switch', {toggled, disabled}]
    },

    coreStyle() {
      return {
        width: px(this.width),
        height: px(this.height),
        backgroundColor: this.cssColors ? null : (this.disabled ? this.colorDisabled : this.colorCurrent),
        borderRadius: px(Math.round(this.height / 2))
      }
    },

    buttonRadius() {
      return this.height - this.margin * 2;
    },

    distance() {
      return px(this.width - this.height + this.margin)
    },

    buttonStyle() {
      const transition = `transform ${this.speed}ms`
      const margin = px(this.margin)

      const transform = this.toggled ? translate3d(this.distance, margin) : translate3d(margin, margin)

      const background = this.switchColor ? this.switchColorCurrent : null

      return {
        width: px(this.buttonRadius),
        height: px(this.buttonRadius),
        transition,
        transform,
        background
      }
    },

    labelStyle() {
      return {
        lineHeight: px(this.height),
        fontSize: this.fontSize ? px(this.fontSize) : null
      }
    },

    colorChecked() {
      let {color} = this

      if (!isObject(color)) {
        return color || DEFAULT_COLOR_CHECKED
      }

      return get(color, 'checked', DEFAULT_COLOR_CHECKED)
    },

    colorUnchecked() {
      return get(this.color, 'unchecked', DEFAULT_COLOR_UNCHECKED)
    },

    colorDisabled() {
      return get(this.color, 'disabled', this.colorCurrent)
    },

    colorCurrent() {
      return this.toggled ? this.colorChecked : this.colorUnchecked
    },

    labelChecked() {
      return get(this.labels, 'checked', DEFAULT_LABEL_CHECKED)
    },

    labelUnchecked() {
      return get(this.labels, 'unchecked', DEFAULT_LABEL_UNCHECKED)
    },

    switchColorChecked() {
      return get(this.switchColor, 'checked', DEFAULT_SWITCH_COLOR)
    },

    switchColorUnchecked() {
      return get(this.switchColor, 'unchecked', DEFAULT_SWITCH_COLOR)
    },

    switchColorCurrent() {
      let {switchColor} = this

      if (!isObject(this.switchColor)) {
        return this.switchColor || DEFAULT_SWITCH_COLOR
      }

      return this.toggled ? this.switchColorChecked : this.switchColorUnchecked
    },



  },
  watch: {
    modelValue(value) {
      this.toggled = this.toBool(value)
    }
  },
  data() {
    return {
      toggled: this.toBool(this.modelValue)
    }
  },
  methods: {
    toggle(event) {
      this.toggled = !this.toBool(this.toggled);

      let value = this.toggled ? '1' : '0';
      this.$emit('update:modelValue', value);
      this.$emit('change', {
        modelValue: value,
        tag: this.tag,
        srcEvent: event,
      });

    },
    toBool(value) {
      if (value === 0) return false
      if (value === "0") return false
      if (value === false) return false
      if (value === "undefined") return false
      return true;
    },
  }
}

</script>


<style>
.v-switch-input {
  opacity: 0;
  position: absolute;
  width: 1px;
  height: 1px;
}

.v-switch-label {
  position: absolute;
  top: 0;
  font-weight: 600;
  color: white;
  z-index: 1;

}

.vue-js-switch {
  display: inline-block;
  position: relative;
  vertical-align: middle;
  user-select: none;
  font-size: 10px;
  cursor: pointer;
}


.v-switch-label.v-left {
  left: 10px;
}

.v-switch-label.v-right {
  right: 10px;
}

.v-switch-core {
  display: block;
  position: relative;
  box-sizing: border-box;

  outline: 0;
  margin: 0;

  transition: border-color .3s, background-color .3s;
  user-select: none;

  .v-switch-button {
    display: block;
    position: absolute;
    overflow: hidden;

    top: 0;
    left: 0;

    border-radius: 100%;
    background-color: #fff;
    z-index: 2;
  }
}

.vue-js-switch.disabled {
  pointer-events: none;
  opacity: 0.6;
}

</style>

