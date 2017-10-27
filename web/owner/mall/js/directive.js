angular.module("directives", [])
    .directive("swiper", function () {
        return {
            restrict: "EA",
            link: function (scope, element, attrs) {
                scope.$watch(scope.cur_style,function (newVal,oldVal) {
                    console.log(newVal)
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
    .directive('water',function ($timeout) {
        return {
            restrict: "EA",
            // scope:false,
            link:function (scope,element,attrs) {
                console.log(element)
                console.log(element.find('div'))
                if(scope.$last === true){
                    $timeout(function () {
                        scope.$emit('ngRepeatFinished')
                    },300)
                }
                //     let all_children = element.find('div')
                // console.log(all_children)
                //     let cur_height = [0,0]
                // for(let i = 0;i<all_children.length;i++){
                //     let min = parseFloat(cur_height[0])>parseFloat(cur_height[1])?cur_height[1]:cur_height[0]
                //     let minIndex = cur_height[0]>cur_height[1]?1:0
                //     all_children.eq(i).css({
                //         'top':min,
                //         'left':minIndex*($(window).width()*0.471),
                //         'display':'inline'
                //     })
                //     cur_height[minIndex] += all_children.eq(i).outerHeight() + 20
                // }
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
