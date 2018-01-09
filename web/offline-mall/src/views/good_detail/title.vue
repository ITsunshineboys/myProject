<template>
  <div class="title-container">
    <p>{{title}}</p>
    <p>{{subtitle}}</p>
    <div class="price">
      <span>¥{{platform_price}}</span>
      <span v-show="show_offline" @click="show=true">线下体验商品</span>
    </div>


    <alert v-model="show" :hide-on-blur="true">
      <slot name="default" class="alert-content">
        <i class="iconfont icon-close" @click="show=false"></i>
        <div>
          <span>该店铺的地址</span>
          <p>四川省成都市蜀汉方式发送到方式发送到发送到发大水方法第三方</p>
        </div>
        <div>
          <span>什么是线下体验商品？</span>
          <p>四川省成都市蜀dasfdg对萨达撒发生的范德萨范德萨发范德萨发的说法师傅的说法撒</p>
        </div>
      </slot>
    </alert>
  </div>
</template>

<script type="text/ecmascript-6">
  import {Alert} from 'vux'
  export default {
    data () {
      return {
        show: false,
        sentence: 'dsaaaaaaaaaaaaaaaaaaa'
      }
    },
    props: ['title', 'subtitle', 'platform_price', 'show_offline'],
    components: {
      Alert
    }
  }
</script>

<style>
  .title-container {
    box-sizing: border-box;
    height: 120px;
    padding: 15px 14px 20px;
  }

  .title-container p:first-child {
    font-size: 18px;
    line-height: 20px;
    color: rgba(102, 102, 102, 1)
  }

  .title-container p:nth-child(2) {
    margin-top: 10px;
    font-size: 12px;
    line-height: 12px;
    color: rgba(153, 153, 153, 1);
  }

  .title-container .price {
    margin-top: 26px;
  }

  .title-container .price span:first-child {
    font-size: 24px;
    line-height: 19px;
    color: rgba(217, 173, 101, 1);
  }

  .title-container .price span:last-child {
    float: right;
    font-size: 12px;
    color: rgba(153, 153, 153, 1);
    line-height: 17px;
  }


  /*弹窗样式*/
  .title-container .weui-dialog__ft,
  .title-container .weui-dialog__hd {
    display: none;
  }

  .title-container .weui-dialog {
    width: 250px;
    height: 232px;
  }

  .title-container .weui-dialog .weui-dialog__bd {
    position: relative;
    padding: 40px 20px 20px;
    height: inherit;
    text-align: start;
  }

  .title-container .weui-dialog .weui-dialog__bd > div:nth-child(2) {
    padding-bottom: 10px;
    border-bottom: 1px solid #E9EDEE;
  }

  .title-container .weui-dialog .weui-dialog__bd > div:nth-child(3) {
    padding-top: 10px;
  }

  .title-container .weui-dialog .weui-dialog__bd i {
    position: absolute;
    top: 10px;
    left: 9px;
    color: #222;
  }

  .title-container .weui-dialog .weui-dialog__bd span {
    font-size: 14px;
    color: rgba(43, 43, 43, 1);
  }

  .title-container .weui-dialog .weui-dialog__bd p {
    font-size: 14px;
    color: rgba(102, 102, 102, 1);
    line-height: 20px;
  }


</style>
