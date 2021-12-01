(function () {
    angular.module('process', ['process.controllers', 'process.services', 'ngMaterial'])
        /*.config(['$routeProvider', function ($routeProvider) {
            $routeProvider
                .when('/', {
                    templateUrl: '/' + relativeUrlBase + '/' + templateBase + '/list',
                    controller: 'ContactController'
                })
                .when('/import', {
                    templateUrl: '/' + relativeUrlBase + '/' + templateBase + '/import/' + idContactlist,
                    controller: 'ContactImportController'
                })
                .when('/newbatch', {
                    templateUrl: '/' + relativeUrlBase + '/' + templateBase + '/newbatch',
                    controller: 'NewbatchController'
                })
                .when('/newcontact', {
                    templateUrl: '/' + relativeUrlBase + '/' + templateBase + '/newcontact/' + idContactlist,
                    controller: 'NewcontactController'
                })
                /!*.when('/import/contacts', {
                 templateUrl: '/' + relativeUrlBase + '/' + templateBase + '/importcontacts',
                 controller: 'ContactImportController'
                 })*!/
                .otherwise({
                    redirectTo: '/'
                });
        }])*/
})();
