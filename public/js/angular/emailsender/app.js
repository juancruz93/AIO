angular.module('emailsender',
        ['ui.router', 'emailsender.services',
          'emailsender.controller',
          'ngAnimate',
          'toastr',
          'moment-picker',
          "ngMaterial"])

        .config(['$stateProvider', '$urlRouterProvider', 'contantemailsender', function ($stateProvider, $urlRouterProvider, contantemailsender) {
            $stateProvider
                    .state('index', {
                      url: "/",
                      templateUrl: fullUrlBase + contantemailsender.Misc.TemplateBase + '/list',
                      controller: 'listController'
                    })
                    .state('create', {
                      url: "/create",
                      templateUrl: fullUrlBase + contantemailsender.Misc.TemplateBase + '/create',
                      controller: 'createController'
                    })
                    .state('edit', {
                      url: "/edit/:idEmailsender",
                      templateUrl: fullUrlBase + contantemailsender.Misc.TemplateBase + '/edit',
                      controller: 'editController'
                    });

          }])
        .constant("contantemailsender", {
          Misc: {
            TemplateBase: "emailsender",
          },
          UrlPeticion: {
            list: 'api/emailsender/list/',
            delete: 'api/emailsender/delete', 
            save: 'api/emailsender/saveemailsender', 
            get: 'api/emailsender/getemailsender/', 
            edit: 'api/emailsender/edit', 
          },
          State: {
            list: {
              state: "index",
            }
          },
        });

;
          