/* 
 * Moduio controllers
 */

angular.module('LandingPageCategoryApp.controllers', [])
        .controller('listCtrl', ['$scope', 'restServices', '$window', 'toastr', 'constantPageCategory', function ($scope, restServices, $window, $toastr, constantPageCategory) {
            //set data
            $scope.data = {};
            $scope.data.initial = 0;
            $scope.data.page = 1;

            $scope.data.filter = {};
            //Set misc
            $scope.misc = {};
            //Set functions universal
            $scope.functions = {
              deleteCategory: function (index) {
                $scope.misc.list.items.splice(index, 1);
              },
              setList: function (data) {
                $scope.misc.list = data;
              },
              setMethodMisc: function (item, value) {
                $scope.misc[item] = value;
              },
              redirect: function (url) {
                var route = $window.myBaseURL + url;
                $window.location.href = route;
              },
              confirmDelete: function (index) {
                $scope.data.categorySelected = $scope.misc.list.items[index];
                $scope.data.categorySelected.index = index;
                this.modals.show(constantPageCategory.Modals.delete);
              },
              Filter: {
                name: function () {
                  if ($scope.data.filter.name.length > constantPageCategory.Filter.minChar) {
                    $scope.restServices.getAll();
                  } else if ($scope.data.filter.name.length == 0) {
                    $scope.restServices.getAll();
                  }
                },
              },
              Pagination: {
                forward: function () {
                  $scope.data.initial += 1;
                  $scope.data.page += 1;
                  $scope.restServices.getAll();
                },
                fastforward: function () {
                  $scope.data.initial = ($scope.misc.list.total_pages - 1);
                  $scope.data.page = $scope.misc.list.total_pages;
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
                }
              },
              Modals: {
                show: function (id) {
                  $('#' + id).addClass('dialog--open');
                },
                hide: function (id) {
                  $('#' + id).removeClass('dialog--open');
                }
              },
            };
            //set functions api
            $scope.restServices = {
              getAll: function () {
                restServices.getAllCategory($scope.data.initial, $scope.data)
                        .then(function (resolve) {
                          $scope.functions.setList(resolve.data);
                        })
                        .catch(function (reject) {
                          $toastr.error(reject.data.message, constantPageCategory.Message.error);
                        })
              },
              deleteCategory: function (idCategory) {
                restServices.deleteCategory(idCategory)
                        .then(function (resolve) {
                          $toastr.info(resolve.data.message);
                          $scope.functions.deleteCategory($scope.data.categorySelected.index);
                          $scope.functions.Modals.hide(constantPageCategory.Modals.delete);
                        })
                        .catch(function (reject) {
                          $toastr.error(reject.data.message, constantPageCategory.Message.error);
                        })
              }
            };
            $scope.$watch('[data.filter.dateinitial,data.filter.dateend]', function () {
              if (angular.isDefined($scope.data.filter.dateinitial) && angular.isDefined($scope.data.filter.dateend)) {
                $scope.restServices.getAll();
              }
            });

            $scope.restServices.getAll();
          }])
        .controller('Ctrl', ['$scope', 'restServices', '$state', 'notificationService', '$stateParams', 'constantPageCategory', 'toastr', function ($scope, restServices, $state, notificationService, $stateParams, constantPageCategory, $toastr) {
            //Set data
            $scope.data = {};
            $scope.data.status = true;
            //Set misc
            $scope.misc = {};
            //Set functions universal
            $scope.functions = {
              setData: function (data) {
                angular.forEach(data, function (value, key) {
                  $scope.data[key] = value;
                });
                console.log($scope.data);
              },
              validate: function () {
                if (angular.isDefined($scope.data.idLandingPageCategory)) {
                  $scope.functionsApi.editCategory();
                } else {
                  $scope.functionsApi.createCategory();
                }
              },
              setKeydata: function (key, value) {
                $scope.data[key] = value;
              },

            };
            //set functions api
            $scope.functionsApi = {
              createCategory: function () {
                restServices.createCategory($scope.data).then(function (data) {
                  $state.go(constantPageCategory.State.list.state);
                  $toastr.success(data.data.message, constantPageCategory.Message.create)
                }).catch(function (error) {
                  $toastr.error(error.data.message, constantPageCategory.Message.error);
                });
              },
              getOne: function () {
                restServices.oneCategory($scope.data.idLandingPageCategory).then(function (data) {
                  $scope.functions.setData(data.data);
                }).catch(function (error) {
                  $toastr.error(error.data.message, constantPageCategory.Message.error);
                });
              },
              editCategory: function () {
                restServices.editCategory($scope.data).then(function (data) {
                  $state.go(constantPageCategory.State.list.state);
                  $toastr.info(data.data.success, constantPageCategory.Message.create)
                }).catch(function (error) {
                  $toastr.error(error.data.message, constantPageCategory.Message.error);
                });
              },
            };

            if (angular.isDefined($stateParams.idLandingPageCategory) && !jQuery.isEmptyObject($stateParams.idLandingPageCategory)) {
              $scope.functions.setKeydata('idLandingPageCategory', $stateParams.idLandingPageCategory);
              $scope.functionsApi.getOne();
            }
          }])

