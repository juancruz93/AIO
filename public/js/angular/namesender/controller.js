(function () {
  angular.module('namesender.controller', [])
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
                  $scope.restServices.listnamesender();
                },
                fastforward: function () {
                  $scope.data.initial = ($scope.misc.list.total_pages - 1);
                  $scope.data.page = $scope.misc.list.total_pages;
                  $scope.restServices.listnamesender();
                },
                backward: function () {
                  $scope.data.initial -= 1;
                  $scope.data.page -= 1;
                  $scope.restServices.listnamesender();
                },
                fastbackward: function () {
                  $scope.data.initial = 0;
                  $scope.data.page = 1;
                  $scope.restServices.listnamesender();
                },
                confirmDelete: function (id) {
                  $scope.data.idnamesender = id;
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
                listnamesender: function () {
                  RestServices.list($scope.data.initial, $scope.data.filter).then(function (data) {
                    $scope.functions.list(data);
                  });
                },
                deletenamesender: function () {
                  RestServices.delete({id: $scope.data.idnamesender}).then(function (data) {
                    notificationService.warning(data.message);
                    $scope.restServices.listnamesender();
                  });
                  closeModal();
                }
              }

              $scope.$watch('[data.filter.dateinitial,data.filter.dateend]', function () {
                $scope.restServices.listnamesender();
              });

              $scope.restServices.listnamesender();

            }])
          .controller('createController', ['$scope', 'RestServices', 'notificationService', 'contantnamesender', '$state', function ($scope, RestServices, notificationService, contantnamesender, $state) {

              $scope.restServices = {
                save: function () {
                  RestServices.save($scope.data).then(function (data) {
                    notificationService.success(data.message);
                    $state.go(contantnamesender.State.list.state);
                  });
                },
              }

            }])
          .controller('editController', ['$scope', '$stateParams', 'RestServices', 'notificationService', 'contantnamesender', '$state', function ($scope, $stateParams, RestServices, notificationService, contantnamesender, $state) {
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
                  RestServices.getnamesender($stateParams.idNameSender).then(function (data) {
                    $scope.functions.load(data.data);
                  });
                },
                edit: function () {
                  RestServices.edit($scope.misc.data).then(function (data) {
                    notificationService.info(data.message);
                    $state.go(contantnamesender.State.list.state);
                  });
                }
              }
              $scope.restServices.loadData();

            }]);
})();
