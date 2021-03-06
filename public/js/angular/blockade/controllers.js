'use strict';
(function () {
  angular.module('blockade.controllers', [])
    .controller('BlockadeController', ['$scope', 'restService', 'notificationService', '$timeout', function ($scope, restService, notificationService, $timeoutl) {

      $scope.deleteBlocked = function (idBlocked) {
        restService.deleteBlockade(idBlocked).then(function (data) {
          notificationService.success(data.message);
          $scope.getAll();
        });
      }

      $scope.initial = 0;
      $scope.page = 1;
      $scope.stringsearch = -1;
      $scope.shownewblockade = true;
      $scope.showblockade = true;
      $scope.getAll = function () {
        $scope.shownewblockade = true;
        $scope.showblockade = true;
        restService.getAll($scope.initial, $scope.stringsearch).then(function (data) {
          if (data.total > 0) {
            $scope.showblockade = false;
          } else {
            $scope.shownewblockade = false;
          }
          $scope.blockade = data;
        });
      };
      $scope.getAll();
      $scope.searchBlocked = function () {
        $scope.stringsearch = $scope.search;
        $scope.getAll();
      };


      $scope.forward = function () {
        $scope.progressbar = false;
        $scope.initial += 1;
        $scope.page += 1;
        $scope.getAll();
      };
      $scope.fastforward = function () {
        $scope.progressbar = false;
        $scope.initial = ($scope.blockade.total_pages - 1);
        $scope.page = $scope.blockade.total_pages;
        $scope.getAll();
      };
      $scope.backward = function () {
        $scope.progressbar = false;
        $scope.initial -= 1;
        $scope.page -= 1;
        $scope.getAll();
      };
      $scope.fastbackward = function () {
        $scope.progressbar = false;
        $scope.initial = 0;
        $scope.page = 1;
        $scope.getAll();
      };

    }])
    .controller('NewBlockadeController', ['$scope', 'restService', 'notificationService', '$timeout', '$window', function ($scope, restService, notificationService, $timeoutl, $window) {

      $scope.initComponents = function () {
        restService.listindicative().then(function (response) {
          $scope.listindicative = response;
        }).catch(function (error) {
          notificationService.error(error.message);
        });
      };

      $scope.addBlockade = function () {
        restService.addBlockade($scope.block).then(function (data) {
          $window.location.href = '#/';
          notificationService.success(data.message);
        });
      };

    }]);

})();
