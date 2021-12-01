angular.module('namesender',
        ['ui.router', 'namesender.services',
          'namesender.controller',
          'ngAnimate',
          'toastr',
          'moment-picker',
          "ngMaterial"])

        .config(['$stateProvider', '$urlRouterProvider', 'contantnamesender', function ($stateProvider, $urlRouterProvider, contantnamesender) {
            $stateProvider
                    .state('index', {
                      url: "/",
                      templateUrl: fullUrlBase + contantnamesender.Misc.TemplateBase + '/list',
                      controller: 'listController'
                    })
                    .state('create', {
                      url: "/create",
                      templateUrl: fullUrlBase + contantnamesender.Misc.TemplateBase + '/create',
                      controller: 'createController'
                    })
                    .state('edit', {
                      url: "/edit/:idNameSender",
                      templateUrl: fullUrlBase + contantnamesender.Misc.TemplateBase + '/edit',
                      controller: 'editController'
                    });

          }])
        .constant("contantnamesender", {
          Misc: {
            TemplateBase: "namesender",
          },
          UrlPeticion: {
            list: 'api/namesender/list/',
            delete: 'api/namesender/delete', 
            save: 'api/namesender/savenamesender', 
            get: 'api/namesender/getnamesender/', 
            edit: 'api/namesender/edit', 
          },
          State: {
            list: {
              state: "index",
            }
          },
        });

;
          