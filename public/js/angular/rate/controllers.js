angular.module('rate.controllers', ['ngMaterial'])
        .filter("propsFilter", function() {
          return function(items, props) {
            var out = [];
            if (angular.isArray(items)) {
              var keys = Object.keys(props);
              items.forEach(function(item) {
                var itemMatches = false;
                for (var i = 0; i < keys.length; i++) {
                  var prop = keys[i];
                  var text = props[prop].toLowerCase();
                  if (
                    item[prop]
                      .toString()
                      .toLowerCase()
                      .indexOf(text) !== -1
                  ) {
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
        .controller('indexController', ['$scope', 'RestServices', '$state', 'notificationService', 'constantPageRate', '$window', function ($scope, RestServices, $state, notificationService, constantPageRate, $window) {
          //set data
          $scope.data = {};
          $scope.data.initial = 0;
          $scope.data.page = 1;

          $scope.data.filter = {};
          //Set misc
          $scope.misc = {};
          //Set functions universal
          $scope.functions = {
            setList: function (data) {
              for(var i=0; i<data.items.length; i++){
                if(data.items[i].accountingMode == "contact"){
                  data.items[i].accountingMode = "Por Contacto";
                } else if(data.items[i].accountingMode == "sending"){
                  data.items[i].accountingMode = "Por Envio";
                } else if(data.items[i].accountingMode == "unlimited"){
                  data.items[i].accountingMode = "Ilimitado";
                } else if(data.items[i].accountingMode == "answer"){
                  data.items[i].accountingMode = "Respuesta";
                } else if(data.items[i].accountingMode == "visualizations"){
                  data.items[i].accountingMode = "Visualizaciones";
                } else if(data.items[i].accountingMode == "sendingsms"){
                  data.items[i].accountingMode = "Por Envio";
                } else if(data.items[i].accountingMode == "sendingpdf"){
                  data.items[i].accountingMode = "Por Envio";
                } else if(data.items[i].accountingMode == "respuestaform"){
                  data.items[i].accountingMode = "Respuesta";
                }
                if(data.items[i].planType == "prepaid"){
                  data.items[i].planType = "Prepago";
                } else if(data.items[i].planType == "postpaid"){
                  data.items[i].planType = "Postpago";
                }
              }
              $scope.misc.list = data;
            },
            setMethodMisc: function (item, value) {
              $scope.misc[item] = value;
            },
            redirect: function (url) {
              var route = $window.myBaseURL + url;
              $window.location.href = route;
            },
            confirmDelete: function (idRate) {
              $scope.idRatedeleted = idRate;
              openModal();
            },
            setMethodData: function (item, data){
              $scope.data[item] = data;
            },
            searchname: function () {
              $scope.restServices.getAll();
            },
            Pagination: {
              forward: function () {
                $scope.data.initial += 1;
                $scope.data.page += 1;
                $scope.restServices.getAll();
              },
              fastforward: function () {
                $scope.data.initial = ($scope.misc.list.total_pages - 1);
                $scope.data.page = $scope.misc.list.total_pages;
                $scope.restServices.getAll();
              },
              backward: function () {
                $scope.data.initial -= 1;
                $scope.data.page -= 1;
                $scope.restServices.getAll();
              },
              fastbackward: function () {
                $scope.data.initial = 0;
                $scope.data.page = 1;
                $scope.restServices.getAll();
              },
              filterDate: function() {
                $scope.data.initial = 0;
                $scope.data.page = 1;
                $scope.restServices.getAll();
              },
              startDateOnSetTime: function() {
                $scope.$broadcast("start-date-changed");
              },
              endDateOnSetTime: function() {
                $scope.$broadcast("end-date-changed");
              },
              startDateBeforeRender: function($dates) {
                if ($scope.data.filter.dateEnd) {
                  var activeDate = moment($scope.data.filter.dateEnd);

                  $dates
                    .filter(function(date) {
                      return date.localDateValue() >= activeDate.valueOf();
                    })
                    .forEach(function(date) {
                      date.selectable = false;
                    });
                }
              },
              endDateBeforeRender: function($view, $dates) {
                if ($scope.data.filter.dateStart) {
                  var activeDate = moment($scope.data.filter.dateStart)
                    .subtract(1, $view)
                    .add(1, "minute");

                  $dates
                    .filter(function(date) {
                      return date.localDateValue() <= activeDate.valueOf();
                    })
                    .forEach(function(date) {
                      date.selectable = false;
                    });
                }
              }
            }
          };
          //set functions api
          $scope.restServices = {
            getAll: function () {
              RestServices.getAllRate($scope.data.initial, $scope.data)
                .then(function (resolve) {
                  $scope.functions.setList(resolve.data);
                })
                .catch(function (error) {
                  notificationService.error(error.data.message);
                });
            },            
            deletedRate:function () {
              RestServices.deletedRate($scope.idRatedeleted)
                .then(function (resolve) {
                  closeModal();
                  notificationService.warning(resolve.data.message);
                  $scope.restServices.getAll();
                })
                .catch(function (error) {
                  notificationService.error(error.data.message);
                });
            },
            reportMail:function(){
              var idMail = {idMail:1438};
              RestServices.getReportMail(idMail).then(function (data){
                console.log(data);
              });
            }
          };
          $scope.restServices.getAll();
        }])
        .controller('createController', ['$scope', 'RestServices', '$state', 'notificationService', '$stateParams', function ($scope, RestServices, $state, notificationService, $stateParams) {
          //Set data
          $scope.data = {};
          $scope.data.status = true;
          $scope.data.online = false;
          //Set range
          $scope.data.ranges = [];
          $scope.data.country = [];
          $scope.data.accountingMode = null;
          $scope.data.planType = null;
          $scope.data.dateInitial = null;
          $scope.data.dateEnd = null;
          //Set misc
          $scope.misc = {};
          $scope.misc.planTypes = [
            {key: "prepaid", name:"Prepago"},
            {key: "postpaid", name:"Postpago"}
          ];
          $scope.misc.viewRange = true;
          $scope.misc.viewEdit = false;
          $scope.misc.viewCreate = true;
          $scope.misc.viewMessage1 = false;
          $scope.misc.viewMessage2 = false;
          $scope.misc.viewMode = false;
          $scope.misc.viewSpace = false;
          $scope.misc.viewOnline = false;
          $scope.misc.Services = null;
          $scope.misc.postCount = null;
          
          $.fn.datetimepicker.defaults = {
            maskInput: false,
              pickDate: true,
              pickTime: true,
              startDate: new Date()
          };
          $('#datetimepicker1,#datetimepicker2').datetimepicker({
            format: 'yyyy-MM-dd hh:mm:ss',
            language: 'es',
            
          });
          //Set functions universal
          $scope.functions = {
            setData: function(data){
              angular.forEach(data,function(value,key){
                $scope.data[key] = value;
              });
            },
            validate:function(){
              if(typeof $scope.data.idRate != "undefined"){
                $scope.functionsApi.editRate();
              }else{
                $scope.functionsApi.createRate();
              }
            },
            addRange:function(){
              $scope.misc.postCount = $scope.data.ranges.length;
              if($scope.data.ranges.length == 0){
                $scope.data.ranges.push({});
                $scope.data.ranges[0].since = 0;
              } else {
                $scope.data.ranges.push({});
                if($scope.data.idServices == 5 && $scope.data.accountingMode == 'unlimited'){
                  $scope.data.ranges[$scope.misc.postCount-1].since = null;
                  $scope.data.ranges[$scope.misc.postCount-1].until = null;
                  if(!$scope.data.ranges[$scope.misc.postCount-1].space){
                    notificationService.error("Debe ingresar la capacidad del rango.");
                    $scope.data.ranges.splice(-1,1);
                    $scope.misc.viewMessage1 = true;
                    return false;
                  } else if(!$scope.data.ranges[$scope.misc.postCount-1].value){
                    notificationService.error("Debe ingresar el valor del rango.");
                    $scope.data.ranges.splice(-1,1);
                    $scope.misc.viewMessage1 = true;
                    return false;
                  }else if($scope.data.ranges.length>=1){
                    notificationService.error("Solo puede agregar un rango en Encuesta ilimitada.");
                    $scope.data.ranges.splice(1,1);
                    return false;
                  }
                } else {
                  if(!$scope.data.ranges[$scope.misc.postCount-1].until){
                    notificationService.error("Debe ingresar el final del rango");
                    $scope.data.ranges.splice(-1,1);
                    $scope.misc.viewMessage1 = true;
                    return false;
                  }
                }
                if(!$scope.data.ranges[$scope.misc.postCount-1].value){
                  notificationService.error("Debe ingresar el valor del rango");
                  $scope.data.ranges.splice(-1,1);
                  $scope.misc.viewMessage1 = true;
                  return false;
                }
                if($scope.data.ranges[$scope.misc.postCount-1].since >= 0){
                  $scope.until = $scope.data.ranges[$scope.misc.postCount-1].until;
                  $scope.space = $scope.data.ranges[$scope.misc.postCount-1].space;
                  $scope.value = $scope.data.ranges[$scope.misc.postCount-1].value;
                  if(parseInt($scope.data.ranges[$scope.misc.postCount-1].since)>=parseInt($scope.until)){
                    notificationService.error("El final del rango no debe ser menor al inicio del rango.");
                    $scope.data.ranges.splice(-1,1);
                    return false;
                  } else {
                    $scope.data.ranges[$scope.misc.postCount].since = parseInt($scope.until) + 1;
                  }
                } else {
                  $scope.data.ranges.splice(-1,1);
                }
              }
            },
            removeRange:function(){
              $scope.data.ranges.splice(-1,1);
            },
            changeService: function () {
              if($scope.data.idRate == "undefined"){
                if($scope.data.idServices != $scope.misc.Services){
                  notificationService.error("Se limpiaran los ultimos campos de los rangos");
                  $scope.data.ranges.splice(1,$scope.data.ranges.length);
                }
              }
              for (var i = 0; i < $scope.misc.services.length; i++) {
                if ($scope.misc.services[i].idServices === $scope.data.idServices) {
                  if ($scope.misc.services[i].name.toLowerCase() === "sms"){
                    $scope.misc.accountingModes = [
                      {key: "sendingsms", name: "Por envío"}
                    ];
                    $scope.misc.viewMode = true;
                    $scope.misc.viewSpace = false;
                    $scope.misc.viewRange = true;
                    $scope.data.accountingMode = null;
                  } else if ($scope.misc.services[i].name.toLowerCase() === "email marketing"){
                    $scope.misc.accountingModes = [
                      {key: "contact", name: "Por contacto"},
                      {key: "sending", name: "Por envío"}
                    ];
                    $scope.data.accountingMode = null;
                    $scope.misc.viewSpace = false;
                    $scope.misc.viewMode = true;
                    $scope.misc.viewRange = true;
                  } else if ($scope.misc.services[i].name.toLowerCase() === "mail tester"){
                    $scope.misc.viewMode = false;
                    $scope.misc.viewSpace = false;
                    $scope.misc.viewRange = true;
                    $scope.data.accountingMode = null;
                  } else if ($scope.misc.services[i].name.toLowerCase() === "automatic campaing"){
                    $scope.misc.viewMode = false;
                    $scope.misc.viewSpace = false;
                    $scope.misc.viewRange = true;
                    $scope.data.accountingMode = null;
                  } else if ($scope.misc.services[i].name.toLowerCase() === "survey"){
                    $scope.misc.viewMode = true;
                    $scope.misc.accountingModes = [
                      {key: "unlimited", name: "Ilimitado"},
                      {key: "answer", name: "Respuesta"}
                    ];
                    $scope.misc.viewSpace = true;
                  } else if ($scope.misc.services[i].name.toLowerCase() === "adjuntar archivos"){
                    $scope.misc.accountingModes = [
                      {key: "sendingpdf", name: "Por envío"}
                    ];
                    $scope.misc.viewMode = true;
                    $scope.misc.viewSpace = true;
                    $scope.misc.viewRange = true;
                    $scope.data.accountingMode = null;
                  } else if ($scope.misc.services[i].name.toLowerCase() === "sms doble-via"){   
                    $scope.misc.accountingModes = [
                      {key: "sendingsms", name: "Por envío"}
                    ];
                    $scope.misc.viewMode = true;
                    $scope.misc.viewSpace = false;
                    $scope.misc.viewRange = true;
                    $scope.data.accountingMode = null;
                  } else if ($scope.misc.services[i].name.toLowerCase() === "landing page"){
                    $scope.misc.accountingModes = [
                      {key: "visualizations", name: "Visualizaciones"}
                    ];
                    $scope.misc.viewMode = true;
                    $scope.misc.viewRange = true;
                    $scope.misc.viewSpace = false;
                    $scope.data.accountingMode = null;
                  } else if ($scope.misc.services[i].name.toLowerCase() === "form"){
                    $scope.misc.accountingModes = [
                      {key: "respuestaform", name: "Respuesta"}
                    ];
                    $scope.misc.viewMode = true;
                    $scope.misc.viewRange = true;
                    $scope.misc.viewSpace = false;
                    $scope.data.accountingMode = null;
                  } else {
                    $scope.misc.viewMode = false;
                    $scope.misc.viewSpace = false;
                    $scope.misc.viewRange = false;
                  }
                }
              }
            },
            changeMode: function () {
              if($scope.data.accountingMode == "unlimited"){
                $scope.misc.viewRange = false;
                $scope.misc.viewSpace = true;
              } else if($scope.data.accountingMode == "answer"){
                $scope.misc.viewRange = true;
                $scope.misc.viewSpace = true;
              }
            },
            changePlan: function () {
              if($scope.data.planType == "prepaid"){
                $scope.misc.viewOnline = true;
              } else if($scope.data.planType == "postpaid"){
                $scope.misc.viewOnline = false;
                $scope.data.online = false;
              }
            },
            openModal1: function () {
              openModal();
              $scope.misc.viewMessage2 = false;
              $scope.misc.viewMessage1 = true;
            },
            openModal2: function () {
              openModal();
              $scope.misc.viewMessage2 = true;
            },
            isDisabled: function () {
              closeModal();
              $scope.isDisabled = false;
              $scope.misc.viewUpdate = false;
            },
            getaccountMode: function (data) {
              $scope.misc.viewEdit = true;
              $scope.misc.viewCreate = false;
//              $scope.misc.viewCreate = false;
              $scope.misc.viewUpdate = true;
              $scope.isDisabled = true;
              if(data.accountingMode != null){
                $scope.misc.viewMode = true;
                if(data.accountingMode == 'contact' || data.accountingMode == 'sending'){
                  $scope.misc.accountingModes = [
                    {key: "contact", name: "Por contacto"},
                    {key: "sending", name: "Por envío"}
                  ];
                  $scope.misc.viewRange = true;
                  $scope.misc.viewSpace = false;
                } else if(data.accountingMode == 'unlimited' || data.accountingMode == 'answer'){
                  $scope.misc.accountingModes = [
                    {key: "unlimited", name: "Ilimitado"},
                    {key: "answer", name: "Respuesta"}
                  ];
                  if(data.accountingMode == 'unlimited'){
                    $scope.misc.viewRange = false;
                    $scope.misc.viewSpace = true;
                  } else {
                    $scope.misc.viewRange = true;
                    $scope.misc.viewSpace = true;
                  }
                } else if(data.accountingMode == 'sendingsms'){
                  $scope.misc.accountingModes = [
                    {key: "sendingsms", name: "Por envío"}
                  ];
                  $scope.misc.viewRange = true;
                  $scope.misc.viewSpace = false;
                } else if(data.accountingMode == 'sendingpdf'){
                  $scope.misc.accountingModes = [
                    {key: "sendingpdf", name: "Por envío"}
                  ];
                  $scope.misc.viewRange = true;
                  $scope.misc.viewSpace = true;
                } else if(data.accountingMode == 'visualizations'){
                  $scope.misc.accountingModes = [
                    {key: "visualizations", name: "Visualizaciones"}
                  ];
                  $scope.misc.viewRange = true;
                  $scope.misc.viewSpace = false;
                  
                } else {
                  $scope.misc.viewRange = true;
                  $scope.misc.viewSpace = false;
                }
              }
              for(var i=0; i<data.ranges.length; i++){
                if(data.ranges[i].visible == "0"){
                  data.ranges[i].visible = false;
                } else if(data.ranges[i].visible == "1"){
                  data.ranges[i].visible = true;
                }
              }
              if(data.planType == 'prepaid'){
                $scope.misc.viewOnline = true;
              } else {
                $scope.misc.viewOnline = false;
              }
            },
            validateRate: function (){
              $scope.data.dateInitial = document.getElementById("dateInitial").value;
              $scope.data.dateEnd = document.getElementById("dateEnd").value;
              if(!$scope.data.country){
                notificationService.error("El campo Paises es obligatorio");
                return false;
              } else if(!$scope.data.idServices){
                notificationService.error("El campo Servicios es obligatorio");
                return false;
              } else if(!$scope.data.planType){
                notificationService.error("El campo Plan de pagos es obligatorio");
                return false;
              } 
              if(!$scope.data.ranges[0]){
                return false;
              }
              if ($scope.misc.viewMode) {
                if ($scope.data.accountingMode == null) {
                    return false;
                } else {
                  $scope.data.accountingMode = $scope.data.accountingMode;
                }
              } else {
                  $scope.data.accountingMode = null;
              }
              $scope.count = $scope.data.ranges.length - 1;
              if($scope.data.idServices == 5 && $scope.data.accountingMode == 'unlimited'){
                if(!$scope.data.ranges[$scope.count].space){
                  notificationService.error("Debe ingresar la capacidad del rango");                  
                  $scope.misc.viewMessage1 = true;
                  return false;
                } else if(!$scope.data.ranges[$scope.count].value){
                  notificationService.error("Debe ingresar el valor del rango");
                  $scope.misc.viewMessage1 = true;
                  return false;
                } else if($scope.count>=1){
                  console.log("Entra");
                  $scope.data.ranges.splice(-1,1);
                }
              } else {
                if($scope.data.ranges[$scope.count].since == null && $scope.data.ranges[$scope.count].until  == null){
                  $scope.data.ranges.splice(-1,1);  
                  return false;
                } else if ($scope.data.ranges[$scope.count].since>=$scope.data.ranges[$scope.count].until){
                  $scope.data.ranges.splice(-1,1);
                  return false;
                } else if(parseInt($scope.data.ranges[$scope.count].since)>=parseInt($scope.data.ranges[$scope.count].until)){
                  notificationService.error("El final del rango no debe ser menor al inicio del rango.");
                  $scope.data.ranges.splice(-1,1);
                  return false;
                } 
              }
            }
          };
          $scope.country = function () {
            RestServices.country().then(function (data) {
              $scope.misc.country = data;
            });
          };
          $scope.country();
          
          $scope.services = function () {
            RestServices.services().then(function (data) {
              $scope.misc.services = data;
            });
          };
          $scope.services();
          
          //set functions api
          $scope.functionsApi = {
            createRate: function () {
              $scope.functions.validateRate();
              RestServices.createRate($scope.data).then(function (data) {
                $state.go("list");
                notificationService.success(data.data.message);
              }).catch(function (error) {
                notificationService.error(error.data.message);
              });
            },
            getOne: function () {
              RestServices.oneRate($scope.data.idRate).then(function (data) {
                $scope.misc.Services = data.data.idServices;
                $scope.functions.getaccountMode(data.data);
                $scope.functions.setData(data.data);                
              }).catch(function (error) {
                notificationService.error(error.data.message);
              });
            },
            editRate:function (){
              $scope.functions.validateRate();
              RestServices.editRatexrange($scope.data).then(function (data) {
                $state.go("list");
                notificationService.success(data.data.message);
              }).catch(function (error) {
                notificationService.error(error.data.message);
              });
            }
          };
          if (typeof $stateParams.idRate != "undefined" && $stateParams.idRate != "") {
            $scope.data.idRate = $stateParams.idRate;
            $scope.functionsApi.getOne();
          }
        }]);


