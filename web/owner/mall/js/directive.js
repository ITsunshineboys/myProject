angular.module("directives", [])
    .directive("swiper", function () {
        return {
            restrict: "EA",
            scope:false,
            link: function (scope, element, attrs) {
                scope.$watch(scope.cur_style,function (newVal,oldVal) {
                    if(newVal!=''||newVal!=undefined){
                        var mySwiper = new Swiper('.swiper-container', {
                            direction: 'horizontal',
                            loop: true,
                            autoplay: 1000,
                            observe:true,
                            observeParents:true,

                            // 如果需要分页器
                            pagination: '.swiper-pagination'
                        })
                    }
                })
            }
        }
    })
    // .directive('loading',function () {
    //    return function () {
    //        $('#myButton').on('click', function () {
    //            var $btn = $(this).button('loading')
    //            // business logic...
    //            $btn.button('reset')
    //        })
    //    }
    // })
