<template>
  <div class="goods-list">
    <div class="goods-item" v-for="i in 10">
      <div class="goods-item-img">
        <img src="http://service.cdlhzz.cn/static/image/2017/12/18/1513588491053.jpg">
      </div>
      <div class="goods-item-text">
        <p class="goods-item-title">标题标题标题标题标题标题标题标题标题标题标题标题标题标题标题标题标题标题</p>
        <p class="goods-item-desc">表述表述表述表述表述表述表述表述表述表述表述表述表述表述表述表述表述表述表述表述表述表述表述表述</p>
        <p class="goods-item-data">
          <span>总销量242</span><span>95%好评</span>
        </p>
        <p class="goods-item-price">￥500.00</p>
      </div>
    </div>
  </div>
</template>

<script>
    export default {}
</script>

<style scoped>
  .goods-list {
    padding: 14px;
  }

  .goods-item {
    display: flex;
    margin-top: 14px;
    padding-top: 14px;
    border-top: 1px solid #e9edee;
  }

  .goods-item:first-child {
    margin-top: 0;
    padding-top: 0;
    border-top: none;
  }

  .goods-item-img {
    width: 88px;
    height: 88px;
    flex: 0 0 auto;
  }

  .goods-item-img img {
    width: 100%;
    height: 100%;
  }

  .goods-item-text {
    margin-left: 14px;
    flex: 1 1 auto;
    overflow: hidden;
  }

  .goods-item-title {
    color: #666;
  }

  .goods-item-title,
  .goods-item-desc {
    white-space: nowrap;
    text-overflow: ellipsis;
    overflow: hidden;
  }

  .goods-item-desc,
  .goods-item-data {
    color: #999999;
  }

  .goods-item-desc,
  .goods-item-price {
    font-size: 14px;
  }

  .goods-item-data {
    font-size: 12px;
  }

  .goods-item-data span:first-child {
    margin-right: 20px;
  }

  .goods-item-price {
    color: #D9AD65;
  }
</style>
