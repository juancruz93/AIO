(function () {
  angular.module('automaticcampaign.controllers', [])
          .filter('getGmtById', function () {
            return function (input, id) {
              var i = 0,
                      len = input.length;
              for (; i < len; i++) {
                if (input[i].gmt == id) {
                  return input[i];
                }
              }
              return null;
            }
          })
          .controller('createController', ['$scope', '$rootScope', '$q', 'notificationService', 'restServices', 'moment', '$filter', '$window', '$location', '$state', function ($scope, $rootScope, $q, notificationService, restServices, moment, $filter, $window, $location, $state) {
              $('#datetimepickerStart').datetimepicker({
                format: 'yyyy-MM-dd hh:mm',
                language: 'es',
                startDate: new Date()
              })
                      .on('changeDate', function (ev) {
                        $scope.applyTextGmt();
                        $scope.$apply();
                      });
              $('#datetimepickerEnd').datetimepicker({
                format: 'yyyy-MM-dd hh:mm',
                language: 'es',
                startDate: new Date()
              })
                      .on('changeDate', function (ev) {
                        $scope.applyTextGmt();
                        $scope.$apply();
                      });
              $scope.showTextGmtStart = false;
              $scope.showTextGmtEnd = false;
              var idMax = 10;
              $scope.formCampaign = {};
              $scope.getServices = function () {
                restServices.getservices().then(function (data) {
                  $scope.services = data;
                  $scope.items = [{
                      class: 'item-success-inverted-no-hover small-text text-center',
                      name: 'Servicio',
                      icon: false,
                      iconClass: "fa fa-envelope-o"
                    }];
                  for (var i = 0; i < data.length; i++) {
                    if (data[i].service == "sms") {
                      $scope.items.push({
                        class: 'small-text text-center cursor-pointer',
                        name: 'Sms',
                        theme: 'service',
                        method: 'sms',
                        templatepopover: fullUrlBase + "flowchart/popoversms",
                        titlepopover: 'Configurar sms',
                        disabled: false,
                        icon: true,
                        iconClass: "fa fa-mobile fa-2x",
                        image: fullUrlBase + "images/automatic/SMSA-01.jpg"
                      });
                    }
                    if (data[i].service == "email marketing") {
                      $scope.items.push({
                        class: 'small-text text-center cursor-pointer',
                        name: 'Mail',
                        theme: 'service',
                        method: 'email',
                        templatepopover: fullUrlBase + "flowchart/popovermail",
                        titlepopover: 'Configurar correo',
                        disabled: false,
                        icon: true,
                        iconClass: "fa fa-envelope-o fa-2x",
                        image: fullUrlBase + "images/automatic/correoa-01.jpg"
                      });
                    }
                    /*if (data[i].service == "survey") {
                     $scope.items.push({
                     class: 'small-text text-center cursor-pointer',
                     name: 'Encuestas',
                     theme: 'service',
                     method: 'survey',
                     templatepopover: fullUrlBase + "flowchart/popoversurvey",
                     titlepopover: 'Configurar survey',
                     disabled: false,
                     icon: true,
                     iconClass: "fa fa-address-book-o fa-2x",
                     image: fullUrlBase + "images/general/forms.png"
                     });
                     }*/
                  }
                  $scope.items.push({
                    class: 'item-primary-inverted-no-hover small-text text-center',
                    name: 'Operadores',
                    icon: false,
                    iconClass: "fa fa-envelope-o"
                  });
                  $scope.items.push({
                    class: 'small-text  text-center cursor-pointer',
                    name: 'Tiempo',
                    theme: 'operator',
                    method: 'time',
                    templatepopover: fullUrlBase + "flowchart/popovertime",
                    titlepopover: 'Operador por tiempo',
                    disabled: true,
                    icon: true,
                    iconClass: "fa fa-clock-o fa-2x",
                    image: fullUrlBase + "images/automatic/tiempoa-01.jpg"
                  });
                  $scope.items.push({
                    class: 'small-text  text-center cursor-pointer',
                    name: 'Accion',
                    theme: 'operator',
                    method: 'actions',
                    templatepopover: fullUrlBase + "flowchart/popoveraction",
                    titlepopover: 'Operador por accion',
                    disabled: true,
                    icon: true,
                    iconClass: "fa fa-cogs fa-2x",
                    image: fullUrlBase + "images/automatic/accion-01.jpg"
                  });
                  $scope.items.push({
                    class: 'small-text  text-center cursor-pointer',
                    name: 'Clicks',
                    theme: 'operator',
                    method: 'clicks',
                    templatepopover: fullUrlBase + "flowchart/popoverclick",
                    titlepopover: 'Operador por click',
                    disabled: true,
                    icon: true,
                    iconClass: "fa fa-link fa-2x",
                    image: fullUrlBase + "images/automatic/click-01.jpg"
                  });
                  restServices.validateService($scope.chartViewModel.data, data);
                }).catch(function (data) {
                  $scope.services = data;
                  $scope.items = [{
                      class: 'item-success-inverted-no-hover small-text text-center  text-center',
                      name: 'Servicio',
                      icon: false,
                      iconClass: "fa fa-envelope-o"
                    }];
                  $scope.items.push({
                    class: 'item-primary-inverted-no-hover small-text text-center  text-center',
                    name: 'Operadores',
                    icon: false,
                    iconClass: "fa fa-envelope-o"
                  });
                  $scope.items.push({
                    class: 'small-text  text-center cursor-pointer',
                    name: 'Tiempo',
                    theme: 'operator',
                    method: 'time',
                    templatepopover: fullUrlBase + "flowchart/popovertime",
                    titlepopover: 'Operador por tiempo',
                    disabled: true,
                    icon: true,
                    iconClass: "fa fa-clock-o fa-2x",
                    image: fullUrlBase + "images/automatic/tiempoa-01.jpg"
                  });
                  $scope.items.push({
                    class: 'small-text  text-center cursor-pointer',
                    name: 'Accion',
                    theme: 'operator',
                    method: 'actions',
                    templatepopover: fullUrlBase + "flowchart/popoveraction",
                    titlepopover: 'Operador por accion',
                    disabled: true,
                    icon: true,
                    iconClass: "fa fa-cogs fa-2x",
                    image: fullUrlBase + "images/automatic/accion-01.jpg"
                  });
                  $scope.items.push({
                    class: 'small-text  text-center cursor-pointer',
                    name: 'Clicks',
                    theme: 'operator',
                    method: 'clicks',
                    templatepopover: fullUrlBase + "flowchart/popoverclick",
                    titlepopover: 'Operador por click',
                    disabled: true,
                    icon: true,
                    iconClass: "fa fa-link fa-2x",
                    image: fullUrlBase + "images/automatic/click-01.jpg"
                  });
                });
              }


              var chartDataModel = {
                nodes: [{
                    name: "Destinatario(s)",
                    id: 0,
                    x: 170,
                    y: 25,
                    width: 100,
                    image: fullUrlBase + "images/automatic/destinatariosa-01.jpg",
                    theme: "primary",
                    method: "primary",
                    templatepopover: fullUrlBase + "flowchart/popoversegment",
                    titlepopover: "Selecciona a quien quiere enviar esta campaña automática.",
                    outputConnectors: [{
                        name: ""
                      }],
                    sendData: {}
                  }],
                connections: []
              };

              $scope.getIdMax = function () {
                var idMax = 0;
                for (var i = 0; i < $scope.chartViewModel.nodes.length; i++) {
                  var idNode = $scope.chartViewModel.nodes[i].getId();
                  if (idNode > idMax) {
                    idMax = idNode;
                  }
                }
                return idMax;
              }

              $scope.addNewNode = function (item) {
                if (typeof item.method == "undefined") {
                  return;
                }

                //          if (item.disabled) {
                //            return;
                //          }
                //          var themePre = $scope.chartViewModel.nodes[$scope.chartViewModel.nodes.length - 1].getTheme();
                //          if (item.theme == "service") {
                //            for (var i = 0; i < $rootScope.items.length; i++) {
                //              if ($rootScope.items[i].theme == "service") {
                //                $rootScope.items[i].disabled = true;
                //                $rootScope.items[i].class += " disabled text-center cursor-pointer";
                //              } else if ($rootScope.items[i].theme == "operator") {
                //                $rootScope.items[i].disabled = false;
                //                $rootScope.items[i].class = "small-text text-center cursor-pointer";
                //              }
                //            }
                //          } else {
                //            for (var i = 0; i < $rootScope.items.length; i++) {
                //              if ($rootScope.items[i].theme == "operator") {
                //                $rootScope.items[i].disabled = true;
                //                $rootScope.items[i].class += " disabled text-center cursor-pointer";
                //              } else if ($rootScope.items[i].theme == "service") {
                //                $rootScope.items[i].disabled = false;
                //                $rootScope.items[i].class = "small-text text-center cursor-pointer";
                //              }
                //            }
                //          }
                var newNodeDataModel = {
                  name: item.name,
                  id: $scope.getIdMax() + 1,
                  x: 10,
                  y: 50,
                  width: 100,
                  theme: item.theme,
                  method: item.method,
                  image: item.image,
                  templatepopover: item.templatepopover,
                  titlepopover: item.titletemplate,
                  inputConnectors: [{
                      name: ""
                    }],
                  outputConnectors: [{
                      name: ""
                    }],
                  sendData: {}
                };
                if (item.method == "actions" || item.method == "clicks" || item.method == "links") {
                  newNodeDataModel.outputConnectors.push({
                    name: ""
                  });
                }
                $scope.chartViewModel.addNode(newNodeDataModel);
              };
              $scope.createAutomaticCampaign = function (option) {
                restServices.validateJsonAutomaticCampaign($scope.chartViewModel.data, $scope.services).then(function (objData) {
                  $scope.optionCreate = option;
                  if (option == 1) {
                    if (typeof $scope.idCampaign != "undefined") {
                      restServices.updateAutomaticCampaignConfiguration($scope.chartViewModel.data, $scope.idCampaign).then(function (data) {
                        notificationService.primary(data.message);
                      });
                    } else {
                      restServices.createAutomaticCampaignDraft($scope.chartViewModel.data).then(function (data) {
                        notificationService.success(data.message);
                        $scope.idCampaign = data.idCampaign;
                      });
                    }
                  } else {
                    $('#createAutomaticCampaign').addClass('dialog--open');
                  }

                });
              }

              $scope.toReturn = function () {
                $state.go('index');
              }

              $scope.insCampaign = function () {
                $scope.formCampaign.startDate = angular.copy($scope.dateGmtStart);
                $scope.formCampaign.endDate = angular.copy($scope.dateGmtEnd);
                restServices.validateFormCampaign($scope.formCampaign).then(function (data) {
                  var objSend = {
                    formCampaign: data,
                    objCampaign: $scope.chartViewModel.data
                  };
                  if (typeof $scope.idCampaign == "undefined") {
                    restServices.createAutomaticCampaign(objSend).then(function (data) {
                      notificationService.success(data.message);
                      $('#createAutomaticCampaign').removeClass('dialog--open');
                      $state.go('index');
                    });
                  } else {
                    restServices.updateAutomaticCampaignAll(objSend, $scope.idCampaign).then(function (data) {
                      notificationService.primary(data.message);
                      $('#createAutomaticCampaign').removeClass('dialog--open');
                      $state.go('index');
                    });
                  }
                  //
                });
              }

              $scope.getallcategory = function () {
                restServices.getallcategory().then(function (data) {
                  $scope.listCategory = data;
                });
                restServices.getGmt().then(function (data) {
                  $scope.listZonaHoraria = data;
                  var filter = $filter('getGmtById')($scope.listZonaHoraria, "-0500");
                  $scope.formCampaign.gmt = filter.gmt;
                });
              }
              $scope.getallcategory();
              $scope.applyTextGmt = function () {
                if ($("#datestartCampaign").val() != '') {
                  $scope.showTextGmtStart = true;
                } else {
                  $scope.showTextGmtStart = false;
                  $scope.textGmtStart = '';
                }
                if ($("#dateendCampaign").val() != '') {
                  $scope.showTextGmtEnd = true;
                } else {
                  $scope.showTextGmtEnd = false;
                  $scope.textGmtEnd = '';
                }
                if ($scope.showTextGmtStart) {
                  var StartUnix = moment($("#datestartCampaign").val()).utc().valueOf();
                  $scope.dateGmtStart = moment(StartUnix).utcOffset($scope.formCampaign.gmt).format('YYYY-MM-DD HH:mm');
                  //            console.log(StartUnix,$scope.formCampaign.gmt);
                }
                if ($scope.showTextGmtEnd) {
                  var EndUnix = moment($("#dateendCampaign").val()).utc().valueOf();
                  $scope.dateGmtEnd = moment(EndUnix).utcOffset($scope.formCampaign.gmt).format('YYYY-MM-DD HH:mm');
                  //            console.log(EndUnix,$scope.formCampaign.gmt);
                }
              }

              $scope.chartViewModel = new flowchart.ChartViewModel(chartDataModel);
              $scope.getServices();
            }])
          .controller('editController', ['$scope', '$rootScope', '$q', '$stateParams', 'restServices', 'notificationService', '$window', '$location', '$state', function ($scope, $rootScope, $q, $stateParams, restServices, notificationService, $window, $location, $state) {
              $('#datetimepickerStart').datetimepicker({
                format: 'yyyy-MM-dd hh:mm',
                language: 'es',
                startDate: new Date()
              })
                      .on('changeDate', function (ev) {
                        $scope.applyTextGmt();
                        $scope.$apply();
                      });
              $('#datetimepickerEnd').datetimepicker({
                format: 'yyyy-MM-dd hh:mm',
                language: 'es',
                startDate: new Date()
              })
                      .on('changeDate', function (ev) {
                        $scope.applyTextGmt();
                        $scope.$apply();
                      });
              $scope.campaign = {};
              $scope.formCampaign = {};
              $scope.complet = false;
              if (typeof $stateParams.idautomaticcampaign == "undefined") {
                $state.go('index');
              }

              $scope.idautomaticcampaign = $stateParams.idautomaticcampaign;
              $scope.getautomaticcampaign = function () {
                restServices.editAutomaticCampaign($scope.idautomaticcampaign).then(function (data) {
                  //            console.log(data);
                  $scope.campaign = data.data.campaign;
                  $scope.setFormCampaing(data.data.configuration);
                  $scope.validStatus($scope.campaign);
                  $scope.initTextGmt();
                }).catch(function (data) {
                  //            console.log(data.data.configuration);
                  if (data.data.configuration == null) {
                    $state.go('index');
                  }
                  $scope.campaign = data.data.campaign;
                  $scope.errorCampaign = true;
                  $scope.msgErrorCampaign = data.message;
                  $scope.setFormCampaing(data.data.configuration);
                  $scope.validStatus($scope.campaign);
                  $scope.initTextGmt();
                });
              }


              $scope.getautomaticcampaign();
              $scope.validStatus = function (campaign) {
                if (campaign.status == 'confirmed') {
                  $('#updateStatusCampaign').addClass('dialog--open');
                }
              }

              $scope.closeModalStatus = function () {
                $state.go('index');
              }

              $scope.toReturn = function () {
                $state.go('index');
              }

              $scope.updateStatusCampaign = function () {
                restServices.updateStatusCampaign($scope.idautomaticcampaign).then(function (data) {
                  notificationService.primary(data.message);
                  $('#updateStatusCampaign').removeClass('dialog--open');
                });
              }

              $scope.setFormCampaing = function (configuration) {
                $scope.formCampaign = angular.copy($scope.campaign);
                //          $scope.formCampaign.status = ($scope.campaign.status == 1);
                $("#datestartCampaign").val($scope.campaign.startDate);
                $("#dateendCampaign").val($scope.campaign.endDate);
                $scope.chartViewModel = new flowchart.ChartViewModel(configuration);
                //          var themePre = $scope.chartViewModel.nodes[$scope.chartViewModel.nodes.length - 1].getTheme();
                //          if (themePre == "service") {
                //            for (var i = 0; i < $scope.items.length; i++) {
                //              if ($scope.items[i].theme == "service") {
                //                $scope.items[i].disabled = true;
                //                $scope.items[i].class += " disabled";
                //              } else if ($scope.items[i].theme == "operator") {
                //                $scope.items[i].disabled = false;
                //                $scope.items[i].class = "small-text";
                //              }
                //            }
                //          } else {
                //            for (var i = 0; i < $scope.items.length; i++) {
                //              if ($scope.items[i].theme == "operator") {
                //                $scope.items[i].disabled = true;
                //                $scope.items[i].class += " disabled";
                //              } else if ($scope.items[i].theme == "service") {
                //                $scope.items[i].disabled = false;
                //                $scope.items[i].class = "small-text";
                //              }
                //            }
                //          }
                $scope.complet = true;
                $scope.getServices();
              }

              $scope.getServices = function () {
                restServices.getservices().then(function (data) {
                  $scope.services = data;
                  $scope.items = [{
                      class: 'item-success-inverted-no-hover small-text text-center',
                      name: 'Servicio',
                      icon: false,
                      iconClass: "fa fa-envelope-o"
                    }];
                  for (var i = 0; i < data.length; i++) {
                    if (data[i].service == "sms") {
                      $scope.items.push({
                        class: 'small-text text-center cursor-pointer',
                        name: 'Sms',
                        theme: 'service',
                        method: 'sms',
                        templatepopover: fullUrlBase + "flowchart/popoversms",
                        titlepopover: 'Configurar sms',
                        disabled: false,
                        icon: true,
                        iconClass: "fa fa-mobile fa-2x",
                        image: fullUrlBase + "images/automatic/SMSA-01.jpg"
                      });
                    }
                    if (data[i].service == "email marketing") {
                      $scope.items.push({
                        class: 'small-text text-center cursor-pointer',
                        name: 'Mail',
                        theme: 'service',
                        method: 'email',
                        templatepopover: fullUrlBase + "flowchart/popovermail",
                        titlepopover: 'Configurar correo',
                        disabled: false,
                        icon: true,
                        iconClass: "fa fa-envelope-o fa-2x",
                        image: fullUrlBase + "images/automatic/correoa-01.jpg"
                      });
                    }
                  }
                  $scope.items.push({
                    class: 'item-primary-inverted-no-hover small-text text-center',
                    name: 'Operadores',
                    icon: false,
                    iconClass: "fa fa-envelope-o"
                  });
                  $scope.items.push({
                    class: 'small-text  text-center cursor-pointer',
                    name: 'Tiempo',
                    theme: 'operator',
                    method: 'time',
                    templatepopover: fullUrlBase + "flowchart/popovertime",
                    titlepopover: 'Operador por tiempo',
                    disabled: true,
                    icon: true,
                    iconClass: "fa fa-clock-o fa-2x",
                    image: fullUrlBase + "images/automatic/tiempoa-01.jpg"
                  });
                  $scope.items.push({
                    class: 'small-text  text-center cursor-pointer',
                    name: 'Accion',
                    theme: 'operator',
                    method: 'actions',
                    templatepopover: fullUrlBase + "flowchart/popoveraction",
                    titlepopover: 'Operador por accion',
                    disabled: true,
                    icon: true,
                    iconClass: "fa fa-cogs fa-2x",
                    image: fullUrlBase + "images/automatic/accion-01.jpg"
                  });
                  $scope.items.push({
                    class: 'small-text  text-center cursor-pointer',
                    name: 'Clicks',
                    theme: 'operator',
                    method: 'clicks',
                    templatepopover: fullUrlBase + "flowchart/popoverclick",
                    titlepopover: 'Operador por click',
                    disabled: true,
                    icon: true,
                    iconClass: "fa fa-link fa-2x",
                    image: fullUrlBase + "images/automatic/click-01.jpg"
                  });
                  restServices.validateService($scope.chartViewModel.data, data);
                }).catch(function (data) {
                  $scope.services = data;
                  $scope.items = [{
                      class: 'item-success-inverted-no-hover small-text text-center  text-center',
                      name: 'Servicio',
                      icon: false,
                      iconClass: "fa fa-envelope-o"
                    }];
                  $scope.items.push({
                    class: 'item-primary-inverted-no-hover small-text text-center  text-center',
                    name: 'Operadores',
                    icon: false,
                    iconClass: "fa fa-envelope-o"
                  });
                  $scope.items.push({
                    class: 'small-text  text-center cursor-pointer',
                    name: 'Tiempo',
                    theme: 'operator',
                    method: 'time',
                    templatepopover: fullUrlBase + "flowchart/popovertime",
                    titlepopover: 'Operador por tiempo',
                    disabled: true,
                    icon: true,
                    iconClass: "fa fa-clock-o fa-2x",
                    image: fullUrlBase + "images/automatic/tiempoa-01.jpg"
                  });
                  $scope.items.push({
                    class: 'small-text  text-center cursor-pointer',
                    name: 'Accion',
                    theme: 'operator',
                    method: 'actions',
                    templatepopover: fullUrlBase + "flowchart/popoveraction",
                    titlepopover: 'Operador por accion',
                    disabled: true,
                    icon: true,
                    iconClass: "fa fa-cogs fa-2x",
                    image: fullUrlBase + "images/automatic/accion-01.jpg"
                  });
                  $scope.items.push({
                    class: 'small-text  text-center cursor-pointer',
                    name: 'Clicks',
                    theme: 'operator',
                    method: 'clicks',
                    templatepopover: fullUrlBase + "flowchart/popoverclick",
                    titlepopover: 'Operador por click',
                    disabled: true,
                    icon: true,
                    iconClass: "fa fa-link fa-2x",
                    image: fullUrlBase + "images/automatic/click-01.jpg"
                  });
                });
              }

              $scope.getIdMax = function () {
                var idMax = 0;
                for (var i = 0; i < $scope.chartViewModel.nodes.length; i++) {
                  var idNode = $scope.chartViewModel.nodes[i].getId();
                  if (idNode > idMax) {
                    idMax = idNode;
                  }
                }
                return idMax;
              }

              /*        $scope.disabledItem = function () {
               //          var themePre = $scope.chartViewModel.nodes[$scope.chartViewModel.nodes.length - 1].getTheme();
               //          if (themePre == "service") {
               //            for (var i = 0; i < $rootScope.items.length; i++) {
               //              if ($rootScope.items[i].theme == "service") {
               //                $rootScope.items[i].disabled = true;
               //                $rootScope.items[i].class += " disabled text-center cursor-pointer";
               //              } else if ($rootScope.items[i].theme == "operator") {
               //                $rootScope.items[i].disabled = false;
               //                $rootScope.items[i].class = "small-text text-center cursor-pointer";
               //              }
               //            }
               //          } else {
               //            for (var i = 0; i < $rootScope.items.length; i++) {
               //              if ($rootScope.items[i].theme == "operator") {
               //                $rootScope.items[i].disabled = true;
               //                $rootScope.items[i].class += " disabled text-center cursor-pointer";
               //              } else if ($rootScope.items[i].theme == "service") {
               //                $rootScope.items[i].disabled = false;
               //                $rootScope.items[i].class = "small-text text-center cursor-pointer";
               //              }
               //            }
               //          }
               //        }
               
               //        $scope.items = [{class: 'item-success-inverted-no-hover small-text', name: 'Servicio'},
               //          {class: 'small-text', name: 'Sms', theme: 'service', method: 'sms', templatepopover: fullUrlBase + "flowchart/popoversms", titlepopover: 'Configurar sms', disable: false},
               //          {class: 'small-text', name: 'Mail', theme: 'service', method: 'email', templatepopover: fullUrlBase + "flowchart/popovermail", titlepopover: 'Configurar correo', disable: false},
               //          {class: 'item-primary-inverted-no-hover small-text', name: 'Operadores'},
               //          {class: 'small-text disabled', name: 'Tiempo', theme: 'operator', method: 'time', templatepopover: fullUrlBase + "flowchart/popovertime", titlepopover: 'Operador por tiempo', disable: true},
               //          {class: 'small-text disabled', name: 'Accion', theme: 'operator', method: 'actions', templatepopover: fullUrlBase + "flowchart/popoveraction", titlepopover: 'Operador por accion', disable: true}];*/

              $scope.getallcategory = function () {
                restServices.getallcategory().then(function (data) {
                  $scope.listCategory = data;
                });
                restServices.getGmt().then(function (data) {
                  $scope.listZonaHoraria = data;
                });
              }
              $scope.getallcategory();

              $scope.addNewNode = function (item) {
                if (typeof item.method == "undefined") {
                  return;
                }
                var newNodeDataModel = {
                  name: item.name,
                  id: $scope.getIdMax() + 1,
                  x: 10,
                  y: 50,
                  width: 100,
                  theme: item.theme,
                  method: item.method,
                  image: item.image,
                  templatepopover: item.templatepopover,
                  titlepopover: item.titletemplate,
                  inputConnectors: [{
                      name: ""
                    }],
                  outputConnectors: [{
                      name: ""
                    }],
                  sendData: {}
                };
                if (item.method == "actions" || item.method == "clicks" || item.method == "links") {
                  newNodeDataModel.outputConnectors.push({
                    name: ""
                  });
                }
                $scope.chartViewModel.addNode(newNodeDataModel);
              };
              $scope.updateAutomaticCampaign = function (option) {
                restServices.validateJsonAutomaticCampaign($scope.chartViewModel.data, $scope.services).then(function (objData) {
                  $scope.optionCreate = option;
                  if (option == 1) {
                    restServices.updateAutomaticCampaignConfiguration($scope.chartViewModel.data, $scope.idautomaticcampaign).then(function (data) {
                      notificationService.primary(data.message);
                      $('#updateAutomaticCampaign').removeClass('dialog--open');
                    });
                  } else {
                    restServices.validateFormCampaign($scope.formCampaign).then(function (data) {
                      var objSend = {
                        formCampaign: data,
                        objCampaign: $scope.chartViewModel.data
                      };
                      restServices.updateAutomaticCampaignAll(objSend, $scope.idautomaticcampaign).then(function (data) {
                        notificationService.primary(data.message);
                        $state.go('index');
                      });
                    }).catch(function () {
                      $scope.opeModalUpdateCampaign();
                    });
                  }
                });
              }

              $scope.uptCampaign = function () {
                $scope.formCampaign.startDate = angular.copy($scope.dateGmtStart);
                $scope.formCampaign.endDate = angular.copy($scope.dateGmtEnd);
                restServices.validateFormCampaign($scope.formCampaign).then(function (data) {
                  restServices.updateAutomaticCampaign(data, $scope.idautomaticcampaign).then(function (data) {
                    //notificationService.primary(data.message);
                    $scope.updateAutomaticCampaign(1);
                    //$('#updateAutomaticCampaign').removeClass('dialog--open');
                    $state.go('index');
                  });
                });
              }

              $scope.initTextGmt = function () {
                $scope.showTextGmtStart = true;
                $scope.showTextGmtEnd = true;
                var StartUnix = moment($("#datestartCampaign").val()).utc().valueOf();
                $scope.dateGmtStart = moment(StartUnix).format('YYYY-MM-DD HH:mm');
                var EndUnix = moment($("#dateendCampaign").val()).utc().valueOf();
                $scope.dateGmtEnd = moment(EndUnix).format('YYYY-MM-DD HH:mm');
              }

              $scope.applyTextGmt = function () {
                if ($("#datestartCampaign").val() != '') {
                  $scope.showTextGmtStart = true;
                } else {
                  $scope.showTextGmtStart = false;
                  $scope.textGmtStart = '';
                }
                if ($("#dateendCampaign").val() != '') {
                  $scope.showTextGmtEnd = true;
                } else {
                  $scope.showTextGmtEnd = false;
                  $scope.textGmtEnd = '';
                }
                if ($scope.showTextGmtStart) {
                  var StartUnix = moment($("#datestartCampaign").val()).valueOf();
                  $scope.dateGmtStart = moment(StartUnix).utcOffset($scope.formCampaign.gmt).format('YYYY-MM-DD HH:mm');
                }
                if ($scope.showTextGmtEnd) {
                  var EndUnix = moment($("#dateendCampaign").val()).valueOf();
                  $scope.dateGmtEnd = moment(EndUnix).utcOffset($scope.formCampaign.gmt).format('YYYY-MM-DD HH:mm');
                }

              }

              $scope.opeModalUpdateCampaign = function () {
                $('#updateAutomaticCampaign').addClass('dialog--open');
              }

            }])
          .controller('listController', ['$scope', 'restServices', '$q', '$interval', function ($scope, restServices, $q, $interval) {
              $scope.initial = 0;
              $scope.page = 1;
              $scope.filter = {};
              $scope.listAutomaticCampaign = {};
              $scope.forward = function () {
                $scope.initial += 1;
                $scope.page += 1;
                $scope.listautocamp();
              };
              $scope.fastforward = function () {
                $scope.initial = ($scope.listAutomaticCampaign.total_pages - 1);
                $scope.page = $scope.listAutomaticCampaign.total_pages;
                $scope.listautocamp();
              };
              $scope.backward = function () {
                $scope.initial -= 1;
                $scope.page -= 1;
                $scope.listautocamp();
              };
              $scope.fastbackward = function () {
                $scope.initial = 0;
                $scope.page = 1;
                $scope.listautocamp();
              };
              $scope.listautocamp = function () {
                var data = {};
                $scope.listAutomaticCampaign.items = [];
                restServices.listcampaign($scope.initial, $scope.filter).then(function (data) {
                  $scope.listAutomaticCampaign = data;
                  for (var i = 0; i < $scope.listAutomaticCampaign.items.length; i++) {
                    var configuration = $scope.listAutomaticCampaign.items[i].configuration.configuration;
                    if (angular.isUndefined(configuration) || configuration == 'null') {
                      continue;
                    }
                    $scope.objConfiguration(configuration, $scope.listAutomaticCampaign.items[i].idAutomaticCampaign).then(function (data) {
                      $scope.listConfiguration = data;
                    });
                  }
                });
              };
              var statusVerify = function () {
                var data = {};
                restServices.listcampaign($scope.initial, data).then(function (data) {

                  data.items.forEach(function (item, index) {
                    if ($scope.listAutomaticCampaign.items[index].status != item.status) {
                      $scope.listAutomaticCampaign.items[index].status = item.status;
                    }
                  });
                  //$scope.mail = data;
                });
              };

              $scope.openModal = function (idAutomaticCampaign) {
                $scope.idAutCampaign = idAutomaticCampaign;
                $('#cancelDialog').addClass('dialog--open');
              };

              $scope.closeModal = function () {
                $('#cancelDialog').removeClass('dialog--open');
              };

              $scope.cancelCampaign = function () {
                restServices.cancelAutomaticCampaign($scope.idAutCampaign).then(function (res) {
                  slideOnTop(res.message, 3000, 'glyphicon glyphicon-remove-circle', 'info');
                  $scope.listautocamp();
                });
                $scope.closeModal();
              }

              $interval(statusVerify, 60000);
              $scope.countContacts = function (type, idAutomaticCampaign, lista) {
                var data = {
                  type: type,
                  segment: lista,
                  contactlist: lista
                };
                restServices.countContact(data).then(function (data) {
                  for (var j = 0; j < $scope.listAutomaticCampaign.items.length; j++) {
                    if (parseInt($scope.listAutomaticCampaign.items[j].idAutomaticCampaign) == parseInt(idAutomaticCampaign)) {
                      $scope.listAutomaticCampaign.items[j].quantitytarget = data;
                    }
                  }
                });
              };
              $scope.objConfiguration = function (Configuration, idAutomaticCampaign) {
                var defer = $q.defer();
                var objConfiguration = JSON.parse(Configuration);
                var arrReturn = [];
                for (var i = 0; i < objConfiguration.nodes.length; i++) {
                  var node = objConfiguration.nodes[i];
                  var str = "";
                  if (node.method == "primary") {
                    str = node.sendData.list.name + " : ";
                    var type = "";
                    for (var j = 0; j < node.sendData.selecteds.length; j++) {
                      if (node.sendData.list.id == 1) {
                        type = "contactlist";
                        $scope.countContacts(type, idAutomaticCampaign, node.sendData.selecteds);
                        str += node.sendData.selecteds[j].name + "(" + node.sendData.selecteds[j].idContactlist + "),";
                      } else {
                        type = "segment";
                        $scope.countContacts(type, idAutomaticCampaign, node.sendData.selecteds);
                        str += node.sendData.selecteds[j].name + "(" + node.sendData.selecteds[j].idSegment + "),";
                      }
                    }

                    str = str.substring(0, str.length - 1);
                    arrReturn.push({
                      step: i,
                      detail: str,
                      method: node.method,
                      methodAlias: "Destinatarios"
                    });
                  }
                  if (node.method == "sms") {
                    str = "La plantilla de sms : " + node.sendData.smstemplate.name + " con la categoria: " + node.sendData.smscategory.name;
                    arrReturn.push({
                      step: i,
                      detail: str,
                      method: node.method,
                      methodAlias: "Sms"
                    });
                  }
                  if (node.method == "email") {
                    str = "Plantilla de correo : " + node.sendData.mailtemplate.name + " con la categoria: " + node.sendData.mailcategory.name;
                    arrReturn.push({
                      step: i,
                      detail: str,
                      method: node.method,
                      methodAlias: "Correo"
                    });
                  }
                  if (node.method == "time") {
                    str = node.sendData.text;
                    arrReturn.push({
                      step: i,
                      detail: str,
                      method: node.method,
                      methodAlias: "Tiempo"
                    });
                  }
                  if (node.method == "actions") {
                    str = node.sendData.text;
                    arrReturn.push({
                      step: i,
                      detail: str,
                      method: node.method,
                      methodAlias: "Acción"
                    });
                  }
                }
                for (var j = 0; j < $scope.listAutomaticCampaign.items.length; j++) {
                  if (parseInt($scope.listAutomaticCampaign.items[j].idAutomaticCampaign) == parseInt(idAutomaticCampaign)) {
                    $scope.listAutomaticCampaign.items[j].listConfiguration = arrReturn;
                  }
                }
                defer.resolve(arrReturn);
                return defer.promise;
              }

              $scope.search = function () {
                $scope.listautocamp();
              }

              $scope.searchcategory = function () {
                $scope.listautocamp();
              };

              $scope.getCategory = function () {
                restServices.getallcategory().then(function (data) {
                  $scope.automaCategory = data;
                });
              }

              $scope.$watch('[filter.dateinitial,filter.dateend]', function () {
//              if((angular.isDefined($scope.filter.dateinitial) && $scope.filter.dateinitial != "") && (angular.isDefined($scope.filter.dateend) && $scope.filter.dateend != "")){
                $scope.listautocamp();
//              }
              });

              $scope.listautocamp();
              $scope.getCategory();
            }])
          .controller('viewschemeController', ['$scope', '$stateParams', 'restServices', '$q', '$interval', function ($scope, $stateParams, restServices, $q, $interval) {
              $scope.idautomaticcampaign = $stateParams.idautomaticcampaign;
              $scope.getautomaticcampaign = function () {
                restServices.getScheme($scope.idautomaticcampaign).then(function (data) {
                  $scope.campaign = data.data.campaign;
                  $scope.setFormCampaing(data.data.configuration);
                }).catch(function (data) {
                  if (data.data.configuration == null) {
                    $state.go('index');
                  }
                  $scope.campaign = data.data.campaign;
                  $scope.errorCampaign = true;
                  $scope.msgErrorCampaign = data.message;
                  $scope.setFormCampaing(data.data.configuration);
                });
              }

              $scope.getautomaticcampaign();

              $scope.setFormCampaing = function (configuration) {
                $scope.formCampaign = angular.copy($scope.campaign);
                $("#datestartCampaign").val($scope.campaign.startDate);
                $("#dateendCampaign").val($scope.campaign.endDate);
                $scope.chartViewModel = new flowchart.ChartViewModel(configuration);
                $scope.complet = true;
              }
            }]);
})();
