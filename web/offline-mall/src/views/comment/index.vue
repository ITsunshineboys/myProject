<template>
  <div>
    <headercommon search="false" headTitle="全部评价" pop="true" :backWay="false"></headercommon>
    <tab defaultColor="#999" active-color="#222" bar-active-color="#222" custom-bar-width="50px" class="tab">
      <tab-item selected @on-item-click="tabHandler('')">全部 （{{count.good + count.medium + count.poor}}）</tab-item>
      <tab-item @on-item-click="tabHandler('good')">好评 （{{count.good}}）</tab-item>
      <tab-item @on-item-click="tabHandler('medium')">中评 （{{count.medium}}）</tab-item>
      <tab-item @on-item-click="tabHandler('poor')">差评 （{{count.poor}}）</tab-item>
    </tab>
    <!--<divider class="divider"></divider>-->
    <!--<scroller :on-infinite="infinite" ref="scrollerdom" style="padding-top:100px;">-->
    <div class="comments-container">
      <usercomment v-for="item in comment_details" :key="item.id"
                   :userIcon="item.icon || user_icon" :commentLevel="item.score" :commentDate="item.create_time"
                   :userName="item.name" :content="item.content" :images="item.images"
                   :reply="item.replies"></usercomment>
    </div>
    <p v-show="isLoading" class="tip-loading">{{loadingText}}</p>
    <!--</scroller>-->
  </div>
</template>

<script>
  import {Tab, TabItem} from 'vux'
  import headercommon from '@/components/HeaderSearch'
  import divider from '@/components/Divider'
  import usercomment from '../comment/user_comment'
  export default {
    name: 'AllComment',
    components: {
      headercommon,
      divider,
      Tab,
      TabItem,
      usercomment
    },
    data () {
      return {
        count: {
          all: 0,     // 全部评论数
          good: 0,    // 好评数
          medium: 0,  // 中评数
          poor: 0     // 差评数
        },
        totalpage: 1, // 总页数
        loadingText: '加载中...',      // 加载提示信息，默认为加载中...
        user_icon: require('../../assets/images/user_icon_default.png'), // 默认用户头像
        comment_details: [],
        last_tab: '',
        isLoading: false,
        params: {
          id: '',
          page: 1,
          level_score: ''
        }
      }
    },
    created () {
      this.params.id = this.$route.params.id // 商品id
      this.axios.get('/mall/goods-comments', this.params, (res) => {
        this.count.good = Number(res.data.goods_comments.stat.good)
        this.count.medium = Number(res.data.goods_comments.stat.medium)
        this.count.poor = Number(res.data.goods_comments.stat.poor)
        this.comment_details = res.data.goods_comments.details
        this.isLoading = false      // 加载成功取消加载状态
        this.totalPage = Math.ceil((this.count.good + this.count.medium + this.count.poor) / 12)
        for (let key in this.comment_details) {
          this.comment_details[key].images = this.comment_details[key].images.map((item) => ({
            src: item
          }))
        }
      })
    },
    mounted () {
      window.addEventListener('scroll', this.handleScroll)
    },
    methods: {
      // 选项卡切换方法
      tabHandler: function (obj) {
        if (this.last_tab !== obj) {
          this.last_tab = obj
          this.params.level_score = obj
          this.params.page = 1
          this.comment_details = []
          this.getData()
        } else {
          return   // eslint-disable-line
        }
      },
      getData () {  // 获取数据
        this.isLoading = true     // 加载成功取消加载状态
        this.axios.get('/mall/goods-comments', this.params, (res) => {
          this.count.good = Number(res.data.goods_comments.stat.good)
          this.count.medium = Number(res.data.goods_comments.stat.medium)
          this.count.poor = Number(res.data.goods_comments.stat.poor)
          this.isLoading = false      // 加载成功取消加载状态
          if (this.params.level_score === '') {
            this.totalPage = Math.ceil((this.count.good + this.count.medium + this.count.poor) / 12)
          } else {
            this.totalPage = Math.ceil((this.count[this.params.level_score]) / 12)
          }
          for (let key in res.data.goods_comments.details) {
            res.data.goods_comments.details[key].images = res.data.goods_comments.details[key].images.map((item) => ({
              src: item
            }))
          }
          this.comment_details = this.comment_details.concat(res.data.goods_comments.details)
        })
      },
      // 滚动加载
      handleScroll () {
        let scrollTop = document.documentElement.scrollTop || document.body.scrollTop || window.pageYOffset     // 滚动条位置
        sessionStorage.setItem('commentPos', scrollTop)      // 记录滚动条位置
        if (this.isLoading) return    // 如果还在加载中，就跳出函数
        let node, top, nodeTop
        node = document.querySelector('.comment-container:last-child')
        top = document.documentElement.clientHeight     // 获取网页可视高度
        nodeTop = node.getBoundingClientRect().top + 100      // 获取商品离可视区域的距离
        if (nodeTop <= top) {
          this.isLoading = true           // 显示加载提示
          if (this.params.page < this.totalPage) {      // 判断当前页是否小于最后一页
            this.loadingText = '加载中...'
            this.params.page++     // 当前页 + 1
            this.getData()      // 请求店铺推荐商品数据
          } else {
            this.loadingText = '没有更多数据了'
          }
        }
      }
    }
  }
</script>

<style scoped>
  .tab {
    width: 100%;
    position: fixed;
    top: 40px;
    z-index: 100;
  }

  .tab > div {
    font-size: 12px;
    color: rgba(34, 34, 34, 1);
  }

  .comments-container {
    margin-top: 94px;
  }
</style>
