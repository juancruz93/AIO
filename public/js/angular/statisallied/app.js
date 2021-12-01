(function () {
    var app = angular.module('statisallied', [
        'ngRoute',
        'statisallied.controllers',
        'statisallied.services',
        'ngAnimate',
        'ngMaterial',
        'ngSanitize'
    ]);

    app.config(['$routeProvider', function ($routeProvider) {
            $routeProvider
                    .when('/', {
                        templateUrl: fullUrlBase + templateBase + '/list',
                        controller: 'statisalliedController'
                    })
                    .when('/create', {
                        templateUrl: fullUrlBase + templateBase + '/create',
                        controller: 'statisalliedCreateController'
                    })
                    .when('/edit/:id', {
                        templateUrl: fullUrlBase + templateBase + '/edit',
                        controller: 'statisalliedEditController'
                    })
                    .when('/view/:id', {
                        templateUrl: fullUrlBase + templateBase + '/view',
                        controller: 'statisalliedViewController'
                    });
            /* .otherwise({
             redirectTo: '/'
             });*/

        }]);


})();
