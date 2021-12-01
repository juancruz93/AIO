angular.module('mailcategory.controllers',[])
        .controller('listController',['$scope','RestServices','notificationService','$state',function($scope,RestServices,notificationService,$state){
            $scope.functions = {
                search: function(){
                  $scope.restServices.getAll();
                },
                refresh: function() {
                  $state.reload();
                },
                forward: function(){
                  $scope.data.initial += 1;
                  $scope.data.page += 1;
                  $scope.restServices.getAll();
                },
                fastforward: function(){
                  $scope.data.initial = ($scope.data.mailcategory.total_pages - 1);
                  $scope.data.page = $scope.data.mailcategory.total_pages;
                  $scope.restServices.getAll();
                },
                backward: function(){
                  $scope.data.initial -= 1;
                  $scope.data.page -= 1;
                  $scope.restServices.getAll();
                },
                fastbackward: function(){
                  $scope.data.initial = 0;
                  $scope.data.page = 1;
                  $scope.restServices.getAll();
                },
                openModal: function (idMail) {
                  $scope.data.mailcategory.idMailCategory = idMail;
                  //$scope.functions.setMethodData("mailcategory.idMailCategory", idMail);
                  $('.dialog').addClass('dialog--open');
                },
                closeModal: function () {
                  $('.dialog').removeClass('dialog--open');
                },
                initializeVars: function() {
                  //Universal Data
                  $scope.data = {};

                  $scope.data.initial = 0;
                  $scope.data.page = 1;
                  $scope.data.filter = "";

                  $scope.data.mailcategory = {};
                },
                initialFunctions: function() {
                  $scope.functions.initializeVars();
                  $scope.restServices.getAll();
                },
                setMethodData : function(item,data){
                $scope.data[item] = data;
              }
            } 
            $scope.restServices = {                  
              getAll: function(){
                RestServices.getAll($scope.data.initial, $scope.data.filter).then(function (data) {
                    $scope.functions.setMethodData('mailcategory',data);
                });
              },
              deleteCategory: function(){
                  RestServices.deleteCategory({idMailCategory: $scope.data.mailcategory.idMailCategory}).then(function (data) {
                    notificationService.warning(data.message);
                    $scope.functions.setMethodData('initial',0);
                    $scope.functions.setMethodData('page',1);
                    $scope.restServices.getAll();
                    $scope.functions.closeModal();
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
        .controller('addmailcategoryController',['$scope','RestServices','notificationService','$window',function($scope,RestServices,notificationService,$window){
              $scope.saveData = {};
              $scope.saveData.status = true;
      
      
              $scope.saveCategory = function(){
                RestServices.saveCategory($scope.saveData).then(function(data){
                  notificationService.success(data.message);
                  $window.location.href = '#/';
                });
              }
          }])
        .controller('editmailcategoryController',['$scope','RestServices','notificationService','$window','$stateParams',function($scope,RestServices,notificationService,$window,$stateParams){
              $scope.saveData = {};
              $scope.mailCategory = {};
              
              $scope.getMailCategory = function(){
                RestServices.getOneMailCategory($stateParams.idMail).then(function(data){
                  $scope.saveData = data[0];
                  $scope.mailCategory = angular.copy($scope.saveData);
                  if ($scope.saveData.status==1){
                    $scope.saveData.status = true;
                  }else{
                    $scope.saveData.status = false;
                  }
                });
              }
      
              $scope.editCategory = function(){
                RestServices.editCategory($scope.saveData).then(function(data){
                  notificationService.info(data.message);
                  $window.location.href = '#/';
                });
              }
              $scope.getMailCategory();
            
          }]);