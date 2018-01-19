<template>
  <div>
    <search v-model="search" @on-cancel="cancelSearch" @on-change="getGoods" class="search" cancel-text="取消"
            placeholder="请输入想要购买的商品，如：冰箱" ref="search"></search>
    <group v-if="good_list.length!=0" v-for="(item,index) of good_list" :key="index"
           :class="{hide_margin:index!=0,hide_top:index==0,hide_bottom:index==good_list.length-1}" class="search_list"
           label-width="375" label-align="left">
      <p>{{item.title}}</p>
    </group>
    <p v-if="typeof good_list=='object'&&good_list.length==0" style="padding: 40px 0;text-align: center;font-size: 16px;color: #999;">暂无数据显示</p>
    <card v-if="search === ''&& history_list.length != 0" class="history" :header="{title:'搜索历史'}">
      <flexbox slot="content" orient="vertical">
        <flexbox-item @click.native="inquiry(item)" v-for="(item,index) of history_list" :key="index">
          <p>{{item}}</p>
        </flexbox-item>
      </flexbox>
      <x-button @click.native="removeHistory" slot="footer">清除历史搜索</x-button>
    </card>
  </div>
</template>

<script>
  import {Search, Group, Card, Flexbox, FlexboxItem, XButton} from 'vux'

  export default {
    name: 'grabble',
    data () {
      return {
        search: '',
        good_list: '',
        history_list: []
      }
    },
    components: {
      Search,
      Group,
      Card,
      Flexbox,
      FlexboxItem,
      XButton
    },
    methods: {
      getGoods () {
        if (this.search === '') {
          this.good_list = ''
          console.log(this.good_list)
        } else {
          if (this.history_list.indexOf(this.search) === -1) {
            if (this.history_list.length < 6) {
              this.history_list.push(this.search)
            } else {
              this.history_list.unshift(this.search)
              this.history_list.shift()
            }
          }
          localStorage.setItem('history_list', JSON.stringify(this.history_list))
          this.axios.get('/mall/search', {
            keyword: this.search
          }, (res) => {
            console.log(res)
            if (res.data.search.categories.length !== 0) {
              this.good_list = res.data.search.categories
            } else {
              this.good_list = res.data.search.goods
            }
            console.log(this.good_list)
          })
        }
      },
      cancelSearch () {
        this.search = ''
        this.good_list = ''
        this.$router.go(-1)
      },
      removeHistory () {
        localStorage.removeItem('history_list')
        this.history_list = []
      },
      inquiry (item) {
        this.search = item
        this.getGoods()
      }
    },
    created () {
      if (localStorage.getItem('history_list') !== null) {
        this.history_list = JSON.parse(localStorage.getItem('history_list'))
      }
      this.$nextTick(() => {
        console.log(this.$refs.search.$refs.input.nextElementSibling.classList)
        this.$refs.search.$refs.input.nextElementSibling.addEventListener('click', () => {
          this.search = ''
          this.good_list = ''
        })
      })
    }
  }
</script>

<style>
  /*修改默认样式*/
  body {
    background-color: #fff;
  }

  .search form ~ a {
    display: block !important;
  }

  .search form > label {
    display: none !important;
  }

  .search .weui-icon-search {
    line-height: 30px !important;
  }

  .search .weui-search-bar {
    padding-bottom: 16px !important;
  }

  .search .weui-search-bar__input {
    height: 1.63265306em !important;
    line-height: 1.63265306em !important;
  }

  .search form {
    background-color: #fff !important;
  }

  .search form:after {
    border-radius: 30px !important;
  }

  .search {
    position: static !important;
  }

  .search .weui-search-bar {
    background-color: #fff !important;
  }

  .search .weui-cells {
    display: none !important;
  }

  .search a {
    color: #666 !important;
    font-size: 16px !important;
  }

  .search .weui-search-bar:after {
    border: 0 !important;
  }

  .search_list p {
    height: 48px;
    line-height: 48px;
    font-size: 16px;
    color: #666;
  }

  .search_list .weui-cells {
    margin: 0 0 0 14px !important;
  }

  .hide_bottom .weui-cells:after {
    border-bottom: 0 !important;
  }

  .hide_top .weui-cells:before {
    border-top: 0 !important;
  }

  .hide_margin .weui-cells {
    margin-top: 0 !important;
  }
  /*搜索历史*/
  .history .weui-panel__hd{
    color: #222;
    font-size: 16px;
  }
  .history .weui-panel__hd:after{
    border-bottom: 0;
  }
  .history .weui-panel__bd p{
    padding-left: 15px;
    color: #959292;
    font-size: 14px;
  }
  .history button{
    width: 140px!important;
    background-color: #fff;
    color: #666;
    font-size: 16px;
    margin-top: 16px;
  }
  .weui-panel:after{
    border-bottom:0!important;
  }
</style>
