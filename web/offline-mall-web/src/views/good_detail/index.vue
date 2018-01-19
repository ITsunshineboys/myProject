<template>
  <div class="good-container">
    <!-- 顶部icon -->
    <div class="guide-icon">
      <i class="iconfont icon-return" @click="$router.go(-1)"></i>
      <i class="iconfont icon-share" @click="androidShare"></i>
      <router-link :to="'/'">
        <i class="iconfont icon-home"></i>
      </router-link>
    </div>

    <!--轮播-->
    <swiper loop auto :list="banner_list" height="375px" dots-class="custom-bottom" dots-position="center"
            :show-desc-mask="false"></swiper>
    <div class="good-detail">
      <div class="title-container">
        <p>{{good_detail.title}}</p>
        <p>{{good_detail.subtitle}}</p>
        <div class="price">
          <span>¥{{good_detail.platform_price}}</span>
          <span v-show="good_detail.line_goods.is_offline_goods === '否' ? false:true" @click="show=true">线下体验商品</span>
        </div>
      </div>
      <divider></divider>
      <group>
        <cell-box is-link class="choose-count" @click.native="showCount('now')">
          选择数量

        </cell-box>
        <cell-box is-link @click.native="show_after_service = true">
          <div class="service" v-for="item in after_sale_services">
            <i class="iconfont icon-checkbox-circle-line"></i>
            <span>{{item}}</span>
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

      <!--底部选项卡-->
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
              <span>产品编码</span>
              <span>{{good_detail.sku}}</span>
            </flexbox>
          </flexbox-item>
          <flexbox-item>
            <flexbox justify="space-between">
              <span>产品品牌</span>
              <span>{{good_detail.brand_name}}</span>
            </flexbox>
          </flexbox-item>
          <flexbox-item>
            <flexbox justify="space-between" v-if="good_detail.series_name">
              <span>系列</span>
              <span>{{good_detail.series_name}}</span>
            </flexbox>
          </flexbox-item>
          <flexbox-item v-if="good_detail.style_name">
            <flexbox justify="space-between">
              <span>风格</span>
              <span>{{good_detail.style_name}}</span>
            </flexbox>
          </flexbox-item>
          <flexbox-item v-if="good_detail.attr!==''">
            <flexbox justify="space-between" v-for="item in good_detail.attrs" :key="item.id">
              <span>{{item.name}}</span>
              <span>{{item.value}}</span>
            </flexbox>
          </flexbox-item>
        </flexbox>
        <divider></divider>
      </div>

      <!--底部按钮-->
      <flexbox class="bottom-tabbar">
        <flexbox-item :span="155/375"></flexbox-item>
        <flexbox-item :span="110/375">
          匹配小区

        </flexbox-item>
        <flexbox-item @click.native="showCount('now')" :span="110/375">
          立即购买

        </flexbox-item>
      </flexbox>
    </div>

    <!-- 选择数量弹窗 -->
    <popup v-model="show_count" @on-hide="showCount('close')">
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
            <i class="iconfont icon-close" @click="showCount('close')"></i>
          </div>
          <group>
            <x-number class="buy-count" title="购买数量" v-model="count" :fillable="true" :max="good_detail.left_number"
                      :min="1" width="34px"></x-number>
          </group>
        </group>
        <flexbox class="count-bottom-btn">
          <flexbox-item alt="now" @click.native="buyNow">
            立即购买

          </flexbox-item>
        </flexbox>
      </div>
    </popup>

    <!--售后保障弹窗 -->
    <popup v-model="show_after_service" height="100%" class="show_after_service">
      <div>
        <flexbox orient="vertical" justify="space-between" style="-webkit-overflow-scrolling: touch;">
          <flexbox-item>
            <div class="after-service" v-if="pop.show_service">
              <p>售后</p>
              <div v-for="item in all_after_sale_services" v-if="afterservice_arr.indexOf(item) !== -1"
                   class="after-service-item">
                <i class="iconfont icon-checkbox-circle-line"></i>
                <span>{{item}}</span>
                <p>清代性灵派诗人袁枚说过这么一句话：“读书不知味,不如束高阁;蠢鱼尔何如,终日食糟粕”，意思就是读书如果不能明白其中的道理，还不如束之高阁，那些只会死读书的书呆子们，相当于在吞食无用的糟粕。</p>
              </div>
            </div>
            <div class="after-service safe-guard">
              <p>保障</p>
              <div v-for="item in all_after_sale_services" v-if="safeguard_arr.indexOf(item) !== -1"
                   class="after-service-item">
                <i class="iconfont icon-checkbox-circle-line"></i>
                <span>{{item}}</span>
                <p>清代性灵派诗人袁枚说过这么一句话：“读书不知味,不如束高阁;蠢鱼尔何如,终日食糟粕”，意思就是读书如果不能明白其中的道理，还不如束之高阁，那些只会死读书的书呆子们，相当于在吞食无用的糟粕。</p>
              </div>
            </div>
          </flexbox-item>
          <flexbox-item class="after-service-done-btn" @click.native="show_after_service = false">完成</flexbox-item>
        </flexbox>
      </div>
    </popup>

    <!-- 分享弹窗 -->
    <!--<popup v-model="show_share" class="show_share">-->
    <!--<div>分享</div>-->
    <!--<div class="share-icon">-->
    <!--<div v-for="item in share_content">-->
    <!--<img :src="item.image" alt=""><br/>-->
    <!--<span>{{item.title}}</span>-->
    <!--</div>-->
    <!--</div>-->
    <!--<div @click="show_share = false">取消</div>-->
    <!--</popup>-->

    <!--线下商品介绍弹窗-->
    <offlinealert :offlineInfo="offlineInfo" :show="show" :isOffline="false" @isShow="showOfflineAlert"></offlinealert>
  </div>
</template>

<script>
  import {Swiper, Group, Cell, CellBox, Flexbox, FlexboxItem, Card, Tab, TabItem, Popup, XNumber, Toast} from 'vux'
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
      divider,
      comment,
      offlinealert
    },
    data () {
      return {
        good_id: '', // 商品id
        role_id: 6, // 角色id
        isShow: false,  // 右上角弹窗
        show: false,    // 线下商品简介
        cart_success: false, // 添加购物车成功toast
        good_detail: {
          line_goods: {
            is_offline_goods: ''
          },
          comments: {
            total: 0,
            latest: {}
          },
          supplier: {},
          attrs: ''
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
        show_count: false,            // 选择数量弹窗
        show_after_service: false,    // 售后弹窗
        show_share: false,            // 分享弹窗
        count: 1,                     // 选择数量默认值
        pop: {
          show_service: false         // 默认不显示售后
        },
        show_description: true,       // 显示图文详情
        all_after_sale_services: [],
        checkin: 1,                    // 是否登录默认登录
        afterservice_arr: ['上门维修', '上门退货', '上门换货', '退货', '换货'],
        safeguard_arr: ['提供发票', '上门安装'],
        user_icon: require('../../assets/images/user_icon_default.png'), // 默认用户头像
        share_content: [
          {image: require('../../assets/images/weixin.png'), title: '微信'},
          {image: require('../../assets/images/pengyouquan.png'), title: '朋友圈'},
          {image: require('../../assets/images/sina.png'), title: '新浪微博'},
          {image: require('../../assets/images/qq.png'), title: 'QQ'},
          {image: require('../../assets/images/qzone.png'), title: 'QQ空间'}
        ]
      }
    },
    activated () {
      this.good_id = this.$route.params.id // 商品id
      this.isShow = false
      this.axios.get('/mall/goods-view', {id: this.good_id}, (res) => {
        this.good_detail = res.data.goods_view
        // 用户名处理
        if (this.good_detail.comments.total !== '0') {
          this.user_name = this.good_detail.comments.latest.name.substr(0, 6) + '...'
        }
        // 售后弹窗显示处理
        this.all_after_sale_services = this.good_detail.after_sale_services
        this.after_sale_services = this.good_detail.after_sale_services.slice(0, 3) // 页面售后显示内容
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
      // 判断是否显示售后
      afterServiceShow () {
        for (let [key, value] of this.all_after_sale_services.entries()) {    // eslint-disable-line
          if (afterserviceArr.indexOf(value) !== -1) {
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
        if (obj === 'close') {
          this.show_count = false
          this.count = 1
        } else {
          this.show_count = true
        }
      },
      // 线下商品弹窗处理
      showOfflineAlert: function (bool) {
        this.show = bool
      },
      // 立即购买
      buyNow () {
        /* params
         * 商品id 购买数量
         * */
        this.$router.push({path: '/order', query: {goods_id: this.good_id, goods_num: this.count}})
        this.show_count = false
      },
      // 分享
      androidShare () {
        window.AndroidWebView.share('我在【艾特生活】分享了一款好物给你，点击链接查看详情【' + this.good_detail.title + '　　' + this.good_detail.subtitle + '】' + location.href)
      }
    }
  }
</script>

<style>
  .good-container {
    background: rgba(255, 255, 255, 1);
    position: relative;
  }

  .good-container .custom-bottom {
    bottom: 26px;
  }

  /*图标样式*/
  .good-container .guide-icon {
    position: relative;
  }

  .good-container .guide-icon .iconfont {
    position: absolute;
    top: 32px;
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
  }

  .good-container .vux-popup-dialog {
    background: rgba(255, 255, 255, 1);
  }

  .good-container .guide-icon .icon-share {
    right: 54px;
  }

  .good-container .guide-icon .icon-home {
    right: 14px;
  }

  .good-container .choose-count {
    border-bottom: 1px solid #E9EDEE;
  }

  .good-container .weui-cells {
    margin-top: 0;
  }

  .good-container .weui-cell {
    padding: 16px 14px;
    font-size: 16px;
    color: rgba(102, 102, 102, 1);
    line-height: 16px;
    font-size: 16px;
  }

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

  .good-container .comment-count {
    padding: 16px 14px;
    line-height: 16px;
    font-size: 16px;
    color: rgba(102, 102, 102, 1);
  }

  .good-container .comment-count span:first-child {
    margin-right: 10px;
  }

  /*商品名称等内容*/
  .good-container .title-container {
    box-sizing: border-box;
    height: 120px;
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

  .good-container .shop-card {
    margin-top: 0;
  }

  .good-container .shop-card img {
    width: 50px;
    height: 50px;
  }

  .good-container .shop-card > div:first-child {
    padding-top: 10px;
    padding-left: 14px;
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
    width: 2px;
    height: 20px;
    background: #CDD3D7;
  }

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

  .good-container .tab > div {
    font-size: 16px;
  }

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
    border-bottom: 1px solid #CDD3D7;
  }

  .good-container .pro-params > div:last-child {
    margin-bottom: 50px;
  }

  /*底部选项卡 开始*/
  .good-container .bottom-tabbar {
    text-align: center;
    height: 48px;
  }

  .good-container .bottom-tabbar > div {
    height: 48px;
    margin-left: 0 !important;
  }

  .good-container .bottom-tabbar > div:nth-child(2) {
    font-size: 16px;
    background: rgba(34, 34, 34, 1);
    color: rgba(255, 255, 255, 1);
    line-height: 48px;
  }

  .good-container .bottom-tabbar > div:last-child {
    line-height: 48px;
    background: rgba(217, 173, 101, 1);
    font-size: 16px;
    color: rgba(255, 255, 255, 1);
  }
  /*底部选项卡 结束*/


  /*选择数量弹窗 开始*/
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
  /*选择数量弹窗 结束*/


  /*购买数量*/
  .good-container .buy-count > div:first-child p {
    font-size: 14px;
    color: rgba(153, 153, 153, 1);
  }

  /*购买数量-Xnumber*/
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
    right: 10px;
    font-size: 18px;
    color: #999;
  }

  /* 售后弹窗 */
  .good-container .show_after_service > div {
    height: inherit;
  }

  .good-container .show_after_service > div > div {
    height: inherit;
  }

  .good-container .after-service > p {
    height: 63px;
    font-size: 18px;
    color: rgba(102, 102, 102, 1);
    text-align: center;
    line-height: 63px;
    border-bottom: 2px solid #CDD3D7;
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
  }

  .good-container .after-service > div p {
    font-size: 12px;
    color: rgba(149, 146, 146, 1);
    line-height: 17px;
    margin-top: 6px;
    margin-left: 25px;
  }

  /* 售后弹窗-保障 */
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

  /*图文详情*/
  .good-container .description-container {
    min-height: 10px;
  }

  .good-container .description {
    margin: auto;
  }

  .good-container .description img {
    max-width: 100%;
  }

  .good-container .show_share > div:first-child {
    height: 48px;
    line-height: 48px;
    text-align: center;
    font-size: 16px;
    color: rgba(102, 102, 102, 1);
    border-bottom: 1px solid rgba(205, 211, 215, 1);
  }

  .good-container .show_share > div:nth-child(2) {
    height: 163px;
  }

  .good-container .show_share > div:last-child {
    height: 48px;
    text-align: center;
    line-height: 48px;
    font-size: 18px;
    color: rgba(255, 255, 255, 1);
    background: rgba(34, 34, 34, 1);
  }

  /*分享弹窗*/
  .good-container .share-icon {
    width: 314px;
    margin: 20px auto;
  }

  .good-container .share-icon img {
    width: 40px;
    height: 40px;
  }

  .good-container .share-icon > div {
    float: left;
    width: 48px !important;
    height: 75px;
    text-align: center;
    margin-right: 40px;
  }

  .good-container .share-icon > div:nth-child(4) {
    margin-right: 0;
  }

  .good-container .share-icon > div:last-child {
    margin-top: 18px;
  }

  .good-container .share-icon > div > span {
    font-size: 12px;
    color: rgba(153, 153, 153, 1);
  }

  /*添加购物车成功弹窗*/
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
</style>

