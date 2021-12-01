(function () {
  angular.module('mail_structure.controllers', [])
    .controller('ctrlCreate', ['$scope', 'restService', 'notificationService', function ($scope, restService, notificationService) {
        $scope.uploadedFile = function (element) {
          $scope.$apply(function ($scope) {
            $scope.preview = element.files;
          });
        };
        $scope.data = {};
        $scope.saveContent = function () {
//          console.log($scope.preview);
          $scope.data.editor = document.getElementById('iframeEditor').contentWindow.catchEditorData();
          $scope.data.preview = $scope.preview;
          restService.addPredeterminedstructure($scope.data).then(function () {
            document.getElementById('iframeEditor').contentWindow.RecreateEditor();
          });
        };
        $scope.saveContentExit = function () {
          $scope.data.editor = document.getElementById('iframeEditor').contentWindow.catchEditorData();
          $scope.data.preview = $scope.preview;
          restService.addPredeterminedstructure($scope.data).then(function () {
            location.href = fullUrlBase + templateBase + "/index";
          });
        };
      }])
    .controller('ctrlIndex', ['$scope', 'restService', 'notificationService', function ($scope, restService, notificationService) {
        $scope.htmlPreview = function (content) {
          htmlPreview(content);
        };
        $scope.initial = 0;
        $scope.page = 1;
        $scope.filter = "";
        $scope.search = function () {
          $scope.getAll();
        };
        $scope.forward = function () {
          $scope.initial += 1;
          $scope.page += 1;
          $scope.getAll();
        };
        $scope.fastforward = function () {
          $scope.initial = ($scope.mailstructure.total_pages - 1);
          $scope.page = $scope.mailstructure.total_pages;
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

        $scope.openModal = function (idMailStructure) {
          $scope.idMailStructure = idMailStructure;
          openModal();
        };

        $scope.confirmDelete = function () {
          restService.deleteStructure($scope.idMailStructure).then(function (data) {
            notificationService.warning(data["msg"]);
            closeModal();
            $scope.getAll();
          });
        };

        $scope.getAll = function () {
          restService.getAll($scope.initial, $scope.filter).then(function (data) {
            $scope.mailstructure = data;
            console.log($scope.mailstructure);
          });
        };
        $scope.getAll();
      }])
    .controller('ctrlEdit', ['$scope', 'restService', 'notificationService', function ($scope, restService, notificationService) {
        $scope.data = {};
        $scope.uploadedFile = function (element) {
          $scope.$apply(function ($scope) {
            $scope.preview = element.files;
          });
        };
        $scope.editContent = function () {
          $scope.data.editor = document.getElementById('iframeEditor').contentWindow.catchEditorData();
          $scope.data.preview = $scope.preview;
//          $scope.data.idMailstructure = idMailstructure;
          restService.editMailStructure($scope.data).then(function () {
//            notificationService.primary("Se ha editado la estructuras prediseñadas");
            document.getElementById('iframeEditor').contentWindow.RecreateEditor();
          });
        };
        $scope.editContentExit = function () {
          $scope.data.editor = document.getElementById('iframeEditor').contentWindow.catchEditorData();
          $scope.data.preview = $scope.preview;
//          $scope.data.editor = document.getElementById('iframeEditor').contentWindow.catchEditorData();
//          $scope.data.idMailstructure = idMailstructure;
          restService.editMailStructure($scope.data).then(function () {
//            notificationService.primary("Se ha editado la estructuras prediseñadas");
            location.href = fullUrlBase + templateBase + "/index";
          });
        };

      }]);
})();

