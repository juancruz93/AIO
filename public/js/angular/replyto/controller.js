(function () {
  angular.module('replyto.controller', [])
          .controller('listController', ['$scope', 'RestServices', 'notificationService','$state', function ($scope, RestServices, notificationService, $state) {
              //set data
              $scope.data = {};

              $scope.data.initial = 0;
              $scope.data.page = 1;

              //Set misc
              $scope.misc = {};

              $scope.functions = {
                forward: function () {
                  $scope.data.initial += 1;
                  $scope.data.page += 1;
                  $scope.restServices.listreplyto();
                },
                fastforward: function () {
                  $scope.data.initial = ($scope.misc.list.total_pages - 1);
                  $scope.data.page = $scope.misc.list.total_pages;
                  $scope.restServices.listreplyto();
                },
                backward: function () {
                  $scope.data.initial -= 1;
                  $scope.data.page -= 1;
                  $scope.restServices.listreplyto();
                },
                fastbackward: function () {
                  $scope.data.initial = 0;
                  $scope.data.page = 1;
                  $scope.restServices.listreplyto();
                },
                confirmDelete: function (id) {
                  $scope.data.idreplyto = id;
                  openModal();
                },
                list: function (data) {
                  $scope.misc.list = data;
                },
                refresh: function () {
                  $state.reload();
                },
              }

              $scope.restServices = {
                listreplyto: function () {
                  RestServices.list($scope.data.initial, $scope.data.filter).then(function (data) {
                    $scope.functions.list(data);
                  });
                },
                deletereplyto: function () {
                  RestServices.delete({id: $scope.data.idreplyto}).then(function (data) {
                    notificationService.warning(data.message);
                    $scope.restServices.listreplyto();
                  });
                  closeModal();
                }
              }

              $scope.$watch('[data.filter.dateinitial,data.filter.dateend]', function () {
                $scope.restServices.listreplyto();
              });

              $scope.restServices.listreplyto();

            }])
          .controller('createController', ['$scope', 'RestServices', 'notificationService', 'contantreplyto', '$state', function ($scope, RestServices, notificationService, contantreplyto, $state) {

              $scope.restServices = {
                save: function () {
                  RestServices.save($scope.data).then(function (data) {
                    notificationService.success(data.message);
                    $state.go(contantreplyto.State.list.state);
                  });
                },
              }

            }])
          .controller('editController', ['$scope', '$stateParams', 'RestServices', 'notificationService', 'contantreplyto', '$state', function ($scope, $stateParams, RestServices, notificationService, contantreplyto, $state) {
              //set data
              $scope.data = {};
              //Set misc
              $scope.misc = {};
              $scope.functions = {
                load: function (data) {
                  $scope.misc.data = data;
                  if (data.status == 1) {
                    $scope.misc.data.status = true;
                  } else {
                    $scope.misc.data.status = false;
                  }
                }
              }

              $scope.restServices = {
                loadData: function () {
                  RestServices.getreplyto($stateParams.idReplyTo).then(function (data) {
                    $scope.functions.load(data.data);
                  });
                },
                edit: function () {
                  RestServices.edit($scope.misc.data).then(function (data) {
                    notificationService.info(data.message);
                    $state.go(contantreplyto.State.list.state);
                  });
                }
              }
              $scope.restServices.loadData();

            }]);
})();
