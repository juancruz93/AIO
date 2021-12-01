angular.module('mailtemplatecategory',
        ['ui.router', 'mailtemplatecategory.services',
          'mailtemplatecategory.controller',
          'ngAnimate',
          'toastr',
          'moment-picker',
          "ngMaterial"])

        .config(['$stateProvider', '$urlRouterProvider', 'contantMailtemplatecategory', function ($stateProvider, $urlRouterProvider, contantMailtemplatecategory) {
            $stateProvider
                    .state('index', {
                      url: "/",
                      templateUrl: fullUrlBase + contantMailtemplatecategory.Misc.TemplateBase + '/list',
                      controller: 'listController'
                    })
                    .state('create', {
                      url: "/create",
                      templateUrl: fullUrlBase + contantMailtemplatecategory.Misc.TemplateBase + '/create',
                      controller: 'createController'
                    })
                    .state('edit', {
                      url: "/edit/:idMailTemplateCategory",
                      templateUrl: fullUrlBase + contantMailtemplatecategory.Misc.TemplateBase + '/edit',
                      controller: 'editController'
                    });

          }])
        .constant("contantMailtemplatecategory", {
          Misc: {
            TemplateBase: "mailtemplatecategory",
          },
          UrlPeticion: {
            list: 'api/mailcategorytemplatecategory/list/',
            delete: 'api/mailcategorytemplatecategory/delete', 
            save: 'api/mailcategorytemplatecategory/savemailtempcategory', 
            get: 'api/mailcategorytemplatecategory/getmailtemplate/', 
            edit: 'api/mailcategorytemplatecategory/edit', 
          },
          State: {
            list: {
              state: "index",
            }
          },
        });

;
          