(function () {
  angular.module('automaticcampaigncategory', ['ui.router', 'automaticcampaigncategory.controller', 'automaticcampaigncategory.services'])
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
            url: "/edit/:idautomacampcateg",
            templateUrl: fullUrlBase + templateBase + '/edit',
            controller: 'editController'
          });
      }]);
})();