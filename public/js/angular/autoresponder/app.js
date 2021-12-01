(function () {
    angular.module('autoresponder', ['ui.router', 'autoresponder.controllers', 'autoresponder.services', 'ui.select', 'ngSanitize', 'ngMaterial', 'moment-picker'])
        .config(['$stateProvider', '$urlRouterProvider', function ($stateProvider, $urlRouterProvider) {
              $stateProvider
                      .state('index', { 
                        url: "/",
                        templateUrl: fullUrlBase + templateBase + '/list',
                        controller: 'listController'
                      })  
                      .state('tools', {
                        url: "/tools",
                        templateUrl: fullUrlBase + templateBase + '/tools',
                        controller: 'toolsController' 
                      })
                      .state('birthday', {
                        url: "/birthday/:id",
                        templateUrl: fullUrlBase + templateBase + '/birthday',
                        controller: 'birthdayController'
                      })
                      .state('birthdaysms', {
                        url: "/birthdaysms/:id",
                        templateUrl: fullUrlBase + templateBase + '/birthdaysms',
                        controller: 'birthdaySmsController'
                      })
                      .state('edit', {
                        url: "/edit/:id",
                        templateUrl: fullUrlBase + templateBase + '/edit',
                        controller: 'editController'
                      });

              $urlRouterProvider.otherwise('/')
            }]);
})();