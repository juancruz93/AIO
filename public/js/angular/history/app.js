(function () {

    var app = angular.module('history', [
        'ngRoute',
        'history.controllers',
        'history.services',
        'mgcrea.ngStrap',
        'ngAnimate',
        'ngMaterial',
        'ui.select'
    ]);

    app.config(['$routeProvider', function ($routeProvider) {
        $routeProvider
            .when('/', {
                templateUrl: fullUrlBase + templateBase + '/list',
                controller: 'HistoryController'
            })
           
            .otherwise({
                redirectTo: '/'
            });

    }]);

})();
