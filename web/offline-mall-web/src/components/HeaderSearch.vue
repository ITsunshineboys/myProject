<template>
  <div>
    <x-header :left-options="{backText: '', preventGoBack: backWay}" @on-click-back="goHome" class="header-search" :backWay="backWay">

      <router-link :to="'/search'" v-if="search==='true'">
        <search class="search" placeholder="请输入想购买的商品" cancel-text=""></search>
      </router-link>

      <div class="headTitle">{{headTitle}}</div>

      <router-link :to="'/'" v-if="pop==='true'" slot="right">
      <span class="iconfont icon-home"></span>
      </router-link>
    </x-header>
  </div>
</template>

<script>
  import {XHeader, Search} from 'vux'

  export default {
    components: {
      XHeader,
      Search
    },
    props: {
      goLink: {       // backWay 为 true 时，跳转的链接
        type: String,
        default: '/'
      },
      goParams: {     // 跳转指定页面的参数
        type: Object,
        default: function () {
          return {}
        }
      },
      search: '',     // true 为显示 搜索框
      headTitle: '',  // 标题
      pop: '',        // true为显示 返回首页
      backWay: ''     // 返回按钮返回方式，true 为 跳转到 goLink 的地址， false 为返回上一页
    },
    data () {
      return {
        isShow: false
      }
    },
    methods: {
      goHome () {
        console.log(this.goLink, this.goParams)
        this.$router.replace({
          path: this.goLink,
          params: this.goParams
        })
      }
    }
  }
</script>

<style>
  #app .header-search .search {
    margin-top: 4px;
    line-height: 1.6;
  }

  #app .header-search .search .weui-search-bar {
    align-items: center;
    background-color: transparent;
  }

  #app .header-search .search .weui-search-bar:before,
  #app .header-search .search .weui-search-bar:after,
  #app .header-search .search .weui-search-bar__form:after {
    content: none
  }

  #app .header-search .search .weui-search-bar__form {
    border: 1px solid #e5e5e5;
    background-color: transparent;
    border-radius: 20px;
  }

  #app .header-search .search .weui-search-bar__box .weui-icon-search {
    top: 2px;
  }

  #app .header-search .search .weui-search-bar__label {
    display: none;
  }

  #app .header-search .weui-search-bar {
    padding: 0;
  }

  #app .header-search .weui-search-bar__cancel-btn {
    display: none !important;
  }

  #app .vux-header-right .icon-home {
    font-size: 22px;
    color: #000;
  }

  .headTitle {
    font-size: 18px;
    color: rgba(102, 102, 102, 1);
  }
</style>
