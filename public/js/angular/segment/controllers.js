(function () {
  angular.module('segment.controllers', [])
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
    .filter("implode", function () {
      return function (array, param) {
        if (angular.isArray(array)) {
          var string = "";
          array.forEach(function (item, index) {
            if ((index + 1) !== array.length) {
              string += item.name + param + " ";
            } else {
              string += item.name;
            }
          });
          return string;
        }
      };
    })
    .controller('SegmentController', ['$scope', 'restService', 'notificationService', function ($scope, restService, notificationService) {
        $scope.stringsearch = -1;
        $scope.initial = 0;
        $scope.page = 1;
        $scope.loaderList = false;
        $scope.getAll = function () {
          restService.getAllSegment($scope.initial, $scope.stringsearch).then(function (data) {
            $scope.segment = data;
          });
        };

        $scope.forward = function () {
          $scope.initial += 1;
          $scope.page += 1;
          $scope.getAll();
        };
        $scope.fastforward = function () {
          $scope.initial = ($scope.segment.total_pages - 1);
          $scope.page = $scope.segment.total_pages;
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
        $scope.searchcontacts = function () {
          $scope.stringsearch = $scope.search;
          $scope.getAll();
        };
        $scope.getAll();
        $scope.confirmDelete = function (idSegment) {
          $scope.idSegment = idSegment;
          openModal();
        };
        $scope.deleteSegment = function () {
          $scope.loaderList = true;
          restService.deleteSegment($scope.idSegment).then(function (data) {
            $scope.getAll();
            $scope.loaderList = false;
            notificationService.warning(data.message);
          }).catch(function (error) {
            notificationService.error(error.message);
          });
          closeModal();
        };
      }])
    .controller('NewsegmentController', ['$scope', 'restService', 'notificationService', '$window', function ($scope, restService, notificationService, $window) {
        $scope.progressbar = true;
        $scope.disbledBtnSave = false;
        $scope.conditionsNumber = ["Es igual a", "Contiene", "No contiene", "Empieza con", "Termina en", "Mayor a", "Menor a"];
        $scope.conditions = ["Es igual a", "Contiene", "No contiene", "Empieza con", "Termina en"];
        $scope.viewFilters = false;
        $scope.segment = {};
        $scope.filters = [];

        restService.getAllContactlistBySubaccount().then(function (data) {
          $scope.contactlist = data;
        });

        $scope.addFilter = function () {
          $scope.filters.push({});
        };
        $scope.popup1 = {
          opened: false
        };
        $scope.opendatepicker = function (index) {
          $scope.popup.opened = true;
        };
        $scope.contactlistSelected = function () {
          restService.getAllCustomField($scope.segment.contactlist).then(function (data) {
            if ($scope.filters.length == 0 && data.length > 0) {
              $scope.filters.push({});
              $scope.viewFilters = true;
            }
            if (data.length == 0) {
              notificationService.error("Esta lista no contiene campos personalizados");
            }
            $scope.customfield = data;
          });
        }
        $scope.deleteFilter = function (index) {
          $scope.filters.splice(index, 1);
        }
        $scope.functions = {
          confirmSave: function () {
            if (!$scope.segment.name || !$scope.segment.contactlist || !$scope.segment.conditions) {
              notificationService.error("Los campos marcados con asterisco(*) son oblogatorios");
            } else {
              openModal();
            }
          },
          addSegment: function () {
            document.getElementById("btn-ok").setAttribute("disabled", true);
            $scope.progressbar = false;
            restService.addSegment($scope.segment, $scope.filters).then(function (data) {
              $scope.progressbar = true;
              document.getElementById("btn-ok").removeAttribute("disabled");
              $window.location.href = '#/';
              notificationService.success(data.message);
            }).catch(function (data) {
              $scope.progressbar = true;
              notificationService.error(data.message);
            });
          }
        };



      }])
    .controller('EditsegmentController', ['$scope', '$routeParams', 'restService', 'notificationService', '$window', '$interval', function ($scope, $routeParams, restService, notificationService, $window, $interval) {
        $scope.disbledBtnSave = false;
        $scope.progressbar = true;
        $scope.conditionsNumber = ["Es igual a", "Contiene", "No contiene", "Empieza con", "Termina en", "Mayor a", "Menor a"];
        $scope.conditions = ["Es igual a", "Contiene", "No contiene", "Empieza con", "Termina en"];
        $scope.viewFilters = false;
        $scope.filters = [];
        $scope.contactlist = [];
        $scope.customfield = [];

        $scope.initItemsEdit = function (item) {
          if (item.type == "Select" || item.type == "Multiselect") {
            restService.findCustomfield(item.idCustomfield).then(function (data) {
              item.value2 = data.value;
            });
          }
          item.con = $scope.conditions;
          if (item.type == "Numerical") {
            item.con = $scope.conditionsNumber
          }
//                $scope.valueItemEdit(item);+
        };

        var id = $routeParams.id;
        $scope.contactlistSelected = function () {
          restService.getAllCustomField($scope.segment.contactlist).then(function (data) {
            if ($scope.filters.length == 0 && data.length > 0) {
              $scope.filters.push({});
              $scope.viewFilters = true;
            }
            if (data.length == 0) {
              notificationService.error("Esta lista no contiene campos personalizados");
            }

            $scope.customfield = data;
          });
        }
        $scope.init = function () {
          restService.findSegment(id).then(function (data) {
            $scope.segment = data[0];
            $scope.contactlistSelected();
          });
          restService.getAllContactlistBySubaccount().then(function (data) {
            for (var j = 0; j < data.length; j++) {
              $scope.contactlist[j] = {idContactlist: data[j].idContactlist, name: data[j].name};
            }
          });
        }

        $scope.deleteFilter = function (index) {
          $scope.segment.filters.splice(index, 1);
        }

        $scope.addFilter = function () {
          $scope.segment.filters.push({});
        };

        $scope.valueItemEdit = function (item) {
          if ($scope.isNumeric(item.idCustomfield)) {
            restService.findCustomfield(item.idCustomfield).then(function (data) {
              if (data.value) {
                item.value2 = data.value;
              }
              item.type = data.type;
            });
          } else {
            $scope.typeInput(item);
          }

        };

        $scope.isNumeric = function (n) {
          return !isNaN(parseFloat(n)) && isFinite(n);
        };
        $scope.typeInput = function (item) {
          switch (item.idCustomfield) {
            case "name":
              item.type = "Text";
              break;
            case "email":
              item.type = "Text";
              break;
            case "lastname":
              item.type = "Text";
              break;
            case "birthdate":
              item.type = "Date";
              break;
            case "phone":
              item.type = "Numerical";
              break;
          }
        }

        $scope.functions = {
          confirmEdit: function () {
            if (!$scope.segment.name || !$scope.segment.contactlist || !$scope.segment.conditions) {
              notificationService.error("Los campos marcados con asterisco(*) son oblogatorios");
            } else {
              openModal();
            }
          },
          editSegment: function () {
            $scope.progressbar = false;
            restService.editSegment($scope.segment).then(function (data) {
              notificationService.error(data.message);
              $scope.progressbar = false;
              $window.location.href = '#/';
            }).catch(function (data) {
              $scope.progressbar = true;
              notificationService.error(data.message);
            });
          }
        };

        $scope.init();
      }]);

})();
