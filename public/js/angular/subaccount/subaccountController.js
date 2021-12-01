angular.module('aio', ['platanus.keepValues', 'ngMaterial'])
        .factory('main', ['$http', '$window', function ($http, $window) {
            return {
              registerSubaccount: function (id, data, success, error) {
                var route = $window.myBaseURL + "subaccount/create/";
                $http.post(route + id, data).success(success).error(error);
              },
              country: function (success, error) {
                var route = $window.myBaseURL + "country/country";
                $http.get(route).success(success).error(error);
              },
              state: function (data, success, error) {
                var route = $window.myBaseURL + "country/state/";
                $http.get(route + data).success(success).error(error);
              },
              city: function (data, success, error) {
                var route = $window.myBaseURL + "country/cities/";
                $http.get(route + data).success(success).error(error);
              },
              editSubaccount: function (id, data, success, error) {
                var route = $window.myBaseURL + "subaccount/edit/";
                $http.post(route + id, data).success(success).error(error);
              },
              createUserSubaccount: function (idSubaccount, data, success, error) {
                var route = $window.myBaseURL + "subaccount/createuser/";
                $http.post(route + idSubaccount, data).success(success).error(error);
              }
            }
          }])
        .factory('notificationService', function () {
          function error(message) {
            slideOnTop(message, 4000, 'glyphicon glyphicon-remove-circle', 'danger');
          }

          function success(message) {
            slideOnTop(message, 4000, 'glyphicon glyphicon-ok-circle', 'success');
          }

          function warning(message) {
            slideOnTop(message, 4000, 'glyphicon glyphicon-exclamation-sign', 'warning');
          }

          function notice(message) {
            slideOnTop(message, 4000, 'glyphicon glyphicon-exclamation-sign', 'notice');
          }

          function primary(message) {
            slideOnTop(message, 4000, 'glyphicon glyphicon-exclamation-sign', 'primary');
          }

          return {
            error: error,
            success: success,
            warning: warning,
            notice: notice,
            primary: primary
          };
        })
        .controller('ctrlSubaccountCreateUser', ['$scope', 'main', '$rootScope', '$window', 'notificationService', function ($scope, main, $rootScope, $window, notificationService) {
            main.country(function (res) {
              $scope.country = res;
            }, function (res) {
              $rootScope.error = 'fail';
            });

            $scope.selectCountryUser = function (id) {
              if (!id) {
                id = $scope.countrySelectedUser;
              }
              $scope.stateUser = {};
              $scope.citiesUser = {};
              main.state(id, function (res) {
                $scope.stateUser = res;
              }, function (res) {
                $rootScope.error = 'fail';
              });
            };
            $scope.selectStateUser = function (id) {
              if (!id) {
                id = $scope.stateSelectedUser;
              }
              $scope.citiesUser = {};
              main.city(id, function (res) {
                $scope.citiesUser = res;
              }, function (res) {
                $rootScope.error = 'fail';
              });
            };

            $scope.createUserSub = function (idSubaccount) {
              //console.log($scope.data);
              main.createUserSubaccount(idSubaccount, $scope.data, function (res) {
                var route = $window.myBaseURL + "subaccount/userlist/" + res.idSubaccount;
                $window.location.href = route;
              }, function (err) {
                notificationService.error(err[0]);
                $rootScope.error = 'fail';
              });
            };
          }])
        .controller('ctrlSubaccount', ['$rootScope', '$scope', '$window', '$http', 'main', function ($rootScope, $scope, $window, $http, main) {
            $scope.services = [];
            $scope.status = true;
            main.country(function (res) {
              $scope.country = res;
              if (idCountry) {
                $scope.countrySelected = idCountry;
                $scope.selectCountry($scope.countrySelected);
              }
            }, function (res) {
              $rootScope.error = 'fail';
            })

            $scope.showsms = false;
            $scope.showsmstwoway = false;
            $scope.showemail = false;
            $scope.showsurvey = false;
            $scope.showlandingpage = false;
            ((smsTotal ) ? $scope.smsTotal = smsTotal : "");
            ((smstwowayTotal ) ? $scope.smstwowayTotal = smstwowayTotal : "");
            ((contactTotal ) ? $scope.contactTotal = contactTotal : "");
            ((spaceTotal ) ? $scope.spaceTotal = spaceTotal : "");
            ((questionTotal ) ? $scope.questionTotal = questionTotal : "");
            ((answerTotal ) ? $scope.answerTotal = answerTotal : "");    
            ((landingpageTotal) ? $scope.landingpageTotal = landingpageTotal : "");
            
            $scope.selectedServices = function () {              
              $scope.showsms = false;
              $scope.showsmstwoway = false;
              $scope.showemail = false;
              $scope.showsurvey = false;
              $scope.showlandingpage = false;
              for (var i = 0; i < $scope.services.length; i++) {
                if ($scope.services[i] == 1) {
                  $scope.showsms = true;
                }
                if ($scope.services[i] == 2) {
                  $scope.showemail = true;
                }
                if ($scope.services[i] == 5) {
                  $scope.showsurvey = true;
                }
                if ($scope.services[i] == 8) {
                  $scope.showlandingpage = true;
                }
                if ($scope.services[i] == 7) {
                    $scope.showsmstwoway = true;
                }
              }
            };

            $scope.createSubaccount = function (idAccount) {
              var data = {
                name: $scope.name,
                description: $scope.description,
                //diskSpace: $scope.fileSpace,
                contactLimit: $scope.contactLimit,
                mailLimit: $scope.mailLimit,
                smsLimit: $scope.smsLimit,
                smstwowayLimit: $scope.smstwowayLimit,
                landingpageLimit: $scope.landingpageLimit,                
                questionLimit: $scope.questionLimit,
                answerLimit: $scope.answerLimit,
                city: $scope.citySelected,
                status: $scope.status,
                services: $scope.services
              };
              console.log(data);
              main.registerSubaccount(idAccount, data, function (res) {
                var route = $window.myBaseURL + "subaccount/createuser/" + res.idSubaccount;
                $window.location.href = route;
              }, function (res) {
                console.log(res);
                slideOnTop(res, 3000, "glyphicon glyphicon-remove-sign", "danger");
                $rootScope.error = 'fail';
              });
            };

            $scope.selectCountry = function (id) {
              if (!id) {
                id = $scope.countrySelected;
                idCountry = "";
                idState = "";
                idCity = "";
                $scope.stateSelected = "";
                $scope.citySelected = "";
              }
              $scope.state = {};
              $scope.cities = {};
              main.state(id, function (res) {
                $scope.state = res;
                if (idState) {
                  $scope.stateSelected = idState;
                  $scope.selectState($scope.stateSelected);
                }
              }, function (res) {
                $rootScope.error = 'fail';
              });
            };
            $scope.selectState = function (id) {
              if (!id) {
                id = $scope.stateSelected;
                idState = "";
                idCity = "";
                $scope.citySelected = "";
              }
              $scope.cities = {};
              main.city(id, function (res) {
                $scope.cities = res;
                if (idCity) {
                  $scope.citySelected = idCity;
                }
              }, function (res) {
                $rootScope.error = 'fail';
              });
            };

            $scope.selectCountryUser = function () {
              $scope.stateUser = {};
              $scope.citiesUser = {};
              main.state($scope.countrySelectedUser, function (res) {
                $scope.stateUser = res;
              }, function (res) {
                $rootScope.error = 'fail';
              });
            };
            $scope.selectStateUser = function () {
              $scope.citiesUser = {};
              main.city($scope.stateSelectedUser, function (res) {
                $scope.citiesUser = res;
              }, function (res) {
                $rootScope.error = 'fail';
              });
            };

          }])
        .controller('ctrlSubaccountEdit', ['$rootScope', '$scope', '$window', '$http', 'main', function ($rootScope, $scope, $window, $http, main) {
            $scope.services = [];
            $scope.mailLimit = contactTotal;
            main.country(function (res) {
              $scope.country = res;
              if (idCountry) {
                $scope.countrySelected = idCountry;
                $scope.selectCountry($scope.countrySelected);
              }
            }, function (res) {
              $rootScope.error = 'fail';
            });

            $scope.showsms = false;
            $scope.showsmstwoway = false;
            $scope.showemail = false;
            $scope.showsurvey = false;
            $scope.showlandingpage = false;
            
            if (smsTotal >= 0) {
              $scope.smsLimit = smsTotal;
            }
            /*if (smstwowayTotal >= 0){
                $scope.smstwowayLimit = smstwowayTotal;
            }*/
            ((contactTotal >= 0) ? $scope.contactTotal = contactTotal : "");
            ((landingpageTotal >= 0) ? $scope.landingpageTotal = landingpageTotal : "");
            ((totalSmsSend >= 0) ? $scope.totalSmsSend = totalSmsSend : "");
            //((totalSmstwowaySend >= 0) ? $scope.totalSmstwowaySend = totalSmstwowaySend : "");
            ((idServices) ? $scope.services = idServices : "");
            ((contactTotalAccount >= 0) ? $scope.contactTotalAccount = contactTotalAccount : "");
            ((smsTotalAccount >= 0) ? $scope.smsTotalAccount = smsTotalAccount + smsTotal : "");
            //((smstwowayTotalAccount >= 0) ? $scope.smstwowayTotalAccount = smstwowayTotalAccount + smstwowayTotal : "");
            ((landingpageTotalAccount >= 0) ? $scope.landingpageTotalAccount = landingpageTotalAccount + landingpageTotal : "");

            if (questionTotal >= 0) {
              $scope.questionTotal = questionTotal;
              $scope.questionLimit = questionTotal;
            }
            ((amountQuestionAccount >= 0) ? $scope.amountQuestionAccount = amountQuestionAccount + questionTotal : "");
            if (answerTotal >= 0) {
              $scope.answerTotal = answerTotal;
              $scope.answerLimit = answerTotal;
            }
            ((amountAnswerAccount >= 0) ? $scope.amountAnswerAccount = amountAnswerAccount + answerTotal : "");

            $scope.selectedServices = function () {
                $scope.showsms = false;
                $scope.showsmstwoway = false;
                $scope.showemail = false;
                $scope.showsurvey = false;
                $scope.showlandingpage = false;
                //console.log($scope.services);
                for (var i = 0; i < $scope.services.length; i++) {
                    if ($scope.services[i] == 1) {
                        $scope.showsms = true;
                    }
                    if ($scope.services[i] == 2) {
                        $scope.showemail = true;
                    }
                    if ($scope.services[i] == 5) {
                        $scope.showsurvey = true;
                    }
                    if ($scope.services[i] == 7) {
                        $scope.showsmstwoway = true;
                    }
                    if ($scope.services[i] == 8) {
                      $scope.showlandingpage = true;
                    }     
              }
            };

            if ($scope.services.length > 0) {
              $scope.selectedServices();
            }

            $scope.fun = function () {
              console.log($scope.name);
            };
            
            $scope.editSubaccount = function (idSubaccount) {
              $scope.disabled = true;
              var data = {
                name: $scope.name,
                description: $scope.description,
                //diskSpace: $scope.fileSpace,
                contactLimit: $scope.contactLimit,
                mailLimit: $scope.mailLimit,
                smsLimit: $scope.smsLimit,
                smstwowayLimit: $scope.smstwowayLimit,
                landingpageLimit: $scope.landingpageLimit,                
                questionLimit: $scope.questionLimit,
                answerLimit: $scope.answerLimit,
                city: $scope.citySelected,
                status: $('#toggle-one').prop('checked'),
                services: $scope.services
              };
              main.editSubaccount(idSubaccount, data, function (res) {
                var route = $window.myBaseURL + "subaccount/index/" + res;
                //console.log(res);
                $window.location.href = route;
                 $scope.disabled = false;
                
              }, function (res) {
                slideOnTop(res, 3000, "glyphicon glyphicon-remove-sign", "danger");
                $rootScope.error = 'fail';
              });
            };

            $scope.selectCountry = function (id) {
              if (!id) {
                id = $scope.countrySelected;
                idCountry = "";
                idState = "";
                idCity = "";
                $scope.stateSelected = "";
                $scope.citySelected = "";
              }
              $scope.state = {};
              $scope.cities = {};
              main.state(id, function (res) {
                $scope.state = res;
                if (idState) {
                  $scope.stateSelected = idState;
                  $scope.selectState($scope.stateSelected);
                }
              }, function (res) {
                $rootScope.error = 'fail';
              });
            };
            $scope.selectState = function (id) {
              if (!id) {
                id = $scope.stateSelected;
                idState = "";
                idCity = "";
                $scope.citySelected = "";
              }
              $scope.cities = {};
              main.city(id, function (res) {
                $scope.cities = res;
                if (idCity) {
                  $scope.citySelected = idCity;
                }
              }, function (res) {
                $rootScope.error = 'fail';
              });
            };
            /*$scope.buttonactive= false;
            $scope.buttonactive= false;

            
            $scope.showbuttons = function(){
              console.log("entre js=",$scope.status);

             if($scope.status ==1){
              $scope.buttoinactive= true;
              console.log("status js=",$scope.status);
            }else{
              $scope.buttonactive= true;

            }
            }; */


          }])


