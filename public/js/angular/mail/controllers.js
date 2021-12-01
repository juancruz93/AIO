'use strict';
(function () {
  angular.module('mail.controllers', [])
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
          .filter('getById', function () {
            return function (input, id) {
              var i = 0, len = input.length;
              for (; i < len; i++) {
                if (input[i].gmt == id) {
                  return input[i];
                }
              }
              return null;
            }
          })
          .controller('main', ['$scope', 'incomplete', '$rootScope', 'wizard', 'restService', function ($scope, incomplete, $rootScope, wizard, restService) {
              $rootScope.incomplete = incomplete.getincomplete();
              wizard.setdescribe(true);
              wizard.setaddressees(false);
              wizard.setcontent(false);
              wizard.setadvanceoptions(false);
              wizard.setshippingdate(false);
              $rootScope.addressees = wizard.getaddressees();
              $rootScope.describe = wizard.getdescribe();
              $rootScope.content = wizard.getcontent();
              $rootScope.advanceoptions = wizard.getadvanceoptions();
              $rootScope.shippingdate = wizard.getshippingdate();
            }])
          .controller('basicinformationController', ['$scope', '$rootScope', 'restService', '$window', 'notificationService', '$stateParams', 'wizard', function ($scope, $rootScope, restService, $window, notificationService, $stateParams, wizard) {

              wizard.setdescribe(true);
              wizard.setaddressees(false);
              wizard.setcontent(false);
              wizard.setadvanceoptions(false);
              wizard.setshippingdate(false);
              $rootScope.addressees = wizard.getaddressees();
              $rootScope.describe = wizard.getdescribe();
              $rootScope.content = wizard.getcontent();
              $rootScope.advanceoptions = wizard.getadvanceoptions();
              $rootScope.shippingdate = wizard.getshippingdate();
              $scope.data = {};
              $scope.data.category = [];
              function getemailname() {
                restService.getemailname().then(function (res) {
                  $scope.emailname = res;
                });
              }

              function getemailsend() {
                restService.getemailsend().then(function (res) {
                  $scope.emailsend = res;
                });
              }

              function getMailCategory() {
                restService.getMailCategory().then(function (res) {
                  $scope.availibleCategory = res;
                });
              }

              function getReplyTo() {
                restService.getReplyTo().then(function (res) {
                  $scope.replyToArray = res;
                });
              }

              getemailname();
              getemailsend();
              getReplyTo();
              getMailCategory();
              $scope.showInputName = false;
              $scope.showInputCategory = false;
              $scope.showSelectName = true;
              $scope.showCategoryName = true;
              $scope.showIconsName = true;
              $scope.showIconsCategory = true;
              $scope.showIconsSaveName = false;
              $scope.showIconsSaveCategory = false;
              $scope.changeStatusNameCategory = function () {
                if (!$scope.showInputCategory) {
                  $scope.showInputCategory = true;
                  $scope.showCategoryName = false;
                  $scope.showIconsCategory = false;
                  $scope.showIconsSaveCategory = true;
                } else {
                  $scope.showInputCategory = false;
                  $scope.showCategoryName = true;
                  $scope.showIconsCategory = true;
                  $scope.showIconsSaveCategory = false;
                }
              };
              $scope.changeStatusInputName = function () {
                if (!$scope.showInputName) {
                  $scope.showInputName = true;
                  $scope.showSelectName = false;
                  $scope.showIconsName = false;
                  $scope.showIconsSaveName = true;
                } else {
                  $scope.showInputName = false;
                  $scope.showSelectName = true;
                  $scope.showIconsName = true;
                  $scope.showIconsSaveName = false;
                }
              }

              $scope.showInputEmail = false;
              $scope.showSelectEmail = true;
              $scope.showIconsEmail = true;
              $scope.showIconsSaveEmail = false;
              $scope.changeStatusInputEmail = function () {
                if (!$scope.showInputEmail) {
                  $scope.showInputEmail = true;
                  $scope.showSelectEmail = false;
                  $scope.showIconsEmail = false;
                  $scope.showIconsSaveEmail = true;
                } else {
                  $scope.showInputEmail = false;
                  $scope.showSelectEmail = true;
                  $scope.showIconsEmail = true;
                  $scope.showIconsSaveEmail = false;
                }
              }


              $scope.showInputReplyto = false;
              $scope.showSelectReplyto = true;
              $scope.showIconsReplyto = true;
              $scope.showIconsSaveReplyto = false;
              $scope.changeStatusInputReplyto = function () {
                if (!$scope.showInputReplyto) {
                  $scope.showInputReplyto = true;
                  $scope.showSelectReplyto = false;
                  $scope.showIconsReplyto = false;
                  $scope.showIconsSaveReplyto = true;
                } else {
                  $scope.showInputReplyto = false;
                  $scope.showSelectReplyto = true;
                  $scope.showIconsReplyto = true;
                  $scope.showIconsSaveReplyto = false;
                }
              }



              $scope.basicInformationRegister = function () {
                if (typeof ($rootScope.idMailGet) != "undefined") {
                  //console.log($scope.data);
                  if (typeof ($scope.data.senderNameSelect) == 'undefined') {
                    notificationService.error("El Nombre del remitente es obigatorio");
                  } else if (typeof ($scope.data.senderMailSelect) == 'undefined') {
                    notificationService.error("El Correo del remitente es obigatorio");
                  } else {
                    restService.editBasicInformation($rootScope.idMailGet, $scope.data).then(function (res) {
                      notificationService.success(res['msg']);
                      $rootScope.idMailGet = res['idMail'];
                      $window.location.href = fullUrlBase + templateBase + '/create#/addressees/' + res['idMail'];
                    });
                  }
                } else {
                  if (typeof ($scope.data.senderNameSelect) == 'undefined') {
                    notificationService.error("El Nombre del remitente es obigatorio");
                  } else if (typeof ($scope.data.senderMailSelect) == 'undefined') {
                    notificationService.error("El Correo del remitente es obigatorio");
                  } else {
                    restService.addBasicInformation($scope.data).then(function (res) {
                      notificationService.success(res['msg']);
                      $rootScope.idMailGet = res['idMail'];
                      $window.location.href = fullUrlBase + templateBase + '/create#/addressees/' + res['idMail'];
                    });
                  }
                }
              };
              $scope.basicInformationSave = function () {
                if (typeof ($scope.data.senderNameSelect) == 'undefined') {
                  notificationService.error("El Nombre del remitente es obigatorio");
                } else if (typeof ($scope.data.senderMailSelect) == 'undefined') {
                  notificationService.error("El Correo del remitente es obigatorio");
                } else {
                  restService.editBasicInformation($rootScope.idMailGet, $scope.data).then(function (res) {
                    //console.log(res);
                    notificationService.success(res['msg']);
                    $rootScope.idMailGet = res['idMail'];
                    $window.location.href = fullUrlBase + templateBase;
                  });
                }
              };
              $scope.saveCategory = function () {
                var data = {name: $scope.categoryName};
                $scope.changeStatusNameCategory();
                restService.saveCategory(data).then(function (res) {
                  notificationService.primary(res['msg']);
                  $scope.categoryName = "";
                  getMailCategory();
                  $scope.data.category.push(res['category'].idMailCategory);
                });
              };
              $scope.saveName = function () {
                var data = {name: $scope.senderName};
                restService.addEmailName(data).then(function (res) {
                  notificationService.success(res['msg']);
                  $scope.senderName = "";
                  getemailname();
                  $scope.changeStatusInputName();
                  $scope.data.senderNameSelect = res['idNameSender'];
                });
              }

              $scope.saveEmail = function () {
                var data = {email: $scope.senderMail};
                restService.addEmailSender(data).then(function (res) {
                  if (res == "" || res == null) {
                    notificationService.warning('No se pudo guardar el remitente, posiblemente el correo ese demasiado largo, intentelo nuevamente o comuniquese con soporte');
                  } else {
                    notificationService.success(res['msg']);
                    $scope.senderMail = "";
                    getemailsend();
                    $scope.changeStatusInputEmail();
                    $scope.data.senderMailSelect = res['idEmailsender'];
                  }
                });
              }

              $scope.saveReplyto = function () {
                var data = {email: $scope.replyTo};
                restService.addReplyTo(data).then(function (res) {
                  notificationService.success(res['msg']);
                  $scope.replyTo = "";
                  getReplyTo();
                  $scope.changeStatusInputReplyto();
                  $scope.data.replyToSelect = res['idReplyTo'];
                });
              }


              if ($stateParams.id) {
                if (!IsNumeric($stateParams.id)) {
                  notificationService.warning("El correo no se ha encontrado");
                  location.href = '#/basicinformation/';
                } else {
                  $rootScope.idMailGet = $stateParams.id;
                }
              }
              function IsNumeric(val) {
                return Number(parseFloat(val)) == val;
              }

              if ($rootScope.idMailGet && IsNumeric($rootScope.idMailGet)) {
                //        if (typeof ($rootScope.idMailGet) != "undefined" ) {
                restService.findMail($rootScope.idMailGet).then(function (res) {
                  $scope.data.name = res.name;
                  $scope.data.senderNameSelect = res.idNameSender;
                  $scope.data.senderMailSelect = res.idEmailsender;
                  $scope.data.replyToSelect = res.idReplyTo;
                  $scope.data.replyto = res.replyto;
                  $scope.data.subject = res.subject;
                  $scope.data.test = (res.test == 1);
                });
                restService.getMailCategoryByIdMail($rootScope.idMailGet).then(function (res) {
                  var idMailCategory = [];
                  res.forEach(function (item, index) {
                    idMailCategory.push(item.idMailCategory);
                  });
                  $scope.data.category = idMailCategory;
                });
              }
            }])
          .controller('shippingdateController', ['$scope', '$rootScope', '$interval', '$filter', 'restService', '$stateParams', '$q', 'notificationService', 'moment', 'wizard', '$FB', '$timeout', function ($scope, $rootScope, $interval, $filter, restService, $stateParams, $q, notificationService, moment, wizard, $FB, $timeout) {
              if (!IsNumeric($stateParams.id)) {
                notificationService.warning("El correo no se ha encontrado");
                location.href = '#/basicinformation/';
                return true;
              }

              function IsNumeric(val) {
                return Number(parseFloat(val)) == val;
              }
              wizard.setdescribe(false);
              wizard.setaddressees(false);
              wizard.setcontent(false);
              wizard.setadvanceoptions(false);
              wizard.setshippingdate(true);
              $rootScope.addressees = wizard.getaddressees();
              $rootScope.describe = wizard.getdescribe();
              $rootScope.content = wizard.getcontent();
              $rootScope.advanceoptions = wizard.getadvanceoptions();
              $rootScope.shippingdate = wizard.getshippingdate();
              $scope.show = false;
              $scope.data = {};
              $scope.zonahoraria = {};
              $rootScope.idMailGet = $stateParams.id;
              $scope.idMail = $rootScope.idMailGet;
              $scope.imagenDate = new Date();
              $scope.imagenTime = $scope.imagenDate.getTime();
              $scope.tester = {
                mailTester: false,
                emailsSendTester: null,
                messageSendTester: null,
                sendTesterMail: function () {
                  if (this.mailTester) {
                    if (this.emailsSendTester == null || this.emailsSendTester == "") {
                      notificationService.error("El campo de correos de envio de tester no puede estar vacio.");
                      return false;
                    }
                    data = {mailsSend: this.emailsSendTester.split(","), messageSend: this.messageSendTester, idTester: $scope.idMail};
                    restService.sendTesterMails(data, $scope.idMail)
                            .then(function (data) {
                              console.log(data);
                            });
                  }

                }
              };


              $('#datetimepicker').datetimepicker({
                format: 'yyyy-MM-dd hh:mm',
                language: 'es'
              }).on('changeDate', function (ev) {
                $scope.tick();
                $scope.$apply();
              });
              $scope.showTextarea = function () {
                if ($scope.show == false) {
                  $scope.show = true;
                } else {
                  $scope.show = false;
                }
              }
              $scope.loginFB = function (idPage) {
                $FB.getLoginStatus(function (response) {
                  if (response.status === 'connected') {
                    $FB.api('/' + idPage + "?fields=access_token,name", function (response) {
                      $scope.setFacebook(response);
                    });
                  } else {
                    $FB.login(function () {
                      $FB.api('/' + idPage + "?fields=access_token,name", function (response) {
                        $scope.setFacebook(response);
                      });
                    }, {
                      scope: 'publish_actions,publish_pages,manage_pages'
                    });
                  }
                });
              }
              $scope.setFacebook = function (data) {

                $FB.api('/' + data.id + '/picture?redirect=false', function (response) {
                  $scope.$apply(function () {
                    $scope.data.facebook.name = data.name;
                    $scope.data.facebook.picture = response.data.url;
                    $scope.data.facebook.access_token = data.access_token;

                  });
                });
              }

              $scope.publishSocialFacebook = function (data) {
                var post = {};

//                post.url = fullUrlBase + 'asset/thumbnailmail/' + $scope.idMail; //SERVIDOR
                post.url = "https://encrypted-tbn3.gstatic.com/images?q=tbn:ANd9GcQ1dmzDhsPc5yHJ5S6j90DUcoUEayldxlaiFDnhAyzR5YFcozADUxk1dBw";//LOCALHOST
                post.access_token = $scope.data.facebook.access_token;
                post.message = ($scope.data.facebook.description != null) ? $scope.data.facebook.description : "";
                //                if(!angular.equals(null,$scope.data.facebook.description)){
                //                  post.caption = $scope.data.facebook.description; 
                //                }
                console.log("Check -> Caption, me imagino que el comentario en el post");
                console.log(post);
                if (data.socialSend != "now") {
                  post.scheduled_publish_time = data.socialSend;
                  post.published = false;
                }
                post.link = "https://aio.sigmamovil.com/webversion/show/" + "1-" + $scope.data.idMail + "-public-" + $scope.data.codigo + "/";
                $FB.api("/" + $scope.data.facebook.idPage + "/feed",
                        "POST", post,
                        function (response) {
                          if (response && !response.error) {
                            var photoId = response.id;
                            var postId = response.post_id;
                            if (typeof postId == "undefined") {
                              postId = $scope.data.facebook.idPage + "_" + photoId;
                            }
                            restService.sendDataComfirmationEmail(data).then(function (data) {
                              notificationService.primary(data.message);
                              openModalConfirm();
                            }).catch(function (data) {
                              $FB.api("/" + photoId,
                                      "DELETE",
                                      {access_token: $scope.data.facebook.access_token},
                                      function (response) {
                                        if (response && !response.error) {
                                        }
                                      });

                              $FB.api("/" + postId,
                                      "DELETE",
                                      {access_token: $scope.data.facebook.access_token},
                                      function (response) {
                                        if (response && !response.error) {
                                        }
                                      });
                            });
                          } else {
                            notificationService.error("ocurrÃ­o un error haciendo el posteo, por favor validar la sessiÃ³n de facebook.")
                          }
                        });
              }

              if ($stateParams.id) {
                $rootScope.idMailGet = $stateParams.id;
                $scope.idMail = $rootScope.idMailGet;
              }

              restService.getSaxs().then(function (res) {
                for (var i = 0; i < res.length; i++) {
                  if (res[i].service == "email marketing") {
                    $scope.ammoutEmail = {ammount: res[i].amount, accountingMode: res[i].accountingMode};
                  }
                }
              });
              restService.findMail($rootScope.idMailGet).then(function (res) {

                $scope.data = res;
                $scope.data.test = (res.test == "1");
                if (res.scheduleDate != null && res.scheduleDate != "") {
                  $("#valueDatepicker").val(res.scheduleDate);
                  $scope.gmtDataBase = res.gmt;
                  $scope.date = res.scheduleDate;
                  $scope.oldHour = true;
                  restService.getlisttimezone().then(function (res) {
                    $scope.timezone = res;
                    $scope.zonahoraria.zone = $filter('getById')($scope.timezone, res.gmt);
                  });
                } else {
                  restService.getlisttimezone().then(function (res) {
                    $scope.timezone = res;
                    $scope.zonahoraria.zone = $filter('getById')($scope.timezone, "-0500");
                  });
                  $scope.oldHour = false;
                }

                if (res.target != null && res.target != "") {
                  var json = jQuery.parseJSON(res.target);
                  if (json.type == "contactlist") {
                    $scope.data.titleList = 'Lista(s) de contacto(s): ';
                    $scope.data.contacts = json.contactlists;
                  } else if (json.type == "segment") {
                    $scope.data.titleList = 'Segmento(s): ';
                    $scope.data.contacts = json.segment;
                  }
                  //console.log(json);
                }

                restService.getContentMail($rootScope.idMailGet).then(function (resStatics) {
                  if (resStatics.statisticsEmails != "") {
                    $scope.data.statisticsEmails = {statisticsEmails: resStatics.statisticsEmails, quantity: resStatics.quantity, typeTime: resStatics.typeTime};
                  } else {
                    $scope.data.statisticsEmails = false;
                  }

                  if (resStatics.notificationEmails != "") {
                    $scope.data.notificationEmails = resStatics.notificationEmails;
                  } else {
                    $scope.data.notificationEmails = false;
                  }

                  if (resStatics.facebook) {
                    $scope.data.facebook = resStatics.facebook;
                    if ($FB.loaded) {
                      $scope.loginFB($scope.data.facebook.idPage);
                    } else {
                      $timeout(function () {
                        $scope.loginFB($scope.data.facebook.idPage);
                      }, 2000);
                    }
                  } else {
                    $scope.data.facebook = false;
                  }
                })
                restService.findEmailSender(res.idEmailsender).then(function (resEmailSender) {
                  //console.log(resEmailSender);
                  $scope.data.emailSender = resEmailSender.email;
                });
                restService.findEmailName(res.idNameSender).then(function (resEmailname) {
                  //            console.log(resEmailname);
                  $scope.data.nameSender = resEmailname.name;
                });

                if (res.idReplyTo != null && angular.isDefined(res.idReplyTo)) {
                  restService.findReplyto(res.idReplyTo).then(function (resReplyto) {
                    //          console.log(resReplyto.email);
                    $scope.data.replyto = resReplyto.email;
                  });
                } else {
                  $scope.data.replyto = null;
                }


                restService.findMailCategory($rootScope.idMailGet).then(function (res) {
                  $scope.data.category = res;
                });
                restService.findMailAttachment($rootScope.idMailGet).then(function (res) {
                  $scope.data.attachment = res;
                  $scope.data.sizeAttachment = 0;
                  if ($scope.data.attachment.length > 0) {
                    for (var i in $scope.data.attachment) {
                      $scope.data.sizeAttachment = $scope.data.sizeAttachment + parseInt($scope.data.attachment[i].size);
                    }
                  }
                  //console.log(res);
                });
              });
              var dateUnix = moment().utc().valueOf();
              var gmt;
              $scope.changeTestMail = function (test) {
                var data = {};
                if (test) {
                  data.test = 1;
                } else {
                  data.test = 0;
                }

                restService.changeTestMail($rootScope.idMailGet, data).then(function (res) {
                  notificationService.primary("Se actualizo correctamente el correo.");
                })
              }
              $scope.tick = function () {
                var selecteDate = $("#valueDatepicker").val();
                if (!selecteDate.length == 0) {
                  $scope.dateSelectedUnix = moment(selecteDate).utc().valueOf();
                  dateUnix = $scope.dateSelectedUnix;
                }

                if (typeof ($scope.zonahoraria.zone) == "undefined") {
                  gmt = "-0500";
                } else {
                  gmt = $scope.zonahoraria.zone.gmt;
                }

                if (!$scope.oldHour) {
                  $scope.date = moment(dateUnix).utcOffset(gmt).format('YYYY-MM-DD HH:mm');
                  $scope.dateCompare = moment(dateUnix).utcOffset(gmt).add(10, 'minutes').format('YYYY-MM-DD HH:mm');
                }

              }

              $scope.program = function () {
                var gmt;
                var selecteDate = $("#valueDatepicker").val();
                if (selecteDate.length == 0) {
                  notificationService.error("No se puede programar el envio, falta la fecha de envio");
                  return false;
                }

                var timenow = moment().utc('-0500').format('YYYY-MM-DD HH:mm');
                if ($scope.dateCompare <= timenow) {
                  notificationService.error("No se puede programar el envio, la fecha de envio ya expiro");
                  return false;
                }
                var enviar = {dateSelected: selecteDate, gmt: $scope.zonahoraria.zone.gmt, idMail: $scope.idMail};
                restService.sendScheduleDateEmail(enviar).then(function (data) {
                  notificationService.primary(data.message);
                  $scope.oldHour = true;
                  $scope.data.gmt = $scope.gmtDataBase = data.mail.gmt;
                  $scope.data.scheduleDate = $scope.date = data.mail.scheduleDate;
                });
              }

              $scope.reprogram = function () {
                $scope.oldHour = false;
                $("#valueDatepicker").val(moment(new Date()).format('YYYY-MM-DD HH:mm'));
                $scope.zonahoraria.zone = $filter('getById')($scope.timezone, "-0500");
                $scope.date = moment(new Date()).format('YYYY-MM-DD HH:mm');
              }

              $scope.sendNow = function () {
                var gmt = '-0500';
                var enviar = {dateSelected: moment().utcOffset(gmt).format('YYYY-MM-DD HH:mm'), gmt: $scope.zonahoraria.zone.gmt, idMail: $scope.idMail};
                restService.sendScheduleDateEmail(enviar).then(function (data) {
                  notificationService.primary(data.message);
                  $scope.oldHour = true;
                  $scope.data.gmt = $scope.gmtDataBase = data.mail.gmt;
                  $scope.data.scheduleDate = $scope.date = data.mail.scheduleDate;
                });
              };
              $scope.closeModalConfirm = function () {
                location.href = fullUrlBase + templateBase;
              };
              $scope.sendConfirmation = function () {
                $scope.dateConfirmation = moment(new Date()).format('YYYY-MM-DD HH:mm');
                $scope.getMd5($scope.data);
              };
              $scope.sendDataConfirmationEmail = function (data) {
                restService.sendDataComfirmationEmail(data).then(function (data) {
                  notificationService.primary(data.message);
                  openModalConfirm();
                });
              }
              $scope.validateData = function () {
                var data = $scope.data;
                var objSendDataConfirmation = {};
                var defer = $q.defer();
                var message = "No se puede enviar el mensaje porque falta el campo ";
                if (data.name == 0 || !angular.isDefined(data.name)) {
                  defer.reject(false);
                  notificationService.error(message + "nombre correo");
                }
                if (data.emailSender == 0 || !angular.isDefined(data.emailSender)) {
                  defer.reject(false);
                  notificationService.error(message + "remitente");
                }
                if (data.category == 0 || !angular.isDefined(data.category)) {
                  defer.reject(false);
                  notificationService.error(message + "categoria de correos");
                }
                if (data.subject == 0 || !angular.isDefined(data.subject)) {
                  defer.reject(false);
                  notificationService.error(message + "asunto de correo");
                }
                if (data.target == null || !angular.isDefined(data.target)) {
                  defer.reject(false);
                  notificationService.error(message + "destinatarios");
                }
                if (data.scheduleDate == null || !angular.isDefined(data.scheduleDate)) {
                  defer.reject(false);
                  notificationService.error(message + "fecha y hora de envio");
                }

                if (data.sizeAttachment > 2000000) {
                  defer.reject(false);
                  notificationService.error("El tamaÃ±o de todos los adjuntos es mayor a lo permitido");
                }

                if (angular.isDefined($scope.ammoutEmail)) {
                  //console.log($scope.ammoutEmail.ammount,data.quantitytarget,parseInt($scope.ammoutEmail.ammount) < parseInt(data.quantitytarget));
                  if ($scope.ammoutEmail.accountingMode == "sending") {

                    if (parseInt($scope.ammoutEmail.ammount) < parseInt(data.quantitytarget)) {
                      defer.reject(false);
                      notificationService.error("No tiene saldo suficiente para realizar esta campaÃ±a, le invitamos a recargar el servicio.");
                    }
                  }
                } else {
                  defer.reject(false);
                  notificationService.error("No tiene los servicios para realizar la campaÃ±a, por favor comunicarse con soporte");
                }

                var scheduleDate = moment(data.scheduleDate).utc(data.gmt).add(10, 'minutes').format('YYYY-MM-DD HH:mm');
                var nowhour = moment().utc('-0500').format('YYYY-MM-DD HH:mm');

                var dateProgram = moment(scheduleDate);
                var hournow = moment();
                var programeSocial = 'now';

                if (scheduleDate <= nowhour) {
                  defer.reject(false);
                  notificationService.error("la fecha de programación ya expiro, por favor validar.");
                } else {
                  var postDate = moment(data.scheduleDate).utc(data.gmt);
                  var nowMoment = moment().utc('-0500');
                  if (postDate.diff(nowMoment, 'minutes') <= 20) {
                    programeSocial = "now";
                  } else {
                    programeSocial = moment(dateProgram).unix();
                  }

                }
                objSendDataConfirmation = {dateConfirmation: $scope.dateConfirmation, idMail: $scope.idMail, socialSend: programeSocial};
                defer.resolve(objSendDataConfirmation);
                return defer.promise;
              }

              //$interval(tick, 1000);

              $scope.test = {};
              $scope.loaderTestMail = false;

              $scope.sendTestMail = function () {
                $scope.test.idMail = $stateParams.id;
                $scope.loaderTestMail = true;
                restService.sendTestMail($scope.test).then(function (data) {
                  notificationService.success(data.message);
                  $scope.test.target = '';
                  $scope.test.message = '';
                  $scope.loaderTestMail = false;
                }).catch(function (data) {
                  $scope.loaderTestMail = false;
                });
              };

              $scope.sendTesterMail = function () {
                $scope.test.idMail = $stateParams.id;
                $scope.loaderTestMail = true;
                restService.sendTestMail($scope.test).then(function (data) {
                  notificationService.success(data.message);
                  $scope.test.target = '';
                  $scope.test.message = '';
                  $scope.loaderTestMail = false;
                }).catch(function (data) {
                  $scope.loaderTestMail = false;
                });
              };

              restService.getThumbnailMail($stateParams.id).then(function (data) {
                console.log(data.thumb);
                $scope.urlThumbnail = data.thumb;
              });

              $scope.downloadMailPreview = function () {
                restService.downloadMailPreview($stateParams.id).then(function (data) {
                  if (!data.thumb) {
                    notificationService.error("Ocurrio un error generando el archivo, por favor comunicarse con el administrador.");
                  } else {
                    var url = fullUrlBase + data.thumb;
                    location.href = url;
                  }
                });
              };

//     $scope.restService.getMd5publish($scope.data).then(function(data){
//        
//      })

              $scope.getMd5 = function (data) {
                restService.getMd5publish(data).then(function (data) {
                  $scope.data.codigo = angular.copy(data.data);
                  $scope.validateData().then(function (success) {
                    if ($scope.data.facebook) {
                      $scope.publishSocialFacebook(success);
                    } else {
                      $scope.sendDataConfirmationEmail(success);
                    }
                  })
                }).catch(function (data) {
                });
              }

            }
          ])
          .controller('addAddresseesController', ['$scope', 'restService', '$window', 'incomplete', '$rootScope', '$stateParams', 'notificationService', 'wizard', function ($scope, restService, $window, incomplete, $rootScope, $stateParams, notificationService, wizard) {
              $rootScope.idMailGet = $stateParams.id;
              $scope.enabled = false;
              $scope.enableddb = false;
              $scope.enabledcat = false;
              if (!IsNumeric($stateParams.id)) {
                notificationService.warning("El correo no se ha encontrado");
                location.href = '#/basicinformation/';
              }

              $scope.data = {
                singleMail: true,
                alldb: false,
                typeUnsuscribed: false,
                typeAccount: null
              };
              function IsNumeric(val) {
                return Number(parseFloat(val)) == val;
              }
              wizard.setdescribe(false);
              wizard.setaddressees(true);
              wizard.setcontent(false);
              wizard.setadvanceoptions(false);
              wizard.setshippingdate(false);
              $rootScope.addressees = wizard.getaddressees();
              $rootScope.describe = wizard.getdescribe();
              $rootScope.content = wizard.getcontent();
              $rootScope.advanceoptions = wizard.getadvanceoptions();
              $rootScope.shippingdate = wizard.getshippingdate();
              $scope.idMail = $rootScope.idMailGet;
              $scope.disabledContactlist = false;
              $scope.disabledSegment = false;
              $scope.addressees = [];
              $scope.addressees = {selectdContactlis: []};
              $scope.addressees = {selectdSegment: []};
              $scope.addressees.showSegment = true;
              $scope.addressees.showstep1 = true;
              $scope.addressees.showContactlist = true;
              $scope.addressees.count = 0;
              $scope.addressees.filerMail = [];
              $scope.categories = "";
              $scope.statusandtype = "";

              restService.findMail($scope.idMail).then(function (data) {

                $scope.addressees.count = data.quantitytarget;

                if (data.singleMail == 1 && data.alldb == 1 ) {
                    var type = false;                    
                    if(data.typeUnsuscribed == 0 || data.typeUnsuscribed == null){
                        type = false;
                    }else{
                        type = true;
                    }
                    $scope.data = {
                    singleMail: true,
                    alldb: true,
                    idAccount: data.idAccount,
                    typeUnsuscribed: type,
                    typeAccount: data.typeAccount
                  };
                } else if (data.singleMail == 1 && data.alldb == 0) {
                   var type = false;                    
                   if(data.typeUnsuscribed == 0 || data.typeUnsuscribed == null){
                       type = false;
                   }else{
                       type = true;
                   }
                  $scope.data = {
                    singleMail: true,
                    alldb: false,
                    idAccount: data.idAccount,
                    typeUnsuscribed: type,
                    typeAccount: data.typeAccount
                  };
                } else if (data.singleMail == 0 && data.alldb == 0 && data.target == null) {
                   var type = false;                    
                   if(data.typeUnsuscribed == 0 || data.typeUnsuscribed == null){
                       type = false;
                   }else{
                       type = true;
                   }
                  $scope.data = {
                    singleMail: true,
                    alldb: false,
                    idAccount: data.idAccount,
                    typeUnsuscribed: type,
                    typeAccount: data.typeAccount
                  };
                } else if (data.singleMail == 0 && data.alldb == 0) {
                   var type = false;                    
                   if(data.typeUnsuscribed == 0 || data.typeUnsuscribed == null){
                       type = false;
                   }else{
                       type = true;
                   }
                  $scope.data = {
                    singleMail: false,
                    alldb: false,
                    idAccount: data.idAccount,
                    typeUnsuscribed: type,
                    typeAccount: data.typeAccount
                  };
                }

                if (data.target) {
                  $scope.addressees.showstep1 = false;
                  var json = jQuery.parseJSON(data.target);
                  if (json.type == "contactlist") {
                    $scope.statusandtype = json.type;
                    $scope.addressees.showContactlist = false;
                    $scope.addressees.selectdContactlis = json.contactlists;
                    $scope.getAllContactlist();
                  } else if (json.type == "segment") {
                    $scope.statusandtype = json.type;
                    $scope.addressees.showSegment = false;
                    $scope.addressees.selectdSegment = json.segment;
                    $scope.getAllSegment();
                  }
                  if (json.filters) {
                    $scope.filters = json.filters;
                    $scope.addressees.condition = json.condition;
                    restService.getMailFilters().then(function (data) {
                      $scope.addressees.filerMail = data;
                    });
                  }
                }
                if(json.filters[0].typeFilters!='' ){
                    $scope.filterContact = 1;
                }

              });
              $scope.clearSelect = function () {
                $scope.filters = [];
                $scope.addressees.count = 0;
                $scope.disabledContactlist = false;
                $scope.disabledSegment = false;
                $scope.addressees.selectdContactlis = "";
                $scope.addressees.selectdSegment = "";
                $scope.categories = "";
              };
              $scope.getContactlist = function () {
                $scope.filters = [];
                $scope.addressees = {selectdSegment: []};
                $scope.segments = [];
                $scope.contactlists = [];
                $scope.addressees.showstep1 = false;
                $scope.addressees.count = 0;
                $scope.addressees.showSegment = true;
                $scope.addressees.showContactlist = false;
                $scope.prueba = undefined;
                $scope.getAllContactlist();
                $scope.getAllSegment();
              };
              $scope.getAllContactlist = function () {
                restService.getContactlist().then(function (data) {
                  $scope.contactlists = data;
                  $scope.only();
                });
              };
              $scope.getSegment = function () {
                $scope.filters = [];
                $scope.addressees = {selectdContactlis: []};
                $scope.segments = [];
                $scope.contactlists = [];
                $scope.addressees.showstep1 = false;
                $scope.addressees.count = 0;
                $scope.prueba = undefined;
                $scope.prueba2 = undefined;
                $scope.addressees.selectdContactlis = [];
                $scope.addressees.selectdSegment = [];
                $scope.addressees.showSegment = false;
                $scope.addressees.showContactlist = true;
                $scope.getAllSegment();
                $scope.getAllContactlist();
              };
              $scope.getAllSegment = function () {
                restService.getSegment().then(function (data) {
                  $scope.segments = data;
                });
              };
              $scope.selectAction = function () {
                $scope.countContacts("contactlist");
              };
              $scope.selectActionSegment = function () {
                $scope.countContacts("segment");
              };
              $scope.countContacts = function (type) {
                $scope.addressees.count = 0;
                var data = {
                  type: type,
                  segment: $scope.addressees.selectdSegment,
                  contactlist: $scope.addressees.selectdContactlis,
                  filters: $scope.filters,
                  condition: $scope.addressees.condition,
                  idMail: $scope.idMail
                };
                restService.countContact(data).then(function (data) {
                  $scope.addressees.count = data.count;
                  $scope.only();
                });
              };

              $scope.addAddressees = function () {
                var data = {
                  selectdContactlis: $scope.addressees.selectdContactlis,
                  selectdSegment: $scope.addressees.selectdSegment,
                  showSegment: $scope.addressees.showSegment,
                  showContactlist: $scope.addressees.showContactlist,
                  idMail: $scope.idMail,
                  quantitytarget: $scope.addressees.count,
                  filters: $scope.filters,
                  condition: $scope.addressees.condition,
                  singleMail: $scope.data.singleMail,
                  alldb: $scope.data.alldb,
                  typeUnsuscribed : $scope.data.typeUnsuscribed
                };
                if ($scope.addressees.selectdContactlis && $scope.addressees.selectdContactlis.length > 0) {
                  incomplete.setincomplete(false);
                  $rootScope.incomplete = incomplete.getincomplete();
                  $scope.add(data);
                } else if ($scope.addressees.selectdSegment && $scope.addressees.selectdSegment.length > 0) {
                  incomplete.setincomplete(false);
                  $rootScope.incomplete = incomplete.getincomplete();
                  $scope.add(data);
                } else {
                  incomplete.setincomplete(true);
                  $rootScope.incomplete = incomplete.getincomplete();
                  $scope.add(data);
                }
              };
              $scope.allSegment = function () {
                $scope.disabledSegment = true;
                $scope.addressees.selectdSegment = $scope.segments;
                $scope.selectActionSegment();
              };
              $scope.allContactlist = function () {
                $scope.disabledContactlist = true;
                $scope.addressees.selectdContactlis = $scope.contactlists;
                $scope.selectAction();
              };
              $scope.filters = [];
              $scope.tipeFilters = [
                {id: 1, name: "Enviar a contactos que hayan recibido un correo"},
                {id: 2, name: "Enviar a contactos que hayan abierto un correo"},
                {id: 3, name: "Enviar a contactos que hayan hecho clic un enlace"}
              ];
              $scope.selectMailFilter = function (key) {
                restService.getMailFilters().then(function (data) {
                  $scope.addressees.filerMail = data;
                });
                if (key.typeFilters == 3) {
                  restService.getLinksByMail(key.mailSelected).then(function (data) {
                    key.links = data;
                  });
                } else {
                  if (!$scope.addressees.showContactlist) {
                    $scope.countContacts("contactlist");
                  } else {
                    $scope.countContacts("segment");
                  }
                }
              };
              function IsNumeric(val) {
                return Number(parseFloat(val)) == val;
              }
              $scope.selectinverted = function (key) {
                if (key.typeFilters == 3) {
                  if (IsNumeric(key.mailSelected) && IsNumeric(key.mailSelected) && IsNumeric(key.linkSelected)) {
                    if (!$scope.addressees.showContactlist) {
                      $scope.countContacts("contactlist");
                    } else {
                      $scope.countContacts("segment");
                    }
                  }
                } else {
                  if (IsNumeric(key.mailSelected) && key.typeFilters) {
                    if (!$scope.addressees.showContactlist) {
                      $scope.countContacts("contactlist");
                    } else {
                      $scope.countContacts("segment");
                    }
                  }
                }
              };
              $scope.selectLinkFilter = function () {
                if (!$scope.addressees.showContactlist) {
                  $scope.countContacts("contactlist");
                } else {
                  $scope.countContacts("segment");
                }
              };
              $scope.selectTypeFilter = function (key) {
                key.mailSelected = [];
                key.linkSelected = [];
                key.mail = [];
                key.links = [];
                key.inverted = "";
                restService.getMailFilters().then(function (data) {
                  $scope.addressees.filerMail = data;

                });
                switch (key.typeFilters) {
                  case 1:

                    break;
                  case 2:

                    break;
                  case 3:

                    break;
                }
              };
              
              $scope.filterContact = 0;
              
              $scope.addFilter = function () {
                $scope.addressees.condition = "all";
                $scope.filters.push({});
                $scope.filterContact = 1;
                 $('#filterContact').addClass('disabled');
              };
              $scope.removeFilters = function (index) {
                $scope.filters.splice(index, 1);
                $scope.filterContact = 0;
                $('#filterContact').removeClass('disabled');
                if (!$scope.addressees.showContactlist) {
                  $scope.countContacts("contactlist");
                } else {
                  $scope.countContacts("segment");
                }
              };
              $scope.add = function (data) {
                restService.addAddressees(data).then(function () {
                  notificationService.success("Se ha guardado la selección de destinatarios correctamente");
                  location.href = '#/content/' + $scope.idMail;
                });
              };
              $scope.only = function () {
                if ($scope.addressees.showContactlist == false) {
                  var data = {
                    selectdContactlis: $scope.addressees.selectdContactlis,
                    filters: $scope.filters,
                    singleMail: $scope.data.singleMail
                  };

                  if (typeof data.selectdContactlis !== 'undefined') {
                    //

                    restService.only(data).then(function (data) {
                      $scope.addressees.count = data.count;
                      $scope.categories = data.categories;
                    });
                  }
                }


              };
              $scope.only();
            }])
          .controller('index', ['$scope', 'incomplete', '$rootScope', 'restService', 'notificationService', '$interval', function ($scope, incomplete, $rootScope, restService, notificationService, $interval) {
              $scope.viewlist = true;
              $scope.viewnolist = true;
              $scope.prueba = [{name: 'Eliminar'}];
              $scope.initial = 0;
              $scope.page = 1;
              $scope.mail = [{}];
              $scope.mailToTemplate = [];
              $scope.selected = [];
              $scope.filter = "";
              $scope.showTest = false;
              $scope.confirmDelete = function (idMail) {
                $scope.idMail = idMail;
                openModal();
              };
              $scope.confirmCancel = function (idMail) {
                $scope.idMail = idMail;
                openModalCancel(idMail);
              };
              $scope.previewmailtempcont = function (id) {
                restService.previewMailTemplateContent(id).then(function (data) {

                  var e = data.template;
                  $('#content-preview').empty();
                  $('<iframe frameborder="0" width="100%" height="100%"/>').appendTo('#content-preview').contents().find('body').append(e);
                  $('#myModal').modal('show');
                });
              };
              $scope.pauseMailAn = function (idMail) {
                //console.log(idMail);
                pauseMail(idMail);
              };
              $scope.cancelMailAn = function (idMail) {
                cancelMail(idMail);
              };
              $scope.resumeMailAn = function (idMail) {
                //console.log(idMail);
                resumeMail(idMail);
              };
              $scope.cancelMail = function (idMail) {
                restService.cancelMail(idMail).then(function (data) {
                  notificationService.warning(data.message);
                  closeModalCancel();
                  $scope.getAll();
                });
              };
              $scope.deleteMail = function () {
                restService.deleteMail($scope.idMail).then(function (data) {
                  notificationService.warning(data.message);
                  closeModal();
                  $scope.getAll();
                });
              };
              $scope.openModalCancel = function (idMail) {
                console.log(idMail);
                $scope.idMail = idMail;
                $('#dialogCancel').addClass('dialog--open');
              };
              //        $scope.filter.category = [];
              $scope.filter = {category: []};
              $scope.$watch('showTest', function () {
                if ($scope.showTest) {
                  $scope.filter.showTest = 0;
                } else {
                  $scope.filter.showTest = 1;
                }
                $scope.getAll();
              }, true);
              $scope.forward = function () {
                $scope.initial += 1;
                $scope.page += 1;
                $scope.getAll();
              };
              $scope.fastforward = function () {
                $scope.initial = ($scope.mail.total_pages - 1);
                $scope.page = $scope.mail.total_pages;
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
               $scope.initial = 0;
               $scope.page = 1;
                $scope.getAll();
              };
              $scope.searchcategory = function () {
                if ($scope.filter.category.length >= 1) {
                  $scope.initial = 0;
                  $scope.page = 1;
                  $scope.getAll();
                } else {
                  $scope.getAll();
                }
              };
              $scope.$watch('[filter.dateinitial,filter.dateend]', function () {
                //          if(angular.isDefined($scope.filter.dateinitial)){
                //            if($scope.filter.dateinitial != ""){
                //              if(angular.isDefined($scope.filter.dateend)){
                //                if($scope.filter.dateend != ""){
                //                  $scope.getAll();
                //                }
                //              }
                //            }
                //          }
                $scope.initial = 0;
                $scope.page = 1;
                $scope.getAll();
              });
              $scope.getAll = function () {
                if (angular.isDefined($scope.mail[0])) {
                  $scope.mail[0].items = [];
                }
                restService.getAllMail($scope.initial, $scope.filter).then(function (data) {
                  $scope.mail = data;
                });
              };
              var statusVerify = function () {
                restService.getAllMail($scope.initial, $scope.filter).then(function (data) {
                  data[0].items.forEach(function (item, index) {
                    if ($scope.mail[0].items[index].status != item.status) {
                      $scope.mail[0].items[index].status = item.status;
                      //console.log(item.status);
                    }
                  });
                  //$scope.mail = data;
                });
              };
              $interval(statusVerify, 1000);
              restService.getMailCategory($scope.initial, $scope.filter).then(function (data) {
                $scope.mailCategory = data;
              });
              //$scope.getAll();
              //              $scope.to = function () {
              $scope.toggle = function (item, list) {
                var idx = list.indexOf(item);
                if (idx > -1) {
                  list.splice(idx, 1);
                } else {
                  list.push(item);
                }
              };
              $scope.exists = function (item, list) {
                return list.indexOf(item) > -1;
              };
              $scope.isChecked = function () {
                return $scope.mail[0].items && $scope.selected.length === $scope.mail[0].items.length;
              };
              $scope.isIndeterminate = function () {
                $scope.initial = 0;
                $scope.page = 1;
                return ($scope.selected.length !== $scope.mail[0].items.length);
              };
              $scope.toggleAll = function () {
                if ($scope.selected.length === $scope.mail[0].items.length) {
                  $scope.selected = [];
                } else if ($scope.selected.length === 0 || $scope.selected.length > 0) {
                  $scope.selected = $scope.mail[0].items.slice(0);
                }
              };             
              $scope.getAll();
              //              }
              $scope.targetString = function (target) {
                var string = "";
                //                for (var i = 0; i > target.contactlists.length; i++) {
                ////                  console.log(target.contactlists[i]);
                //                  string += target.contactlists[i].name;
                //                }
                return string;
              }

              //Todo sobre pasar mail a template
              $scope.empty = false;
              $scope.length = false;
              $scope.categ = false;
              $scope.restart = function (idMail) {
                for (var i = 0; i < $scope.mail[0].items.length; i++) {
                  if ($scope.mail[0].items[i].idMail == idMail) {
                    $scope.mailToTemplate = $scope.mail[0].items[i];
                    break;
                  }
                }
                $scope.empty = false;
                $scope.length = false;
                $scope.categ = false;
                $scope.namemailtempcat = '';
              };

              restService.getMailTemplateCategory().then(function (data) {
                $scope.mailtempcats = data;
              });

              $scope.saveMailAsMailTemplate = function () {
                restService.getMailContentJSON($scope.mailToTemplate.idMail).then(function (data) {
                  $scope.editor = data.content;

                  if (angular.isUndefined($scope.namemailtempcat) || $scope.namemailtempcat === '') {
                    $scope.empty = true;
                    $scope.length = false;
                    //notificationService.error("El campo de nombre de la plantilla no puede estar vacÃƒÆ’Ã‚Â­o");
                    //alert("El campo de nombre de la plantilla no puede estar vacÃƒÆ’Ã‚Â­o");
                    return false;
                  }
                  if ($scope.namemailtempcat.length < 2 || $scope.namemailtempcat.length > 80) {
                    $scope.empty = false;
                    $scope.length = true;
                    //notificationService.error("El campo de nombre de la plantilla debe tener mÃƒÆ’Ã‚Â­nimo 2 caracteres y mÃƒÆ’Ã‚Â¡ximo 80");
                    //alert("El campo de nombre de la plantilla debe tener mÃƒÆ’Ã‚Â­nimo 2 caracteres y mÃƒÆ’Ã‚Â¡ximo 80");
                    return false;
                  }
                  if (angular.isUndefined($scope.mailtempcat)) {
                    $scope.categ = true;
                    //notificationService.error("Debe seleccionar un ÃƒÆ’Ã‚Â­tem del listado de categorÃƒÆ’Ã‚Â­as");
                    //alert("Debe seleccionar un ÃƒÆ’Ã‚Â­tem del listado de categorÃƒÆ’Ã‚Â­as");
                    return false;
                  }

                  var data = {
                    nameMailTemplate: $scope.namemailtempcat,
                    mailTemplateCateg: $scope.mailtempcat,
                    editor: $scope.editor
                  };
                  console.log(data);

                  //                document.getElementById('iframeEditor').contentWindow.RecreateEditor();

                  restService.saveMailAsMailTemplate(data, null).then(function (data) {
                    $("#saveMailAsMailTemplate").modal("hide");
                    $scope.namemailtempcat = '';
                    $scope.mailtempcat = '';
                    $scope.empty = false;
                    $scope.length = false;
                    $scope.cat = false;

                    notificationService.success(data.message);
                  });
                });


              };
              // Fin de todo sobre pasar mail a template
              
              $scope.statusFunc = function () {
                  console.log('Afuera');
                  console.log($scope.filter.mailStatus);
                if ($scope.filter.mailStatus.length > 0) {
                  console.log('Adentro');
                  console.log($scope.filter.mailStatus);
                  $scope.initial = 0;
                  $scope.page = 1;
                  $scope.getAll();
                }
              };

            }])
          .controller('contentController', ['$scope', 'restService', '$stateParams', '$rootScope', 'notificationService', '$filter', 'wizard', function ($scope, restService, $stateParams, $rootScope, notificationService, $filter, wizard) {
              wizard.setdescribe(false);
              wizard.setaddressees(false);
              wizard.setcontent(true);
              wizard.setadvanceoptions(false);
              wizard.setshippingdate(false);
              $rootScope.addressees = wizard.getaddressees();
              $rootScope.describe = wizard.getdescribe();
              $rootScope.content = wizard.getcontent();
              $rootScope.advanceoptions = wizard.getadvanceoptions();
              $rootScope.shippingdate = wizard.getshippingdate();
              $rootScope.idMailGet = $stateParams.id;
              $scope.idMail = $rootScope.idMailGet;
              $scope.imagenDate = new Date();
              $scope.imagenTime = $scope.imagenDate.getTime();
              if (!IsNumeric($stateParams.id)) {
                notificationService.warning("El correo no se ha encontrado");
                location.href = '#/basicinformation/';
              }

              function IsNumeric(val) {
                return Number(parseFloat(val)) == val;
              }
              $scope.getContent = {};
              $scope.setPlane = {};
              $scope.boolEditors = true;
              $scope.PlaneError = {};
              htmlPreview($scope.idMail);
              restService.getContentMail($scope.idMail).then(function (data) {

                if (!data.content) {
                  //            console.log(data);
                  $scope.boolEditors = true;
                } else {
                  //            console.log(data);
                  restService.getThumbnailMail($stateParams.id).then(function (data) {
                    $scope.urlThumbnail = data.thumb;
                  });
                  $scope.boolEditors = false;
                  $scope.getContent = data.content;
                  $scope.setPlane.content = data.content.plaintext;
                }
              });
              $scope.addplaintext = function () {
                var data = {
                  idMail: $scope.idMail,
                  plaintext: $scope.setPlane.content
                };
                restService.addPlaintext(data).then(function () {
                  notificationService.primary("Se ha modificado el texto plano del correo");
                  $scope.closeModal();
                });
              };
              $scope.openModal = function () {
                $('.dialog').addClass('dialog--open');
              }

              $scope.closeModal = function () {
                $('.dialog').removeClass('dialog--open');
              }
            }])
          .controller('contentEditorController', ['$scope', 'restService', 'notificationService', function ($scope, restService, notificationService) {

              $scope.registerContent = function (idMail) {
                var data = {
                  editor: document.getElementById('iframeEditor').contentWindow.catchEditorData()
                };
                restService.addContentEditor(idMail, data).then(function (res) {
                  location.href = fullUrlBase + templateBase + '/create#/content/' + res['idMail'];
                });
              };
              $scope.saveContent = function (idMail) {
                var data = {
                  editor: document.getElementById('iframeEditor').contentWindow.catchEditorData()
                };
                restService.addContentEditor(idMail, data).then(function (res) {
                  document.getElementById('iframeEditor').contentWindow.RecreateEditor();
                  notificationService.success(res['msg']);
                });
              };
              $scope.empty = false;
              $scope.length = false;
              $scope.categ = false;



              $scope.saveMailAsMailTemplate = function () {
                if (angular.isUndefined($scope.namemailtempcat) || $scope.namemailtempcat === '') {
                  $scope.empty = true;
                  $scope.length = false;
                  //notificationService.error("El campo de nombre de la plantilla no puede estar vacÃƒÆ’Ã‚Â­o");
                  //alert("El campo de nombre de la plantilla no puede estar vacÃƒÆ’Ã‚Â­o");
                  return false;
                }
                if ($scope.namemailtempcat.length < 2 || $scope.namemailtempcat.length > 80) {
                  $scope.empty = false;
                  $scope.length = true;
                  //notificationService.error("El campo de nombre de la plantilla debe tener mÃƒÆ’Ã‚Â­nimo 2 caracteres y mÃƒÆ’Ã‚Â¡ximo 80");
                  //alert("El campo de nombre de la plantilla debe tener mÃƒÆ’Ã‚Â­nimo 2 caracteres y mÃƒÆ’Ã‚Â¡ximo 80");
                  return false;
                }
                if (angular.isUndefined($scope.mailtempcat)) {
                  $scope.categ = true;
                  //notificationService.error("Debe seleccionar un ÃƒÆ’Ã‚Â­tem del listado de categorÃƒÆ’Ã‚Â­as");
                  //alert("Debe seleccionar un ÃƒÆ’Ã‚Â­tem del listado de categorÃƒÆ’Ã‚Â­as");
                  return false;
                }

                var data = {
                  nameMailTemplate: $scope.namemailtempcat,
                  mailTemplateCateg: $scope.mailtempcat,
                  editor: document.getElementById('iframeEditor').contentWindow.catchEditorData()
                };
                document.getElementById('iframeEditor').contentWindow.RecreateEditor();
                restService.saveMailAsMailTemplate(data, null).then(function (data) {
                  $("#saveMailAsMailTemplate").modal("hide");
                  $scope.namemailtempcat = '';
                  $scope.mailtempcat = '';
                  $scope.empty = false;
                  $scope.length = false;
                  $scope.cat = false;
                  notificationService.success(data.message);
                });
              };
              $scope.restart = function () {
                $scope.empty = false;
                $scope.length = false;
                $scope.categ = false;
                $scope.namemailtempcat = '';
              };


            }])
          .controller('contentUrlController', ['$scope', 'restService', 'notificationService', function ($scope, restService, notificationService) {
              $scope.trimContent = function () {
                $('#url').val($('#url').val().trim());
              }

              $scope.registerContentUrl = function () {
                //          if ($scope.checkboximage) {
                //            var image = $scope.checkboximage;
                //          } else {
                //            image = false;
                //          }
                sendData("no load");
                //          sendData(image);
              };
              //        $scope.saveContent = function (idMail) {
              //          var data = {
              //            editor: document.getElementById('iframeEditor').contentWindow.catchEditorData()
              //          };
              //          restService.addContentEditor(idMail, data).then(function (res) {
              //            document.getElementById('iframeEditor').contentWindow.RecreateEditor();
              //            notificationService.success(res['msg']);
              //          });
              //        };
            }])
          .controller('advanceoptionsController', ['$scope', 'restService', 'notificationService', '$rootScope', '$stateParams', '$mdDialog', 'FileUploader', 'wizard', '$FB', '$q', 'constantMail', '$timeout', '$window', function ($scope, restService, notificationService, $rootScope, $stateParams, $mdDialog, FileUploader, wizard, $FB, $q, constantMail, $timeout, $window) {
          
              //              $scope.notifications = false;
              //              $scope.notificationEmails = "";
              //              $scope.dataadv = [];
              $scope.dataadv = {};
              $scope.sizeFiles = 0;

              $scope.result = [];

              var num = 1;
              var numbers = [];
              while (num <= 30) {
                numbers[num] = num;
                num++;
              }
              $scope.numbers = numbers;
              //              $scope.typesTimes = typesTimes;
              $scope.typesTimes = {hour: "horas", day: "días", week: "semanas"};
              //              console.log($scope.numbers);
              //              console.log($scope.typesTimes);
              $scope.dataadv.notifications = false;
              $scope.dataadv.notificationEmails = "";
              $scope.dataadv.statistics = false;
              $scope.dataadv.statisticsEmails = "";
              wizard.setdescribe(false);
              wizard.setaddressees(false);
              wizard.setcontent(false);
              wizard.setadvanceoptions(true);
              wizard.setshippingdate(false);
              $rootScope.addressees = wizard.getaddressees();
              $rootScope.describe = wizard.getdescribe();
              $rootScope.content = wizard.getcontent();
              $rootScope.advanceoptions = wizard.getadvanceoptions();
              $rootScope.shippingdate = wizard.getshippingdate();
              $rootScope.idMailGet = $stateParams.id;
              $scope.isModal = "";
              if (!IsNumeric($stateParams.id)) {
                notificationService.warning("El correo no se ha encontrado");
                location.href = '#/basicinformation/';
              }

              function IsNumeric(val) {
                return Number(parseFloat(val)) == val;
              }
              $scope.idMail = $rootScope.idMailGet;
              $scope.status = '  ';
              $scope.customFullscreen = false;
              $scope.deleteAttached = function (id, index) {
                restService.deleteAttached(id).then(function () {
                  $scope.attach();
                });
              };
              function DialogController($scope, $mdDialog) {
                $scope.hide = function () {
                  $mdDialog.hide();
                };
                $scope.cancel = function () {
                  $mdDialog.cancel();
                };
                $scope.answer = function () {
                  $('#adjun').modal('hide');
                  $scope.selectFile();
                };
              }
              
              
              var uploadUrl = fullUrlBase + "gallery/uploadfileadjunt/" + $scope.idMail;
              var uploader = $scope.uploader = new FileUploader({
                url: uploadUrl
              });
              // FILTERS
              //
              //        uploader.filters.push({
              //          name: 'customFilter',
              //          fn: function (item /*{File|FileLikeObject}*/, options) {
              //            return this.queue.length < 10;
              //          }
              //        });


              uploader.onWhenAddingFileFailed = function (item /*{File|FileLikeObject}*/, filter, options) {
                //console.info('onWhenAddingFileFailed', item, filter, options);
              };
              uploader.onAfterAddingFile = function (fileItem) {
                //console.info('onAfterAddingFile', fileItem);
                //          $scope.arrFilePending = true;
                $scope.uploader.queue[$scope.uploader.queue.length - 1].upload();
              };
              uploader.onAfterAddingAll = function (addedFileItems) {
                //console.info('onAfterAddingAll', addedFileItems);
              };
              uploader.onBeforeUploadItem = function (item) {
                //console.info('onBeforeUploadItem', item);
              };
              uploader.onProgressItem = function (fileItem, progress) {
                //console.info('onProgressItem', fileItem, progress);
              };
              uploader.onProgressAll = function (progress) {
                //console.info('onProgressAll', progress);
              };
              uploader.onSuccessItem = function (fileItem, response, status, headers) {
                //console.info('onSuccessItem', fileItem, response, status, headers);
              };
              uploader.onErrorItem = function (fileItem, response, status, headers) {
                //console.info('onErrorItem', fileItem, response, status, headers);
              };
              uploader.onCancelItem = function (fileItem, response, status, headers) {
                //console.info('onCancelItem', fileItem, response, status, headers);
              };
              uploader.onCompleteItem = function (fileItem, response, status, headers) {

                //console.info('onCompleteItem', fileItem, response, status, headers);
              };
              uploader.onCompleteAll = function () {

                $scope.attach();
                //          $scope.arrFilePending = false;
                //console.info('onCompleteAll');
              };
              console.log($scope.idMail);
              $scope.showTabDialog = function (ev) {
                document.body.scrollTop = 0;
                $mdDialog.show({
                  scope: $scope.$new(),
                  //            mdDialog: $mdDialog,
                  controller: DialogController,
                  templateUrl: 'tabDialog.tmpl.html',
                  parent: angular.element(document.body),
                  targetEvent: ev,
                  clickOutsideToClose: true
                }).then(function (answer) {
                  $scope.status = 'You said the information was "' + answer + '".';
                }, function () {
                  $scope.status = 'You cancelled the dialog.';
                });
              };
              
        
        $scope.uploaderTwo = function() {

      //==============================================

      /*var hashLocal  = location.hash;
                  var arrayTemp = hashLocal.split("/");
                  var idMail = arrayTemp['2'];*/

                  var uploader = new plupload.Uploader({
        browse_button : 'pickfiles', // you can pass in id...
        container: document.getElementById('container'), 
        //url: "{{url('mail/loadpdf')}}/"+idMail,
        url: "../../mail/loadpdf/"+$scope.idMail,
        file_data_name: "file",
        filters : {
            max_file_size : '500mb',
            mime_types: [
      {title : "Zip files", extensions : "zip"}
                          ]
        },
        // Flash settings
        flash_swf_url : '/plupload/js/Moxie.swf',
        // Silverlight settings
        silverlight_xap_url : '/plupload/js/Moxie.xap',
        multi_selection: false,
        rename: true,
        sortable: true,
        dragdrop: true,

        views: {
            list: false,
            thumbs: false
        },        

        init: {
            PostInit: function() {
          document.getElementById('filelist').innerHTML = '';

          document.getElementById('uploadfiles').onclick = function() {
              uploader.start()
              return false;
          };
            },

            FilesAdded: function(up, files) {
          plupload.each(files, function(file) {
            document.getElementById('filelist').innerHTML += '<div id="' + file.id + '">' + file.name + ' (' + plupload.formatSize(file.size) + ') <b></b></div>';
              });
            },

            UploadProgress: function(up, file) {
          document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = '<span>' + file.percent + "%</span>";
            },
                          
            FileUploaded: function(up, err) {

          restService.getTableResults($scope.idMail)
           .then(function (data) { 
             $scope.fileadjuntpdf = data;
             $scope.result = data; 
             if(data.uploadstatus == "error"){
                 notificationService.error("Ah ocurrido un error validando el archivo que estas intentando cargar,");
                 //debo de hacer los movimientos a la inversa para que pueda cargar un archivo nuevamente en la vista.
                 $('#next').hide('slow');
                 $('#gogo').hide('slow');
                 $('#resume').hide('slow');
                 $('#buttons').show('slow');
                 $('#htab3').show('slow');
                 $('#filelist').show('slow');
                 $('#container').show('slow');
                 
                 document.getElementById('filelist').innerHTML = '';
                 
             }
           })
           .catch(function (data) { notificationService.error(data); });
          
          $('#next').show('slow');
          $('#gogo').show('slow');
          $('#resume').show('slow');
          $('#buttons').hide('slow');
          $('#htab3').hide('slow');
          $('#filelist').hide('slow');
          $('#container').hide('slow');

            },

            Error: function(up, err) {
          document.getElementById('console').innerHTML += "\nError #" + err.code + ": " + err.message;
            }
        }
          });

                  uploader.init();
      
      //==============================================

        }
        $scope.uploaderTwo(); //inicializando el modulo puesto...

              $scope.answer = function () {
                $('#adjun').modal('hide');
              };
              //        prueba($scope.idMail);
              $scope.fileadjunt = [];
              $scope.fileadjuntpdf = [];
              $scope.fileSelecteds = [];
              $scope.divfile = true;
              //        $scope.tabload = function () {
              //          $scope.divfile = false;
              //        }
              //        $scope.tabgallery = function () {
              //          $scope.divfile = true;
              //        }
              $scope.count = 0;
              $scope.initial = 0;
              $scope.page = 1;
              $scope.hidefile = false;
              $scope.search = function () {
                $scope.getAll();
              };
              $scope.forward = function () {
                $scope.initial += 1;
                $scope.page += 1;
                $scope.getAll();
              };
              $scope.fastforward = function () {
                $scope.initial = ($scope.gallery.total_pages - 1);
                $scope.page = $scope.gallery.total_pages;
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
                //          $scope.addFile();
                $scope.getAll();
              };
              $scope.getAll = function () {
                restService.getAllGallery($scope.initial).then(function (data) {
                  $scope.gallery = data;
                  $scope.attach();
                  $scope.attachpdf();
                });
              };
              $scope.attach = function () {
                restService.getAllAttachment($scope.idMail).then(function (data) {
                  $scope.fileadjunt = data;
                  $scope.sizeFiles = 0;
                  $scope.fileSelecteds = angular.copy($scope.fileadjunt);
                  if ($scope.fileadjunt.length > 0) {
                    $scope.hidefile = true;
                    $scope.selectFile();
                    $scope.setSizeFile();
                  }
                });
              };
              $scope.attachpdf = function () {
                restService.getAllAttachmentpdf($scope.idMail).then(function (data) {
                  $scope.fileadjuntpdf = data;
                  $scope.result = data;
                  $scope.sizeFiles = 0;
                  //$scope.fileSelecteds = angular.copy($scope.fileadjuntpdf);
                  if ($scope.fileadjuntpdf.files.length > 0) {
                    $('#next').show('slow');
                    $('#gogo').show('slow');
                    $('#resume').show('slow');
                    $('#buttons').hide('slow');
                    $('#htab3').hide('slow');
                    $('#filelist').hide('slow');
                    $('#container').hide('slow');
                  }
                });
              };
              $scope.setSizeFile = function () {
                for (var i in $scope.fileadjunt) {
                  $scope.sizeFiles = $scope.sizeFiles + parseInt($scope.fileadjunt[i].size);
                }
              }

              $scope.selectFile = function () {
                for (var i = 0; i < $scope.gallery[0].items.length; i++) {
                  $scope.gallery[0].items[i].class = {}
                  for (var j in $scope.fileadjunt) {
                    if ($scope.fileadjunt[j].idAsset == $scope.gallery[0].items[i].idAsset) {
                      $scope.gallery[0].items[i].class.border = "1px solid red";
                      $scope.gallery[0].items[i].class.opacity = 0.5;
                    }
                  }
                }
              };
              $scope.selectedAsset = function (data, $index) {
                var x = $scope.gallery[0].items[$index];
                if (angular.isDefined(x.class) && !angular.equals(x.class, {})) {
                  for (var i in $scope.fileSelecteds) {
                    if ($scope.fileSelecteds[i].idAsset == data.idAsset) {
                      $scope.fileSelecteds.splice(i, 1);
                    }
                  }
                  $scope.gallery[0].items[$index].class = {};
                } else {
                  $scope.fileSelecteds.push(data);
                  $scope.gallery[0].items[$index].class = {};
                  $scope.gallery[0].items[$index].class.border = "1px solid red";
                  $scope.gallery[0].items[$index].class.opacity = 0.5;
                }
              };
              $scope.addFile = function () {
                //          if($scope.arrFilePending){
                //            $scope.uploader.uploadAll();
                //          }
                //          console.log($scope.fileSelecteds);
                //          return;
                if ($scope.fileSelecteds.length > 0) {
                  $scope.hidefile = true;
                  var data = {
                    file: $scope.fileSelecteds,
                    idMail: $scope.idMail
                  };
                  restService.addAdjunt(data).then(function () {
                    $scope.attach();
                  });
                }
                //$scope.answer('not useful');
                $('#adjun').modal('hide');
              };
              $scope.addFileLoad = function () {
                //console.log(123);
                //          $("#myModal").modal("hide");
                //          $scope.getAll();
              };
              $scope.getAll();
              $scope.googleAnalyticsLink = [];
              $scope.showLinksGoogleAnalytics = false;
              $scope.googleAnalytics = {links: [], campaignName: "", idMail: $stateParams.id};
              $scope.disabledSendDataGoogleAnalytics = false;

              $scope.sendDataGoogleAnalytics = function () {
                if ($scope.googleAnalytics.campaignName.length < 2 || $scope.googleAnalytics.links.length == 0) {
                  notificationService.error("Los campos nombre de campaña y seguimiento de Google Analytics no pueden estar vacios.");
                } else {
                  restService.sendDataGoogleAnalitics($scope.googleAnalytics).then(function (data) {
                    if (data.action == "create") {
                      notificationService.success(data.message);
                    } else {
                      notificationService.primary(data.message);
                    }
                  });
                }
              };
              $scope.clearDataGoogleAnalytics = function () {
                $scope.googleAnalytics.campaignName = "";
                $scope.googleAnalytics.links = [];
                //$scope.dataGoogleAnalytics = {};
              };
              $scope.discardChangesGoogleAnalytics = function () {
                $scope.showLinksGoogleAnalytics = false;
                $scope.googleAnalytics.campaignName = "";
                $scope.googleAnalytics.links = [];
                //$scope.dataGoogleAnalytics = {};
              };
              $scope.changeNotifications = function () {
                $("#notificationArea").removeClass("error-focus");
                if (!$scope.notifications) {
                  $scope.dataadv.notificationEmails = "";
                  $("#advert-error").removeClass('color-danger');
                }
                //                console.log($scope.dataadv.notifications);
                //                console.log($scope.dataadv.notificationEmails);

              }
              $scope.changeStatistics = function () {
                $("#statisticsArea").removeClass("error-focus");
                if (!$scope.statistics) {
                  $scope.dataadv.statisticsEmails = "";
                  $scope.dataadv.quantity = null;
                  $scope.dataadv.typeTime = null;
                  $("#advert-error-statistics").removeClass('color-danger');
                }
                //                console.log($scope.dataadv.statistics);
                //                console.log($scope.dataadv.statistics);

              }

              $scope.validateEmail = function () {
                $("#notificationArea").removeClass("error-focus");
                var notEmails = $scope.dataadv.notificationEmails.split(',');
                var cont = 0;
                var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                for (i = 0; i < notEmails.length; i++) {
                  if (!re.test(notEmails[i])) {
                    cont++;
                  }
                }
                //                .log.log("El array tiene " + notEmails.length + " posiciones y su contenido es '" + notEmails[0] + "' en su posicion 0");
                if (cont > 0) {
                  $("#notificationArea").addClass('error-focus');
                }

                if (notEmails.length == 1 && notEmails[0] == '') {
                  $("#notificationArea").removeClass("error-focus");
                  $("#advert-error").removeClass('color-danger');
                }
                //                console.log("Posiciones: " + notEmails.length);

                if (notEmails.length > 8) {
                  $("#notificationArea").addClass('error-focus');
                  $("#advert-error").addClass('color-danger');
                } else {
                  $("#advert-error").removeClass('color-danger');
                }

              }
              $scope.validateEmailStatistics = function () {
                $("#statisticsArea").removeClass("error-focus");
                var notEmails = $scope.dataadv.statisticsEmails.split(',');
                var cont = 0;
                var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                for (i = 0; i < notEmails.length; i++) {
                  if (!re.test(notEmails[i])) {
                    cont++;
                  }
                }
                //                console.log("El array tiene " + notEmails.length + " posiciones y su contenido es '" + notEmails[0] + "' en su posicion 0");
                if (cont > 0) {
                  $("#statisticsArea").addClass('error-focus');
                }

                if (notEmails.length == 1 && notEmails[0] == '') {
                  $("#statisticsArea").removeClass("error-focus");
                  $("#advert-error-statistics").removeClass('color-danger');
                }
                //                console.log("Posiciones: " + notEmails.length);

                if (notEmails.length > 8) {
                  $("#statisticsArea").addClass('error-focus');
                  $("#advert-error-statistics").addClass('color-danger');
                } else {
                  $("#advert-error-statistics").removeClass('color-danger');
                }

              }

              $scope.sendDataForTable = function(){
                  //console.log("esta entrando al metodo xD");  
                  restService.getTableResults($scope.idMail)
                        .then(function (data) { $scope.result = data; })
                        .catch(function (data) { notificationService.error(data); });
              }
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

              $scope.appFacebook = {
                fanPageSelected: false,
                checkPermissionFacebookPage: function (page) {
                  var defer = $q.defer();
                  var promise = defer.promise;
                  var arrPageReturn = {data: []};
                  for (var i in page) {
                    if (typeof page[i].perms.indexOf(constantMail.permissionFBAdmin) != "number" ||
                            typeof page[i].perms.indexOf(constantMail.permissionFBBasicAdmin) != "number" ||
                            typeof page[i].perms.indexOf(constantMail.permissionFBCreateContent) != "number") {
                      continue;
                    }
                    arrPageReturn.data.push(page[i]);
                  }
                  if (arrPageReturn.data.length <= 0) {
                    defer.reject(constantMail.errorLengthFanPage);
                  }
                  defer.resolve(arrPageReturn);
                  return promise;
                },
                changeSwitch: function () {
                  if ($scope.appFacebook.facebook) {
                    if (!this.fanPageSelected) {
                      $scope.appFacebook.login(false);
                    }
                  } else {

                  }
                },
                login: function (objPage) {
                  $FB.getLoginStatus(function (response) {
                    console.log(response);
                    if (response.status === 'connected') {
                      $FB.api('/me/accounts', function (response) {
//                      $FB.api('/' + $FB.getUserID() + '/accounts', function (response) {
                        if (response.error) {
                         //notificationService.error(constantMail.errorApiFacebook);
                          return;
                        }
                        $scope.appFacebook.checkPermissionFacebookPage(response.data).then(function (response) {
                        if (!objPage) {
                          $scope.appFacebook.showModalSelectedPage(response);
                        } else {
                          $scope.appFacebook.setFacebook(response, objPage);
                        }
                        }).catch(function (data) {
                          notificationService.error(data);
                        });
                      });
                    } else {
                      $FB.login(function () {
                        $FB.api('/me/accounts', function (response) {
//                        $FB.api('/' + $FB.getUserID() + '/accounts', function (response) {
                          if (response.error) {
                            //notificationService.error(constantMail.errorApiFacebook);
                            return;
                          }
                          $scope.appFacebook.checkPermissionFacebookPage(response.data).then(function (response) {
                          if (!objPage) {
                            $scope.appFacebook.showModalSelectedPage(response);
                          } /*else {
                            $scope.appFacebook.setFacebook(response, objPage);
                          }*/
                          }).catch(function (data) {
                            notificationService.error(data);
                          });
                        });
                      }, {
                        scope: 'publish_actions,publish_pages,manage_pages'
                      });
                    }
                  });
                },
                getPicturesPage: function (id) {
                  var defer = $q.defer();
                  var promise = defer.promise;
                  FB.api('/' + id + '/picture?redirect=false', function (response) {
                    defer.resolve(response);
                  });
                  return promise;
                },
                getFanPageArr: function (data) {
                  var defer = $q.defer();
                  var promises = [];
                  var response = data;
                  angular.forEach(response, function (value) {
                    promises.push($scope.appFacebook.getPicturesPage(value.id));
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
                },
                showModalSelectedPage: function (data) {
                  var pages = data.data

                  $scope.appFacebook.getFanPageArr(pages).then(function (data) {
                    document.body.scrollTop = 0;
                    $mdDialog.show({
                      scope: $scope.$new(),
                      controller: ModalPageFacebookCtrl,
                      template: constantMail.templateModalPageFacebook,
                      parent: angular.element(document.body),
                      clickOutsideToClose: true,
                      disableParentScroll: false,
                      locals: {
                        pages: data
                      },
                    }).then(function (response) {
                      $scope.appFacebook.fanPageSelected = response;
                    }, function () {
                      if (!$scope.appFacebook.fanPageSelected) {
                        $scope.appFacebook.facebook = false;
                      }
                    });
                  });
                },
                setFacebook: function (response, objPage) {
                  $scope.appFacebook.getPicturesPage(objPage.idPage).then(function (data) {
                    for (var i = 0; i < response.data.length; i++) {
                      let value = response.data[i];
                      console.log(parseInt(value.id) == parseInt(objPage.idPage));
                      if (parseInt(value.id) == parseInt(objPage.idPage)) {
                        $scope.appFacebook.facebook = true;
                        $scope.appFacebook.fanPageSelected = value;
                        $scope.appFacebook.fanPageSelected.picture = data.data.url;
                        $scope.appFacebook.descriptionPublish = objPage.description;
                        console.log($scope.appFacebook);
                        break;
                      }
                    }
                  });

                }
              };
              restService.getContentMail($scope.idMail).then(function (data) {
                $scope.showLinksGoogleAnalytics = (data.googleAnalytics == 1 ? true : false);
                $scope.googleAnalyticsLinks = data.content.links;
                if (typeof data.googleAnalyticsData !== 'undefined') {
                  $scope.googleAnalytics = data.googleAnalyticsData;
                }

                if (data.notificationEmails) {
                  $scope.dataadv.notifications = true;
                  $scope.dataadv.notificationEmails = data.notificationEmails;
                }

                if (data.statisticsEmails) {
                  $scope.dataadv.statistics = true;
                  $scope.dataadv.statisticsEmails = data.statisticsEmails;
                  $scope.dataadv.quantity = data.quantity;
                  $scope.dataadv.typeTime = data.typeTime;
                }

                if (data.facebook) {
                  var data = {idPage: data.facebook.idPage, description: data.facebook.description};
                  console.log("Valida si existe faceboog loko");
                  console.log(data);
                  if ($FB.loaded) {
                    $scope.appFacebook.login(data);
                  } else {
                    $timeout(function () {
                      $scope.appFacebook.login(data);
                    }, 100);
                  }

                }

                $scope.saveAdvanceOptions = function () {
                  if ($scope.appFacebook.facebook && $scope.appFacebook.fanPageSelected) {
                    $scope.dataadv.facebook = angular.copy($scope.appFacebook);
                  } else {
                    $scope.dataadv.facebook = false;
                  }

                  $scope.dataadv.googleAnalytics = $scope.showLinksGoogleAnalytics;
                  restService.saveAdvanceOptions($scope.idMail, $scope.dataadv).then(function (data) {
                    notificationService.primary("La informaciÃ³n del mail ha sido actualizada correctamente.");
                    window.location.href = fullUrlBase + templateBase + '/create#/shippingdate/' + $scope.idMail;
                  });
                }
                
                $scope.statusAttached = function (type) {
                  if(type == 'attachment'){
                    if($scope.fileadjuntpdf.files.length > 0){
                      $('#cancelDialog').addClass('dialog--open');
                      $scope.isModal = 'customizedpdf';
                    }
                  }
                  if (type == 'customizedpdf'){
                    if ($scope.fileadjunt.length > 0) {
                      $('#cancelDialog').addClass('dialog--open');
                      $scope.isModal = 'attachment';
                    }
                  }
                }
                
                $scope.closeModal = function (){
                  if($scope.isModal == 'attachment'){
                    $('.nav-tabs a[href="/#htab1"]').tab('show');
                  }
                  if($scope.isModal == 'customizedpdf'){
                    $('.nav-tabs a[href="/#htab2"]').tab('show');
                  }
                  $('.dialog').removeClass('dialog--open');
                }
                
                $scope.saveAdjunt = function (){
                  if($scope.isModal == 'attachment'){
                    //$scope.fileadjunt = [];
                    for(var i = 0; i<$scope.fileadjunt.length; i++){
                      restService.deleteAttached($scope.fileadjunt[i].idMailattachment).then(function () {
                        $scope.attach();
                      });
                    }
                    $('.nav-tabs a[href="/#htab2"]').tab('show');
                    $('.dialog').removeClass('dialog--open');
                  }
                  if($scope.isModal == 'customizedpdf'){
                    document.getElementById('filelist').innerHTML = '';
                    $scope.uploader = [];
                    if($scope.result.files.length){
                      restService.deleteCustomizedpdf($scope.idMail).then(function () {
                        $scope.attachpdf();
                      });
                    }
                    $('#next').hide('slow');
                    $('#gogo').hide('slow');
                    $('#resume').hide('slow');
                    $('#buttons').hide('slow');
                    $('#htab3').show('slow');
                    $('#filelist').show('slow');
                    $('#container').show('slow');

                    $('.nav-tabs a[href="/#htab1"]').tab('show');
                    $('.dialog').removeClass('dialog--open');
                  }
                }
                
                $scope.deleteAll = function (idMail) {
                  restService.deleteCustomizedpdf(idMail).then(function () {
                    //$scope.attachpdf();
                    $scope.fileadjuntpdf = {'files':[]};
                    $('#next').hide('slow');
                    $('#gogo').hide('slow');
                    $('#resume').hide('slow');
                    $('#buttons').show('slow');
                    $('#htab3').show('slow');
                    $('#filelist').show('slow');
                    $('#container').show('slow');

                    document.getElementById('filelist').innerHTML = '';
                  });
                }
              });
            }
          ])
          .controller('mailStructureEditorController', ['$scope', 'restService', 'notificationService', '$rootScope', '$stateParams', function ($scope, restService, notificationService, $rootScope, $stateParams) {
              $scope.initial = 0;
              $scope.page = 1;
              $scope.filter = "";
              $scope.search = function () {
                $scope.getAll();
              };
              $scope.forward = function () {
                $scope.initial += 1;
                $scope.page += 1;
                $scope.getAll();
              };
              $scope.fastforward = function () {
                $scope.initial = ($scope.mailstructure.total_pages - 1);
                $scope.page = $scope.mailstructure.total_pages;
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
              $scope.getAll = function () {
                restService.getAllMailStructure($scope.initial, $scope.filter).then(function (data) {
                  $scope.mailstructure = data;
                });
              };
              $scope.getAll();
            }]);
})();