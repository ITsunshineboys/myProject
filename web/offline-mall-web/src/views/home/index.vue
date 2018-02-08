<template>
  <div class="home">
    <flexbox class="search_header">
      <flexbox-item class="location" :span="4/25">
        <router-link :to="{path:'/choose-city',query:{cur_city:cur_city}}">
          <p style="white-space:nowrap;overflow: hidden;text-overflow: ellipsis"><i class="iconfont icon-location"></i>{{city}}</p>
        </router-link>
      </flexbox-item>
      <flexbox-item style="margin: 0" :span="20/25">
        <router-link to="search">
          <search v-model="search" cancel-text="" placeholder="请输入想购买的商品" ref="search"></search>
        </router-link>
      </flexbox-item>
    </flexbox>
    <swiper class="swiper_list" dots-position="center" loop auto height="171px" :aspect-ratio="375/171" :show-desc-mask="false">
      <swiper-item @click.native="getClickRecord(item)" v-for="(item,index) in banner_list" :key="index">
        <router-link :to="'/good-detail/'+item.url">
          <img :src="item.image">
        </router-link>
      </swiper-item>
    </swiper>
    <flexbox :gutter="0" class="category" wrap="wrap">
      <flexbox-item  style="text-align: center;padding: 12px 0;" :span="1/4" v-for="(item,index) in category_list"
                    :key="index">
        <router-link :to="'/class/' + item.id">
          <img width="32px" height="32px" :src="item.icon">
          <p style="font-size: 14px;color: #999;line-height: 20px;">{{item.title}}</p>
        </router-link>
      </flexbox-item>
    </flexbox>
    <card :header="{title:'推荐'}" class="command">
      <flexbox align="flex-start" :gutter="0" slot="content" wrap="wrap">

          <flexbox-item @click.native="getClickRecord(item)" style="margin-left: 3.46%!important;" :span="56/125" class="command_list" :class="{odd_col:index%2==0,even_col:index%2==1}"
                        v-for="(item,index) in recommended_list" :key="index">
            <router-link :to="'/good-detail/' + item.url">
              <!--<div style="width: 160px;height: 160px;border-radius: 2px;overflow: hidden;" :style="{'background-image':'url('+item.image+')',backgroundSize:'contain',backgroundRepeat:'no-repeat',backgroundPosition:'center center' }">-->
              <div style="width: 100%;height: 160px;border-radius: 2px;overflow: hidden;background-color: #f4f4f4;">
                <img style="width: 100%;" :src="item.image" alt="">
              </div>
            <p class="command_title nowrap">{{item.title}}</p>
            <p class="command_description nowrap">{{item.description}}</p>
            <p class="command_price">￥{{item.platform_price}}</p>
            </router-link>
          </flexbox-item>
      </flexbox>
    </card>
    <div style="height: 48px;"></div>
    <flexbox class="nav" wrap="wrap" style="background-color:#fff;position: fixed;bottom: 0;">
    <flexbox-item style="text-align: center;margin: 0;padding-top: 4px;" @click.native="goNewModule(item)" :span="1/3" v-for="(item,index) in nav_list" :key="index">
    <img width="20px" :src="item.image">
    <p :style="{color:index==1?'#D9AD65':'#999'}" style="font-size: 12px;">{{item.title}}</p>
    </flexbox-item>
    </flexbox>
  </div>
</template>

<script>
  import {Grid, GridItem, Swiper, SwiperItem, Search, Flexbox, FlexboxItem, Card} from 'vux'
  import JsonData from '../../assets/city'
  import wx from 'weixin-js-sdk'
  export default {
    name: 'home',
    data () {
      return {
        search: '',
        banner_list: [],
        category_list: [],
        recommended_list: [],
        city: '成都',
        cur_city: ['510000', '510100'],
        nav_list: [
          {image: require('../../assets/images/Quote.png'), title: '报价', url: '/mall/index.html'},
          {image: require('../../assets/images/shop.png'), title: '商城'},
          {image: require('../../assets/images/public_fenxiao.png'), title: '分销', url: '/distribution-web/index.html'}
        ],
        wxData: {}
      }
    },
    components: {
      Flexbox,
      FlexboxItem,
      Swiper,
      SwiperItem,
      Search,
      Card,
      Grid,
      GridItem
    },
    methods: {
      goNewModule (item) {
        if (item.url !== undefined) {
          window.location.href = item.url
        }
      },
      getClickRecord (item) {
        this.axios.post('/mall/recommend-click-record', {
          recommend_id: item.id
        }, (res) => {
          console.log(res)
        })
      }
    },
    created () {
      if (this.$route.query.cur_city !== undefined) {
        this.cur_city = this.$route.query.cur_city
        let str = JsonData[0][this.cur_city[0]][this.cur_city[1]]
        this.city = str.substring(0, str.length - 1)
      }
      this.axios.get('/mall/carousel', {
        district_code: this.cur_city[1]
      }, (res) => {
        console.log(res)
        this.banner_list = res.data.carousel
      })
      this.axios.get('/mall/categories', {}, (res) => {
        console.log(res)
        this.category_list = res.data.categories
      })
      this.axios.get('/mall/recommend-second', {
        district_code: this.cur_city[1]
      }, (res) => {
        console.log(res)
        this.recommended_list = res.data.recommend_second
      })
    },
    mounted () {
      this.axios.get('/order/iswxlogin', {}, (res) => {
        res.code === 200 ? sessionStorage.setItem('wxCodeFlag', true) : sessionStorage.getItem('wxCodeFlag', false)
        if (sessionStorage.getItem('wxCodeFlag')) {
          this.wxData = res.data
          wx.config({
            debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
            appId: this.wxData.appId, // 必填，公众号的唯一标识
            timestamp: this.wxData.timestamp, // 必填，生成签名的时间戳
            nonceStr: this.wxData.nonceStr, // 必填，生成签名的随机串
            signature: this.wxData.signature, // 必填，签名，见附录1
            jsApiList: ['onMenuShareAppMessage', 'onMenuShareTimeline'] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
          })
          wx.ready(function () {
//            wx.showAllNonBaseMenuItem()
//            wx.showMenuItems({
//              menuList: ['menuItem:share:appMessage', 'menuItem:share:timeline'] // 要显示的菜单项，所有menu项见附录3
//            })
            wx.onMenuShareAppMessage({
              title: '微信分享给朋友', // 分享标题
              desc: 'This is a test!', // 分享描述
              link: 'http://www.baidu.com', // 分享链接
              imgUrl: 'http://img1.3lian.com/img013/v2/4/d/101.jpg', // 分享图标
              type: 'link', // 分享类型,music、video或link，不填默认为link
              dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
              success: function () {
                // 用户确认分享后执行的回调函数
                alert('确认分享后321')
              },
              cancel: function () {
                // 用户取消分享后执行的回调函数
                alert('取消分享1234')
              }
            })
            wx.onMenuShareTimeline({
              title: 'qwe', // 分享标题
              link: 'http://www.jd.com',
              imgUrl: 'http://img1.3lian.com/img013/v2/4/d/101.jpg', // 分享图标
              success: function () {
                // 用户确认分享后执行的回调函数
                alert('朋友圈分享成功')
              },
              cancel: function () {
                // 用户取消分享后执行的回调函数
                alert('朋友圈分享取消')
              }
            })
          })
        }
        if (sessionStorage.getItem('wxCodeFlag') && !sessionStorage.getItem('wxStatus')) {
          this.axios.post('/order/find-open-id', {
            url: location.href
          }, (res) => {
            location.href = res.data
            sessionStorage.setItem('wxStatus', true)
          })
        } else if (sessionStorage.getItem('wxCodeFlag') && sessionStorage.getItem('wxStatus')) {
          this.openID = location.href.split('code=')[1].split('&state')[0]
          this.axios.post('/order/get-open-id', {
            code: this.openID
          }, (res) => {
            sessionStorage.setItem('openID', res.data)
          })
        }
      })
    }
  }
</script>

<style>
  /*修改默认样式*/
  .search_header {
    background-color: #fff !important;
  }

  .search_header form > label, .search_header form ~ a {
    display: none !important;
  }

  .search_header form {
    background-color: #fff !important;
  }

  .search_header form i {
    color: #000 !important;
  }

  .search_header form:after {
    border-radius: 25px !important;
    background-color: #ece9ee !important;
  }

  .search_header .vux-search-box {
    position: static !important;
  }

  .search_header .vux-search-box > div {
    background-color: #fff !important;
  }

  .command .weui-panel__hd {
    font-size: 16px !important;
    padding: 16px 15px 17px !important;
  }

  .command .weui-panel__hd:after {
    border-bottom: 0 !important;
  }

  .search_header .weui-search-bar {
    padding: 0;
  }

  .search_header .weui-search-bar:before {
    border-top: none;
  }

  .search_header .weui-search-bar__box .weui-search-bar__input {
    font-size: 12px;
  }

  .search_header .weui-search-bar:after {
    border: none;
  }

  .search_header .weui-icon-search {
    font-size: 12px;
  }

  /*定位*/
  .location {
    text-align: center;
    height: 44px;
    line-height: 44px;
  }

  .location p {
    font-size: 12px;
  }

  .location a {
    color: #222 !important;
  }

  .icon-location {
    vertical-align: initial;
    margin-right: 2px;
    line-height: 45px;
  }

  /*推荐*/
  /*.odd_col{!*推荐奇数项*!*/
  /*padding-right: 7.5px;*/
  /*}*/
  /*.even_col{!*推荐偶数项*!*/
  /*padding-left: 7.5px;*/
  /*}*/
  .nowrap {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .command {
    margin-bottom: 10px;
  }

  .command_list { /*推荐列表项*/
    margin-bottom: 9px;
    /*box-shadow: 0px 2px 4px 0px #F6F6F6;*/
  }

  .command_title { /*推荐商品名称*/
    font-size: 14px;
    color: #000;
    margin-top: 6px;
  }

  .command_description { /*推荐商品特色*/
    font-size: 12px;
    color: #999;
  }

  .command_price { /*推荐商品价格*/
    font-size: 14px;
    color: #D9AD65;
    margin-bottom: 10px;
  }

  /*分类*/
  .category {
    margin-bottom: 10px;
    background-color: #fff;
  }

  /*轮播图*/
  .swiper_list img{
    width: 100%;
  }
  .swiper_list .vux-icon-dot.active{
    background-color: #222!important;
  }
</style>
