<template>
  <div>
    <x-header :left-options="{backText: ''}">确认订单</x-header>
    <!--收货地址-->
    <div class="bg-white">
      <cell-box :link="{path:'/address'}">
        <i class="iconfont icon-blue1"></i>
        <span class="add_address">填写收货地址</span>
      </cell-box>
    </div>
    <!--商品图文详情-->
    <ShopDetails :shopObj="shopObj"></ShopDetails>
    <!--发票信息-->
    <div class="bg-white">
      <div class="cell-invoice">
        <group>
          <cell title ="发票信息" value="明细" is-link></cell>
        </group>
      </div>
      <!--付款方式-->
      <div class="cell-pay">
        <cell title="付款方式" value="在线支付"></cell>
      </div>
      <!--买家留言-->
      <div class="cell-buy">
        <group>
          <x-textarea :title="'买家留言'" :placeholder="'选填：对本次交易的说明'" :max=30 :show-counter="false" :rows="1" autosize></x-textarea>
        </group>
      </div>
    </div>
    <!--价格-->
    <div class="bg-white price-box">
      <div class="goods-price-box">
        <cell title="商品价格" :value="'￥'+market_price "></cell>
      </div>
      <div class="discount-price-box">
        <cell title="优惠价格" :value="'￥'+platform_price"></cell>
      </div>
      <div class="ship-cost-box">
        <cell  title="+运费" :value="'￥'+freight"></cell>
      </div>
        <cell>
          <span class="need-pay">需付款：</span>
          <span class="gold-color">￥{{allCost}}</span>
        </cell>
    </div>
    <!--去付款按钮-->
    <div class="bg-white">
      <div class="footer-box">
        <div class="total-box">
          <span class="total-txt">合计：</span>
          <span class="total-p">￥{{allCost}}</span>
        </div>
        <div class="to-pay-box">
          <span>去支付</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import { XHeader, Group, Cell, CellBox, XTextarea } from 'vux'
  import ShopDetails from './shop_details'
  export default {
    name: 'Order',
    components: {
      XHeader,
      Group,
      Cell,
      CellBox,
      XTextarea,
      ShopDetails
    },
    methods: {
    },
    created () {
      let that = this
      this.axios.get('/order/get-line-goods-info', {
        goods_id: 43,
        goods_num: 10
      }, function (res) {
        console.log(res)
        that.shop_name = res.data.shop_name // 店铺名称
        that.title = res.data.title // 商品名称
        that.cover_image = res.data.cover_image // 商品图片
        that.platform_price = res.data.platform_price // 商品价格
        that.goods_num = res.data.goods_num // 数量
        that.market_price = res.data.market_price // 优惠价
        that.freight = res.data.freight // 运费
        that.allCost = res.data.allCost // 总价
        that.shopObj = {
          shop_name: that.shop_name,
          title: that.title,
          cover_image: that.cover_image,
          platform_price: that.platform_price,
          goods_num: that.goods_num
        }
      })
    },
    data () {
      return {
        shopObj: {},
        shop_name: '',
        goods_name: '',
        cover_image: '',
        platform_price: '',
        goods_num: '',
        market_price: '',
        freight: '',
        allCost: ''
      }
    }
  }
</script>

<style>
  .add_address{
    margin-left: 10px;
  }
  .cell-invoice{
    margin: 10px 0 0 15px;
  }
  .cell-invoice .weui-cells,.price-box{
    margin-top: 10px;
  }
  .cell-invoice .weui-cell{
    padding: 10px 15px 10px 0;
  }
  .cell-buy .weui-cells{
    margin-top: 0;
  }
  .cell-invoice .weui-cell__ft,
  .cell-pay .weui-cell__ft,
  .cell-buy .weui-textarea,
  .goods-price-box .weui-cell__ft,
  .discount-price-box .weui-cell__ft,
  .ship-cost-box{
    font-size: 14px;
  }
  .cell-invoice .vux-label,
  .cell-pay .vux-label,
  .cell-buy .weui-label,
  .need-pay
  {
    color: #666;
  }
  .gold-color,
  .discount-price-box .weui-cell__ft{
    font-size: 14px;
    color: #D9AD65;
  }
  .goods-price-box .weui-cell__ft{
    text-decoration: line-through;
  }
  .ship-cost-box .weui-cell__ft{
    font-size: 12px;
  }
  .goods-price-box .vux-label,
  .discount-price-box .vux-label,
  .ship-cost-box .vux-label,
  .cell-buy .weui-textarea
  {
    color: #999;
  }
  .footer-box{
    height: 48px;
    line-height: 48px;
    position: fixed;
    bottom: 0;
    width: 100%;
    background-color: #fff;
  }
  .footer-box:after{
    content: '';
    display: table;
    clear: both;
  }
  .total-box{
    float: left;
    padding-left: 15px;
  }
  .total-txt{
    font-size: 14px;
    color: #999;
  }
  .total-p{
    font-size: 18px;
    color: #D9AD65;
  }
  .to-pay-box{
    float: right;
  }
  .to-pay-box span{
    display: inline-block;
    width: 100px;
    color: #fff;
    text-align: center;
    background-color: #D9AD65;
  }


</style>
