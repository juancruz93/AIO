(function () {
  angular.module("smssendingrule.controllers", [])
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
    .filter("capitalize", function () {
      return function (text) {
        if (text != null) {
          return text.substring(0, 1).toUpperCase() + text.substring(1);
        }
      };
    })
    .filter("implode", function () {
      return function (array, param) {
        if (angular.isArray(array)) {
          var string = "";
          array.forEach(function (item, index) {
            if ((index + 1) !== array.length) {
              string += item + param + " ";
            } else {
              string += item;
            }
          });
          return string;
        }
      };
    })
    .controller("listController", [
      "$scope",
      "$state",
      "restServices",
      "notificationService",
      function ($scope, $state, restServices, notificationService) {
        $scope.initial = 0;
        $scope.page = 1;
        $scope.forward = function () {
          $scope.initial += 1;
          $scope.page += 1;
          $scope.listsmssendingrule();
        };
        $scope.fastforward = function () {
          $scope.initial = ($scope.list.total_pages - 1);
          $scope.page = $scope.list.total_pages;
          $scope.listsmssendingrule();
        };
        $scope.backward = function () {
          $scope.initial -= 1;
          $scope.page -= 1;
          $scope.listsmssendingrule();
        };
        $scope.fastbackward = function () {
          $scope.initial = 0;
          $scope.page = 1;
          $scope.listsmssendingrule();
        };
        $scope.listsmssendingrule = function () {
          restServices.list($scope.initial, "").then(function (data) {
            $scope.list = data;
          }).catch(function (data) {
            notificationService.error(data.message);
          });
        };
        $scope.listsmssendingrule();
        $scope.searchForName = function () {
          restServices.list($scope.initial, $scope.filterName).then(function (data) {
            $scope.list = data;
          }).catch(function (data) {
            notificationService.error(data.message);
          });
        };

        $scope.id = null;
        $scope.confirmDelete = function (id) {
          $scope.id = id;
          openModal();
        };

        $scope.deleteSmstemplate = function () {
          restServices.delete($scope.id).then(function (data) {
            $scope.listsmssendingrule();
            closeModal();
            notificationService.warning(data.message);
          }).catch(function (data) {
            notificationService.error(data.message);
          });
        };
      }])
    .controller("createController", [
      "$scope",
      "$state",
      "restServices",
      "notificationService",
      function ($scope, $state, restServices, notificationService) {
        $scope.data = {};
        $scope.data.status = true;
        $scope.forms = [{ adapter: "", byDefault: true, prefix: [], prefixDisabled: true }];
        restServices.listindicative().then(function (data) {
          $scope.listindicative = data;
        }).catch(function (data) {
          notificationService.error(data.message);
        });
        restServices.adapter().then(function (data) {
          $scope.listadapter = data;
        }).catch(function (data) {
          notificationService.error(data.message);
        });

        $scope.create = function () {
          $scope.verifyDefault();
          $scope.data.forms = $scope.forms;
          restServices.create($scope.data).then(function (data) {
            notificationService.success(data.message);
            $state.go("index");
          }).catch(function (data) {
            notificationService.error(data.message);
          });
        };

        $scope.addForm = function () {
          if ($scope.forms.length == 20) {
            notificationService.warning("Solo puede agregar 20 reglas");
            return false;
          }
          $scope.forms.push({ idAdapter: "", byDefault: false, prefix: [], prefixDisabled: false });
        };

        $scope.removeForm = function (index) {
          if ($scope.forms.length === 1) {
            notificationService.warning("No puede eliminar esta configuración, debe haber al menos una");
            return false;
          }
          $scope.forms.splice(index, 1);
        };

        $scope.switchDefault = function (ind) {
          $scope.forms.forEach(function (item, index) {
            if (ind === index) {
              item.byDefault = true;
              item.prefixDisabled = true;
              item.prefix = [];
            } else {
              item.byDefault = false;
              item.prefixDisabled = false;
            }
          });
        };

        $scope.verifyDefault = function () {
          var df = 0;
          $scope.forms.forEach(function (item, index) {
            if (item.byDefault === true) {
              df++;
            }
          });
          if (df === 0) {
            $scope.forms[0].byDefault = true;
          }
        };
      }
    ])
    .controller("showController", [
      "$scope",
      "$state",
      "$stateParams",
      "restServices",
      "notificationService",
      function ($scope, $state, $stateParams, restServices, notificationService) {
        restServices.show($stateParams.id).then(function (data) {
          $scope.data = data;
        }).catch(function (data) {
          notificationService.error(data.message);
          $state.go("index");
        });
      }
    ])
    .controller("editController", [
      "$scope",
      "$state",
      "$stateParams",
      "restServices",
      "notificationService",
      function ($scope, $state, $stateParams, restServices, notificationService) {
        restServices.listindicative().then(function (data) {
          $scope.listindicative = data;
        }).catch(function (data) {
          notificationService.error(data.message);
        });
        restServices.adapter().then(function (data) {
          $scope.listadapter = data;
        }).catch(function (data) {
          notificationService.error(data.message);
        });

        restServices.show($stateParams.id).then(function (data) {
          $scope.data = data;
          $scope.forms = $scope.data.config;
          $scope.data.status = (parseInt(data.status) == 1);
        });

        $scope.addForm = function () {
          if ($scope.forms.length == 20) {
            notificationService.warning("Solo puede agregar 20 reglas");
            return false;
          }
          $scope.forms.push({adapter: "", byDefault: false, prefix: [], prefixDisabled: false });
        };

        $scope.removeForm = function (index) {
          if ($scope.forms.length === 1) {
            notificationService.warning("No puede eliminar esta configuración, debe haber al menos una");
            return false;
          }
          $scope.forms.splice(index, 1);
        };

        $scope.switchDefault = function (ind) {
          $scope.forms.forEach(function (item, index) {
            if (ind === index) {
              item.byDefault = true;
              item.prefixDisabled = true;
              item.prefix = [];
            } else {
              item.byDefault = false;
              item.prefixDisabled = false;
              item.prefix = [];
            }
          });
        };

        $scope.verifyDefault = function () {
          var df = 0;
          $scope.forms.forEach(function (item, index) {
            if (item.byDefault === true) {
              df++;
            }
          });
          if (df === 0) {
            $scope.forms[0].byDefault = true;
          }
        };

        $scope.edit = function () {
          $scope.verifyDefault();
          $scope.data.forms = $scope.forms;
          restServices.edit($scope.data).then(function (data) {
            notificationService.info(data.message);
            $state.go("index");
          }).catch(function (data) {
            notificationService.error(data.message);
          });
        };
      }
    ]);
})();
