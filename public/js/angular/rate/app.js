angular.module("rateApp",["ui.router", "rate.services", "rate.controllers", "ui.bootstrap.datetimepicker", "ui.select", "ngMaterial", "ngSanitize"])
        .config(['$stateProvider','$urlRouterProvider', function ($stateProvider, $urlRouterProvider) {
          $stateProvider
            .state("list", {
              url: "/",
              templateUrl: fullUrlBase + templateBase + "/list",
              controller: "indexController"
            })
            .state("create", {
              url: "/create",
              templateUrl: fullUrlBase + templateBase + "/create",
              controller: "createController"
            }) 
            .state("edit", {
              url: "/edit/:idRate",
              templateUrl: fullUrlBase + templateBase + "/create",
              controller: "createController"
            }); 
            $urlRouterProvider.otherwise("/");
        }])
        .constant('constantPageRate', {
          UrlPeticion: {
            create: fullUrlBase + 'api/' + templateBase + '/create',
            getall: fullUrlBase + 'api/' + templateBase + '/getall',
            getone: fullUrlBase + 'api/' + templateBase + '/getone',
            edit: fullUrlBase + 'api/' + templateBase + '/edit',
            delete: fullUrlBase + 'api/' + templateBase + '/delete',
          },
          Filter:{
            minChar:3
          }
        });

//angular.element(document).ready(function () {
//    angular.bootstrap(document, ['rateApp']);
//});