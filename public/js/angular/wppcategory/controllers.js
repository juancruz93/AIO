angular.module('wppcategory.controllers', [])
    .controller('listController', ['$scope', 'RestServices', 'notificationService', '$state', function ($scope, RestServices, notificationService, $state) {

        $scope.search = function () {
            $scope.getAll();
        };

        $scope.openModal = function (idWppCategory) {
            $scope.data.idWppCategory = idWppCategory;
            $('#deleteDialog').addClass('dialog--open');
        };
        $scope.closeModal = function () {
            $('.dialog').removeClass('dialog--open');
        };

        $scope.initializeVariable = function () {
            $scope.data = {};
            $scope.data.initial = 0;
            $scope.data.page = 1;
            $scope.data.filter = "";
            $scope.data.wppcategory = [{}];
        };

        $scope.setMethodData = function (item, data) {
            $scope.data[item] = data;
        };

        $scope.getAll = function () {
            RestServices.getAll($scope.data.initial, $scope.data.filter).then(function (data) {
                $scope.setMethodData('wppcategory', data);
            });
        };

        $scope.deleteCategory = function () {
            RestServices.deleteCategory($scope.data.idWppCategory).then(function (data) {
                notificationService.warning(data.message);
                $scope.setMethodData('initial', 0);
                $scope.setMethodData('page', 1);
                $scope.getAll();
                $scope.closeModal();
            });
        };

        $scope.$watch('[data.filter.dateinitial,data.filter.dateend]', function () {
            if (typeof $scope.data.filter.dateinitial != 'undefined' & typeof $scope.data.filter.dateend != 'undefined') {
                $scope.getAll();
            }
        });

        $scope.initializeVariable();
        $scope.getAll();
    }])