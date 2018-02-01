<template>
  <div class="goods-list">
    <div class="goods-item" v-for="obj in goodsList"  :key="obj.id" @click="toDetail(obj.id)">
      <div class="goods-item-img">
        <img :src="obj.cover_image">
      </div>
      <div class="goods-item-text">
        <p class="goods-item-title">{{obj.title}}</p>
        <p class="goods-item-desc">{{obj.subtitle}}</p>
        <p class="goods-item-data">
          <span>总销量{{obj.sold_number || 0}}</span><span>{{obj.favourable_comment_rate}}%好评</span>
        </p>
        <p class="goods-item-price">￥{{obj.platform_price}}</p>
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    props: {
      goodsList: {
        type: Array,
        default: []
      }
    },
    methods: {
      toDetail (id) {
        this.$router.push('/good-detail/' + id)
        this.$emit('recordScroll')
      }
    }
  }
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
