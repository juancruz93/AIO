'use strict';
(function () {
  angular.module("tax.controller", [])
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
    .controller("listController", ["$scope", "RestServices", "notificationService", function ($scope, RestServices, notificationService) {
        $scope.initial = 0;
        $scope.page = 1;

        $scope.forward = function () {
          $scope.initial += 1;
          $scope.page += 1;
          $scope.lisTax();
        };
        $scope.fastforward = function () {
          $scope.initial = ($scope.list.total_pages - 1);
          $scope.page = $scope.list.total_pages;
          $scope.lisTax();
        };
        $scope.backward = function () {
          $scope.initial -= 1;
          $scope.page -= 1;
          $scope.lisTax();
        };
        $scope.fastbackward = function () {
          $scope.initial = 0;
          $scope.page = 1;
          $scope.lisTax();
        };

        $scope.lisTax = function () {
          RestServices.listTax($scope.initial, "").then(function (data) {
            $scope.list = data;
          });
        };

        $scope.lisTax();

        $scope.searchForName = function () {
          RestServices.listTax($scope.initial, $scope.filterName).then(function (data) {
            $scope.list = data;
          });
        };

        $scope.openMod = function (id) {
          $scope.idTax = id;
          openModal();
        };

        $scope.deleteTax = function () {
          RestServices.deleteTax($scope.idTax).then(function (data) {
            $scope.lisTax();
            closeModal();
            notificationService.warning(data.message);
          });
        };
      }])
    .controller("createController", ["$scope", "$state", "RestServices", "notificationService", function ($scope, $state, RestServices, notificationService) {
        $scope.types = [
          {key: "percentage", name: "Porcentaje"},
          {key: "net", name: "Neto"},
        ];
        $scope.data = {};
        $scope.data.status = true;
        $scope.listCountry = function () {
          RestServices.listCountry().then(function (data) {
            $scope.countries = data;
          });
        };
        $scope.listCountry();

        $scope.saveTax = function () {
          if ($scope.data.country == null) {
            notificationService.error("Debes seleccionar el país en donde estará disponible el impuesto");
            return false;
          }

          if ($scope.data.tp == null) {
            notificationService.error("Debes seleccionar el tipo del valor del impuesto neto o porcentaje");
            return false;
          }

          $scope.data.idCountry = $scope.data.country.idCountry;
          $scope.data.type = $scope.data.tp.key;
          RestServices.createTax($scope.data).then(function (data) {
            notificationService.success(data.message);
            $state.go("index");
          });
        };
      }])
    .controller("editController", ["$scope", "$state", "RestServices", "notificationService", "$stateParams", function ($scope, $state, RestServices, notificationService, $stateParams) {
        $scope.getTax = function () {
          RestServices.getTax($stateParams.idTax).then(function (data) {
            $scope.data = data;
            $scope.data.status = (data.status === 1);
            $scope.listCountry();
            $scope.selectType();
          });
        };
        $scope.getTax();

        $scope.listCountry = function () {
          RestServices.listCountry().then(function (data) {
            $scope.countries = data;
            $scope.selectCountry();
          });
        };

        $scope.types = [
          {key: "percentage", name: "Porcentaje"},
          {key: "net", name: "Neto"}
        ];

        $scope.selectCountry = function () {
          for (var i = 0; i < $scope.countries.length; i++) {
            if ($scope.countries[i].idCountry === $scope.data.idCountry) {
              $scope.data.country = {idCountry: $scope.data.idCountry, name: $scope.countries[i].name};
            }
          }
        }

        $scope.selectType = function () {
          for (var i = 0; i < $scope.types.length; i++) {
            if ($scope.types[i].key === $scope.data.type) {
              $scope.data.tp = {key: $scope.data.type, name: $scope.types[i].name};
            }
          }
        }

        $scope.editTax = function () {
          if ($scope.data.country == null) {
            notificationService.error("Debes seleccionar el país en donde estará disponible el impuesto");
            return false;
          }

          if ($scope.data.tp == null) {
            notificationService.error("Debes seleccionar el tipo del valor del impuesto neto o porcentaje");
            return false;
          }

          $scope.data.idCountry = $scope.data.country.idCountry;
          $scope.data.type = $scope.data.tp.key;
          RestServices.ediTax($scope.data).then(function (data) {
            notificationService.info(data.message);
            $state.go("index");
          });
        };
      }]);
})();