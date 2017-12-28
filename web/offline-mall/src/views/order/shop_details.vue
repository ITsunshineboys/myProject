<template>
    <div class="bg-white">
      <div class="shop_name">
        <i class="iconfont icon-Group"></i>
        <span>{{shopObj.shop_name}}</span>
      </div>
      <div class="goods_box">
        <div class="goods_left">
          <img :src="shopObj.cover_image" alt="">
        </div>
        <div class="goods_right">
          <div class="goods_title">
            <div>{{shopObj.title}}</div>
          </div>
          <div class="goods_details">
            <span class="goods_price">￥{{shopObj.platform_price}}</span>
            <span class="goods_num">x{{shopObj.goods_num}}</span>
          </div>
        </div>
      </div>
    </div>
</template>

<script>
  import { Flexbox, FlexboxItem } from 'vux'
  export default {
    name: 'shopDetails',
    components: {
      Flexbox,
      FlexboxItem
    },
    data () {
      return {}
    },
    props: {
      shopObj: Object
    }
  }
</script>

<style scoped>
  .shop_name{
    margin: 10px 0 0 15px;
    padding: 5px 0;
    border-bottom: 2px solid #E9EDEE;
  }
  .shop_name > span{
    color: #666666;
    font-size: 14px;
    margin-left: 5px;
  }
  .goods_box{
    height: 120px;
    padding: 15px;
  }
  .goods_left{
    width: 26%;
    height: 90px;
    float: left;
    text-align: center;
  }
  .goods_left>img{
    width: 100%;
    height: 100%;
  }
  .goods_right{
    width: 70%;
    float: right;
    height: 90px;
    position: relative;
  }
  .goods_title{
    font-size: 14px;
    color: #666;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
    overflow: hidden;
  }
  .goods_details{
    position: absolute;
    bottom: 0;
    width: 100%;
  }
  .goods_price{
    font-size: 14px;
    color: #D9AD65;
    float: left;
  }
  .goods_num{
    font-size: 14px;
    color: #999;
    float: right;
    margin-right: 5px;
  }
</style>
