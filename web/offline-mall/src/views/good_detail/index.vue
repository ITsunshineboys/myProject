<template>
  <div class="good-container">
    <div class="guide-icon">
      <i class="iconfont icon-return"></i>
      <i class="iconfont icon-share"></i>
      <i class="iconfont icon-more"></i>
    </div>
    <swiper loop auto :list="banner_list" height="375px" dots-class="custom-bottom" dots-position="center"
            :show-desc-mask="false"></swiper>
    <div class="good-detail">
      <goodsTitle :title="good_detail.title" :subtitle="good_detail.subtitle"
                  :platform_price="good_detail.platform_price"
                  :show_offline="is_offline_goods === '否' ? false:true"></goodsTitle>
      <divider></divider>
      <group>
        <cell-box is-link class="choose-count" @click.native="show_count = true">
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
      <flexbox justify="flex-start" class="comment-count">
        <span class="sum-comment">评价</span>
        <span>(493)</span>
      </flexbox>
      <comment headshotStyle="headshot-style" nameStyle="name-style" dateStyle="date-style"></comment>
      <flexbox justify="center" class="view-all">
        <span>
          查看全部评价
          </span>
        <i class="iconfont icon-arrow_line_right"></i>
      </flexbox>
      <divider></divider>
      <card class="shop-card">
        <flexbox slot="header" justify="flex-start" align="center">
          <img src="./logo.png" alt="">
          <span>马可波罗其打算的撒</span>
        </flexbox>
        <flexbox slot="content" justify="space-between" class="shop-intro">
          <div>
            <span>1130</span>
            <br/>
            商品数
































          </div>
          <span></span>
          <div>
            <span>15</span>
            <br/>
            粉丝数
































          </div>
          <span></span>
          <div>
            <span>0</span>
            <br/>
            综合评分































          </div>

        </flexbox>
        <flexbox slot="footer" justify="center" class="view-shop-btn">
          <button type="button">进店逛逛</button>
        </flexbox>
      </card>
      <divider></divider>
      <tab defaultColor="#999" active-color="#222" bar-active-color="#222" custom-bar-width="50px" class="tab">
        <tab-item selected>图文详情</tab-item>
        <tab-item>产品参数</tab-item>
      </tab>
      <div></div>
      <div>
        <flexbox orient="vertical" class="pro-params" align="flex-start">
          <flexbox-item>
            <flexbox justify="space-between">
              <span>产品编码</span>
              <span>DSADSA5S5</span>
            </flexbox>
          </flexbox-item>
          <flexbox-item>
            <flexbox justify="space-between">
              <span>产品编码</span>
              <span>DSADSA5S5</span>
            </flexbox>
          </flexbox-item>
          <flexbox-item>
          </flexbox-item>
        </flexbox>
        <divider></divider>
      </div>
      <div class="bottom-tabbar">
        <div><i class="iconfont icon-service"></i><br/>联系商家</div>
        <div>加入购物车</div>
        <div>立即购买</div>
      </div>
    </div>

    <!-- 选择数量弹窗 -->
    <popup v-model="show_count">
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
            <i class="iconfont icon-close" @click="show_count = false"></i>
          </div>
          <group>
            <x-number title="购买数量" v-model="count" :fillable="true" :max="good_detail.left_number" :min="1"
                      width="34px"></x-number>
          </group>
        </group>
        <flexbox class="count-bottom-btn">
          <flexbox-item>
            加入购物车



          </flexbox-item>
          <flexbox-item>
            立即购买



          </flexbox-item>
        </flexbox>
      </div>
    </popup>

    <!--售后保障弹窗 -->
    <popup v-model="show_after_service" height="100%">
      <div>
        <group>
          <div class="after-service">
            <p>售后</p>
            <div>
              <i class="iconfont icon-checkbox-circle-line"></i>
              <span>上门维修</span>
              <p>清代性灵派诗人袁枚说过这么一句话：“读书不知味,不如束高阁;蠢鱼尔何如,终日食糟粕”，意思就是读书如果不能明白其中的道理，还不如束之高阁，那些只会死读书的书呆子们，相当于在吞食无用的糟粕。</p>
            </div>
            <div>
              <i class="iconfont icon-checkbox-circle-line"></i>
              <span>上门维修</span>
              <p>清代性灵派诗人袁枚说过这么一句话：“读书不知味,不如束高阁;蠢鱼尔何如,终日食糟粕”，意思就是读书如果不能明白其中的道理，还不如束之高阁，那些只会死读书的书呆子们，相当于在吞食无用的糟粕。</p>
            </div>
          </div>
          <div class="after-service safe-guard">
            <p>保障</p>
            <div>
              <i class="iconfont icon-checkbox-circle-line"></i>
              <span>上门维修</span>
              <p>清代性灵派诗人袁枚说过这么一句话：“读书不知味,不如束高阁;蠢鱼尔何如,终日食糟粕”，意思就是读书如果不能明白其中的道理，还不如束之高阁，那些只会死读书的书呆子们，相当于在吞食无用的糟粕。</p>
            </div>
          </div>
          <div class="after-service-done-btn">完成</div>
        </group>
      </div>
    </popup>

  </div>
</template>

<script>
  import {Swiper, Group, Cell, CellBox, Flexbox, FlexboxItem, Card, Tab, TabItem, Popup, XNumber} from 'vux'
  import goodsTitle from '../good_detail/title'
  import divider from '../good_detail/divider'
  import comment from '../good_detail/comment.vue'

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
        good_detail: {},
        is_offline_goods: '',
        banner_list: [],
        after_sale_services: [],        // 页面售后显示
        show_count: false,          // 选择数量弹窗
        show_after_service: false,  // 售后弹窗
        count: 1                    // 选择数量默认值
      }
    },
    created () {
      this.axios.get('/mall/goods-view', {id: 31}, (res) => {
        this.good_detail = res.data.goods_view
        this.is_offline_goods = res.data.goods_view.line_goods.is_offline_goods
        this.after_sale_services = this.good_detail.after_sale_services.splice(0, 3)
        const imgList = this.good_detail.images
        imgList.splice(0, 0, this.good_detail.cover_image)
        this.banner_list = imgList.map((item) => ({
          img: item
        }))
      })
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
    padding: 10px 22px;
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
    height: 50px;
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

  /*选择数量*/
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

  .good-container .count-bottom-btn > div:first-child {
    background: rgba(34, 34, 34, 1);
  }

  .good-container .count-bottom-btn > div:last-child {
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

  /* 保障 */
  .good-container .safe-guard > p {
    height: 46px;
    line-height: 46px;
  }

  .good-container .weui-cells:after {
    border-bottom: none;
  }

  .good-container .after-service-done-btn {
    position: absolute;
    bottom: 0;
    height: 48px;
    line-height:48px;
    text-align: center;
    background:rgba(34,34,34,1);
    font-size:18px;
    color:rgba(255,255,255,1);
  }

</style>
