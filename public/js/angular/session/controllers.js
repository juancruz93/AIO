(function () {
  angular.module("session.controllers", [])
          .filter('propsFilter', function () {
            return function (items, props) {
              var out = [];

              if (angular.isArray(items)) {
                var keys = Object.keys(props);

                items.forEach(function (item) {
                  var itemMatches = false;

                  for (var i = 0; i < keys.length; i++) {
                    var prop = keys[i];
                    var text = props[prop].toLowerCase();
                    if (item[prop].toString().toLowerCase().indexOf(text) !== -1) {
                      itemMatches = true;
                      break;
                    }
                  }

                  if (itemMatches) {
                    out.push(item);
                  }
                });
              } else {
                // Let the output be the input untouched
                out = items;
              }

              return out;
            };
          })
          .controller("loginController", ["$scope", "$state", "notificationService", "restServices", function ($scope, $state, notificationService, restServices) {
              $scope.initComponents = function () {
                $scope.loginFacebook.configInitFB();
              };
              sessionStorage.clear();
              $scope.login = function () {
                restServices.loginEmail($scope.data).then(function (data) {
                  if (angular.isDefined(data.roles)) {
                    sessionStorage.setItem("roles", JSON.stringify(data.roles));
                  }
                  if (angular.isDefined(data.rol)) {
                    sessionStorage.setItem("rol", data.rol);
                  }
                  sessionStorage.setItem("email", data.email);
                  $state.go("loginpass");
                });
              };

              $scope.loginFacebook = {
                appIdFB: restServices.getAppIdFB(),
                configInitFB: function () {
                  this.appIdFB.then(function (data) {
                    window.fbAsyncInit = function () {
                      FB.init({
                        appId: data.idAppFb,
                        xfbml: true,
                        version: 'v2.9'
                      });
                      FB.AppEvents.logPageView();
                    };

                    (function (d, s, id) {
                      var js, fjs = d.getElementsByTagName(s)[0];
                      if (d.getElementById(id)) {
                        return;
                      }
                      js = d.createElement(s);
                      js.id = id;
                      js.src = "//connect.facebook.net/en_US/sdk.js";
                      fjs.parentNode.insertBefore(js, fjs);
                    }(document, 'script', 'facebook-jssdk'));

                  }).catch(function (data) {
                    console.error("Full error" + data.message);
                  });
                },
                loginFB: function () {
                  $scope.validateUserFB = this.validateUserFB;
                  FB.login(function (response) {
                    $scope.validateUserFB();
                  }, {scope: 'publish_actions,publish_pages,manage_pages,public_profile,email'});
                },
                validateUserFB: function () {
                  FB.getLoginStatus(function (response) {
                    if (response.status == 'connected') {
                      let url = 'me?fields=email,id';
                      FB.api(url, function (response) {
                        restServices.loginWithFacebook(response).then(function (data) {
                          switch (data.status) {
                            case "authorized":
                              document.location.href = fullUrlBase;
                              break;
                            case "completeprofile":
                              document.location.href = fullUrlBase + "register#/completeprofile";
                              break;
                            case "notregistered":
                              angular.element(document.querySelector("#modalLoginFb")).modal();
                              break;
                            default:
                              notificationService.error("La respuesta del servidor es desconocida");
                              break;
                          }
                        }).catch(function (data) {
                          notificationService.error(data.message);
                        });
                      });
                    } else if (response.status == 'not_authorized') {
                      notificationService.warning("Debes autorizar nuestra aplicación para poder iniciar sesión");
                    } else {
                      notificationService.warning("Debes ingresar tu cuenta de Facebook para poder iniciar sesión");
                    }
                  });
                }
              };
            }])
          .controller("loginpassController", ["$scope", "$state", "notificationService", "restServices", function ($scope, $state, notificationService, restServices) {
              $scope.data = {};
              var email = sessionStorage.getItem("email");

              if (email === null) {
                $state.go("index");
              }

              $scope.roles = JSON.parse(sessionStorage.getItem("roles"));
              if ($scope.roles == null) {
                $scope.data.rol = "";
              }

              if (sessionStorage.getItem("rol") != null) {
                $scope.data.rol = sessionStorage.getItem("rol");
              }

              $scope.data.email = email;

              $scope.cancel = function () {
                $state.go("index");
              };

              $scope.login = function () {
                restServices.loginPass($scope.data).then(function (data) {
                  if (data.status === "authorized") {
                    sessionStorage.clear();
                    document.location.href = fullUrlBase;
                  }
                }).catch(function (data) {
                  notificationService.error(data.message);
                });
              };
            }])
          .controller("recoverpassController", ["$scope", "$state", "notificationService", "restServices", function ($scope, $state, notificationService, restServices) {
              
              $scope.misc = {};
              $scope.data = {};
              
              $scope.functions = {
                validateRoles: function () {
                  if ($scope.misc.verified == false) {
                    $scope.restServicesFunction.recoverpass();
                  } else {
                    if (angular.isDefined($scope.data.email) && angular.isDefined($scope.data.rol)) {
                      $scope.restServicesFunction.recoverpassGenerateMail($scope.data.email, $scope.data.rol);
                    } else {
                      if (!angular.isDefined($scope.data.email)) {
                        notificationService.warning("No ha digitado un email.");
                      }
                      if (!angular.isDefined($scope.data.rol)) {
                        notificationService.warning("No ha seleccionado un rol para recuperar contraseña.");
                      }
                    }
                  }
                },
                initializeVariable: function () {
                  $scope.misc.verified = false;
                },
                changeData: function (data) {
                  $scope.data.roles = data.roles;
                  $scope.misc.verified = data.verified;
                },
                validate: function (data) {
                  if (angular.isDefined(data.rol) && angular.isDefined(data.email)) {
                    $scope.restServicesFunction.recoverpassGenerateMail(data.email, data.rol);
                  }
                },
                refress: function (){
                  $state.go($state.current, {}, {reload: true});
                }
              };

              $scope.restServicesFunction = {
                recoverpass: function () {
                  restServices.emailRecoverpass($scope.data).then(function (data) {
                    $scope.functions.changeData(data);
                    $scope.functions.validate(data);
                  });
                },
                recoverpassGenerateMail: function (email, rol) {
                  restServices.recoverpassGenerateMail(email, rol).then(function (data) {
                    notificationService.successSession(data.message);
                    $state.go("index");
                  }).catch(function (data) {
                    notificationService.error(data.message);
                    $state.go("index");
                  });
                }
              };
              $scope.functions.initializeVariable();
            }]);
})();
