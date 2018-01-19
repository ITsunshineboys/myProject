<template>
  <div>
    <header-search headTitle="店铺介绍" pop="true"></header-search>
    <div class="store" flex="main:justify cross:center">
      <div flex>
        <div class="store-img">
          <img src="http://service.cdlhzz.cn/static/image/2017/12/18/1513588491053.jpg">
        </div>
        <div class="store-name">
          <p>{{storeData.shop_name}}</p>
          <p class="experience-shop" v-if="storeData.is_line_supplier === 1" @click="isShowAlert = true">线下体验店</p>
        </div>
      </div>
      <div flex>
        <div class="attention" flex="dir:top main:justify">
          <span class="iconfont" :class="{'icon-heart': isAttention === 0, 'icon-heart-solid': isAttention === 1}"></span>
          <span>关注</span>
        </div>
        <div class="split-line"></div>
        <div class="fans" flex="dir:top main:justify">
          <span>{{storeData.follower_number}}</span>
          <span>粉丝数</span>
        </div>
      </div>
    </div>
    <ul class="store-info">
      <li flex="main:justify cross:center">
        <span class="store-title">店铺号</span>
        <span class="store-msg">{{storeData.shop_no}}</span>
      </li>
      <li flex="main:justify cross:center">
        <span class="store-title">开店时间</span>
        <span class="store-msg">{{storeData.open_shop_time}}</span>
      </li>
    </ul>

    <ul class="store-info">
      <li flex="main:justify cross:center">
        <span class="store-title">综合评分</span>
        <span class="store-score">{{storeData.comprehensive_score}}</span>
      </li>
      <li flex="main:justify cross:center">
        <span class="store-title">配送员服务</span>
        <span class="store-score">{{storeData.delivery_service_score}}</span>
      </li>
    </ul>
    <ul class="store-info">
      <!--<li flex="main:justify cross:center">-->
        <!--<span class="store-title">掌柜名</span>-->
        <!--<span class="store-msg" flex="cross:center">-->
          <!--<span>冬瓜先生</span>-->
          <!--<span class="iconfont icon-service"></span>-->
        <!--</span>-->
      <!--</li>-->
      <li flex="main:justify cross:center">
        <span class="store-title">质保金</span>
        <span class="store-score" style="color: #d9ad65;">{{storeData.quality_guarantee_deposit}}质保金</span>
      </li>
    </ul>
    <!-- 线下体验店详情弹窗 -->
    <offline-alert @isShow="isShow" :show="isShowAlert" :offlineInfo="offlineInfo"></offline-alert>
  </div>
</template>

<script>
  import HeaderSearch from '@/components/HeaderSearch'
  import OfflineAlert from '@/components/OfflineAlert'

  export default {
    components: {
      HeaderSearch,
      OfflineAlert
    },
    data () {
      return {
        isShowAlert: false,
        isAttention: 0,
        offlineInfo: {
          address: '',
          iphone: ''
        },
        storeData: {}
      }
    },
    activated () {
      this.axios.get('/supplier/view', {id: this.$route.params.id}, res => {
        console.log(res, '店铺简介')
        let data = res.data.supplier_view
        this.storeData = data
        this.offlineInfo.address = data.district
        this.offlineInfo.iphone = data.line_supplier_mobile
        this.isAttention = data.is_follow
      })
    },
    methods: {
      isShow (bool) {
        this.isShowAlert = bool
      },
      contactShop () {      // 联系商家
        // do something
      },
      attentionStore () {
        // 关注店铺
        let params = {
          supplier_id: this.$route.params.id,
          status: this.isAttention === 1 ? 0 : 1
        }
        this.axios.post('/user-follow/user-follow-shop', params, res => {
          console.log(res, '关注')
          this.isAttention = params.status
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

  .store-info {
    margin-top: 10px;
    padding-left: 14px;
    list-style-type: none;
    background-color: #fff;
  }

  .store-info > li{
    padding-right: 14px;
    height: 48px;
    border-top: 1px solid #e9edee;
  }

  .store-info > li:first-child {
    border-top: none;
  }

  .store-title {
    font-size: 16px;
    color: #666666;
  }

  .store-msg {
    font-size: 14px;
    color: #999999;
  }

  .store-msg .iconfont {
    margin-left: 5px;
    color: #222;
    font-size: 18px;
  }

  .store-score {
    font-size: 14px;
    color: #222222;
  }
</style>
