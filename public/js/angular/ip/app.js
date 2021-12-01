angular.module('ip',
        ['ui.router', 'ip.services',
          'ip.controller',
          'ngAnimate',
          'toastr',
          'moment-picker',
          "ngMaterial"])

        .config(['$stateProvider', '$urlRouterProvider', 'contantip', function ($stateProvider, $urlRouterProvider, contantip) {
            $stateProvider
                    .state('index', {
                      url: "/",
                      templateUrl: fullUrlBase + contantip.Misc.TemplateBase + '/list',
                      controller: 'listController'
                    })
                    .state('create', {
                      url: "/create",
                      templateUrl: fullUrlBase + contantip.Misc.TemplateBase + '/create',
                      controller: 'createController'
                    })
                    .state('edit', {
                      url: "/edit/:idIp",
                      templateUrl: fullUrlBase + contantip.Misc.TemplateBase + '/edit',
                      controller: 'editController'
                    });

          }])
        .constant("contantip", {
          Misc: {
            TemplateBase: "ip",
          },
          UrlPeticion: {
            list: 'api/ip/list/',
            delete: 'api/ip/delete', 
            save: 'api/ip/saveip', 
            get: 'api/ip/getip/', 
            edit: 'api/ip/edit', 
          },
          State: {
            list: {
              state: "index",
            }
          },
        });

;
          