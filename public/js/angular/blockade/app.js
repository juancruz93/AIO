'use strict';
(function () {
  angular.module('blockade', ['ngRoute', 'blockade.controllers', 'blockade.directives', 'blockade.services',  "ngMaterial"])
          .config(['$routeProvider', function ($routeProvider) {
              $routeProvider
                      .when('/', {
                        templateUrl: fullUrlBase + templateBase + '/list',
                        controller: 'BlockadeController'
                      })
                      .when('/new', {
                        templateUrl: fullUrlBase + templateBase + '/new',
                        controller: 'NewBlockadeController'
                      })
                      .otherwise({
                        redirectTo: '/'
                      });
            }])
})();
