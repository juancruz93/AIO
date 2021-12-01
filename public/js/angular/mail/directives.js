'use strict';
(function () {
  angular.module('mail.directives', [])
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
          )
          .directive('fb', ['$FB', function ($FB) {
              return {
                restrict: "E",
                replace: true,
                template: "<div id='fb-root'></div>",
                compile: function (tElem, tAttrs) {
                  return {
                    post: function (scope, iElem, iAttrs, controller) {
                      var fbAppId = iAttrs.appId || '';

                      var fb_params = {
                        appId: iAttrs.appId || "",
                        cookie: iAttrs.cookie || true,
                        status: iAttrs.status || true,
                        xfbml: iAttrs.xfbml || true
                      };

                      // Setup the post-load callback
                      window.fbAsyncInit = function () {
                        $FB._init(fb_params);

                        if ('fbInit' in iAttrs) {
                          iAttrs.fbInit();
                        }
                      };

                      (function (d, s, id, fbAppId) {
                        var js, fjs = d.getElementsByTagName(s)[0];
                        if (d.getElementById(id))
                          return;
                        js = d.createElement(s);
                        js.id = id;
                        js.async = true;
                        js.src = "//connect.facebook.net/en_US/all.js";
                        fjs.parentNode.insertBefore(js, fjs);
                      }(document, 'script', 'facebook-jssdk', fbAppId));
                    }
                  }
                }
              };
            }]);
})();