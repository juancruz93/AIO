angular.element(document).ready(function (e) {
  var dependecias = ['ngAnimate', 'ngSanitize', 'ui.bootstrap'];
  var app = angular.module("appMailTester", dependecias);

  app.service('appMailTesterServices', ['$http', '$q', 'appMailTesterConstant', function ($http, $q, constants) {
      this.getJsonMailTester = function () {
        return $http.get(constants.consUrlMailTester);
      }
    }]);

  app.factory('notificationService', function () {
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

  app.constant('appMailTesterConstant', {
    consMailTester: mailTester,
    consIdAllied: idAllied,
    consBaseUrlMailTester: "https://www.mail-tester.com/",
    consUrlMailTester: "https://www.mail-tester.com/" + mailTester + "&format=json",
  });

  app.controller('appMailTesterCtrl', ['$scope', 'appMailTesterConstant', 'appMailTesterServices', 'notificationService', function ($scope, constants, restService, notificationTop) {
      //Obj Global
      $scope.objGlobal = {
        openFirst: false,
        openSecond: false,
        openThird: false,
        openFourth: false,
        openFifth: false,
        openSixth: false,
        oneAtATime: true,
        MailTester: {},
        complete: false,
        porcenProgressBar: 0,
        initFun: function () {
          restService.getJsonMailTester()
                  .success(function (data) {
                    console.log(JSON.stringify(data));
                    $scope.objGlobal.MailTester = data;
                    $scope.objGlobal.complete = true;
                    $scope.objGlobal.setProgessBar(data.displayedMark);
                  })
                  .error(function (err) {
                    notificationTop.err("Ha ocurrido un error tratando de consultar el testeo del mail, por favor intentar mas tarde.");
                  });
        },
        setProgessBar: function(porcen){
          newPorcen = porcen.split("/");
          porcenProgressBar = parseFloat(newPorcen[0]);
          $scope.objGlobal.porcenProgressBar = porcenProgressBar*10;
          $scope.objGlobal.setStatusProgressBar($scope.objGlobal.porcenProgressBar);
          $scope.objGlobal.porcenProgressBar += "%"; 
        },
        setStatusProgressBar: function(porcen){
          if(porcen<=20){
            $scope.objGlobal.colorProgressBar = "#d9534f";
          }else if (porcen>20 && porcen<=40){
            $scope.objGlobal.colorProgressBar = "#e0e0eb";
          }else if (porcen>20 && porcen<=60){
            $scope.objGlobal.colorProgressBar = "#46b8da";
          }else if (porcen>60 && porcen<=80){
            $scope.objGlobal.colorProgressBar = "#eea236";
          }else if (porcen>80 && porcen<=100){
            $scope.objGlobal.colorProgressBar = "#4cae4c";
          }
        }
      }
    }]);

  angular.bootstrap(document, ['appMailTester']);
});