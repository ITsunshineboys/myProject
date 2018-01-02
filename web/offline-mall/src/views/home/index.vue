<template>
  <div class="home">
    <flexbox class="search_header">
      <flexbox-item class="location" :span="4/25">
        <router-link :to="{path:'/choose_city',query:{cur_city:cur_city}}">
          <p><i class="iconfont icon-white"></i>{{city}}</p>
        </router-link>
      </flexbox-item>
      <flexbox-item style="margin: 0" :span="18/25">
        <router-link to="search">
          <search v-model="search" cancel-text="" placeholder="超级无敌地暖片" ref="search"></search>
        </router-link>
      </flexbox-item>
      <flexbox-item :span="3/25" style="margin: 0;">
        <i style="font-size:24px;" class="iconfont icon-messages-blue"></i>
      </flexbox-item>
    </flexbox>
    <swiper dots-position="center" :list="banner_list" loop auto height="171px" :aspect-ratio="375/171"></swiper>
    <flexbox :gutter="0" class="category" wrap="wrap">
      <flexbox-item style="text-align: center;padding: 12px 0;" :span="1/4" v-for="(item,index) in category_list"
                    :key="index">
        <img width="32px" height="32px" :src="item.icon">
        <p style="font-size: 14px;color: #999;line-height: 20px;">{{item.title}}</p>
      </flexbox-item>
    </flexbox>
    <card :header="{title:'推荐'}" class="command">
      <flexbox justify="space-around" align="flex-start" :gutter="0" slot="content" wrap="wrap">
        <flexbox-item :span="56/125" class="command_list" :class="{odd_col:index%2==0,even_col:index%2==1}"
                      v-for="(item,index) in recommended_list" :key="index">
          <img width="168px" height="160px" :src="item.image" alt="">
          <p class="command_title nowrap">{{item.title}}</p>
          <p class="command_description nowrap">{{item.description}}</p>
          <p class="command_price">￥{{item.platform_price}}</p>
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
    method: {},
    activated () {
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
          url: item.url,
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
    background-color: #C8C8C8 !important;
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

  /*推荐*/
  /*.odd_col{!*推荐奇数项*!*/
  /*padding-right: 7.5px;*/
  /*}*/
  /*.even_col{!*推荐偶数项*!*/
  /*padding-left: 7.5px;*/
  /*}*/
  .nowrap{
    white-space: nowrap;
    overflow: hidden;
    text-overflow:ellipsis;
  }
  .command {
    margin-bottom: 10px;
  }

  .command_list { /*推荐列表项*/
    margin-bottom: 9px;
    box-shadow: 0px 2px 4px 0px #F6F6F6;
  }

  .command_title { /*推荐商品名称*/
    font-size: 14px;
    color: #000;
  }

  .command_description { /*推荐商品特色*/
    font-size: 12px;
    color: #999;
  }

  .command_price { /*推荐商品价格*/
    font-size: 14px;
    color: #FF7900;
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
