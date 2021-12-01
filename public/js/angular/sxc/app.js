(function () {
  angular.module('sxc', ['ngRoute', 'sxc.controllers', 'sxc.directives', 'sxc.services', 'xeditable', "ngMaterial", "checklist-model"])
          .run(function (editableOptions, editableThemes) {
            editableThemes.bs3.inputClass = 'input-sm';
            editableThemes.bs3.buttonsClass = 'btn-sm';
            editableOptions.theme = 'bs3';
          })
          .config(['$routeProvider', function ($routeProvider) {
              $routeProvider
                      .when('/', {
//                        templateUrl: '/' + relativeUrlBase + '/' + "sxc" + '/findcontactsegment' ,
                        templateUrl: fullUrlBase + templateBase + '/findcontactsegment',
                        controller: 'ContactSegmentController'
                      })
                      .otherwise({
                        redirectTo: '/'
                      });
            }])
})();
