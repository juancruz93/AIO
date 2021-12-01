angular.module("smsxemailApp",["ui.router", "smsxemail.services", "smsxemail.controllers", "ui.select", "ngMaterial", "ngSanitize"])
        .config(['$stateProvider','$urlRouterProvider', function ($stateProvider, $urlRouterProvider) {
          $stateProvider
            .state("list", {
              url: "/",
              templateUrl: fullUrlBase + templateBase + "/create",
              controller: "createController"
            }); 
            $urlRouterProvider.otherwise("/");
        }])
        .constant('constantPageSmsxemail', {
          UrlPeticion: {
            create: fullUrlBase + 'api/' + templateBase + '/create',
            getone: fullUrlBase + 'api/' + templateBase + '/getone',
            copykey: fullUrlBase + 'api/' + templateBase + '/copykey/',
          },
          Filter:{
            minChar:3
          }
        });
