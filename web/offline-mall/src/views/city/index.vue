<template>
  <div>
    <x-header @on-click-back="goHome" class="header" :left-options="{backText:'',preventGoBack:true}">选择所在城市
    </x-header>
    <div class="choose_city">{{city}}</div>
    <popup-picker style="display: none" :show.sync="showPopupPicker" :show-cell="false" @on-change="getData" :display-format="format" class="location" :columns="2"  :data="city_list"
                  v-model="cur_city" value-text-align="left"></popup-picker>
    <x-button class="city_btn" text="选择城市" @click.native="showPopupPicker = true"></x-button>
  </div>
</template>

<script>
  import {XHeader, XAddress, PopupPicker, XButton} from 'vux'
  import JsonData from '../../assets/city'

  export default {
    name: 'city',
    data () {
      return {
        cur_city: ['510000', '510100'],
        city: '成都市',
        city_list: [],
        showPopupPicker: false,
        format: function (value, name) {
          return `${name.split(' ')[1]}`
        }
      }
    },
    components: {
      XHeader,
      PopupPicker,
      XButton,
      XAddress
    },
    created () {
      console.log(this.$route.query)
      this.cur_city = this.$route.query.cur_city
      this.city = JsonData[0][this.cur_city[0]][this.cur_city[1]]
      console.log(this.city)
      let arr = []
      console.log(Object.entries(JsonData[0]['86']))
      for (let [key, value] of Object.entries(JsonData[0]['86'])) {
        arr.push({
          name: value,
          value: key,
          parent: 0
        })
      }
      this.city_list = JSON.parse(JSON.stringify(arr))
      for (let value of this.city_list) {
        for (let [key1, value1] of Object.entries(JsonData[0][value.value])) {
          arr.push({
            name: value1,
            value: key1,
            parent: value.value
          })
        }
      }
      this.city_list = JSON.parse(JSON.stringify(arr))
      console.log(this.city_list)
    },
    methods: {
      getData () {
        console.log(this.cur_city)
        this.city = JsonData[0][this.cur_city[0]][this.cur_city[1]]
      },
      goHome () {
        console.log(this.cur_city)
        this.$router.push({path: '/', query: {cur_city: this.cur_city}})
      }
    },
    watch: {
      'cur_city': 'getData'
    }
  }
</script>

<style>
  /*初始化样式*/
  .vux-popup-header-right{
    color:#222!important;
  }
  /*定位*/
  .location {
    margin-top: 10px !important;
    height: 48px !important;
    line-height: 48px !important;
    background-color: #fff !important;
  }

  .location span {
    margin-left: 14px !important;
  }

  .location .weui-cell__hd, .location .weui-cell__ft {
    display: none !important;
  }

  .location .weui-cell {
    padding: 0 !important;
  }
  /*页面头部*/
  .header{
    height: 44px;
    line-height: 44px;
    font-size: 18px;
    color: #666;
  }
  /*城市选择*/
  .choose_city{
    height: 48px;
    line-height: 48px;
    background-color:#fff;
    color: #666;
    font-size: 16px;
    padding-left: 14px;
    margin-top: 10px;
  }
  /*选择城市按钮*/
  .city_btn {
    font-size: 18px !important;
    margin-top: 30px !important;
    width: 92.5% !important;
    text-align: center !important;
    color: #fff !important;
    background-color: #222 !important;
  }
</style>
