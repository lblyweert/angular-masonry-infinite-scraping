app.directive("emitNgRepeatFinished", function(){
    return {
        restrict: "A",
        link: function(scope, element){
            if (scope.$last){
                scope.$emit('ngRepeatFinished');
            }
        }
    }
});