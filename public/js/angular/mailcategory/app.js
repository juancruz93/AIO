  angular.module('mailcategory', ['ui.router', 'mailcategory.controllers', 'mailcategory.services', 'ngMaterial', 'ui.select', 'ngSanitize', 'ui.bootstrap.datetimepicker'])
      .config(['$stateProvider', '$urlRouterProvider', function ($stateProvider, $urlRouterProvider) {
          $stateProvider
              .state('index', {
                url: "/",
                templateUrl: fullUrlBase + templateBase + '/list',
                controller: 'listController'
              })
              .state('add',{
                url:"/add",
                templateUrl: fullUrlBase + templateBase + '/add',
                controller: 'addmailcategoryController'
              })
              .state('edit',{
                url:"/edit/:idMail",
                templateUrl: fullUrlBase + templateBase + '/edit',
                controller: 'editmailcategoryController'
              });
          $urlRouterProvider.otherwise(function ($injector, $location) {
          var $state = $injector.get('$state');
          $state.go('index');
      });
      }])
      .constant('constantMailCategory',{
        urlPeticion: {
          getmailcategory: fullUrlBase + 'api/mailcategory/getmailcategory/',
          savemailcategory: fullUrlBase + 'api/mailcategory/savemailcategory',
          getonemailcategory: fullUrlBase + 'api/mailcategory/getonemailcategory/',
          editmailcategory: fullUrlBase + 'api/mailcategory/editmailcategory',
          deletemailcategory: fullUrlBase + 'api/mailcategory/deletemailcategory',
        },
         messageNotification:{
          duration: 4000,
          styles:{
            error: {
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

