angular.module('aio', ['platanus.keepValues', 'ui.select', 'ngSanitize', 'ngMaterial', 'ngAnimate', 'moment-picker', 'angularMoment'])
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
        .filter('split', function () {
          return function (input, splitChar) {
            // do some bounds checking here to ensure it has that index
            return input.split(splitChar);
          }
        })
        .directive('statusSms', function ($compile) {
          return{
            scope: {statusSms: '@'},
            restrict: 'A',
            link: function (scope, element, attr) {
              var template = "Estado: <span class='{{classStatus}}'>{{tralateSms}}<span>";
              scope.chaneStatus = function () {
                switch (scope.statusSms) {
                  case 'draft':
                    scope.classStatus = 'color-draft';
                    scope.tralateSms = "Borrador";
                    break;
                  case 'scheduled':
                    scope.classStatus = 'color-scheduled';
                    scope.tralateSms = "Programado";
                    break;
                  case 'pending':
                    scope.classStatus = 'color-pending';
                    scope.tralateSms = "Pendiente";
                    break;
                  case 'sending':
                    scope.classStatus = 'color-sending';
                    scope.tralateSms = "En proceso de envío";
                    break;
                  case 'sent':
                    scope.classStatus = 'color-sent';
                    scope.tralateSms = "Enviado";
                    break;
                  case 'paused':
                    scope.classStatus = 'color-paused';
                    scope.tralateSms = "Pausado";
                    break;
                  case 'canceled':
                    scope.classStatus = 'color-canceled';
                    scope.tralateSms = "Cancelado";
                    break;
                  case 'undelivered':
                    scope.classStatus = 'color-canceled';
                    scope.tralateSms = "No enviado";
                    break;
                  default:
                    break;
                }
              }
              scope.$watch('statusSms', function (v) {
                scope.chaneStatus();
                element.html(template);
                $compile(element.contents())(scope);
              });
              scope.chaneStatus();
              element.html(template);
              $compile(element.contents())(scope);
            }
          }
        })
        .directive('typeSms', function ($compile) {
          return{
            restrict: 'A',
            link: function (scope, element, attr) {
              var template = "Tipo: {{statusType}}";
              switch (attr.typeSms) {
                case 'contact':
                  scope.statusType = "Contacto";
                  break;
                case 'csv':
                  scope.statusType = "Csv";
                  break;
                case 'lote':
                  scope.statusType = "Envío rapido";
                  break;
                case 'automatic':
                  scope.statusType = "Contacto";
                  break;    
                default:
                  scope.statusType = "Envío rapido";
                  break;
              }

              element.html(template);
              $compile(element.contents())(scope);
            }
          }
        })
        .factory('socket', function ($rootScope) {
          return {
            on: function (eventName, callback) {
              socket.on(eventName, function () {
                var args = arguments;
                $rootScope.$apply(function () {
                  callback.apply(socket, args);
                });
              });
            },
            emit: function (eventName, data, callback) {
              socket.emit(eventName, data, function () {
                var args = arguments;
                $rootScope.$apply(function () {
                  if (callback) {
                    callback.apply(socket, args);
                  }
                });
              })
            }
          }
        })
        .factory('main', ['$http', '$window', '$q', 'notificationService', function ($http, $window, $q, notificationService) {
            return {
              getServiceVerified: function () {
                var route = fullUrlBase + '/api/sms/verifysmstwowayservice';
                var defer = $q.defer();
                $http.get(route)
                        .success(function (data) {
                          defer.resolve(data);
                        })
                        .error(function (data) {
                          defer.resolve(data);
                        });
                return defer.promise;
              },
              createLote: function (data, success, error) {
                $http.post('createlote', data).success(success).error(error);
              },
              editLote: function (data, success, error) {
                var route = $window.myBaseURL + "sms/edit/";
                $http.post(route + data.idSms, data).success(success).error(error);
              },
              getlisttimezone: function (success, error) {
                var route = $window.myBaseURL + "mail/timezone/";
                $http.post(route).success(success).error(error);
              },
              getcontactlist: function (success, error) {
                var route = $window.myBaseURL + "api/sendmail/getcontactlist";
                return $http.get(route);
              },
              getsegments: function () {
                var route = $window.myBaseURL + "api/sendmail/getsegment";
                return $http.get(route);
              },
              createContact: function (data) {
                return $http.post('createcontact', data);
              },
              validatesmscontact: function (idSms) {
                var route = $window.myBaseURL + "sms/validatecontact/" + idSms;
                return $http.post(route, idSms);

              },
              countContact: function (data) {
                var route = $window.myBaseURL + "api/sms/countcontact";
                return $http.post(route, data);
              },
              editContact: function (data) {
                var route = $window.myBaseURL + "sms/editcontact/" + data.idSms;
//                console.log("error,",data);
//                return $http.post(route, data);
                var defer = $q.defer();
                $http.post(route, data)
                        .success(function (data) {
                          defer.resolve(data);
                        })
                        .error(function (data) {
                          defer.reject(data);

                        });
                return defer.promise;
              },
              listfullsmstemplate: function () {
                var route = $window.myBaseURL + "api/smstemplate/listfull";
                var defer = $q.defer();
                $http.get(route)
                        .success(function (data) {
                          defer.resolve(data);
                        });
                return defer.promise;
              },
              getDetailSms: function (idSms, page) {
                var route = fullUrlBase + 'api/statics/getdetailsms/' + idSms + "/" + page;
                var defer = $q.defer();
                $http.get(route)
                        .success(function (data) {
                          defer.resolve(data);
                        });
                return defer.promise;
              },
              getCategory: function () {
                var route = fullUrlBase + '/api/smscategory/getall';
                var defer = $q.defer();
                $http.get(route)
                        .success(function (data) {
                          defer.resolve(data);
                        });
                return defer.promise;
              },
              getSms: function (page, data) {
                var route = fullUrlBase + '/api/sms/getall/' + page;
                var defer = $q.defer();
                $http.post(route, data)
                        .success(function (data) {
                          defer.resolve(data);
                        })
                        .error(function (data) {
                          defer.reject(data);
                          notificationService.warning(data.message);
                        });
                return defer.promise;
              },
              getOneSms: function (idSms) {
                var route = fullUrlBase + '/api/sms/getone/' + idSms;
                var defer = $q.defer();
                $http.get(route)
                        .success(function (data) {
                          defer.resolve(data);
                        })
                        .error(function (data) {
                          defer.resolve(data);
                        });
                return defer.promise;
              },
              downloadFailedSms: function (data, idSms) {
                var route = fullUrlBase + 'api/sms/downloadreportsmsfailed/' + idSms;
                var defer = $q.defer();
                $http.post(route, data)
                        .success(function (data) {
                          defer.resolve(data);
                        }).error(function (data) {
                  defer.resolve(data);
                });
                return defer.promise;
              },
              deletevarioussmslotes: function (idSms) {
                var route = fullUrlBase + 'api/sms/deletevariouslotes/' + idSms;
                var defer = $q.defer();
                $http.post(route)
                        .success(function (data) {
                          //console.log(data); 
                          defer.resolve(data);
                          notificationService.warning(data.message);
                        }).error(function (data) {
                  defer.resolve(data);
                });
                return defer.promise;
              },
              validateBalance: function(){
                  var route = fullUrlBase + 'api/sms/validatebalance';
                  var defer = $q.defer();
                  $http.post(route)
                          .success(function(data){
                              defer.resolve(data);
                              //notificationService.warning(data.message);
                          }).error(function(data){
                              defer.resolve(data);
                          });
                          return defer.promise;
              },
              sendMailNotificationSmsBalance: function(data){
                  var route = fullUrlBase + 'api/sms/sendmailnotsmsbalance';
                  var defer = $q.defer();
                  $http.post(route,data)
                          .success(function(data){
                              defer.resolve(data);
                              //notificationService.warning(data.message);
                          }).error(function(data){
                              defer.resolve(data);
                          });
                          return defer.promise;
              },
              getBalanceSubAccount: function(){
                  var route = fullUrlBase + 'api/sms/getbalancesubaccount';
                  var defer = $q.defer();
                  $http.post(route)
                          .success(function(data){
                              defer.resolve(data);
                              //notificationService.warning(data.message);
                          }).error(function(data){
                              defer.resolve(data);
                          });
                          return defer.promise;
            },
              downloadFailedSmsContact: function (data, idSms) {
                var route = fullUrlBase + 'api/sms/downloadfailedsmscontact/' + idSms;
                console.log(route);
                var defer = $q.defer();
                $http.post(route, data)
                        .success(function (data) {
                          defer.resolve(data);
                        }).error(function (data) {
                  defer.resolve(data);
                });
                return defer.promise;
              }
            }
          }])
        .controller('ctrlSms', ['$rootScope', '$scope', '$http', 'main', '$window', '$timeout', 'socket', 'notificationService', function ($rootScope, $scope, $http, main, $window, $timeout, socket, notificationService) {
            $scope.showButtonTwoway = false;
            $scope.gmt = "-0500";
            $scope.initial = 0;
            $scope.page = 1;
            $scope.filter = {};
            $scope.complete = false;
            $scope.misc = {};
            $scope.misc.ProccessCsv = {};
            $scope.typeCsv;
            $scope.targetcsv;
            $scope.morecaracter = false;
            main.getlisttimezone(function (res)
            {
              $scope.timezones = res;
              $scope.invalidCharacters = false;
              $scope.existTags = false;
            }, function (res) {
              slideOnTop("Ha ocurrido un error intentelo de nuevo mas tarde", 3000, "glyphicon glyphicon-remove-sign", "danger");
            });
            $scope.addDisabled = function (id) {
              $("#" + id).attr('disabled', 'disabled');
            }
            $scope.removeDisabled = function (id) {
              $("#" + id).removeAttr('disabled');
            }
            $scope.showDestinatary = false;
            $scope.not = false;
            $scope.datenow = true;
            $scope.sendpush = false;
            $scope.sendnotification = function () {
              if ($scope.not) {
                $scope.not = false;
                $("#email-addresses").hide("slow");
              } else {
                $scope.not = true;
                $("#email-addresses").show("slow");
              }
            }
            
            $scope.validateChecks = function (param){
                if($scope.morecaracter == true && $scope.sendpush == true){

                    slideOnTop("Lo sentimos no se puede realizar envíos de mensajes flash con la modalidad de más de 160 caracteres.", 3000, "glyphicon glyphicon-info-sign", "danger");
                    
                    if(param == 1 && $scope.sendpush == true){
                       $scope.morecaracter = false;                        
                    }else if(param == 2 && $scope.morecaracter == true){
                       $scope.sendpush = false;                        
                    }
                }
            }
            //Opciones avanzadas
            $scope.advancedoptions = false;
            $scope.evaluateAdvancedoptions = function () {
              if ($scope.advancedoptions) {
                $scope.advancedoptions = false;
              } else {
                $scope.advancedoptions = true;
              }
            }

            $scope.divide = false;
            $scope.divideSending = function () {
              if ($scope.divide) {
                $scope.divide = false;
                $("#divide-container").hide("slow");
              } else {
                $scope.divide = true;
                $("#divide-container").show("slow");
              }
            }

            $scope.timeFormats = [
              {value: 'minute', name: "Minuto(s)"},
              {value: 'hour', name: "Hora(s)"},
              {value: 'day', name: "Día(s)"},
              {value: 'week', name: "Semana(s)"},
              {value: 'month', name: "Mes(es)"}
            ];
            //fin de opciones avanzadas

//      $scope.expresion = function (msg){
//        var regex =  /[ñÑáéíóúÁÉÍÓÚ¿¡´]/;
//        console.log(regex.test(msg));
//      }

            $scope.validate = function () {
              $scope.error = 0;
              $scope.success = 0;

              var existTags = 0;
              var flag = true;
              $scope.wrongRow = false;
              $scope.arrError = [{}];
              var breaks = (typeof $scope.receiver != 'undefined' && typeSms != 'csv') ? $scope.receiver.split('\n') : [];
//              if (!$scope.receiver && typeSms != 'csv') {
//                flag = false;
//                slideOnTop("El campo destinatario es obligatorio", 3000, "glyphicon glyphicon-info-sign", "danger");
//              }
              $scope.total = breaks.length;
              console.log("total: ",$scope.total);
              console.log("totalAvailable: ",$scope.misc.totalAvailable);
              if(($scope.total>$scope.misc.totalAvailable) && (idSubaccount != "420" || idSubaccount != 420)){
                  //flag = false;
                  $scope.apismsemailbalance = {};
                  $scope.apismsemailbalance.total = $scope.total;
                  $scope.apismsemailbalance.totalAvailable = $scope.misc.totalAvailable;
                  $scope.sendMailNotificationSmsBalance();
                  openModalAdvice();
                  return false;
              }
              
              //if (!$('#toggle-two').prop('checked')) {
              if (!$scope.datenow) {
                var startdate = document.getElementById("datesend").value;
                if (startdate) {
                  var startdate = startdate.split(" ");
                  var time = startdate[1].split(":");
                  if (time[0] < startHour || time[0] > endHour) {
                    flag = false;
                    slideOnTop("La hora de envio debe de ser entre las" + startHour + " :00  y las " + endHour + ":00 ", 3000, "glyphicon glyphicon-info-sign", "danger");
                  }
                } else {
                  flag = false;
                  slideOnTop("Debes seleccionar una fecha y hora ", 3000, "glyphicon glyphicon-info-sign", "danger");
                }
              }
              if ($scope.not == true) {
                if ($scope.email == "") {
                  slideOnTop("Por favor ingrese el email de notificación", 3000, "glyphicon glyphicon-info-sign", "danger");
                }
              }
              if ($scope.email) {


                var email = $scope.email.split(",");
                emailRegex = /^[-\w.%+]{1,64}@(?:[A-Z0-9-]{1,63}\.){1,125}[A-Z]{2,63}$/i;
                var flagemail = false;
                for (var a = 0; a < email.length; a++) {
                  console.log(email[a]);
                  if (emailRegex.test(email[a])) {
                    //pasa
                  } else {
                    flag = false;
                    flagemail = true;
                    if (flagemail) {
                      slideOnTop("Hay algún correo de notificación erroneo por favor verifique", 3000, "glyphicon glyphicon-info-sign", "danger");
                      flagemail = false;
                    }
                  }
                }

                if (email.length > 8) {
                  flag = false;
                  slideOnTop("Solo se puede ingresar un máximo de 8 correos electrónicos", 3000, "glyphicon glyphicon-info-sign", "danger");
                }
              }
              if (!$scope.idSmsCategory) {
                flag = false;
                slideOnTop("Debes seleccionar una categoria", 3000, "glyphicon glyphicon-info-sign", "danger");
              }
              if (!$scope.receiver && typeSms != 'csv') {
                flag = false;
                slideOnTop("Debes ingresar al menos un destinatario", 3000, "glyphicon glyphicon-info-sign", "danger");
              }
              if (breaks.length > 50 && typeSms != 'csv') {
                flag = false;
                slideOnTop("Solo se puede ingresar 50 destinatarios", 3000, "glyphicon glyphicon-info-sign", "danger");
              }
              var flagValidate = false;
              if (typeSms != 'csv') {
                for (var i = 0; i < breaks.length; i++) {
                  if (!breaks[i].includes(";")) {
                    flagValidate = true;
                  }
                  var sms = breaks[i].split(";");
                  if (sms.length < 3) {
                    flagValidate = true;
                  }
                }
              }

              if (flagValidate) {
                flag = false;
                slideOnTop("Hay algún destinatario con el formato erróneo", 3000, "glyphicon glyphicon-info-sign", "danger");
                $scope.wrongRow = true;
              } else {
                $scope.wrongRow = false;
              }

              var re = /[ñÑáéíóúÁÉÍÓÚ¿¡´]/;
              var tags = /%%+[a-z0-9_]+%%/;
              $scope.success = breaks.length;
              for (var i = 0; i < breaks.length; i++) {


                var sms = breaks[i].split(";");
                var str = sms[0].indexOf("+");
                if (sms.length >= 4) {
                  $scope.arrError.push(breaks[i]);
                  $scope.success--;
                }
                if (!isFinite(sms[1])) {
                  $scope.arrError.push(breaks[i]);
                  $scope.success--;
                }
                if (sms.length < 3) {
                  $scope.arrError.push(breaks[i]);
                  $scope.success--;
                }
                if (sms.length == 3) {
                  if (sms[1].toString().trim().length !== 10) {
                    $scope.arrError.push(breaks[i]);
                    $scope.success--;
                  }
                  if (sms[2].toString().length > 160) {
                    $scope.arrError.push(breaks[i]);
                    $scope.success--;
                  }
                  if (tags.test(sms[2].toString())) {
                    existTags++;
                    $scope.arrError.push(breaks[i]);
                    $scope.success--;
                  }
                }
              }
              //Se hace este proceso para la carga del csv y traiga el target que envia
              if ($scope.typeCsv = 'csv') {
                $scope.success = $scope.targetcsv;
              }

              if (existTags > 0) {
                $scope.existTags = true;
              } else {
                $scope.existTags = false;
              }
              if (flag) {
                openModal();
              }
            };
            $scope.validateInLine = function () {
              $scope.error = 0;
              $scope.success = 0;
              $scope.wrongRow = false;
              var existTags = 0;
              var breaks = $scope.receiver.split('\n');
              var re = /[ñÑáéíóúÁÉÍÓÚ¿¡´]/;
              var tags = /%%+[a-z0-9_]+%%/;
              $scope.success = breaks.length;
              var flagValidate = false;
              for (var i = 0; i < breaks.length; i++) {
                if (!breaks[i].includes(";")) {
                  flagValidate = true;
                }
                var sms = breaks[i].split(";");
                if (sms.length < 3) {
                  flagValidate = true;
                }
              }
              if (flagValidate) {
                $scope.wrongRow = true;
              } else {
                $scope.wrongRow = false;
                var email = $scope.email.split(",");
                emailRegex = /^[-\w.%+]{1,64}@(?:[A-Z0-9-]{1,63}\.){1,125}[A-Z]{2,63}$/i;
                var flagemail = false;
                for (var a = 0; a < email.length; a++) {

                  console.log(email[a]);
                  if (emailRegex.test(email[a])) {
                    //pasa
                  } else {
                    flag = false;
                    flagemail = true;
                    if (flagemail) {
                      slideOnTop("Hay algún correo de notificación erroneo por favor verifique", 3000, "glyphicon glyphicon-info-sign", "danger");
                      flagemail = false;
                    }
                  }
                }

                if (email.length > 8) {
                  flag = false;
                  slideOnTop("Solo se puede ingresar un máximo de 8 correos electrónicos", 3000, "glyphicon glyphicon-info-sign", "danger");
                }
              }
              if (!$scope.idSmsCategory) {
                flag = false;
                slideOnTop("Debes seleccionar una categoria", 3000, "glyphicon glyphicon-info-sign", "danger");
              }
              if (!$scope.receiver) {
                flag = false;
                slideOnTop("Debes ingresar al menos un destinatario", 3000, "glyphicon glyphicon-info-sign", "danger");
              }
              if (breaks.length > 50) {
                flag = false;
                slideOnTop("Solo se puede ingresar 50 destinatarios", 3000, "glyphicon glyphicon-info-sign", "danger");
              }
              var flagValidate = false;
              for (var i = 0; i < breaks.length; i++) {
                if (!breaks[i].includes(";")) {
                  flagValidate = true;
                }
                var sms = breaks[i].split(";");
                if (sms.length < 3) {
                  flagValidate = true;
                }
              }
              if (flagValidate) {
                flag = false;
                slideOnTop("Hay algún destinatario con el formato erróneo", 3000, "glyphicon glyphicon-info-sign", "danger");
                $scope.wrongRow = true;
              } else {
                $scope.wrongRow = false;
              }

              var re = /[ñÑáéíóúÁÉÍÓÚ¿¡´]/;
              var tags = /%%+[a-z0-9_]+%%/;
              $scope.success = breaks.length;
              for (var i = 0; i < breaks.length; i++) {


                var sms = breaks[i].split(";");
                var str = sms[0].indexOf("+");
                if (sms.length >= 4) {
                  $scope.arrError.push(breaks[i]);
                  $scope.error++;
                  $scope.success--;
                }
                if (!isFinite(sms[1])) {
                  $scope.arrError.push(breaks[i]);
                  $scope.error++;
                  $scope.success--;
                }
                if (sms.length < 3) {
                  $scope.arrError.push(breaks[i]);
                  $scope.error++;
                  $scope.success--;
                }
                if (sms.length == 3) {
                  if (sms[1].toString().trim().length !== 10) {
                    $scope.arrError.push(breaks[i]);
                    $scope.error++;
                    $scope.success--;
                  }
                  if (sms[2].toString().length > 160) {
                    $scope.arrError.push(breaks[i]);
                    $scope.error++;
                    $scope.success--;
                  }
                  if (re.test(sms[2].toString())) {
                    $scope.arrError.push(breaks[i]);
                    $scope.error++;
                    $scope.success--;
                  }
                  if (tags.test(sms[2].toString())) {
                    existTags++;
                    $scope.arrError.push(breaks[i]);
                    $scope.error++;
                    $scope.success--;
                  }
                }
              }
              if (existTags > 0) {
                $scope.existTags = true;
              } else {
                $scope.existTags = false;
              }
              if (flag) {
                openModal();
              }
            };
            $scope.validateInLine = function () {
              $scope.error = 0;
              $scope.success = 0;
              $scope.wrongRow = false;
              var existTags = 0;
              var breaks = $scope.receiver.split('\n');
              var re = /[ñÑáéíóúÁÉÍÓÚ¿¡´]/;
              var tags = /%%+[a-z0-9_]+%%/;
              $scope.success = breaks.length;
              var flagValidate = false;
              for (var i = 0; i < breaks.length; i++) {
                if (!breaks[i].includes(";")) {
                  flagValidate = true;
                }
                var sms = breaks[i].split(";");
                if (sms.length < 3) {
                  flagValidate = true;
                }
              }
              if (flagValidate) {
                $scope.wrongRow = true;
              } else {
                $scope.wrongRow = false;
              }
              for (var i = 0; i < breaks.length; i++) {
                var sms = breaks[i].split(";");
                var str = sms[0].indexOf("+");
                if (sms.length == 3) {
                  if (tags.test(sms[2].toString())) {
                    existTags++;
                    $scope.error++;
                    $scope.success--;
                  }
                }
              }

              if (existTags > 0) {
                $scope.existTags = true;
              } else {
                $scope.existTags = false;
              }
            };
            $scope.createlote = function () {
              var data = {};
              data = {
                name: $scope.name,
                notification: $scope.not,
                email: $scope.email,
                receiver: $scope.receiver,
                idSmsCategory: $scope.idSmsCategory,
                datesend: document.getElementById("datesend").value,
//                datenow: $('#toggle-two').prop('checked'),
                timezone: $scope.timezone,
                AproximateSendings: $scope.success,
                advancedoptions: $scope.advancedoptions,
                morecaracter : $scope.morecaracter,
                divide: $scope.divide,
                continueError:  $scope.continueError,
                sendingTime: $scope.sendingTime,
                timeFormat: $scope.timeFormat,
                quantity: $scope.quantity,
                idSms: $scope.idSms,
                datenow: $scope.datenow,
                gmt: $scope.gmt,
                originalDate: document.getElementById("datesend").value,
                sendpush: $scope.sendpush
                
              };
              main.createLote(data, function (res) {
                closeModal();
                var route = $window.myBaseURL + "sms/";
                $window.location.href = route;
              }, function (res) {                
                slideOnTop(res.message, 3000, "glyphicon glyphicon-remove-sign", "danger");
                $rootScope.error = 'fail';
              });
            }

            $scope.editlote = function () {
              var data = {
                name: $scope.name,
                notification: $scope.not,
                email: $scope.email,
                receiver: $scope.receiver,
                idSmsCategory: $scope.idSmsCategory,
                datesend: document.getElementById("datesend").value,
//                datenow: $('#toggle-two').prop('checked'),
                timezone: $scope.timezone,
                AproximateSendings: $scope.success,
                advancedoptions: $scope.advancedoptions,
                morecaracter : $scope.morecaracter,
                divide: $scope.divide,
                continueError:  $scope.continueError,
                sendingTime: document.getElementById("sendingTime").value,
                timeFormat: $scope.timeFormat,
                quantity: $scope.quantity,
                idSms: $scope.idSms,
                datenow: $scope.datenow,
                gmt: $scope.gmt,
                originalDate: document.getElementById("datesend").value,
                sendpush: $scope.sendpush
              };
              main.editLote(data, function (res) {
                closeModal();
                var route = $window.myBaseURL + "sms/";
                $window.location.href = route;
              }, function (res) {
                if (data.datenow == false) {
                  $scope.datenow = false;
                } else {
                  $scope.datenow = true;
                } 
                slideOnTop(res, 3000, "glyphicon glyphicon-remove-sign", "danger");
                $rootScope.error = 'fail';
              });
            }

            $scope.sendnow = function () {
              if ($scope.datenow) {
                $scope.datenow = false;
              } else {
                $scope.datenow = true;
              }
            }
            
            $scope.opeModalMoreCa = function () {
                if( $('#morecaracter').prop('checked') && $('#sendpush').prop('checked') == false) {
                   $('#alertMoreCaracter').removeClass('modal'); 
                   $('#alertMoreCaracter').addClass('dialog dialog--open'); 
                }
            }

            $scope.getCategory = function () {
              main.getCategory().then(function (data) {
                $scope.smsCategory = data;
              });
            }

            $scope.validateprocess = function () {
              var flag = false;
              var name = document.getElementById("name").value;
              var idSmsCategory = document.getElementById("idSmsCategory").value;
              var timezone = document.getElementById("timezone").value;
              var datesend = document.getElementById("datesend").value;
              var email = document.getElementById("email").value;
              var quantity = document.getElementById("quantity").value;
              var sendingTime = document.getElementById("sendingTime").value;
              var timeFormat = document.getElementById("timeFormat").value;
              if (name == "") {
                slideOnTop("El nombre de envío esta incompleto, por favor ingrese un nombre", 3000, "glyphicon glyphicon-info-sign", "danger");
                flag = true;
              } else if (idSmsCategory == "") {
                slideOnTop("Por favor ingrese una categoria", 3000, "glyphicon glyphicon-info-sign", "danger");
                flag = true;
              } else if ($scope.datenow == false) {
                if (timezone == "") {
                  slideOnTop("Por favor ingrese zona horaria", 3000, "glyphicon glyphicon-info-sign", "danger");
                  flag = true;
                } else if (datesend == "") {
                  slideOnTop("Por favor ingrese fecha y hora de envio", 3000, "glyphicon glyphicon-info-sign", "danger");
                  flag = true;
                }
              } else if ($scope.advancedoptions == true) {

                if ($scope.not == true) {
                  if (email == "") {
                    slideOnTop("Por favor ingrese el email de notificación", 3000, "glyphicon glyphicon-info-sign", "danger");
                    flag = true;
                  } else {
                    let emailArray = $scope.email.split(/[\s\n, ;]+/);
                    let emailRegex = /^[-\w.%+]{1,64}@(?:[A-Z0-9-]{1,63}\.){1,125}[A-Z]{2,63}$/i;
                    for (var a = 0; a < emailArray.length; a++) {
                      if (!emailRegex.test(emailArray[a])) {
                        slideOnTop("El coreo ( " + emailArray[a] + " ) ingresado no es valido", 3000, "glyphicon glyphicon-info-sign", "danger");
                        flag = true;
                      }
                    }
                    if (emailArray.length > 8) {
                      slideOnTop("Solo se puede ingresar un máximo de 8 correos electrónicos", 3000, "glyphicon glyphicon-info-sign", "danger");
                      flag = true;
                    }
                  }
                } /*else if ($scope.divide == true) {
                  if (quantity == "") {
                    slideOnTop("Por favor ingrese la cantidad de envio por intervalos", 3000, "glyphicon glyphicon-info-sign", "danger");
                    flag = true;
                  } else if (sendingTime == "") {
                    slideOnTop("Por favor ingrese tiempo de envio", 3000, "glyphicon glyphicon-info-sign", "danger");
                    flag = true;
                  } else if (timeFormat == "") {
                    slideOnTop("Por favor ingrese el formato de envio", 3000, "glyphicon glyphicon-info-sign", "danger");
                    flag = true;
                  }
                }*/
              }
              if (flag == false) {
                var form = angular.element(document.getElementById('form').elements);
                var formData = new FormData();
                var config = {};
                config.headers = {};
                config.transformRequest = angular.identity;
                config.headers['Content-Type'] = undefined;
                for (var i = 0; i < form.length; i++) {
                  if (form[i].getAttribute("name") != null) {
                    if (form[i].getAttribute("name") == "csv") {
                      formData.append(form[i].getAttribute("name"), form[i].files[0]);
                      //formData.append("idSmsCreated", $scope.misc.idSms); 
                    } else {
                      console.log(form[i].getAttribute("name"), form[i].type, form[i].checked);
                      if (form[i].type == "checkbox") {
                        if (form[i].checked) {
                          formData.append(form[i].getAttribute("name"), form[i].value);
                        }
                      } else {
                        formData.append(form[i].getAttribute("name"), form[i].value);
                      }
                    }
                  }
                }
                $('#ProcessCsv').addClass('dialog--open');
                $scope.misc.ProccessCsv.porc = 20;
                $http.post(fullUrlBase + 'api/sms/createcsv', formData, config)
                        .success(function (data) {})
                        .error(function (data) {
                          notificationService.error(data.message);
                          //$('#ProcessCsv').removeClass('dialog--open');
                        });
              } else {
                document.getElementById("savecsv").style.display = 'block';
              }

            }

            $scope.verifyservicetwoway = function () {
              main.getServiceVerified().then(function (data) {
                $scope.smsverified = data;
                if ($scope.smsverified == 1) {
                  $scope.showButtonTwoway = true;
                } else {
                  $scope.showButtonTwoway = false;
                }
              });
            };
            $scope.forward = function () {
              $scope.initial += 1;
              $scope.page += 1;
              $scope.getAll();
            };
            $scope.fastforward = function () {
              $scope.initial = ($scope.sms.total_pages - 1);
              $scope.page = $scope.sms.total_pages;
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
            $scope.search = function () {
              $scope.misc.progressbar = false;
              $scope.initial = 0;
              $scope.page = 1;
              $scope.getAll();
            };
            $scope.searchcategory = function () {
              $scope.misc.progressbar = false;
              if ($scope.filter.category.length >= 1) {
                $scope.initial = 0;
                $scope.page = 1;
                $scope.getAll();
              } else {
                $scope.getAll();
              }
            };
            $scope.$watch('[filter.dateinitial,filter.dateend]', function () {
              if ((angular.isDefined($scope.filter.dateinitial) && $scope.filter.dateinitial != "") && (angular.isDefined($scope.filter.dateend) && $scope.filter.dateend != "")) {
                $scope.misc.progressbar = false;
                $scope.initial = 0;
                $scope.page = 1;
                $scope.getAll();
              }
            });
            $scope.statusFunc = function () {
//                  console.log('Afuera');
//                  console.log($scope.filter.mailStatus);
              if ($scope.filter.smsStatus.length > 0) {
//                  console.log('Adentro');
//                  console.log($scope.filter.mailStatus);
                $scope.initial = 0;
                $scope.page = 1;
                $scope.getAll();
              }
            };
            $scope.getAll = function () {              
                main.getSms($scope.initial, $scope.filter).then(function (data)
                {
                  $scope.sms = data;
                  $scope.complete = true;
                  $scope.misc.progressbar = true;
                }).catch(function (error) {
                  notificationService.error(error.message);
                  $scope.misc.progressbar = true;
                })              
            };
            $scope.getOne = function (idSms) {
              
              main.getOneSms(idSms).then(function (data)
              {
                $scope.sms = data;
                $scope.typeCsv = $scope.sms.type;
                $scope.targetcsv = $scope.sms.target;
                $scope.idSms = $scope.sms.idSms;
                $scope.name = $scope.sms.name;
                $scope.not = false;
                if ($scope.sms.notification == 1) {
                  $scope.not = true;
                }
                if($scope.sms.morecaracter === true){
                   $scope.morecaracter = true;
                   $("#morecaracter").prop('checked', true);
                }else{
                   $scope.morecaracter = false; 
                   $("#morecaracter").prop('checked', false);
                }
                $scope.email = $scope.sms.email;
                $scope.receiver = $scope.sms.receiver;
                $scope.idSmsCategory = $scope.sms.idSmsCategory;
                $scope.originalDate = $scope.sms.originalDate;
                $scope.datenow = false;
                $('#toggle-two').bootstrapToggle('off');
                if ($scope.sms.dateNow == 1) {
                  $scope.datenow = true;
                  $('#toggle-two').bootstrapToggle('on');
                }
                if ($scope.sms.advancedoptions == 1) {
                  $scope.advancedoptions = true;
                  $('#toggle-three').bootstrapToggle('on');
                }
                if ($scope.sms.notification == 1) {
                  $scope.not = true;
                  $('#toggle-one').bootstrapToggle('on');
                }
                if ($scope.sms.divide == 1) {
                  $scope.divide = true;
                  $('#toggle-four').bootstrapToggle('on');
                }
                if($scope.sms.sendpush == 1){
                  $scope.sendpush = true;
                  $('#toggle-one').bootstrapToggle('on');
                }
                $scope.timezone;
                $scope.AproximateSendings;
                $scope.timeFormat = $scope.sms.timeFormat;
                $scope.quantity = parseInt($scope.sms.quantity);
//                $scope.gmt = $scope.sms.gmt;
                $scope.gmt = $scope.sms.gmt;
              });
            };
            $scope.validateBalance = function () {
              main.validateBalance().then(function (data) {
                $scope.misc.tempSumSmsPendingTarget = 0;
                if(data.smsFindPending.length>0){
                    angular.forEach(data.smsFindPending, function (value, key) {
                        $scope.misc.tempSumSmsPendingTarget = $scope.misc.tempSumSmsPendingTarget + parseInt(value.target);
                    });
                }
                else{
                    $scope.misc.smsAvailable = 0;
                }
                if(data.balanceConsumedFind.length>0){
                    $scope.misc.balanceAvailable = data.balanceConsumedFind[0]['amount'];
                    $scope.misc.balanceTotalGet = data.balanceConsumedFind[0]['totalAmount'];
                }
                else{
                    $scope.misc.balanceAvailable = 0;
                }
                
                $scope.misc.totalAvailable = parseInt($scope.misc.balanceAvailable) - parseInt($scope.misc.tempSumSmsPendingTarget);
                
                console.log('smsFindPending:',data.smsFindPending);
                console.log('balanceConsumedFind:',data.balanceConsumedFind);
                console.log('tempSumSmsPendingTarget',$scope.misc.tempSumSmsPendingTarget);
                console.log('scope.misc.balanceConsumed',$scope.misc.balanceAvailable);
                console.log('scope.misc.totalAvailable',$scope.misc.totalAvailable);
              });
            };
            
            $scope.sendMailNotificationSmsBalance = function () {
                   main.sendMailNotificationSmsBalance($scope.apismsemailbalance).then(function (data) { 
                    });
                }; 
            $scope.sendMailNotificationSmsBalanceCsv = function () {
                   main.sendMailNotificationSmsBalance($scope.apismsemailbalancecvs).then(function (data) { 
                    });
                }; 
            
            if (loadGetAll == ''){
              $scope.getAll();
            }
            
            //$scope.verifyservicetwoway();
            $scope.validateBalance();
            $scope.changeStatusCsv = function (status) {
              $http.post(fullUrlBase + 'api/sms/changestatus/' + $scope.misc.idSms, {status: status}).then(function () {
                var route = $window.myBaseURL + "sms/";
                $window.location.href = route;
              }).catch(function () {});
            }
            
            //METODO PARA VALIDAR LA CANTIDAD DE MENSAJES EN EL CSV Y EL SALDO DISPONIBLE
            $scope.balanceCSV = false;
            $scope.validateBalanceCSV = function(totalAvailable, countSent){
                if(totalAvailable < countSent){
                    $scope.balanceCSV = true;
                }
            };
            
            socket.on('loading-csv-sms', function (data) {
              console.info("Node Data", data);
              if (data.idSubaccount == idSubaccountLogin) {
                if (data.status == "preload") {
                  $scope.misc.ProccessCsv.preload = {};
                  $scope.misc.ProccessCsv.porc += 20;
                }
                if (data.status == "validations") {
                  $scope.misc.ProccessCsv.porc += 20;
                  $scope.misc.ProccessCsv.preload.data = data.data;
                  $scope.misc.ProccessCsv.validations = {};
                }
                if (data.status == "load") {
                  $scope.misc.ProccessCsv.porc += 20;
                  $scope.misc.ProccessCsv.validations.data = data.data;
                  $scope.misc.ProccessCsv.load = {};
                }
                if (data.status == "finish") {
                 
                  console.log('totalAvailable:',$scope.misc.totalAvailable);
                  $scope.misc.ProccessCsv.porc += 20;
                  $scope.misc.ProccessCsv.load.data = data.data;
                  $scope.misc.ProccessCsv.finish = {};
                  $scope.misc.idSms = data.id;
                  $scope.misc.ProccessCsv.finish.message = data.message;
                  $scope.triggerEmailNotificationBalance();
                  if(idSubaccountLogin != 420 || idSubaccountLogin != "420"){
                    $scope.validateBalanceCSV($scope.misc.totalAvailable, $scope.misc.ProccessCsv.load.data.countSent);
                  }

                }
              }
            });
            

            
            $scope.cancelSmsAndDeleteLotes = function () {
              main.deletevarioussmslotes($scope.misc.idSms).then(function (data) { /*nothing to do...*/
              });
            };
            $scope.misc.progressbar = false;
            $scope.downloadFailedSms = function () {
              $scope.titleReport = "Detalle números Invalidos";
              $scope.misc.progressbar = true;
              main.downloadFailedSms($scope.misc.ProccessCsv, $scope.misc.idSms).then(function () {
                var url = fullUrlBase + 'statistic/downloadexcel/' + $scope.titleReport;
                location.href = url;
                $scope.misc.progressbar = false;
              });
            };
            socket.on('refresh-view-sms', function (data) {
              for (i in $scope.sms.items) {
                if ($scope.sms.items[i].idSms == data.idSms) {
                  $scope.sms.items.target = 1000;
                  $scope.sms.items[i].status = data.status;
                }
              }
            });
            socket.on('refresh-view-sms-send', function (data) {
              for (i in $scope.sms.items) {
                if ($scope.sms.items[i].idSms == data.idSms) {
                  $scope.sms.items[i].sent = data.sent;
                  $scope.sms.items[i].total = data.total;
                }
              }
            });
            /*$scope.getBalanceSubAccount = function(){
                main.getBalanceSubAccount().then(function (data) {
                    console.log('Data getBalanceSubAccount:', data);
                });
            }
            $scope.getBalanceSubAccount();*/
                
            $scope.triggerEmailNotificationBalance = function (){
                if($scope.misc.totalAvailable<$scope.misc.ProccessCsv.load.data.countSent){
                   $scope.apismsemailbalancecvs = {};
                   $scope.apismsemailbalancecvs.total = $scope.misc.ProccessCsv.load.data.countSent;
                   $scope.apismsemailbalancecvs.totalAvailable = $scope.misc.totalAvailable;
                   $scope.sendMailNotificationSmsBalanceCsv();
                }
                
            };
            
            
                
          }])
        .controller('smsContact', ['$rootScope', '$scope', '$http', 'main', '$window', '$timeout', 'notificationService', function ($rootScope, $scope, $http, main, $window, $timeout, notificationService) {
            $scope.listAddressee = [{id: 1, name: "Listas de contactos"}, {id: 2, name: "Segmentos"}];
            $scope.arrAddressee = [];
            $scope.listSelected = {};
            $scope.data = {};
            $scope.datenow = false;
            $scope.viewTemplate = false;
            $scope.notification = false;
            $scope.notificationEmail = false;
            $scope.allList = false;
            $scope.gmt = "-0500";
            $scope.countContactsApproximate = {};
            $scope.countContactsApproximate.counts = 0;
            $scope.advancedoptions = false;
            $scope.divide = false;
            $scope.continueError = false;
            $scope.initProcessValidate = false;
            $scope.editandcreate = false;
            $scope.progresslinear = false;
            $scope.switchrepeated = true;
            $scope.morecaracter = false;
            $scope.sendpush = false;
            $scope.misc = {};
            
            $scope.validateBalanceSmsContact = function () {
              main.validateBalance().then(function (data) {
                $scope.misc.tempSumSmsPendingTarget = 0;
                if(data.smsFindPending.length>0){
                    angular.forEach(data.smsFindPending, function (value, key) {
                        $scope.misc.tempSumSmsPendingTarget = $scope.misc.tempSumSmsPendingTarget + parseInt(value.target);
                    });
                }
                else{
                    $scope.misc.smsAvailable = 0;
                }
                if(data.balanceConsumedFind.length>0){
                    $scope.misc.balanceAvailable = data.balanceConsumedFind[0]['amount'];
                    $scope.misc.balanceTotalGet = data.balanceConsumedFind[0]['totalAmount'];
                }
                else{
                    $scope.misc.balanceAvailable = 0;
                }
                
                $scope.misc.totalAvailable = parseInt($scope.misc.balanceAvailable) - parseInt($scope.misc.tempSumSmsPendingTarget);
                
                console.log('smsFindPending:',data.smsFindPending);
                console.log('balanceConsumedFind:',data.balanceConsumedFind);
                console.log('tempSumSmsPendingTarget',$scope.misc.tempSumSmsPendingTarget);
                console.log('scope.misc.balanceConsumed',$scope.misc.balanceAvailable);
                console.log('scope.misc.totalAvailable',$scope.misc.totalAvailable);
              });
            };
            
            $scope.validateChecks = function (param){
                if($scope.morecaracter == true && $scope.sendpush == true){

                    slideOnTop("Lo sentimos no se puede realizar envíos de mensajes flash con la modalidad de más de 160 caracteres.", 3000, "glyphicon glyphicon-info-sign", "danger");
                    
                    if(param == 1 && $scope.sendpush == true){
                       $scope.morecaracter = false;                        
                    }else if(param == 2 && $scope.morecaracter == true){
                       $scope.sendpush = false;                        
                    }
                }
            }
            
            $scope.validateId = function () {
              if (!angular.isUndefined(idSms)) {
                $scope.name = nameSms;
                $scope.showAddressee = true;
                if (receiverSms.type == "contactlist") {
                  $scope.data.listSelected = $scope.listAddressee[0];
                  $scope.getDetinatary($scope.data.listSelected);
                  $scope.data.arrAddressee = receiverSms.contactlists;
                } else {
                  $scope.data.listSelected = $scope.listAddressee[1];
                  $scope.getDetinatary($scope.data.listSelected);
                  $scope.data.arrAddressee = receiverSms.segment;
                }
                $scope.data.message = messageSms;
                $scope.countContacts();
                if (emailSms != "") {
                  $scope.not = false;
                  $scope.notificationEmail = true;
                  $scope.email = emailSms;
                }
              }
            }
            main.getlisttimezone(function (res) {
              $scope.timezones = res;
              $scope.timezone = "-0500";
            }, function (res) {
              slideOnTop("Ha ocurrido un error intentelo de nuevo mas tarde", 3000, "glyphicon glyphicon-remove-sign", "danger");
            });

            $scope.timeFormats = [
              {value: 'minute', name: "Minuto(s)"},
              {value: 'hour', name: "Hora(s)"},
              {value: 'day', name: "Día(s)"},
              {value: 'week', name: "Semana(s)"},
              {value: 'month', name: "Mes(es)"}
            ];
            //fin de opciones avanzadas

            $scope.addDisabled = function (id) {
              $("#" + id).attr('disabled', 'disabled');
            }
            $scope.removeDisabled = function (id) {
              $("#" + id).removeAttr('disabled');
            }

            $scope.opeModalMoreCa = function () {
                if( $('#morecaracter').prop('checked') && $('#sendpush').prop('checked') == false) {
                   $('#alertMoreCaracter').removeClass('modal'); 
                   $('#alertMoreCaracter').addClass('dialog dialog--open'); 
                }
            }

            $scope.timeFormats = [
              {value: 'minute', name: "Minuto(s)"},
              {value: 'hour', name: "Hora(s)"},
              {value: 'day', name: "Día(s)"},
              {value: 'week', name: "Semana(s)"},
              {value: 'month', name: "Mes(es)"}
            ];
            //fin de opciones avanzadas

            $scope.addDisabled = function (id) {
              $("#" + id).attr('disabled', 'disabled');
            }
            $scope.removeDisabled = function (id) {
              $("#" + id).removeAttr('disabled');
            }
            
            //METODO PARA VALIDAR LA CANTIDAD DE MENSAJES EN EL CSV Y EL SALDO DISPONIBLE
            $scope.balanceCSV = false;
            $scope.validateBalanceCSV = function(totalAvailable, countSent){
                if(totalAvailable < countSent){
                    $scope.balanceCSV = true;
                    console.log("balanceCSV: ",$scope.balanceCSV);
                }
            };

            $scope.validate = function () {
              $scope.error = 0;
              $scope.success = 0;
              var flag = true;
              $scope.arrError = [{}];
              try {

                if (!$scope.datenow) {
                  var startdate = document.getElementById("datesend").value;
                  if (startdate) {
                    var startdate = startdate.split(" ");
                    var time = startdate[1].split(":");
                    if (time[0] < startHour || time[0] > endHour) {
                      throw("La hora de envio debe de ser entre las" + startHour + " :00  y las " + endHour + ":00 ");
                    }
                  } else {
                    throw("Debes seleccionar una fecha y hora ");
                  }
                }
                if ($scope.not == true) {
                  if ($scope.email == "") {
                    throw("Por favor ingrese el email de notificación");
                  }
                }
                if ($scope.email) {
                  var email = $scope.email.split(",");
                  if (email.length == 0) {
                    throw("No se encontro ningun correo electronico.");
                  }
                  if (email.length > 8) {
                    throw("Solo se puede ingresar un máximo de 8 correos electrónicos");
                  }
                  for (var i = 0; i < email.length; i++) {
                    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                    email[i] = email[i].trim();
                    if (!email[i].match(re)) {
                      throw("El correo " + email[i] + " no es valido.");
                    }
                  }
                }
                var message = $('#message').val(); 
                $scope.data.message = message;
                message = null;
                if (!$scope.idSmsCategory) {
                  throw("Debes seleccionar una categoria");
                }
                if (angular.isUndefined($scope.data.listSelected)) {
                  throw("Debe seleccionar una lista de destinatarios");
                }
                if (angular.isUndefined($scope.data.arrAddressee) || $scope.data.arrAddressee == 0) {
                  throw ("Debe seleccionar al menos un segmento o lista de contacto.");
                }

                if (angular.isUndefined($scope.data.message) || $scope.data.message.length < 2) {
                  console.log($scope.data);  
                  throw ("El mensaje es obligatorio y debe ser mayor a dos caracteres.");
                }
//                if ($scope.newMessage.length > 160) {
//                  throw ("El mensaje no puede ser mayor a 160 caracteres.");
//                }
                console.log("totalAvailable: ",$scope.misc.totalAvailable);
                console.log("countContactsApproximate: ",$scope.countContactsApproximate.counts);
                if(idSubaccount != 420 || idSubaccount != "420"){
                    $scope.validateBalanceCSV($scope.misc.totalAvailable, $scope.countContactsApproximate.counts);
                }
                openModal();

              } catch ($err) {
                slideOnTop($err, 3000, "glyphicon glyphicon-info-sign", "danger");
              }
            };

            $scope.validatecreate = function () {
              main.validatesmscontact($scope.IdSms)
                      .then(function (data) {
                        closeModal();
                        var route = $window.myBaseURL + "sms/";
                        $window.location.href = route;
                      })
                      .catch(function (data) {
                        slideOnTop(data.message, 3000, "glyphicon glyphicon-remove-sign", "danger");
                        $rootScope.error = 'fail';
                      });

            }

            $scope.canceledandedit = function () {
              idSms = $scope.IdSms
              closeModal();
              $scope.initProcessValidate = false;
              $scope.editandcreate = true;

            }

            $scope.downloadFailedSms = function () {
              $scope.titleReport = "Detalle números Invalidos";
              $scope.data;
              main.downloadFailedSmsContact($scope.data, $scope.IdSms).then(function () {
                var url = fullUrlBase + 'statistic/downloadexcel/' + $scope.titleReport;
                location.href = url;
              });
            };

            $scope.sendnow = function () {
              if ($scope.datenow) {
                $scope.datenow = false;
              } else {
                $scope.datenow = true;
              }
            }

            $scope.createcontact = function () {
              $scope.disabled = true;
              $scope.progresslinear = true;

              if ($scope.editandcreate == true) {
                $scope.editContact();
              } else {

                var data = {};
                var target = {};
                if ($scope.data.listSelected.id == 1) {
                  target = {type: 'contactlist', contactlists: $scope.data.arrAddressee};
                } else {
                  target = {type: 'segment', segment: $scope.data.arrAddressee};
                }

                data = {
                  name: $scope.name,
//                notification: $('#toggle-one').prop('checked'),
                  notification: $scope.not,
                  email: $scope.email,
                  target: $scope.countContactsApproximate.counts,
                  receiver: target,
                  message: $scope.data.message,
                  idSmsCategory: $scope.idSmsCategory,
                  datesend: document.getElementById("datesend").value,
//                datenow: $('#toggle-two').prop('checked'),
                  timezone: $scope.timezone,
                  AproximateSendings: $scope.success,
                  advancedoptions: $scope.advancedoptions,
                  divide: $scope.divide,
                  continueError: $scope.continueError,
                  sendingTime: $scope.sendingTime,
                  timeFormat: $scope.timeFormat,
                  quantity: $scope.quantity,
                  idSms: $scope.idSms,
                  datenow: $scope.datenow,
                  gmt: $scope.gmt,
                  morecaracter : $scope.morecaracter,
                  originalDate: document.getElementById("datesend").value,
                  switchrepeated: $scope.switchrepeated,
                  sendpush: $scope.sendpush
                };

                if ($scope.viewTemplate == true && $scope.idSmsTemplate == undefined) {
                  notificationService.error("Si desea usar una plantilla predefinida de SMS, debe seleccionarla de la lista de plantillas");
                  return false;
                }

                main.createContact(data)
                        .then(function (data) {
                          console.log(data);
                          $scope.disabled = false;
                          $scope.validcount = data.data.Envios;
                          $scope.invalicount = data.data.Invalidos;
                          $scope.IdSms = data.data.IdSms;
                          $scope.initProcessValidate = true;
                          $scope.progresslinear = false;

                          //closePreview();
                          //var route = $window.myBaseURL + "sms/";
                          //$window.location.href = route;
                        })
                        .catch(function (data) {
                          $scope.progresslinear = false;
                          slideOnTop(data.data.message, 3000, "glyphicon glyphicon-remove-sign", "danger");
                          $rootScope.error = 'fail';
                        });
              }
            }

            $scope.sendnotification = function () {
              if ($scope.not) {
                $scope.not = false;
                //$("#email-addresses").hide("slow");
              } else {
                $scope.not = true;
                // $("#email-addresses").show("slow");
              }
            }

            $scope.editContact = function () {

              $scope.progresslinear = true;

              var data = {};
              var target = {};
              if ($scope.data.listSelected.id == 1) {
                target = {type: 'contactlist', contactlists: $scope.data.arrAddressee};
              } else {
                target = {type: 'segment', segment: $scope.data.arrAddressee};
              }

              if (!$scope.not) {
                $scope.email = "";
              }

              data = {
                name: $scope.name,
                notification: $('#toggle-one').prop('checked'),
                email: $scope.email,
                target: $scope.countContactsApproximate.counts,
                receiver: target,
                message: $scope.data.message,
                idSmsCategory: $scope.idSmsCategory,
                datesend: document.getElementById("datesend").value,
                datenow: $scope.datenow,
                timezone: $scope.timezone,
                idSms: idSms,
                morecaracter : $scope.morecaracter,
                sendpush : $scope.sendpush,
                switchrepeated: $scope.switchrepeated
              };
              $scope.taggedMessage = $scope.data.message;
              console.log(data);
              main.editContact(data)
                      .then(function (data) {

                        $scope.validcount = data.Envios;
                        $scope.invalicount = data.Invalidos;
                        $scope.IdSms = data.IdSms;
                        $scope.initProcessValidate = true;
                        $scope.progresslinear = false;
                      })
                      .catch(function (data) {
                        $scope.progresslinear = false;
                        console.log(data);
                        slideOnTop(data, 3000, "glyphicon glyphicon-remove-sign", "danger");
                        $rootScope.error = 'fail';
                      });
            }
            $scope.errorMessage = false;
            $scope.validateInLine = function () {
              $scope.invalidCharacters = false;
              $scope.existTags = false;
              $scope.taggedMessage = $scope.data.message;
              $scope.newMessage = $scope.data.message;
              var re = /[ñÑáéíóúÁÉÍÓÚ¿¡´]/;
              var tags = /%%+[a-zA-Z0-9_]+%%/;
              var count = 0;
              if (tags.test($scope.data.message)) {
                $scope.existTags = true;
                $scope.taggedMessage = "";
                $scope.newMessage = "";
                var words = $scope.data.message.split(" ");
                for (var cont = 0; cont < words.length; cont++) {
                  var word = words[cont];
                  var word2 = words[cont];
                  if (word.substr(0, 2) == "%%" && (word.substr(-2) == "%%" || word.substr(-3) == "%%," || word.substr(-3) == "%%." || word.substr(-3) == "%%;")) {
                    word = word.substr(2);
                    //word2 = "";
                    word = "<b><i>" + word;
                    if (word.substr(-2) == "%%") {
                      word = word.substr(0, word.length - 2);
                    } else if (word.substr(-3) == "%%," || word.substr(-3) == "%%." || word.substr(-3) == "%%;") {
                      word = word.substr(0, word.length - 3);
                    }
                    word = word + "</i></b>";
                    count = count + word.length;
                  }
                  $scope.taggedMessage += word + " ";
                  $scope.newMessage += word2;
                }
              }
            };
            /*$scope.$watch("data.message", function () {
             var message = $scope.data.message;
             var tags = /%%+[a-z0-9_]+%%/;
             console.log(message);
             }, true);*/
            $scope.changeMessage = function () {
              var message = $scope.data.message;
              var tags = /%%+[a-z0-9_]+%%/;
              /*if(tags.search(message)){
               console.log("hola");
               }*/
            };

            $scope.countContacts = function () {
                console.log('scope.data',$scope.data);
              var type = '';
              $scope.countError = false;
              if ($scope.data.listSelected.id == 1) {
                type = 'contactlist';
              } else {
                type = 'segment';
              }
              if (angular.isUndefined($scope.data.arrAddressee) || $scope.data.arrAddressee == 0) {
                $scope.countContactsApproximate.counts = 0
                return;
              }
              var data = {
                type: type,
                segment: $scope.data.arrAddressee,
                contactlist: $scope.data.arrAddressee,
                switchrepeated: $scope.switchrepeated
              };
              main.countContact(data).then(function (data) {
                $scope.countContactsApproximate = data.data;
                console.log('countContacts:',$scope.countContactsApproximate);
                if($scope.countContactsApproximate.counts > 40000 && !$scope.divide){
                  slideOnTop("Sobrepasa el maximo permitido de 40.000 Contactos", 3000, "glyphicon glyphicon-remove-sign", "warning");
                  return;
                }
                $scope.triggerEmailNotificationBalanceSmsContact($scope.countContactsApproximate.counts);
              }).catch(
                function (data) {
                  $scope.countError = true;
                  slideOnTop(/*data.data.message*/"Sobrepasa el maximo permitido de 40.000 Contactos", 3000, "glyphicon glyphicon-remove-sign", "warning");
                }
              );
            };
            $scope.getDetinatary = function (list) {
//        $scope.countContactsApproximate.counts=1;
              $scope.showAddressee = false;
              $scope.data.arrAddressee = [];
              if (list.id == 1) {
                main.getcontactlist()
                        .success(function (data) {
                          $scope.showAddressee = true;
                          $scope.listAllAddressee = data;
                        })
                        .catch(function (data) {
                          slideOnTop(data.message, 3000, "glyphicon glyphicon-remove-sign", "danger");
                        });
              } else {
                main.getsegments()
                        .success(function (data) {
                          $scope.showAddressee = true;
                          $scope.listAllAddressee = data;
                        })
                        .catch(function (data) {
                          slideOnTop(data.message, 3000, "glyphicon glyphicon-remove-sign", "danger");
                        });
              }
            }


            $scope.openPreview = function () {
              $("#preview").addClass('dialog--open');
            }
            $scope.closePreview = function () {
              $("#preview").removeClass('dialog--open');
            }

            $scope.addTag = function (tag) {
              if (typeof $scope.data.message == "undefined") {
                $scope.data.message = "";
                $scope.data.message += tag;
              } else {
                var text = $scope.data.message + " " + tag;
                if (text.length > 160 && $scope.morecaracter === false) {
                  slideOnTop("No puede agregar esta etiqueta, ya que excede el límite de caracteres", 3000, "glyphicon glyphicon-remove-sign", "danger");
                  return false;
                }else if(text.length > 300 && $scope.morecaracter === true){
                  slideOnTop("No puede agregar esta etiqueta, ya que excede el límite de caracteres", 3000, "glyphicon glyphicon-remove-sign", "danger");
                  return false;                    
                }
                $scope.data.message += " " + tag;
              }

              $scope.validateInLine();
            }

            main.listfullsmstemplate().then(function (data) {
              $scope.listfullsmstemplate = data;
            });
            $scope.smstemplate = function (data) {
              $scope.viewTemplate = !data;
            };
            $scope.useTemplate = function () {
              angular.forEach($scope.listfullsmstemplate, function (value, key) {
                if (value.idSmsTemplate == $scope.idSmsTemplate) {
                  $scope.addTag(value.content);
                }
              });
            };
            $scope.getOne = function () {

              main.getOneSms(idSms).then(function (data)
              {
                $scope.sms = data;
                $scope.idSms = $scope.sms.idSms;
                $scope.name = $scope.sms.name;
                $scope.not = false;
                if ($scope.sms.notification == 1) {
                  $scope.not = true;
                }
                if($scope.sms.morecaracter === true){
                   $scope.morecaracter = true;
                   $("#morecaracter").prop('checked', true);
                }else{
                   $scope.morecaracter = false; 
                   $("#morecaracter").prop('checked', false);
                }
                $scope.email = $scope.sms.email;
                $scope.receiver = $scope.sms.receiver;
                $scope.idSmsCategory = $scope.sms.idSmsCategory;
                $scope.originalDate = $scope.sms.originalDate;
                $scope.datenow = true;
                $('#toggle-two').bootstrapToggle('off');

 

                if ($scope.sms.dateNow == 1) {
                  $scope.datenow = true;
                  $('#toggle-two').bootstrapToggle('off');
                }
                if ($scope.sms.advancedoptions == 1) {
                  $scope.advancedoptions = true;
                  $('#toggle-three').bootstrapToggle('on');
                }
                if ($scope.sms.notification == 1) {
                  $scope.not = true;
                  $('#toggle-one').bootstrapToggle('on');
                }
                if ($scope.sms.divide == 1) {
                  $scope.divide = true;
                  $('#toggle-four').bootstrapToggle('on');
                }
                if($scope.sms.sendpush == 1){
                  $scope.sendpush = true;
                  $('#toggle-one').bootstrapToggle('on');
                }
                $scope.timezone;
                $scope.AproximateSendings;
                $scope.timeFormat = $scope.sms.timeFormat;
                $scope.quantity = parseInt($scope.sms.quantity);
//                $scope.gmt = $scope.sms.gmt;
                $scope.gmt = $scope.sms.gmt;
              });
            };
            $scope.getOne();
            $scope.validateBalanceSmsContact();
            
            $scope.triggerEmailNotificationBalanceSmsContact = function (totalContacts){
              if($scope.misc.totalAvailable<totalContacts){
                    $scope.apismsContactEmailbalance = {};
                    $scope.apismsContactEmailbalance.total = totalContacts;
                    $scope.apismsContactEmailbalance.totalAvailable = $scope.misc.totalAvailable;
                    $scope.sendMailNotificationSmsContactBalance();
                   }
              };
//              
              $scope.sendMailNotificationSmsContactBalance = function () {
                   main.sendMailNotificationSmsBalance($scope.apismsContactEmailbalance).then(function (data) { 
                    });
                }; 
            
            $scope.selectAllList = function () {
              console.log($scope.allList);
              if ($scope.allList) {
                $scope.data.arrAddressee = [];
                for (let i = 0; i < $scope.listAllAddressee.length; i++) {
                  $scope.data.arrAddressee.push($scope.listAllAddressee[i]);
                }
              } else {
                $scope.data.arrAddressee = [];
              }
              $scope.countContacts();
            }


            
            $scope.switchrepeatedclic = function (switchrepeated) {
              $scope.switchrepeated = switchrepeated;
              console.log($scope.switchrepeated);

              if ($scope.switchrepeated !== true) {
                $scope.switchrepeated = false;
              }
              if ($scope.switchrepeated == true) {
                document.getElementById('repe1').style.display = "block";
                document.getElementById('repe2').style.display = "none";
                $scope.countContacts();
              }
              if ($scope.switchrepeated == false) {
                document.getElementById('repe1').style.display = "none";
                document.getElementById('repe2').style.display = "block";
                $scope.countContacts();
              }
            }

          }])
        .controller('showContact', ['$rootScope', '$scope', '$http', 'main', '$window', '$timeout', 'notificationService', function ($rootScope, $scope, $http, main, $window, $timeout, notificationService) {
            $scope.initial = 0;
            $scope.page = 1;
            $scope.getAll = function () {
              main.getDetailSms(idSms, $scope.initial).then(function (res) {
                $scope.listsms = res;
              });
            };
            $scope.forward = function () {
              $scope.initial += 1;
              $scope.page += 1;
              $scope.getAll();
            };
            $scope.fastforward = function () {
              $scope.initial = ($scope.listsms.detail[1].total_pages - 1);
              $scope.page = $scope.listsms.detail[1].total_pages;
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
            $scope.traslateStatus = function (status) {
              var string = "";
              switch (status) {
                case "sent":
                  string = "Enviado";
                  break;
                case "undelivered":
                  string = "No enviado";
                  break;
              }
              return string;
            };
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
        });
