(function() {
  angular
    .module("LandingPageTemplate.controllers", [])
    .filter("propsFilter", function() {
      return function(items, props) {
        var out = [];
        if (angular.isArray(items)) {
          var keys = Object.keys(props);
          items.forEach(function(item) {
            var itemMatches = false;
            for (var i = 0; i < keys.length; i++) {
              var prop = keys[i];
              var text = props[prop].toLowerCase();
              if (
                item[prop]
                  .toString()
                  .toLowerCase()
                  .indexOf(text) !== -1
              ) {
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
    .controller("listCtrl", [
      "$scope",
      "RestServices",
      "notificationService",
      function($scope, RestServices, notificationService) {
        //Initialize
        (function() {
          //Set Data
          $scope.data = {};
          $scope.data.filter = {};
          //Set misc
          $scope.misc = {
            loader: false
          };
          $scope.data.initial = 0;
          $scope.data.page = 1;
        })();

        //Set functions
        $scope.functions = {
          startDateOnSetTime: function() {
            $scope.$broadcast("start-date-changed");
          },
          endDateOnSetTime: function() {
            $scope.$broadcast("end-date-changed");
          },
          startDateBeforeRender: function($dates) {
            if ($scope.data.filter.dateEnd) {
              var activeDate = moment($scope.data.filter.dateEnd);

              $dates
                .filter(function(date) {
                  return date.localDateValue() >= activeDate.valueOf();
                })
                .forEach(function(date) {
                  date.selectable = false;
                });
            }
          },
          endDateBeforeRender: function($view, $dates) {
            if ($scope.data.filter.dateStart) {
              var activeDate = moment($scope.data.filter.dateStart)
                .subtract(1, $view)
                .add(1, "minute");

              $dates
                .filter(function(date) {
                  return date.localDateValue() <= activeDate.valueOf();
                })
                .forEach(function(date) {
                  date.selectable = false;
                });
            }
          },
          forward: function() {
            $scope.data.initial += 1;
            $scope.data.page += 1;
            $scope.misc.loader = true;
            $scope.functionsApi.getall();
          },
          fastforward: function() {
            $scope.data.initial = $scope.data.list.total_pages - 1;
            $scope.data.page = $scope.data.list.total_pages;
            $scope.misc.loader = true;
            $scope.functionsApi.getall();
          },
          backward: function() {
            $scope.data.initial -= 1;
            $scope.data.page -= 1;
            $scope.misc.loader = true;
            $scope.functionsApi.getall();
          },
          fastbackward: function() {
            $scope.data.initial = 0;
            $scope.data.page = 1;
            $scope.misc.loader = true;
            $scope.functionsApi.getall();
          },
          setData: function(data) {
            $scope.data.list = data;
            $scope.misc.loader = false;
          },
          filterName: function() {
            $scope.data.initial = 0;
            $scope.data.page = 1;
            $scope.functionsApi.getall();
            $scope.misc.loader = true;
          },
          setCategories: function(data) {
            $scope.data.categories = data;
          },
          filterCategory: function() {
            $scope.data.initial = 0;
            $scope.data.page = 1;
            $scope.functionsApi.getall();
            $scope.misc.loader = true;
          },
          filterDate: function() {
            $scope.data.initial = 0;
            $scope.data.page = 1;
            $scope.functionsApi.getall();
            $scope.misc.loader = true;
          }
        };

        $scope.functionsApi = {
          getall: function() {
            RestServices.getAll($scope.data.filter, $scope.data.initial)
              .then(function(response) {
                $scope.functions.setData(response);
              })
              .catch(function(error) {
                notificationService.error(error.message);
              });
          },
          getAllCategories: function() {
            RestServices.getAllCategories()
              .then(function(response) {
                $scope.functions.setCategories(response);
              })
              .catch(function(error) {
                notificationService.error(error.message);
              });
          }
        };

        $scope.functionsApi.getAllCategories();
        $scope.functionsApi.getall();
      }
    ])
    .controller("selectCtrl", [
      "$scope",
      "RestServices",
      "notificationService",
      "$state",
      "$stateParams",
      function($scope, RestServices, notificationService, $state, $stateParams) {
        //Initialize
        (function() {
          //Set Data
          $scope.data = {};
          $scope.data.filter = {};
          $scope.data.params = {};
          //Set misc
          $scope.misc = {
            loader: false
          };
          $scope.data.initial = 0;
          $scope.data.page = 1;

          $scope.data.params.idLandingPage = $stateParams.idLandingPage;
        })();

        //Set functions
        $scope.functions = {
          startDateOnSetTime: function() {
            $scope.$broadcast("start-date-changed");
          },
          endDateOnSetTime: function() {
            $scope.$broadcast("end-date-changed");
          },
          startDateBeforeRender: function($dates) {
            if ($scope.data.filter.dateEnd) {
              var activeDate = moment($scope.data.filter.dateEnd);

              $dates
                .filter(function(date) {
                  return date.localDateValue() >= activeDate.valueOf();
                })
                .forEach(function(date) {
                  date.selectable = false;
                });
            }
          },
          endDateBeforeRender: function($view, $dates) {
            if ($scope.data.filter.dateStart) {
              var activeDate = moment($scope.data.filter.dateStart)
                .subtract(1, $view)
                .add(1, "minute");

              $dates
                .filter(function(date) {
                  return date.localDateValue() <= activeDate.valueOf();
                })
                .forEach(function(date) {
                  date.selectable = false;
                });
            }
          },
          refresh: function () {
            $state.reload();
          },
          forward: function() {
            $scope.data.initial += 1;
            $scope.data.page += 1;
            $scope.misc.loader = true;
            $scope.functionsApi.getall();
          },
          fastforward: function() {
            $scope.data.initial = $scope.data.list.total_pages - 1;
            $scope.data.page = $scope.data.list.total_pages;
            $scope.misc.loader = true;
            $scope.functionsApi.getall();
          },
          backward: function() {
            $scope.data.initial -= 1;
            $scope.data.page -= 1;
            $scope.misc.loader = true;
            $scope.functionsApi.getall();
          },
          fastbackward: function() {
            $scope.data.initial = 0;
            $scope.data.page = 1;
            $scope.misc.loader = true;
            $scope.functionsApi.getall();
          },
          setData: function(data) {
            $scope.data.list = data;
            $scope.misc.loader = false;
          },
          filterName: function() {
            $scope.data.initial = 0;
            $scope.data.page = 1;
            $scope.functionsApi.getall();
            $scope.misc.loader = true;
          },
          setCategories: function(data) {
            $scope.data.categories = data;
          },
          filterCategory: function() {
            $scope.data.initial = 0;
            $scope.data.page = 1;
            $scope.functionsApi.getall();
            $scope.misc.loader = true;
          },
          filterDate: function() {
            $scope.data.initial = 0;
            $scope.data.page = 1;
            $scope.functionsApi.getall();
            $scope.misc.loader = true;
          }
        };

        $scope.functionsApi = {
          getall: function() {
            RestServices.getAll($scope.data.filter, $scope.data.initial)
              .then(function(response) {
                $scope.functions.setData(response);
              })
              .catch(function(error) {
                notificationService.error(error.message);
              });
          },
          getAllCategories: function() {
            RestServices.getAllCategories()
              .then(function(response) {
                $scope.functions.setCategories(response);
              })
              .catch(function(error) {
                notificationService.error(error.message);
              });
          }
        };

        $scope.functionsApi.getAllCategories();
        $scope.functionsApi.getall();
      }
    ]);
})();