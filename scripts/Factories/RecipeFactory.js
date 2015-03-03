/**
 * Created by Lorenzo Blyweert on 2015-02-26.
 */

app.factory('RecipeFactory', function($http) {
    return {
        nextPage: function(page, query) {
            var url ="scripts/parser.php?page=" + page + "&recherche=" + query;
            return $http.get(url);
        }
    }
});