
angularFlowchart.controller('FlowChartController', ['$scope', 'dragging', '$element', '$rootScope', '$templateCache', '$popover', 'flowchartDataModal', function FlowChartController($scope, dragging, $element, $rootScope, $templateCache, $popover, flowchartDataModal) {

    var controller = this;
    var chart = null;
    var idNode = null;
    var clicks = null;
    var idLink = null;
    $rootScope.chart = $scope.chart;
    //
    // Reference to the document and jQuery, can be overridden for testting.
    //
    this.document = document;
    //
    // Wrap jQuery so it can easily be  mocked for testing.
    //
    this.jQuery = function (element) {
      return $(element);
    }

//
// Init data-model variables.
//
    $scope.draggingConnection = false;
    $scope.connectorSize = 5;
    $scope.dragSelecting = false;
    /* Can use this to test the drag selection rect.
     $scope.dragSelectionRect = {
     x: 0,
     y: 0,
     width: 0,
     height: 0,
     };
     */

    //
    // Reference to the connection, connector or node that the mouse is currently over.
    //
    $scope.mouseOverConnector = null;
    $scope.mouseOverConnection = null;
    $scope.mouseOverNode = null;
    //
    // The class for connections and connectors.
    //
    this.connectionClass = 'connection';
    this.connectorClass = 'connector';
    this.nodeClass = 'node';
    //
    // Search up the HTML element tree for an element the requested class.
    //
    this.searchUp = function (element, parentClass) {

      //
      // Reached the root.
      //
      if (element == null || element.length == 0) {
        return null;
      }

      // 
      // Check if the element has the class that identifies it as a connector.
      //
      if (hasClassSVG(element, parentClass)) {
        //
        // Found the connector element.
        //
        return element;
      }

      //
      // Recursively search parent elements.
      //
      return this.searchUp(element.parent(), parentClass);
    };
    //
    // Hit test and retreive node and connector that was hit at the specified coordinates.
    //
    this.hitTest = function (clientX, clientY) {

      //
      // Retreive the element the mouse is currently over.
      //
      return this.document.elementFromPoint(clientX, clientY);
    };
    //
    // Hit test and retreive node and connector that was hit at the specified coordinates.
    //
    this.checkForHit = function (mouseOverElement, whichClass) {

      //
      // Find the parent element, if any, that is a connector.
      //
      var hoverElement = this.searchUp(this.jQuery(mouseOverElement), whichClass);
      if (!hoverElement) {
        return null;
      }

      return hoverElement.scope();
    };
    //
    // Translate the coordinates so they are relative to the svg element.
    //
    this.translateCoordinates = function (x, y, evt) {
      var svg_elem = $element.get(0);
      var matrix = svg_elem.getScreenCTM();
      var point = svg_elem.createSVGPoint();
      point.x = x - evt.view.pageXOffset;
      point.y = y - evt.view.pageYOffset;
      return point.matrixTransform(matrix.inverse());
    };
    //
    // Called on mouse down in the chart.
    //
    $scope.mouseDown = function (evt) {

//            $scope.chart.deselectAll();
//
//            dragging.startDrag(evt, {
//                //
//                // Commence dragging... setup variables to display the drag selection rect.
//                //
//                dragStarted: function (x, y) {
//                    $scope.dragSelecting = true;
//                    var startPoint = controller.translateCoordinates(x, y, evt);
//                    $scope.dragSelectionStartPoint = startPoint;
//                    $scope.dragSelectionRect = {
//                        x: startPoint.x,
//                        y: startPoint.y,
//                        width: 0,
//                        height: 0,
//                    };
//                },
//                //
//                // Update the drag selection rect while dragging continues.
//                //
//                dragging: function (x, y) {
//                    var startPoint = $scope.dragSelectionStartPoint;
//                    var curPoint = controller.translateCoordinates(x, y, evt);
//
//                    $scope.dragSelectionRect = {
//                        x: curPoint.x > startPoint.x ? startPoint.x : curPoint.x,
//                        y: curPoint.y > startPoint.y ? startPoint.y : curPoint.y,
//                        width: curPoint.x > startPoint.x ? curPoint.x - startPoint.x : startPoint.x - curPoint.x,
//                        height: curPoint.y > startPoint.y ? curPoint.y - startPoint.y : startPoint.y - curPoint.y,
//                    };
//                },
//                //
//                // Dragging has ended... select all that are within the drag selection rect.
//                //
//                dragEnded: function () {
//                    $scope.dragSelecting = false;
//                    $scope.chart.applySelectionRect($scope.dragSelectionRect);
//                    delete $scope.dragSelectionStartPoint;
//                    delete $scope.dragSelectionRect;
//                },
//            });
    };
    //
    // Called for each mouse move on the svg element.
    //
    $scope.mouseMove = function (evt) {

      //
      // Clear out all cached mouse over elements.
      //
      $scope.mouseOverConnection = null;
      $scope.mouseOverConnector = null;
      $scope.mouseOverNode = null;
      var mouseOverElement = controller.hitTest(evt.clientX, evt.clientY);
      if (mouseOverElement == null) {
        // Mouse isn't over anything, just clear all.
        return;
      }

      if (!$scope.draggingConnection) { // Only allow 'connection mouse over' when not dragging out a connection.

        // Figure out if the mouse is over a connection.
        var scope = controller.checkForHit(mouseOverElement, controller.connectionClass);
        $scope.mouseOverConnection = (scope && scope.connection) ? scope.connection : null;
        if ($scope.mouseOverConnection) {
          // Don't attempt to mouse over anything else.
          return;
        }
      }

      // Figure out if the mouse is over a connector.
      var scope = controller.checkForHit(mouseOverElement, controller.connectorClass);
      $scope.mouseOverConnector = (scope && scope.connector) ? scope.connector : null;
      if ($scope.mouseOverConnector) {
        // Don't attempt to mouse over anything else.
        return;
      }

      // Figure out if the mouse is over a node.
      var scope = controller.checkForHit(mouseOverElement, controller.nodeClass);
      $scope.mouseOverNode = (scope && scope.node) ? scope.node : null;
    };
    //
    // Handle mousedown on a node.
    //
    $scope.nodeMouseDown = function (evt, node) {

      var chart = $scope.chart;
      var lastMouseCoords;
      dragging.startDrag(evt, {
        //
        // Node dragging has commenced.
        //
        dragStarted: function (x, y) {
          lastMouseCoords = controller.translateCoordinates(x, y, evt);
          //
          // If nothing is selected when dragging starts, 
          // at least select the node we are dragging.
          //
          if (!node.selected()) {
            chart.deselectAll();
            node.select();
          }

          $(node.data.element[0].parentNode).popover('hide');
        },
        //
        // Dragging selected nodes... update their x,y coordinates.
        //
        dragging: function (x, y) {
          var curCoords = controller.translateCoordinates(x, y, evt);
          var deltaX = curCoords.x - lastMouseCoords.x;
          var deltaY = curCoords.y - lastMouseCoords.y;
          chart.updateSelectedNodesLocation(deltaX, deltaY);
          lastMouseCoords = curCoords;
        },
        //
        // The node wasn't dragged... it was clicked.
        //
        clicked: function () {
          chart.handleNodeClicked(node, evt.ctrlKey);
        },
        //
        // final de dragging
        //
        dragEnded: function () {
          if (node.getPopoverShow()) {
            $(node.data.element[0].parentNode).popover('show');
          } else {
            $(node.data.element[0].parentNode).popover('hide');
          }
        }
      });
    };
    //
    // Handle mousedown on a connection.
    //
    $scope.connectionMouseDown = function (evt, connection) {
      var chart = $scope.chart;
      chart.handleConnectionMouseDown(connection, evt.ctrlKey);
      // Don't let the chart handle the mouse down.
      evt.stopPropagation();
      evt.preventDefault();
    };
    //
    // Handle mousedown on an input connector.
    //
    $scope.connectorMouseDown = function (evt, node, connector, connectorIndex, isInputConnector) {

      //
      // Initiate dragging out of a connection.
      //

      dragging.startDrag(evt, {
        //
        // Called when the mouse has moved greater than the threshold distance
        // and dragging has commenced.
        //
        dragStarted: function (x, y) {

          var curCoords = controller.translateCoordinates(x, y, evt);
          $scope.draggingConnection = true;
          $scope.dragPoint1 = flowchart.computeConnectorPos(node, connectorIndex, isInputConnector);
          $scope.dragPoint2 = {
            x: curCoords.x,
            y: curCoords.y
          };
          $scope.dragTangent1 = flowchart.computeConnectionSourceTangent($scope.dragPoint1, $scope.dragPoint2);
          $scope.dragTangent2 = flowchart.computeConnectionDestTangent($scope.dragPoint1, $scope.dragPoint2);
        },
        //
        // Called on mousemove while dragging out a connection.
        //
        dragging: function (x, y, evt) {
          var startCoords = controller.translateCoordinates(x, y, evt);
          $scope.dragPoint1 = flowchart.computeConnectorPos(node, connectorIndex, isInputConnector);
          $scope.dragPoint2 = {
            x: startCoords.x,
            y: startCoords.y
          };
          $scope.dragTangent1 = flowchart.computeConnectionSourceTangent($scope.dragPoint1, $scope.dragPoint2);
          $scope.dragTangent2 = flowchart.computeConnectionDestTangent($scope.dragPoint1, $scope.dragPoint2);
        },
        //
        // Clean up when dragging has finished.
        //
        dragEnded: function () {

          if ($scope.mouseOverConnector &&
                  $scope.mouseOverConnector !== connector) {

            //
            // Dragging has ended...
            // The mouse is over a valid connector...
            // Create a new connection.
            //
            $scope.chart.createNewConnection(connector, $scope.mouseOverConnector);
          }

          $scope.draggingConnection = false;
          delete $scope.dragPoint1;
          delete $scope.dragTangent1;
          delete $scope.dragPoint2;
          delete $scope.dragTangent2;
        },
      });
    };
//      $scope.dynamicPopover = {
//          content: 'Hello, World!',
//          templateUrl: fullUrlBase + "flowchart/popoversegment",
//          title: 'Title',
//          isOpen: true,
//        };

//    $scope.showDialogDeleted = function (chart, idNode) {
//      $('#dialogDeleteNode').addClass('dialog--open');
//      flowchartDataModal.setData(chart, idNode);
//      for (var i = 0; i < chart.nodes.length; i++) {
//        $(chart.nodes[i].data.element[0].parentNode).popover('hide');
//      }
//    }

    $scope.deleteNodeSelected = function () {
      chart = flowchartDataModal.getData().chart;
      idNode = flowchartDataModal.getData().idNode;
      method = flowchartDataModal.getData().method;
      idLink = flowchartDataModal.getData().idLink;

      chart.deselectAll();

      clicks = chart.connections.filter(function(items){
        if (items.data.source.nodeID == idNode && items.data.class == "success") {
          return items.data.dest.nodeID;
        }
      });

      if (method == "clicks" && clicks.length > 0) {
        for (var i = 0; i < clicks.length; i++) {
          for (var j = 0; j < chart.nodes.length; j++) {
            $(chart.nodes[j].data.element[0].parentNode).popover('hide');
            if (clicks[i].data.dest.nodeID == chart.nodes[j].getId() || idNode == chart.nodes[j].getId()) {
              chart.nodes[j].select();
            }
          }
        }
        chart.deleteSelected();
      } else if (method == "links") {
        // Se actualiza el nodo de clicks se le quita el link seleccionado.
        for (var i = 0; i < chart.connections.length; i++) {
          if (chart.connections[i].data.dest.nodeID == idNode) {
            for (var j = 0; j < $scope.chartViewModel.nodes.length; j++) {
              if (chart.connections[i].data.source.nodeID == $scope.chartViewModel.nodes[j].getId()) {
                $scope.chartViewModel.nodes[j].getSendData().linksTemplateSelected = $scope.chartViewModel.nodes[j].getSendData().linksTemplateSelected.filter(function(items){
                  if (items.id != idLink) {
                    return items;
                  }
                });
                $scope.chartViewModel.nodes[j].data.dataForm.linksTemplateSelected = $scope.chartViewModel.nodes[j].data.dataForm.linksTemplateSelected.filter(function(items){
                  if (items.id != idLink) {
                    return items;
                  }
                });
                break;
              }
            }
          }
        }
        //
        for (var i = 0; i < chart.connections.length; i++) {
          if (chart.connections[i].data.source.nodeID == idNode) {
            for (var j = 0; j < chart.nodes.length; j++) {
              $(chart.nodes[j].data.element[0].parentNode).popover('hide');
              if (idNode == chart.nodes[j].getId()) {
                chart.nodes[j].select();
              }
            }
          }
        }
        for (var j = 0; j < chart.nodes.length; j++) {
          if (idNode == chart.nodes[j].getId()) {
            chart.nodes[j].select();
          }
        }
        chart.deleteSelected();
      } else {
        for (var i = 0; i < chart.connections.length; i++) {
          if (chart.connections[i].data.source.nodeID == idNode) {
            for (var j = 0; j < chart.nodes.length; j++) {
              $(chart.nodes[j].data.element[0].parentNode).popover('hide');
              if (idNode == chart.nodes[j].getId()) {
                chart.nodes[j].select();
              }
            }
          }
        }
        for (var j = 0; j < chart.nodes.length; j++) {
          if (idNode == chart.nodes[j].getId()) {
            chart.nodes[j].select();
          }
        }
        chart.deleteSelected();
      }
      $('#dialogDeleteNode').removeClass('dialog--open');
      if (angular.isDefined($rootScope.items)) {
        var themePre = chart.nodes[$scope.chartViewModel.nodes.length - 1].getTheme();
//        if (themePre == "service") {
//          for (var i = 0; i < $rootScope.items.length; i++) {
//            if ($rootScope.items[i].theme == "service") {
//              $rootScope.items[i].disabled = true;
//              $rootScope.items[i].class += " disabled text-center cursor-pointer";
//            } else if ($rootScope.items[i].theme == "operator") {
//              $rootScope.items[i].disabled = false;
//              $rootScope.items[i].class = "small-text text-center cursor-pointer";
//            }
//          }
//        } else {
//          for (var i = 0; i < $rootScope.items.length; i++) {
//            if ($rootScope.items[i].theme == "operator") {
//              $rootScope.items[i].disabled = true;
//              $rootScope.items[i].class += " disabled text-center cursor-pointer";
//            } else if ($rootScope.items[i].theme == "service") {
//              $rootScope.items[i].disabled = false;
//              $rootScope.items[i].class = "small-text text-center cursor-pointer";
//            }
//          }
//        }
      }
    }

    $scope.popoverElementShow = function (data) {
      $rootScope.popoverElementShow(data);
    }



  }]);
angularFlowchart.controller('FlowChartCustomController', ['$scope', 'flowchartService', '$templateCache', '$compile', '$rootScope', 'notificationService', 'flowchartDataModal', '$q', 'setDataSms', 'setDataMail', 'FileUploader', function ($scope, flowchartService, $templateCache, $compile, $rootScope, notificationService, flowchartDataModal, $q, setDataSms, setDataMail, FileUploader) {

    /*
     * VARIABLES GLOBALES
     */

    var template, element;
    $scope.elements = [];
    $scope.initElement = function (elem) {
      $scope.data.element = elem;
      $scope.data.showPopover = false;
      if (typeof $scope.data.dataForm == "undefined") {
        $scope.selected = {};
        $scope.selected.hrefCreateMail = fullUrlBase + "mailtemplate#/create";
        $scope.selected.hrefCreateSms = fullUrlBase + "smstemplate#/create";
        $scope.selected.hrefCreateSurvey = fullUrlBase + "survey/create#/basicinformation/";
      } else {
        $scope.selected = angular.copy($scope.data.dataForm);
        $scope.compilationAssets();
      }
    }

    $scope.classRefreshrotate = false;
    $scope.timeList = [
      {id: 1, name: "1"},
      {id: 2, name: "2"},
      {id: 3, name: "3"},
      {id: 4, name: "4"},
      {id: 5, name: "5"},
      {id: 6, name: "6"},
      {id: 7, name: "7"},
      {id: 8, name: "8"},
      {id: 9, name: "9"},
      {id: 10, name: "10"},
      {id: 11, name: "11"},
      {id: 12, name: "12"},
      {id: 13, name: "13"},
      {id: 14, name: "14"},
      {id: 15, name: "15"},
      {id: 16, name: "16"},
      {id: 17, name: "17"},
      {id: 18, name: "18"},
      {id: 19, name: "19"},
      {id: 20, name: "20"},
      {id: 21, name: "21"},
      {id: 22, name: "22"},
      {id: 23, name: "23"},
      {id: 24, name: "24"},
      {id: 25, name: "25"},
      {id: 26, name: "26"},
      {id: 27, name: "27"},
      {id: 28, name: "28"},
      {id: 29, name: "29"},
      {id: 30, name: "30"},
      {id: 31, name: "31"},
      {id: 32, name: "32"},
      {id: 33, name: "33"},
      {id: 34, name: "34"},
      {id: 35, name: "35"},
      {id: 36, name: "36"},
      {id: 37, name: "37"},
      {id: 38, name: "38"},
      {id: 39, name: "39"},
      {id: 40, name: "40"},
      {id: 41, name: "41"},
      {id: 42, name: "42"},
      {id: 43, name: "43"},
      {id: 44, name: "44"},
      {id: 45, name: "45"},
      {id: 46, name: "46"},
      {id: 47, name: "47"},
      {id: 48, name: "48"},
      {id: 49, name: "49"},
      {id: 50, name: "50"},
      {id: 51, name: "51"},
      {id: 52, name: "52"},
      {id: 53, name: "53"},
      {id: 54, name: "54"},
      {id: 55, name: "55"},
      {id: 56, name: "56"},
      {id: 57, name: "57"},
      {id: 58, name: "58"},
      {id: 59, name: "59"},
      {id: 60, name: "60"}];
    $scope.timeListtwo = [{id: 1, name: "Minuto(s)"}, {id: 2, name: "Hora(s)"}, {id: 3, name: "Día(s)"}, {id: 4, name: "Semana(s)"}, {id: 5, name: "Mes(es)"}];
    $scope.listDestinary = [{id: 1, name: "Listas de contactos"}, {id: 2, name: "Segmentos"}];
    // $scope.listActions = [{id: 1, name: "Apertura"}, {id: 2, name: "Click"}, {id: 3, name: "No apertura"}, {id: 4, name: "No click"}];
    $scope.listActions = [{id: 1, name: "Apertura"}, {id: 2, name: "Click"}];
    $scope.getpublicsurvey = function () {
      $scope.publicsurvey = [];
      flowchartService.getPublicsurvey().then(function (data) {
        $scope.publicsurvey = data;
      });
    }

    flowchartService.setCacheTemplate($scope.data.templatepopover).then(function (data) {
      $templateCache.put($scope.data.templatepopover, data.data);
      $scope.popover(data.data);
    });
    $scope.popover = function (template) {
      template = $compile(template)($scope);
      $($scope.data.element[0].parentNode).popover({
        html: true,
        title: $scope.data.titlepopover,
        placement: "bottom",
        content: template,
        container: 'body',
        trigger: 'manual',
        width: '500px',
      });
    }


    $rootScope.popoverElementShow = function (data) {
      data.showPopover = true;
      $(data.element[0].parentNode).popover('show');
      if (data.method == "primary") {
        flowchartService.getContactlist().then(function (list) {
          $scope.list = list;
        });
      }
      if (data.method == "sms") {
        flowchartService.getallsmstemplate().then(function (data) {
          setDataSms.setData(data, false);
          $scope.listSmsTemplate = data;
        });
        flowchartService.getAllSmsCategory().then(function (data) {
          setDataSms.setData(data, true);
          $scope.listSmsCategory = data;
        });
      }
      if (data.method == "actions") {
        $scope.searchConnection(data.id).then(function (dataConnection) {
          if (!dataConnection) {
            notificationService.error("El nodo no se encuentra relacionado.");
            $scope.selected.selectAction = {};
            $scope.closePopover();
            return;
          }
          var dataSource = dataConnection.source.node.getSendData();
          if (typeof dataSource.mailtemplate == "undefined") {
            notificationService.error("No se ha seleccionado un template email con anterioridad");
            $scope.selected.selectAction = {};
          }
          if (angular.isDefined(data.sendData.selectAction)) {
            if (data.sendData.selectAction.id == 2 || data.sendData.selectAction.id == 4) {
              $scope.getlinkmailtemplate(dataSource.mailtemplate.idMailTemplate);
            } else {
              $scope.showLinksTemplate = false;
            }
          }

        });
      }
      if (data.method == 'clicks') {
        if (typeof data.sendData.mailtemplate == "undefined") {
          notificationService.error("No se ha seleccionado un template email con anterioridad");
          $scope.selected.selectAction = {};
        }
        if (angular.isDefined(data.sendData.mailtemplate)) {
          $scope.getlinkmailtemplate(data.sendData.mailtemplate.idMailTemplate);
        }
        if (angular.isDefined(data.sendData.linksTemplateSelected)) {
          $scope.selected.linksTemplateSelected = [];
          $scope.selected.linksTemplateSelected = angular.copy(data.dataForm.linksTemplateSelected);
          $scope.refreshData();
        }        
      }
    }

    $scope.closePopover = function () {
      if (typeof $scope.data.dataForm != "undefined") {
        if ($scope.selected !== $scope.data.dataForm) {
          $scope.selected = angular.copy($scope.data.dataForm);
        }
      } else {
        $scope.selected = {};
      }

      $scope.data.showPopover = false;
      $($scope.data.element[0].parentNode).popover('hide');
    }

    $scope.searchConnection = function (idNode) {
      var defer = $q.defer();
      var objReturn = {};
      for (var i = 0; i < $scope.connections.length; i++) {
        var nodeSource = $scope.connections[i].source.parentNode();
        var nodeDest = $scope.connections[i].dest.parentNode();
        if (nodeSource.getId() == idNode) {
          objReturn.dest = {node: nodeDest};
        }
        if (nodeDest.getId() == idNode) {
          objReturn.source = {node: nodeSource};
        }
      }
      if (jQuery.isEmptyObject(objReturn)) {
        defer.resolve(false);
      } else {
        defer.resolve(objReturn);
      }
      return defer.promise;
    }

    $scope.refreshData = function () {
      //$scope.classRefreshrotate = true;
      switch ($scope.data.method) {
        case "sms":
          flowchartService.getallsmstemplate().then(function (data) {
            setDataSms.setData(data, false);
            $scope.listSmsTemplate = data;
            flowchartService.getAllSmsCategory().then(function (data) {
              setDataSms.setData(data, true);
              $scope.listSmsCategory = data;
              $scope.classRefreshrotate = false;
            });
          });
          break;
        case "email":
          $scope.getSender().then(function () {
            $scope.classRefreshrotate = false;
          });
          break;
        case "survey":
          flowchartService.getPublicsurvey().then(function () {
            $scope.classRefreshrotate = false;
          });
          break;
        case "actions":
          $scope.searchConnection($scope.data.id).then(function (data) {
            var dataSource = data.source.node.getSendData();
            $scope.getlinkmailtemplate(dataSource.mailtemplate.idMailTemplate);
          });
          break;
        case "clicks":
          $scope.selected = angular.copy($scope.data.dataForm);
          $scope.getlinkmailtemplate($scope.data.sendData.mailtemplate.idMailTemplate);
          break;
      }
    }

    /*
     * Metodos para el funcionamiento de los Destinarios
     */
    $scope.getSegment = function () {
      flowchartService.getSegment().then(function (data) {
        $scope.list = data;
      });
    }

    $scope.getContactlist = function () {
      flowchartService.getContactlist().then(function (list) {
        $scope.list = list;
      });
    }

    $scope.setListChange = function (list) {
      $scope.selected.selected = [];
      if (list.id == 1) {
        $scope.getContactlist();
      } else {
        $scope.getSegment();
      }
    }

    $scope.applyListSelected = function () {
      //validar la cantidad de contactos para hacer envio y que devuelva una validacion
      $scope.selected.error = false;
      if (typeof $scope.selected.list == "undefined") {
        $scope.selected.error = true;
      }

      if (typeof $scope.selected.selected == "undefined" || $scope.selected.selected == '' || $scope.selected.selected.length == 0) {
        $scope.selected.error = true;
      }

      if (!$scope.selected.error) {
        $scope.data.sendData.list = $scope.selected.list;
        $scope.data.sendData.selecteds = $scope.selected.selected;
        $scope.data.dataForm = angular.copy($scope.selected);
        $scope.data.sendData.textTitle = $scope.selected.list.name;
        if ($scope.data.sendData.selecteds.length > 0) {
          var arrayNameSelected = [];
          for (var i = 0 in $scope.data.sendData.selecteds) {
            arrayNameSelected.push($scope.data.sendData.selecteds[i].name.toString());
          }
          if (arrayNameSelected.length > 2) {
            $scope.data.sendData.text = arrayNameSelected.slice(0, 2).toString() + "...";
          } else {
            $scope.data.sendData.text = arrayNameSelected.toString();
          }
        }
        $scope.closePopover();
      }
    }


    /*
     * METODOS PARA OPERADOR DE TIEMPO
     */
    $scope.applyListSelectedTime = function () {

      $scope.selected.error = false;
      if (typeof $scope.selected.time == "undefined") {
        $scope.selected.error = true;
      }

      if (typeof $scope.selected.timetwo == "undefined") {
        $scope.selected.error = true;
      }

      if (!$scope.selected.error) {
        $scope.data.sendData.time = $scope.selected.time;
        $scope.data.sendData.timetwo = $scope.selected.timetwo;
        $scope.data.dataForm = angular.copy($scope.selected);
        $scope.data.sendData.text = $scope.data.sendData.time.name + ' ' + $scope.data.sendData.timetwo.name + ' Después.';
        $scope.data.sendData.textTitle = "Tiempo Programado";
        $scope.closePopover();
      }
    }

    /*
     * METODO DE FUNCIONAMIENTO DE ACCION
     */
    $scope.changeSelectedAction = function (action) {
      $scope.searchConnection($scope.data.id).then(function (data) {
        if (!data) {
          notificationService.error("El nodo no se encuetra relacionado");
          $scope.selected.selectAction = {};
          return false;
        }
        if (angular.isUndefined(data.source)) {
//          notificationService.error("error mk");
          $scope.selected.selectAction = {};
          return false;
        }

        if (data.source.node.getMethod() != "email") {
          notificationService.error("El nodo anterior debe ser EMAIL");
          $scope.selected.selectAction = {};
          return false;
        }

        var dataSource = data.source.node.getSendData();
        if (typeof dataSource.mailtemplate == "undefined") {
          notificationService.error("No se ha seleccionado un template email con anterioridad");
          $scope.selected.selectAction = {};
        }

        if (action.id == 2 || action.id == 4) {
          $scope.getlinkmailtemplate(dataSource.mailtemplate.idMailTemplate);
        } else {
          $scope.showLinksTemplate = false;
        }
      });
    }

    //FUNCION QUE DEVUELVE LOS LINKS DE LA PLANTILLA-------------------------------------------------------------------------------
    $scope.getlinkmailtemplate = function (idTemplate) {
      flowchartService.getlinkmailtemplate(idTemplate).then(function (data) {
        $scope.listLinksTemplate = [];
        if (data.links.length > 0) {
          // console.log(data);
          $scope.showLinksTemplate = true;
          $scope.listLinksTemplate = data.links;
        } else {
          notificationService.error("El template seleccionado no contiene ningun enlace");
        }
      });
    }

    $scope.applyListSelectedAction = function () {
      $scope.selected.error = false;
      if (typeof $scope.selected.selectAction == "undefined" || $scope.selected.selectAction == '' || jQuery.isEmptyObject($scope.selected.selectAction)) {
        $scope.selected.error = true;
      }

      if (typeof $scope.selected.time == "undefined" || $scope.selected.time == '' || jQuery.isEmptyObject($scope.selected.time)) {
        $scope.selected.error = true;
      }

      if (typeof $scope.selected.timetwo == "undefined" || $scope.selected.timetwo == '' || jQuery.isEmptyObject($scope.selected.timetwo)) {
        $scope.selected.error = true;
      }

      if ($scope.selected.selectAction.id == 2 || $scope.selected.selectAction.id == 4) {
        if (typeof $scope.selected.linksTemplateSelected == "undefined" || $scope.selected.linksTemplateSelected.length <= 0) {
          $scope.selected.error = true;
        }
      }

      if (!$scope.selected.error) {
        $scope.data.sendData.selectAction = $scope.selected.selectAction;
        $scope.data.sendData.time = $scope.selected.time;
        $scope.data.sendData.timetwo = $scope.selected.timetwo;
        $scope.data.sendData.linksTemplateSelected = $scope.selected.linksTemplateSelected;
        $scope.data.dataForm = angular.copy($scope.selected);
        $scope.data.sendData.text = $scope.data.sendData.selectAction.name + ' ' + $scope.data.sendData.time.name + ' ' + $scope.data.sendData.timetwo.name + ' Despues.';
        //$scope.data.sendData.text = 'Categoria:'+$scope.selected.mailcategory.name;
        $scope.data.sendData.textTitle = "Tiempo Programado";
        $scope.closePopover();
      }
      var actionId = $scope.data.id;
      var connections = $rootScope.chart.data.connections;
      connections.forEach(function(elemento, indice) {
        if(actionId == elemento.source.nodeID){
            elemento.sendData.time = $scope.data.sendData.time;
            elemento.sendData.timetwo = $scope.data.sendData.timetwo;
            elemento.sendData.text = $scope.data.sendData.time.name + ' ' + $scope.data.sendData.timetwo.name + ' Despues.';
            elemento.sentData.time = $scope.data.sendData.time.id;
            elemento.sentData.timetwo = $scope.data.sendData.timetwo.id;
        }
      });
    }

    $scope.applyListSelectedClick = function () {
      $scope.data.sendData.linksTemplateSelected = $scope.selected.linksTemplateSelected;
      $scope.data.dataForm = angular.copy($scope.selected);
      $scope.addNewNode($scope.data); 
      $scope.closePopover();
    }

    $scope.addNewNode = function (item) {
      if (typeof item.method == "undefined") {
        return;
      }
      $scope.elements = $rootScope.chart.nodes.map(items => {
        if (items.data.method == 'links' && items.data.sendData.clicks_id == item.id) {
          return items.data.sendData.text;
        }
      });
      if(item.sendData.linksTemplateSelected.length) {
        item.sendData.linksTemplateSelected.forEach(function(value, key){
          if ($scope.elements.indexOf(value.name) == -1) {
            var newNodeDataModel = {
              name: 'Link',
              id: $rootScope.chart.getIdMax() + 1,
              x: 10,
              y: 50,
              width: 100,
              theme: 'operator',
              method: 'links',
              image: fullUrlBase + "images/automatic/link-01.jpg",
              templatepopover: fullUrlBase + "flowchart/popoverlinks",
              titlepopover: '',
              inputConnectors: [
                {
                  name: ""
                }
              ],
              outputConnectors: [
                {
                  name: ""
                },
                {
                  name: ""
                }
              ],
              sendData: {}
            };
            // Revisar para que no cree uno nuevo
            newNodeDataModel.sendData.clicks_id = item.id;
            newNodeDataModel.sendData.linksTemplateSelected = value;
            newNodeDataModel.sendData.text = value.name;
            newNodeDataModel.sendData.textTitle = 'Link';
            $rootScope.chart.addNode(newNodeDataModel);
          }
        });
      }
    }


    /*
     * METODO DE FUNCIONAMIENTO DE MAIL
     */

    $scope.getSender = function () {
      var defer = $q.defer();
      flowchartService.getemailname().then(function (data) {
        $scope.emailname = data;
        flowchartService.getemailsend().then(function (data) {
          $scope.emailsend = data;
        });
        $scope.refreshAddresses = function (search) {
          if (search) {
            $scope.listSMailTemplate = [];
            flowchartService.getallmailtemplatebyfilter(search).then(function (data) {
              setDataMail.setData(data, false);
              $scope.listSMailTemplate = data;
            });
          } else {
            flowchartService.getallmailtemplatebyfilter().then(function (data) {
              setDataMail.setData(data, false);
              $scope.listSMailTemplate = data;
            });
          }
        };
        $scope.refreshAddresses();
        flowchartService.getallmailcategory().then(function (data) {
          setDataMail.setData(data, true);
          $scope.listSMailCategory = data;
        });
        flowchartService.getservices().then(function (data) {
          for(i=0; i<data.length; i++){
            if(data[i]["idService"] == "6"){
              $scope.idService = data[i]["idService"];
              $scope.statusService = data[i]["status"];
            }
          }
        });
      });
      defer.resolve(true);
      return defer.promise;
    }

          //INICIO PROCESAMIENTO DE ARCHIVOS ADJUNTOS++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
          $scope.fileadjunt = [];
          $scope.initialModal = 0;//VALIDAR SI LOS ARCHIVOS DENTRO DEL MODAL CAMBIAN
          $scope.sizeFiles = 0;
          var uploadUrl = fullUrlBase + "gallery/uploadfileadjuntca/";
          var uploader = $scope.uploader = new FileUploader({
            url: uploadUrl
          });
      
          uploader.onAfterAddingFile = function (fileItem) {
            $scope.uploader.queue[$scope.uploader.queue.length - 1].upload();
          };
          uploader.onSuccessItem = function (fileItem, response, status, headers) {
            var id = response[0].newName.split(".");
            var idAsset = id[0];
            var data = {
              id: idAsset,
              name: response[0].name,
              size: response[0].size
            };
            if(angular.isUndefined($scope.fileadjunt)) {
                $scope.fileadjunt = [];
                $scope.fileadjunt.push(data);
            }else{
                $scope.fileadjunt.push(data);
            }
          };
          uploader.onCompleteAll = function () {
            $scope.attach();
            $scope.setSizeFile();
          };
          $scope.attach = function () {
            $scope.sizeFiles = 0;
            if(!angular.isUndefined($scope.fileadjunt)) {
                if ($scope.fileadjunt.length > 0) {
                  $scope.hidefile = true;
                  $scope.setSizeFile();
                }
            }
          };
          $scope.setSizeFile = function () {
            for (var i in $scope.fileadjunt) {
              $scope.sizeFiles += parseInt($scope.fileadjunt[i].size);
            }
          }
          $scope.showModalAdj = function (){
            $('#adjun').modal('show');
          }
          $scope.closeModalAdj = function (){
            $('#adjun').modal('hide');
          }
          $scope.saveAsset = function () {
            $scope.dataModal = [];
            if($scope.uploader.queue.length > 0){
              if($scope.uploader.queue.length != $scope.initialModal){
                $scope.initialModal = $scope.uploader.queue.length;
                for(i=0; i < $scope.uploader.queue.length; i++){
                  var data = {
                    name : $scope.uploader.queue[i].file.name,
                    size : $scope.uploader.queue[i].file.size,
                    contentType : $scope.uploader.queue[i].file.type
                  };
                  $scope.dataModal.push(data);
                }
                $scope.closeModalAdj();
              }else{
                $scope.closeModalAdj();
              }
            }else{
              $scope.closeModalAdj();
            }
          };
          $scope.deleteAsset = function (id) {
            flowchartService.deleteAsset(id).then(function () {
              for(i=0; i<$scope.fileadjunt.length; i++){
                if($scope.fileadjunt[i].id == id){
                  $scope.indexAsset = i;
                }
              }
              $scope.fileadjunt.splice( $scope.indexAsset, 1 );
              $scope.uploader.queue.splice( $scope.indexAsset, 1 );
              $scope.attach();
            });
          };
          $scope.attach();
          //FUNCION PARA MOSTRAR LOS ASSETS EN LA EDICION DE LA CAMPAÑA
          $scope.compilationAssets = function () {
            if($scope.data.name == "Mail"){
              $scope.fileadjunt = $scope.data.sendData.idAssets;
              $scope.attach();
            }
          };
        //FIN PROCESAMIENTO DE ARCHIVOS ADJUNTOS+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

    $scope.changeSelectedMailTemplate = function (mailtemplate) {

      $scope.selected.flagSelected = true;
      $scope.selected.hrefSelectedMail = fullUrlBase + "mailtemplate/edit/" + $scope.selected.mailtemplate.idMailTemplate;
    }

    $scope.changeSelectedSurveyTemplate = function (publicsurvey) {

      $scope.selected.flagSelected = true;
      $scope.selected.hrefSelectedSurvey = fullUrlBase + "survey/create#/basicinformation/" + $scope.selected.publicsurvey.idSurvey;
    }

    $scope.setActionFromEmail = function () {
      $scope.searchConnection($scope.data.id).then(function (data) {
        if (angular.isDefined(data.dest)) {
          if (data.dest.node.getMethod() == "actions") {
            data.dest.node.data.sendData = {};
            $rootScope.popoverElementShow(data.dest.node.data);
          }
        }
      });
    }

    $scope.applyListSelectedMail = function () {
      $scope.selected.error = false;
      if($scope.sizeFiles > 1400000){
        $scope.selected.error = true;
        notificationService.error("El tamaño de los archivos supera los 1.40MB permitidos.");
      }
      if ($scope.selected.mailtemplate == '' || typeof $scope.selected.mailtemplate == "undefined") {
        $scope.selected.error = true;
        notificationService.error("No se encontro ninguna plantilla de correo.");
      }
      if ($scope.selected.subject == '' || typeof $scope.selected.subject == "undefined") {
        $scope.selected.error = true;
      }
      if ($scope.selected.senderEmail == '' || typeof $scope.selected.senderEmail == "undefined") {
        $scope.selected.error = true;
      }
      if ($scope.selected.senderName == '' || typeof $scope.selected.senderName == "undefined") {
        $scope.selected.error = true;
      }
      if ($scope.selected.mailcategory == '' || typeof $scope.selected.mailcategory == "undefined") {
        $scope.selected.error = true;
        notificationService.error("No se encontro ninguna categoria de correo.");
      }



      if ($scope.selected.replyto != '' && typeof $scope.selected.replyto != "undefined") {
        var expr = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        if (!expr.test($scope.selected.replyto)) {
          notificationService.error("Validar el formato del correo de responder a.");
          $scope.selected.error = true;
        }
      }
      if (angular.isDefined($scope.data.sendData.mailtemplate)) {
        if ($scope.data.sendData.mailtemplate != $scope.selected.mailtemplate) {
          $scope.setActionFromEmail();
        }
      }

      if (!$scope.selected.error) {
        $scope.data.sendData.mailtemplate = $scope.selected.mailtemplate;
        $scope.data.sendData.idAssets = $scope.fileadjunt;
        $scope.data.sentData = { "idAssets" : $scope.fileadjunt};
        $scope.data.sendData.subject = $scope.selected.subject;
        $scope.data.sendData.senderEmail = $scope.selected.senderEmail;
        $scope.data.sendData.senderName = $scope.selected.senderName;
        $scope.data.sendData.mailcategory = $scope.selected.mailcategory;
        $scope.data.dataForm = angular.copy($scope.selected);
        $scope.data.sendData.replyto = (typeof $scope.selected.replyto == "undefined") ? '' : $scope.selected.replyto;
        //$scope.data.sendData.text = $scope.selected.mailtemplate.name;
        $scope.data.sendData.text = 'Categoria: ' + $scope.selected.mailcategory.name;
        $scope.data.sendData.textTitle = $scope.selected.mailtemplate.name;
        $scope.data.sendData.textSubject = 'Asunto: ' + $scope.selected.subject;
        $scope.closePopover();
      }
    }
    /*
     * METODO DE SURVEY
     */

    $scope.applyListSelectedSurvey = function () {

      $scope.selected.error = false;
      if ($scope.selected.publicsurvey == '' || typeof $scope.selected.publicsurvey == "undefined") {
        $scope.selected.error = true;
      }
      if ($scope.selected.mailtemplate == '' || typeof $scope.selected.mailtemplate == "undefined") {
        $scope.selected.error = true;
      }
      if ($scope.selected.subject == '' || typeof $scope.selected.subject == "undefined") {
        $scope.selected.error = true;
      }
      if ($scope.selected.senderEmail == '' || typeof $scope.selected.senderEmail == "undefined") {
        $scope.selected.error = true;
      }
      if ($scope.selected.senderName == '' || typeof $scope.selected.senderName == "undefined") {
        $scope.selected.error = true;
      }
      if ($scope.selected.mailcategory == '' || typeof $scope.selected.mailcategory == "undefined") {
        $scope.selected.error = true;
      }
      if ($scope.selected.replyto != '' && typeof $scope.selected.replyto != "undefined") {
        var expr = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        if (!expr.test($scope.selected.replyto)) {
          notificationService.error("Validar el formato del correo de responder a.");
          $scope.selected.error = true;
        }
      }
      if (!$scope.selected.error) {
        $scope.data.sendData.publicsurvey = $scope.selected.publicsurvey;
        $scope.data.sendData.mailtemplate = $scope.selected.mailtemplate;
        $scope.data.sendData.subject = $scope.selected.subject;
        $scope.data.sendData.senderEmail = $scope.selected.senderEmail;
        $scope.data.sendData.senderName = $scope.selected.senderName;
        $scope.data.sendData.mailcategory = $scope.selected.mailcategory;
        $scope.data.dataForm = angular.copy($scope.selected);
        $scope.data.sendData.replyto = (typeof $scope.selected.replyto == "undefined") ? '' : $scope.selected.replyto;
        //$scope.data.sendData.text = $scope.selected.mailtemplate.name;
        $scope.data.sendData.text = 'Categoria:' + $scope.selected.mailcategory.name;
        $scope.data.sendData.textTitle = $scope.selected.mailtemplate.name;
        $scope.closePopover();
      }

    }

    /*
     * METHODO FUNCIONAMIENTO DEL SMS
     */
//ESTA FUNCION SE UTILIZÓ CUANDO ESA AUTOCOMPLETE DE SELECT2
//    $scope.changeSelectedSmsTemplate = function (smstemplate) {
//      if ($scope.selected.smstemplate != '' && typeof $scope.selected.smstemplate != "undefined") {
//        $scope.selected.flagSelected = true;
//        $scope.selected.hrefSelectedSms = fullUrlBase + "smstemplate#/edit/" + $scope.selected.smstemplate;
//        if (dataObjSelectSmsTemplate.items.length > 0) {
//          for (var i = 0; i < dataObjSelectSmsTemplate.items.length; i++) {
//            if (dataObjSelectSmsTemplate.items[i].id == smstemplate) {
//              $scope.selected.objSmsTemplate = dataObjSelectSmsTemplate.items[i];
//            }
//          }
//        }
//      } else {
//        $scope.selected.flagSelected = false;
//      }
//    }
    $scope.changeSelectedSmsTemplate = function (smstemplate) {
      $scope.selected.flagSelected = true;
      $scope.selected.hrefSelectedSms = fullUrlBase + "smstemplate#/edit/" + $scope.selected.smstemplate.idSmsTemplate;
    }
    $scope.applyListSelectedSms = function () {
      $scope.selected.error = false;
      if ($scope.selected.smstemplate == '' || typeof $scope.selected.smstemplate == "undefined") {
        $scope.selected.error = true;
      }

      if ($scope.selected.smscategory == '' || typeof $scope.selected.smscategory == "undefined") {
        $scope.selected.error = true;
      }

      if (!$scope.selected.error) {
        $scope.data.sendData.smstemplate = $scope.selected.smstemplate;
        $scope.data.sendData.smscategory = $scope.selected.smscategory;
        $scope.data.sendData.text = 'Categoria:' + $scope.selected.smscategory.name;
        $scope.data.sendData.textTitle = $scope.selected.smstemplate.name;
        $scope.data.dataForm = angular.copy($scope.selected);
        $scope.closePopover();
      } else {
        notificationService.error("No se encontro ninguna plantilla de sms.");
      }
    }




  }]);
angularFlowchart.controller('FlowChartConexionController', ['$scope', 'flowchartService', '$templateCache', '$compile', '$rootScope', 'notificationService', 'flowchartDataModal', '$q', function ($scope, flowchartService, $templateCache, $compile, $rootScope, notificationService, flowchartDataModal, $q) {
    /**
     * METHOD PARA LA CONECION
     */

    $scope.timeList = [
      {id: 1, name: "1"},
      {id: 2, name: "2"},
      {id: 3, name: "3"},
      {id: 4, name: "4"},
      {id: 5, name: "5"},
      {id: 6, name: "6"},
      {id: 7, name: "7"},
      {id: 8, name: "8"},
      {id: 9, name: "9"},
      {id: 10, name: "10"},
      {id: 11, name: "11"},
      {id: 12, name: "12"},
      {id: 13, name: "13"},
      {id: 14, name: "14"},
      {id: 15, name: "15"},
      {id: 16, name: "16"},
      {id: 17, name: "17"},
      {id: 18, name: "18"},
      {id: 19, name: "19"},
      {id: 20, name: "20"},
      {id: 21, name: "21"},
      {id: 22, name: "22"},
      {id: 23, name: "23"},
      {id: 24, name: "24"},
      {id: 25, name: "25"},
      {id: 26, name: "26"},
      {id: 27, name: "27"},
      {id: 28, name: "28"},
      {id: 29, name: "29"},
      {id: 30, name: "30"},
      {id: 31, name: "31"},
      {id: 32, name: "32"},
      {id: 33, name: "33"},
      {id: 34, name: "34"},
      {id: 35, name: "35"},
      {id: 36, name: "36"},
      {id: 37, name: "37"},
      {id: 38, name: "38"},
      {id: 39, name: "39"},
      {id: 40, name: "40"},
      {id: 41, name: "41"},
      {id: 42, name: "42"},
      {id: 43, name: "43"},
      {id: 44, name: "44"},
      {id: 45, name: "45"},
      {id: 46, name: "46"},
      {id: 47, name: "47"},
      {id: 48, name: "48"},
      {id: 49, name: "49"},
      {id: 50, name: "50"},
      {id: 51, name: "51"},
      {id: 52, name: "52"},
      {id: 53, name: "53"},
      {id: 54, name: "54"},
      {id: 55, name: "55"},
      {id: 56, name: "56"},
      {id: 57, name: "57"},
      {id: 58, name: "58"},
      {id: 59, name: "59"},
      {id: 60, name: "60"}
    ];
    $scope.timeListtwo = [{id: 1, name: "Minuto(s)"}, {id: 2, name: "Hora(s)"}, {id: 3, name: "Día(s)"}, {id: 4, name: "Semana(s)"}, {id: 5, name: "Mes(es)"}];
    $scope.myContextDiv = "<ul id='contextmenu-node' ></ul>";
    $scope.myContextDiv = "<ul id='contextmenu-node' >";
    /*if ($scope.connections.class() == "negation") {
      $scope.myContextDiv += "<li style='list-style: none;' ng-click='EliminarConexion()'>Eliminar ConexiÃƒÂ³n</li>";
      $scope.myContextDiv += "<li style='list-style: none;' ng-click='setTemplatePopover()'>Editar ConexiÃƒÂ³n</li>";
    } else {*/
      $scope.myContextDiv += "<li style='list-style: none;' ng-click='EliminarConexion()'>Eliminar Conexion</li>";
    //}
    $scope.myContextDiv += "</ul>";
    $scope.EliminarConexion = function () {
      $rootScope.chart.deleteSelected();
      $($scope.element[0].children[1]).popover('hide');
    };
    $scope.initElementConector = function (elemnt) {
      $scope.element = elemnt;
      var template = $compile($scope.myContextDiv)($scope);
      $($scope.element[0].children[1]).popover({
        html: true,
        title: "Opciones",
        placement: "right",
        content: template,
        container: 'body',
        trigger: 'manual',
      });
    }


    $scope.setTemplatePopover = function () {
      var url = fullUrlBase + "flowchart/connection";
      flowchartService.setCacheTemplate(url).then(function (data) {
        $templateCache.put(url, data.data);
        $scope.showPopoverEdit(data.data);
      });
    }

    $scope.showPopoverEdit = function (template) {
      var template = $compile(template)($scope);
      $($scope.element[0].children[1]).popover('destroy');
      if (typeof $scope.data.dataForm == "undefined") {
        $scope.selected = {};
      } else {
        $scope.selected = angular.copy($scope.data.dataForm);
      }

      setTimeout(function () {
        $($scope.element[0].children[1]).popover({
          html: true,
          title: "Configuración para la negación",
          placement: "bottom",
          content: template,
          container: 'body',
          trigger: 'manual',
        });
        $($scope.element[0].children[1]).popover('show');
      }, 300);
    }

    $scope.closePopover = function () {
      $($scope.element[0].children[1]).popover('hide');
      $($scope.element[0].children[1]).popover('destroy');
      setTimeout(function () {
        $scope.initElementConector($scope.element);
      }, 200);
    }

    $scope.applyListSelectedConnection = function () {
      $scope.selected.error = false;
      if (typeof $scope.selected.time == "undefined") {
        $scope.selected.error = true;
      }
      if (typeof $scope.selected.timetwo == "undefined") {
        $scope.selected.error = true;
      }
      if (!$scope.selected.error) {
        $scope.connections.data.sendData.time = $scope.selected.time;
        $scope.connections.data.sendData.timetwo = $scope.selected.timetwo;
        $scope.connections.data.dataForm = angular.copy($scope.selected);
        $scope.connections.data.sendData.text = $scope.selected.time.name + ' ' + $scope.selected.timetwo.name + ' Despues.';
        $scope.connections.data.sendData.textTitle = "Tiempo Programado";
        $scope.closePopover();
      }

    }

    $scope.$watch('connections._selected', function () {
      if (!$scope.connections.selected()) {
        $scope.closePopover();
      }
    });
    $scope.showPopover = function () {
      if ($scope.connections.selected()) {
        $($scope.element[0].children[1]).popover('show');
      } else {
        $scope.closePopover();
      }
    }

  }]);
angularFlowchart.controller('FlowChartNodeController', ['$scope', 'flowchartService', '$templateCache', '$compile', '$rootScope', 'notificationService', 'flowchartDataModal', '$q', function ($scope, flowchartService, $templateCache, $compile, $rootScope, notificationService, flowchartDataModal, $q) {
    /**
     * METHOD PARA LA CONECION
     */
    $scope.myContextDiv = "<ul id='contextmenu-node' >";
    if ($scope.nodeSelected.isPrimary()) {
      $scope.myContextDiv += "<li style='list-style: none;' ng-click='showPopoverEdit()'>Editar componente</li>";
    } else if ($scope.nodeSelected.data.method == "links") {
      $scope.myContextDiv += "<li style='list-style: none;' ng-click='showDialogDeleted()'>Eliminar componente</li>";
    } else if ($scope.nodeSelected.data.method == "clicks") {
      $scope.myContextDiv += "<li style='list-style: none;' ng-click='showDialogDeleted()'>Eliminar componente</li>";
      $scope.myContextDiv += "<li style='list-style: none;' ng-click='showPopoverEdit()'>Editar componente</li>";
    } else {
      $scope.myContextDiv += "<li style='list-style: none;' ng-click='showDialogDeleted()'>Eliminar componente</li>";
      $scope.myContextDiv += "<li style='list-style: none;' ng-click='showPopoverEdit()'>Editar componente</li>";
      $scope.myContextDiv += "<li style='list-style: none;' ng-click='duplicateNode()'>Duplicar componente</li>";
    }
    $scope.myContextDiv += "</ul>";
    $scope.showDialogDeleted = function () {
      $('#dialogDeleteNode').addClass('dialog--open');
      if($scope.nodeSelected.getMethod() != "links" && $scope.nodeSelected.getSendData().linksTemplateSelected == undefined) {
        flowchartDataModal.setData($rootScope.chart, $scope.nodeSelected.getId(), $scope.nodeSelected.getMethod(), 0);
      } else {
        flowchartDataModal.setData($rootScope.chart, $scope.nodeSelected.getId(), $scope.nodeSelected.getMethod(), $scope.nodeSelected.getSendData().linksTemplateSelected.id);
      }
      
      for (var i = 0; i < $rootScope.chart.nodes.length; i++) {
        $($scope.element[0].children[0]).popover('hide');
        $($scope.nodeSelected.data.element[0].parentNode).popover('hide');
      }
    }

    $scope.showPopoverEdit = function () {
      $rootScope.popoverElementShow($scope.nodeSelected.data);
      $($scope.element[0].children[0]).popover('hide');
    }
    
    $scope.duplicateNode = function (){
        $scope.addCloneNode($scope.nodeSelected.data);
        $($scope.element[0].children[0]).popover('hide');
    }
    //DUPLICATE NODE
    $scope.addCloneNode = function (item) {
      if (typeof item.method == "undefined") {
        return;
      }
      $scope.elements = $rootScope.chart.nodes.map(items => {
        if (items.data.method == 'links' && items.data.sendData.clicks_id == item.id) {
          return items.data.sendData.text;
        }
      });
      if(item.method == "email"){
        if(item.sendData.mailtemplate){
            var newNodeDataModel = {
              name: item.name,
              id: $rootScope.chart.getIdMax() + 1,
              x: 10,
              y: 50,
              width: 100,
              theme: item.theme,
              method: item.method,
              image: item.image,
              templatepopover: item.templatepopover,
              titlepopover: "Configurar correo",
              inputConnectors: [{
                  name: ""
              }],
              outputConnectors: [{
                  name: ""
              }],
              sendData: {}
            };
            //ASIGNACION DE DATOS DUPLICADOS
            newNodeDataModel.sendData.mailtemplate = item.sendData.mailtemplate;
            /*newNodeDataModel.sendData.idAssets = item.sendData.idAssets;
            newNodeDataModel.sentData = { "idAssets" : item.sendData.idAssets};*/
            newNodeDataModel.sendData.subject = item.sendData.subject;
            newNodeDataModel.sendData.senderEmail = item.sendData.senderEmail;
            newNodeDataModel.sendData.senderName = item.sendData.senderName;
            newNodeDataModel.sendData.mailcategory = item.sendData.mailcategory;
            newNodeDataModel.dataForm = angular.copy(item.sendData);
            newNodeDataModel.sendData.replyto = (typeof item.sendData.replyto == "undefined") ? '' : item.sendData.replyto;
            newNodeDataModel.sendData.text = 'Categoria:' + item.sendData.mailcategory.name;
            newNodeDataModel.sendData.textTitle = item.sendData.mailtemplate.name;
            newNodeDataModel.sendData.textSubject = 'Asunto: ' + item.sendData.subject;
            $rootScope.chart.addNode(newNodeDataModel);
        }else{
          notificationService.error("No se pueden duplicar servicios EMAIL vacios.");
        }
      }else if(item.method == "sms"){
        if(item.sendData.smstemplate){
            var newNodeDataModel = {
              name: item.name,
              id: $rootScope.chart.getIdMax() + 1,
              x: 10,
              y: 50,
              width: 100,
              theme: item.theme,
              method: item.method,
              image: item.image,
              templatepopover: item.templatepopover,
              titlepopover: "Configurar correo",
              inputConnectors: [{
                  name: ""
              }],
              outputConnectors: [{
                  name: ""
              }],
              sendData: {}
            };
            newNodeDataModel.sendData.smstemplate = item.sendData.smstemplate;
            newNodeDataModel.sendData.smscategory = item.sendData.smscategory;
            newNodeDataModel.sendData.text = 'Categoria:' + item.sendData.smscategory.name;
            newNodeDataModel.sendData.textTitle = item.sendData.smstemplate.name;
            newNodeDataModel.dataForm = angular.copy(item.sendData);
            $rootScope.chart.addNode(newNodeDataModel);
        }else{
          notificationService.error("No se pueden duplicar servicios SMS vacios.");
        }
      }else if(item.method == "actions"){
        if(item.sendData.selectAction){
            var newNodeDataModel = {
              name: item.name,
              id: $rootScope.chart.getIdMax() + 1,
              x: 10,
              y: 50,
              width: 100,
              theme: item.theme,
              method: item.method,
              image: item.image,
              templatepopover: item.templatepopover,
              titlepopover: "Configurar correo",
              inputConnectors: [{
                  name: ""
              }],
              outputConnectors: [
                  {
                      name: ""
                  },
                  {
                      name: ""
                  }
              ],
              sendData: {}
            };
            newNodeDataModel.sendData.selectAction = item.sendData.selectAction;
            newNodeDataModel.sendData.time = item.sendData.time;
            newNodeDataModel.sendData.timetwo = item.sendData.timetwo;
            newNodeDataModel.sendData.linksTemplateSelected = item.sendData.linksTemplateSelected;
            newNodeDataModel.dataForm = angular.copy(item.sendData);
            newNodeDataModel.sendData.text = item.sendData.selectAction.name + ' ' + item.sendData.time.name + ' ' + item.sendData.timetwo.name + ' Despues.';
            newNodeDataModel.sendData.textTitle = "Tiempo Programado";
            $rootScope.chart.addNode(newNodeDataModel);
        }else{
          notificationService.error("No se pueden duplicar operadores de ACCION vacios.");
        }
      }
    }

    $scope.initElementConector = function (elemnt) {
      $scope.element = elemnt;
      var template = $compile($scope.myContextDiv)($scope);
      $($scope.element[0].children[0]).popover({
        html: true,
        title: "Opciones",
        placement: "left",
        content: template,
        container: 'body',
        trigger: 'manual',
      });
    }

    $scope.$watch('nodeSelected._selected', function () {
      if (!$scope.nodeSelected.selected()) {
        $($scope.element[0].children[0]).popover('hide');
      }
    });
    $scope.showPopover = function () {
      if ($scope.nodeSelected.selected()) {
        $($scope.element[0].children[0]).popover('show');
      } else {
        $($scope.element[0].children[0]).popover('hide');
      }
    }

  }]);
angularFlowchart.controller('FlowChartstatisticsController', ['$scope', 'flowchartService', '$templateCache', '$compile', '$rootScope', 'notificationService', 'flowchartDataModal', '$q', '$compile', function ($scope, flowchartService, $templateCache, $compile, $rootScope, notificationService, flowchartDataModal, $q, $compile) {

    var controller = this;
    var chart = null;
    var idNode = null;
    var method = null;
    $rootScope.chart = $scope.chart;
    this.document = document;
    this.jQuery = function (element) {
      return $(element);
    }

    $scope.draggingConnection = false;
    $scope.connectorSize = 5;
    $scope.dragSelecting = false;
    $scope.mouseOverConnector = null;
    $scope.mouseOverConnection = null;
    $scope.mouseOverNode = null;
    this.connectionClass = 'connection';
    this.connectorClass = 'connector';
    this.nodeClass = 'node';
    $scope.boolShowPopover = false;
    $scope.elemnt = "";
    this.searchUp = function (element, parentClass) {

      //
      // Reached the root.
      //
      if (element == null || element.length == 0) {
        return null;
      }

      // 
      // Check if the element has the class that identifies it as a connector.
      //
      if (hasClassSVG(element, parentClass)) {
        //
        // Found the connector element.
        //
        return element;
      }

      //
      // Recursively search parent elements.
      //
      return this.searchUp(element.parent(), parentClass);
    };
    this.hitTest = function (clientX, clientY) {

      //
      // Retreive the element the mouse is currently over.
      //
      return this.document.elementFromPoint(clientX, clientY);
    };
    this.checkForHit = function (mouseOverElement, whichClass) {

      //
      // Find the parent element, if any, that is a connector.
      //
      var hoverElement = this.searchUp(this.jQuery(mouseOverElement), whichClass);
      if (!hoverElement) {
        return null;
      }

      return hoverElement.scope();
    };
//
///NO SE TOCAN ESTAS FUNCIONES
    $scope.popoverElementShow = function (data) {
      $rootScope.popoverElementShow(data);
    }

    $scope.setContentPopover = function (statictis, idNode) {
      //console.log(idNode);
//      console.log(statictis);
      $scope.idNode = idNode;
      $scope.statictis = statictis;

      let content = null;
      if ($scope.statictis.nodeType == "primary") {
      content =  "\
              <strong>Tipo de destinatario :</strong></strong> <spam>{{statictis.typeRecipients}}</spam><br>\n\
              <strong>Nombre lista de contactos : </strong><spam>{{statictis.listcontacname}}</spam><br>\n\
              <strong>Total de contactos en la lista :</strong> <spam>{{statictis.totalContac}}</spam>\n\
         ";
      } 
      else if ($scope.statictis.nodeType == "time") {
        content =  "\
              <strong>Tiempo :</strong> <spam>{{statictis.timeName}}</spam><br>\n\
              <strong>Formato de tiempo :</strong> <spam>{{statictis.timetwoName}}</spam><br>\n\
              <strong>destalle final del tiempo :</strong> <spam>{{statictis.text}}</spam>\n\
         ";
      }
      else if ($scope.statictis.nodeType == "actions") {
        if ($scope.statictis.selectactionname == "Respuesta") {
          content =  "\
              <strong>Tipo de opcion :</strong><spam> {{statictis.selectactionname}}</spam><br>\n\
              <strong>Tiempo :</strong> <spam>{{statictis.timename}}</spam><br>\n\
              <strong>Formato del tiempo : </strong><spam>{{statictis.timetwoname}}</spam><br>\n\
              <strong>Pregunta :</strong> <spam>{{statictis.question}}</spam><br>\n\
              <strong>Condicion de la pregunta :</strong> <spam>{{statictis.condition}}</spam><br>\n\
              <strong>Respuesta de la pregunta :</strong> <spam>{{statictis.answer}}</spam>\n\
         ";
        }
        else if($scope.statictis.selectactionname == "Finalizacion"){
          content =  "\
              <strong>Tipo de opcion :</strong> <spam>{{statictis.selectactionname}}</spam><br>\n\
              <strong>Tiempo :</strong> <spam>{{statictis.timename}}</spam><br>\n\
              <strong>Formato del tiempo :</strong> <spam>{{statictis.timetwoname}}</spam><br>\n\
              <strong>Pregunta :</strong> <spam>{{statictis.question}}</spam>\n\
              ";
        }
        else if ($scope.statictis.selectactionname != "Finalizacion" && $scope.statictis.selectactionname != "Finalizacion") {
          content =  "\
              <strong>Tipo de opcion :</strong> <spam>{{statictis.selectactionname}}</spam><br>\n\
              <strong>Tiempo :</strong> <spam>{{statictis.timename}}</spam><br>\n\
              <strong>Formato del tiempo :</strong> <spam>{{statictis.timetwoname}}</spam>\n\
              ";
        }
      }
      else if ($scope.statictis.nodeType == "email") {
      content = "\
                            <div class='external-event  bg-green ui-draggable ui-draggable-handle'>\n\
                              <i class='fa fa-square' style='color: green;'>&nbsp;&nbsp;<strong style='color: white;'>Enviados :<strong> &nbsp;&nbsp;<spam style='color: white;'>{{statictis.sent}}</spam><br>\n\
                              <i class='fa fa-square' style='color: red;'>&nbsp;&nbsp;<strong style='color: white;'>Fallidos :<strong> &nbsp;&nbsp;<spam style='color: white;'>{{statictis.Notshipped}}</spam> <br>\n\
                              <i class='fa fa-square' style='color: blue;'>&nbsp;&nbsp;<strong style='color: white;'>Rebotados :<strong> &nbsp;&nbsp;<spam style='color: white;'>{{statictis.bounced}}</spam><br>\n\
                              <i class='fa fa-square' style='color: orange;'>&nbsp;&nbsp;<strong style='color: white;'>Aperturas :<strong> &nbsp;&nbsp;<spam style='color: white;'>{{statictis.totalOpening}}</spam><br>\n\
                              <i class='fa fa-square' style='color: yellow;'>&nbsp;&nbsp;<strong style='color: white;'>Clicks :<strong> &nbsp;&nbsp;<spam style='color: white;'>{{statictis.uniqueClicks}}</spam><br>\n\
                              <i class='fa fa-square' style='color: purple;'>&nbsp;&nbsp;<strong style='color: white;'>Spam :<strong> &nbsp;&nbsp;</i><spam style='color: white;'>{{statictis.spam}}</spam><br><br><br>\n\
                              <a ng-show='statictis.idMail' href='/statistic#/mail/{{statictis.idMail}}' target='_blank'>Ver detalle de estadisticas</a><br>\n\
                            </div>";
      }
      else if ($scope.statictis.nodeType == "sms") {
      content = "\
                            <div class='external-event  bg-green ui-draggable ui-draggable-handle'>\n\
                              <i class='fa fa-square' style='color: green;'>&nbsp;&nbsp;<strong style='color: white;'>Enviados :<strong> &nbsp;&nbsp;<spam style='color: white;'>{{statictis.sent}}</spam><br>\n\
                              <i class='fa fa-square' style='color: red;'>&nbsp;&nbsp;<strong style='color: white;'>Fallidos :<strong> &nbsp;&nbsp;<spam style='color: white;'>{{statictis.Notshipped}}</spam><br><br><br>\n\
                              <a href='/statistic#/sms/{{statictis.idSms}}' target='_blank'>Ver detalle de estadisticas</a><br>\n\
                             </div>";
      }
      else if ($scope.statictis.nodeType == "survey") {
      content = "\
                            <div class='external-event  bg-green ui-draggable ui-draggable-handle'>\n\
                              <i class='fa fa-square' style='color: green;'>&nbsp;&nbsp;<strong style='color: white;'>Enviados :<strong> &nbsp;&nbsp;<spam style='color: white;'>{{statictis.sent}}</spam><br>\n\
                              <i class='fa fa-square' style='color: red;'>&nbsp;&nbsp;<strong style='color: white;'>Fallidos :<strong> &nbsp;&nbsp;<spam style='color: white;'>{{statictis.Notshipped}}</spam> <br>\n\
                              <i class='fa fa-square' style='color: blue;'>&nbsp;&nbsp;<strong style='color: white;'>Rebotados :<strong> &nbsp;&nbsp;<spam style='color: white;'>{{statictis.bounced}}</spam><br>\n\
                              <i class='fa fa-square' style='color: orange;'>&nbsp;&nbsp;<strong style='color: white;'>Aperturas :<strong> &nbsp;&nbsp;<spam style='color: white;'>{{statictis.totalOpening}}</spam><br>\n\
                              <i class='fa fa-square' style='color: yellow;'>&nbsp;&nbsp;<strong style='color: white;'>Clicks :<strong> &nbsp;&nbsp;<spam style='color: white;'>{{statictis.totalClicks}}</spam><br>\n\
                              <i class='fa fa-square' style='color: purple;'>&nbsp;&nbsp;<strong style='color: white;'>Spam :<strong> &nbsp;&nbsp;</i><spam style='color: white;'>{{statictis.spam}}</spam><br>\n\
                            </div>";
      }
      else if ($scope.statictis.nodeType == "clicks") {
      content = "\
        <strong>Tiempo :</strong> <spam>{{statictis.timename}}</spam><br>\n\
        <strong>Formato del tiempo : </strong><spam>{{statictis.timetwoname}}</spam><br>\n\
        ";
      }
      else if ($scope.statictis.nodeType == "links") {
      content = "\
        <strong>Tiempo :</strong> <spam>{{statictis.timename}}</spam><br>\n\
        <strong>Formato del tiempo : </strong><spam>{{statictis.timetwoname}}</spam><br>\n\
        <strong>Link :</strong> <spam>{{statictis.linkname}}</spam><br>\n\
        ";
      }
      
      
      let popoverTitle = $scope.statictis.nodeTitle;
      $scope.setPopover($compile(content)($scope), popoverTitle);
    }
    
    $scope.setPopover = function (content, popoverTitle) {

      $($scope.elemnt).popover({
        html: true,
        title: popoverTitle,
        placement: "bottom",
        content: content,
        container: 'body',
        trigger: ' manual ',
        width: '500px',
      });
    }

    $scope.showPopover = function () {
      $($scope.elemnt).popover('show');
    }

    $scope.hidePopover = function () {
      $($scope.elemnt).popover('hide');
    }
  }]);
