(function () {
  angular.module('history.controllers', [])
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
          .controller('HistoryController', ['$scope', 'restService', 'notificationService', function ($scope, restService, notificationService) {
              $scope.userRole = userRole;
              $scope.root = root;
              $scope.master = master;
              $scope.allied = allied;
              $scope.account = account;
              $scope.subaccount = subaccount;
              $scope.initDisabled = function () {
                $scope.disabledMaster = true;
                $scope.disabledAllied = true;
                $scope.disabledAccount = true;
                $scope.disabledSubaccount = true;

                if (userRole == root) {
                  $scope.disabledMaster = false;
                } else if (userRole == master) {
                  $scope.disabledAllied = false;
                } else if (userRole == allied) {
                  $scope.disabledAccount = false;
                }
              };
              $scope.getSelectFilters = function () {
                if (userRole == root) {
                  $scope.getMasteraccounts();
                } else if (userRole == master) {
                  $scope.filters.masteraccount = [];
                  $scope.filters.masteraccount.idMasteraccount = "0";
                  $scope.getAllieds();
                } else if (userRole == allied) {
                  $scope.filters.allied = [];
                  $scope.filters.allied.idAllied = "0";
                  $scope.getAccounts();
                }
              }
              $.fn.datetimepicker.defaults = {
                maskInput: false,
                pickDate: true,
                pickTime: true,
//                startDate: new Date()
              };
              $('#datetimepicker').datetimepicker({
                format: 'yyyy-MM-dd hh:mm:ss',
                language: 'es'
              });
              $('#datetimepicker2').datetimepicker({
                format: 'yyyy-MM-dd hh:mm:ss',
                language: 'es'
              });

              $scope.filters = [];
              $scope.filters.masteraccount = [];
              $scope.filters.allied = [];
              $scope.filters.account = [];
              $scope.filters.subaccount = [];
              $scope.getAll = function () {


                var data = {};
                data = {
                  string: $scope.filters.string,
                  inidate: document.getElementById("inidate").value,
                  findate: document.getElementById("findate").value,
                  idMasteraccount: $scope.filters.masteraccount.idMasteraccount,
                  idAllied: $scope.filters.allied.idAllied,
                  idAccount: $scope.filters.account.idAccount,
                  idSubaccount: $scope.filters.subaccount.idSubaccount
                };
                restService.getAll($scope.initial, data).then(function (data) {

                  $scope.history = data;
                });
              };

              $scope.initDisabled();
              $scope.getMasteraccounts = function () {
                restService.getMasteraccounts().then(function (data) {

                  $scope.masteraccounts = data;
                  $scope.allieds = [];
                  $scope.accounts = [];
                  $scope.subaccounts = [];
                });
              };
              
              $scope.getAllieds = function () {

                restService.getAllieds($scope.filters.masteraccount.idMasteraccount).then(function (data) {
                  $scope.allieds = [];
                  $scope.accounts = [];
                  $scope.subaccounts = [];
                  $scope.filters.allied = "";
                  $scope.filters.account = "";
                  $scope.filters.subaccount = "";
                  $scope.disabledAllied = true;
                  $scope.disabledAccount = true;
                  $scope.disabledSubaccount = true;

                  if (data.length != 0) {
                    $scope.allieds = data;
                    $scope.disabledAllied = false;
                  }
                });

              };

              $scope.getAccounts = function () {

                restService.getAccounts($scope.filters.allied.idAllied).then(function (data) {
                  $scope.accounts = [];
                  $scope.subaccounts = [];
                  $scope.filters.account = "";
                  $scope.filters.subaccount = "";
                  $scope.disabledAccount = true;
                  $scope.disabledSubaccount = true;
                  if (data.length != 0) {
                    $scope.accounts = data;
                    $scope.disabledAccount = false;
                  }
                });
              };
              
              $scope.getSubaccounts = function () {
                restService.getSubaccounts($scope.filters.account.idAccount).then(function (data) {
                  $scope.subaccounts = [];
                  $scope.filters.subaccount = "";
                  $scope.disabledSubaccount = true;
                  if (data.length != 0) {
                    $scope.subaccounts = data;
                    $scope.disabledSubaccount = false;
                  }
                });

              };

              $scope.cleanFilters = function () {
                $scope.filters.string = "";
                document.getElementById("inidate").value = "";
                document.getElementById("findate").value = "";
                $scope.allieds = [];
                $scope.accounts = [];
                $scope.subaccounts = [];
                $scope.filters.masteraccount = "";
                $scope.filters.allied = "";
                $scope.filters.account = "";
                $scope.filters.subaccount = "";
                $scope.initDisabled();

                $scope.getAll();
                $scope.getSelectFilters();
              }



              $scope.initial = 0;
              $scope.page = 1;

              $scope.forward = function () {
                $scope.initial += 1;
                $scope.page += 1;
                $scope.getAll();
              };
              $scope.fastforward = function () {
                $scope.initial = ($scope.history.total_pages - 1);
                $scope.page = $scope.history.total_pages;
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
              $scope.getSelectFilters();

              

            }])

})();
