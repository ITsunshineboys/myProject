<template>
  <div class="good-container">
    <div class="guide-icon">
      <i class="iconfont icon-return"></i>
      <i class="iconfont icon-share" @click="show_share=true"></i>
      <i class="iconfont icon-more"></i>
    </div>
    <swiper loop auto :list="banner_list" height="375px" dots-class="custom-bottom" dots-position="center"
            :show-desc-mask="false"></swiper>
    <div class="good-detail">
      <goodsTitle :title="good_detail.title" :subtitle="good_detail.subtitle"
                  :platform_price="good_detail.platform_price"
                  :show_offline="good_detail.line_goods.is_offline_goods === '否' ? false:true"></goodsTitle>
      <divider></divider>
      <group>
        <cell-box is-link class="choose-count" @click.native="showCount('count')">
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
      <div v-if="good_detail.comments.total">
        <flexbox justify="flex-start" class="comment-count">
          <span class="sum-comment">评价</span>
          <span>({{good_detail.comments.total}})</span>
        </flexbox>
        <comment headshotStyle="headshot-style" nameStyle="name-style" dateStyle="date-style"
                 :src="good_detail.comments.latest.icon" :userName="good_detail.comments.latest.name"
                 :content="good_detail.comments.latest.content"></comment>
        <flexbox justify="center" class="view-all">
        <span>
          查看全部评价
          </span>
          <i class="iconfont icon-arrow-line-right"></i>
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
            <span>{{good_detail.supplier.goods_number}}</span>
            <br/>
            商品数


          </div>
          <span></span>
          <div>
            <span>{{good_detail.supplier.follower_number}}</span>
            <br/>
            粉丝数


          </div>
          <span></span>
          <div>
            <span>{{good_detail.supplier.comprehensive_score}}</span>
            <br/>
            综合评分


          </div>
        </flexbox>
        <flexbox slot="footer" justify="center" class="view-shop-btn">
          <button type="button">进店逛逛</button>
        </flexbox>
      </card>
      <divider></divider>

      <!--底部选项卡-->
      <tab defaultColor="#999" active-color="#222" bar-active-color="#222" custom-bar-width="50px" class="tab">
        <tab-item selected @on-item-click="showDescription">图文详情</tab-item>
        <tab-item @on-item-click="showParams">产品参数</tab-item>
      </tab>
      <div v-show="show_description">
        <flexbox>
          <flexbox-item class="description" v-html="good_detail.description"></flexbox-item>
        </flexbox>
      </div>
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
      <div class="bottom-tabbar">
        <div><i class="iconfont icon-service"></i><br/>联系商家</div>
        <div @click="showCount('cart')">加入购物车</div>
        <div @click="showCount('now')">立即购买</div>
      </div>
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
                      :min="1"
                      width="34px"></x-number>
          </group>
        </group>
        <flexbox class="count-bottom-btn">
          <flexbox-item alt="cart" v-if="count_cart||default_count">
            加入购物车


          </flexbox-item>
          <flexbox-item alt="now" v-if="count_now||default_count">
            立即购买


          </flexbox-item>
        </flexbox>
      </div>
    </popup>

    <!--售后保障弹窗 -->
    <popup v-model="show_after_service" height="100%" class="show_after_service">
      <div>
        <flexbox orient="vertical" justify="space-between">
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

    <!-- 分享 -->
    <popup v-model="show_share" class="show_share">
      <div>分享</div>
      <div class="share-icon">
        <div>
          <img src="../../assets/images/weixin.png" alt=""><br/>
          <span>微信</span>
        </div>
        <div>
          <img src="../../assets/images/pengyouquan.png" alt=""><br/>
          <span>朋友圈</span>
        </div>
        <div>
          <img src="../../assets/images/sina.png" alt=""><br/>
          <span>新浪</span>
        </div>
        <div class="qq">
          <img src="../../assets/images/qq.png" alt=""><br/>
          <span>QQ</span>
        </div>
        <div>
          <img src="../../assets/images/qzone.png" alt=""><br/>
          <span>QQ空间</span>
        </div>
      </div>
      <div @click="show_share=false">取消</div>
    </popup>

  </div>
</template>

<script>
  import {Swiper, Group, Cell, CellBox, Flexbox, FlexboxItem, Card, Tab, TabItem, Popup, XNumber} from 'vux'
  import goodsTitle from '../good_detail/title'
  import divider from '../good_detail/divider'
  import comment from '../good_detail/comment.vue'
  //  const safeguard_arr = ['提供发票', '上门安装']

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
      goodsTitle,
      divider,
      comment
    },
    data () {
      return {
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
        banner_list: [],
        after_sale_services: [],      // 页面售后显示
        show_count: false,            // 选择数量弹窗
        show_after_service: false,    // 售后弹窗
        show_share: false,            // 分享弹窗
        count: 1,                     // 选择数量默认值
        pop: {
          show_service: false         // 默认不显示售后
        },
        show_description: true,       // 显示图文详情
        all_after_sale_services: [],
        afterservice_arr: ['上门维修', '上门退货', '上门换货', '退货', '换货'],
        safeguard_arr: ['提供发票', '上门安装'],
        count_cart: false,            // 加入购物车按钮显示
        count_now: false,             // 立即购买按钮显示
        default_count: false          // 两个按钮显示
      }
    },
    created () {
      this.axios.get('/mall/goods-view', {id: 31}, (res) => {
        this.good_detail = res.data.goods_view
        this.all_after_sale_services = this.good_detail.after_sale_services
        this.after_sale_services = this.good_detail.after_sale_services.slice(0, 3) // 页面售后显示内容
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
      afterServiceShow: function () {
        for (let [key, value] of this.all_after_sale_services.entries()) {    // eslint-disable-line
          if (this.afterservice_arr.indexOf(value) !== -1) {
            this.pop.show_service = true
          }
        }
      },
      showDescription: function () {
        this.show_description = true
      },
      showParams: function () {
        this.show_description = false
      },
      showCount: function (obj) {
        if (obj === 'all') {
          this.show_count = false
          this.count_cart = false
          this.count_now = false
          this.default_count = false
          this.count = 1
        } else {
          this.show_count = true
          obj === 'cart' ? this.count_cart = true : obj === 'now' ? this.count_now = true : this.default_count = true
        }
      }
    }
  }
</script>

<style>
  .good-container {
    background: rgba(255, 255, 255, 1);
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

  .good-container .guide-icon .icon-more {
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

  /*评价组件样式*/
  .good-container .headshot-style {
    width: 50px;
    height: 50px;
    border-radius: 50%;
  }

  .good-container .name-style {
    margin-left: 15px;
    font-size: 16px;
    color: rgba(153, 153, 153, 1);
  }

  .good-container .date-style {
    font-size: 14px;
    color: rgba(153, 153, 153, 1);
    line-height: 16px;
  }

  .good-container .view-all {
    padding-top: 16px;
    padding-bottom: 16px;
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

  .good-container .view-shop-btn > button {
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

  .good-container .bottom-tabbar {
    text-align: center;
    height: 48px;
  }

  .good-container .bottom-tabbar > div {
    float: left;
    height: 48px;
  }

  .good-container .bottom-tabbar i {
    display: inline-block;
    margin-top: 2px;
  }

  .good-container .bottom-tabbar > span {
    display: inline-block;
    float: left;
    width: 2px;
    height: 20px;
    margin-top: 15px;
    background: #CDD3D7;
  }

  .good-container .bottom-tabbar > div:first-child {
    width: 79px;
    font-size: 12px;
    color: rgba(153, 153, 153, 1);
  }

  .good-container .bottom-tabbar > div:nth-child(2) {
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

  /*选择数量弹窗*/
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
    width: 46px !important;
    height: 75px;
    text-align: center;
  }

  .good-container .share-icon > div:not(.qq) {
    margin-right: 42px;
  }

  .good-container .share-icon > div:last-child {
    margin-top: 18px;
  }

  .good-container .share-icon > div > span {
    font-size: 12px;
    color: rgba(153, 153, 153, 1);
  }
</style>

