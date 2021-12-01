(function () {

    var app = angular.module('scheduled', [
        'ngRoute',
        'scheduled.controllers',
        'scheduled.services',
        'mgcrea.ngStrap',
        'ngAnimate',
        'ngMaterial'
    ]);

    app.config(['$routeProvider', function ($routeProvider) {
        $routeProvider
            .when('/', {
                templateUrl: fullUrlBase + templateBase + '/list',
                controller: 'ScheduledController'
            })
           
            .otherwise({
                redirectTo: '/'
            });

    }]);

})();
