(function () {
  angular.module('segment', ['ngRoute', 'segment.controllers', 'segment.directives', 'segment.services', 'xeditable', 'ui.select', 'ngSanitize', "mgcrea.ngStrap", "moment-picker", "ngMaterial"])
          .run(function (editableOptions, editableThemes) {
            editableThemes.bs3.inputClass = 'input-sm';
            editableThemes.bs3.buttonsClass = 'btn-sm';
            editableOptions.theme = 'bs3';
          })
          .config(['momentPickerProvider', function (momentPickerProvider) {
              momentPickerProvider.options({
                locale: 'es',
                format: 'L LTS',
                minView: 'decade',
                maxView: 'minute',
                startView: 'year',
                today: true
              });
            }])
          .config(['$routeProvider', function ($routeProvider) {
              $routeProvider
                      .when('/', {
                        templateUrl: fullUrlBase + templateBase + '/list',
                        controller: 'SegmentController'
                      })
                      .when('/newsegment', {
                        templateUrl: fullUrlBase + templateBase + '/newsegment',
                        controller: 'NewsegmentController'
                      })
                      .when('/editsegment/:id', {
                        templateUrl: fullUrlBase + templateBase + '/editsegment',
                        controller: 'EditsegmentController'
                      })
                      .otherwise({
                        redirectTo: '/'
                      });
            }])

})();
