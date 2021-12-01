'use strict';
(function () {
  angular.module("paymentplan.controller", [])
          .filter('propsFilter', function () {
            return function (items, props) {
              var out = [];
              if (angular.isArray(items)) {
                var keys = Object.keys(props);
                items.forEach(function (item) {
                  var itemMatches = !1;
                  for (var i = 0; i < keys.length; i++) {
                    var prop = keys[i];
                    var text = props[prop].toLowerCase();
                    if (item[prop].toString().toLowerCase().indexOf(text) !== -1) {
                      itemMatches = !0;
                      break
                    }
                  }
                  if (itemMatches) {
                    out.push(item)
                  }
                })
              } else {
                out = items
              }
              return out
            }
          })
          .constant('constantPaymentplan', {
            menssages: {
              errorService: "Debe seleccionar al menos un servicio",
              errorConfSms: "Debe seleccionar los datos de configuración para el servicio de SMS",
              errorConfMailmarketing: "Debe seleccionar los datos de configuración para el servicio de Email Marketing",
              errorMailtester: "Debe seleccionar los datos de configuración para el servicio de Mail Tester",
              errorCountry: "Debe seleccionar un pais"
            },
            boolean: {
              t: "true",
              f: "false"
            }
          })
          .controller("listController", ["$scope", "RestServices", "notificationService", function ($scope, RestServices, notificationService) {
              $scope.initial = 0;
              $scope.page = 1;
              $scope.forward = function () {
                $scope.initial += 1;
                $scope.page += 1;
                $scope.listPaymentPlan()
              };
              $scope.fastforward = function () {
                $scope.initial = ($scope.list.total_pages - 1);
                $scope.page = $scope.list.total_pages;
                $scope.listPaymentPlan()
              };
              $scope.backward = function () {
                $scope.initial -= 1;
                $scope.page -= 1;
                $scope.listPaymentPlan()
              };
              $scope.fastbackward = function () {
                $scope.initial = 0;
                $scope.page = 1;
                $scope.listPaymentPlan()
              };
              $scope.listPaymentPlan = function () {
                RestServices.listPaymentPlan($scope.initial, "").then(function (data) {
                  $scope.list = data
                })
              };
              $scope.listPaymentPlan();
              $scope.searchForName = function () {
                RestServices.listPaymentPlan($scope.initial, $scope.filterName).then(function (data) {
                  $scope.list = data
                })
              };
              $scope.openMod = function (id) {
                $scope.idPaymentPlan = id;
                openModal()
              };
              $scope.deletePaymentPlan = function () {
                RestServices.deletePaymentPlan($scope.idPaymentPlan).then(function (data) {
                  notificationService.warning(data.message);
                  closeModal();
                  $scope.listPaymentPlan()
                })
              }
            }])
          .controller("createController", ["$scope", "RestServices", "notificationService", "$state", "constantPaymentplan", function ($scope, RestServices, notificationService, $state, $constantPaymentplan) {
              $scope.data = {};
              $scope.data.status = !0;
              $scope.sms = {};
              $scope.email = {};
              $scope.mailtester = {};
              $scope.attachment = {};
              $scope.survey = {};
              $scope.smstwoway = {};
              $scope.landingpage = {};
              $scope.listCountry = function () {
                RestServices.listCountry().then(function (data) {
                  $scope.countries = data
                })
              };
              $scope.listCountry();
              $scope.fulltax = function (idCountry) {
                RestServices.listTax(idCountry).then(function (data) {
                  $scope.listtax = data
                })
              };
              $scope.loadTax = function () {
                $scope.fulltax($scope.data.idCountry)
              };
              $scope.services = function () {
                RestServices.services().then(function (data) {
                  $scope.listServices = data
                })
              };
              $scope.services();
              RestServices.plantypes().then(function (data) {
                $scope.plantypes = data
              });
              RestServices.adapter().then(function (data) {
                $scope.listadapter = data
              });
              RestServices.mta().then(function (data) {
                $scope.listmta = data
              });
              RestServices.urldomain().then(function (data) {
                $scope.listurldomain = data
              });
              RestServices.mailclass().then(function (data) {
                $scope.listmailclass = data
              });
              $scope.listaccountingMode = [{
                  value: "contact",
                  name: "Contacto"
                }, {
                  value: "sending",
                  name: "Envío"
                }];
              $scope.tabsms = !1;
              $scope.tabemail = !1;
              $scope.tabmailtester = !1;
              $scope.tabattachment = !1;
              $scope.tabsurvey = !1;
              $scope.tabsmstwoway = !1;
              $scope.tablandingpage = !1;
              $scope.hr = !1;
              $scope.changeService = function () {
                $scope.tabsms = !1;
                $scope.tabemail = !1;
                $scope.tabmailtester = !1;
                $scope.tabattachment = !1;
                $scope.tabsurvey = !1;
                $scope.tabsmstwoway = !1;
                $scope.tablandingpage = !1;
         
                if (angular.isDefined($scope.service.idServices)) {
                  for (var i = 0; i < $scope.service.idServices.length; i++) {
                    if ($scope.service.idServices[i] == 1) {
                      $scope.hr = !0;
                      $scope.tabsms = !0
                    }
                    if ($scope.service.idServices[i] == 2) {
                      $scope.hr = !0;
                      $scope.tabemail = !0
                    }
                    if ($scope.service.idServices[i] == 3) {
                      $scope.hr = !0;
                      $scope.tabmailtester = !0
                    }
                    if ($scope.service.idServices[i] == 5) {
                      $scope.hr = !0;
                      $scope.tabsurvey = !0
                    }
                    if ($scope.service.idServices[i] == 6) {
                      $scope.hr = !0;
                      $scope.tabattachment = !0
                    }
                    if ($scope.service.idServices[i] == 7) {
                      $scope.hr = !0;
                      $scope.tabsmstwoway = !0
                    }
                    if ($scope.service.idServices[i] == 8) {
                      $scope.hr = !0;
                      $scope.tablandingpage = !0
                    }
                  }
                } else if (angular.isUndefined($scope.service.idServices)) {
                  $scope.tabsms = !1;
                  $scope.tabemail = !1;
                  $scope.tabmailtester = !1;
                  $scope.tabattachment = !1;
                  $scope.tabsurvey = !1;
                  $scope.tabsmstwoway = !1;
                  $scope.tablandingpage = !1;
                  $scope.hr = !1
                }
              };
              $scope.savePaymentPlan = function () {
                if ($scope.service.idServices.length < 1) {
                  notificationService.error($constantPaymentplan.menssages.errorService);
                  return !1
                }
                for (var i = 0; i < $scope.service.idServices.length; i++) {
                  if (parseInt($scope.service.idServices[i]) === 1) {
                    if (angular.isUndefined($scope.sms)) {
                      notificationService.error($constantPaymentplan.menssages.errorConfSms);
                      return !1
                    }
                  } else if (parseInt($scope.service.idServices[i]) === 2) {
                    if (angular.isUndefined($scope.email)) {
                      notificationService.error($constantPaymentplan.menssages.errorConfMailmarketing);
                      return !1
                    }
                  } else if (parseInt($scope.service.idServices[i]) === 3) {
                    if (angular.isUndefined($scope.mailtester)) {
                      notificationService.error($constantPaymentplan.menssages.errorMailtester);
                      return !1
                    }
                  }
                }
                $scope.fullData = {
                  data: $scope.data,
                  tax: $scope.tax,
                  service: $scope.service,
                  sms: (!$scope.tabsms) ? null : $scope.sms,
                  email: (!$scope.tabemail) ? null : $scope.email,
                  mailtester: (!$scope.tabmailtester) ? null : $scope.mailtester,
                  attachment: (!$scope.tabattachment) ? null : $scope.attachment,
                  survey: (!$scope.tabsurvey) ? null : $scope.survey,
                  smstwoway: (!$scope.tabsmstwoway) ? null : $scope.smstwoway,
                  landingpage: (!$scope.tablandingpage) ? null : $scope.landingpage
                };
    
                RestServices.createPaymentPlan($scope.fullData).then(function (data) {
                  notificationService.success(data.message);
                  $state.go("index")
                })
              };
              $scope.search = function (serv, data) {
                RestServices.pricelist(serv, data).then(function (data) {
                  if (serv == 1) {
                    $scope.listpricelistsms = data
                  } else if (serv == 2) {
                    $scope.listpricelistemail = data
                  } else if (serv == 3) {
                    $scope.listpricelistmailterster = data
                  } else if (serv == 5) {
                    $scope.listpricelistsurvey = data
                  } else if (serv == 6) {
                    $scope.listpricelistattachment = data
                  } else if (serv == 7) {
                    $scope.listpricelistsmstwoway = data
                  } else if (serv == 8) {
                    $scope.listpricelistlandingpage = data
                  }
                })
              };
              $scope.validatecourtesyplan = function () {
                if ($scope.data.courtesy == !0) {
                  if (typeof $scope.data.idCountry == "undefined") {
                    notificationService.error($constantPaymentplan.menssages.errorCountry);
                    $scope.data.courtesy = $constantPaymentplan.boolean.f;
                    return
                  }
                  RestServices.ValidateCourtesyPlan($scope.data.courtesy, $scope.data.idCountry).then(function (data) {
                    if (data.message) {
                      notificationService.error(data.message);
                      $scope.data.courtesy = $constantPaymentplan.boolean.f
                    }
                  })
                }
              };

            }])
          .controller("editController", ["$scope", "RestServices", "notificationService", "$stateParams", "$templateCache", "$compile", "$state", function ($scope, RestServices, notificationService, $stateParams, $templateCache, $compile, $state) {
              $scope.sms = {};
              $scope.email = {};
              $scope.mailtester = {};
              $scope.attachment = {};
              $scope.survey = {};
              $scope.smstwoway = {};
              $scope.landingpage = {};
              $scope.services = function () {
                RestServices.services().then(function (data) {
                  $scope.listServices = data
                })
              };
              $scope.services();
              $scope.tabsms = !1;
              $scope.tabemail = !1;
              $scope.tabmailtester = !1;
              $scope.tabattachment = !1;
              $scope.tabsurvey = !1;
              $scope.tabsmstwoway = !1;
              $scope.tablandingpage = !1;
              $scope.hr = !1;
              $scope.changeService = function () {
                $scope.tabsms = !1;
                $scope.tabemail = !1;
                $scope.tabmailtester = !1;
                $scope.tabattachment = !1;
                $scope.tabsurvey = !1;
                $scope.tabsmstwoway = !1;
                $scope.tablandingpage = !1;
                if (angular.isDefined($scope.service.idServices)) {
                  for (var i = 0; i < $scope.service.idServices.length; i++) {
                    if ($scope.service.idServices[i] == 1) {
                      $scope.hr = !0;
                      $scope.tabsms = !0
                    }
                    if ($scope.service.idServices[i] == 2) {
                      $scope.hr = !0;
                      $scope.tabemail = !0
                    }
                    if ($scope.service.idServices[i] == 3) {
                      $scope.hr = !0;
                      $scope.tabmailtester = !0
                    }
                    if ($scope.service.idServices[i] == 5) {
                      $scope.hr = !0;
                      $scope.tabsurvey = !0
                    }
                    if ($scope.service.idServices[i] == 6) {
                      $scope.hr = !0;
                      $scope.tabattachment = !0
                    }
                    if ($scope.service.idServices[i] == 7) {
                      $scope.hr = !0;
                      $scope.tabsmstwoway = !0
                    }
                    if ($scope.service.idServices[i] == 8) {
                      $scope.hr = !0;
                      $scope.tablandingpage = !0
                    }
                  }
                }
                if ((angular.isUndefined($scope.service.idServices)) || ($scope.service.idServices.length === 0)) {
                  $scope.tabsms = !1;
                  $scope.tabemail = !1;
                  $scope.tabmailtester = !1;
                  $scope.tabattachment = !1;
                  $scope.tabsurvey = !1;
                  $scope.tabsmstwoway = !1;
                  $scope.tablandingpage = !1;
                  $scope.hr = !1
                }
              };
              $scope.tax = {};
              $scope.service = {};
              $scope.getPaymentPlan = function () {
                RestServices.getPaymentPlan($stateParams.idPaymentPlan).then(function (data) {
                  $scope.data = data;
                  $scope.data.status = (parseInt(data.status) === 1);
                  $scope.fulltax($scope.data.idCountry);
                  $scope.initialize($scope.data)
                })
              };
              $scope.getPaymentPlan();
              $scope.fulltax = function (idCountry) {
                RestServices.listTax(idCountry).then(function (data) {
                  $scope.listtax = data
                })
              };
              $scope.loadTax = function () {
                $scope.fulltax($scope.data.idCountry)
              };
              $scope.search = function (serv, data) {
                RestServices.pricelist(serv, data).then(function (data) {
                  if (serv == 1) {
                    $scope.listpricelistsms = data
                  } else if (serv == 2) {
                    $scope.listpricelistemail = data
                  } else if (serv == 3) {
                    $scope.listpricelistmailterster = data
                  } else if (serv == 5) {
                    $scope.listpricelistsurvey = data
                  } else if (serv == 6) {
                    $scope.listpricelistattachment = data
                  } else if (serv == 7) {
                    $scope.listpricelistsmstwoway = data
                  } else if (serv == 8) {
                    $scope.listpricelistlandingpage = data
                  }
                })
              };
              $scope.initialize = function (data) {
                $scope.listypes = [{
                    value: "public",
                    name: "Público"
                  }, {
                    value: "private",
                    name: "Privado"
                  }];
                $scope.listaccountingMode = [{
                    value: "contact",
                    name: "Contacto"
                  }, {
                    value: "sending",
                    name: "Envío"
                  }];
                RestServices.listCountry().then(function (data) {
                  $scope.countries = data
                });
                RestServices.plantypes().then(function (data) {
                  $scope.plantypes = data
                });
                RestServices.pricelist(1, "").then(function (data) {
                  $scope.listpricelistsms = data
                });
                RestServices.pricelist(2, "").then(function (data) {
                  $scope.listpricelistemail = data
                });
                RestServices.pricelist(7, "").then(function (data) {
                  $scope.listpricelistsmstwoway = data
                });
                RestServices.pricelist(8, "").then(function (data) {
                  $scope.listpricelistlandingpage = data
                });
                RestServices.adapter().then(function (data) {
                  $scope.listadapter = data
                });
                RestServices.mta().then(function (data) {
                  $scope.listmta = data
                });
                RestServices.urldomain().then(function (data) {
                  $scope.listurldomain = data
                });
                RestServices.mailclass().then(function (data) {
                  $scope.listmailclass = data
                });
                var idTax = [];
                data.paymentplanxtax.forEach(function (item, index) {
                  idTax.push(item.idTax)
                });
                $scope.tax.idTax = idTax;
                var idService = [];
                data.services.forEach(function (item, index) {
                  idService.push(item.idServices)
                });
                $scope.service.idServices = idService;
                if (!angular.isUndefined(data.services[0]) || !angular.isUndefined(data.services[1])) {
                  if ($scope.searchArray(data, "sms")) {
                    $scope.servsms = $scope.getArray(data, "Sms");
                    $scope.sms = $scope.servsms;
                    var idAdapter = [];
                    $scope.servsms.ppxsxadapter.forEach(function (item, index) {
                      idAdapter.push(item.idAdapter)
                    });
                    $scope.sms.idAdapter = idAdapter
                  }
                  if ($scope.searchArray(data, "sms doble-via")) {
                    $scope.servsmstwoway = $scope.getArray(data, "Sms Doble-via");
                    $scope.smstwoway = $scope.servsmstwoway
                  }
                  if ($scope.searchArray(data, "landing page")) {
                    $scope.servlandingpage = $scope.getArray(data, "Landing Page");
                    $scope.landingpage = $scope.servlandingpage
                  }
                  if ($scope.searchArray(data, "email marketing")) {
                    $scope.servemail = $scope.getArray(data, "Email Marketing");
                    $scope.email = $scope.servemail;
                    $scope.listaccountingMode = [{
                        value: "contact",
                        name: "Contacto"
                      }, {
                        value: "sending",
                        name: "Envío"
                      }];
                    var idMta = [];
                    $scope.servemail.ppxsxmta.forEach(function (item, index) {
                      idMta.push(item.idMta)
                    });
                    $scope.email.idMta = idMta;
                    var idUrldomain = [];
                    $scope.servemail.ppxsxurldomain.forEach(function (item, index) {
                      idUrldomain.push(item.idUrldomain)
                    });
                    $scope.email.idUrldomain = idUrldomain;
                    var idMailClass = [];
                    $scope.servemail.ppxsxmailclass.forEach(function (item, index) {
                      idMailClass.push(item.idMailClass)
                    });
                    $scope.email.idMailClass = idMailClass
                  }
                  if ($scope.searchArray(data, "mail tester")) {
                    $scope.servmailtester = $scope.getArray(data, "Mail Tester");
                    $scope.mailtester = $scope.servmailtester
                  }
                  if ($scope.searchArray(data, "adjuntar archivos")) {
                    $scope.servattachment = $scope.getArray(data, "Adjuntar Archivos");
                    $scope.attachment = $scope.servattachment
                  }
                  if ($scope.searchArray(data, "survey")) {
                    $scope.servsurvey = $scope.getArray(data, "Survey");
                    $scope.survey = $scope.servsurvey;
                    var idMta = [];
                    $scope.servsurvey.ppxsxmta.forEach(function (item, index) {
                      idMta.push(item.idMta)
                    });
                    $scope.survey.idMta = idMta;
                    var idUrldomain = [];
                    $scope.servsurvey.ppxsxurldomain.forEach(function (item, index) {
                      idUrldomain.push(item.idUrldomain)
                    });
                    $scope.survey.idUrldomain = idUrldomain;
                    var idMailClass = [];
                    $scope.servsurvey.ppxsxmailclass.forEach(function (item, index) {
                      idMailClass.push(item.idMailClass)
                    });
                    $scope.survey.idMailClass = idMailClass
                  }
                }
                $scope.changeService()
              };
              $scope.searchArray = function (array, valorabuscar) {
                for (var i = 0; i < array.services.length; i++) {
                  if (array.services[i].name.toLowerCase() === valorabuscar.toLowerCase()) {
                    return !0;
                    break
                  }
                }
                return !1
              };
              $scope.getArray = function (array, valorabuscar) {
                for (var i = 0; i < array.services.length; i++) {
                  if (array.services[i].name === valorabuscar) {
                    return array.services[i]
                  }
                }
              };
              $scope.editPaymentPlan = function () {
                if ($scope.service.idServices.length < 1) {
                  notificationService.error("Debe seleccionar al menos un servicio");
                  return !1
                }
                for (var i = 0; i < $scope.service.idServices.length; i++) {
                  if (parseInt($scope.service.idServices[i]) === 1) {
                    if (angular.isUndefined($scope.sms)) {
                      notificationService.error("Debe seleccionar los datos de configuración para el servicio de SMS");
                      return !1
                    }
                  } else if (parseInt($scope.service.idServices[i]) === 2) {
                    if (angular.isUndefined($scope.email)) {
                      notificationService.error("Debe seleccionar los datos de configuración para el servicio de Email Marketing");
                      return !1
                    }
                  } else if (parseInt($scope.service.idServices[i]) === 3) {
                    if (angular.isUndefined($scope.mailtester)) {
                      notificationService.error("Debe seleccionar los datos de configuración para el servicio de Mail Tester");
                      return !1
                    }
                  } else if (parseInt($scope.service.idServices[i]) === 7) {
                    if (angular.isUndefined($scope.smstwoway)) {
                      notificationService.error("Debe seleccionar los datos de configuración para el servicio de Sms doble-via");
                      return !1
                    }
                  } else if (parseInt($scope.service.idServices[i]) === 8) {
                    if (angular.isUndefined($scope.landingpage)) {
                      notificationService.error("Debe seleccionar los datos de configuración para el servicio de Sms doble-via");
                      return !1
                    }
                  }
                }
                $scope.fullData = {
                  data: $scope.data,
                  tax: $scope.tax,
                  service: $scope.service,
                  sms: (!$scope.tabsms) ? null : $scope.sms,
                  email: (!$scope.tabemail) ? null : $scope.email,
                  mailtester: (!$scope.tabmailtester) ? null : $scope.mailtester,
                  attachment: (!$scope.tabattachment) ? null : $scope.attachment,
                  survey: (!$scope.tabsurvey) ? null : $scope.survey,
                  smstwoway: (!$scope.tabsmstwoway) ? null : $scope.smstwoway,
                  landingpage: (!$scope.tablandingpage) ? null : $scope.landingpage
                };
                RestServices.editPaymentPlan($scope.fullData).then(function (data) {
                  notificationService.info(data.message);
                  $state.go("index")
                })
              }
            }])
          .controller("showController", ["$scope", "RestServices", "notificationService", "$stateParams", "$templateCache", "$compile", "$state", function ($scope, RestServices, notificationService, $stateParams, $templateCache, $compile, $state) {
              $scope.confserv = function () {
                RestServices.getconfigServices($stateParams.idPaymentPlan).then(function (data) {
                  $scope.data = data;
                  $scope.serv($scope.data.services)
                })
              };
              $scope.tabsms = !1;
              $scope.tabemail = !1;
              $scope.tabsmstwoway = !1;
              $scope.tablandingpage = !1;
              $scope.hr = !1;
              $scope.serv = function (service) {
                $scope.hr = !0;
                for (var i = 0; i < service.length; i++) {
                  if (parseInt(service[i].idService) === 1) {
                    $scope.sms = service[i];
                    $scope.tabsms = !0
                  } else if (parseInt(service[i].idService) === 2) {
                    $scope.email = service[i];
                    $scope.tabemail = !0
                  } else if (parseInt(service[i].idService) === 7) {
                    $scope.smstwoway = service[i];
                    $scope.tabsmstwoway = !0
                  } else if (parseInt(service[i].idService) === 8) {
                    $scope.landingpage = service[i];
                    $scope.tablandingpage = !0
                  }
                }
              };
              $scope.confserv()
            }])
})() 