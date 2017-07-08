angular.module("directives",[])
       .directive("nodataDirective",function () {
           return function (scope,element,attr) {
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
       }).directive('head', ['$rootScope','$compile',
    function($rootScope, $compile){
        return {
            restrict: 'E',
            link: function(scope, elem){
                var html = '<link rel="stylesheet" ng-repeat="(routeCtrl, cssUrl) in routeStyles" ng-href="{{cssUrl}}" />';
                elem.append($compile(html)(scope));
                scope.routeStyles = {};
                $rootScope.$on('$routeChangeStart', function (e, next, current) {
                    if(current && current.$$route && current.$$route.css){
                        if(!angular.isArray(current.$$route.css)){
                            current.$$route.css = [current.$$route.css];
                        }
                        angular.forEach(current.$$route.css, function(sheet){
                            delete scope.routeStyles[sheet];
                        });
                    }
                    if(next && next.$$route && next.$$route.css){
                        if(!angular.isArray(next.$$route.css)){
                            next.$$route.css = [next.$$route.css];
                        }
                        angular.forEach(next.$$route.css, function(sheet){
                            scope.routeStyles[sheet] = sheet;
                        });
                    }
                });
            }
        };
    }
]);