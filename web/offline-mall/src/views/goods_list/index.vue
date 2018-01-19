<template>
  <div>
    <header-search pop="true" search="true"></header-search>
    <div class="goods-filter">
      <div :class="{active: sortName === 'sold_number'}" @click="tabHandle('sold_number')">销量优先</div>
      <div :class="{active: sortName === 'platform_price'}"  @click="tabHandle('platform_price')">
        <span>价格</span>
        <span class="sort">
          <span :class="{active: platformPriceSortNum === 4 & sortName === 'platform_price'}" class="iconfont icon-sort-up"></span>
          <span :class="{active: platformPriceSortNum === 3 & sortName === 'platform_price'}" class="iconfont icon-sort-down"></span>
        </span>
      </div>
      <div :class="{active: sortName === 'favourable_comment_rate'}" @click="tabHandle('favourable_comment_rate')">
        <span>好评率</span>
        <span class="sort">
          <span :class="{active: favourableCommentRateSortNum === 4 & sortName === 'favourable_comment_rate'}" class="iconfont icon-sort-up"></span>
          <span :class="{active: favourableCommentRateSortNum === 3 & sortName === 'favourable_comment_rate'}" class="iconfont icon-sort-down"></span>
        </span>
      </div>
      <div class="btn-filter" @click="filterModalData">
        <span>筛选</span>
        <span class="iconfont icon-filter"></span>
      </div>
    </div>
    <scroller :on-infinite="init" ref="scrollerDom" style="top: 90px;">
      <goods-list :goods-list="goodsListData"></goods-list>
    </scroller>

    <!-- 筛选 -->
    <popup class="modal-filter" position="right" v-model="isModalOpen">
      <group class="modal-filter-content">
        <cell :title="'风格'" :is-link="true" :border-intent="false" :arrow-direction="isStyleOpen ? 'up' : 'down'" @click.native="isStyleOpen = !isStyleOpen"></cell>
        <div class="modal-filter-style" v-if="isStyleOpen">
          <checker v-model="filterParams.styleParams" default-item-class="btn" selected-item-class="btn-primary" v-if="styleData.length !== 0">
            <checker-item v-for="obj in styleData" :value="obj.id" :key="obj.id">{{obj.name}}</checker-item>
          </checker>
          <p v-else>暂无风格</p>
        </div>
        <cell :title="'系列'" :is-link="true" :border-intent="false" :arrow-direction="isSeriesOpen ? 'up' : 'down'" @click.native="isSeriesOpen = !isSeriesOpen"></cell>
        <div class="modal-filter-style" v-if="isSeriesOpen">
          <checker v-model="filterParams.seriesParams" default-item-class="btn" selected-item-class="btn-primary" v-if="seriesData.length !== 0">
            <checker-item v-for="obj in seriesData" :value="obj.id" :key="obj.id">{{obj.name}}</checker-item>
          </checker>
          <p v-else>暂无风格</p>
        </div>
        <cell :title="'价格区间'" :border-intent="false" disabled></cell>
        <div class="modal-filter-style price-range">
          <input type="number" placeholder="最低价" v-model="filterParams.priceMin">
          <span class="iconfont icon-reduce"></span>
          <input type="number" placeholder="最高价" v-model="filterParams.priceMax">
        </div>
        <cell :title="'品牌'" :is-link="true" :border-intent="false" :arrow-direction="isBrandOpen ? 'up' : 'down'" @click.native="isBrandOpen = !isBrandOpen"></cell>
        <div class="modal-filter-style" v-if="isBrandOpen">
          <checker v-model="filterParams.brandParams" default-item-class="btn" selected-item-class="btn-primary" type="checkbox" v-if="brandData.length !== 0">
            <checker-item v-for="obj in brandData" :value="obj.id" :key="obj.id">{{obj.name}}</checker-item>
          </checker>
          <p v-else>暂无风格</p>
        </div>
        <div class="btn-group">
          <button class="btn-reset" type="button" @click="filterReset">重置</button>
          <button class="btn-finish" type="button" @click="filterFinish">完成</button>
        </div>
      </group>
    </popup>
  </div>
</template>

<script>
  import {Popup, Group, Cell, Checker, CheckerItem} from 'vux'
  import HeaderSearch from '@/components/HeaderSearch'
  import GoodsList from '@/components/GoodsList'

  export default {
    components: {
      Popup,
      Group,
      Cell,
      Checker,
      CheckerItem,
      GoodsList,
      HeaderSearch
    },
    data () {
      return {
        isMoreOpen: false,      // 头部更多选项是否显示
        isModalOpen: false,     // 模态框是否显示
        isStyleOpen: false,     // 风格是否显示
        isSeriesOpen: false,    // 系列是否显示
        isBrandOpen: false,     // 品牌是否显示
        sortName: 'sold_number',      // tab排序名称  sold_number(销量优先)  platform_price(价格) favourable_comment_rate(好评率)
        platformPriceSortNum: 4,      // 价格排序方式   3：降序 4：升序
        favourableCommentRateSortNum: 4,      // 好评率排序方式   3：降序 4：升序
        styleData: [],          // 筛选模态框风格数据
        seriesData: [],         // 筛选模态框系列数据
        brandData: [],          // 筛选模态框品牌数据
        goodsListData: [],      // 商品列表数据
        totalPage: 0,               // 商品列表数据总页数
        goodsListParams: {
          district_code: 510100,        // 城市 code
          category_id: null,           // 3级分类ID
          'sort[]': 'sold_number:3',   // 默认按照销量优先降序排序
          page: 1,
          brand_id: null,     // 筛选品牌ID
          style_id: null,     // 筛选风格ID
          series_id: null     // 筛选系列ID
        },
        filterParams: {     // 模态框筛选
          styleParams: '',  // 筛选模态框里，选中的风格
          seriesParams: '', // 筛选模态框里，选中的系列
          brandParams: [],  // 筛选模态框里，选中的品牌（可多选）
          priceMin: '',     // 最低价
          priceMax: ''      // 最高价
        }
      }
    },
    activated () {
      this.goodsListData = []
      this.getGoodsList()
    },
    methods: {
      tabHandle (str) {
        this.sortName = str
        switch (str) {      // 只有在价格和好评率会做排序
          case 'platform_price':
            // 点击价格 tab 默认将默认为降序
            this.platformPriceSortNum = this.platformPriceSortNum === 4 ? 3 : 4     // 第一次点击价格 tab 为降序：3
            this.favourableCommentRateSortNum = 4                                   // 将好评率改为升序
            this.goodsListParams['sort[]'] = this.sortName + ':' + this.platformPriceSortNum
            break
          case 'favourable_comment_rate':
            // 点击好评率 tab 默认将默认为降序
            this.favourableCommentRateSortNum = this.favourableCommentRateSortNum === 4 ? 3 : 4     // 第一次点击好评率 tab 为降序：3
            this.platformPriceSortNum = 4                                                           // 将价格改为升序
            this.goodsListParams['sort[]'] = this.sortName + ':' + this.favourableCommentRateSortNum
            break
          default:
            this.allGoodsParams['sort[]'] = this.sortName + ':' + 3
        }
        this.getGoodsList()
      },
      getGoodsList () {     // 商品列表数据请求
        this.goodsListParams.category_id = this.$route.params.id      // 获取三级分类ID
        this.axios.get('/mall/category-goods', this.goodsListParams, res => {
          console.log(res)
          let data = res.data
          this.goodsListData = this.goodsListData.concat(data.category_goods)
          this.totalPage = Math.ceil(data.total / 12)
        })
      },
      filterModalData () {      // 模态框数据请求
        this.isModalOpen = true     // 显示筛选模态框
        this.axios.get('/mall/category-brands-styles-series', {category_id: this.$route.params.id}, res => {
          console.log(res, '风格、系列和品牌')
          let data = res.data.category_brands_styles_series
          this.styleData = data.styles
          this.seriesData = data.series
          this.brandData = data.brands
        })
      },
      filterFinish () {     // 完成筛选
        // 商品列表请求参数
        this.goodsListParams.platform_price_min = this.filterParams.priceMin * 100   // 最低价
        this.goodsListParams.platform_price_max = this.filterParams.priceMax * 100   // 最高价
        this.goodsListParams.brand_id = this.filterParams.brandParams.join(',')       // 品牌id，可传多个
        this.goodsListParams.style_id = this.filterParams.styleParams                 // 风格id
        this.goodsListParams.series_id = this.filterParams.seriesParams               // 系列id
        this.isModalOpen = false      // 隐藏模态框
        this.getGoodsList()           // 请求商品列表数据
      },
      filterReset () {      // 重置筛选
        this.filterParams = {     // 模态框筛选
          styleParams: '',      // 筛选模态框里，选中的风格
          seriesParams: '',     // 筛选模态框里，选中的系列
          brandParams: [],      // 筛选模态框里，选中的品牌（可多选）
          priceMin: '',         // 最低价
          priceMax: ''          // 最高价
        }
        this.goodsListParams.platform_price_min = null      // 最低价
        this.goodsListParams.platform_price_max = null      // 最高价
        this.goodsListParams.brand_id = null                // 品牌id，可传多个
        this.goodsListParams.style_id = null                // 风格id
        this.goodsListParams.series_id = null               // 系列id
        this.isModalOpen = false      // 隐藏模态框
        this.getGoodsList()           // 请求商品列表数据
      },
      init (done) {
        let vm = this
        if (vm.goodsListParams.page >= vm.totalPage) {
          this.$refs.scrollerDom.finishInfinite(2)
          console.log(this.$refs)
          return
        }
        setTimeout(() => {
          vm.goodsListParams.page ++
          vm.getGoodsList()
          done()
        }, 1500)
      }
    }
  }
</script>

<style scoped>
  .goods-filter {
    position: fixed;
    width: 100%;
    top: 46px;
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

  .btn-filter {
    border-left: 1px solid #cdd3d7;
  }

  .btn-filter > .iconfont {
    color: #222;
  }

  .goods-list {
    /*margin-top: 100px;*/
    background-color: #fff;
  }

  .modal-filter {
    width: 90% !important;
    background-color: #fff;
  }

  .modal-filter-style {
    margin-bottom: 49px;
    padding: 5px 15px;
    font-size: 14px;
  }

  .price-range input {
    padding: 10px 5px;
    width: 72px;
    font-size: 14px;
    text-align: center;
    outline: none;
    border: 1px solid #999999;
    border-radius: 2px;
  }

  .weui-cell {
    font-size: 16px;
    color: #666;
  }

  .btn {
    margin-right: 5px;
    margin-top: 10px;
    padding-left: 2px;
    padding-right: 2px;
    width: 22%;
    height: 28px;
    line-height: 28px;
    color: #959292;
    text-align: center;
    border: 1px solid #999;
    border-radius: 6px;
    white-space: nowrap;
    text-overflow: ellipsis;
    overflow: hidden;
  }

  .btn-primary {
    color: #222;
    border: 1px solid #222;
  }

  .btn-group {
    position: fixed;
    bottom: 0;
    left: 10%;
    width: 90%;
    font-size: 0;
    background-color: #fff;
    border-top: 1px solid #cdd3d7;
  }

  .btn-group button {
    width: 50%;
    height: 48px;
    line-height: 48px;
    text-align: center;
    font-size: 16px;
    border: none;
  }

  .btn-reset {
    color: #666;
    background-color: #fff;
  }

  .btn-finish {
    color: #fff;
    background-color: #222222;
  }
</style>

<style>
  .modal-filter .weui-cells:before {
    border-top: none;
  }

  .modal-filter .weui-cells:after {
    border-bottom: none;
  }

  .modal-filter .vux-cell-disabled .vux-label {
    color: #666;
  }

  .modal-filter .vux-cell-disabled.weui-cell_access .weui-cell__ft:after {
    border-color: #C8C8CD;
  }
</style>
