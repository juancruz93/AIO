angular.module('replyto',
        ['ui.router', 'replyto.services',
          'replyto.controller',
          'ngAnimate',
          'toastr',
          'moment-picker',
          "ngMaterial"])

        .config(['$stateProvider', '$urlRouterProvider', 'contantreplyto', function ($stateProvider, $urlRouterProvider, contantreplyto) {
            $stateProvider
                    .state('index', {
                      url: "/",
                      templateUrl: fullUrlBase + contantreplyto.Misc.TemplateBase + '/list',
                      controller: 'listController'
                    })
                    .state('create', {
                      url: "/create",
                      templateUrl: fullUrlBase + contantreplyto.Misc.TemplateBase + '/create',
                      controller: 'createController'
                    })
                    .state('edit', {
                      url: "/edit/:idReplyTo",
                      templateUrl: fullUrlBase + contantreplyto.Misc.TemplateBase + '/edit',
                      controller: 'editController'
                    });

          }])
        .constant("contantreplyto", {
          Misc: {
            TemplateBase: "replyto",
          },
          UrlPeticion: {
            list: 'api/replyto/list/',
            delete: 'api/replyto/delete', 
            save: 'api/replyto/savereplyto', 
            get: 'api/replyto/getreplyto/', 
            edit: 'api/replyto/edit', 
          },
          State: {
            list: {
              state: "index",
            }
          },
        });

;
          