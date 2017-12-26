<template>
  <div>
    <x-header :left-options="{backText: ''}">确认订单</x-header>
    <!--收货地址-->
    <div class="bg-white">
      <!--  没有填写收货地址-- 显示   -->
      <cell-box :link="{path:'/address'}" v-if= consigneeFlag>
        <i class="iconfont icon-location"></i>
        <span class="add_address">填写收货地址</span>
      </cell-box>
      <!--  已经填写过收货地址---显示 -->
      <div class="consignee-box" v-else = !consigneeFlag>
        <div class="consignee-top">
          <span>收货人：{{consignee}}</span><span style="margin-left: 20px">{{head_number}}****{{foot_number}}</span>
        </div>
        <cell-box :link="{path:'/address'}">
          <i class="iconfont icon-location"></i>
          <span class="add_address">地址：{{addressValue}}{{detailAddress}}</span>
        </cell-box>
      </div>
    </div>
    <!--商品图文详情-->
    <ShopDetails :shopObj="shopObj"></ShopDetails>
    <!--发票信息-->
    <div class="bg-white">
      <div class="cell-invoice">
        <group>
          <cell title ="发票信息" value="明细" :link="{path:'/invoice'}"></cell>
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
    data () {
      return {
        consigneeFlag: true,
        shopObj: {},
        shop_name: '',
        goods_name: '',
        cover_image: '',
        platform_price: '',
        goods_num: '',
        market_price: '',
        freight: '',
        allCost: '',
        consignee: '',
        head_number: '',
        foot_number: '',
        addressValue: '',
        detailAddress: '',
        str: ''
      }
    },
    methods: {
    },
    activated () {
      this.axios.get('/order/get-line-goods-info', {
        goods_id: 43,
        goods_num: 10
      }, (res) => {
        console.log(res)
        this.shop_name = res.data.shop_name // 店铺名称
        this.title = res.data.title // 商品名称
        this.cover_image = res.data.cover_image // 商品图片
        this.platform_price = res.data.platform_price // 商品价格
        this.goods_num = res.data.goods_num // 数量
        this.market_price = res.data.market_price // 优惠价
        this.freight = res.data.freight // 运费
        this.allCost = res.data.allCost // 总价
        this.shopObj = {
          shop_name: this.shop_name,
          title: this.title,
          cover_image: this.cover_image,
          platform_price: this.platform_price,
          goods_num: this.goods_num
        }
      })
      // 判断有无收货地址
      if (sessionStorage.getItem('address_id')) {
        this.consigneeFlag = false
        this.axios.get('/order/get-line-receive-address', {
          address_id: sessionStorage.getItem('address_id')
        }, (res) => {
          console.log(res)
          this.consignee = res.data.consignee
          this.phoneNumber = res.data.mobile
          this.head_number = this.phoneNumber.substring(0, 3)
          this.foot_number = this.phoneNumber.substring(7)
          this.addressValue = res.data.district.replace(/,/g, '')
          this.detailAddress = res.data.region
        })
      } else {
        this.consigneeFlag = true
      }
      // 判断有无发票信息
      if (sessionStorage.getItem('address_id')) {

      }
    }
  }
</script>

<style>
  .consignee-box{
    font-size: 14px;
    color: #666;
  }
  .consignee-box .vux-cell-box{
    padding-top: 0!important;
    padding-bottom: 0!important;
  }
  .consignee-top{
    padding-left: 41px;
  }
  .consignee-box .add_address{
    color: #999!important;
  }
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
  .vux-cell-box:before{
    border: 0!important;
  }



</style>
