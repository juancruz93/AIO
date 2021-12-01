(function () {
  angular.module("activitylog.controllers", [])
    .controller("listController", ['$scope', "$q", "notificationService", "restServices", "$state", function ($scope, $q, notificationService, restService, $state) {
        $scope.initial = 0;
        $scope.page = 1;
        $scope.forward = function () {
          $scope.initial += 1;
          $scope.page += 1;
          $scope.listmailtemplate();
        };
        $scope.fastforward = function () {
          $scope.initial = ($scope.list.total_pages - 1);
          $scope.page = $scope.list.total_pages;
          $scope.listmailtemplate();
        };
        $scope.backward = function () {
          $scope.initial -= 1;
          $scope.page -= 1;
          $scope.listmailtemplate();
        };
        $scope.fastbackward = function () {
          $scope.initial = 0;
          $scope.page = 1;
          $scope.listmailtemplate();
        };

        $scope.listactivitylog = function () {
          restService.list($scope.initial, {}).then(function (data) {
            $scope.list = data;
          });
        };

        $scope.listactivitylog();
        $scope.data = {};
        restService.services().then(function (data) {
          $scope.listservices = data;
        });

        $scope.endDate = true;

        $scope.enableDate2 = function () {
          $scope.endDate = false;
          $scope.data.endDate = "";
          if (angular.isUndefined($scope.data.startDate) || $scope.data.startDate === "") {
            restService.list($scope.initial, $scope.data).then(function (data) {
              $scope.list = data;
            });
          }
        };

        $scope.searchForDate = function () {
          if (angular.isUndefined($scope.data.startDate) || $scope.data.startDate === "") {
            notificationService.error("Debe seleccionar la fecha de inicio");
            return false;
          }
          restService.list($scope.initial, $scope.data).then(function (data) {
            $scope.list = data;
          });
        };

        $scope.searchForService = function () {
          restService.list($scope.initial, $scope.data).then(function (data) {
            $scope.list = data;
          });
        };

        $scope.searchForName = function () {
          restService.list($scope.initial, $scope.data).then(function (data) {
            $scope.list = data;
          });
        };
        
        $scope.refresh = function () {
          $state.reload();
        };

      }]);
})();