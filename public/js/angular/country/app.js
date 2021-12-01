(function () {

    var app = angular.module('country', [
        'ngRoute',
        'country.controllers',
        'country.services',
        'mgcrea.ngStrap',
        'ngAnimate',
        'ngMaterial',
        'ui.select'
    ]);

    app.config(['$routeProvider', function ($routeProvider) {
        $routeProvider
            .when('/', {
                templateUrl: fullUrlBase + templateBase + '/list',
                controller: 'CountryController'
            })
           .when('/edit/:id', {
                templateUrl: fullUrlBase + templateBase + '/edit',
                controller: 'CountryEditController'
            })
            .otherwise({
                redirectTo: '/'
            });

    }]);

})();
