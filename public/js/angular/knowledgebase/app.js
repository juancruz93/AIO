(function () {

    var app = angular.module('knowledgebase', [
        'ngRoute',
        'knowledgebase.controllers',
        'knowledgebase.services',
        'mgcrea.ngStrap',
        'ngAnimate',
        'ngMaterial',
        'angularFileUpload'
    ]);

    app.config(['$routeProvider', function ($routeProvider) {
        $routeProvider
            .when('/', {
                templateUrl: fullUrlBase + templateBase + '/list',
                controller: 'KnowledgebaseController'
            })
            .when('/import', {
                templateUrl: fullUrlBase + templateBase + '/import',
                controller: 'KnowledgebaseImportController'
            })
            .when('/validate', {
                templateUrl: fullUrlBase + templateBase + '/validate',
                controller: 'KnowledgebaseValidateController'
            })
           
            .otherwise({
                redirectTo: '/'
            });

    }]);

})();
