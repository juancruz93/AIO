angular.module('aio', ['platanus.keepValues', 'ngMaterial', 'ngRoute', 'ui.router'])
        .factory('main', ['$http', '$window', function ($http, $window) {
            return {
              registerAccount: function (data, success, error) {
                $http.post('create', data).success(success).error(error);
              },
              editAccount: function (data, idAccount, success, error) {
                var route = $window.myBaseURL + "account/edit/";
                $http.post(route + idAccount, data).success(success).error(error);
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
              planbycountryaccount: function (idAllied, idCounty, success, error) {
                $http.get($window.myBaseURL + 'account/planbycountryaccount/' + idAllied + '/' + idCounty).success(success).error(error);
              },
              getfooters: function (success, error) {
                $http.get($window.myBaseURL + 'account/getfooters/').success(success).error(error);
              },
              userCreate: function (idAccount, data, success, error) {
                $http.post($window.myBaseURL + 'account/usercreate/' + idAccount, data).success(success).error(error);
              },
              categories: function (success, error) {
                $http.get($window.myBaseURL + 'api/accountcategory/getaccountcategories').success(success).error(error);
              },
              habeasData: function (success, idAccount, error){
                $http.post($window.myBaseURL + '/api/accounting/gethabeasdata' + idAccount).success(success).error(error);
              },
              getAll: function (page, name, accountRegisterType, status, success, error) {
                
                var data = [page, name, accountRegisterType, status];
                $http.post($window.myBaseURL + 'account/index', data).success(success).error(error);
                
              },
              downloadexcelaccounts: function(accountRegisterType, status, success, error){
                var data = [accountRegisterType, status];
                $http.post($window.myBaseURL + 'account/downloadexcelaccounts', data).success(success).error(error);
              },
              getservicesaccount: function (idAccount, success, error) {
                $http.get($window.myBaseURL + 'account/getservicesaccount/' + idAccount).success(success).error(error);
              },
              rechargeAccount: function (data, success, error) {
                $http.post($window.myBaseURL + 'account/rechargeaccount', data).success(success).error(error);
              },
              mta : function (data, success, error) {
                $http.post($window.myBaseURL + 'mta/getallmta', data).success(success).error(error);
              },
              getMta : function (data, success, error){
                $http.post($window.myBaseURL + 'mta/getidmtadcxmta/', data).success(success).error(error);
              },
              getrechargeservices: function (idServices, success, error) {
                $http.get($window.myBaseURL + 'account/rechargeservices/' + idServices).success(success).error(error);
              },
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
        .controller('ctrlAccount', ['$rootScope', '$scope', '$http', 'main', '$window', 'notificationService', '$routeParams', '$location', function ($rootScope, $scope, $http, main, $window, notificationService, $routeParams, $location) {
            var arrayParams = $location.absUrl().split('/');
            var lastposition = arrayParams[arrayParams.length-1];
            
            if(parseInt(lastposition)){
              $scope.namePage = 'edit';
            }
            
            $scope.createUser = function (idAccount) {
              main.userCreate(idAccount, $scope.data, function (res) {
                var route = $window.myBaseURL + "account/userlist/" + res.idAccount;
                $window.location.href = route;
              }, function (err) {
                notificationService.error(err[0]);
                $rootScope.error = 'fail';
              });
            };
            $scope.hourInit = (typeof hourInit == "undefined")? 7 : hourInit;
            $scope.hourEnd = (typeof hourEnd == "undefined")? 18 : hourEnd;
            $scope.rest = function (init){
              if(init){
                $scope.hourInit--;
                $scope.changeHour($scope.hourInit,'hourInit');
              }else{
                $scope.hourEnd--;
                $scope.changeHour($scope.hourEnd,'hourEnd');
              }
            }
            $scope.sum = function (init){
              if(init){
                $scope.hourInit++;
                $scope.changeHour($scope.hourInit,'hourInit');
              }else{
                $scope.hourEnd++;
                $scope.changeHour($scope.hourEnd,'hourEnd');
              }
            }
            $scope.changeHour = function(model,key){
              if(model>24){
                $scope[key] = 24;
              }
              if(model<1){
                $scope[key] = 1;
              }
            }
            $scope.status = true;
            $scope.showDetail = false;
            $scope.expirationDate = 0;
            $scope.showMta = false;
            //$scope.attachments = false;

            if (typeof (category) != 'undefined') {
              $scope.idAccountCategory = category;
              var cate = jQuery.parseJSON(category);
              $scope.expirationDate = cate.expirationDate;
            }

            if (typeof (paymentPlan) != 'undefined') {
              $scope.paymentBefore = jQuery.parseJSON(paymentPlan);
              $scope.payment = jQuery.parseJSON(paymentPlan);
              $scope.idPaymentPlan = $scope.payment.idPaymentPlan;
            }

            if (typeof (idFooter) != 'undefined') {
              $scope.idFooter = idFooter;
            }

            if (typeof (footerEditable) != 'undefined') {
              $scope.footerEditable = footerEditable;
            }

            if (typeof (senderAllowed) != 'undefined') {
              $scope.senderAllowed = senderAllowed;
            }

            if (typeof (expiryDate) != 'undefined') {
              $scope.expiryDate = new Date(expiryDate);
            }

            if (typeof (status) != 'undefined') {
              $scope.status = status == 1;
            }

            /*if (typeof (attachments) != 'undefined') {
             $scope.attachments = attachments == 1;
             }*/
            
            if (typeof (idCountry) != 'undefined') {
              $scope.idCountry = idCountry;
            }
            
            if (typeof (idState) != 'undefined') {
              $scope.idState = idState;
            }
            
            if (typeof (idCity) != 'undefined') {
              $scope.idCity = idCity;
            }

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

            main.getfooters(function (res) {
              $scope.footers = res;
            }, function (res) {
              $rootScope.error = 'fail';
            });

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

            $scope.selectCountry = function (id) {
              if (!id) {
                id = $scope.countrySelected;
                $scope.idCountry = "";
                $scope.idState = "";
                $scope.idCity = "";
                $scope.stateSelected = "";
               $scope.citySelected = "";
              }
              $scope.state = {};
              $scope.cities = {};
             $scope.showDetail = false;
              if (typeof (paymentPlan) != 'undefined') {
                var jsonPaymentPlan = jQuery.parseJSON(paymentPlan);
//                $scope.paymentPlanSelected = paymentPlan;
                $scope.paymentPlanSelected = jsonPaymentPlan.idPaymentPlan;
             } else {
                $scope.paymentPlanSelected = '';
              }
              main.state(id, function (res) {
                $scope.state = res;
                if (idState) {
//                  $scope.stateSelected = idState;
                  $scope.stateSelected = $scope.idState;
                  $scope.selectState($scope.stateSelected);
                }
              }, function (res) {
                $rootScope.error = 'fail';
              });

              main.planbycountryaccount(idAllied, id, function (res) {
                $scope.paymentPlan = res;
                if($scope.idPaymentPlan){
                  $scope.paymentPlanSelected = $scope.idPaymentPlan;
                  //$scope.descriptionPlan($scope.stateSelected);
                  $scope.selectPlanSelected($scope.paymentPlanSelected);
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
//                  $scope.citySelected = idCity;
                  $scope.citySelected = $scope.idCity;
                }
              }, function (res) {
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
                $scope.fileSpace = 0;
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
                $scope.mailLimit = 0;
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
                $scope.contactLimit = 0;
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
                $scope.smsLimit = 0;
                $scope.sms = $scope.smsTotal;
              }
            };
            
            $scope.smstwowayL = function () {
              if (isFinite($scope.smstwowayLimit) && $scope.smstwowayLimit > 0) {
                $scope.smstwoway = $scope.smstwowayTotal - $scope.smstwowayLimit;
                if ($scope.smstwowayLimit > $scope.smstwowayTotal) {
                  $scope.smstwowayLimit = $scope.$scope.smstwowayTotal;
                  $scope.smstwoway = 0;
                }
              } else {
                $scope.smstwowayLimit = 0;
                $scope.smstwoway = $scope.smstwowayTotal;
              }
            };
            
            $scope.landingpageL = function () {
              if (isFinite($scope.landingpageLimit) && $scope.landingpageLimit > 0) {
                $scope.landingpage = $scope.landingpageTotal - $scope.landingpageLimit;
                if ($scope.landingpageLimit > $scope.smstwowayTotal) {
                  $scope.landingpageLimit = $scope.$scope.smstwowayTotal;
                  $scope.landingpage = 0;
                }
              } else {
                $scope.landingpageLimit = 0;
                $scope.landingpage = $scope.landingpageTotal;
              }
            };
            
            
            $scope.calculeDiskSpace = function () {
              $scope.totalspace = diskSpaceAllied - $scope.plan.diskSpace;
            };
            
            //For create users of the account
            if(!(typeof diskSpaceAllied) == 'undefined'){
              if($scope.totalspace==null||$scope.totalspace==''){
                $scope.totalspace = diskSpaceAllied;
              }
            }

            $scope.newAccount = function () {
              var plan = jQuery.parseJSON($scope.paymentPlanSelected);
              var category = jQuery.parseJSON($scope.idAccountCategory);
              if ($scope.expirationDate == 1 && $scope.expiryDate == null) {
                notificationService.error('El campo fecha de expiración es obligatorio');
                return;
              } else if ($scope.expirationDate == 0) {
                $scope.expiryDate = null;
              }
              var data = {
                /*datos de occount*/
                name: $scope.name,
                phone: $scope.phone,
                address: $scope.address,
                email: $scope.email,
                nit: $scope.nit,
                city: $scope.citySelected,
                status: $scope.status,
                idPaymentPlan: plan.idPaymentPlan,
                senderAllowed: $scope.senderAllowed,
                idFooter: $scope.idFooter,
                footerEditable: $scope.footerEditable,
                expiryDate: $scope.expiryDate,
                tolerance: $scope.tolerance,
                idAccountCategory: category.idAccountCategory,
                //attachments: $scope.attachments,
                url: $scope.urlWebSite,
                hourInit: $scope.hourInit,
                hourEnd: $scope.hourEnd,
                habeasData: $scope.habeasdata,
                mta: $scope.mtaSelected,
                showMta: $scope.showMta
              };

              main.registerAccount(data, function (res) {
                var route = $window.myBaseURL + "account/usercreate/" + res.idAccount;
                $window.location.href = route;
              }, function (res) {
                slideOnTop(res[0], 3000, "glyphicon glyphicon-remove-sign", "danger");
                $rootScope.error = 'fail';
              });
            };
                        
            /*$scope.selectPlanSelected = function (id) {
              $scope.showMta = false;
              if (!id) {
                id = $scope.paymentPlanSelected;
                angular.forEach($scope.paymentPlan, function (value, key) {
                  if (value.idPaymentPlan == $scope.paymentPlanSelected) {
                    angular.forEach(value.planxservice, function (obj, key) {
                      if (obj.service == "Email Marketing") {
                        $scope.showMta = true;
                      }
                    })
                    $scope.payment = value;
                  }
                });
                $scope.plan = $scope.payment;
                $scope.showDetail = true;
                $scope.idPaymentPlan = "";
              } else {
                angular.forEach($scope.payment.planxservice, function (obj, key) {
                  if (obj.service == "Email Marketing") {
                    $scope.showMta = true;
                  }
                });
              }
            }*/
            
            $scope.selectPlanSelected = function (id) {
              $scope.showMta = false;
              if (!id) {
                id = $scope.paymentPlanSelected;
              }
              angular.forEach($scope.paymentPlan, function (value, key) {
                if (value.idPaymentPlan == $scope.paymentPlanSelected) {
                  angular.forEach(value.planxservice, function (obj, key) {
                    if (obj.service == "Email Marketing") {
                      $scope.showMta = true;
                    }
                  })
                  $scope.payment = value;
                }
              });
              $scope.plan = $scope.payment;
              $scope.showDetail = true;
              $scope.idPaymentPlan = "";
            }
            
            /*
             * For the view Create
             * @return {undefined}
             */
            $scope.descriptionPlan = function () {
              $scope.showMta = false;
              $scope.plan = jQuery.parseJSON($scope.paymentPlanSelected);
              $scope.showDetail = true;
              
              angular.forEach($scope.plan.planxservice, function (value, key) {
                if(value.service=="Email Marketing"){
                  $scope.showMta = true;
                }
              });
            };

            $scope.showExpiryDate = function () {
              var category = jQuery.parseJSON($scope.idAccountCategory);
              $scope.expirationDate = category.expirationDate;
            };

            $scope.editAccount = function (idAccount, validateConfirm) {

              var plan = jQuery.parseJSON($scope.paymentPlanSelected);
              var category = jQuery.parseJSON($scope.idAccountCategory);
              if ($scope.expirationDate == 1 && $scope.expiryDate == null) {
                notificationService.error('El campo fecha de expiración es obligatorio');
                return;
              } else if ($scope.expirationDate == 0) {
                $scope.expiryDate = null;
              }
              var data = {
                /*datos de account*/
                name: $scope.name,
                phone: $scope.phone,
                address: $scope.address,
                email: $scope.email,
                nit: $scope.nit,
//                idPaymentPlan: plan.idPaymentPlan,
                idPaymentPlan: $scope.paymentPlanSelected,
                city: $scope.citySelected,
                status: $scope.status,
                senderAllowed: $scope.senderAllowed,
                idFooter: $scope.idFooter,
                footerEditable: $scope.footerEditable,
                expiryDate: $scope.expiryDate,
                tolerancePeriod: $scope.tolerancePeriod,
                idAccountCategory: category.idAccountCategory,
                //attachments: $scope.attachments,
                validateConfirm: validateConfirm,
                url: $scope.urlWebSite,
                hourInit: $scope.hourInit,
                hourEnd: $scope.hourEnd,
                habeasData: $scope.habeasdata,
                mta: $scope.mtaSelected,
                showMta: $scope.showMta
              };

              if (validateConfirm == true) {
                $scope.validateConfirm = true;
              }

              main.editAccount(data, idAccount, function (res) {
                var route = $window.myBaseURL + "account/index";
                $window.location.href = route;
              }, function (res) {
                if (res.code == 403) {
                  slideOnTop(res[0], 3000, "glyphicon glyphicon-remove-sign", "danger");
                  $scope.validateConfirm = false;
                  $rootScope.error = 'fail';
                } else if (res.code == 409) {
                  $scope.errorEditAccount = res.message;
                  openModalConfirm();
                }
              });
            }
           
            main.mta($scope.data, function (res) {
              $scope.mta = res;
              if ($scope.idMta) {
                $scope.mtaSelected = $scope.idMta;
                $scope.selectMta($scope.mtaSelected);
              }
            }, function (err) {
              notificationService.error(err[0]);
              $rootScope.error = 'fail';
            });
              
            $scope.selectMta = function (id) {
              if (!id) {
                id = $scope.mtaSelected;
                $scope.idMta = "0";
              }
            }
            
            if ($scope.namePage == 'edit') {
              var idAccount = lastposition;
              main.getMta(idAccount, function (res) {
                if(res!=null&&res!=''){
                  $scope.idMta = res[0]['idMta'];
                  $scope.namePage = '';
                }
                else{
                  $scope.idMta = '0';
                  $scope.namePage = '';
                }
                $scope.mtaSelected = $scope.idMta;
                $scope.selectMta($scope.mtaSelected);
                /*$scope.stateSelected = $scope.idState;
                $scope.selectState($scope.stateSelected);*/
              }, function (res) {
                $rootScope.error = 'fail';
              });
            }
            
          }])
        .controller('ctrlAccountlist', ['$scope', '$http', 'main', '$q', 'notificationService', function ($scope, $http, main, $q, notificationService) {
            $scope.initial = 0;
            $scope.page = 1;
            $scope.idAccount;
            $scope.name = "";
            $scope.accountRegisterType;
            $scope.status;

            $scope.getAll = function (name) {
              if (name) {
                $scope.name = name;
              }
              main.getAll($scope.initial, $scope.name, $scope.accountRegisterType, $scope.status, function (res) {
                $scope.accounts = res.accounts;
                $scope.configAllied = res.configAllied;

              }, function (res) {
                $rootScope.error = 'fail';
              });
            };
            //FUNCION PARA TAER LOS REGISTROS POR TIPO DE ORIGEN
            $scope.typeFunc = function () {
              if ($scope.accountRegisterType.length > 0) {
                $scope.initial = 0;
                $scope.getAll();
              }
            };
            //FUNCION PARA TRAER LOS REGISTROS POR ESTADO DE LA CUENTA
            $scope.statusFunc = function () {
              if ($scope.status.length > 0) {
                $scope.initial = 0;
                $scope.getAll();
              }
            };
            //FUNCION PARA DESCARGAR LISTADO DE CUENTAS
            $scope.downloadReport = function(){
              main.downloadexcelaccounts($scope.accountRegisterType, $scope.status, function (res) {
                var url = fullUrlBase + 'account/downloadexcel/'+res["title"];
                location.href = url;
              }, function (res) {
                $rootScope.error = 'fail';
              });
            };

            ((contactTotalAllied >= 0) ? $scope.contactTotalAllied = contactTotalAllied : "");
            ((smsTotalAllied >= 0) ? $scope.smsTotalAllied = smsTotalAllied : "");
            ((smstwowayTotalAllied >= 0) ? $scope.smstwowayTotalAllied = smstwowayTotalAllied : "");
            ((landingpageTotalAllied >= 0) ? $scope.landingpageTotalAllied = landingpageTotalAllied : "");            
            ((accountingModeAllied >= 0) ? $scope.accountingModeAllied = accountingModeAllied : "");

            $scope.restartPagination = function () {
              $scope.initial = 0;
              $scope.page = 1;
            };

            $scope.listMailTemplateAccount = function (data) {
              $scope.idAccount = data;
              $http.get(fullUrlBase + "api/mailtemplate/listmailtempbyacco/" + data + "/" + $scope.initial)
                      .success(function (data) {
                        $scope.list = data;
                      })
                      .error(function (data) {
                        console.log(data);
                        //notificationService.error(data.message);
                      });
              $("#myModal").modal('show');
            };

            $scope.rechargeService = function (idAccount) {
              $scope.idAccount = idAccount;
              $scope.showsms = false;
              $scope.showsmstwoway = false;
              $scope.showlandingpage = false;
              $scope.showemail = false;
              $scope.smsLimit = '';
              $scope.smstwowayLimit = '';
              $scope.mailLimit = '';
              $scope.services = [];
              main.getservicesaccount(idAccount, function (res) {
                $scope.result = res;
                $scope.limitSmsAccount = res.limitSmsAccount;
                $scope.limitSmstwowayAccount = res.limitSmstwowayAccount;
                $scope.limitContactAccount = res.limitContactAccount;
                $scope.limitLandingpageAccount = res.limitLandingpageAccount;
              }, function (error) {
                $rootScope.error = 'fail';
              });

              $("#modalRecharge").modal('show');
            };

            $scope.showsms = false;
            $scope.showsmstwoway = false;
            $scope.showlandingpage = false;
            $scope.showemail = false;

            $scope.selectedServices = function () {
              $scope.showsms = false;
              $scope.showsmstwoway = false;
              $scope.showlandingpage = false;              
              $scope.showemail = false;
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
                idAccount: $scope.idAccount,
                services: $scope.services
              };

              main.rechargeAccount(data, function (res) {
                $scope.smsTotalAllied = $scope.smsTotalAllied - $scope.smsLimit;
                $scope.smstwowayTotalAllied = $scope.smstwowayTotalAllied - $scope.smstwowayLimit;
                $scope.landingpageTotalAllied = $scope.landingpageTotalAllied - $scope.landingpageLimit;
                $scope.contactTotalAllied = $scope.contactTotalAllied - $scope.mailLimit;
                notificationService.success(res[0]);
              }, function (error) {
                $rootScope.error = 'fail';
              });

              $("#modalRecharge").modal('hide');
            };

            $scope.forward = function () {
              $scope.initial += 1;
              $scope.page += 1;
              $scope.getAll();
            };
            $scope.fastforward = function () {
              $scope.initial = ($scope.accounts.total_pages - 1);
              $scope.page = $scope.accounts.total_pages;
              $scope.getAll();
            };
            $scope.backward = function () {
              $scope.initial -= 1;
              $scope.page -= 1;
              $scope.getAll();
            };
            $scope.fastbackward = function () {
              $scope.initial = 0;
              $scope.page = 1;
              $scope.getAll();
            };

            $scope.getAll();
          }])
        .controller('ctrlRecharges', ['$scope', '$http', 'main', '$q', 'notificationService', function ($scope, $http, main, $q, notificationService) {
          $scope.account = {};
          $scope.history = [];
          $scope.listRangesprices = [];
          $scope.quantity = null;
          $scope.objectRangePrices = false;
          $scope.is_services = '';

          $scope.data = "";

          $scope.rechargeService = function (idServices) {
            main.getrechargeservices(idServices, function (res) {
              $scope.account = res.account;
              $scope.history = res.history;
              $scope.services = res.services;
              $scope.is_services = res.services.idServices == 1 ? true : false;
              $scope.listRangesprices = res.rangesprices;
            }, function (error) {
              $rootScope.error = 'fail';
            });
          };
          $scope.rechargeService(idServices);

          $scope.ngChange = function (value) {
            recharges = value;
            if (value != null) {
              idRangesPrices = value.idRangesPrices;
              $scope.objectRangePrices = true;
            } else {
              $scope.objectRangePrices = false;
            }
          };

        }]);