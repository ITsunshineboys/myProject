import Vue from 'vue'
import Router from 'vue-router'
import Order from '@/views/order/index'
import Address from '@/views/address/index'

Vue.use(Router)

export default new Router({
  routes: [
    {
      path: '/order',
      name: 'Order',
      component: Order
    },
    {
      path: '/address',
      name: 'Address',
      component: Address
    }
  ]
})
