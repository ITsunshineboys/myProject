/**
 * Created by Administrator on 2017/12/19/019.
 */
var ModalHelper = (function (bodyCls) { // eslint-disable-line
  var scrollTop // 在闭包中定义一个用来保存滚动位置的变量
  return {
    afterOpen: function () { // 弹出之后记录保存滚动位置，并且给body添加.modal-open
      scrollTop = document.documentElement.scrollTop || document.body.scrollTop
      document.body.setAttribute('class', bodyCls)
      document.body.style.top = -scrollTop + 'px'
    },
    beforeClose: function () { // 关闭时将.modal-open移除并还原之前保存滚动位置
      document.body.setAttribute('class', '')
      document.documentElement.scrollTop = scrollTop
      document.body.scrollTop = scrollTop
    }
  }
})('modal-open')

export default {
  // 联系商家
  install: function (Vue, options) {
    Vue.prototype.contactStore = function (uid, roleId) {
      /* params
       * 商家对应用户ID 角色ID
       * */
      window.AndroidWebView.ConnetionStore(uid, roleId)
    }
    Vue.prototype.ModalHelper = ModalHelper
  }
}
