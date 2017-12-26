import Vue from 'vue'
import Router from 'vue-router'
import Order from '@/views/order/index'
import Address from '@/views/address/index'
import GoodDetail from '@/views/good_detail/index'
import ClassList from '@/views/class/index'

Vue.use(Router)

export default new Router({
  routes: [
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
