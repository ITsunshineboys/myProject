app.directive('contenteditable',function () {
    return {
        restrict:'A',
        require:'?^ngModel',
        link:function (scope,ele,attrs,ctrl) {
            if(!ctrl){
                return
            }
            ctrl.$render = function () {
                ele.val(ctrl.$viewValue || attrs.defaultText)
            }
            ele.bind('focus',function(){
                if(ele.val()==attrs.defaultText){
                    ele.val('')
                }
            })
            ele.bind('focus blur keyup change',function(){
                console.log(ctrl);
                ctrl.$setViewValue(ele.val());
            })
        }
    }
})