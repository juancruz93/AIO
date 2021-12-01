'use strict';
(function () {
  angular.module('mail', ['ui.router', 'mail.controllers', 'mail.directives', 'mail.services', 'ngMaterial', 'ui.select', 'ngSanitize', 'moment-picker', 'angularMoment', 'angularFileUpload'])
//    .config(function($mdAriaProvider) {
//      // Globally disables all ARIA warnings.
//      $mdAriaProvider.disableWarnings();
//    })
          .constant('constantMail', {
            permissionFBAdmin:"ADMINISTER",
            permissionFBBasicAdmin:"CREATE_CONTENT",
            permissionFBCreateContent:"BASIC_ADMIN",
            
            errorApiFacebook:"Ha ocurrido un problema. Por favor intente de nuevo",
            errorLengthFanPage:"No tiene fan page asociadas. Por favor intente de nuevo.",
            
            templateModalPageFacebook:'<md-dialog aria-label="">' +
              //Toolbar  
              "<md-toolbar>" +
              "<div class=\"md-toolbar-tools\">" +
              "<h4 class=\"modal-title\" id=\"exampleModalLabel\">Seleccionar fan page</h4>" +
              "<span flex></span>" +
              "<md-button class=\"md-icon-button\" ng-click=\"cancel()\">" +
              "<md-icon  aria-label=\"Close dialog\"></md-icon>" +
              "</md-button>" +
              "</div>" +
              "</md-toolbar>" +
              //Dialog Content
              "<md-dialog-content >" +
              '<div class="container-fluid">'+
              '<div class="row row-flex row-flex-wrap">'+
              '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" ng-if="pages>0">'+
                '<h1>No tiene actualmente Fan Page para seleccionar.</h1>'+
              '</div>'+
              '<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4 cursor-pointer"  ng-repeat="page in pages" ng-click="selectedPage(page)"> '+
              '<div class="card facebook-list">'+
                '<div class="text-center">'+
                    '<a ><img src="{{page.picture}}" class="img-circle"/></a>'+
                    '<h3 class="text-center">{{page.name}}</h3>'+
                '</div>'+
              '</div>'+
              '</div>'+
              '</div>'+
              '</div>'+
              '<br>'+
              '<div class=row>' +
              '<md-dialog-actions class="col-lg-12 text-right">' +
              "<button type=\"button\" class=\"btn btn-default\"  ng-click=\"closeDialog()\">Cerrar</button>" +
              '</md-dialog-actions>' +
              '</div>' +
              '</md-dialog-content>' +
              '</md-dialog>'
          })
          .config(['$stateProvider', '$urlRouterProvider', function ($stateProvider, $urlRouterProvider) {
              $stateProvider
                      .state('describe', {
                        url: "/basicinformation/:id",
                        templateUrl: fullUrlBase + templateBase + '/basicinformation',
                        controller: 'basicinformationController'
                      })
                      .state('addressees', {
                        url: "/addressees/:id",
                        templateUrl: fullUrlBase + templateBase + '/addressees',
                        controller: 'addAddresseesController'
                      })
                      .state('content', {
                        url: "/content/:id",
                        templateUrl: fullUrlBase + templateBase + '/content',
                        controller: 'contentController'
                      })
                      .state('advanceoptions', {
                        url: "/advanceoptions/:id",
                        templateUrl: fullUrlBase + templateBase + '/advanceoptions/',
                        controller: 'advanceoptionsController'
                      })
                      .state('shippingdate', {
                        url: "/shippingdate/:id",
                        templateUrl: fullUrlBase + templateBase + '/shippingdate',
                        controller: 'shippingdateController'
                      })
//        $urlRouterProvider.otherwise(function ($injector, $location) {
//          var $state = $injector.get('$state');
//          $state.go('index');
//        });
            }]);
})();
