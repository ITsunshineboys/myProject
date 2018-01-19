<template>
  <div>
    <v-header headTitle="店铺首页" pop="true"></v-header>
    <div class="store" flex="main:justify cross:center">
      <div flex>
        <div class="store-img">
          <img :src="storeData.icon">
        </div>
        <div class="store-name">
          <p>{{storeData.shop_name}}</p>
          <p class="experience-shop" v-if="storeData.is_line_supplier === 1" @click="isShowAlert = true">线下体验店</p>
        </div>
      </div>
      <div flex>
        <div class="attention" flex="dir:top main:justify" @click="attentionStore">
          <span class="iconfont" :class="{'icon-heart': isAttention === 0, 'icon-heart-solid': isAttention}"></span>
          <span>关注</span>
        </div>
        <div class="split-line"></div>
        <div class="fans" flex="dir:top main:justify">
          <span>{{storeData.follower_number}}</span>
          <span>粉丝数</span>
        </div>
      </div>
    </div>
    <div :style="{minHeight: tabHeight + 'px'}" style="min-height: 44px;">
      <sticky :offset="40" :check-sticky-support="false">
        <tab class="tab" active-color="#222" bar-active-color="#222" defaultColor="#999" custom-bar-width="50px">
          <tab-item @on-item-click="onClickTab" selected>店铺首页</tab-item>
          <tab-item @on-item-click="onClickTab">全部商品</tab-item>
        </tab>
        <div class="goods-filter" v-if="tabActive == 1">
          <div :class="{active: sortName === 'sold_number'}" @click="tabHandle('sold_number')">销量优先</div>
          <div :class="{active: sortName === 'platform_price'}" @click="tabHandle('platform_price')">
            <span>价格</span>
            <span class="sort">
              <span :class="{active: platformPriceSortNum === 4 & sortName === 'platform_price'}"
                    class="iconfont icon-sort-up"></span>
              <span :class="{active: platformPriceSortNum === 3 & sortName === 'platform_price'}"
                    class="iconfont icon-sort-down"></span>
            </span>
          </div>
          <div :class="{active: sortName === 'favourable_comment_rate'}" @click="tabHandle('favourable_comment_rate')">
            <span>好评率</span>
            <span class="sort">
              <span :class="{active: favourableCommentRateSortNum === 4 & sortName === 'favourable_comment_rate'}"
                    class="iconfont icon-sort-up"></span>
              <span :class="{active: favourableCommentRateSortNum === 3 & sortName === 'favourable_comment_rate'}"
                    class="iconfont icon-sort-down"></span>
            </span>
          </div>
        </div>
      </sticky>
    </div>
    <div class="store-home" v-if="tabActive == 0">
      <swiper :list="carousel" :show-desc-mask="false" dots-position="center" dots-class="dots" :loop="true" :auto="true" height="145px"></swiper>
      <div class="store-goods-list" flex>
        <router-link class="store-goods-item" v-for="obj in recommendGoods" :to="'/good-detail/' + obj.url" tag="div" :key="obj.id">
          <img :src="obj.image">
          <p class="store-goods-title">{{obj.title}}</p>
          <p class="store-goods-desc">{{obj.description}}</p>
          <p class="store-goods-price">￥{{obj.show_price}}</p>
        </router-link>
      </div>
    </div>
    <div class="all-goods" v-else>
      <goods-list :goods-list="allGoodsData"></goods-list>
    </div>
    <div class="btn-group" flex>
      <router-link :to="{path: '/shop-intro/' + $route.params.id}" tag="button" type="button">
        店铺介绍
      </router-link>
      <button class="" type="button" @click="contactStore(1 ,6)">联系商家</button>
    </div>
    <!-- 线下体验店详情弹窗 -->
    <offline-alert @isShow="isShow" :show="isShowAlert" :offlineInfo="offlineInfo"></offline-alert>
  </div>
</template>

<script>
  import {Tab, TabItem, Swiper, Sticky} from 'vux'
  import vHeader from '@/components/HeaderSearch'
  import GoodsList from '@/components/GoodsList'
  import OfflineAlert from '@/components/OfflineAlert'

  export default {
    components: {
      Tab,
      TabItem,
      Swiper,
      Sticky,
      vHeader,
      GoodsList,
      OfflineAlert
    },
    data () {
      return {
        isAttention: 0,   // 关注图标默认未关注
        tabActive: 0,         // 默认选中店铺首页
        tabHeight: 44,        // tab 默认最小高度为 44 像素
        isShowAlert: false,  // 是否显示线下体验店弹窗
        carousel: [],         // 店铺首页轮播
        storeData: {},        // 店铺信息
        recommendGoods: [],   // 推荐商品列表
        offlineInfo: {        // 线下体验店弹窗信息
          address: '',
          phone: '',
          desc: ''
        },
        sortName: 'sold_number',              // tab排序名称  sold_number(销量优先)  platform_price(价格) favourable_comment_rate(好评率)
        platformPriceSortNum: 4,              // 价格排序方式   3：降序 4：升序
        favourableCommentRateSortNum: 4,      // 好评率排序方式   3：降序 4：升序
        allGoodsParams: {     // 全部商品请求参数
          supplier_id: this.$route.params.id,
          page: 1,
          'sort[]': 'sold_number:3',
          size: 12
        },
        allGoodsData: []
      }
    },
    activated () {
      // 请求店铺数据
      this.axios.get('/supplier/index', {supplier_id: this.$route.params.id}, res => {
        console.log(res, '店铺首页数据')
        let data = res.data.index
        this.storeData = data
        this.carousel = data.carousel.map(item => {
          return {
            url: '/good-detail/' + item.url,
            img: item.image
          }
        })
        this.offlineInfo.address = data.district
        this.offlineInfo.phone = data.line_supplier_mobile
        this.isAttention = data.is_follow
      })
      // 请求店铺首页推荐商品
      this.axios.get('supplier/recommend-second', {supplier_id: this.$route.params.id}, res => {
        console.log(res, '店铺推荐商品')
        this.recommendGoods = res.data.recommend_second
      })
    },
    methods: {
      onClickTab (index) {
        this.tabActive = index
        if (index === 0) {
          // 当 tab 为店铺首页，tab 高度最小为 44 像素
          this.tabHeight = 44
        } else {
          // 当 tab 为全部商品，tab 高度最小为 88 像素
          this.tabHeight = 88
          this.getAllGoodsData()
        }
      },
      /**
       * 线下体验店弹窗
       * 传值给父级，告知是否隐藏
       * @param bool
       */
      isShow (bool) {
        this.isShowAlert = bool
      },
      getAllGoodsData () {
        this.axios.get('/supplier/goods', this.allGoodsParams, res => {
          console.log(res, '全部商品列表')
          this.allGoodsData = res.data.supplier_goods
        })
      },
      tabHandle (str) {
        this.sortName = str
        switch (str) {      // 只有在价格和好评率会做排序
          case 'platform_price':
            // 点击价格 tab 默认将默认为降序
            this.platformPriceSortNum = this.platformPriceSortNum === 4 ? 3 : 4     // 第一次点击价格 tab 为降序：3
            this.favourableCommentRateSortNum = 4                                   // 将好评率改为升序
            this.allGoodsParams['sort[]'] = this.sortName + ':' + this.platformPriceSortNum
            break
          case 'favourable_comment_rate':
            // 点击好评率 tab 默认将默认为降序
            this.favourableCommentRateSortNum = this.favourableCommentRateSortNum === 4 ? 3 : 4     // 第一次点击好评率 tab 为降序：3
            this.platformPriceSortNum = 4                                                           // 将价格改为升序
            this.allGoodsParams['sort[]'] = this.sortName + ':' + this.favourableCommentRateSortNum
            break
          default:
            this.allGoodsParams['sort[]'] = this.sortName + ':' + 3
        }
        this.getAllGoodsData()
      },
      attentionStore () {
        // 关注店铺
        let params = {
          supplier_id: this.$route.params.id,
          status: this.isAttention === 1 ? 0 : 1
        }
        this.axios.post('/user-follow/user-follow-shop', params, res => {
          console.log(res, '关注')
        })
      }
    }
  }
</script>

<style scoped>
  .store {
    margin-top: 46px;
    margin-bottom: 10px;
    padding: 9px 14px;
    color: #999;
    background-color:  #fff;
  }

  .store-img {
    margin-right: 10px;
    width: 50px;
    height: 50px;
  }

  .store-img img {
    width: 100%;
    height: 100%;
  }

  .experience-shop {
    font-size: 12px;
  }

  .icon-heart-solid {
    color: #f18074;
  }

  .fans,
  .attention {
    font-size: 12px;
    text-align: center;
    line-height: normal;
  }

  .attention .iconfont {
    font-size: 20px;
  }

  .fans span:first-child {
    margin-top: 3px;
  }

  .split-line {
    margin: 10px 6px 0;
    width: 1px;
    height: 20px;
    background-color: #cdd3d7;
  }

  .vux-tab .vux-tab-item {
    font-size: 16px;
    background: none;
  }

  .store-goods-list,
  .all-goods {
    margin-bottom: 70px;
  }

  .store-goods-list {
    padding: 10px 14px;
    flex-wrap: wrap;
    background-color: #fff;
  }

  .store-goods-item {
    margin-bottom: 10px;
    margin-right: 10px;
    width: 168px;
  }

  .store-goods-item:nth-child(2n) {
    margin-right: 0;
  }

  .store-goods-item img {
    width: 168px;
    height: 168px;
  }

  .store-goods-title {
    font-size: 14px;
  }

  .store-goods-desc {
    font-size: 12px;
    color: #999999;
  }

  .store-goods-price {
    font-size: 14px;
    color: #ff7900;
  }

  .all-goods .goods-list {
    margin-top: 10px;
    background-color: #fff;
  }

  .goods-filter {
    display: flex;
    align-items: center;
    height: 44px;
    background-color: #fff;
  }

  .goods-filter > div {
    flex-grow: 1;
    line-height: normal;
    text-align: center;
    color: #999;
  }

  .goods-filter > div.active {
    color: #222;
  }

  .btn-group {
    position: fixed;
    width: 100%;
    bottom: 0;
    left: 0;
    padding: 16px 0;
    background-color: #fff;
  }

  .btn-group button {
    width: 50%;
    line-height: normal;
    font-size: 16px;
    color: #666;
    border: none;
    background-color: transparent;
  }

  .btn-group button:first-child {
    border-right: 1px solid #cdd3d7;
  }
</style>

<style>
  .vux-slider > .vux-indicator.dots > a > .vux-icon-dot.active {
    width: 12px;
    background-color: #222;
  }
</style>
