(function () {

  angular.module('appTestSurvey', ['ngSanitize', 'ngMaterial', 'toastr'])
          .service('servicesTestSurvey', ['$http', '$q', function ($http, $q) {

            }])
          .constant('constTestSurvey', {
            templateModalPageFacebook: '<md-dialog aria-label="">' +
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
                    '<div class="container-fluid">' +
                    '<div class="row row-flex row-flex-wrap">' +
                    '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" ng-if="pages>0">' +
                    '<h1>No tiene actualmente Fan Page para seleccionar.</h1>' +
                    '</div>' +
                    '<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4 cursor-pointer"  ng-repeat="page in pages" ng-click="selectedPage(page)"> ' +
                    '<div class="card facebook-list">' +
                    '<div class="text-center">' +
                    '<a ><img src="{{page.picture}}" class="img-circle"/></a>' +
                    '<h3 class="text-center">{{page.name}}</h3>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '<br>' +
                    '<div class=row>' +
                    '<md-dialog-actions class="col-lg-12 text-right">' +
                    "<button type=\"button\" class=\"btn btn-default\"  ng-click=\"closeDialog()\">Cerrar</button>" +
                    '</md-dialog-actions>' +
                    '</div>' +
                    '</md-dialog-content>' +
                    '</md-dialog>'
          })
          .factory('$FB', ['$rootScope', function ($rootScope) {
              var fbLoaded = false;
              // Our own customisations
              var _fb = {
                loaded: fbLoaded,
                _init: function (params) {
                  if (window.FB) {
                    // FIXME: Ugly hack to maintain both window.FB
                    // and our AngularJS-wrapped $FB with our customisations
                    angular.extend(window.FB, _fb);
                    angular.extend(_fb, window.FB);
                    // Set the flag
                    _fb.loaded = true;
                    // Initialise FB SDK
                    window.FB.init(params);
                    if (!$rootScope.$$phase) {
                      $rootScope.$apply();
                    }
                  }
                }

              }
              return _fb;
            }])
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
            }])
          .controller('controllerTestSurvey', ['$scope', 'servicesTestSurvey', '$FB', '$mdDialog', 'constTestSurvey', '$q', '$timeout', 'toastr', function ($scope, servicesTestSurvey, $FB, $mdDialog, constTestSurvey, $q, $timeout, toastr) {
              $scope.fanPageSelected = false;
              //APP FACEBOOK
              function ModalPageFacebookCtrl($scope, $mdDialog, pages) {
                $scope.pages = pages
                $scope.hide = function () {
                  $mdDialog.hide();
                };
                $scope.closeDialog = function () {
                  $mdDialog.cancel();
                };
                $scope.selectedPage = function (page) {
                  $mdDialog.hide(page);
                }
              }
              checkLoginState = function () {
                alert("hola");
              }
              $scope.selectedPage = function () {
//                $FB.getLoginStatus(function (response) {
//                  if (response.status === 'connected') {
                $FB.api('/' + $FB.getUserID() + '/accounts', function (response) {
                  $scope.showModalSelectedPage(response);
                });
//                  } else {
//                    $FB.login(function () {
//                      $FB.api('/' + $FB.getUserID() + '/accounts', function (response) {
//                        $scope.showModalSelectedPage(response);
//                      });
//                    }, {
//                      scope: 'publish_actions,publish_pages,manage_pages'
//                    });
//                  }
//                });
              }
              $scope.getPicturesPage = function (id) {
                var defer = $q.defer();
                var promise = defer.promise;
                FB.api('/' + id + '/picture', function (response) {
                  defer.resolve(response);
                });
                return promise;
              }
              $scope.getFanPageArr = function (data) {
                var defer = $q.defer();
                var promises = [];
                var response = data;
                angular.forEach(response, function (value) {
                  promises.push($scope.getPicturesPage(value.id));
                });
                function setResolve(data) {
                  for (var i = 0; i < data.length; i++) {
                    response[i].picture = data[i].data.url;
                  }
                  defer.resolve(response);
                }
                $q.all(promises).then(function (data) {
                  setResolve(data)
                });
                return defer.promise;
              }
              $scope.showModalSelectedPage = function (data) {
                var pages = data.data

                $scope.getFanPageArr(pages).then(function (data) {
                  document.body.scrollTop = 0;
                  $mdDialog.show({
                    scope: $scope.$new(),
                    controller: ModalPageFacebookCtrl,
                    template: constTestSurvey.templateModalPageFacebook,
                    parent: angular.element(document.body),
                    clickOutsideToClose: true,
                    locals: {
                      pages: data
                    },
                  }).then(function (response) {
                    $scope.fanPageSelected = response;
                    console.log($scope.fanPageSelected);
                  }, function () {
                    if (!$scope.fanPageSelected) {
                      $scope.fanPageSelected = false;
                    }
                  });
                });
              }
              $scope.publish = function () {
                if ($scope.imagen) {
                  var post = {
                    access_token: $scope.fanPageSelected.access_token,
                  }
                  if ($scope.description != "" && typeof $scope.description != "undefined") {
                    post.caption = $scope.description;
                  }
                  if ($scope.imageUrl != "" && typeof $scope.imageUrl != "undefined") {
                    post.url = $scope.imageUrl;
                  } else {
                    alert("La imagen de posteo no puede estar vacio");
                    return;
                  }
                  $FB.api("/" + $scope.fanPageSelected.id + "/photos",
                          "POST", post,
                          function (response) {
                            if (response && !response.error) {
                              alert('Se realizo la publicación de manera exitosa. ');
                            } else {
                              alert('ocurrio un error haciendo la publicación');
                            }
                          });
                } else {
                  var post = {
                    access_token: $scope.fanPageSelected.access_token,
                  }
                  if ($scope.description != "" && typeof $scope.description != "undefined") {
                    post.message = $scope.description;
                  }
                  console.log($scope);
                  if ($scope.url != "" && typeof $scope.url != "undefined") {
                    post.link = $scope.url;
                  } else {
                    alert("El link de posteo no puede estar vacio");
                    return;
                  }
                  $FB.api("/" + $scope.fanPageSelected.id + "/feed",
                          "POST", post,
                          function (response) {
                            if (typeof response.error == "undefined") {
                              alert('Se realizo la publicación de manera exitosa. ');
                            } else {
                              alert('ocurrio un error haciendo la publicación');
                            }
                          });
                }
              }

              $scope.validateLogin = function () {
                $FB.getLoginStatus(function (response) {
                  if (response.status === 'connected') {
                    $scope.login = true;
                  } else {
                    $scope.login = false;
                  }
                });
              }

              $scope.loginFunc = function () {
                $FB.login(function () {
                  $scope.$apply(function () {
                    $scope.login = true;
                  });
                }, {
                  scope: 'publish_actions,publish_pages,manage_pages'
                });
              }

              console.log($FB);

              $scope.statusChangeCallback = function (response) {
                alert("hola");
              };
              if ($FB.loaded) {
                $scope.validateLogin();
              } else {
                $timeout($scope.validateLogin, 1000);
              }
            }]);

//  angular.bootstrap(document,['appTestSurvey']);
})();