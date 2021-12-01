(function () {
  angular.module("register.controllers", [])
          .constant("constantRegister",{
            menssasmessage:{
              errorTermsConditions:"Debe aceptar los términos y condiciones",
              errorTermsConditionsFacebook:"Debe aceptar los términos y condiciones para suscribirse con Facebook",
              
            }
          })
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
          .controller("signupController", ["$scope", "$state", "notificationService", "restServices","constantRegister", function ($scope, $state, notificationService, restServices,constantRegister) {
              $scope.misc = {};
              $scope.misc.termsConfitionsAccount = false;
              $scope.misc.termsConfitionsFacebook = false;
              $scope.validateEmpShow = true;
              
              $scope.initComponents = function () {
                $scope.data = {};
                $scope.data.account = {};
                $scope.loader = false;
                $scope.loaderBar = false;
                restServices.countries().then(function (data) {
                  $scope.listcountry = data;
                }).catch(function (data) {
                  notificationService.error(data.message);
                });
                
                $scope.loginnetworkingsocials.configInitFB();
              };
              
              $scope.states = function (idCountry) {
                $scope.data.account.idState = undefined;
                $scope.data.account.idCity = undefined;
                $scope.liststates = [];
                $scope.listcities = [];
                restServices.states(idCountry).then(function (data) {
                  $scope.liststates = data;
                  $scope.showstate = false;
                }).catch(function (data) {
                  notificationService.error(data.message);
                });
              };
              
              $scope.cities = function (state) {
                $scope.data.account.idCity = undefined;
                $scope.listcities = [];
                restServices.cities(state).then(function (data) {
                  $scope.listcities = data;
                  $scope.showcity = false;
                }).catch(function (data) {
                  notificationService.error(data.message);
                });
              };
              
              //FUNCION PARA SABER QUE CHECK ESTA SELECCIONADO Y ALTERAR LA VARIABLE GLOBAL
              $scope.validateEmpY = function (value){
                if(value.data.account.ycheckEmp){
                    $("#ncheckEmp").prop("checked", false);
                    $scope.validateEmpShow = true;
                }else{
                    $("#ycheckEmp").prop("checked", false);
                    $scope.validateEmpShow = false;
                }
              };
              
              $scope.validateEmpN = function (value){
                $("#ycheckEmp").prop("checked", false);
                $scope.validateEmpShow = false;
              };
              
              $scope.createAccount = function () {
                if ($scope.data.account.acceptTermsConditions != true) {
                  $scope.misc.termsConfitionsAccount = true;
                  notificationService.error(constantRegister.menssasmessage.errorTermsConditions);
                  return;
                }
                if($scope.data.account.nit == null && $scope.data.account.ycheckEmp == true){
                    notificationService.error("El campo NIT es obligatorio");
                  return;
                }
                if($scope.data.account.nomemp == null && $scope.data.account.ycheckEmp == true){
                    notificationService.error("El campo nombre empresa es obligatorio");
                  return;
                }
                $scope.loader = true;
                restServices.create($scope.data).then(function (data) {
                  $scope.loader = false;
                  window.location.href = fullUrlBase + "register/congratulations/" + data;
                }).catch(function (data) {
                  $scope.loader = false;
                  notificationService.error(data.message);
                });
              };
              
              $scope.loginnetworkingsocials = {
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
                  if ($scope.acceptTermsConditionsFacebook !=true) {
                    $scope.misc.termsConfitionsFacebook = true;
                    notificationService.error(constantRegister.menssasmessage.errorTermsConditionsFacebook);
                    return;
                  }
                  $scope.loaderBar = true;
                  const validateFB = this.validateUserFB;
                  FB.login(function (response) {
                    validateFB();
                    $scope.loaderBar = false;
                  }, {scope: 'publish_actions,publish_pages,manage_pages,public_profile,email'});
                },
                validateUserFB: function () {
                  FB.getLoginStatus(function (response) {
                    if (response.status == 'connected') {
                      let url = 'me?fields=email,id,first_name,last_name';
                      FB.api(url, function (response) {
                        restServices.createAccountFB(response,$scope.acceptTermsConditionsFacebook).then(function (data) {
                          window.location.href = fullUrlBase + "register/congratulations/" + data.idUser;
                        }).catch(function (data) {
                          notificationService.error(data.message);
                        });
                      });
                    } else if (response.status == 'not_authorized') {
                      $scope.loaderBar = false;
                      notificationService.warning("Debes autorizar nuestra aplicación para poder registrarte");
                    } else {
                      $scope.loaderBar = false;
                      notificationService.warning("Debes ingresar tu cuenta de Facebook para poder registrarte");
                    }
                    $scope.loaderBar = false;
                  });
                  $scope.loaderBar = false;
                }
              };
            }])
          .controller("paymentplanController", ["$scope", "$rootScope", "$state", "$stateParams", "notificationService", "restServices", function ($scope, $rootScope, $state, $stateParams, notificationService, restServices) {
              if ($stateParams.id === "") {
                notificationService.error("No hay ningún dato de cuenta para verificar");
                $state.go("index");
              }
              
              $scope.initialize = function () {
                restServices.verifyAccount($stateParams.id).then(function (data) {
                  sessionStorage.setItem("idAcc", data);
                }).catch(function (data) {
                  notificationService.error(data.message);
                  $state.go("index");
                });
                
                $rootScope.idAcc = sessionStorage.getItem("idAcc");
                $scope.data = {};
                restServices.paymentsplans().then(function (data) {
                  $scope.listpay = data;
                }).catch(function (data) {
                  notificationService.error(data.message);
                });
              };
              $scope.initialize();
              
              $scope.viewPaymentPlan = function (idPaymentPlan) {
                $state.go("payment.paymentplan.detail", {idPaymentPlan: idPaymentPlan});
              };
              
              $rootScope.current = $state.is('payment.paymentplan');
            }])
          .controller("detailController", ["$scope", "$rootScope", "$state", "$stateParams", "notificationService", "restServices", function ($scope, $rootScope, $state, $stateParams, notificationService, restServices) {
              $scope.loader = true;
              restServices.paymentpladetail($stateParams.idPaymentPlan).then(function (data) {
                $scope.data = data;
                $scope.loader = false;
              }).catch(function (data) {
                notificationService.error(data.message);
              });
              
              $scope.updatePlanAccount = function () {
                var data = {
                  idSub: $rootScope.idAcc,
                  paymentPlan: $stateParams.idPaymentPlan
                };
                restServices.assignPaymentPlan(data).then(function (data) {
                  console.log(data);
                }).catch(function (data) {
                  notificationService.error(data.message);
                });
                //$state.go("payment.pay");
              };
            }])
          .controller("paymentController", ["$rootScope", "$state", function ($rootScope, $state) {
              $rootScope.current = false;
              $state.go("payment.paymentplan");
            }])
          .controller("payController", ["$scope", "$rootScope", "$state", "notificationService", "restServices", function ($scope, $rootScope, $state, notificationService, restServices) {
              $rootScope.current = $state.is('payment.paymentplan');
            }])
          .controller("completeprofileController", ["$scope", "$rootScope", "$state", "notificationService", "restServices", function ($scope, $rootScope, $state, notificationService, restServices) {
              $scope.initComponents = function () {
                restServices.verifyStatusUser().then(function (response) {
                  if (response.status == "authorized") {
                    restServices.countries().then(function (data) {
                      $scope.listcountry = data;
                    }).catch(function (data) {
                      notificationService.error(data.message);
                    });
                  } else {
                    document.location.href = fullUrlBase + "session#/";
                  }
                }).catch(function (error) {
                  notificationService.error(error.message);
                });
              };
              
              $scope.states = function (idCountry) {
                $scope.data.account.idState = undefined;
                $scope.data.account.idCity = undefined;
                $scope.liststates = [];
                $scope.listcities = [];
                restServices.states(idCountry).then(function (data) {
                  $scope.liststates = data;
                  $scope.showstate = false;
                }).catch(function (data) {
                  notificationService.error(data.message);
                });
              };
              
              $scope.cities = function (state) {
                $scope.data.account.idCity = undefined;
                $scope.listcities = [];
                restServices.cities(state).then(function (data) {
                  $scope.listcities = data;
                  $scope.showcity = false;
                }).catch(function (data) {
                  notificationService.error(data.message);
                });
              };
              
              $scope.completeProfile = function () {
                if ($scope.data.account.idCity == undefined) {
                  notificationService.warning("Debe seleccionar una ciudad para poder continuar");
                  return false;
                }
                restServices.completeProfileUser($scope.data.account.idCity).then(function (response) {
                  if (response.status === "authorized") {
                    document.location.href = fullUrlBase;
                  } else if (response.status === "inauthorized") {
                    document.location.href = fullUrlBase + "session#/";
                  }
                }).catch(function (error) {
                  notificationService.error(error.message);
                });
              }
            }]);
})();
