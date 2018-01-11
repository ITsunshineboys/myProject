<template>
  <div>
    <headercommon search="false" headTitle="全部评价" pop="true" :backWay="false"></headercommon>
      <tab defaultColor="#999" active-color="#222" bar-active-color="#222" custom-bar-width="50px" class="tab">
        <tab-item selected @on-item-click="tabHandler('all')">全部 （{{count.good + count.medium + count.poor}}）</tab-item>
        <tab-item @on-item-click="tabHandler('good')">好评 （{{count.good}}）</tab-item>
        <tab-item @on-item-click="tabHandler('medium')">中评 （{{count.medium}}）</tab-item>
        <tab-item @on-item-click="tabHandler('poor')">差评 （{{count.poor}}）</tab-item>
      </tab>
    <divider></divider>
    <usercomment v-for="item in comment_details" :key="item.id"
                 :userIcon="item.icon || user_icon" :commentLevel="item.score" :commentDate="item.create_time"
                 :userName="item.name" :content="item.content" :images="item.images"
                 :reply="item.replies"></usercomment>
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
        user_icon: require('../../assets/images/user_icon_default.png'), // 默认用户头像
        comment_details: '',
        last_tab: ''
      }
    },
    created () {
      this.axios.get('/mall/goods-comments', {id: 332}, (res) => {
        this.count.good = Number(res.data.goods_comments.stat.good)
        this.count.medium = Number(res.data.goods_comments.stat.medium)
        this.count.poor = Number(res.data.goods_comments.stat.poor)
        this.comment_details = res.data.goods_comments.details
      })
    },
    methods: {
      // 选项卡切换优化方法
      tabHandler: function (obj) {
        if (this.last_tab !== obj) {
          this.last_tab = obj
          this.tabContent(obj)
        } else {
          return   // eslint-disable-line
        }
      },
      tabContent: function (obj) {
        this.axios.get('/mall/goods-comments', {id: 332, level_score: obj === 'all' ? '' : obj}, (res) => {
          this.comment_details = res.data.goods_comments.details
        })
      }
    }
  }
</script>

<style scoped>
  .tab {
    width: 100%;
    margin-top: 46px;
  }

  .tab > div {
    font-size: 12px;
    color: rgba(34, 34, 34, 1);
  }

</style>
