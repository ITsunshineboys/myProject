<template>
  <div class="download-container">
    <v-header></v-header>
    <div class="download-content">
      <p>抱歉，此商品不支持无登录购买，请下载APP进行购买</p>
      <img :src="qrCode" alt="">
    </div>
  </div>
</template>

<script type="text/ecmascript-6">
  import vHeader from '@/components/HeaderSearch'
  export default {
    name: 'Download',
    components: {
      vHeader
    },
    data () {
      return {
        qrCode: require('../../assets/images/qr-code.png')
      }
    }
  }
</script>

<style scoped>
  .download-container {
    padding-top: 122px;
    background: rgba(246, 246, 246, 1);
  }

  .download-content {
    margin: 0 auto;
    width: 260px;
    height: 250px;
    text-align: center;
  }

  .download-content p {
    font-size: 20px;
    color: rgba(102, 102, 102, 1);
    line-height: 28px;
    margin-bottom: 50px;
  }

  .download-content img {
    width: 144px;
  }
</style>
