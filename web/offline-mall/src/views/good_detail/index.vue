<template>
  <div class="good-container">
    <div class="guide-icon">
      <i class="iconfont icon-return"></i>
      <i class="iconfont icon-share"></i>
      <i class="iconfont icon-more"></i>
    </div>
    <!--<swiper :list="demo03_list" loop auto height="375px" dots-class="custom-bottom" dots-position="center"></swiper>-->
    <swiper loop auto :list="demo03_list" height="375px" dots-class="custom-bottom" dots-position="center"></swiper>
    <div class="good-detail">
      <goodsTitle></goodsTitle>
      <divider></divider>
      <group>
        <cell-box is-link>
          选择数量
        </cell-box>
        <cell-box is-link>
          <div class="service">
            <i class="iconfont icon-blue"></i>
            <span>上门退货</span>
          </div>
          <div class="service">
            <i class="iconfont icon-blue"></i>
            <span>上门退货</span>
          </div>
          <div class="service">
          <i class="iconfont icon-blue"></i>
          <span>上门退货</span>
          </div>
        </cell-box>
      </group>

      <divider></divider>
      <group>
        <cell-box>
          <span class="sum-comment">
            评价
          </span>
          <span>
            (493)
            </span>
        </cell-box>
      </group>
      <comment headshotStyle="headshot-style" nameStyle="name-style" dateStyle="date-style"></comment>
      <group>
        <cell-box>
          <p class="view-all">
            查看全部评价
          </p>
         <i class="iconfont icon-arrow_line_right"></i>
        </cell-box>
      </group>




    </div>


  </div>
</template>

<script>
  import {Swiper, Group, Cell, CellBox} from 'vux'
  import goodsTitle from '../good_detail/title'
  import divider from '../good_detail/divider'
  import comment from '../good_detail/comment.vue'

  const baseList = [{
    url: 'javascript:',
    img: 'https://static.vux.li/demo/1.jpg'
  }, {
    url: 'javascript:',
    img: 'https://static.vux.li/demo/2.jpg'
  }, {
    url: 'javascript:',
    img: 'https://static.vux.li/demo/5.jpg',
    fallbackImg: 'https://static.vux.li/demo/3.jpg'
  }]

  export default {
    name: 'GoodDetail',
    components: {
      Swiper,
      Group,
      Cell,
      CellBox,
      goodsTitle,
      divider,
      comment
    },
    data () {
      return {
        // note: changing this line won't causes changes
        // with hot-reload because the reloaded component
        // preserves its current state and we are modifying
        // its initial state.
        demo03_list: baseList
      }
    }
  }
</script>

<style>
  .good-container {
    background: rgba(255, 255, 255, 1);
  }

  .custom-bottom {
    bottom: 26px;
  }

  /*图标样式*/
  .good-container .guide-icon {
    position: relative;
  }

  .good-container .guide-icon .iconfont {
    position: absolute;
    top: 32px;
    z-index: 999;
    font-size: 15px;
    color: #FFFFFF;
  }

  .good-container .guide-icon .iconfont:after {
    position: absolute;
    content: '';
    left: -7px;
    top: -2px;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: rgba(73, 73, 73, 1);
    z-index: -1;
  }

  .good-container .guide-icon .icon-return {
    left: 14px;
  }

  .good-container .guide-icon .icon-share {
    right: 54px;
  }

  .good-container .guide-icon .icon-more {
    right: 14px;
  }

  .good-container .weui-cells {
    margin-top: 0;
  }

  .good-container .weui-cell {
    padding: 16px 14px;
    font-size: 16px;
    color: rgba(102, 102, 102, 1);
    line-height: 16px;
    font-size: 16px;
  }

  .good-container .service {
    line-height: 16px;
    margin-right: 25px;
  }

  .good-container .icon-blue {
    font-size: 20px;
    color: #222222;
  }

  .good-container .service span {
    font-size: 16px;
    color: rgba(153, 153, 153, 1);
  }

  .good-container .sum-comment {
    font-size:16px;
    margin-right: 10px;
    color:rgba(102,102,102,1);
    line-height:16px;
  }

  /*评价组件样式*/
  .headshot-style {
    width: 50px;
    height: 50px;
    border-radius: 50%;
  }

  .name-style {
    margin-left: 15px;
    font-size:16px;
    color:rgba(153,153,153,1);
  }

  .date-style {
    font-size:14px;
    color:rgba(153,153,153,1);
    line-height:16px;
  }

  .view-all {
    text-align: center;
    font-size:16px;
    color:rgba(34,34,34,1);
    line-height:16px;
  }


</style>
