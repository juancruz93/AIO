(function () {
  angular.module('smstemplate', ['ui.router', 'smstemplate.controller', 'smstemplate.services', "ngMaterial", "ui.select", "ngSanitize"])
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
            url: "/edit/:idsmstemplate",
            templateUrl: fullUrlBase + templateBase + '/edit',
            controller: 'editController'
          });
      }]);
})();
