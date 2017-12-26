import Vue from 'vue'
import Router from 'vue-router'
import Home from '@/views/home/index'
import Search from '@/views/search/index'
import City from '@/views/city/index'
import Order from '@/views/order/index'
import Address from '@/views/address/index'
import GoodDetail from '@/views/good_detail/index'
import ClassList from '@/views/class/index'

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
      path: '/choose_city',
      name: 'city',
      component: City
    },
    {
      path: '/good-detail',
      name: 'GoodDetail',
      component: GoodDetail
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
      path: '/class',
      name: 'ClassList',
      component: ClassList
    }
  ]
})
