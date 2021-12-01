 angular.module('smscategory', ['ui.router', 'smscategory.controllers', 'smscategory.services', 'ngMaterial', 'ui.select', 'ngSanitize', 'ui.bootstrap.datetimepicker'])
    .config(['$stateProvider', '$urlRouterProvider', function ($stateProvider, $urlRouterProvider) {
            $stateProvider
                .state('index', {
                  url: "/",
                  templateUrl: fullUrlBase + templateBase + '/list',
                  controller: 'listController'
                });
            $urlRouterProvider.otherwise(function ($injector, $location) {
              var $state = $injector.get('$state');
//              $state.go('index');
            });
        }])
    .constant('constantSmsCategory',{
        urlPeticion: {
            getSmsCategory: fullUrlBase + 'api/smscategory/getsmscategory/',
            deletesmscategory: fullUrlBase + 'api/smscategory/deletesmscategory',
        },
        messageNotification:{
          duration: 4000,
          styles:{
            danger: {
               icon: 'glyphicon glyphicon-remove-circle',
              color: 'danger'
            },
            success:{
              color: 'glyphicon glyphicon-ok-circle',
               icon: 'success'
            },
            warning: {
               icon: 'glyphicon glyphicon-exclamation-sign',
              color: 'warning'
            },
            notice: {
              icon : 'glyphicon glyphicon-exclamation-sign', 
              color: 'notice'
            },
            info: {
              icon : 'glyphicon glyphicon-exclamation-sign', 
              color: 'info'
            }
          }
        },
    });