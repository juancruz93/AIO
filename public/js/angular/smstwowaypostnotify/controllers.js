angular.module('smstwowaypostnotify.controllers', ['ngMaterial'])
  .controller('createController', ['$scope', 'RestServices', '$state', 'notificationService', '$stateParams', 'constantPageSmsxemail', function ($scope, RestServices, $state, notificationService, $stateParams, constantPageSmsxemail) {
      //Set data
      $scope.data = {
        smstwowaydata: {}
      };
      $scope.styleLink = {};
      //Set misc
      $scope.misc = {};
      $scope.misc.showSave = true;
      $scope.misc.isDisabled = false;
      $scope.misc.generate = true;
      $scope.misc.copy = false;
      $scope.misc.url = "";
      $scope.linksurv = '';
      $scope.data.smstwowaydata.edit = false;
      //Set functions universal
      $scope.functions = {
        setData: function (data) {
          if (data.idSubaccount != null) {
            angular.forEach(data, function (value, key) {
              $scope.data.smstwowaydata.url = data.url;
              $scope.data.smstwowaydata.password = data.password;
              //$scope.data[key] = value;
            });
            $scope.misc.isDisabled = true;
            $scope.misc.showSave = false;
            $scope.misc.generate = false;
            $scope.misc.copy = true;
          }
        },
        validate: function () {
          $scope.functionsApi.create();
        },
        generateKey: function () {
          RestServices.generateKey().then(function (data) {
            //$scope.data.generateKey = data.data;
            $scope.data.smstwowaydata.password = data.data;

          });
        },
        copyKey: function (idSmsxEmail) {
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

      //set functions api
      $scope.functionsApi = {
        create: function () {
          if (angular.isUndefined($scope.data.smstwowaydata.password) || $scope.data.smstwowaydata.password == "") {
            notificationService.warning("Por favor Genere clave de autenticacion");
            return;
          }
          $scope.misc.isDisabled = false;
          $scope.misc.isDisabledButton = true;
          $scope.misc.generate = false;
          RestServices.createSmstwowayPost($scope.data).then(function (data) {
            if (data.data.res == 1) {
              if($scope.data.smstwowaydata.edit == true){
                 notificationService.success(constantPageSmsxemail.Messages.confirmationEdit);
              }else{
                notificationService.success(constantPageSmsxemail.Messages.confirmation);
              }
              $scope.misc.isDisabled = true;
              $scope.misc.isDisabledButton = true;
              $scope.misc.showSave = true;
              $scope.misc.generate = true;
              $scope.misc.copy = true;
              $scope.styleLink = {
                "pointer-events": "none"
              };
            } else {
              notificationService.success(constantPageSmsxemail.Messages.error);
            }
          }).catch(function (error) {
            notificationService.error(error.data.message);
          });
        },
        edit: function(){
          $scope.data.smstwowaydata.edit = true;
          $scope.misc.isDisabled = false;
          $scope.misc.isDisabledButton = false;
          $scope.misc.showSave = false;
          $scope.misc.generate = false;
          $scope.misc.copy = false;
          $scope.styleLink = {};
        },
        getSavedCredentials: function () {
          RestServices.getSavedCredentials().then(function (data) {
            $scope.data.smstwowaydata.res = data.data.res;
             $scope.misc.isDisabledButton = false;
            if (data.data.res != 0) {
              $scope.data.smstwowaydata.url = data.data.url;
              $scope.data.smstwowaydata.password = data.data.password;
              $scope.misc.isDisabled = true;
              $scope.misc.showSave = false;
              $scope.misc.generate = false;
              $scope.misc.isDisabledButton = true;
              $scope.misc.copy = true;
              $scope.styleLink = {
                "pointer-events": "none"
              }
              //$scope.functions.setData(data.data);
            }
          }).catch(function (error) {
            notificationService.error(error.data.message);
          });
        }
      };
      $scope.functionsApi.getSavedCredentials();
    }]);



