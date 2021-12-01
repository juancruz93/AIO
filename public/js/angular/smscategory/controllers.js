angular.module('smscategory.controllers', [])
        .controller('listController', ['$scope','RestServices','notificationService', '$state', function ($scope, RestServices, notificationService, $state) {
            
            $scope.functions = {
              search: function () {
                $scope.restServices.getAll();
              },
              refresh: function () {
                $state.reload();
              },
              forward: function () {
                $scope.data.initial += 1;
                $scope.data.page += 1;
                $scope.restServices.getAll();
              },
              fastforward: function () {
                $scope.data.initial = ($scope.data.smscategory.total_pages - 1);
                $scope.data.page = $scope.data.smscategory.total_pages;
                $scope.restServices.getAll();
              },
              backward: function () {
                $scope.data.initial -= 1;
                $scope.data.page -= 1;
                $scope.restServices.getAll();
              },
              fastbackward: function () {
                $scope.data.initial = 0;
                $scope.data.page = 1;
                $scope.restServices.getAll();
              },
              openModal: function (idSms) {
                $scope.data.idSms = idSms;
                $('#deleteDialog').addClass('dialog--open');
              },
              closeModal: function () {
                $('.dialog').removeClass('dialog--open');
              },
              initializeVariable: function () {
                //Universal Data
                $scope.data = {};

                $scope.data.initial = 0;
                $scope.data.page = 1;
                $scope.data.filter = "";

                $scope.data.smscategory = [{}];
              },
              initialFunctions: function() {
                $scope.functions.initializeVariable();
                $scope.restServices.getAll();
              },
              setMethodData : function(item,data){
                $scope.data[item] = data;
              }
            }
            $scope.restServices = {
              getAll: function () {
                RestServices.getAll($scope.data.initial, $scope.data.filter).then(function (data) {
                  $scope.functions.setMethodData('smscategory',data);
                });
              },
              deleteCategory: function () {
                RestServices.deleteCategory({idSmsCategory: $scope.data.idSms}).then(function (data) {
                  notificationService.warning(data.message);
                  $scope.functions.setMethodData('initial',0);
                  $scope.functions.setMethodData('page',1);
                  $scope.restServices.getAll();
                  $scope.functions.closeModal();
                  $window.location.href = '#/';
                });
              },
            }
            $scope.functions.initialFunctions();
            $scope.$watch('[data.filter.dateinitial,data.filter.dateend]', function () {
              if (typeof $scope.data.filter.dateinitial != 'undefined' & typeof $scope.data.filter.dateend != 'undefined') {
                $scope.restServices.getAll();
              }
            });
            
          }])
        