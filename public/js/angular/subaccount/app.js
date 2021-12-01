angular.module('aio', ['ngRoute'])
    .config(['$routeProvider', function ($routeProvider) {
        $routeProvider
            .when('/', {
                templateUrl: fullUrlBase + templateBase + '/list',
                controller: 'ContactlistController'
            })
            .when('/add', {
                templateUrl: fullUrlBase + templateBase + '/add',
                controller: 'ContactlistAddController'
            })
            .when('/edit/:id', {
                templateUrl: fullUrlBase + templateBase + '/edit',
                controller: 'ContactlistEditController'
            })
            .when('/delete/:id', {
                templateUrl: fullUrlBase + templateBase + '/delete',
                controller: 'ContactlistDeleteController'
            })
            .otherwise({
                redirectTo: '/'
            });

    }]);
