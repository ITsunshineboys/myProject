import Vue from 'vue'
import Router from 'vue-router'
import Home from '@/views/home/index'               // 首页
import Search from '@/views/search/index'           // 搜索
import City from '@/views/city/index'               // 城市选择
import Order from '@/views/order/index'             // 订单主页
import Address from '@/views/address/index'         // 收货地址
import Invoice from '@/views/invoice/index'         // 发票
import PaySuccess from '@/views/pay_success/index'  // 支付成功
import GoodDetail from '@/views/good_detail/index'  // 商品详情
import AllComment from '@/views/comment/index'      // 全部评价
import ClassList from '@/views/class/index'         // 分类列表
// import GoodsList from '@/views/goods_list/index'    // 商品列表

Vue.use(Router)

export default new Router({
  routes: [
    {
      path: '/',
      name: 'home',
      component: Home
    }, {
      path: '/search',
      name: 'grabble',
      component: Search
    }, {
      path: '/choose-city',
      name: 'city',
      component: City
    },
    {
      path: '/good-detail',
      name: 'GoodDetail',
      component: GoodDetail
    },
    {
      path: '/all-comment',
      name: 'AllComment',
      component: AllComment
    },
    {
      path: '/order',
      name: 'Order',
      component: Order
    },
    {
      path: '/address',
      name: 'Address',
      component: Address
    },
    {
      path: '/invoice',
      name: 'Invoice',
      component: Invoice
    },
    {
      path: '/class',
      name: 'ClassList',
      component: ClassList
    },
    {
      path: '/success',
      name: 'PaySuccess',
      component: PaySuccess
    }
    // {
    //   path: '/goods-list',
    //   name: 'GoodsList',
    //   component: GoodsList
    // }
  ]
})
