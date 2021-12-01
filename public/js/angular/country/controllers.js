(function () {
  angular.module('country.controllers', [])
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
          .controller('CountryController', ['$scope', 'restService', 'notificationService', function ($scope, restService, notificationService) {
              $scope.userRole = userRole;
              $scope.root = root;
              $scope.master = master;
              $scope.allied = allied;
              $scope.account = account;
              $scope.subaccount = subaccount;

              $scope.getAll = function () {
                restService.getAll($scope.initial).then(function (data) {
                  $scope.countries = data;
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
                $scope.initial = ($scope.countries.total_pages - 1);
                $scope.page = $scope.countries.total_pages;
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
          .controller('CountryEditController', ['$scope', '$routeParams', '$window', 'restService', 'notificationService', function ($scope, $routeParams, $window, restService, notificationService) {
              var id = $routeParams.id;
              restService.getOne(id).then(function (data) {
                $scope.country = data;
              });

              $scope.editCountry = function () {
                if (typeof($scope.country.name) == "undefined") {
                  notificationService.error("El campo nombre no puede estar vacio");
                }
                restService.edit($scope.country).then(function (data) {
                  $window.location.href = '#/';
                  notificationService.info(data.message);
                });
              };
            }])
})();
