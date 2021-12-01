(function () {
  angular.module('automaticcampaign', ['ui.router', 'automaticcampaign.controllers', 'automaticcampaign.services', 'ngMaterial', 'flowChart', 'ui.bootstrap', 'mgcrea.ngStrap', 'ui.select', 'ngSanitize', 'angularMoment', 'moment-picker'])
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
            url: "/edit/:idautomaticcampaign",
            templateUrl: fullUrlBase + templateBase + '/edit',
            controller: 'editController'
          })
          .state('viewscheme', {
            url: "/viewscheme/:idautomaticcampaign",
            templateUrl: fullUrlBase + templateBase + "/viewscheme",
            controller: "viewschemeController"
          });

        $urlRouterProvider.otherwise(function ($injector, $location) {
          var $state = $injector.get('$state');
          $state.go('index');
        });
      }]);
})();
