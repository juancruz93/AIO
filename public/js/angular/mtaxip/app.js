angular.module('mtaxip',
        ['ui.router', 'mtaxip.services',
          'mtaxip.controller',
          'ngAnimate',
          'toastr',
          'moment-picker',
          "ngMaterial",
          "ui.select",
          'ngSanitize'
        ])

        .config(['$stateProvider', '$urlRouterProvider', 'contantmtaxip', function ($stateProvider, $urlRouterProvider, contantmtaxip) {
            $stateProvider
                    .state('index', {
                      url: "/",
                      templateUrl: fullUrlBase + contantmtaxip.Misc.TemplateBase + '/list',
                      controller: 'listController'
                    })
                    .state('create', {
                      url: "/create",
                      templateUrl: fullUrlBase + contantmtaxip.Misc.TemplateBase + '/create',
                      controller: 'createController'
                    })
                    .state('edit', {
                      url: "/edit/:idMta",
                      templateUrl: fullUrlBase + contantmtaxip.Misc.TemplateBase + '/edit',
                      controller: 'editController'
                    });

          }])
        .constant("contantmtaxip", {
          Misc: {
            TemplateBase: "mtaxip",
          },
          UrlPeticion: {
            list: 'api/mtaxip/list/',
            delete: 'api/mtaxip/delete', 
            save: 'api/mtaxip/savemtaxip', 
            get: 'api/mtaxip/getmtaxip/', 
            edit: 'api/mtaxip/edit', 
            getip: 'api/mtaxip/getipmta', 
          },
          State: {
            list: {
              state: "index",
            }
          },
        });

;
          