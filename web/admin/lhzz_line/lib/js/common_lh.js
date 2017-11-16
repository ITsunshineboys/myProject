//const baseUrl = 'http://test.cdlhzz.cn';
// const baseUrl = 'http://v1.cdlhzz.cn:888';
let baseUrl = (function () {
    return 'http://test.cdlhzz.cn';
    // return '';
})();
/**
 * ajax请求
 * @param url          请求接口
 * @param params       请求参数
 * @param callback     回调函数
 */
app.service('_ajax', function ($http, $state) {
    this.get = function (url, params, callback) {
        $http({
            method: 'GET',
            url: baseUrl + url,
            params: params
        }).then(function (response) {
            let res = response.data;
            if (res.code === 403) {
                window.location.href = "login.html";
            } else if (res.code === 200 || res.code == 1007) {
                if (typeof callback === 'function') {
                    callback(res)
                }
            } else {
                alert(res.msg)
            }
        }, function (response) {
            console.log(response.statusText);
            alert(response.statusText)
        })
    };
    this.post = function (url, params, callback) {
        $http({
            method: 'post',
            url: baseUrl + url,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            data: params,
            transformRequest: function (data) {
                return $.param(data);
            }
        }).then(function (response) {
            let res = response.data;
            if (res.code === 403) {
                window.location.href = "login.html";
            } else if (res.code === 200 || res.code == 1007) {
                if (typeof callback === 'function') {
                    callback(res)
                }
            } else {
                alert(res.msg)
            }
        }, function (response) {
            console.log(response);
            alert(response.statusText)
        })
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
 *     onChange: function    分页发生改变的执行函数
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
    /**
     * 星级评分
     * ratingValue: number  选中几颗星
     * max: number          总共几颗心
     * readonly: boolean    是否只读
     */
    .directive('star', function () {
        return {
            template: '<ul class="rating">' +
            '<li ng-repeat="star in stars" ng-class="star" ng-click="clickStar($index + 1)">' +
            '<i class="glyphicon glyphicon-star"></i>' +
            '</li>' +
            '</ul>',
            scope: {
                ratingValue: '=',
                max: '=',
                readonly: '@'
            },
            link: function (scope, elem, attrs) {
                // scope.ratingValue = scope.max;
                elem.css("text-align", "center");
                let updateStars = function () {
                    scope.stars = [];
                    for (let i = 0; i < scope.max; i++) {
                        scope.stars.push({
                            filled: i < scope.ratingValue
                        });
                    }
                };
                updateStars();

                scope.clickStar = function (num) {
                    if (scope.readonly == 'true') {
                        return false
                    }
                    scope.ratingValue = num;
                };

                scope.$watch('ratingValue', function (newVal) {
                    if (newVal) {
                        updateStars();
                    }
                });
            }
        };
    })
    .directive('ngcLayDate', function ($timeout) {
        return {
            require: '?ngModel',
            restrict: 'A',
            scope: {
                ngModel: '=',
                maxDate: '@',
                minDate: '@'
            },
            link: function (scope, element, attr, ngModel) {
                let _date = null, _config = {};
                // 渲染模板完成后执行
                $timeout(function () {
                    // 初始化参数
                    _config = {
                        elem: '#' + attr.id,
                        format: attr.format != undefined && attr.format != '' ? attr.format : 'YYYY-MM-DD',
                        max: attr.hasOwnProperty('maxDate') ? attr.maxDate : '',
                        min: attr.hasOwnProperty('minDate') ? attr.minDate : '',
                        choose: function (data) {
                            scope.$apply(setViewValue);
                        },
                        clear: function () {
                            ngModel.$setViewValue(null);
                        }
                    };
                    // 初始化
                    _date = laydate(_config);

                    // 监听日期最大值
                    if (attr.hasOwnProperty('maxDate')) {
                        attr.$observe('maxDate', function (val) {
                            _config.max = val;
                        })
                    }
                    // 监听日期最小值
                    if (attr.hasOwnProperty('minDate')) {
                        attr.$observe('minDate', function (val) {
                            _config.min = val;
                        })
                    }

                    // 模型值同步到视图上
                    ngModel.$render = function () {
                        element.val(ngModel.$viewValue || '');
                    };

                    // 监听元素上的事件
                    element.on('blur keyup change', function () {
                        scope.$apply(setViewValue);
                    });

                    setViewValue();

                    // 更新模型上的视图值
                    function setViewValue() {
                        let val = element.val();
                        ngModel.$setViewValue(val);
                    }
                }, 0);
            }
        };
    })
    /**
     * 面包屑
     * crumbConf    为一个数组例子如下
     * [{
     *  name: '',       各级面包屑名称
     *  icon: '',       图标，一级才写
     *  link: '',       各级面包屑跳转地址，同 ui-sref 的地址，最后一级不写；；若 link: -1 则为history.back() 跳转
     *  params: {}      跳转地址所带参数，有参数才写
     * }]
     */
    .directive('breadcrumb', function ($state) {
        return {
            restrict: 'E',
            replace: true,
            template: '<ol class="breadcrumb">' +
            '<li ng-repeat="obj in crumbConf">' +
            '<i class="iconfont" ng-if="$index == 0" ng-class="obj.icon"></i>' +
            '<a ng-if="!$last" href="javascript:void (0);" ng-bind="obj.name" ng-click="goToPage(obj.link, obj.params)"></a>' +
            '<span ng-if="$last" ng-bind="obj.name"></span>' +
            '</li></ol>',
            scope: {
                crumbConf: '='
            },
            link: function (scope) {
                scope.goToPage = function (url, params) {
                    if (url === -1) {
                        window.history.back()
                    } else {
                        if (params === undefined) {
                            $state.go(url)
                        } else {
                            $state.go(url, params)
                        }
                    }
                }
            }
        }
    })
    .filter("toHtml", ["$sce", function ($sce) {
        return function (text) {
            return $sce.trustAsHtml(text);
        }
    }]);
/**
 * 确认模态框
 * @param info  提示信息
 * @param fun   确认后的执行函数
 * @private
 */
function _confirm(info, fun) {
    let node = `
    <div id="confirm" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">提示</h4>
                </div>
                <div class="modal-body">
                    <p>${info}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                    <button id="confirmEnter" type="button" class="btn btn-primary">确认</button>
                </div>
            </div>
        </div>
    </div>`;
    $('body').append(node);
    let $confirm = $('#confirm');
    $confirm.one('show.bs.modal', function () {
        $('#confirmEnter').one('click', function () {
            if (typeof fun === 'function') {
                fun();
            }
            $confirm.modal('hide');
        });
    });
    $confirm.one('hidden.bs.modal', function () {
        $confirm.remove()
    });
    $confirm.modal('show');
}

/**
 * 警告模态框
 * @param title 警告标题
 * @param info  警告信息
 * @param fun   执行函数
 * @private
 */
function _alert(title, info, fun) {
    let node = `
    <div id="alert" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">${title}</h4>
                </div>
                <div class="modal-body">
                    <p>${info}</p>
                </div>
                <div class="modal-footer">
                    <button id="alertEnter" type="button" class="btn btn-primary" data-dismiss="modal">确定</button>
                </div>
            </div>
        </div>
    </div>`;
    $('body').append(node);
    let _alert = $('#alert');
    _alert.one('hide.bs.modal', function () {
        if (typeof fun === 'function') {
            fun();
        }
    });
    _alert.one('hidden.bs.modal', function () {
        _alert.remove()
    });
    _alert.modal('show');
}