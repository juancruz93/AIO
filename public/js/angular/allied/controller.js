angular.module('aio', ['ngMaterial'])
        .factory('main', ['$http', '$window', function ($http, $window) {
            return {
              newAllied: function (data, success, error) {
                $http.post($window.myBaseURL + 'masteraccount/aliascreate/' + data.idMasteraccount, data).success(success).error(error);
              },
              country: function (success, error) {
                $http.get($window.myBaseURL + 'country/country').success(success).error(error);
              },
              state: function (data, success, error) {
                $http.get($window.myBaseURL + 'country/state/' + data).success(success).error(error);
              },
              city: function (data, success, error) {
                $http.get($window.myBaseURL + 'country/cities/' + data).success(success).error(error);
              },
              planbycountryallied: function (idMasteraccount, idCounty, success, error) {
                $http.get($window.myBaseURL + 'masteraccount/planbycountryallied/' + idMasteraccount + '/' + idCounty).success(success).error(error);
              },
              categories: function (success, error) {
                $http.get($window.myBaseURL + 'api/accountcategory/getaccountcategories').success(success).error(error);
              },
              getservicesallied: function (idAllied, success, error) {
                $http.get($window.myBaseURL + 'masteraccount/getservicesallied/' + idAllied).success(success).error(error);
              },
              rechargeAllied: function (data, success, error) {
                $http.post($window.myBaseURL + 'masteraccount/rechargeallied', data).success(success).error(error);
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
        .controller('ctrlAllied', ['$rootScope', '$scope', '$http', 'main', '$window', '$interval', function ($rootScope, $scope, $http, main, $window, $interval) {
            $scope.status = true;
            $scope.showDetail = false;
            main.country(function (res) {
              $scope.country = res;
              if ($scope.idCountry) {
                $scope.countrySelected = $scope.idCountry;
                $scope.selectCountry($scope.countrySelected);
              }
            }, function (res) {
              $rootScope.error = 'fail';
            });

            main.categories(function (res) {
              $scope.categories = res;
            }, function (res) {
              $rootScope.error = 'fail';
            });

            $scope.selectCountry = function (id) {
              if (!id) {
                id = $scope.countrySelected;
                $scope.idCountry = "";
                $scope.idState = "";
                $scope.idCity = "";
                $scope.stateSelected = "";
                $scope.citySelected = "";
              }
              $scope.showDetail = false;
              $scope.state = {};
              $scope.cities = {};
              $scope.paymentPlanSelected = "";
              main.state(id, function (res) {
                $scope.state = res;
                if ($scope.idState) {
                  $scope.stateSelected = $scope.idState;
                  $scope.selectState($scope.stateSelected);
                }
              }, function (res) {
                $rootScope.error = 'fail';
              });

              main.planbycountryallied(idMasteraccount, id, function (res) {
                $scope.paymentPlan = res;
              }, function (res) {
                $rootScope.error = 'fail';
              });
            };

            $scope.calculeDiskSpace = function () {
              $scope.totalspace = diskSpaceMaster - $scope.plan.diskSpace;
            };

            $scope.descriptionPlan = function () {
              $scope.plan = jQuery.parseJSON($scope.paymentPlanSelected);
              $scope.showDetail = true;
            };

            $scope.selectState = function (id) {
              if (!id) {
                id = $scope.stateSelected;
                $scope.idState = "";
                $scope.idCity = "";
                $scope.citySelected = "";
              }
              $scope.cities = {};
              main.city(id, function (res) {
                $scope.cities = res;
                if ($scope.idCity) {
                  $scope.citySelected = $scope.idCity;
                }
              }, function (res) {
                $rootScope.error = 'fail';
              });
            };

            $scope.createAllied = function (idMasteraccount) {
              var plan = jQuery.parseJSON($scope.paymentPlanSelected);
              var data = {
                idCity: $scope.citySelected,
                name: $scope.name,
                nit: $scope.nit,
                address: $scope.address,
                phone: $scope.phone,
                email: $scope.email,
                zipcode: $scope.zipcode,
                status: $scope.status,
                idMasteraccount: idMasteraccount,
                idPaymentPlan: plan.idPaymentPlan,
                idAccountCategory: $scope.idAccountCategory
              };
              main.newAllied(data, function (res) {
                var route = $window.myBaseURL + "allied/createuser/" + res;
                $window.location.href = route;
              }, function (res) {
                slideOnTop(res[0], 3000, "glyphicon glyphicon-remove-sign", "danger");
                $rootScope.error = 'fail';
              });
            };

            $scope.space = function () {
              if (isFinite($scope.fileSpace) && $scope.fileSpace > 0) {
                $scope.ss = $scope.spaceTotal - $scope.fileSpace;
                if ($scope.fileSpace > $scope.spaceTotal) {
                  $scope.fileSpace = $scope.spaceTotal;
                  $scope.ss = 0;
                }
              } else {
                $scope.ss = $scope.spaceTotal;
              }
            };

            $scope.spaceEdit = function (space) {
              var i = (parseInt(space) + parseInt($scope.spaceTotal));
              if (isFinite($scope.fileSpace) && $scope.fileSpace > 0 && $scope.fileSpace > space) {
                $scope.ss = $scope.spaceTotal - ($scope.fileSpace - space);
                if ($scope.fileSpace > i) {
                  $scope.fileSpace = i;
                  $scope.ss = 0;
                }
              } else {
                $scope.ss = $scope.spaceTotal;
              }
            };

            $scope.mailL = function () {
              if (isFinite($scope.mailLimit) && $scope.mailLimit > 0) {
                $scope.mail = $scope.mailTotal - $scope.mailLimit;
                if ($scope.mailLimit > $scope.mailTotal) {
                  $scope.mailLimit = $scope.mailTotal;
                  $scope.mail = 0;
                }
              } else {
                $scope.mail = $scope.mailTotal;
              }
            };

            $scope.mailLEdit = function (mail) {
              var i = (parseInt(mail) + parseInt($scope.mailTotal));
              if (isFinite($scope.mailLimit) && $scope.mailLimit > 0 && $scope.mailLimit > mail) {
                $scope.mail = $scope.mailTotal - ($scope.mailLimit - mail);
                if ($scope.mailLimit > i) {
                  $scope.mailLimit = i;
                  $scope.mail = 0;
                }
              } else {
                $scope.mail = $scope.mailTotal;
              }
            };

            $scope.contactL = function () {
              if (isFinite($scope.contactLimit) && $scope.contactLimit > 0) {
                $scope.contact = $scope.contactTotal - $scope.contactLimit;
                if ($scope.contactLimit > $scope.contactTotal) {
                  $scope.contactLimit = $scope.contactTotal;
                  $scope.contact = 0;
                }
              } else {
                $scope.contact = $scope.contactTotal;
              }
            };

            $scope.contactLEdit = function (contact) {
              var i = (parseInt(contact) + parseInt($scope.contactTotal));
              if (isFinite($scope.contactLimit) && $scope.contactLimit > 0 && $scope.contactLimit > contact) {
                $scope.contact = $scope.contactTotal - ($scope.contactLimit - contact);
                if ($scope.contactLimit > i) {
                  $scope.contactLimit = i;
                  $scope.contact = 0;
                }
              } else {
                $scope.contact = $scope.contactTotal;
              }
            };

            $scope.smsL = function () {
              if (isFinite($scope.smsLimit) && $scope.smsLimit > 0) {
                $scope.sms = $scope.smsTotal - $scope.smsLimit;
                if ($scope.smsLimit > $scope.smsTotal) {
                  $scope.smsLimit = $scope.smsTotal;
                  $scope.sms = 0;
                }
              } else {
                $scope.sms = $scope.smsTotal;
              }
            };

            $scope.smsLEdit = function (sms) {
              var i = (parseInt(sms) + parseInt($scope.smsTotal));
              if (isFinite($scope.smsLimit) && $scope.smsLimit > 0 && $scope.smsLimit > sms) {
                $scope.sms = $scope.smsTotal - ($scope.smsLimit - sms);
                if ($scope.smsLimit > i) {
                  $scope.smsLimit = i;
                  $scope.sms = 0;
                }
              } else {
                $scope.sms = $scope.smsTotal;
              }
            };

            $scope.smsVe = function () {
              if (isFinite($scope.smsLimit)) {
                $scope.smsV = $scope.smsVTotal - $scope.smsVelocity;
              }
            };

            $scope.smstwowayL = function () {
              if (isFinite($scope.smstwowayLimit) && $scope.smstwowayLimit > 0) {
                $scope.smstwoway = $scope.smstwowayTotal - $scope.smstwowayLimit;
                if ($scope.smstwowayLimit > $scope.smstwowayTotal) {
                  $scope.smstwowayLimit = $scope.smstwowayTotal;
                  $scope.smstwoway = 0;
                }
              } else {
                $scope.smstwoway = $scope.smstwowayTotal;
              }
            };

            $scope.smstwowayLEdit = function (smstwoway) {
              var i = (parseInt(smstwoway) + parseInt($scope.smstwowayTotal));
              if (isFinite($scope.smstwowayLimit) && $scope.smstwowayLimit > 0 && $scope.smstwowayLimit > smstwoway) {
                $scope.smstwoway = $scope.smstwowayTotal - ($scope.smstwowayLimit - smstwoway);
                if ($scope.smstwowayLimit > i) {
                  $scope.smstwowayLimit = i;
                  $scope.smstwoway = 0;
                }
              } else {
                $scope.sms = $scope.smsTotal;
              }
            };

            $scope.selectCountryUser = function (id) {
              if (!id) {
                id = $scope.countrySelectedUser
              }
              $scope.state = {};
              $scope.cities = {};
              main.state(id, function (res) {
                $scope.stateUser = res;
              }, function (res) {
                $rootScope.error = 'fail';
              });
            };
            $scope.selectStateUser = function (id) {
              if (!id) {
                id = $scope.stateSelectedUser
              }
              $scope.cities = {};
              main.city(id, function (res) {
                //console.log(res);
                $scope.citiesUser = res;
              }, function (res) {
                $rootScope.error = 'fail';
              });
            };

          }])
        .controller('ctrlAlliedList', ['$rootScope', '$scope', '$http', 'main', '$window', '$interval', 'notificationService', function ($rootScope, $scope, $http, main, $window, $interval, notificationService) {
            $scope.showsms = false;
            $scope.showemail = false;
            $scope.showsmstwoway = false;
            $scope.showlandingpage = false;

            ((contactTotalMaster >= 0) ? $scope.contactTotalMaster = contactTotalMaster : "");
            ((smsTotalMaster >= 0) ? $scope.smsTotalMaster = smsTotalMaster : "");
            ((smstwowayTotalMaster >= 0) ? $scope.smstwowayTotalMaster = smstwowayTotalMaster : "");
            ((accountingModeMaster >= 0) ? $scope.accountingModeMaster = accountingModeMaster : "");
            ((landingpageTotalMaster >= 0) ? $scope.landingpageTotalMaster = landingpageTotalMaster : "");

            $scope.rechargeService = function (idAllied) {
              $scope.idAllied = idAllied;
              $scope.showsms = false;
              $scope.showsmstwoway = false;
              $scope.showlandingpage = false;
              $scope.showemail = false;
              
              $scope.smsLimit = '';
              $scope.smstwowayLimit = '';
              $scope.landingpageLimit = '';
              $scope.mailLimit = '';
              $scope.services = [];
              main.getservicesallied(idAllied, function (res) {
                $scope.result = res;
                $scope.limitSmsAccount = res.limitSmsAccount;
                $scope.limitSmstwowayAccount = res.limitSmstwowayAccount;
                $scope.limitContactAccount = res.limitContactAccount;
                $scope.limitLandingpageAccount = res.limitLandingpageAccount;
                //console.log(res);
              }, function (error) {
                $rootScope.error = 'fail';
              });

              $("#modalRecharge").modal('show');
            };

            $scope.selectedServices = function () {
              $scope.showsms = false;
              $scope.showemail = false;
              $scope.showsmstwoway = false;
              $scope.showlandingpage = false;
              for (var i = 0; i < $scope.services.length; i++) {
                if ($scope.services[i] == 1) {
                  $scope.showsms = true;
                }
                if ($scope.services[i] == 2) {
                  $scope.showemail = true;
                }
                if ($scope.services[i] == 7) {
                  $scope.showsmstwoway = true;
                }
                if ($scope.services[i] == 8) {
                  $scope.showlandingpage = true;
                }
              }
            };

            $scope.rechargeApply = function () {
              var data = {
                smsLimit: $scope.smsLimit,
                smstwowayLimit: $scope.smstwowayLimit,
                landingpageLimit: $scope.landingpageLimit,
                mailLimit: $scope.mailLimit,
                idAllied: $scope.idAllied,
                services: $scope.services
              };

              main.rechargeAllied(data, function (res) {
                $scope.smsTotalMaster = $scope.smsTotalMaster - $scope.smsLimit;
                $scope.smstwowayTotalMaster = $scope.smstwowayTotalMaster - $scope.smstwowayLimit;
                $scope.landingpageTotalMaster = $scope.landingpageTotalMaster - $scope.landingpageLimit;
                $scope.contactTotalMaster = $scope.contactTotalMaster - $scope.mailLimit;
                notificationService.success(res[0]);
              }, function (error) {
                $rootScope.error = 'fail';
              });

              $("#modalRecharge").modal('hide');
            };

          }])
