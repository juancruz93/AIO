angular.module('aio', [])
        .factory('main', ['$http', '$window', function ($http, $window) {
            return {
              newAllied: function (data, success, error) {
                $http.post($window.myBaseURL + 'masteraccount/aliascreate/' + data.idMasteraccount, data).success(success).error(error);
              },
              country: function (success, error) {
                $http.get($window.myBaseURL + 'country/country').success(success).error(error);
              },
              state: function (data, success, error) {
                $http.get($window.myBaseURL + 'country/state/' + data).success(success).error(error);
              },
              city: function (data, success, error) {
                $http.get($window.myBaseURL + 'country/cities/' + data).success(success).error(error);
              }
            }
          }])
        .controller('ctrlUser', ['$rootScope', '$scope', '$http', 'main', '$window', '$interval', function ($rootScope, $scope, $http, main, $window, $interval) {

            main.country(function (res) {
              $scope.country = res;
            }, function (res) {
              $rootScope.error = 'fail';
            });

            $scope.selectCountry = function (id) {
              if (!id) {
                id = $scope.countrySelected;
              }
              $scope.state = {};
              $scope.cities = {};
              main.state(id, function (res) {
                $scope.state = res;
              }, function (res) {
                $rootScope.error = 'fail';
              });
            };
            $scope.selectState = function () {
              $scope.cities = {};
              main.city($scope.stateSelected, function (res) {
                $scope.cities = res;
              }, function (res) {
                $rootScope.error = 'fail';
              });
            };


            $scope.selectCountryUser = function (id) {
              if (!id) {
                id = $scope.countrySelectedUser
              }
              $scope.stateUser = "";
              $scope.citiesUser = "";
              main.state(id, function (res) {
                $scope.stateUser = res;
              }, function (res) {
                $rootScope.error = 'fail';
              });
            };
            $scope.selectStateUser = function (id) {
              if (!id) {
                id = $scope.stateSelectedUser
              }
              $scope.cities = {};
              main.city(id, function (res) {
                //console.log(res);
                $scope.citiesUser = res;
              }, function (res) {
                $rootScope.error = 'fail';
              });
            };

          }]);
