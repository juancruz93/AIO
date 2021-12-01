(function () {
    angular.module("accounting.controllers", [])
        .controller("listController", ["$scope", "$state", "notificationService", "restServices", function ($scope, $state, notificationService, restServices) {
            $scope.loader = false;
            $scope.initial = 0;
            $scope.page = 1;
            $scope.forward = function () {
                $scope.initial += 1;
                $scope.page += 1;
                $scope.listAccountingAccount();
            };
            $scope.fastforward = function () {
                $scope.initial = ($scope.list.total_pages - 1);
                $scope.page = $scope.list.total_pages;
                $scope.listAccountingAccount();
            };
            $scope.backward = function () {
                $scope.initial -= 1;
                $scope.page -= 1;
                $scope.listAccountingAccount();
            };
            $scope.fastbackward = function () {
                $scope.initial = 0;
                $scope.page = 1;
                $scope.listAccountingAccount();
            };

            $scope.listAccountingAccount = function () {
                $scope.loader = true;
                restServices.list($scope.initial).then(function (response) {
                    $scope.list = response;
                    $scope.loader = false;
                }).catch(function (error){
                    notificationService.error(error.message);
                });
            };

            $scope.listAccountingAccount();


        }])
})();