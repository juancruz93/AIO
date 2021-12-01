angular.module('contactlist', [ 'ngRoute', 'contactlist.controllers', 'contactlist.services', 'mgcrea.ngStrap', 'ngAnimate', 'ngMaterial', 'ui.select',  'ngSanitize', 'ui.bootstrap.datetimepicker'])
  .config(['$routeProvider', function ($routeProvider) {
      $routeProvider
        .when('/', {
          templateUrl: fullUrlBase + templateBase + '/list',
          controller: 'ContactlistController'
        })
        .when('/add', {
          templateUrl: fullUrlBase + templateBase + '/add',
          controller: 'ContactlistAddController'
        })
        .when('/edit/:id', {
          templateUrl: fullUrlBase + templateBase + '/edit',
          controller: 'ContactlistEditController'
        })
        .when('/editcustomfield/:id', {
          templateUrl: fullUrlBase + templateBase + '/editcustomfield',
          controller: 'EditCustomFieldController'
        })
        .when('/delete/:id', {
          templateUrl: fullUrlBase + templateBase + '/delete',
          controller: 'ContactlistDeleteController'
        })
        .when('/deletecustomfield/:id', {
          templateUrl: fullUrlBase + templateBase + '/deletecustomfield',
          controller: 'CustomfieldDeleteController'
        })
        .when('/addcustomfield/:id', {
          templateUrl: fullUrlBase + templateBase + '/addcustomfield',
          controller: 'AddcustomfieldController'
        })
        .when('/customfield/:id', {
          templateUrl: fullUrlBase + templateBase + '/customfield',
          controller: 'customfield'
        })
        .otherwise({
          redirectTo: '/'
        });

    }])
  .constant('constantContactList',{
    urlPeticion:{
         getcontactlists: fullUrlBase + 'api/contactlist/getcontactlists/',
         getcontactlist: fullUrlBase + 'api/contactlist/getcontactlist/',
         getcontactlistcategory: fullUrlBase + 'api/contactlist/getcontactlistcategory',
         getonecustomfield: fullUrlBase + 'api/contactlist/getonecustomfield/',
         exportcontacts: fullUrlBase + 'api/contactlist/exportcontacts/',
         addcontactlist: fullUrlBase + 'api/contactlist/add',
         addcustomfield: fullUrlBase + 'api/contactlist/addcustomfield',
         editcontactlist: fullUrlBase + 'api/contactlist/edit/',
         editcustomfield: fullUrlBase + 'api/contactlist/editcustomfield/',
         deleteContactlist: fullUrlBase + 'api/contactlist/delete/',
         deletecustomfield: fullUrlBase + 'api/contactlist/deletecustomfield/',
         listcustomfield: fullUrlBase + 'api/contactlist/listcustomfield/',         
         getcontactlistbysubaccount: fullUrlBase + 'api/contactlist/getcontactlistbysubaccount',
         savecontactlistcategory: fullUrlBase + 'api/contactlist/savecategory',
         permissionCustomfield: fullUrlBase + 'api/contactlist/permissioncustomfield/',
         gettotalcontactlist: fullUrlBase + 'api/contactlist/gettotalcontactlist'
    },
    messageNotification:{
          duration: 400,
          styles:{
            info: {
              icon : 'glyphicon glyphicon-exclamation-sign', 
              color: 'info'
            },
            notice: {
              icon : 'glyphicon glyphicon-exclamation-sign', 
              color: 'notice'
            },
            warning: {
               icon: 'glyphicon glyphicon-exclamation-sign',
              color: 'warning'
            },
            success:{
              color: 'success',
               icon: 'glyphicon glyphicon-ok-circle'
            },
            danger: {
               icon: 'glyphicon glyphicon-remove-circle',
              color: 'danger'
            }
          }
        },
     misc:{
       regexEmail:/^(([^<>()[\]\.,;:\s@\"]+(\.[^<>()[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i
     }
        
  });
