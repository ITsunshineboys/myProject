angular.module("directives", [])
    .directive("swiper", function () {
        return {
            restrict: "EA",
            link: function (scope, element, attrs) {
                var mySwiper = new Swiper('.swiper-container', {
                    direction: 'horizontal',
                    loop: true,
                    autoplay: 1000,

                    // 如果需要分页器
                    pagination: '.swiper-pagination'
                })
            }
        }
    })
