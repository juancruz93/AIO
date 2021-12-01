(function () {
  angular.module('automaticcampaigncategory.controller', [])
    .controller('listController', ['$scope', 'RestServices', 'notificationService', function ($scope, RestServices, notificationService) {
        $scope.initial = 0;
        $scope.page = 1;

        $scope.forward = function () {
          $scope.initial += 1;
          $scope.page += 1;
          $scope.listautocampcateg();
        };
        $scope.fastforward = function () {
          $scope.initial = ($scope.list.total_pages - 1);
          $scope.page = $scope.list.total_pages;
          $scope.listautocampcateg();
        };
        $scope.backward = function () {
          $scope.initial -= 1;
          $scope.page -= 1;
          $scope.listautocampcateg();
        };
        $scope.fastbackward = function () {
          $scope.initial = 0;
          $scope.page = 1;
          $scope.listautocampcateg();
        };

        $scope.listautocampcateg = function () {
          RestServices.list($scope.initial, $scope.filter).then(function (data) {
            $scope.list = data;
          });
        };

        $scope.listautocampcateg();
//-----------------------------------------------
//Filtros
        $scope.filtername = function () {
          RestServices.list($scope.initial, $scope.filter).then(function (data) {
            $scope.list = data;
          });
        };
//-----------------------------------------------
//Eliminar
        $scope.confirmDelete = function (id) {
          $scope.idautocampcateg = id;
          openModal();
        }

        $scope.deleteAutocampcateg = function () {
          console.log($scope.idautocampcateg);
          RestServices.delete({id: $scope.idautocampcateg}).then(function (data) {
            notificationService.warning(data.message);
            $scope.listautocampcateg();
          });
          closeModal();
        }
      }])
    .controller('createController', ['$scope', 'RestServices', 'notificationService', function ($scope, RestServices, notificationService) {
        $scope.save = function () {
          console.log($scope.data);
          RestServices.save($scope.data).then(function (data) {
            notificationService.success(data.message);
            document.location.href = fullUrlBase + "automaticcampaigncategory#/";
          });
        };
      }])
    .controller('editController', ['$scope', '$stateParams', 'RestServices', 'notificationService', function ($scope, $stateParams, RestServices, notificationService) {
        $scope.loadData = function () {
          RestServices.get($stateParams.idautomacampcateg).then(function (data) {
            $scope.data = data;
          });
        };

        $scope.loadData();

        $scope.edit = function () {
          RestServices.edit($scope.data).then(function (data) {
            notificationService.info(data.message);
            document.location.href = fullUrlBase + "automaticcampaigncategory#/";
          });
        };
      }]);
})();