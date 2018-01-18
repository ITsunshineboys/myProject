/**
 * Created by Administrator on 2017/12/19/019.
 */
export default {
  // 联系商家
  install (Vue, options) {
    Vue.prototype.contactStore = function (uid, roleId) {
      /* params
       * 商家对应用户ID 角色ID
       * */
      window.AndroidWebView.ConnetionStore(uid, roleId)
    }
  }
}
