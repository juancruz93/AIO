(function () {
    angular.module('accountcategory', ['ui.router', 'accountcategory.controllers', 'accountcategory.services', 'ngMaterial'])
        .config(['$stateProvider', '$urlRouterProvider', function ($stateProvider, $urlRouterProvider) {
            $stateProvider
                .state('index', {
                    url: "/",
                    templateUrl: fullUrlBase + templateBase + '/list',
                    controller: 'listController'
                })
                .state('create', {
                    url: "/create",
                    templateUrl: fullUrlBase + templateBase + '/create',
                    controller: 'createController'
                })
                .state('edit', {
                    url: "/edit/:id",
                    templateUrl: fullUrlBase + templateBase + '/edit',
                    controller: 'editController'
                })

            $urlRouterProvider.otherwise('/')
        }]);
})();