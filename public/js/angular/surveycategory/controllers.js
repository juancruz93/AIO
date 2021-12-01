(function () {
  angular.module("surveycategory.controllers", [])
    .controller("listController", ["$scope", "$state", "restServices", "notificationService", function ($scope, $state, restServices, notificationService) {
        $scope.initial = 0;
        $scope.page = 1;
        $scope.forward = function () {
          $scope.initial += 1;
          $scope.page += 1;
          $scope.listsurveycategory();
        };
        $scope.fastforward = function () {
          $scope.initial = ($scope.list.total_pages - 1);
          $scope.page = $scope.list.total_pages;
          $scope.listsurveycategory();
        };
        $scope.backward = function () {
          $scope.initial -= 1;
          $scope.page -= 1;
          $scope.listsurveycategory();
        };
        $scope.fastbackward = function () {
          $scope.initial = 0;
          $scope.page = 1;
          $scope.listsurveycategory();
        };

        $scope.listsurveycategory = function () {
          restServices.list($scope.initial, "").then(function (data) {
            $scope.list = data;
          }).catch(function (data) {
            notificationService.error(data.message);
          });
        };
        $scope.listsurveycategory();

        $scope.searchForName = function () {
          restServices.list($scope.initial, $scope.filterName).then(function (data) {
            $scope.list = data;
          }).catch(function (data) {
            notificationService.error(data.message);
          });
        };
        
        $scope.id = null;
        $scope.confirmDelete = function (id) {
          $scope.id = id;
          openModal();
        };
        
        $scope.delete = function () {
          restServices.delete($scope.id).then(function (data) {
            $scope.listsurveycategory();
            closeModal();
            notificationService.warning(data.message);
          }).catch(function (data) {
            notificationService.error(data.message);
          });
        };
      }])
    .controller("createController", ["$scope", "$state", "restServices", "notificationService", function ($scope, $state, restServices, notificationService) {
        $scope.initialize = function () {
          $scope.data = {};
          $scope.data.status = true;
        };

        $scope.initialize();

        $scope.create = function () {
          restServices.create($scope.data).then(function (data) {
            notificationService.success(data.message);
            $state.go("index");
          }).catch(function (data) {
            notificationService.error(data.message);
          });
        };
      }])
    .controller("editController", ["$scope", "$state", "$stateParams", "restServices", "notificationService", function ($scope, $state, $stateParams, restServices, notificationService) {
        restServices.getOne($stateParams.idSurveyCategory).then(function (data) {
          $scope.data = data;
          $scope.data.status = (data.status === 1 ? true : false);
        }).catch(function (data) {
          notificationService.error(data.message);
          $state.go("index");
        });

        $scope.edit = function () {
          restServices.edit($scope.data).then(function (data) {
            notificationService.info(data.message);
            $state.go("index");
          }).catch(function (data) {
            notificationService.error(data);
          });
        };
      }]);
})();
