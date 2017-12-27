<template>
  <div class="title-container">
    <p>{{title}}</p>
    <p>{{subtitle}}</p>
    <div>
      <span>¥{{platform_price}}</span>
      <span v-show="show_offline">线下体验商品</span>
    </div>
  </div>
</template>

<script type="text/ecmascript-6">
  export default {
    props: ['title', 'subtitle', 'platform_price', 'show_offline']
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

  .title-container div {
    margin-top: 26px;
  }

  .title-container div span:first-child {
    font-size: 24px;
    line-height: 19px;
    color: rgba(217, 173, 101, 1);
  }

  .title-container div span:last-child {
    float: right;
    font-size: 12px;
    color: rgba(153, 153, 153, 1);
    line-height: 17px;
  }


</style>
