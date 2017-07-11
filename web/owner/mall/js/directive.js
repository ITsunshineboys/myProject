/*
angular.module("directives", [])
    .directive("nodataDirective", function () {
        return function (scope, element, attr) {
            $(".reduce_a").on("click", function () {
                var text = $(".val_input").val();
                //alert(text);
                if (text > 0) {
                    text--;
                    $(".val_input").val(text);
                    //alert(text);
                } else {
                    text = 0;
                }
            });
            $(".add_a").on("click", function () {
                var text = $(".val_input").val();
                if (text < 6) {
                    text++;
                    $(".val_input").val(text);
                } else if (text = 6) {
                    text = 6;
                }
            });

//第二个点击事件加减
            $(".reduce_b").on("click", function () {
                var text = $(".val_input_b").val();
                //alert(text);
                if (text > 0) {
                    text--;
                    $(".val_input_b").val(text);
                    //alert(text);
                } else {
                    text = 0;
                }
            });
            $(".add_b").on("click", function () {
                var text = $(".val_input_b").val();
                if (text < 3) {
                    text++;
                    $(".val_input_b").val(text);
                } else if (text = 3) {
                    text = 3;
                }
            });

//第三个点击事件加减
            $(".reduce_c").on("click", function () {
                var text = $(".val_input_c").val();
                //alert(text);
                if (text > 0) {
                    text--;
                    $(".val_input_c").val(text);
                    //alert(text);
                } else {
                    text = 0;
                }
            });
            $(".add_c").on("click", function () {
                var text = $(".val_input_c").val();
                if (text < 4) {
                    text++;
                    $(".val_input_c").val(text);
                } else if (text = 3) {
                    text = 3;
                }
            });

//第四个点击事件加减
            $(".reduce_d").on("click", function () {
                var text = $(".val_input_d").val();
                //alert(text);
                if (text > 0) {
                    text--;
                    $(".val_input_d").val(text);
                    //alert(text);
                } else {
                    text = 0;
                }
            });
            $(".add_d").on("click", function () {
                var text = $(".val_input_d").val();
                if (text < 2) {
                    text++;
                    $(".val_input_d").val(text);
                } else if (text = 2) {
                    text = 2;
                }
            });

        }
    })
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
  /!*  .directive("houseStyle",function () {
        return function (scope,element,attrs) {
            for(var i = 0;i<house.length;i++){
               house[i].on("click",function () {
                   this.addClass("house").siblings().removeClass("house")
               })
            }
        }
    })*!/*/
