<template>
  <div class="home">
    <flexbox class="search_header">
      <flexbox-item class="location" :span="4/25">
        <router-link :to="{path:'/choose-city',query:{cur_city:cur_city}}">
          <p style="white-space:nowrap;overflow: hidden;text-overflow: ellipsis"><i class="iconfont icon-location"></i>{{city}}</p>
        </router-link>
      </flexbox-item>
      <flexbox-item style="margin: 0" :span="18/25">
        <router-link to="search">
          <search v-model="search" cancel-text="" placeholder="超级无敌地暖片" ref="search"></search>
        </router-link>
      </flexbox-item>
      <flexbox-item @click.native="goMessage" :span="3/25" style="margin: 0;text-align: center;">
        <i style="font-size:18px;line-height: 31px;" class="iconfont icon-news-square"></i>
      </flexbox-item>
    </flexbox>
    <swiper dots-position="center" :list="banner_list" loop auto height="171px" :aspect-ratio="375/171" :show-desc-mask="false"></swiper>
    <flexbox :gutter="0" class="category" wrap="wrap">
      <flexbox-item style="text-align: center;padding: 12px 0;" :span="1/4" v-for="(item,index) in category_list"
                    :key="index">
        <router-link :to="'/class/' + item.id">
          <img width="32px" height="32px" :src="item.icon">
          <p style="font-size: 14px;color: #999;line-height: 20px;">{{item.title}}</p>
        </router-link>
      </flexbox-item>
    </flexbox>
    <card :header="{title:'推荐'}" class="command">
      <flexbox align="flex-start" :gutter="0" slot="content" wrap="wrap">

          <flexbox-item style="margin-left: 3.46%!important;" :span="56/125" class="command_list" :class="{odd_col:index%2==0,even_col:index%2==1}"
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
    <!--<div class="shopcart">-->
    <!--<p><i class="iconfont icon-home-shoppingcart"></i></p>-->
    <!--<p style="font-size: 12px;color: #999;">购物车</p>-->
    <!--</div>-->
    <!--<div style="height: 64px;"></div>-->
    <!--<flexbox class="nav" wrap="wrap">-->
    <!--<flexbox-item style="text-align: center;margin: 0;" :span="1/4" v-for="(item,index) in nav_list" :key="index">-->
    <!--<img width="23px" height="23px" :src="item.image">-->
    <!--<p :style="{color:index==1?'#D9AD65':'#999'}" style="font-size: 12px;">{{item.title}}</p>-->
    <!--</flexbox-item>-->
    <!--</flexbox>-->
  </div>
</template>

<script>
  import {Grid, GridItem, Swiper, SwiperItem, Search, Flexbox, FlexboxItem, Card} from 'vux'
  import JsonData from '../../assets/city'

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
          {image: require('../../assets/images/Quote.png'), title: '报价'},
          {image: require('../../assets/images/shop.png'), title: '商城'},
          {image: require('../../assets/images/shopping_cart.png'), title: '购物车'},
          {image: require('../../assets/images/Mine.png'), title: '我的'}
        ]
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
      goMessage () {
        window.AndroidWebView.skipMessageCenter()
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
        const imgList = res.data.carousel
        this.banner_list = imgList.map((item, index) => ({
          url: '/good-detail/' + item.url,
          img: item.image,
          title: '',
          id: item.id
        }))
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
      window.AndroidWebView.showTable()
    },
    mounted () {
      this.axios.get('/order/iswxlogin', {}, (res) => {
        res.code === 200 ? sessionStorage.setItem('wxCodeFlag', true) : sessionStorage.getItem('wxCodeFlag', false)
        if (sessionStorage.getItem('wxCodeFlag') && !sessionStorage.getItem('wxStatus')) {
          this.axios.post('/order/find-open-id', {
            url: location.href
          }, (res) => {
            console.log(res)
            console.log(res.data)
            location.href = res.data
            sessionStorage.setItem('wxStatus', true)
          })
        } else if (sessionStorage.getItem('wxCodeFlag') && sessionStorage.getItem('wxStatus')) {
          this.openID = location.href.split('code=')[1].split('&state')[0]
          this.axios.post('/order/get-open-id', {
            code: this.openID
          }, (res) => {
            console.log(this.openID)
            sessionStorage.setItem('openID', res.data)
            console.log(res)
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

  /*底部导航*/
  /*.nav {*/
  /*background-color: #fff;*/
  /*padding: 8px 0;*/
  /*position: fixed;*/
  /*bottom: 0;*/
  /*}*/

  /*购物车*/
  /*.shopcart {*/
  /*background-color: #fff;*/
  /*text-align: center;*/
  /*height: 60px;*/
  /*width: 60px;*/
  /*border-radius: 60px;*/
  /*border: 1px solid #222;*/
  /*position: fixed;*/
  /*top: calc(50% - 30px);*/
  /*right: 30px;*/
  /*}*/

  /*.shopcart i {*/
  /*font-size: 24px;*/
  /*color: #222;*/
  /*}*/
</style>
