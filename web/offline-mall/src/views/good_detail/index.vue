<template>
  <div class="good-container">
    <!-- 顶部icon -->
    <div class="guide-icon">
      <i class="iconfont icon-return" @click="goHome"></i>
      <i class="iconfont icon-share" @click="androidShare"></i>
      <i class="iconfont icon-more" @click.passive="showMore"></i>
    </div>

    <!--更多弹窗-->
    <div v-if="show_more" class="pop-down">
      <ul>
        <router-link :to="'/'" tag="li">
          <span class="iconfont icon-home"></span>
          <span class="pop-text">商城首页</span>
        </router-link>
        <li @click="androidMessageCenter">
          <span class="iconfont icon-news-circle"></span>
          <span class="pop-text">消息</span>
          <span class="pop-dot" v-show="msgFlag && show_msgdot"></span>
        </li>
      </ul>
    </div>

    <!--轮播-->
    <swiper loop auto :list="banner_list" height="375px" dots-class="bottom-dots" dots-position="center" :show-desc-mask="false"></swiper>
    <div class="good-detail">
      <div class="title-container">
        <p>{{good_detail.title}}</p>
        <p>{{good_detail.subtitle}}</p>
        <div class="price">
          <span>¥{{good_detail.platform_price}}</span>
          <span v-show="good_detail.line_goods.is_offline_goods === '否' ? false:true" @click="show_offlinegood=true">线下体验商品</span>
        </div>
      </div>
      <divider></divider>
      <group>
        <cell-box is-link class="choose-count" @click.native="showCount('count')">
          选择数量
        </cell-box>
        <cell-box is-link @click.native="show_after_service = true">
          <div class="service" v-for="item in after_sale_services">
            <i class="iconfont icon-checkbox-circle-line"></i>
            <span>{{item.title}}</span>
          </div>
        </cell-box>
      </group>
      <divider></divider>
      <!--评价-->
      <div v-if="good_detail.comments.total!=='0'?true:false">
        <router-link :to="'/all-comment/' + good_id">
          <flexbox justify="flex-start" class="comment-count">
            <span class="sum-comment">评价</span>
            <span>({{good_detail.comments.total}})</span>
          </flexbox>
        </router-link>
        <router-link :to="'/all-comment/' + good_id">
          <comment :userName="user_name" :userIcon="good_detail.comments.latest.icon || user_icon"
                   :commentDate="good_detail.comments.latest.create_time"
                   :content="good_detail.comments.latest.content"></comment>
        </router-link>
        <flexbox justify="center" class="view-all">
          <router-link :to="'/all-comment/' + good_id">
           <span>
            查看全部评价
          </span>
            <i class="iconfont icon-arrow-line-right"></i>
          </router-link>
        </flexbox>
        <divider></divider>
      </div>

      <!--店铺简介-->
      <card class="shop-card">
        <flexbox slot="header" justify="flex-start" align="center">
          <img :src="good_detail.supplier.icon" alt="">
          <span>{{good_detail.supplier.shop_name}}</span>
        </flexbox>
        <flexbox slot="content" justify="space-between" class="shop-intro">
          <div>
            <span>{{good_detail.supplier.goods_number}}</span><br/>商品数

          </div>
          <span></span>
          <div>
            <span>{{good_detail.supplier.follower_number}}</span><br/>粉丝数

          </div>
          <span></span>
          <div>
            <span>{{good_detail.supplier.comprehensive_score}}</span><br/>综合评分

          </div>
        </flexbox>
        <flexbox slot="footer" justify="center" class="view-shop-btn">
          <router-link :to="'/store/' + good_detail.supplier.id">
            <button type="button">进店逛逛</button>
          </router-link>
        </flexbox>
      </card>
      <divider></divider>

      <!--图文详情选项卡-->
      <div class="graphic-detail-container">
        <tab defaultColor="#999" active-color="#222" bar-active-color="#222" custom-bar-width="50px" class="tab">
          <tab-item selected @on-item-click="tabHandler('des')">图文详情</tab-item>
          <tab-item @on-item-click="tabHandler('params')">产品参数</tab-item>
        </tab>
        <!-- 图文详情 -->
        <div v-show="show_description" class="description-container">
          <flexbox>
            <flexbox-item class="description" v-html="good_detail.description"></flexbox-item>
          </flexbox>
        </div>
        <!-- 产品参数 -->
        <div v-show="!show_description">
          <flexbox orient="vertical" class="pro-params" align="flex-start">
            <flexbox-item>
              <flexbox justify="space-between">
                <flexbox-item>产品编码</flexbox-item>
                <flexbox-item :span="257/375" class="attr-content">{{good_detail.sku}}</flexbox-item>
              </flexbox>
            </flexbox-item>
            <flexbox-item>
              <flexbox justify="space-between">
                <flexbox-item>产品品牌</flexbox-item>
                <flexbox-item :span="257/375" class="attr-content">{{good_detail.brand_name}}</flexbox-item>
              </flexbox>
            </flexbox-item>
            <flexbox-item>
              <flexbox justify="space-between" v-if="good_detail.series_name">
                <flexbox-item>系列</flexbox-item>
                <flexbox-item :span="257/375" class="attr-content">{{good_detail.series_name}}</flexbox-item>
              </flexbox>
            </flexbox-item>
            <flexbox-item v-if="good_detail.style_name">
              <flexbox justify="space-between">
                <flexbox-item>风格</flexbox-item>
                <flexbox-item :span="257/375" class="attr-content">{{good_detail.style_name}}</flexbox-item>
              </flexbox>
            </flexbox-item>
            <flexbox-item v-if="good_detail.attr!==''">
              <flexbox justify="space-between" v-for="item in good_detail.attrs" :key="item.id">
                <flexbox-item>{{item.name}}</flexbox-item>
                <flexbox-item :span="257/375" class="attr-content">{{item.value}}</flexbox-item>
              </flexbox>
            </flexbox-item>
          </flexbox>
          <divider></divider>
        </div>
      </div>

      <!--底部按钮-->
      <flexbox class="bottom-tabbar">
        <flexbox-item @click.native="contactShop" :span="76/375">
          <i class="iconfont icon-service1"></i><br/>联系商家

        </flexbox-item>
        <span></span>
        <flexbox-item @click.native="androidCart" :span="77/375">
          <i class="iconfont icon-cart"></i><br/>购物车

        </flexbox-item>
        <flexbox-item @click.native="bottomAdd('cart')" :span="110/375">
          加入购物车

        </flexbox-item>
        <flexbox-item @click.native="bottomAdd('now')" :span="110/375">
          立即购买

        </flexbox-item>
      </flexbox>
    </div>

    <!-- 选择数量弹窗 -->
    <popup v-model="show_count" @on-hide="showCount('all')">
      <div>
        <group>
          <div class="count-top">
            <div class="count-cover-img">
              <img :src="good_detail.cover_image" alt="封面图">
            </div>
            <div class="count-price-sum">
              <p>¥{{good_detail.platform_price}}</p>
              <span>库存{{good_detail.left_number}}件</span>
            </div>
            <i class="iconfont icon-close" @click="showCount('all')"></i>
          </div>
          <group>
            <x-number class="buy-count" title="购买数量" v-model="count" :fillable="true" :max="good_detail.left_number"
                      :min="1" width="34px"></x-number>
          </group>
        </group>
        <flexbox class="count-bottom-btn">
          <flexbox-item alt="cart" v-if="count_cart||default_count" @click.native="addCart">
            加入购物车

          </flexbox-item>
          <flexbox-item alt="now" v-if="count_now||default_count" @click.native="buyNow">
            立即购买

          </flexbox-item>
        </flexbox>
      </div>
    </popup>

    <!--售后保障弹窗 -->
    <popup v-model="show_after_service" height="100%" class="after-service-container">
      <div>
        <flexbox orient="vertical" justify="space-between" style="-webkit-overflow-scrolling: touch;">
          <flexbox-item>
            <div class="after-service" v-if="pop.show_service">
              <p>售后</p>
              <div v-for="item in all_after_sale_services" v-if="afterservice_arr.indexOf(item.title) !== -1"
                   class="after-service-item">
                <i class="iconfont icon-checkbox-circle-line"></i>
                <span>{{item.title}}</span>
                <p>{{item.description}}</p>
              </div>
            </div>
            <div class="after-service safe-guard">
              <p>保障</p>
              <div v-for="item in all_after_sale_services" v-if="safeguard_arr.indexOf(item.title) !== -1"
                   class="after-service-item">
                <i class="iconfont icon-checkbox-circle-line"></i>
                <span>{{item.title}}</span>
                <p>{{item.description}}</p>
              </div>
            </div>
          </flexbox-item>
          <flexbox-item class="after-service-done-btn" @click.native="show_after_service = false">完成</flexbox-item>
        </flexbox>
      </div>
    </popup>

    <!--线下商品介绍弹窗-->
    <offlinealert :offlineInfo="offlineInfo" :show="show_offlinegood" :isOffline="false" @isShow="showOfflineAlert"></offlinealert>

    <!--加入购物车成功提示-->
    <toast v-model="show_cart_success" width="200px" position="middle" :time="2000" :is-show-mask="true">
      <i class="iconfont icon-checkbox-circle-chec"></i>
      <p>成功添加到购物车</p>
    </toast>

    <!--库存不足弹窗-->
    <alert class="goodshort-alert" v-model="show_goodshort_alert" :hide-on-blur="true">
      <slot name="default" class="alert-content">
        <div>库存不足请选择其他商品</div>
        <div @click="goodshortSure()">确认</div>
      </slot>
    </alert>

    <!--商品已下架提示-->
    <popup class="offline-warning" v-model="show_offline" position="bottom" height="49px" :hide-on-blur="true" :show-mask="false">
      <div>该商品已下架</div>
    </popup>
  </div>
</template>

<script>
  import {
    Swiper,
    Group,
    Cell,
    CellBox,
    Flexbox,
    FlexboxItem,
    Card,
    Tab,
    TabItem,
    Popup,
    XNumber,
    Toast,
    Alert
  } from 'vux'
  import divider from '@/components/Divider'
  import comment from '../good_detail/comment'
  import offlinealert from '@/components/OfflineAlert'
  const afterserviceArr = ['上门维修', '上门退货', '上门换货', '退货', '换货']

  export default {
    name: 'GoodDetail',
    components: {
      Swiper,
      Group,
      Cell,
      CellBox,
      Flexbox,
      FlexboxItem,
      Card,
      Tab,
      TabItem,
      Popup,
      XNumber,
      Toast,
      Alert,
      divider,
      comment,
      offlinealert
    },
    data () {
      return {
        isFromAndroid: false,       // 由安卓页面跳转至此页面
        good_id: '',                // 商品id
        role_id: 6,                 // 角色id
        show_more: false,           // 更多弹窗
        show_count: false,          // 选择数量弹窗
        show_after_service: false,  // 售后弹窗
        pop: {
          show_service: false       // 默认不显示售后
        },
        show_offlinegood: false,    // 线下商品简介
        show_goodshort_alert: false, // 商品不足弹窗
        show_offline: false,        // 商品下架提示
        show_cart_success: false,   // 添加购物车成功toast
        count_cart: false,          // 加入购物车按钮显示
        count_now: false,           // 立即购买按钮显示
        default_count: false,       // 两个按钮显示
        show_description: true,     // 显示图文详情
        show_msgdot: false,         // 默认不显示消息提示点
        msgFlag: false,              // 消息中心默认按未登录显示
        good_detail: {
          line_goods: {
            is_offline_goods: ''
          },
          comments: {
            total: 0,
            latest: {}
          },
          supplier: {},
          attrs: '',
          status: 2 // 默认商品状态：已上架
        },
        offlineInfo: {
          address: '大环境的撒谎就开会开会',
          phone: '1231545412312',
          desc: '对萨达撒打撒多撒'
        },
        banner_list: [],
        goodcookie: '',
        after_sale_services: [],      // 页面售后显示
        user_name: '',
        count: 1,                     // 选择数量默认值
        all_after_sale_services: [],
        checkin: 1,                    // 是否登录默认登录
        afterservice_arr: ['上门维修', '上门退货', '上门换货', '退货', '换货'],
        safeguard_arr: ['提供发票', '上门安装'],
        user_icon: require('../../assets/images/user_icon_default.png') // 默认用户头像
      }
    },
    created () {
      window.scrollTo(0, 0)
      if (this.$route.query.system === 'android') this.isFromAndroid = true
      this.good_id = this.$route.params.id // 商品id
      this.axios.get('/mall/goods-view', {id: this.good_id}, (res) => {
        this.good_detail = res.data.goods_view
        // 用户名处理
        if (this.good_detail.comments.total !== '0') {
          // 用户名长度处理
          if (this.good_detail.comments.latest.name.length > 6) {
            this.user_name = this.good_detail.comments.latest.name.substr(0, 6) + '...'
          } else {
            this.user_name = this.good_detail.comments.latest.name
          }
        }

        this.offlineInfo = {
          address: this.good_detail.line_goods.line_district,
          phone: this.good_detail.line_goods.line_mobile,
          desc: '线下体验店商品是线下门店销售的可供消费者体验的产品，每个体验店的产品都不尽相同，消费者可提前在网上查询各体验店的商品种类'
        }

        // 售后弹窗显示处理
        this.all_after_sale_services = this.good_detail.after_sale_services
        this.good_detail.after_sale_services.length > 3 ? this.after_sale_services = this.good_detail.after_sale_services.slice(0, 3) : this.after_sale_services = this.good_detail.after_sale_services // 页面售后显示内容

        // 轮播图处理
        const imgList = this.good_detail.images  // 轮播图数组
        imgList.splice(0, 0, this.good_detail.cover_image)
        this.banner_list = imgList.map((item) => ({
          img: item
        }))
        this.afterServiceShow()
      })
    },
    methods: {
      // 返回上一页面
      goHome () {
        if (this.isFromAndroid) {
          window.AndroidWebView.webfinish()
          return
        }
        this.$router.go(-1)
      },
      // 判断是否显示售后
      afterServiceShow () {
        for (let [key, value] of this.all_after_sale_services.entries()) {    // eslint-disable-line
          if (afterserviceArr.indexOf(value.title) !== -1) {
            this.pop.show_service = true
          }
        }
      },
      // 图片详情 选项卡切换
      tabHandler: function (obj) {
        obj === 'des' ? this.show_description = true : this.show_description = false
      },

      // 选择数量 弹出层按钮显示处理
      showCount: function (obj) {
        if (obj === 'all') {
          this.show_count = false
          this.count_cart = false
          this.count_now = false
          this.default_count = false
          this.count = 1
          this.watchCount()
        } else {
          this.show_count = true
          obj === 'cart' ? this.count_cart = true : obj === 'now' ? this.count_now = true : this.default_count = true
          this.watchCount = this.$watch('count', function (newVal, oldVal) {
            if (newVal === oldVal) return
            let reg = /^[1-9]*[1-9][0-9]*$/
            if (!reg.test(newVal) || newVal > this.good_detail.left_number) {
              this.count = ''
            }
          }
          )
        }
      },
      // 线下商品弹窗处理
      showOfflineAlert: function (bool) {
        this.show_offlinegood = bool
      },
      // 更多
      showMore () {
        this.show_more = !this.show_more
        if (this.show_more) {
          this.axios.get('/site/check-is-login', {}, (res) => {
            if (res.code === 403) {
              this.msgFlag = false
            } else {
              this.msgFlag = true
              this.axios.get('/site/user-check-news', {}, (res) => {
                res.data === 1 ? this.show_msgdot = true : this.show_msgdot = false
              })
            }
          })
        }
      },
      // 底部按钮加入购物车&立即购买
      bottomAdd: function (obj) {
        // 判断商品的上下架状态
        this.axios.get('/mall/goods-view', {id: this.good_id}, (res) => {
          this.good_detail.status = res.data.goods_view.status
          this.good_detail.left_number = res.data.goods_view.left_number
          if (this.good_detail.status !== 2) {
            this.show_offline = true  // 商品下架提示
            setTimeout(() => {
              this.show_offline = false
            }, 1500)
          } else if (!this.good_detail.left_number) {
            this.show_goodshort_alert = true // 商品不足弹窗
          } else {
            this.showCount(obj)
          }
        })
      },
      // 库存不足确认
      goodshortSure () {
        this.show_goodshort_alert = false
      },
      // 添加购物车
      addCart () {
        this.axios.get('/mall/goods-view', {id: this.good_id}, (res) => {
          this.good_detail.status = res.data.goods_view.status
          this.good_detail.left_number = res.data.goods_view.left_number
          if (this.good_detail.status !== 2) {
            this.show_offline = true  // 商品下架
            setTimeout(() => {
              this.show_offline = false
            }, 1500)
          } else if (!this.good_detail.left_number) {   //  库存不足
            this.showCount('all')
            this.show_goodshort_alert = true
          } else {
            this.axios.post('/order/add-shipping-cart', {
              goods_id: this.good_id,
              goods_num: this.count
            }, (res) => {
              if (res.code === 200) {
                this.showCount('all')
                this.show_cart_success = true // 购物车添加成功弹窗显示
                window.AndroidWebView.showInfoFromJs(res.data)
              }
            })
          }
        })
      },
      // 弹窗中的立即购买
      buyNow () {
        this.axios.get('/mall/goods-view', {id: this.good_id}, (res) => {
          this.good_detail.status = res.data.goods_view.status
          this.good_detail.left_number = res.data.goods_view.left_number
          if (this.good_detail.status !== 2) {
            this.show_offline = true  // 商品下架
            setTimeout(() => {
              this.show_offline = false
            }, 1500)
          } else if (!this.good_detail.left_number) { //  库存不足
            this.showCount('all')
            this.show_goodshort_alert = true
          } else {
            /* params
             * 商品id 购买数量
             * */
            let tempCount = this.count
            this.showCount('all')
            window.AndroidWebView.skipIntent(this.good_id, tempCount)
          }
        })
      },
      // 分享
      androidShare () {
        window.AndroidWebView.share('我在【艾特生活】分享了一款好物给你，点击链接查看详情【' + this.good_detail.title + '　　' + this.good_detail.subtitle + '】' + location.href)
      },
      // 跳转消息中心
      androidMessageCenter () {
        this.show_more = false
        if (this.msgFlag) {
          window.AndroidWebView.skipMessageCenter()
        } else {
          window.AndroidWebView.storeskipIntent()
        }
      },
      // 跳转购物车
      androidCart () {
        window.AndroidWebView.skipShopCart()
      },
      // 联系商家
      contactShop () {
        this.contactStore(this.good_detail.supplier.uid, this.role_id)
      }
    }
  }
</script>

<style>
  .good-container {
    background: rgba(255, 255, 255, 1);
    position: relative;
  }

  /*图标样式 start*/
  .good-container .guide-icon {
    position: relative;
  }

  .good-container .guide-icon .iconfont {
    position: fixed;
    top: 14px;
    z-index: 200;
    font-size: 15px;
    color: #FFFFFF;
  }

  .good-container .guide-icon .iconfont:after {
    position: absolute;
    content: '';
    left: -7px;
    top: -2px;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: rgba(73, 73, 73, 1);
    z-index: -1;
  }

  .good-container .guide-icon .icon-return {
    left: 14px;
    font-size: 10px;
  }

  .good-container .guide-icon .icon-return:before {
    display: block;
    margin: 3px 1px;
  }

  .good-container .guide-icon .icon-share {
    right: 54px;
  }

  .good-container .guide-icon .icon-share:before {
    display: block;
    margin: 2px 1px;
  }

  .good-container .guide-icon .icon-more {
    right: 14px;
    z-index: 210;
  }

  .good-container .guide-icon .icon-more:before {
    display: block;
    margin: 2px 1px;
  }

  /*图标样式 end*/

  /*右上弹窗样式 start*/
  .good-container .pop-down {
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    z-index: 200;
    margin-top: 0 !important;
    background-color: transparent;
  }

  .good-container .pop-down ul {
    position: fixed;
    top: 69px;
    right: 6px;
    padding-left: 0;
    width: 140px;
    list-style-type: none;
    background-color: #fff;
    border: 1px solid #e5e5e5;
    border-radius: 2px;
  }

  .good-container .pop-down ul:before {
    content: "";
    position: absolute;
    right: 8px;
    top: -6px;
    width: 10px;
    height: 10px;
    background-color: #fff;
    border-style: solid;
    border-color: #e5e5e5;
    border-width: 1px 0 0 1px;
    -webkit-transform: rotate(45deg);
    transform: rotate(45deg);
  }

  .good-container .pop-down ul li {
    display: flex;
    align-items: center;
    width: 100%;
    height: 48px;
    line-height: 48px;
  }

  .good-container .pop-down ul li .iconfont {
    flex: 0 0;
    padding: 0 14px;
    font-size: 18px;
  }

  .good-container .pop-text {
    flex: 1 1;
    color: #666;
    border-bottom: 1px solid #e6e6e6;
  }

  .good-container .pop-down ul li:last-child .pop-text {
    border-bottom: none;
  }

  .good-container .pop-dot {
    margin-right: 14px;
    width: 8px;
    height: 8px;
    background-color: #d9ad65;
    border-radius: 50%;
  }

  /*右上弹窗样式 end*/

  /*轮播样式 start*/
  .good-container .vux-slider > .vux-indicator.bottom-dots > a > .vux-icon-dot.active {
    background: #222;
  }
  /*轮播样式 end*/


  /*商品信息 start*/
  .good-container .title-container {
    box-sizing: border-box;
    padding: 15px 14px 20px;
  }

  .good-container .title-container p:first-child {
    font-size: 18px;
    line-height: 20px;
    color: rgba(102, 102, 102, 1)
  }

  .good-container .title-container p:nth-child(2) {
    margin-top: 10px;
    font-size: 12px;
    line-height: 12px;
    color: rgba(153, 153, 153, 1);
  }

  .good-container .title-container .price {
    margin-top: 26px;
  }

  .good-container .title-container .price span:first-child {
    font-size: 24px;
    line-height: 19px;
    color: rgba(217, 173, 101, 1);
  }

  .good-container .title-container .price span:last-child {
    float: right;
    font-size: 12px;
    color: rgba(153, 153, 153, 1);
    line-height: 17px;
  }
  /*商品信息 end*/

  /*选择数量cell start*/
  .good-container .choose-count {
    border-bottom: 0px solid #E9EDEE;
  }

  .good-container .weui-cells {
    margin-top: 0;
  }

  .good-container .weui-cell {
    padding: 16px 14px;
    font-size: 16px;
    color: rgba(102, 102, 102, 1);
    line-height: 16px;
  }

  @media (max-width: 360px){
    .good-container .weui-cell {
      padding: 16px 14px;
      font-size: 14px;
      color: rgba(102, 102, 102, 1);
      line-height: 16px;
    }
  }
  /*选择数量cell end*/

  /*选择数量弹窗 start*/
  .good-container .count-top {
    overflow: hidden;
    padding: 15px 0 10px 14px;
    border-bottom: 1px solid #E9EDEE;
  }

  .good-container .count-cover-img {
    float: left;
    width: 112px;
    padding-right: 10px;
  }

  .good-container .count-cover-img img {
    width: 88px;
    height: 88px;
  }

  .good-container .count-price-sum {
    float: left;
  }

  .good-container .count-price-sum p:first-child {
    font-size: 18px;
    color: rgba(217, 173, 101, 1);
    line-height: 18px;
  }

  .good-container .count-price-sum span:last-child {
    font-size: 14px;
    color: rgba(153, 153, 153, 1);
    line-height: 14px;
  }

    /*购买数量 start*/
  .good-container .buy-count > div:first-child p {
    font-size: 14px;
    color: rgba(153, 153, 153, 1);
  }

   /*购买数量-Xnumber start*/
  .good-container .vux-number-selector svg {
    width: 15px;
    height: 15px;
  }

  .good-container .vux-number-input {
    height: 23px;
    font-size: 14px;
  }

  .good-container .vux-number-selector {
    float: left;
    height: 23px;
    font-size: 16px;
    line-height: 0;
    color: #222;
    border: 1px solid #ececec;
  }

  .good-container .vux-number-selector svg {
    fill: #222;
  }

  .good-container .vux-number-selector-sub {
    border-right: 0;
  }

  .good-container .vux-number-selector-plus {
    border-left: 0;
  }
  /*购买数量-Xnumber end*/

  /*购买数量-button start*/
  .good-container .count-bottom-btn {
    height: 49px;
  }

  .good-container .count-bottom-btn > div {
    height: 49px;
    text-align: center;
    line-height: 49px;
    font-size: 16px;
    color: rgba(255, 255, 255, 1);
  }

  .good-container .count-bottom-btn > div[alt="cart"] {
    background: rgba(34, 34, 34, 1);
  }

  .good-container .count-bottom-btn > div[alt="now"] {
    background: rgba(217, 173, 101, 1);
    margin-left: 0 !important;
  }

  .good-container .icon-close {
    position: absolute;
    right: 0px;
    top: 9px;
    font-size: 18px;
    color: #999;
    width: 30px;
    height: 30px;
  }
  /*购买数量-button end*/
  /*购买数量 end*/
  /*选择数量弹窗 end*/

  /*售后服务cell start*/
  .good-container .service {
    line-height: 16px;
    margin-right: 25px;
  }

  .good-container .icon-blue {
    font-size: 20px;
    color: #222222;
  }

  .good-container .icon-checkbox-circle-line {
    font-size: 20px;
  }

  .good-container .service span {
    font-size: 16px;
    color: rgba(153, 153, 153, 1);
    vertical-align: text-top;
  }

  @media (max-width: 360px){
    .good-container .icon-blue {
      font-size: 18px;
      color: #222222;
    }

    .good-container .service span {
      font-size: 14px;
      color: rgba(153, 153, 153, 1);
      vertical-align: text-top;
    }
  }

  @media (max-width: 320px){
    .good-container .service {
      line-height: 18px;
      margin-right:16px;
    }

    .good-container .icon-blue {
      font-size: 16px;
      color: #222222;
    }

    .good-container .service span {
      font-size: 14px;
      color: rgba(153, 153, 153, 1);
      vertical-align: text-top;
    }
  }
  /*售后服务cell end*/

  /* 售后弹窗服务弹窗 start */
  .good-container .after-service-container {
    background: #fff;
  }

  .good-container .after-service-container > div {
    height: inherit;
  }

  .good-container .after-service-container > div > div {
    height: inherit;
  }

  /*售后part start*/
  .good-container .after-service > p {
    height: 63px;
    font-size: 18px;
    color: rgba(102, 102, 102, 1);
    text-align: center;
    line-height: 63px;
    border-bottom: 1px solid #e9edee;
  }

  .good-container .after-service > div {
    padding: 11px 14px 4px;
  }

  .good-container .after-service > div .iconfont {
    color: #222;
  }

  .good-container .after-service > div span {
    font-size: 16px;
    color: rgba(102, 102, 102, 1);
    line-height: 16px;
    vertical-align: text-top;
  }

  .good-container .after-service > div p {
    font-size: 12px;
    color: rgba(149, 146, 146, 1);
    line-height: 17px;
    margin-top: 6px;
    margin-left: 25px;
  }

  /*售后part end*/

  /*保障part start*/
  .good-container .safe-guard > p {
    height: 46px;
    line-height: 46px;
  }

  .good-container .safe-guard > div:last-child {
    margin-bottom: 200px;
  }

  .good-container .weui-cells:after {
    border-bottom: none;
  }

  .good-container .after-service-done-btn {
    position: fixed;
    bottom: 0;
    height: 48px;
    line-height: 48px;
    text-align: center;
    background: rgba(34, 34, 34, 1);
    font-size: 18px;
    color: rgba(255, 255, 255, 1);
  }

  /*保障part end*/
  /* 售后弹窗服务弹窗 end */

  /*评价flex start*/
  .good-container .comment-count {
    padding: 16px 14px;
    line-height: 16px;
    font-size: 16px;
    color: rgba(102, 102, 102, 1);
  }

  .good-container .comment-count span:first-child {
    margin-right: 10px;
  }
  /*评价flex end*/


  /*查看全部评价 start*/
  .good-container .view-all {
    padding-top: 16px;
    padding-bottom: 16px;
  }

  .good-container .view-all a {
    font-size: 16px;
    color: rgba(34, 34, 34, 1);
  }

  .good-container .view-all .iconfont {
    margin-top: 2px;
  }
    /*查看全部评价 end*/

  /*店铺简介 start*/
  .good-container .shop-card {
    margin-top: 0;
  }

  .good-container .shop-card img {
    width: 50px;
    height: 50px;
  }

  .good-container .shop-card > div:first-child {
    padding: 10px 14px 0;
  }

  .good-container .shop-card > div:first-child span {
    font-size: 16px;
    color: rgba(153, 153, 153, 1);
    line-height: 16px;
    margin-left: 10px;
  }

  .good-container .shop-card > div:nth-child(2) > div {
    text-align: center;
  }

  .good-container .shop-intro {
    padding-top: 15px;
    padding-bottom: 20px;
  }

  .good-container .shop-intro > div {
    width: 128px;
    text-align: center;
    font-size: 14px;
    color: rgba(149, 146, 146, 1);
    line-height: 14px;
  }

  .good-container .shop-intro > div > span {
    display: inline-block;
    margin-bottom: 9px;
    font-size: 18px;
    color: rgba(217, 173, 101, 1);
    line-height: 18px;
  }

  .good-container .shop-intro > span {
    display: inline-block;
    width: 1px;
    height: 20px;
    background: #e9edee;
  }

    /*进店逛逛 start*/
  .good-container .view-shop-btn {
    padding-bottom: 20px;
  }

  .good-container .view-shop-btn button {
    width: 100px;
    height: 34px;
    line-height: 17px;
    text-align: center;
    border: 1px solid rgba(34, 34, 34, 1);
    border-radius: 41px;
    color: rgba(34, 34, 34, 1);
    background: rgba(255, 255, 255, 1);;
  }
    /*进店逛逛 start*/

  /*店铺简介 end*/

  /*图文详情&产品参数 start*/
  .good-container .tab > div {
    font-size: 16px;
  }

  .good-container .graphic-detail-container {
    margin-bottom: 48px;
  }

  /*图文详情 start*/
  .good-container .description-container {
    min-height: 10px;
    padding-top: 15px;
    padding-bottom: 10px;
  }

  .good-container .description {
    margin: auto;
  }

  .good-container .description img {
    max-width: 100%;
  }
    /*图文详情 end*/

    /*产品参数 start*/
  .good-container .pro-params {
    margin-top: 39px;
  }

  .good-container .pro-params .vux-flexbox-item {
    margin-top: 0 !important;
  }

  .good-container .pro-params .vux-flexbox-item .vux-flexbox {
    box-sizing: border-box;
    padding: 14px 17px;
    font-size: 14px;
    color: rgba(153, 153, 153, 1);
    line-height: 14px;
    border-bottom: 1px solid #e9edee;
  }

  .good-container .pro-params > div:last-child {
    margin-bottom: 50px;
  }

  .good-container .pro-params .attr-content {
    text-align: right;
    line-height: 18px;
  }
    /*产品参数 end*/
  /*图文详情&产品参数 end*/

  /*底部选项卡 start*/
  .good-container .bottom-tabbar {
    position: fixed;
    bottom: 0;
    text-align: center;
    height: 48px;
    background: #fff;
    box-shadow: 0 -1px 0 rgba(0,0,0,0.05);
    -webkit-box-shadow: 0 -1px 0 rgba(0,0,0,0.05);
  }

  .good-container .bottom-tabbar > div {
    height: 48px;
    margin-left: 0 !important;
  }

  .good-container .bottom-tabbar i {
    display: inline-block;
    margin-top: 2px;
  }

  .good-container .bottom-tabbar > div:first-child,
  .good-container .bottom-tabbar > div:nth-child(3) {
    width: 79px;
    font-size: 12px;
    color: rgba(153, 153, 153, 1);
  }

  .good-container .bottom-tabbar > span {
    display: inline-block;
    width: 1px;
    height: 20px;
    background: #e9edee;
  }

  .good-container .bottom-tabbar > div:nth-child(4) {
    width: 148px;
    font-size: 16px;
    background: rgba(34, 34, 34, 1);
    color: rgba(255, 255, 255, 1);
    line-height: 48px;
  }

  .good-container .bottom-tabbar > div:last-child {
    width: 148px;
    line-height: 48px;
    background: rgba(217, 173, 101, 1);
    font-size: 16px;
    color: rgba(255, 255, 255, 1);
  }
  /*底部选项卡 end*/


  /*添加购物车成功弹窗 start*/
  .good-container .weui-toast {
    text-align: center;
    padding-top: 31px;
    background: rgba(74, 74, 74, 0.7);
    border-radius: 6px;
  }

  .good-container .weui-toast .weui-icon-success-no-circle {
    display: none;
  }

  .good-container .weui-toast i {
    font-size: 18px;
  }

  .good-container .weui-toast p {
    margin-top: 5px;
    font-size: 16px;
    color: rgba(255, 255, 255, 1);
  }

  /*添加购物车成功弹窗 end*/

  /*商品不足弹窗 start*/
  .good-container .goodshort-alert .weui-dialog__ft,
  .good-container .goodshort-alert .weui-dialog__hd {
    display: none;
  }

  .good-container .goodshort-alert .weui-dialog {
    width: 250px;
    height: 136px;
    border-radius: 6px;
  }

  .good-container .goodshort-alert .weui-dialog .weui-dialog__bd {
    text-align: center;
    padding: 0;
  }

  .good-container .goodshort-alert .weui-dialog .weui-dialog__bd > div:nth-child(1) {
    line-height: 86px;
    font-size: 16px;
    color: rgba(102, 102, 102, 1);
    border-bottom: 1px solid #e9edee;
  }

  .good-container .goodshort-alert .weui-dialog .weui-dialog__bd > div:nth-child(2) {
    line-height: 48px;
    font-size: 16px;
    color: rgba(34, 34, 34, 1);
  }

  /*商品不足弹窗 end*/

  /*商品下架弹窗 start*/
  .good-container .offline-warning {
    background: rgb(34, 34, 34);
    text-align: center;
    line-height: 49px;
    font-size: 18px;
    color: rgba(255, 255, 255, 1);
  }
  /*商品下架弹窗 end*/
</style>
