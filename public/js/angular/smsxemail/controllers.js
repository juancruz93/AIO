angular.module('smsxemail.controllers', ['ngMaterial'])
        .controller('createController', ['$scope', 'RestServices', '$state', 'notificationService', '$stateParams', function ($scope, RestServices, $state, notificationService, $stateParams) {
          //Set data
          $scope.data = {};
          //Set misc
          $scope.misc = {};
          $scope.misc.showSave = true;
          $scope.misc.isDisabled = false;
          $scope.misc.generate = true;
          $scope.misc.copy = false;
          $scope.linksurv = '';
          //Set functions universal
          $scope.functions = {
            setData: function(data){
              if(data.idSmsxEmail != null){
                angular.forEach(data,function(value,key){
                  $scope.data[key] = value;
                });
                $scope.misc.isDisabled = true;
                $scope.misc.showSave = false;
                $scope.misc.generate = false;
                $scope.misc.copy = true;
              }
            },
            validate:function(){
              $scope.functionsApi.create();
            },
            generateKey:function(){
              RestServices.generateKey().then(function (data){
                $scope.data.generateKey = data.data;
              });
            },
            copyKey:function (idSmsxEmail) {
              RestServices.copyKey(idSmsxEmail).then(function (data) {
                $scope.linksurv = data.data.copy;
                var buttonCopy = document.getElementById("buttonCopy");
                var link = document.getElementById("link");
                buttonCopy.addEventListener('click', function (e) {
                  link.select();
                  if (document.execCommand("copy")) {
                    notificationService.success("la Clave ha sido copiado exitosamente");
                  } else {
                    notificationService.error("No se pudo copiar la Clave");
                  }
                  angular.element(document.querySelector(".linkgen")).modal('hide');
                });
                angular.element(document.querySelector(".linkgen")).modal('show');
              }).catch(function (data) {
                notificationService.error(data.message);
              });
            }
          };
          $scope.smsCategory = function () {
            RestServices.smsCategory().then(function (data) {
              $scope.misc.smsCategory = data;
            });
          };
          $scope.smsCategory();

          //set functions api
          $scope.functionsApi = {
            create: function (){ 
              RestServices.createSmsxemail($scope.data).then(function (data) {
                notificationService.success(data.data.message);
                $scope.misc.isDisabled = true;
                $scope.misc.showSave = false;
                $scope.misc.generate = false;
                $scope.misc.copy = true;
              }).catch(function (error) {
                notificationService.error(error.data.message);
              });
            },
            getOne: function () {
              RestServices.oneSmsxemail().then(function (data) {
                $scope.functions.setData(data.data);                
              }).catch(function (error) {
                notificationService.error(error.data.message);
              });
            }
          };
          $scope.functionsApi.getOne();
        }]);
        


