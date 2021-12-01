(function () {
  angular.module('mailtemplate', ['ui.router', 'mailtemplate.controller', 'mailtemplate.services', "ngMaterial", "ui.select", "ngSanitize"])
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
//          .state('edit', {
//            url: "/edit/:idMail",
//            templateUrl: function (stateParams) {
//              return fullUrlBase + templateBase + '/edit/' + stateParams.idMail;
//            },
//            controller: 'editController',
//          });
                      .state('select', {
                        url: "/select/:idmail",
                        templateUrl: fullUrlBase + templateBase + '/select',
                        controller: 'selectController'
                      })
                      .state('selectautoresp', {
                        url: "/selectautoresponder/:idautoresponder",
                        templateUrl: fullUrlBase + templateBase + '/selectautoresp',
                        controller: 'selectautorespController'
                      });
            }])
          ;
          
})();
