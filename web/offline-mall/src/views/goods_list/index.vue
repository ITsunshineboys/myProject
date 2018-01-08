<template>
  <div>
    <v-header :go-link="'/'" @show="isShow"></v-header>
    <div class="goods-filter">
      <div>销量优先</div>
      <div class="active">
        <span>价格</span>
        <span class="sort">
          <span class="iconfont icon-sort-up active"></span><span class="iconfont icon-sort-down"></span>
        </span>
      </div>
      <div>
        <span>好评率</span>
        <span class="sort">
          <span class="iconfont icon-sort-up"></span><span class="iconfont icon-sort-down"></span>
        </span>
      </div>
      <div class="btn-filter" @click="isModalOpen = true">
        <span>筛选</span>
        <span class="iconfont icon-filter"></span>
      </div>
    </div>
    <goods-list></goods-list>

    <!-- 筛选 -->
    <popup class="modal-filter" position="right" v-model="isModalOpen">
      <group class="modal-filter-content">
        <cell :title="'风格'" :is-link="true" :border-intent="false" :arrow-direction="isStyleOpen ? 'up' : 'down'" @click.native="isStyleOpen = !isStyleOpen"></cell>
        <div class="modal-filter-style" v-if="isStyleOpen">
          <checker v-model="styleParams" default-item-class="btn" selected-item-class="btn-primary">
            <checker-item value="1">123123</checker-item>
            <checker-item value="2">1231232132</checker-item>
            <checker-item value="3">3453535</checker-item>
            <checker-item value="4">57683423</checker-item>
            <checker-item value="5">567324</checker-item>
          </checker>
        </div>
        <cell :title="'系列'" :is-link="true" :border-intent="false" :arrow-direction="isSeriesOpen ? 'up' : 'down'" @click.native="isSeriesOpen = !isSeriesOpen"></cell>
        <div class="modal-filter-style" v-if="isSeriesOpen">
          <checker v-model="seriesParams" default-item-class="btn" selected-item-class="btn-primary">
            <checker-item value="1">123123</checker-item>
            <checker-item value="2">1231232132</checker-item>
            <checker-item value="3">3453535</checker-item>
            <checker-item value="4">57683423</checker-item>
            <checker-item value="5">567324</checker-item>
            <checker-item value="5">567324</checker-item>
          </checker>
        </div>
        <cell :title="'价格区间'" :border-intent="false" disabled></cell>
        <div class="modal-filter-style price-range">
          <input type="number" placeholder="最低价"> <span class="iconfont icon-reduce"></span> <input type="number" placeholder="最高价">
        </div>
        <cell :title="'品牌'" :is-link="true" :border-intent="false" :arrow-direction="isBrandOpen ? 'up' : 'down'" @click.native="isBrandOpen = !isBrandOpen"></cell>
        <div class="modal-filter-style" v-if="isBrandOpen">
          <checker v-model="brandParams" default-item-class="btn" selected-item-class="btn-primary" type="checkbox">
            <checker-item value="1">123123</checker-item>
            <checker-item value="2">1231232132</checker-item>
            <checker-item value="3">3453535</checker-item>
            <checker-item value="4">57683423</checker-item>
            <checker-item value="5">567324</checker-item>
            <checker-item value="6">123123</checker-item>
            <checker-item value="7">1231232132</checker-item>
            <checker-item value="8">3453535</checker-item>
            <checker-item value="9">57683423</checker-item>
            <checker-item value="0">567324</checker-item>
            <checker-item value="10">123123</checker-item>
            <checker-item value="12">1231232132</checker-item>
            <checker-item value="13">3453535</checker-item>
            <checker-item value="14">57683423</checker-item>
            <checker-item value="15">最后一个</checker-item>
          </checker>
        </div>
        <div class="btn-group">
          <button class="btn-reset" type="button">重置</button>
          <button class="btn-finish" type="button">完成</button>
        </div>
      </group>
    </popup>
  </div>
</template>

<script>
  import {Popup, Group, Cell, Checker, CheckerItem} from 'vux'
  import vHeader from '@/components/HeaderSearch'
  import GoodsList from '@/components/GoodsList'

  export default {
    components: {
      Popup,
      Group,
      Cell,
      Checker,
      CheckerItem,
      GoodsList,
      vHeader
    },
    data () {
      return {
        styleParams: '',
        seriesParams: '',
        brandParams: [],
        isMoreOpen: false,      // 头部更多选项是否显示
        isModalOpen: false,     // 模态框是否显示
        isStyleOpen: false,     // 风格是否显示
        isSeriesOpen: false,    // 系列是否显示
        isBrandOpen: false      // 品牌是否显示
      }
    },
    methods: {
      isShow (bool) {
        this.isMoreOpen = bool
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
    margin-top: 100px;
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
    padding-top: 10px;
    padding-bottom: 10px;
    width: 72px;
    font-size: 14px;
    text-align: center;
    border: 1px solid #999999;
    border-radius: 2px;
  }

  .weui-cell {
    font-size: 16px;
    color: #666;
  }

  .btn {
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
