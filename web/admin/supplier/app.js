const app = angular.module("app", ["ui.router", "shop_style", "freight_template", "template_details",
    "shopoffline_Module", "systemoffline_Module", "wait_online_Module", "commodity_manage",
    "up_shelves_detail_module", "index_module", "shopmanageModule", "applybrandModule", "authorizedetailModule",
  /*三阶段王杰---开始*/
    "supplier_index", "login","shop_decoration_module","supplier_wallet_module",
    "intelligent_directive","shop_data_module","wallet_detail_module","income_pay_module",
    "set_password_module",
  /*三阶段王杰---结束*/
  /*三阶段芳子---开始*/
    "supplier_accountModule","withdraw_depositModule",
    "edit_cardModule","frozen_moneyModule",
    // "ordermanageModule",
    // "waitpay_detailModule","done_detailModule","cancel_detailModule","expressModule"
  /*三阶段芳子---结束*/
]);

// 传参：通过url的get参数stage来获取，不传则使用默认的开发域名
let baseUrl = (function () {
    let stages = [
        "http://test.cdlhzz.cn:888", // 开发接口域名
        "http://v1.cdlhzz.cn:888" // 展示接口域名
    ];
    let stage = 0;
    try {
        let stageParam = location.search.split("&stage=")[1].split("&")[0];
        if (stages[stageParam])  {
            stage = stageParam;
        }
    } catch (e) {}
    return stages[stage];
})();

//路由拦截
app.config(function ($stateProvider, $httpProvider, $urlRouterProvider) {
    $urlRouterProvider.otherwise("/login");
    $httpProvider.defaults.withCredentials = true;
    $stateProvider
    /*--------------三阶段开始----王杰-----------------*/
        .state("login", {   //登录
            url: "/login",
            templateUrl: "pages/login/login.html"
        })
        .state("supplier_index", {   //首页
            url: "/supplier_index",
            templateUrl: "pages/supplier_index/supplier_index.html"
        })
        .state("shop_data", {   //店铺数据
            url: "/shop_data",
            templateUrl: "pages/shop_data/shop_data.html"
        })
        .state("shop_decoration", {   //店铺装修
            url: "/shop_decoration",
            templateUrl: "pages/shop_decoration/shop_decoration.html"
        })

        .state("supplier_wallet", {   //钱包
            url: "/supplier_wallet",
            templateUrl: "pages/supplier_wallet/supplier_wallet.html"
        })
        .state("wallet_detail", {   //钱包详情
            url: "/wallet_detail",
            templateUrl: "pages/supplier_wallet/wallet_detail.html",
            params:{transaction_no:null,income:null}
        })
        .state("income_pay", {   //收支详情
            url: "/income_pay",
            templateUrl: "pages/supplier_wallet/income_pay.html"
        })
        .state("set_password", {   //交易密码
            url: "/set_password",
            templateUrl: "pages/supplier_wallet/set_password.html",
            params:{code_status:null}
        })
        .state("waitsend_detail", {   //待发货详情
            url: "/waitsend_detail",
            templateUrl: "pages/order_manage/waitsend_detail.html",
            params:{item:null,sku:null,wait_receive:null}
        })
        .state("record_goods_detail", {   //记录商品详情
            url: "/record_goods_detail",
            templateUrl: "pages/order_manage/record_goods_detail.html",
            params:{item:null,wait_receive:null}
        })
        /*--------------三阶段结束----王杰-----------------*/


        /*--------------三阶段开始----芳子-----------------*/
        .state("supplier_account", {   //商家账户
            url: "/supplier_account",
            templateUrl: "pages/supplier_wallet/supplier_account.html"
        })
        .state("withdraw_deposit", {   //提现
            url: "/withdraw_deposit",
            templateUrl: "pages/supplier_wallet/withdraw_deposit.html"
        })
        .state("edit_card", {         //添加/修改银行卡
            url: "/edit_card",
            templateUrl: "pages/supplier_wallet/edit_card.html"
        })
        .state("frozen_money", {      //冻结银行卡
            url: "/frozen_money",
            templateUrl: "pages/supplier_wallet/frozen_money.html"
        })
        .state("order_manage", {      //订单管理
            params:{tabflag:null},
            url: "/order_manage",
            templateUrl: "pages/order_manage/order_manage_index.html"
        })
        .state("waitpay_detail", {   //待付款订单详情
            params:{order_no:null,sku:null,tabflag:null},
            url: "/waitpay_detail",
            templateUrl: "pages/order_manage/waitpay_detail.html"
        })
        .state("done_detail", {     //已完成订单详情
            params:{order_no:null,sku:null,tabflag:null},
            url: "/done_detail",
            templateUrl: "pages/order_manage/done_detail.html"
        })
        .state("cancel_detail", {   //已取消订单详情
            params:{order_no:null,sku:null,tabflag:null},
            url: "/cancel_detail",
            templateUrl: "pages/order_manage/cancel_detail.html"
        })
        .state("express", {        //物流详情
            params:{express_params:null},
            url: "/express",
            templateUrl: "pages/order_manage/express.html"
        })
        /*--------------三阶段结束----芳子-----------------*/


        .state("shop_manage", {   //店铺管理
            url: "/shop_manage",
            templateUrl: "pages/shop_manage/shop_manage_index.html",
            params:{authorize_flag:null}
        })
        .state("apply_brand", {   //申请新品牌
            url: "/apply_brand",
            templateUrl: "pages/shop_manage/apply_brand.html"
        })
        .state("authorize_detail", {   //品牌授权详情
            url: "/authorize_detail",
            templateUrl: "pages/shop_manage/authorize_detail.html"
        })
        .state("commodity_manage", {   //商品管理
            url: "/commodity_manage",
            templateUrl: "pages/commodity_manage/commodity_manage.html",
            params: {id: 'id', name: 'name', on_flag: '', down_flag: ''}
        })
        .state("brand_manage", {   //品牌管理
            url: "/brand_manage",
            templateUrl: "pages/brand_manage/brand_manage.html"
        })
        .state("class_manage", {   //分类管理
            url: "/class_manage",
            templateUrl: "pages/class_manage/class_manage.html"
        })

        .state("shop_style", {   //商品管理风格系类跳转
            url: "/shop_style",
            templateUrl: "pages/commodity_manage/shop_style.html",
            params: {
                category_id: '',
                first_category_title: '',
                second_category_title: '',
                third_category_title: ''
            }
        })
        .state("freight_template", {   //商品管理添加物流模板
            url: "/freight_template",
            templateUrl: "pages/commodity_manage/freight_template.html"
            //controller: "shop_style_ctrl"
        })
        .state("template_details", {   //商品管理物流模板详情
            url: "/template_details",
            templateUrl: "pages/commodity_manage/template_details.html",
            params: {id: 'id', name: 'name'}
        })

        .state("up_shelves_detail", {   //商品管理==>已上架商品详情
            url: "/up_shelves_detail",
            templateUrl: "pages/commodity_manage/up_shelves_detail.html",
            params: {item: '', flag: ''}
        })
        .state("shop_offline", {
          /*已下架-商家下架*/
            url: "/shop_offline",
            templateUrl: "pages/commodity_manage/shop_offline.html"
        })
        .state("system_offline", {
          /*已下架-系统下架*/
            url: "/system_offline",
            templateUrl: "pages/commodity_manage/system_offline.html",
            params: {item: ''}
        })
        .state("wait_online", {
          /*等待上架*/
            url: "/wait_online",
            templateUrl: "pages/commodity_manage/wait_online.html",
            params: {item: '', flag: ''}
        })
})
    .directive('wdatePicker',function(){
        return{
            restrict:"A",
            link:function(scope,element,attr){
                element.bind('click',function(){
                    window.WdatePicker({
                        onpicked: function(){element.change()},
                        oncleared:function(){element.change()}
                    })
                });
            }
        }
    })
/**
 * 分页
 * config = {
 *     prevBtn: string,      上一页(默认显示上一页)
 *     nextBtn: string,      下一页(默认显示下一页)
 *     showTotal: boolean,   是否显示总条数
 *     showJump: boolean,    是否显示跳转
 *     itemsPerPage: number, 每页个数
 *     totalItems: number,   数据总条数
 *     currentPage: number,  当前所在页数
 *     onChange: function    页面改变发生事件
 * }
 */
.directive('tmPagination', function () {
    return {
        restrict: 'EA',
        template: '<div class="page-list clearfix">' +
        '<span class="pagination-total" ng-class="{true: \'\', false: \'no-data\'}[conf.totalItems != 0]" ng-show="conf.showTotal">总共有 {{conf.totalItems}} 条数据</span>' +
        '<ul class="pagination" ng-show="conf.totalItems > 0">' +
        '<li ng-class="{disabled: conf.currentPage == 1}" ng-click="prevPage()"><span>{{conf.prevBtn || "上一页"}}</span></li>' +
        '<li ng-repeat="item in pageList track by $index" ng-class="{active: item == conf.currentPage, separate: item == \'...\'}" ' +
        'ng-click="changeCurrentPage(item)">' +
        '<span>{{ item }}</span>' +
        '</li>' +
        '<li ng-class="{disabled: conf.currentPage == conf.numberOfPages}" ng-click="nextPage()"><span>{{conf.nextBtn || "下一页"}}</span></li>' +
        '</ul>' +
        '<div class="jump" ng-show="conf.showJump && conf.totalItems > 0"><input id="pageJump" class="form-control" type="text"><button class="btn btn-default" ng-click="jumpPage()">跳转</button></div>' +
        '<div class="no-items" ng-show="conf.totalItems <= 0">暂无数据</div>' +
        '</div>',
        replace: true,
        scope: {
            conf: '='
        },
        link: function (scope, element, attrs) {

            let conf = scope.conf;

            // 默认分页长度
            let defaultPagesLength = 9;

            // 默认分页选项可调整每页显示的条数
            let defaultPerPageOptions = [10, 15, 20, 30, 50];
            conf.perPageOptions = [];
            // 默认每页的个数
            let defaultPerPage = 15;

            // 获取分页长度
            if (conf.pagesLength) {
                // 判断一下分页长度
                conf.pagesLength = parseInt(conf.pagesLength, 10);

                if (!conf.pagesLength) {
                    conf.pagesLength = defaultPagesLength;
                }

                // 分页长度必须为奇数，如果传偶数时，自动处理
                if (conf.pagesLength % 2 === 0) {
                    conf.pagesLength += 1;
                }

            } else {
                conf.pagesLength = defaultPagesLength
            }

            // 分页选项可调整每页显示的条数
            if (!conf.perPageOptions) {
                conf.perPageOptions = defaultPagesLength;
            }

            // pageList数组
            function getPagination(newValue, oldValue) {

                // conf.currentPage
                if (conf.currentPage) {
                    conf.currentPage = parseInt(scope.conf.currentPage, 10);
                }

                if (!conf.currentPage) {
                    conf.currentPage = 1;
                }

                // conf.totalItems
                if (conf.totalItems) {
                    conf.totalItems = parseInt(conf.totalItems, 10);
                }

                // conf.totalItems
                if (!conf.totalItems) {
                    conf.totalItems = 0;
                    return;
                }

                // conf.itemsPerPage
                if (conf.itemsPerPage) {
                    conf.itemsPerPage = parseInt(conf.itemsPerPage, 10);
                }
                if (!conf.itemsPerPage) {
                    conf.itemsPerPage = defaultPerPage;
                }

                // numberOfPages
                conf.numberOfPages = Math.ceil(conf.totalItems / conf.itemsPerPage);

                // 如果分页总数>0，并且当前页大于分页总数
                if (scope.conf.numberOfPages > 0 && scope.conf.currentPage > scope.conf.numberOfPages) {
                    scope.conf.currentPage = scope.conf.numberOfPages;
                }

                // 如果itemsPerPage在不在perPageOptions数组中，就把itemsPerPage加入这个数组中
                let perPageOptionsLength = scope.conf.perPageOptions.length;

                // 定义状态
                let perPageOptionsStatus;
                for (var i = 0; i < perPageOptionsLength; i++) {
                    if (conf.perPageOptions[i] == conf.itemsPerPage) {
                        perPageOptionsStatus = true;
                    }
                }
                // 如果itemsPerPage在不在perPageOptions数组中，就把itemsPerPage加入这个数组中
                if (!perPageOptionsStatus) {
                    conf.perPageOptions.push(conf.itemsPerPage);
                }

                // 对选项进行sort
                conf.perPageOptions.sort(function (a, b) {
                    return a - b
                });


                // 页码相关
                scope.pageList = [];
                if (conf.numberOfPages <= conf.pagesLength) {
                    // 判断总页数如果小于等于分页的长度，若小于则直接显示
                    for (i = 1; i <= conf.numberOfPages; i++) {
                        scope.pageList.push(i);
                    }
                } else {
                    // 总页数大于分页长度（此时分为三种情况：1.左边没有...2.右边没有...3.左右都有...）
                    // 计算中心偏移量
                    let offset = (conf.pagesLength - 1) / 2;
                    if (conf.currentPage <= offset) {
                        // 左边没有...
                        for (i = 1; i <= offset + 1; i++) {
                            scope.pageList.push(i);
                        }
                        scope.pageList.push('...');
                        scope.pageList.push(conf.numberOfPages);
                    } else if (conf.currentPage > conf.numberOfPages - offset) {
                        scope.pageList.push(1);
                        scope.pageList.push('...');
                        for (i = offset + 1; i >= 1; i--) {
                            scope.pageList.push(conf.numberOfPages - i);
                        }
                        scope.pageList.push(conf.numberOfPages);
                    } else {
                        // 最后一种情况，两边都有...
                        scope.pageList.push(1);
                        scope.pageList.push('...');

                        for (i = Math.ceil(offset / 2); i >= 1; i--) {
                            scope.pageList.push(conf.currentPage - i);
                        }
                        scope.pageList.push(conf.currentPage);
                        for (i = 1; i <= offset / 2; i++) {
                            scope.pageList.push(conf.currentPage + i);
                        }

                        scope.pageList.push('...');
                        scope.pageList.push(conf.numberOfPages);
                    }
                }

                scope.$parent.conf = conf;
            }

            // prevPage
            scope.prevPage = function () {
                if (conf.currentPage == 1) {
                    return false;
                }
                if (conf.currentPage > 1) {
                    conf.currentPage -= 1;
                }
                getPagination();
                if (conf.onChange) {
                    conf.onChange();
                }
            };

            // nextPage
            scope.nextPage = function () {
                if (conf.currentPage == conf.numberOfPages) {
                    return false;
                }
                if (conf.currentPage < conf.numberOfPages) {
                    conf.currentPage += 1;
                }
                getPagination();
                if (conf.onChange) {
                    conf.onChange();
                }
            };

            // 变更当前页
            scope.changeCurrentPage = function (item) {

                if (item == '...' || item == conf.currentPage) {
                    return;
                } else {
                    conf.currentPage = item;
                    getPagination();
                    // conf.onChange()函数
                    if (conf.onChange) {
                        conf.onChange();
                    }
                }
            };

            // 跳转到页面
            scope.jumpPage = function () {
                let jumpNum = angular.element('#pageJump').val();
                scope.changeCurrentPage(jumpNum);
                angular.element('#pageJump').val('')
            };

            scope.$watch('conf.totalItems', function (value, oldValue) {
                // 在无值或值不相等的时候，去执行onChange事件
                if (value == undefined && oldValue == undefined) {

                    if (conf.onChange) {
                        conf.onChange();
                    }
                }
                getPagination();
            });
        }
    };
})