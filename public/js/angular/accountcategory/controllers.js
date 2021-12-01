(function () {
    angular.module('accountcategory.controllers', [])
        .controller('listController', ['$scope', 'RestServices', 'notificationService', function ($scope, RestServices, notificationService) {
            $scope.initial = 0;
            $scope.page = 1;

            $scope.forward = function () {
                $scope.initial += 1;
                $scope.page += 1;
                $scope.listAccountCategory();
            };
            $scope.fastforward = function () {
                $scope.initial = ($scope.list.total_pages - 1);
                $scope.page = $scope.list.total_pages;
                $scope.listAccountCategory();
            };
            $scope.backward = function () {
                $scope.initial -= 1;
                $scope.page -= 1;
                $scope.listAccountCategory();
            };
            $scope.fastbackward = function () {
                $scope.initial = 0;
                $scope.page = 1;
                $scope.listAccountCategory();
            };

            $scope.listAccountCategory = function () {
                RestServices.listAccountCategorys($scope.initial, $scope.filter).then(function (res) {
                    $scope.list = res;
                });
            };

            $scope.listAccountCategory();

//-----------------------------------------------
//Filtros
            $scope.filtername = function () {
                RestServices.listAccountCategorys($scope.initial, $scope.filter).then(function (res) {
                    $scope.list = res;
                });
            };

//-----------------------------------------------
//Eliminar
            $scope.confirmDelete = function (id) {
                $scope.idAccountCategory = id;
                openModal();
            };

            $scope.deleteAccountCategory = function () {
                RestServices.delete($scope.idAccountCategory).then(function (res) {
                    notificationService.warning(res.message);
                    $scope.listAccountCategory();
                });
                closeModal();
            }
        }])
        .controller('createController', ['$scope', 'RestServices', 'notificationService', '$window', function ($scope, RestServices, notificationService, $window) {
            $scope.data = {};
            $scope.data.expirationDate = true;
            $scope.data.status = true;

            $scope.saveAccountCategory = function () {
                RestServices.createAccountCategory($scope.data).then(function (res) {
                    notificationService.success(res.message);
                    $window.location.href = fullUrlBase + 'accountcategory#/';
                })
            };
        }])
        .controller('editController', ['$scope', 'RestServices', 'notificationService','$window', '$stateParams', function ($scope, RestServices, notificationService, $window, $stateParams) {
            function loadData() {
                RestServices.get($stateParams.id).then(function (res) {
                    $scope.data = res;
                    if(res.status == 1){
                        $scope.data.status = true;
                    }else {
                        $scope.data.status = false;
                    }
                    if(res.expirationDate == 1){
                        $scope.data.expirationDate = true;
                    }else {
                        $scope.data.expirationDate = false;
                    }
                })
            }
            loadData();

            $scope.editAccountCategory = function () {
                RestServices.editAccountCategory($scope.data).then(function (res) {
                    notificationService.primary(res.message);
                    $window.location.href = fullUrlBase + 'accountcategory#/';
                })
            };

        }])
})();