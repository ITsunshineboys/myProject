<template>
  <div>
    <x-header :left-options="{backText: '', preventGoBack: true}" @on-click-back="goHome" class="header-search">
      <router-link :to="'/search'">
        <search class="search" placeholder="请输入想购买的商品" cancel-text=""></search>
      </router-link>
      <span class="iconfont icon-more" slot="right" @click="showMore"></span>
    </x-header>

    <div class="pop-down" v-show="isShow" @click="showMore">
      <ul>
        <router-link :to="'/'" tag="li">
          <span class="iconfont icon-home"></span>
          <span class="pop-text">商城首页</span>
        </router-link>
        <li>
          <span class="iconfont icon-news-circle"></span>
          <span class="pop-text">消息</span>
          <span class="pop-dot"></span>
        </li>
      </ul>
    </div>
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
      goLink: {
        type: String,
        default: '/'
      },
      goParams: {
        type: Object,
        default: function () {
          return {}
        }
      }
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
      },
      showMore () {
        this.isShow = !this.isShow
        this.$emit('show', this.isShow)
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

  .pop-down {
    position: fixed;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    z-index: 900;
    margin-top: 0 !important;
    background-color: transparent;
  }

  .pop-down ul {
    position: absolute;
    top: 45px;
    right: 9px;
    padding-left: 0;
    width: 140px;
    list-style-type: none;
    background-color: #fff;
    border: 1px solid #e5e5e5;
    border-radius: 2px;
  }

  .pop-down ul:before {
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

  .pop-down ul li {
    display: flex;
    align-items: center;
    width: 100%;
    height: 48px;
    line-height: 48px;
  }

  .pop-down ul li .iconfont {
    flex: 0 0;
    padding: 0 14px;
    font-size: 18px;
  }

  .pop-text {
    flex: 1 1;
    color: #666;
    border-bottom: 1px solid #e6e6e6;
  }

  .pop-down ul li:last-child .pop-text {
    border-bottom: none;
  }

  .pop-dot {
    margin-right: 14px;
    width: 8px;
    height: 8px;
    background-color: #d9ad65;
    border-radius: 50%;
  }
</style>
