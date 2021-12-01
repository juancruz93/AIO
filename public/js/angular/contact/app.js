(function () {
  angular.module('contact', ['ngRoute', 'contact.controllers', 'contact.directives', 'contact.services', 'xeditable', "ngMaterial", "checklist-model", "ui.bootstrap.datetimepicker"])
          .run(function (editableOptions, editableThemes) {
            editableThemes.bs3.inputClass = 'input-sm';
            editableThemes.bs3.buttonsClass = 'btn-sm';
            editableOptions.theme = 'bs3';
          })
          .config(['$routeProvider', function ($routeProvider) {
              $routeProvider
                      .when('/', {
                        templateUrl: fullUrlBase + templateBase + '/list',
                        controller: 'ContactController'
                      })
                      .when('/import', {
                        templateUrl: fullUrlBase + templateBase + '/import/' + idContactlist,
                        controller: 'ContactImportController'
                      })
                      .when('/newbatch', {
                        templateUrl: fullUrlBase + templateBase + '/newbatch',
                        controller: 'NewbatchController'
                      })
                      .when('/newcontact', {
                        templateUrl: fullUrlBase + templateBase + '/newcontact/' + idContactlist,
                        controller: 'NewcontactController'
                      })
                      .when('/history', {
                        templateUrl: fullUrlBase + templateBase + '/history',
                        controller: 'HistoryController'
                      })
                      /*.when('/import/contacts', {
                       templateUrl: '/' + relativeUrlBase + '/' + templateBase + '/importcontacts',
                       controller: 'ContactImportController'
                       })*/
                      .otherwise({
                        redirectTo: '/'
                      });
            }])
})();
