(function () {
  angular.module('sxc.controllers', [])
    .controller('ContactSegmentController', ['$scope', 'restService', 'notificationService', function ($scope, restService, notificationService) {
        $scope.show = true;
        $scope.showList = true;
        $scope.loading = false;
        $scope.progressbar = false;

        $scope.showStatus = function (arr, value) {
          var selected = [];
          angular.forEach(arr, function (s) {
            angular.forEach(value, function (c) {
              if (s == c) {
                selected.push(s);
              }
            });
          });
          return selected.length ? selected.join(', ') : 'empty';
        };

        $scope.updateUser = function (data, key, idContact, idCustomfield) {
          $scope.progressbar = false;
          restService.editContact(idContact, key, data, idCustomfield).then(function (data) {
            notificationService.primary(data.message);
            $scope.progressbar = true;
            $scope.getAll();
          });
        }

        $scope.initial = 0;
        $scope.page = 1;
        $scope.stringsearch = -1;

        $scope.getAll = function () {
          $scope.progressbar = false;
          $scope.loading = false;
          restService.getAll($scope.initial, idSegment, $scope.stringsearch).then(function (data) {
            $scope.contact = data;
            if ($scope.contact.total == 0) {
              $scope.show = false;
              $scope.showList = true;
            } else {
              $scope.show = true;
              $scope.showList = false;
            }
          });
          restService.getAllIndicative().then(function (data) {
            $scope.indicative = data;
          });
          restService.customfield(idSegment).then(function (data) {
            $scope.arr = [];
            for (var i = 0; i < data.length; i++) {
              if (data[i].type == "Select" || data[i].type == "Multiselect")
                $scope.arr[data[i].idCustomfield] = data[i].value;
            }
            $scope.loading = true;
            $scope.progressbar = true;
          });

        }


        $scope.forward = function () {
          $scope.progressbar = true;
          $scope.loading = false;
          $scope.initial += 1;
          $scope.page += 1;
          $scope.getAll();
        };
        $scope.fastforward = function () {
          $scope.progressbar = true;
          $scope.loading = false;
          $scope.initial = ($scope.contact.total_pages - 1);
          $scope.page = $scope.contact.total_pages;
          $scope.getAll();
        };
        $scope.backward = function () {
          $scope.progressbar = true;
          $scope.loading = false;
          $scope.initial -= 1;
          $scope.page -= 1;
          $scope.getAll();
        };
        $scope.fastbackward = function () {
          $scope.progressbar = true;
          $scope.loading = false;
          $scope.initial = 0;
          $scope.page = 1;
          $scope.getAll();
        };
        $scope.searchcontacts = function () {
          $scope.progressbar = true;
          $scope.loading = false;
          $scope.stringsearch = $scope.search;
          $scope.getAll();
        };

        $scope.stringfieldsprimary = function (field, value) {
          var string = field;
          switch (field) {
            case "name":
              string = "Nombre";
              break;
            case "lastname":
              string = "Apellido";
              break;
            case "email":
              string = "Correo electronico";
              break;
            case "phone":
              string = "Telefono";
              break;
            case "birthdate":
              string = "Fecha de cumpleaños";
              break;
            case "indicative":
              string = "Indicativo";
              break;
          }
          return string;
        }
        $scope.changestatus = function (idContact, idContactlist) {
          restService.changestatus(idContact, idContactlist).then(function (data) {
            notificationService.primary(data.message);
            $scope.getAll();
          });
        };
        $scope.searchcontacts = function () {
          $scope.stringsearch = $scope.search;
          $scope.getAll();
        };
        $scope.getAll();
      }])
})();
