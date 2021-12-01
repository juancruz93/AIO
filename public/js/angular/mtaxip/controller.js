(function () {
  angular.module('mtaxip.controller', ['ui.select'])
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
          .controller('listController', ['$scope', 'RestServices', 'notificationService', '$state', function ($scope, RestServices, notificationService, $state) {
              //set data
              $scope.data = {};
              $scope.contador = 0;

              $scope.data.initial = 0;
              $scope.data.page = 1;

              //Set misc
              $scope.misc = {};

              $scope.functions = {
                forward: function () {
                  $scope.data.initial += 1;
                  $scope.data.page += 1;
                  $scope.restServices.listmtaxip();
                },
                fastforward: function () {
                  $scope.data.initial = ($scope.misc.list.total_pages - 1);
                  $scope.data.page = $scope.misc.list.total_pages;
                  $scope.restServices.listmtaxip();
                },
                backward: function () {
                  $scope.data.initial -= 1;
                  $scope.data.page -= 1;
                  $scope.restServices.listmtaxip();
                },
                fastbackward: function () {
                  $scope.data.initial = 0;
                  $scope.data.page = 1;
                  $scope.restServices.listmtaxip();
                },
                confirmDelete: function (id) {
                  $scope.data.idmtaxip = id;
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
                listmtaxip: function () {
                  RestServices.list($scope.data.initial, $scope.data.filter).then(function (data) {
                    $scope.functions.list(data);
                  });
                },
                deletemtaxip: function () {
                  RestServices.delete({id: $scope.data.idmtaxip}).then(function (data) {
                    notificationService.warning(data.message);
                    $scope.restServices.listmtaxip();
                  });
                  closeModal();
                }
              }

              $scope.$watch('[data.filter.dateinitial,data.filter.dateend]', function () {
                $scope.restServices.listmtaxip();
              });

              $scope.restServices.listmtaxip();

            }])
          .controller('createController', ['$scope', 'RestServices', 'notificationService', 'contantmtaxip', '$state', function ($scope, RestServices, notificationService, contantmtaxip, $state) {
              //set data
              $scope.data = {
                ips: {},
                search: {}
              };

              $scope.misc = {
                search: {},
                accounts: [],
                categories: [{name: 'A'}, {name: 'B'}, {name: 'D'}, {name: 'F'}],
              }



              $scope.functions = {
                ips: function (data) {
                  $scope.misc.ips = data.data;
                  //console.log($scope.data.ips);
                },

                searchReport: function () {
                  console.log('Entro al search');
                },
              }

              $scope.restServices = {
                getip: function () {
                  RestServices.getip().then(function (data) {
                    $scope.functions.ips(data);
                  });
                },
                save: function () {
                  RestServices.save($scope.data).then(function (data) {
                    notificationService.success(data.message);
                    $state.go(contantmtaxip.State.list.state);
                  });
                },
              }

              $scope.restServices.getip();

            }])
          .controller('editController', ['$scope', '$stateParams', 'RestServices', 'notificationService', 'contantmtaxip', '$state', function ($scope, $stateParams, RestServices, notificationService, contantmtaxip, $state) {
              //set data
              $scope.data = {};
              //Set misc
              $scope.misc = {};
              $scope.functions = {
                load: function (data) {
                  $scope.misc.data = data;                  
                },
                ips: function (data) {
                  $scope.misc.ips = data.data;                  
                },
              }

              $scope.restServices = {
                getip: function () {
                  RestServices.getip().then(function (data) {
                    $scope.functions.ips(data);
                  });
                },
                loadData: function () {
                  RestServices.getmtaxip($stateParams.idMta).then(function (data) {
                    $scope.functions.load(data.data);
                  });
                },
                edit: function () {
                  RestServices.edit($scope.misc.data).then(function (data) {
                    notificationService.info(data.message);
                    $state.go(contantmtaxip.State.list.state);
                  });
                }
              }
              $scope.restServices.loadData();
              $scope.restServices.getip();

            }]);
})();
