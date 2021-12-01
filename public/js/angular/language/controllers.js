(function () {
  angular.module('language.controllers', [])
          .controller('LanguageController', ['$scope', 'restService', 'notificationService', function ($scope, restService, notificationService) {
              $scope.getAll = function () {
                restService.getAll($scope.initial).then(function (data) {

                  $scope.language = data;
                });
              };
              $scope.confirmDelete = function (idLanguage) {
                $scope.idLanguage = idLanguage;
                openModal();
              };
              $scope.deleteLanguage = function () {
                restService.deleteLanguage($scope.idLanguage).then(function (data) {
                  closeModal();
                  notificationService.warning(data.message);
                  $scope.getAll();
                });
              };

              $scope.initial = 0;
              $scope.page = 1;

              $scope.forward = function () {
                $scope.initial += 1;
                $scope.page += 1;
                $scope.getAll();
              };
              $scope.fastforward = function () {
                $scope.initial = ($scope.language.total_pages - 1);
                $scope.page = $scope.language.total_pages;
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
            }])
          .controller('LanguageEditController', ['$scope', '$routeParams', '$window', 'restService', 'notificationService', function ($scope, $routeParams, $window, restService, notificationService) {
              var id = $routeParams.id;
              restService.getOne(id).then(function (data) {
                $scope.language = data;
              });

              $scope.editLanguage = function () {
                if (!$scope.language) {
                  notificationService.error("El campo nombre no puede estar vacio");
                }
                restService.edit(id, $scope.language.name, $scope.language.shortName).then(function (data) {
                  $window.location.href = '#/';
                  notificationService.info(data.message);
                });
              };
            }])

})();
