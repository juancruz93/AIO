(function () {
  angular.module('knowledgebase.controllers', [])
          .controller('KnowledgebaseController', ['$scope', 'restService', 'notificationService', function ($scope, restService, notificationService) {
              $scope.getAll = function () {
                restService.getAll($scope.initial).then(function (data) {
                  $scope.imports = data;
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
                $scope.initial = ($scope.imports.total_pages - 1);
                $scope.page = $scope.imports.total_pages;
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
          .controller('KnowledgebaseImportController', ['$scope', '$routeParams', '$window', 'restService', 'notificationService', 'FileUploader', function ($scope, $routeParams, $window, restService, notificationService, FileUploader) {
              $scope.dis = false;
              var uploadUrl = fullUrlBase + 'api/knowledgebase/importcsv';
              var uploader = $scope.uploader = new FileUploader({
                url: uploadUrl
              });
              uploader.onWhenAddingFileFailed = function (item /*{File|FileLikeObject}*/, filter, options) {
                //console.info('onWhenAddingFileFailed', item, filter, options);
              };
              uploader.onAfterAddingFile = function (fileItem) {
                //console.info('onAfterAddingFile', fileItem);
//          $scope.arrFilePending = true;
//                $scope.uploader.queue[$scope.uploader.queue.length - 1].upload();
              };
              uploader.onBeforeUploadItem = function (item) {
                //console.info('onBeforeUploadItem', item);
              };
              uploader.onProgressItem = function (fileItem, progress) {
                //console.info('onProgressItem', fileItem, progress);
              };
              uploader.onSuccessItem = function (fileItem, response, status, headers) {
//                var url = fullUrlBase + 'knowledgebase/download/' + $scope.uploader.queue[$scope.uploader.queue.length - 1].file.name;
//                location.href = url;
                notificationService.info("Se ha importado exitosamente el archivo CSV");
                $window.location.href = '#/';
//                console.info('onSuccessItem', fileItem, response, status, headers);
              };
              uploader.onErrorItem = function (fileItem, response, status, headers) {
//                console.info('onErrorItem', fileItem, response, status, headers);
                notificationService.error(response.message);
                $scope.dis = false;
              };
              uploader.onCompleteItem = function (fileItem, response, status, headers) {

                //console.info('onCompleteItem', fileItem, response, status, headers);
              };
              $scope.file = [];
              $scope.pushFiles = function (element, name) {
                $scope.$apply(function ($scope) {
                  element.files.id = name;
                  $scope.file = element.files;
                });
              };

              $scope.importcsv = function () {

                if (typeof ($scope.uploader.queue[$scope.uploader.queue.length - 1]) == "undefined") {
                  notificationService.error("Debe elegir un archivo");
                } else {
                  $scope.dis = true;
                  $scope.uploader.queue[$scope.uploader.queue.length - 1].upload();
                }
              }

            }])
          .controller('KnowledgebaseValidateController', ['$scope', '$routeParams', '$window', 'restService', 'notificationService', 'FileUploader', function ($scope, $routeParams, $window, restService, notificationService, FileUploader) {
              $scope.dis = false;
              var uploadUrl = fullUrlBase + 'api/knowledgebase/validatecsv';
              var uploader = $scope.uploader = new FileUploader({
                url: uploadUrl
              });
              uploader.onWhenAddingFileFailed = function (item /*{File|FileLikeObject}*/, filter, options) {
                //console.info('onWhenAddingFileFailed', item, filter, options);
              };
              uploader.onAfterAddingFile = function (fileItem) {
                //console.info('onAfterAddingFile', fileItem);
//          $scope.arrFilePending = true;
//                $scope.uploader.queue[$scope.uploader.queue.length - 1].upload();
              };
              uploader.onBeforeUploadItem = function (item) {
                //console.info('onBeforeUploadItem', item);
              };
              uploader.onProgressItem = function (fileItem, progress) {
                //console.info('onProgressItem', fileItem, progress);
              };
              uploader.onSuccessItem = function (fileItem, response, status, headers) {
                var url = fullUrlBase + 'knowledgebase/download/' + $scope.uploader.queue[$scope.uploader.queue.length - 1].file.name;
                location.href = url;
                notificationService.info("Se ha validado los correos y descargado un CSV de resultados.");
                $window.location.href = '#/';
//                console.info('onSuccessItem', fileItem, response, status, headers);
              };
              uploader.onErrorItem = function (fileItem, response, status, headers) {
//                console.info('onErrorItem', fileItem, response, status, headers);
                notificationService.error(response.message);
                $scope.dis = false;
              };
              uploader.onCompleteItem = function (fileItem, response, status, headers) {

                //console.info('onCompleteItem', fileItem, response, status, headers);
              };
              $scope.file = [];
              $scope.pushFiles = function (element, name) {
                $scope.$apply(function ($scope) {
                  element.files.id = name;
                  $scope.file = element.files;
                });
              };

              $scope.importcsv = function () {

                if (typeof ($scope.uploader.queue[$scope.uploader.queue.length - 1]) == "undefined") {
                  notificationService.error("Debe elegir un archivo");
                } else {
                  $scope.dis = true;
                  $scope.uploader.queue[$scope.uploader.queue.length - 1].upload();
                }
              }

            }])

})();
