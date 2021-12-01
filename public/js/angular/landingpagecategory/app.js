/* 
 * Modulo principal
 */

angular.module('LandingPageCategoryApp', ['ui.router', 'LandingPageCategoryApp.services', 'LandingPageCategoryApp.controllers','ngAnimate', 'toastr','moment-picker'])
        .constant('constantPageCategory', {
          Global:{
            templateBase: "landingpagecategory"
          },
          UrlPeticion: {
            create: fullUrlBase + 'api/landingpagecategory/create',
            getall: fullUrlBase + 'api/landingpagecategory/getall',
            getone: fullUrlBase + 'api/landingpagecategory/getone',
            edit: fullUrlBase + 'api/landingpagecategory/edit',
            delete: fullUrlBase + 'api/landingpagecategory/delete',
          },
          State: {
            list: {
              state: "list",
              objState: {
                url: "/",
                templateUrl: fullUrlBase + 'landingpagecategory/list',
                controller: 'listCtrl'
              }
            },
            create: {
              state: "create",
              objState: {
                url: "/create",
                templateUrl: fullUrlBase +  'landingpagecategory/create',
                controller: 'Ctrl'
              }
            },
            edit: {
              state: "edit",
              objState: {
                url: "/edit/:idLandingPageCategory",
                templateUrl: fullUrlBase +  'landingpagecategory/create',
                controller: 'Ctrl'
              }
            }
          },
          Message:{
            edit: "Correcto",
            create: "Correcto",
            error: "Error"
          },
          Modals:{
            delete:"deleteCategory"
          },
          Filter:{
            minChar:3
          }
        })
        .config(['$stateProvider', '$urlRouterProvider', '$interpolateProvider', 'constantPageCategory', function ($stateProvider, $urlRouterProvider, $interpolateProvider, constantPageCategory) {
            $stateProvider
                    .state('list', constantPageCategory.State.list.objState)
                    .state('create', constantPageCategory.State.create.objState)
                    .state('edit', constantPageCategory.State.edit.objState);
            $urlRouterProvider.otherwise("/");
            //SE UTILIZA PARA UTILIZAR VARIABLES EN EL HTML DE ANGULAR REMPLAZA EL {{}}POR [[]]
            $interpolateProvider.startSymbol('[[');
            $interpolateProvider.endSymbol(']]');
          }]);

angular.element(document).ready(function () {
  angular.bootstrap(document, ['LandingPageCategoryApp']);
});


