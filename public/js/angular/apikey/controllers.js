(function () {
    angular.module('apikey.controllers', [])
        .controller('indexController', ['$scope', 'restService', 'notificationService', function ($scope, restService, notificationService) {

            $scope.initial = 0;
            $scope.page = 1;
            var selectIdApikey;
            var selectIdApikeyRege;

            $scope.getAll = function () {
                restService.getApikeyList($scope.initial).then(function (res) {
                    $scope.apikey = res;
                    //console.log($scope.apikey);
                });
            };
            $scope.forward = function () {
                $scope.initial += 1;
                $scope.page += 1;
                $scope.getAll();
            };
            $scope.fastforward = function () {
                $scope.initial = ($scope.apikey.total_pages - 1);
                $scope.page = $scope.apikey.total_pages;
                $scope.getAll();
            };
            $scope.backward = function () {
                $scope.initial -= 1;
                $scope.page -= 1;
                $scope.getAll();
            };
            $scope.fastbackward = function () {
                $scope.initial = 0;
                $scope.page = 1;
                $scope.getAll();
            };

            $scope.getAll();

            $scope.addApikey = function (idUser) {
                restService.addApikey(idUser).then(function (res) {
                    //console.log(res);
                    notificationService.success(res.message);
                    $scope.getAll();
                });
            };

            $scope.editApikey = function (idUser) {
                restService.editApikey(idUser).then(function (res) {
                    //console.log(res);
                    notificationService.primary(res.message);
                    $scope.getAll();
                });
            };

            $scope.changeStatusApikey = function (idUser, status) {
                //console.log(idUser + " " + status);
                var data = {
                    status: status
                };
                restService.changeStatusApikey(idUser, data).then(function (res) {
                    //console.log(res);
                    notificationService.primary(res.message);
                    $scope.getAll();
                });
            };

            $scope.openModal = function (idApikey) {
                selectIdApikey = idApikey;
                $('#deletedialog').addClass('dialog--open');
            };

            $scope.closeModal = function () {
                $('#deletedialog').removeClass('dialog--open');
            };

            $scope.openModalRegenerate = function (idApikey) {
                //console.log(idApikey);
                selectIdApikeyRege = idApikey;
                $('#regeneratedialog').addClass('dialog--open');
            };

            $scope.closeModalRegenerate = function () {
                $('#regeneratedialog').removeClass('dialog--open');
            };

            $scope.deleteApikey = function () {
                restService.deleteApikey(selectIdApikey).then(function (res) {
                    //console.log(res);
                    notificationService.warning(res.message);
                    $scope.closeModal();
                    $scope.getAll();
                });
            };

            $scope.RegenerateApikey = function () {
                restService.editApikey(selectIdApikeyRege).then(function (res) {
                    //console.log(res);
                    notificationService.primary(res.message);
                    $scope.closeModalRegenerate();
                    $scope.getAll();
                });
            };

        }])
})();
