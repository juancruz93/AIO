//
// Flowchart module.
//
var angularFlowchart = angular.module('flowChart', ['dragging', 'angularFileUpload']);

//
// Directive that generates the rendered chart from the data model.
//
angularFlowchart.directive('flowChart', function () {
  return {
    restrict: 'E',
    templateUrl: fullUrlBase + "flowchart/index",
    replace: true,
    scope: {
      chart: "=chart",
    },
    //
    // Controller for the flowchart directive.
    // Having a separate controller is better for unit testing, otherwise
    // it is painful to unit test a directive without instantiating the DOM 
    // (which is possible, just not ideal).
    //
    controller: 'FlowChartController',
  };
});

//
// Directive that generates the rendered chart from the data model demo.
//
angularFlowchart.directive('flowChartStatistics', function () {
  return {
    restrict: 'E',
    templateUrl: fullUrlBase + "flowchart/statictis",
    replace: true,
    scope: {
      chart: "=chart",
    },
    //
    // Controller for the flowchart directive.
    // Having a separate controller is better for unit testing, otherwise
    // it is painful to unit test a directive without instantiating the DOM 
    // (which is possible, just not ideal).
    //
    controller: 'FlowChartstatisticsController',
  };
});

//
// Directive that allows the chart to be edited as json in a textarea.
//
angularFlowchart.directive('chartJsonEdit', function () {
  return {
    restrict: 'A',
    scope: {
      viewModel: "="
    },
    link: function (scope, elem, attr) {

      //
      // Serialize the data model as json and update the textarea.
      //
      var updateJson = function () {
        if (scope.viewModel) {
          var json = JSON.stringify(scope.viewModel.data, null, 4);
          $(elem).val(json);
        }
      };

      //
      // First up, set the initial value of the textarea.
      //
      updateJson();

      //
      // Watch for changes in the data model and update the textarea whenever necessary.
      //
      scope.$watch("viewModel.data", updateJson, true);

      //
      // Handle the change event from the textarea and update the data model
      // from the modified json.
      //
      $(elem).bind("input propertychange", function () {
        var json = $(elem).val();
        var dataModel = JSON.parse(json);
        scope.viewModel = new flowchart.ChartViewModel(dataModel);

        scope.$digest();
      });
    }
  }

});
angularFlowchart.directive('svgPopover', ['$templateCache', '$compile', '$http', 'flowchartService', function ($templateCache, $compile, $http, flowchartService) {
    return {
      scope: true,
      restrict: "A",
      scope: {
        data: "=ngModel",
        idNode: "=idNode",
        connections: "=ngConnections",
      },
      link: function (scope, element, attrs) {
        scope.initElement(element);
      },
      controller: 'FlowChartCustomController'
    }
  }]);

angularFlowchart.filter('propsFilter', function () {
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
});

angularFlowchart.directive("contextMenu", function ($compile) {
  contextMenu = {};
  contextMenu.restrict = "AE";
  contextMenu.scope = {
    connections: "=ngConnections",
  };
  contextMenu.link = function (scope, elemt, attr) {

    scope.initElementConector(elemt);
    elemt.on("contextmenu", function (e) {
      e.preventDefault();
      scope.showPopover();
    });

  };
  contextMenu.controller = "FlowChartConexionController";
  return contextMenu;
});

angularFlowchart.directive("optionsNode", function ($compile) {
  contextMenu = {};
  contextMenu.restrict = "AE";
  contextMenu.scope = {
    nodeSelected: "=ngNode",
  };

  contextMenu.link = function (scope, elemt, attr) {
    scope.initElementConector(elemt);
    elemt.on("contextmenu", function (e) {
      e.preventDefault();
      scope.showPopover(elemt);
    });


  };
  contextMenu.controller = "FlowChartNodeController";
  return contextMenu;
});

angularFlowchart.directive("tooltipStatitics", ['$compile', '$http', function ($compile, $http) {
    tooltipStatitics = {};
    tooltipStatitics.restrict = "A";
    tooltipStatitics.scope = {
      nodeTooltip: '=tooltipStatitics'
    };

    tooltipStatitics.link = function (scope, elemt, attrs) {
      scope.elemnt = elemt;
      scope.setContentPopover(scope.nodeTooltip.getStatictitisNode(), scope.nodeTooltip.getId());
//      console.log(scope.nodeTooltip.getStatictitisNode());
      scope.elemnt.bind('click', function () {
        
        if (scope.boolShowPopover) {
          $(scope.elemnt).popover('hide');
          scope.boolShowPopover = false;
        } else {
          $(scope.elemnt).popover('show');
          scope.boolShowPopover = true;
        }
      });
    };

    tooltipStatitics.controller = 'FlowChartstatisticsController';
    return tooltipStatitics;
  }]);
