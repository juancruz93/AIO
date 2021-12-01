(function () {

    var app = angular.module('language', [
        'ngRoute',
        'language.controllers',
        'language.services',
        'mgcrea.ngStrap',
        'ngAnimate',
        'ngMaterial'
    ]);

    app.config(['$routeProvider', function ($routeProvider) {
        $routeProvider
            .when('/', {
                templateUrl: fullUrlBase + templateBase + '/list',
                controller: 'LanguageController'
            })
            .when('/edit/:id', {
                templateUrl: fullUrlBase + templateBase + '/edit',
                controller: 'LanguageEditController'
            })
           
            .otherwise({
                redirectTo: '/'
            });

    }]);

})();
