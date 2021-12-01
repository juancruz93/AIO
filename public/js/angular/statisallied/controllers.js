(function () {
  angular.module('statisallied.controllers', [])
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
          .controller('statisalliedController', ['$scope', 'restService', 'notificationService', '$rootScope', '$timeout', '$routeParams', function ($scope, restService, notificationService, $rootScope, $timeout, $routeParams) {
                  
                      
              $scope.initial = 0;
              $scope.page = 1;     
              $scope.loader = true;
              
              $scope.getAll = function () {
                restService.getAll($scope.initial).then(function (data) {
                  $scope.statisallied = data;
                  $scope.loader = false;
                  $scope.arrMailCategory = data;
                  console.log(data);
                });
              };
              $scope.forward = function () {
                $scope.initial += 1;
                $scope.page += 1;
                $scope.loader = true;
                $scope.getAll();
              };
              $scope.fastforward = function () {
                $scope.initial = ($scope.statisallied.total_pages - 1);
                $scope.page = $scope.statisallied.total_pages;
                $scope.loader = true;
                $scope.getAll();
              };
              $scope.backward = function () {
                $scope.initial -= 1;
                $scope.page -= 1;
                $scope.loader = true;
                $scope.getAll();
              };
              $scope.fastbackward = function () {
                $scope.initial = 0;
                $scope.page = 1;
                $scope.loader = true;
                $scope.getAll();
              };
              $scope.getAll();
              
              $scope.dowloadReport = function (data) {
                restService.downloadReport(data).then(function () {
                  var url = fullUrlBase + 'statistic/download'
                  location.href = url;
                });
              };

            }])
          .controller('statisalliedCreateController', ['$scope', 'restService', 'notificationService', '$rootScope', '$timeout', 'arrayConstruct', '$window', function ($scope, restService, notificationService, $rootScope, $timeout, arrayConstruct, $window) {


            }])
          .controller('statisalliedEditController', ['$scope', 'restService', 'notificationService', '$rootScope', '$timeout', 'arrayConstruct', '$routeParams', '$window', function ($scope, restService, notificationService, $rootScope, $timeout, arrayConstruct, $routeParams, $window) {

            }])
          .controller('statisalliedViewController', ['$scope', 'restService', 'notificationService', '$rootScope', '$timeout', 'arrayConstruct', '$routeParams', function ($scope, restService, notificationService, $rootScope, $timeout, arrayConstruct, $routeParams) {


            }])


})();
