(function () {
  angular.module('contact.directives', [])
          .directive('fileModel', ["$parse", function ($parse) {
              return {
                restrict: 'A',
                link: function (scope, element, attrs) {
                  var model = $parse(attrs.fileModel);
                  var modelSetter = model.assign;

                  element.bind('change', function () {
                    scope.$apply(function () {
                      modelSetter(scope, element[0].files[0]);
                    });
                  });
                }
              };
            }])
          .filter('trustedhtml', function ($sce) {
            return function (value, type) {
              return $sce.trustAs(type || 'html', value);
            }
          }
          );
})();
