<template>
  <div>
    <HeaderCommon headTitle="首页"></HeaderCommon>
    <tab defaultColor="#999" active-color="#222" bar-active-color="#222" custom-bar-width="50px" class="tab">
      <tab-item selected>全部 （{{count.good + count.medium + count.poor}}）</tab-item>
      <tab-item>好评 （{{count.good}}）</tab-item>
      <tab-item>中评 （{{count.medium}}）</tab-item>
      <tab-item>差评 （{{count.poor}}）</tab-item>
    </tab>
    <Divider></Divider>
    <userComment v-for="item in comment_details" :key="item.id"
                 :userIcon="item.icon || user_icon"  :commentLevel="item.score" :commentDate="item.create_time" :userName="item.name" :content="item.content" :images="item.images" :reply="item.replies"></userComment>
  </div>
</template>

<script>
  import {Tab, TabItem} from 'vux'
  import HeaderCommon from '@/components/HeaderCommon'
  import Divider from '@/components/Divider'
  import userComment from '../comment/user_comment'
  export default {
    name: 'AllComment',
    components: {
      HeaderCommon,
      Divider,
      Tab,
      TabItem,
      userComment
    },
    data () {
      return {
        count: {
          all: 0,
          good: 0,
          medium: 0,
          poor: 0
        },
        user_icon: require('../../assets/images/user_icon_default.png') // 默认用户头像
      }
    },
    created () {
      this.axios.get('/mall/goods-comments', {id: 332}, (res) => {
        this.count.good = Number(res.data.goods_comments.stat.good)
        this.count.medium = Number(res.data.goods_comments.stat.medium)
        this.count.poor = Number(res.data.goods_comments.stat.poor)
        this.comment_details = res.data.goods_comments.details
      })
    }
  }
</script>

<style scoped>
  .tab {
    width: 100%;
  }

  .tab > div {
    font-size: 12px;
    color: rgba(34, 34, 34, 1);
  }

</style>
