(function () {
  angular.module('footer.controllers', [])
      .controller('indexController', ['$scope', '$rootScope', 'restService', 'notificationService', function ($scope, $rootScope, restService, notificationService) {

        $scope.initial = 0;
        $scope.page = 1;

        var selectIdFooter;

        $scope.getAll = function () {
          restService.getFooterList($scope.initial).then(function (res) {
            $scope.footer = res;
          });
        };
        $scope.forward = function () {
          $scope.initial += 1;
          $scope.page += 1;
          $scope.getAll();
        };
        $scope.fastforward = function () {
          $scope.initial = ($scope.footer.total_pages - 1);
          $scope.page = $scope.footer.total_pages;
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

        $scope.openModal = function (idFooter) {
          selectIdFooter = idFooter;
          $('.dialog').addClass('dialog--open');
        }

        $scope.closeModal = function () {
          $('.dialog').removeClass('dialog--open');
        }

        $scope.deleteFooter = function () {
          restService.deleteFooter(selectIdFooter).then(function (res) {
            notificationService.warning(res['message']);
            $scope.closeModal();
            $scope.getAll();
          });
        }

        $scope.viewContent = function (idFooter) {
          verPreview(idFooter);
        }
      }])
      .controller('createController', ['$scope', '$rootScope', 'restService', 'notificationService', function ($scope, $rootScope, restService, notificationService) {
        $scope.data = {};
        $scope.saveFooter = function () {
          $scope.data.editor = document.getElementById('iframeEditor').contentWindow.catchEditorData();
          document.getElementById('iframeEditor').contentWindow.RecreateEditor();
          restService.addFooter($scope.data).then(function (res) {
            //console.log(res);
            location.href = fullUrlBase + templateBase;
          });
        }
      }])

      .controller('updateController', ['$scope', '$rootScope', 'restService', 'notificationService', function ($scope, $rootScope, restService, notificationService) {
        $scope.data = {};
        $scope.updateFooter = function () {
          $scope.data.editor = document.getElementById('iframeEditor').contentWindow.catchEditorData();
          document.getElementById('iframeEditor').contentWindow.RecreateEditor();
          restService.updateFooter($scope.data).then(function (res) {
            //console.log(res);
            location.href = fullUrlBase + templateBase;
          });
        }
        restService.getOneFooter(idFooter).then(function (res) {
          //console.log(res);
          $scope.data.idFooter = res[0].idFooter;
          $scope.data.name = res[0].name;
          $scope.data.description = res[0].description;
        });
      }])

})();

