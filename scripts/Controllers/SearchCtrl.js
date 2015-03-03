/**
 * Created by Lorenzo Blyweert on 2015-02-26.
 */

app.controller('SearchCtrl', function ($scope, RecipeFactory) {

    init();

    function init() {
        $scope.items = [
             //{'src':'http://lorempixel.com/350/200/sports/1', 'title':'Lorem Ipsum and shit', 'author':'lblyweert'},
             //{'src':'http://lorempixel.com/350/200/sports/2', 'title':'Lorem Ipsum and shit', 'author':'lblyweert'}
        ];

        $scope.shouldLoad = false;
        $scope.busy = false;
        $scope.page = 1;
        $scope.getRecipes = getRecipes;
        $scope.startLoading = startLoading;
    }

    function startLoading() {
        $scope.shouldLoad = true;
        getRecipes(true);
    }

    function getRecipes(firstCall) {
        if ($scope.busy || !$scope.shouldLoad) return;
        $scope.busy = true;
        var promise = RecipeFactory.nextPage($scope.page, $scope.query);
        promise.then(
            function(payload) {

                console.log(payload.data);

                $scope.currentItems = payload.data;
                if (firstCall) {
                    $scope.items = $scope.currentItems;
                }
                else {
                    $scope.items = this.items.concat(this.currentItems);
                }
                $scope.page++;
                $scope.busy = false;

            }.bind(this),

            function (errorPayload){
                console.log("Error JSON");
                $scope.busy = false;
            });
    };

});