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
          <cell class="invoice-content" title ="发票信息"  :link="{path:'/invoice'}">
            <span v-if="!!invoice_header" slot="default">明细-{{invoice_header}}</span>
            <span v-else="!invoice_header" slot="default">明细</span>
          </cell>
        </group>
      </div>
      <!--付款方式-->
      <div class="cell-pay">
        <cell title="付款方式" value="在线支付"></cell>
      </div>
      <!--买家留言-->
      <div class="cell-buy">
        <group>
          <x-textarea v-model="buyer_message" :title="'买家留言'" :placeholder="'选填：对本次交易的说明'" :max=30 :show-counter="false" :rows="1" autosize></x-textarea>
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
    <!--商城购买协议-->
    <div class="agreement-box">
        <input type="checkbox" v-model="checked_flag" >
        <span>已同意</span>
        <a>《商城购买协议》</a>
    </div>
    <!--去付款按钮-->
    <div class="bg-white">
      <div class="footer-box">
        <div class="total-box">
          <span class="total-txt">合计：</span>
          <span class="total-p">￥{{allCost}}</span>
        </div>
        <div class="to-pay-box" @click="toPay">
          <span>去支付</span>
        </div>
      </div>
    </div>
    <!--模态框-->
    <Modal :modalStatus="modalStatus"></Modal>
  </div>
</template>

<script>
  import { XHeader, Group, Cell, CellBox, XTextarea } from 'vux'
  import ShopDetails from './shop_details'
  import Modal from '../order/modal'
  export default {
    name: 'Order',
    components: {
      XHeader,
      Group,
      Cell,
      CellBox,
      XTextarea,
      ShopDetails,
      Modal
    },
    data () {
      return {
        shopObj: {}, // 店铺商品信息
        modalStatus: {}, // 模态框
        consigneeFlag: true, // 有无收货地址状态
        shop_name: '', // 店铺名称
        goods_name: '', // 商品名称
        cover_image: '', // 封面图
        platform_price: '', // 平台价 优惠价
        goods_num: '',  // 数量
        market_price: '', // 市场价
        freight: '', // 运费
        allCost: '', // 需付款金额
        consignee: '', // 收货人
        head_number: '', // 手机号前3位
        foot_number: '', // 手机号后4位
        addressValue: '', // 所属区域
        adCode: '', // 所属区域的区号
        detailAddress: '', // 详细地址
        invoice_header: '', // 抬头
        buyer_message: '', // 留言
        checked_flag: false, // 是否勾选“商城购买协议”
        paymentMethod: '' // 支付方式
      }
    },
    methods: {
      toPay () {
        if (!this.consigneeFlag) {      // 已填写收货地址
          this.axios.get('/order/judge-address', {
            goods_id: 43,
            district_code: this.adCode
          }, (res) => {
            console.log(res)
            if (res.code === 200) {
              if (this.checked_flag) {                  // 已勾选协议
                if (this.paymentMethod) {               // 微信支付
                  console.log('微信支付')
                } else {        // 支付宝支付
                  console.log('支付宝支付 ')
                  this.axios.post('/order/order-line-ali-pay', {
                    order_price: this.allCost,
                    goods_id: 43,
                    goods_num: 10,
                    address_id: sessionStorage.getItem('address_id'),
                    invoice_id: sessionStorage.getItem('invoice_id  '),
                    freight: this.freight,
                    buyer_message: this.buyer_message
                  }, (res) => {
                    console.log(res)
                    const div = document.createElement('div') // 创建div
                    div.innerHTML = res // 将返回的form 放入div
                    document.body.appendChild(div)
                    document.forms[0].submit()
                  })
                }
              } else {
                this.modalStatus = {
                  error_status: true,
                  dialogTitle: '请先勾选购买协议'
                }
              }
            } else {
              this.modalStatus = {            // 所填区域不在配送范围内
                error_status: true,
                dialogContent: '你好，你购买的［' + this.goods_name + '］属于区域销售商品，你所选择的收货地址不在配送范围内，请更换商品或收货地址！'
              }
            }
          })
        } else {                        // 未填写收货地址
          this.modalStatus = {
            error_status: true,
            dialogTitle: '请填写收货地址'
          }
        }
      }
    },
    activated () {
      this.axios.get('/order/get-line-goods-info', {
        goods_id: 43,
        goods_num: 10
      }, (res) => {
        console.log(res)
        this.shop_name = res.data.shop_name // 店铺名称
        this.goods_name = res.data.title // 商品名称
        this.cover_image = res.data.cover_image // 商品图片
        this.platform_price = res.data.platform_price // 商品价格
        this.goods_num = res.data.goods_num // 数量
        this.market_price = res.data.market_price // 优惠价
        this.freight = res.data.freight // 运费
        this.allCost = res.data.allCost // 总价
        this.shopObj = {
          shop_name: this.shop_name,
          title: this.goods_name,
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
          this.adCode = res.data.adCode
        })
      } else {
        this.consigneeFlag = true
      }
      // 判断有无发票信息
      if (sessionStorage.getItem('invoice_id')) {
        this.axios.get('/order/get-line-order-invoice-data', {
          invoice_id: sessionStorage.getItem('invoice_id')
        }, (res) => {
          console.log(res)
          this.invoice_header = res.data.invoice_header
        })
      }
      // 判断是微信还是浏览器
      this.axios.get('/order/iswxlogin', {}, (res) => {
        res.code === 200 ? this.paymentMethod = true : this.paymentMethod = false
      })
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
    padding-left: 40px;
  }
  .consignee-box .add_address{
    color: #999!important;
  }
  .consignee-box .vux-cell-box:before{
    left: 0!important;
    border: 0!important;
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
  .need-pay {
    color: #666;
  }
  .cell-invoice .vux-label,
  .cell-pay .vux-label,
  .cell-buy .weui-label,
  .goods-price-box .vux-label,
  .discount-price-box .vux-label {
    font-size: 16px;
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
  .invoice-content .weui-cell__ft{
    display: inline-block;
    width: 75%;
  }
  .cell-buy .weui-textarea{
    height: 24px;
    line-height: 24px;
  }
  /*底部*/
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
  /*购买协议*/
  .agreement-box{
    margin-bottom: 48px;
    padding: 14px
  }
  .agreement-box > input {
    width: 12px;
    height: 12px;
    border-radius: 2px;
  }
  .agreement-box > span {
    font-size: 12px;
    color: #7c7c7c;
  }
  .agreement-box > a {
    font-size: 12px;
    color: #222;
  }
</style>
