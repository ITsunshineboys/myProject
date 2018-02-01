<template>
  <div class="download-container">
    <div class="download-content">
      <p>抱歉，此商品不支持无登录购买，请下载APP进行购买</p>
    </div>
  </div>
</template>

<script type="text/ecmascript-6">
  export default {
    name: 'Download',
    data () {
      return {}
    }
  }
</script>

<style scoped>
  .download-container {
    padding-top: 122px;
    background: rgba(246, 246, 246, 1);
  }

  .download-content p {
    font-size: 20px;
    color: rgba(102, 102, 102, 1);
    line-height: 28px;
  }
</style>
