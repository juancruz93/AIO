(function () {
  angular.module("currency.controller", [])
    .controller("listController", ["$scope", "RestServices", "notificationService", function ($scope, RestServices, notificationService) {
        $scope.initial = 0;
        $scope.page = 1;

        $scope.forward = function () {
          $scope.initial += 1;
          $scope.page += 1;
          $scope.listcurrency();
        };
        $scope.fastforward = function () {
          $scope.initial = ($scope.list.total_pages - 1);
          $scope.page = $scope.list.total_pages;
          $scope.listcurrency();
        };
        $scope.backward = function () {
          $scope.initial -= 1;
          $scope.page -= 1;
          $scope.listcurrency();
        };
        $scope.fastbackward = function () {
          $scope.initial = 0;
          $scope.page = 1;
          $scope.listcurrency();
        };

        $scope.listcurrency = function () {
          RestServices.listCurrency($scope.initial, "").then(function (data) {
            $scope.list = data;
          });
        };

        $scope.listcurrency();

        $scope.searchForName = function () {
          RestServices.listCurrency($scope.initial, $scope.filterName).then(function (data) {
            $scope.list = data;
          });
        };

        $scope.openMod = function (id) {
          $scope.idCurrency = id;
          openModal();
        };

        $scope.deleteCurrency = function () {
          RestServices.deleteCurrency($scope.idCurrency).then(function (data) {
            notificationService.warning(data.message);
            $scope.listcurrency();
            closeModal();
          });
        };

      }])
    .controller("createController", ["$scope", "RestServices", "notificationService", "$state", function ($scope, RestServices, notificationService, $state) {
        $scope.data = {};
        $scope.data.status = true;
        $scope.saveCurrency = function () {
          RestServices.saveCurrency($scope.data).then(function (data) {
            notificationService.success(data.message);
            $state.go("index");
          });
        };
      }])
    .controller("editController", ["$scope", "$stateParams", "RestServices", "notificationService", "$state", function ($scope, $stateParams, RestServices, notificationService, $state) {
        $scope.getcurrency = function () {
          RestServices.getCurrency($stateParams.idCurrency).then(function (data) {
            $scope.data = data;
            $scope.data.status = (data.status == 1);
          });
        };
        $scope.getcurrency();

        $scope.updateCurrency = function () {
          RestServices.updateCurrency($scope.data).then(function (data) {
            notificationService.info(data.message);
            $state.go("index");
          });
        };
      }]);
})();