angular.module('smstwoway.controllers', [])
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
        .directive('typeSms', function ($compile, contantSmstwoway) {
          return{
            restrict: 'A',
            link: function (scope, element, attr) {
              //console.log(attr);
              var template = "Tipo: {{statusType}}";
              switch (attr.typeSms) {
                case contantSmstwoway.cases.contact:
                  scope.statusType = contantSmstwoway.statusType.contact;
                  break;
                case contantSmstwoway.cases.csv:
                  scope.statusType = contantSmstwoway.statusType.csv;
                  break;
                case contantSmstwoway.cases.speedSent:
                  scope.statusType = contantSmstwoway.statusType.sentSpeed;
                  break;
                default:
                  break;
              }

              element.html(template);
              $compile(element.contents())(scope);
            }
          }
        })
        .directive('statusSms', function ($compile, contantSmstwoway) {
          return{
            scope: {statusSms: '@'},
            restrict: 'A',
            link: function (scope, element, attr) {
              //console.log(scope);
              var template = "Estado: <span class='{{classStatus}}'>{{tralateSms}}<span>";
              scope.chaneStatus = function () {
                switch (scope.statusSms) {
                  case contantSmstwoway.cases.draft:
                    scope.classStatus = contantSmstwoway.classSt.colorDraft;
                    scope.tralateSms = contantSmstwoway.smsTranslate.borrador;
                    break;
                  case contantSmstwoway.cases.scheduled:
                    scope.classStatus = contantSmstwoway.classSt.colorScheduled;
                    scope.tralateSms = contantSmstwoway.smsTranslate.programado;
                    break;
                  case contantSmstwoway.cases.pending:
                    scope.classStatus = contantSmstwoway.classSt.colorPending;
                    scope.tralateSms = contantSmstwoway.smsTranslate.pendiente;
                    break;
                  case contantSmstwoway.cases.sending:
                    scope.classStatus = contantSmstwoway.classSt.colorSending;
                    scope.tralateSms = contantSmstwoway.smsTranslate.enProcesoDeEnvio;
                    break;
                  case contantSmstwoway.cases.sent:
                    scope.classStatus = contantSmstwoway.classSt.colorSent;
                    scope.tralateSms = contantSmstwoway.smsTranslate.enviado;
                    break;
                  case contantSmstwoway.cases.paused:
                    scope.classStatus = contantSmstwoway.classSt.colorPaused;
                    scope.tralateSms = contantSmstwoway.smsTranslate.pausado;
                    break;
                  case contantSmstwoway.cases.canceled:
                    scope.classStatus = contantSmstwoway.classSt.colorCanceled1;
                    scope.tralateSms = contantSmstwoway.smsTranslate.cancelado;
                    break;
                  case contantSmstwoway.cases.undelivered:
                    scope.classStatus = contantSmstwoway.classSt.colorCanceled2;
                    scope.tralateSms = contantSmstwoway.smsTranslate.noEnviado;
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
        .directive("fileread", [function (contantSmstwoway) {
            return {
              scope: {
                fileread: contantSmstwoway.equalFR.equalFileRead
              },
              link: function (scope, element, attributes) {
                element.bind("change", function (changeEvent) {
                  var reader = new FileReader();
                  reader.onload = function (loadEvent) {
                    scope.$apply(function () {
                      scope.fileread = loadEvent.target.result;
                    });
                  }
                  reader.readAsDataURL(changeEvent.target.files[0]);
                });
              }
            }
          }])
        .directive('fileModel', ['$parse', function ($parse, contantSmstwoway) {
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
        .controller('tools', ['$scope', function ($scope) {}])
        .controller('main', ['$scope', '$state', 'contantSmstwoway', 'restservices', '$q', 'notificationService', 'socket', function ($scope, $state, contantSmstwoway, restservices, $q, notificationService, socket) {
            /**
             * aray de los sockets que se usan en nod.js
             */
            $scope.sockets = {
              cancelSent: function () {
                socket.emit(contantSmstwoway.sckts.stopSmsTwoway, {idSmsTwoway: $scope.node.idSmsTwoway, func: contantSmstwoway.funcNode.canc});
              },
              pausedSent: function () {

                socket.emit(contantSmstwoway.sckts.pausedSmsTwoway, {idSmsTwoway: $scope.node.idSmsTwoway, func: contantSmstwoway.funcNode.paus});
              },
              resumenSent: function () {
                socket.emit(contantSmstwoway.sckts.resumeSmsTwoway, {idSmsTwoway: $scope.node.idSmsTwoway, func: contantSmstwoway.funcNode.sche});
              },
              executeFunction: function () {
                // switch ($scope.node.status) {
                //   case contantSmstwoway.casesNode.cancel:
                //     $scope.sockets.cancelSent();
                //     break;
                //   case contantSmstwoway.casesNode.pau:
                //     $scope.sockets.pausedSent();
                //     break;
                //   case contantSmstwoway.casesNode.contin:
                //     $scope.sockets.resumenSent();
                //     break;
                // }
                restservices.changestatus($scope.node.idSmsTwoway, $scope.node.status).then(function () {
                  $scope.functions.clseModal();
                  $scope.services.getInitial();
                });
              }
            }

            $scope.validateDate = function (data) {
              restservices.validateDate(data).then(function () {}).catch(function () {})
            }

            /**
             * llamado al soked que escucha las respuestas de nod.js
             */
            socket.on(contantSmstwoway.sckts.refreshViewSmsTwoWay, function (data) {
              for (i in $scope.misc.smstwoway.items) {
                if ($scope.misc.smstwoway.items[i].idSmsTwoway == data.idSms) {
                  $scope.misc.smstwoway.items[i].status = data.status;
                }
              }
            });

            socket.on(contantSmstwoway.sckts.processSmsTwoWay, function (data) {
              $scope.functions.responseNode(data);
            });
            /**
             * array de funciones que se utilizan en el front net 
             */
            $scope.functions = {
              searchcategory: function () {
                if ($scope.data.filter.category.length >= contantSmstwoway.values.messages.initValueOne) {
                  $scope.services.getInitial();
                } else {
                  $scope.services.getInitial();
                }
              },
              getCategory: function () {
                restservices.getCategory().then(function (data) {
                  $scope.smsCategory = data;
                });
              },
              setAll: function (data) {
                $scope.misc.smstwoway = data;
              },
              forward: function () {

                $scope.misc.initial += contantSmstwoway.values.messages.initValueOne;
                $scope.misc.page += contantSmstwoway.values.messages.initValueOne;
                $scope.services.getInitial();
              },
              fastforward: function () {

                $scope.misc.initial = ($scope.misc.smstwoway.total_pages - contantSmstwoway.values.messages.initValueOne);
                $scope.misc.page = $scope.misc.smstwoway.total_pages;
                $scope.services.getInitial();
              },
              backward: function () {

                $scope.misc.initial -= contantSmstwoway.values.messages.initValueOne;
                $scope.misc.page -= contantSmstwoway.values.messages.initValueOne;
                $scope.services.getInitial();
              },
              fastbackward: function () {

                $scope.misc.initial = contantSmstwoway.values.messages.initValueZero;
                $scope.misc.page = contantSmstwoway.values.messages.initValueOne;
                $scope.services.getInitial();
              },
              search: function () {
                $scope.services.getInitial();
              },
              initVariable: function () {
                $scope.data = {};
                $scope.node = {};
                $scope.misc = {};
                $scope.misc.initial = 0;
                $scope.misc.page = 1;
                $scope.data.filter = {category: []};
              },
              openModal: function (id, status) {
                $scope.node.status = status;
                $scope.node.idSmsTwoway = id;
                $('#cancelDialog').addClass('dialog--open');
              },
              clseModal: function () {
                $('#cancelDialog').removeClass('dialog--open');
              },
              responseNode: function (data) {
                if (data.response) {
                  $scope.functions.clseModal();
                  switch (data.type) {
                    case contantSmstwoway.caseResponseNode.caseOne:
                      notificationService.warning(data.response);
                      break;
                    case contantSmstwoway.caseResponseNode.caseTwo:
                      notificationService.info(data.response);
                      break;
                    case contantSmstwoway.caseResponseNode.caseThree:
                      notificationService.success(data.response);
                      break;
                  }
                }
              }
            };
            /**
             * array de funciones que haran peticiones al services
             */
            $scope.services = {
              getInitial: function () {
                var arrInitialPeticion = [restservices.getAllSmsTwoway($scope.misc.initial, $scope.data.filter)];
                $q.all(arrInitialPeticion).then(function (data) {
                  $scope.functions.setAll(data[0].data);
                }).catch(function (error) {
                  notificationService.error(error.message);
                })
              }
            };
            /**
             * observador para filtros de fecha
             */
            $scope.$watch('[data.filter.dateinitial,data.filter.dateend]', function () {

              if (angular.isDefined($scope.data.filter.dateinitial) & angular.isDefined($scope.data.filter.dateend)) {
                //console.log($scope.data.filter);
                $scope.services.getInitial();
              }
            });
            //ELIMINAR SMS2WAY
            $scope.cancelSms2way = function (status) {
              restservices.changestatus($scope.node.idSmsTwoway, status).then(function () {
                $scope.functions.clseModal();
                $scope.services.getInitial();
              });

            };
            $scope.functions.initVariable();
            $scope.functions.getCategory();
            $scope.services.getInitial();


          }])
        .controller('speedsent', ['$scope', '$state', 'contantSmstwoway', 'restservices', 'notificationService', '$q', '$window', '$stateParams', 'misc', function ($scope, $state, contantSmstwoway, restservices, notificationService, $q, $window, $stateParams, misc) {

            //Carga JQuery
            $('.toggle-sms-two-way').bootstrapToggle({
              on: contantSmstwoway.toggleSmsTwoway.toggOn,
              off: contantSmstwoway.toggleSmsTwoway.toggOff,
              onstyle: contantSmstwoway.toggleSmsTwoway.toggOnStyle,
              offstyle: contantSmstwoway.toggleSmsTwoway.toggOffStyle,
              size: contantSmstwoway.toggleSmsTwoway.toggSize
            });
            $.fn.datetimepicker.defaults = {
              maskInput: false,
              pickDate: true,
              pickTime: true,
              startDate: new Date()
            };
            $('.datetimepicker').datetimepicker({
              format: contantSmstwoway.dTPicker.frmt,
              language: contantSmstwoway.dTPicker.lng
            });

            //Universal functions
            $scope.functions = {
              addResponse: function () {
                $scope.data.typeResponse.push({});
              },
              deleteResponse: function (index) {
                console.log(index);
                $scope.data.typeResponse.splice(index, 1);
              },
              validate: function () {
                if ($stateParams.idSmsTwoway) {
                  $scope.data.idSmsTwoway = $stateParams.idSmsTwoway;
                }
                misc.validationGeneral($scope.data).then(function () {
                  var breaks = (angular.isDefined($scope.data.receiver)) ? $scope.data.receiver.split('\n') : [];
                  if (!$scope.data.receiver) {
                    slideOnTop(contantSmstwoway.error.messages.msgDestinataries, contantSmstwoway.milliSeconds.threeThousand, contantSmstwoway.slideOnTop.classSlide, contantSmstwoway.classToogle.danger);
                    return;
                  } else if ($scope.data.receiver) {
                    $scope.misc.smsCount = breaks.length;
                    for (var i = 0; i < breaks.length; i++) {
                      if (!breaks[i].includes(";")) {
                        slideOnTop(contantSmstwoway.error.messages.msgValidatePuntoYComa, contantSmstwoway.milliSeconds.threeThousand, contantSmstwoway.slideOnTop.classSlide, contantSmstwoway.classToogle.danger);
                        return;
                      }
                      var smscontent = breaks[i].split(";");
                      if (!Number(smscontent[0])) {
                        slideOnTop(contantSmstwoway.error.messages.msgIndicative, contantSmstwoway.milliSeconds.threeThousand, contantSmstwoway.slideOnTop.classSlide, contantSmstwoway.classToogle.danger);
                        return;
                      }

                      if (!Number(smscontent[1])) {
                        slideOnTop(contantSmstwoway.error.messages.msgValidatePhone, contantSmstwoway.milliSeconds.threeThousand, contantSmstwoway.slideOnTop.classSlide, contantSmstwoway.classToogle.danger);
                        return;
                      }
                      if (angular.isUndefined(smscontent[2]) || smscontent[2].length <= 0 || smscontent[2].length > contantSmstwoway.values.messages.msgMaxCharacters) {
                        slideOnTop(contantSmstwoway.error.messages.msgMaxCharacters, contantSmstwoway.milliSeconds.threeThousand, contantSmstwoway.slideOnTop.classSlide, contantSmstwoway.classToogle.danger);
                        return;
                      }
//                      else {
//                        var patternAccents = contantSmstwoway.patterns.accentsMsgDestinataries;
//                        if (patternAccents.test(smscontent[2])) {
//                          slideOnTop(contantSmstwoway.error.messages.msgValidateInvalidCharacters, contantSmstwoway.milliSeconds.fourThousand, contantSmstwoway.slideOnTop.classSlide, contantSmstwoway.classToogle.warning);
//                          return;
//                        }
//                      }
                    }
                    if (breaks.length > contantSmstwoway.values.messages.msgMaxDestinatariesValue) {
                      slideOnTop(contantSmstwoway.error.messages.msgMaxDestinataries, contantSmstwoway.milliSeconds.threeThousand, contantSmstwoway.slideOnTop.classSlide, contantSmstwoway.classToogle.danger);
                      return;
                    }
                  }
                  openModal();
                }).catch(function (message) {
                  slideOnTop(message, 3000, contantSmstwoway.slideOnTop.classSlide, contantSmstwoway.classToogle.danger);
                })

              },
              initVariable: function () {
                //Universal Data
                $scope.data = {};
                //set misc
                $scope.misc = {};
                //set Response array 
                $scope.data.typeResponse = contantSmstwoway.misc.typeResponseInit;
                //set default gmt(bogota)
                $scope.data.gmt = contantSmstwoway.misc.timeZn;
                //set arrayNumber misc
                $scope.misc.numberSending = new Array(60);
                //set Limit typeResponse
                $scope.misc.limitTypeResponse = contantSmstwoway.misc.limitTypeResponse;
                //set min typeResponse
                $scope.misc.minTypeResponse = contantSmstwoway.misc.minTypeResponse;
                //set timeFormats 
                $scope.misc.timeFormats = [{
                    value: contantSmstwoway.misc.minValue,
                    name: contantSmstwoway.misc.minName
                  },
                  {
                    value: contantSmstwoway.misc.hourValue,
                    name: contantSmstwoway.misc.hourName
                  },
                  {
                    value: contantSmstwoway.misc.dayValue,
                    name: contantSmstwoway.misc.dayName
                  },
                  {
                    value: contantSmstwoway.misc.weekValue,
                    name: contantSmstwoway.misc.weekName
                  },
                  {
                    value: contantSmstwoway.misc.monthValue,
                    name: contantSmstwoway.misc.monthName
                  }
                ];
              },

              setCategory: function (data) {
                for(i=0; i< data.length; i++){
                  if(data[i]["idSmsCategory"] == $scope.data.category){
                    $scope.data.category = data[i];
                  }
                }
                $scope.misc.listCategory = data;
              },
              setTimezone: function (data) {
                for(i=0; i< data.length; i++){
                  if(data[i]["gmt"] == $scope.data.gmt){
                    $scope.data.gmt = data[i];
                  }
                }
                $scope.misc.listTimezone = data;
              },
            };
            //Functions RestServices
            $scope.restServicesFunction = {
              getAllSmsTwoway: function (data) {
                $scope.misc.listSmsTwoway = data;
              },
              getInitial: function () {
                $scope.functions.initVariable();
                $scope.restServicesFunction.getAllEdit();
                var arrInitialPeticion = [restservices.getCategory(), restservices.getlisttimezone()];
                $q.all(arrInitialPeticion).then(function (data) {
                  $scope.functions.setCategory(data[0]);
                  $scope.functions.setTimezone(data[1].data);
                }).catch(function (error) {})
              },
              setSmsTwoway: function (data) {
                $scope.data.smstwoway = data;
              },
              createLoteTwoway: function () {
                $scope.data.response = JSON.stringify({
                  typeResponse: $scope.data.typeResponse
                });
                restservices.create($scope.data).then(function (data) {
                  closeModal();
                  var route = $window.myBaseURL + contantSmstwoway.routing.smstwowayRoute;
                  $window.location.href = route;
                  slideOnTop(contantSmstwoway.error.messages.msgEnvioExitoso, contantSmstwoway.milliSeconds.threeThousand, contantSmstwoway.slideOnTop.classSlide, contantSmstwoway.classToogle.success);
                }).catch(function (res) {
                  slideOnTop(res.message, contantSmstwoway.milliSeconds.threeThousand, contantSmstwoway.slideOnTop.classSlide, contantSmstwoway.classToogle.danger);
                });
              },
              getAllEdit: function () {
                if ($stateParams.idSmsTwoway) {
                  restservices.getDataAll($stateParams.idSmsTwoway).then(function (data) {
                    $scope.data = data;
                    $scope.data.category = data["category"];
                    $scope.data.gmt = data["gmt"];
                    $scope.data.typeResponse = data.typeResponse.typeResponse;
                  }).catch(function (data) {
                  })
                }
              },
              cancel: function () {
                if ($stateParams.idSmsTwoway) {
                  restservices.cancelEdit($stateParams.idSmsTwoway).then(function (data) {
                    var route = $window.myBaseURL + contantSmstwoway.routing.smstwowayRoute;
                    $window.location.href = route;
                  }).catch(function (data) {
                  })
                } else {
                  var route = $window.myBaseURL + contantSmstwoway.routing.smstwowayRoute;
                  $window.location.href = route;
                }
              },

            }
            $scope.restServicesFunction.getInitial();
          }])
        .controller('createdcontact', ['$scope', '$state', 'contantSmstwoway', 'restservices', '$q', '$window', '$stateParams', function ($scope, $state, contantSmstwoway, restservices, $q, $window, $stateParams) {
            console.log("contact");
            //Carga JQuery
            $('.toggle-sms-two-way').bootstrapToggle({
              on: contantSmstwoway.toggleSmsTwoway.toggOn,
              off: contantSmstwoway.toggleSmsTwoway.toggOff,
              onstyle: contantSmstwoway.toggleSmsTwoway.toggOnStyle,
              offstyle: contantSmstwoway.toggleSmsTwoway.toggOffStyle,
              size: contantSmstwoway.toggleSmsTwoway.toggSize
            });
            $.fn.datetimepicker.defaults = {
              maskInput: false,
              pickDate: true,
              pickTime: true,
              startDate: new Date()
            };
            $('.datetimepicker').datetimepicker({
              format: contantSmstwoway.dTPicker.frmt,
              language: contantSmstwoway.dTPicker.lng
            });

            $scope.countContactsApproximate = {};

            $scope.functions = {
              countContacts: function () {

                var type = '';
                if ($scope.data.listSelected == 1) {
                  type = contantSmstwoway.clType.typeContactList;
                } else {
                  type = contantSmstwoway.clType.typeSegment;
                }
                if (angular.isUndefined($scope.data.arrAddressee) || $scope.data.arrAddressee == 0) {
                  $scope.countContactsApproximate.counts = 0;
                  return;
                }
                var data = {
                  type: type,
                  segment: $scope.data.arrAddressee,
                  contactlist: $scope.data.arrAddressee
                };
                $scope.restServicesFunction.getCountContac(data);
              },
              addTag: function (tag) {

                if (tangular.isUndefined($scope.data.message)) {
                  $scope.data.message = "";
                  $scope.data.message += tag;
                } else {
                  $scope.data.message += " " + tag;
                }

                $scope.functions.validateInLine();
              },
              smstemplate: function (data) {
                $scope.restServicesFunction.getSmsTemplate();
              },
              timeSender: function () {
                for (var i = 1; i <= 60; i++) {
                  $scope.timeSender[i] = i;
                }
                console.log($scope.timeSender);
              },
              addResponse: function () {
                $scope.data.typeResponse.push({});
              },
              deleteResponse: function (index) {
                console.log(index);
                $scope.data.typeResponse.splice(index, 1);
              },
              validate: function () {

                $scope.error = 0;
                $scope.success = 0;
                var flag = true;
                $scope.arrError = [{}];
                try {
                  if (!$scope.data.sentNow || $scope.data.sentNow == false) {
                    if ($scope.data.timezone == "" || angular.isUndefined($scope.data.timezone) || !$scope.data.timezone) {
                      throw("Debe seleccionar una zona horaria.");
                    }
                    if (document.getElementById("datesend").value == "undefined" || document.getElementById("datesend").value == "") {
                      throw("Debe agregar una fecha y hora de envio.");
                    }
                  }
                  if ($scope.data.email) {
                    var email = $scope.data.email.split(",");
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
                  if (!$scope.data.idSmsCategory) {
                    throw("Debes seleccionar una categoria");
                  }
                  if (angular.isUndefined($scope.data.listSelected)) {
                    throw("Debe seleccionar una lista de destinatarios");
                  }
                  if (angular.isUndefined($scope.data.arrAddressee) || $scope.data.arrAddressee == 0) {
                    throw ("Debe seleccionar al menos un segmento o lista de contacto.");
                  }
                  var re = /[Ã±Ã‘Ã¡Ã©Ã­Ã³ÃºÃÃ‰ÃÃ“ÃšÂ¿Â¡Â´]/;
                  if (angular.isUndefined($scope.data.message) || $scope.data.message.length < 2) {
                    throw ("El mensaje es obligatorio y debe ser mayor a dos caracteres.");
                  }
                  if (re.test($scope.data.message)) {
                    throw ("Hay caracteres no válidos en el mensaje");
                  }
                  if ($scope.data.typeResponse.length == 0) {
                    throw ("Debe agregar minimo una respuesta con su respectiva homologacion.");
                  } else {
                    for (var key in $scope.data.typeResponse) {
                      if ((!$scope.data.typeResponse[key].homologate || $scope.data.typeResponse[key].homologate == "") || (!$scope.data.typeResponse[key].response || $scope.data.typeResponse[key].response == "")) {
                        throw ("Debe agregar minimo una respuesta con su respectiva homologacion.");
                      } else {
                        break;
                      }
                    }
                  }
                  $scope.functions.openModal();
                } catch ($err) {
                  slideOnTop($err, 3000, "glyphicon glyphicon-info-sign", "danger");
                }
              },
              openPreview: function () {
                $("#preview").addClass('dialog--open');
              },
              closePreview: function () {
                $("#preview").removeClass('dialog--open');
              },
              validateInLine: function () {
                $scope.misc.invalidCharacters = false;
                $scope.misc.existTags = false;
                $scope.misc.taggedMessage = $scope.data.message;
                $scope.misc.newMessage = $scope.data.message;
                var re = /[Ã±Ã‘Ã¡Ã©Ã­Ã³ÃºÃÃ‰ÃÃ“ÃšÂ¿Â¡Â´]/;
                var tags = /%%+[a-zA-Z0-9_]+%%/;
                var count = 0;
                if (re.test($scope.data.message)) {
                  $scope.misc.invalidCharacters = true;
                }
                if (tags.test($scope.data.message)) {
                  $scope.misc.existTags = true;
                  $scope.misc.taggedMessage = "";
                  $scope.misc.newMessage = "";

                  var words = $scope.data.message.split(" ");
                  for (var cont = 0; cont < words.length; cont++) {
                    var word = words[cont];
                    var word2 = words[cont];
                    if (word.substr(0, 2) == "%%" && (word.substr(-2) == "%%" || word.substr(-3) == "%%," || word.substr(-3) == "%%." || word.substr(-3) == "%%;")) {
                      word = word.substr(2);
                      word2 = "";
                      word = "<b><i>" + word;
                      if (word.substr(-2) == "%%") {
                        word = word.substr(0, word.length - 2);
                      } else if (word.substr(-3) == "%%," || word.substr(-3) == "%%." || word.substr(-3) == "%%;") {
                        word = word.substr(0, word.length - 3);
                      }
                      word = word + "</i></b>";
                      count = count + word.length;
                    }
                    $scope.misc.taggedMessage += word + " ";
                    $scope.misc.newMessage += word2 + " ";
                  }
                }
              },
              openModal: function () {
                $('.dialog').addClass('dialog--open');
              },
              closeModal: function () {
                $('.dialog').removeClass('dialog--open');
              },
              createcontact: function () {
                $scope.data.originalDate = angular.copy(document.getElementById("datesend").value);
                $scope.data.idSmsTwoway = $stateParams.idSmsTwoway;
                var target = {};
                if ($scope.data.listSelected == 1) {
                  target = {type: 'contactlist', contactlists: $scope.data.arrAddressee};
                } else {
                  target = {type: 'segment', segment: $scope.data.arrAddressee};
                }
                $scope.data.receiver = target;
                $scope.data.target = $scope.countContactsApproximate.counts;
                $scope.restServicesFunction.createContact($scope.data);
              },
              setEdit: function () {
                if ($stateParams.idSmsTwoway) {
                  $scope.restServicesFunction.getEdit($stateParams.idSmsTwoway);
                }
              },
              useTemplate: function () {
                angular.forEach($scope.misc.listfullsmstemplate, function (value, key) {
                  if (value.idSmsTemplate == $scope.data.idSmsTemplate) {
                    $scope.functions.addTag(value.content);
                  }
                });
              },
              initializeVariables: function () {
                //Universal Data
                $scope.data = {};
                $scope.data.gmt = "-0500";
                //set misc
                $scope.misc = {};
                $scope.misc.listAddressee = [{id: 1, name: "Lista de contactos"}, {id: 2, name: "Segmentos"}];
                //set Response array 
                $scope.data.typeResponse = [];
                $scope.misc.timeFormats = [
                  {value: 'minute', name: "Minuto(s)"},
                  {value: 'hour', name: "Hora(s)"},
                  {value: 'day', name: "Día(s)"},
                  {value: 'week', name: "Semana(s)"},
                  {value: 'month', name: "Mes(es)"}
                ];
                $scope.misc.arrAddressee = [];
                $scope.viewTemplate = false;
                $scope.timeSender = [];
              },
              changeTitle: function () {
                if ($stateParams.idSmsTwoway) {
                  $scope.misc.title = "Editar SMS TWOWAY por contacto o segmentos.";
                } else {
                  $scope.misc.title = "Crear SMS TWOWAY por contacto o segmentos.";
                }
              }
            };
            $scope.restServicesFunction = {
              getDetinatary: function (list) {
                $scope.misc.typeDestin = false;
                $scope.showAddressee = false;
                if (list == 1) {
//                  let arrDestinatarylist = restservices.getcontactlist();
                  restservices.getcontactlist().then(function (data) {
                    $scope.showAddressee = true;
                    $scope.misc.listAllAddressee = data;
                  }).catch(function (error) {})
                } else {
//                  let arrDestinataryseg = restservices.getsegments();
                  restservices.getsegments().then(function (data) {
                    $scope.showAddressee = true;
                    $scope.misc.listAllAddressee = data;
                  }).catch(function (error) {})
                }
              },
              setCategory: function (data) {
                $scope.misc.listCategory = data;
              },
              getInitial: function () {
                var arrInitialPeticion = [restservices.getCategory(), restservices.getlisttimezone()];
                $q.all(arrInitialPeticion).then(function (data) {
                  $scope.restServicesFunction.setCategory(data[0]);
                  $scope.restServicesFunction.setTimezone(data[1].data);
                }).catch(function (error) {})
              },
              setTimezone: function (data) {
                $scope.misc.listTimezone = data;
                $scope.data.timezone = "-0500";
              },
              getCountContac: function (data) {
                restservices.countContact(data).then(function (data) {
                  $scope.countContactsApproximate = data;
                }).catch(function (error) {
                })
              },
              getSmsTemplate: function () {
                restservices.listfullsmstemplate().then(function (data) {
                  $scope.misc.listfullsmstemplate = data;
                }).catch(function (error) {

                })
              },
              createContact: function (data) {

                restservices.savesmstwowaycontact(data).then(function (data) {
                  $scope.functions.closeModal();
                  $window.location.href = contantSmstwoway.urlPeticion.indexSmsTwoway;
                }).catch(function (error) {

                })
              },
              getEdit: function (data) {
                restservices.getEdit(data).then(function (data) {
                  $scope.restServicesFunction.getDetinatary(data.listSelected);
                  $scope.data = data;
                  $scope.functions.countContacts();
                }).catch(function (error) {

                })
              }
            };

            $scope.restServicesFunction.getInitial();
            $scope.functions.setEdit();
            $scope.functions.initializeVariables();
            $scope.functions.changeTitle();
          }])
        .controller('csv', ['$scope', '$state', 'contantSmstwoway', 'restservices', '$q', 'socket', '$stateParams', 'notificationService', 'misc', function ($scope, $state, contantSmstwoway, restservices, $q, socket, $stateParams, notificationService, misc) {
            $scope.validateCheckInternational = false;
            //Carga JQuery
            $('.toggle-sms-two-way').bootstrapToggle({
              on: contantSmstwoway.toggleSmsTwoway.toggOn,
              off: contantSmstwoway.toggleSmsTwoway.toggOff,
              onstyle: contantSmstwoway.toggleSmsTwoway.toggOnStyle,
              offstyle: contantSmstwoway.toggleSmsTwoway.toggOffStyle,
              size: contantSmstwoway.toggleSmsTwoway.toggSize
            });

            $.fn.datetimepicker.defaults = {
              maskInput: false,
              pickDate: true,
              pickTime: true,
              startDate: new Date()
            };

            $('.datetimepicker').datetimepicker({
              format: contantSmstwoway.dTPicker.frmt,
              language: contantSmstwoway.dTPicker.lng
            });

            //Universal Data
            $scope.data = {};
            //set misc
            $scope.misc = {};
            $scope.misc.ProccessCsv = {};


            //set SentNow
            $scope.data.dateNow = false;

            //set optionsAvanced
            $scope.data.advancedoptions = false;

            //set sendNotification
            $scope.data.notification = false;

            //set divideSendign
            $scope.data.divide = false;

            //set Response array 
            $scope.data.typeResponse = contantSmstwoway.misc.typeResponseInit;
            //set default gmt(bogota)
            $scope.data.gmt = contantSmstwoway.misc.timeZn;
            //set Limit typeResponse
            $scope.misc.limitTypeResponse = contantSmstwoway.misc.limitTypeResponse;
            //set min typeResponse
            $scope.misc.minTypeResponse = contantSmstwoway.misc.minTypeResponse;

            //set arrayNumber misc
            $scope.misc.numberSending = new Array(60);

            //set timeFormats 
            $scope.misc.timeFormats = [
              {value: contantSmstwoway.misc.minValue, name: contantSmstwoway.misc.minName},
              {value: contantSmstwoway.misc.hourValue, name: contantSmstwoway.misc.hourName},
              {value: contantSmstwoway.misc.dayValue, name: contantSmstwoway.misc.dayName},
              {value: contantSmstwoway.misc.weekValue, name: contantSmstwoway.misc.weekName},
              {value: contantSmstwoway.misc.monthValue, name: contantSmstwoway.misc.monthName}
            ];
            
            $scope.ValidateCheckInter = function (){
                if($scope.validateCheckInternational == true){
                    slideOnTop("Lo sentimos, la cuenta no tiene habilitado envios internacionales, si deseas habilitarlo comunicate con soporte", 3000, "glyphicon glyphicon-info-sign", "danger");    
                $scope.data.international = false;
                }
                
            }
            //Universal functions
            $scope.functions = {

              optionsAvanced: function () {
                if (!$scope.data.advancedoptions || angular.isUndefined($scope.data.advancedoptions)) {
                  $scope.data.advancedoptions = true;
                } else {
                  $scope.data.advancedoptions = false;
                }
              },
              sentNow: function () {
                if (!$scope.data.dateNow || angular.isUndefined($scope.data.dateNow)) {
                  $scope.data.dateNow = true;
                } else {
                  $scope.data.dateNow = false;
                }
              },
              sendNotification: function () {
                if (!$scope.data.notification || angular.isUndefined($scope.data.notification)) {
                  $scope.data.notification = true;
                } else {
                  $scope.data.notification = false;
                }
              },
              divideSending: function () {
                if (!$scope.data.divide || angular.isUndefined($scope.data.divide)) {
                  $scope.data.divide = true;
                } else {
                  $scope.data.divide = false;
                }
              },
              addResponse: function () {
                $scope.data.typeResponse.push({});
              },
              deleteResponse: function (index) {
                console.log($scope.data.typeResponse);
                $scope.data.typeResponse.splice(index, 1);
              },
              validate: function () {
                misc.validationGeneral($scope.data).then(function (data) {
                  if (angular.isUndefined($scope.data.csv) && angular.isUndefined($scope.data.idSmsTwoway)) {
                    slideOnTop(contantSmstwoway.error.messages.msgCsv, contantSmstwoway.milliSeconds.threeThousand, contantSmstwoway.slideOnTop.classSlide, contantSmstwoway.classToogle.danger);
                    return;
                  }
                  if($scope.data.international == true && angular.isUndefined($scope.data.idcountry)){
                    slideOnTop("Seleccione un pais para el envio internacional", contantSmstwoway.milliSeconds.threeThousand, contantSmstwoway.slideOnTop.classSlide, contantSmstwoway.classToogle.danger);
                    return;
                  }
                  $scope.functions.openModal();
                }).catch(function (message) {
                  slideOnTop(message, contantSmstwoway.milliSeconds.threeThousand, contantSmstwoway.slideOnTop.classSlide, contantSmstwoway.classToogle.danger);
                });
              },
              openModal: function () {
                $('#ProcessCsv').addClass('dialog--open');
              },
              openModalEdit: function () {
                $('#editCsv').addClass('dialog--open');
              },
              closeModalEdit: function () {
                $('#editCsv').removeClass('dialog--open');
              },
              setDataEdit: function (data) {
                $scope.misc.edit = true;
                $scope.data.category = data["idSmsCategory"];
                $scope.data.gmt = data["gmt"];
                angular.forEach(data, function (value, key) {
                  if (key == 'typeResponse') {
                    var typeResponse = JSON.parse(value);
                    $scope.data[key] = typeResponse.typeResponse;
                  } else {
                    $scope.data[key] = value;
                  }
                });
              },
              senitize: function (data, callback) {
                angular.forEach(data, function (value, key) {
                  if (contantSmstwoway.misc.arrBoolean.indexOf(value) >= 0) {
                    data[key] = (value == contantSmstwoway.valueSenitizeCsv.valuSC) ? true : false;
                  }
                });
                callback(data);
              }
            };
            //Functions RestServices
            $scope.restServicesFunction = {
              getAllSmsTwoway: function (data) {
                $scope.misc.listSmsTwoway = data;
              },
              getInitial: function () {
                var arrInitialPeticion = [restservices.getCategory(), restservices.getlisttimezone()];
                $q.all(arrInitialPeticion).then(function (data) {
                  $scope.restServicesFunction.setCategory(data[0]);
                  $scope.restServicesFunction.setTimezone(data[1].data);
                }).catch(function (error) {})
              },
              setCategory: function (data) {
                for(i=0; i< data.length; i++){
                  if(data[i]["idSmsCategory"] == $scope.data.category){
                    $scope.data.category = data[i];
                  }
                }
                $scope.misc.listCategory = data;
              },
              setTimezone: function (data) {
                for(i=0; i< data.length; i++){
                  if(data[i]["gmt"] == $scope.data.gmt){
                    $scope.data.gmt = data[i];
                  }
                }
                $scope.misc.listTimezone = data;
              },
              setAvalaibleCountry: function (){
                restservices.getAvalaibleCountry().then(function (data) {                  
                  
                  if(data.result != false){
                    $scope.internationalcountries = data.result;
                  }else{
                    $scope.validateCheckInternational = true;
                  }
                }).catch(function () {

                });
              },
              setSmsTwoway: function (data) {
                $scope.data.smstwoway = data;
              },
              createLoteTwoway: function () {
                if (!$stateParams.idSmsTwoway) {
                  $scope.misc.initProcessUpload = true;
                  $scope.misc.ProccessCsv.porc = contantSmstwoway.csvProcess.csvPorc;
                  $scope.data.response = JSON.stringify({typeResponse: $scope.data.typeResponse});
                  if(angular.isUndefined($scope.data.international)){
                    $scope.data.international = false;
                  }
                  //if($scope.data.idcountry != '' || !angular.isUndefined($scope.data.idcountry)){
//                    var tmp = $scope.data.idcountry;
//                    //$scope.data.idcountry = null;
//                    //$scope.data.idcountry; = tmp.idcountry;
//                  }
                  console.log("PASE POR AQUI",$scope.data);   
                  restservices.createcsv($scope.data).then(function (data) {

                  }).catch(function (res) {
                    $scope.misc.initProcessUpload = false;
                    closeModal();
                    slideOnTop(res.message, contantSmstwoway.milliSeconds.threeThousand, contantSmstwoway.slideOnTop.classSlide, contantSmstwoway.classToogle.danger);
                  });
                }else{
                  console.log($scope.data);
                  $scope.data.response = JSON.stringify({
                  typeResponse: $scope.data.typeResponse
                });
                  restservices.editcsv($scope.data).then(function(data){
                  closeModal();
                  $state.go(contantSmstwoway.routing.goState);
                  }).catch(function(res){
                  slideOnTop(res.message, contantSmstwoway.milliSeconds.threeThousand, contantSmstwoway.slideOnTop.classSlide, contantSmstwoway.classToogle.danger);

                    
                  });
                }

              },
              changeStatusCsv: function (status) {
                var idSms = false;
                if (angular.isDefined($scope.data.idSmsTwoway)) {
                  idSms = $scope.data.idSmsTwoway;
                } else {
                  idSms = $scope.misc.idSms;
                }
                restservices.changestatus(idSms, status).then(function () {
                  if (angular.isDefined($scope.data.idSmsTwoway)) {
                    $scope.functions.closeModalEdit();
                  } else {
                    $state.go(contantSmstwoway.routing.goState);
                  }
                }).catch(function () {

                });
              },
              getOneSms: function () {
                restservices.getOne($scope.data.idSmsTwoway).then(function (data) {
                  $scope.functions.senitize(data, $scope.functions.setDataEdit);
//                  $scope.functions.setDataEdit(data);
                }).catch(function (error) {
                  notificationService.error(error.message);
                  $state.go(contantSmstwoway.routing.goState);
                });
              },
              cancel: function () {
                if ($stateParams.idSmsTwoway) {
                  restservices.cancelEdit($stateParams.idSmsTwoway).then(function (data) {
                    var route = $window.myBaseURL + contantSmstwoway.routing.smstwowayRoute;
                    $window.location.href = route;
                  }).catch(function (data) {

                  })
                } else {
                  var route = $window.myBaseURL + contantSmstwoway.routing.smstwowayRoute;
                  $window.location.href = route;
                }

              },
            }

            $scope.socket = {
              searchProcess: function (idSmsTwoway) {
                socket.emit('search-sms-twoway', idSmsTwoway);
              }
            }
            if (angular.isDefined($stateParams.idSmsTwoway) && $stateParams.idSmsTwoway != "") {
              $scope.data.idSmsTwoway = $stateParams.idSmsTwoway;
              $scope.functions.openModalEdit();
              $scope.restServicesFunction.getOneSms();
              $scope.restServicesFunction.getInitial();
            } else {
              $scope.restServicesFunction.getInitial();
              $scope.restServicesFunction.setAvalaibleCountry();
            }

            socket.on('loading-csv-twoway', function (data) {
              if (data.idSubaccount == contantSmstwoway.misc.idSubaccount) {
                if (data.status == contantSmstwoway.statusLoadingCsv.preload) {
                  $scope.misc.ProccessCsv.preload = {};
                  $scope.misc.ProccessCsv.porc += contantSmstwoway.csvProcess.csvPorc;
                }
                if (data.status == contantSmstwoway.statusLoadingCsv.validations) {
                  $scope.misc.ProccessCsv.porc += contantSmstwoway.csvProcess.csvPorc;
                  $scope.misc.ProccessCsv.preload.data = data.data;
                  $scope.misc.ProccessCsv.validations = {};
                }
                if (data.status == contantSmstwoway.statusLoadingCsv.load) {
                  $scope.misc.ProccessCsv.porc += contantSmstwoway.csvProcess.csvPorc;
                  $scope.misc.ProccessCsv.validations.data = data.data;
                  $scope.misc.ProccessCsv.load = {};
                }
                if (data.status == contantSmstwoway.statusLoadingCsv.finish) {
                  $scope.misc.ProccessCsv.porc += contantSmstwoway.csvProcess.csvPorc;
                  $scope.misc.ProccessCsv.load.data = data.data;
                  $scope.misc.ProccessCsv.finish = {};
                  $scope.misc.idSms = data.id;
                  $scope.misc.ProccessCsv.finish.message = data.message;
                }
              }
            });

            socket.on('process-sms-twoway', function (data) {
              $scope.misc.hideSpinner = true;
              $scope.misc.isProcess = false;
            });

          }]);
