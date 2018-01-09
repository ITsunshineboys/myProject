<template>
  <alert class="offline-alert" v-model="show">
    <slot name="default" class="alert-content">
      <i class="iconfont icon-close" @click="showFun"></i>
      <div>
        <p class="title">该店铺的地址</p>
        <p>{{offlineInfo.address}}</p>
        <p>{{offlineInfo.phone}}</p>
      </div>
      <div>
        <p class="title" v-if="isOffline">什么是线下体验店？</p>
        <p class="title" v-else>什么是线下体验商品？</p>
        <p>{{offlineInfo.desc}}</p>
      </div>
    </slot>
  </alert>
</template>

<script>
  import {Alert} from 'vux'

  export default {
    components: {
      Alert
    },
    props: {
      isOffline: {  // 判断是否是线下体验店，true 是体验店； false 是体验商品
        type: Boolean,
        default: true
      },
      show: { // 是否显示弹窗，默认不显示
        type: Boolean,
        default: false
      },
      /**
       * 体验店弹框信息
       * 对象请包含
       * {
       *   address: '',
       *   phone: '',
       *   desc: ''
       * }
       */
      offlineInfo: {
        type: Object,
        default: {}
      }
    },
    methods: {
      showFun () {
        this.$emit('isShow', false)
      }
    }
  }
</script>

<style>
  .offline-alert .weui-dialog__ft,
  .offline-alert .weui-dialog__hd {
    display: none;
  }

  .offline-alert .weui-dialog {
    max-width: 80%;
    height: auto;
  }

  .offline-alert .weui-dialog .weui-dialog__bd {
    position: relative;
    padding: 40px 20px 20px;
    height: inherit;
    text-align: start;
  }

  .offline-alert .weui-dialog .weui-dialog__bd > div:nth-child(2) {
    padding-bottom: 10px;
    border-bottom: 1px solid #E9EDEE;
  }

  .offline-alert .weui-dialog .weui-dialog__bd > div:nth-child(3) {
    padding-top: 10px;
  }

  .offline-alert .weui-dialog .weui-dialog__bd i {
    position: absolute;
    top: 10px;
    left: 9px;
    color: #222;
  }

  .offline-alert .alert-content p {
    font-size: 14px;
    color: #666666;
    line-height: 20px;
  }

  .offline-alert .title {
    margin-bottom: 8px;
    color: #2b2b2b;
  }
</style>
