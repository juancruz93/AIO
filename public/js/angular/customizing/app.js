(function () {

  var app = angular.module('customizing', [
    'ui.router',
    'customizing.controllers',
    'customizing.services',
    'mgcrea.ngStrap',
    'ngAnimate',
    'colorpicker.module',
    'ngMaterial'
  ]);

  app.config(['$stateProvider', '$urlRouterProvider', function ($stateProvider, $urlRouterProvider) {
      $stateProvider
              .state('index', {
                url: "/",
                templateUrl: fullUrlBase + templateBase + '/list',
                controller: 'customizingController'
              })
              .state('add', {
                url: "/add",
                templateUrl: fullUrlBase + templateBase + '/add',
                controller: 'customizingAddController'
              })
              .state('edit', {
                url: "/edit/:id",
                templateUrl: fullUrlBase + templateBase + '/edit',
                controller: 'customizingEditController'
              });
             

      $urlRouterProvider.otherwise(function ($injector, $location) {
        var $state = $injector.get('$state');
        $state.go('index');
      });


    }]);

})();
