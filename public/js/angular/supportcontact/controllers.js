(function () {
  angular.module('supportcontact.controllers', [])
    .filter('propsFilter', function () {
      return function (items, props) {
        var out = [];

        if (angular.isArray(items)) {
          var keys = Object.keys(props);

          items.forEach(function (item) {
            var itemMatches = false;

            for (var i = 0; i < keys.length; i++) {
              var prop = keys[i];
              var text = props[prop].toLowerCase();
              if (item[prop].toString().toLowerCase().indexOf(text) !== -1) {
                itemMatches = true;
                break;
              }
            }

            if (itemMatches) {
              out.push(item);
            }
          });
        } else {
          // Let the output be the input untouched
          out = items;
        }

        return out;
      };
    })
    .controller('indexcontroller', ['$scope', 'restService', function ($scope, restService) {
        $scope.openModalDelete = function (idSupportContact) {
          $scope.url = "/technicalcontact/delete/" + idSupportContact;
          openModal();
          $scope.getAll();
        };
        $scope.initial = 0;
        $scope.page = 1;
        $scope.forward = function () {
          $scope.initial += 1;
          $scope.page += 1;
          $scope.getAll();
        };
        $scope.fastforward = function () {
          $scope.initial = ($scope.mail.total_pages - 1);
          $scope.page = $scope.mail.total_pages;
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

        $scope.getAll = function () {
          restService.getalltechnical($scope.initial).then(function (data) {
            $scope.supportcontact = data;
          });
        };
        $scope.getAll();
      }])
    .controller('createcontroller', ['$scope', 'restService', '$window', function ($scope, restService, $window) {

        $scope.types = [{id: "technical", name: "Técnico"}, {id: "administrative", name: "Administrativo"}];
        $scope.addtechnicalcontact = function () {
          restService.addtechnical($scope.data).then(function (data) {
            location.href = '/technicalcontact/index/' + idAllied;
          });
        };

      }])
    .controller('editcontroller', ['$scope', 'restService', function ($scope, restService) {

        $scope.types = [{id: "technical", name: "Técnico"}, {id: "administrative", name: "Administrativo"}];
        $scope.edittechnicalcontact = function () {
          restService.edittechnical($scope.data).then(function () {
            location.href = '/technicalcontact/index/' + idAllied + "/" +idMasteraccount;
          });
        };
        restService.getfirsttechnical().then(function (data) {
          $scope.data = data[0];
        });
      }]);

})();

