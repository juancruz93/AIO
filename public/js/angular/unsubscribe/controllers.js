(function () {
  angular.module('unsubscribeApp', ['ngDragDrop', 'unsubscribe.service'])
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
                    
          .directive('htmlSortable', ["$parse", "$timeout", "$log", "$window", function ($parse, $timeout, $log, $window) {

              return {
                restrict: 'A',
                require: '?ngModel',
                scope: {
                  htmlSortable: '=',
                  ngModel: '=',
                  ngExtraSortable: '='
                },
                //scope: true,   // optionally create a child scope
                link: function (scope, element, attrs, ngModel) {
                  //var model = $parse(attrs.htmlSortable);
                  /*attrs.html5Sortable*/

                  var sortable = {};
                  sortable.is_handle = false;
                  sortable.in_use = false;

                  sortable.handleDragStart = function (e) {

                    if (sortable.options && angular.isDefined(sortable.options.disableDrag)) {
                      if (sortable.options.disableDrag(ngModel.$modelValue, angular.element(this)) === true) {
                        e.preventDefault();
                        return;
                      }
                    }

                    $window['drag_source'] = null;
                    $window['drag_source_extra'] = null;

                    if (sortable.options && !sortable.is_handle && sortable.options.handle) {
                      e.preventDefault();
                      return;
                    }

                    sortable.is_handle = false;
                    e.dataTransfer.effectAllowed = 'move';
                    //Fixed on firefox and IE 11
                    if (sortable.browser != "IE") {
                      e.dataTransfer.setData('text/plain', 'anything');
                    }


                    $window['drag_source'] = this;
                    $window['drag_source_extra'] = element.extra_data;

                    // this/e.target is the source node.
                    this.classList.add('moving');
                  };

                  sortable.handleDragOver = function (e) {
                    if (e.preventDefault) {
                      e.preventDefault(); // Allows us to drop.
                    }

                    e.dataTransfer.dropEffect = 'move';

                    if (!this.classList.contains('over')) {
                      this.classList.add('over');
                    }

                    //return false;
                  };

                  sortable.handleDragEnter = function (e) {
                    if (!this.classList.contains('over')) {
                      this.classList.add('over');
                    }
                  };

                  sortable.handleDragLeave = function (e) {
                    this.classList.remove('over');
                  };

                  sortable.handleDrop = function (e) {
                    // this/e.target is current target element.
                    if (e.stopPropagation) {
                      // stops the browser from redirecting.
                      e.stopPropagation();
                    }
                    e.preventDefault();
                    this.classList.remove('over');

                    // Don't do anything if we're dropping on the same column we're dragging.
                    if ($window['drag_source'] != this) {

                      if ($window['drag_source'] == null) {
                        //                        $log.info("Invalid sortable");
                        return;
                      }


                      var source_model = $window['drag_source'].model;
                      var drop_index = this.index;

                      if (ngModel.$modelValue.indexOf(source_model) != -1) {

                        var drag_index = $window['drag_source'].index;
                        var temp = angular.copy(ngModel.$modelValue[drag_index]);

                        sortable.unbind();

                        ngModel.$modelValue.splice(drag_index, 1);
                        ngModel.$modelValue.splice(drop_index, 0, temp);

                      } else if (sortable.options.allow_cross) {
                        ngModel.$modelValue.splice(drop_index, 0, source_model);
                      } else {
                        //                        $log.info("disabled cross dropping");
                        return;
                      }

                      //return;
                      scope.$apply();

                      if (sortable.options && angular.isDefined(sortable.options.stop)) {
                        //                        $log.info('Make callback');
                        sortable.options.stop(ngModel.$modelValue, drop_index,
                                element.extra_data, $window['drag_source_extra']);
                      }
                    }

                    return false;
                  };

                  sortable.handleDragEnd = function (e) {
                    // this/e.target is the source node.
                    [].forEach.call(sortable.cols_, function (col) {
                      col.classList.remove('over');
                      col.classList.remove('moving');
                    });

                  };

                  //Unbind all events are registed before
                  sortable.unbind = function () {

                    //                    $log.info('Unbind sortable');
                    [].forEach.call(sortable.cols_, function (col) {
                      col.removeAttribute('draggable');
                      col.removeEventListener('dragstart', sortable.handleDragStart, false);
                      col.removeEventListener('dragenter', sortable.handleDragEnter, false);
                      col.removeEventListener('dragover', sortable.handleDragOver, false);
                      col.removeEventListener('dragleave', sortable.handleDragLeave, false);
                      col.removeEventListener('drop', sortable.handleDrop, false);
                      col.removeEventListener('dragend', sortable.handleDragEnd, false);
                    });
                    sortable.in_use = false;
                  }

                  sortable.activehandle = function () {
                    sortable.is_handle = true;
                  }

                  sortable.register_drop = function (element_children) {
                    element_children.addEventListener('drop', sortable.handleDrop, false);
                    element_children.addEventListener('dragstart', sortable.handleDragStart, false);
                    element_children.addEventListener('dragenter', sortable.handleDragEnter, false);
                    element_children.addEventListener('dragover', sortable.handleDragOver, false);
                    element_children.addEventListener('dragleave', sortable.handleDragLeave, false);
                    element_children.addEventListener('drop', sortable.handleDrop, false);
                    element_children.addEventListener('dragend', sortable.handleDragEnd, false);
                  }

                  sortable.getBrowser = function () {
                    var browser_agent = $window.navigator.userAgent;
                    if (browser_agent.indexOf(".NET") != -1) {
                      //IE 11
                      return "IE";
                    } else if (browser_agent.indexOf("Firefox") != -1) {
                      return "Firefox";
                    } else {
                      return "Chrome";
                    }
                  }

                  sortable.update = function () {
                    //                    $log.info("Update sortable");
                    $window['drag_source'] = null;
                    var index = 0;

                    //This's empty list, so just need listen drop from other
                    if (ngModel.$modelValue.length == 0) {
                      if (element[0].children.length > 0) {
                        //Set index = 0( simulate first index )
                        element[0].children[0].index = 0;
                        sortable.register_drop(element[0].children[0]);
                      }
                      return;
                    }

                    this.browser = this.getBrowser();

                    this.cols_ = element[0].children;

                    [].forEach.call(this.cols_, function (col) {
                      if (sortable.options && sortable.options.handle) {
                        var handle = col.querySelectorAll(sortable.options.handle)[0];
                        handle.addEventListener('mousedown', sortable.activehandle, false);
                      }

                      col.index = index;
                      col.model = ngModel.$modelValue[index];

                      index++;

                      col.setAttribute('draggable', 'true');  // Enable columns to be draggable.
                      sortable.register_drop(col);
                    });

                    sortable.in_use = true;
                  }

                  if (ngModel) {
                    ngModel.$render = function () {
                      $timeout(function () {
                        //Init flag indicate the first load sortable is done or not
                        sortable.first_load = false;

                        scope.$watch('ngExtraSortable', function (value) {
                          element.extra_data = value;
                          //sortable.extra_data = value;
                        });

                        scope.$watch('htmlSortable', function (value) {

                          sortable.options = angular.copy(value);

                          if (value == "destroy") {
                            if (sortable.in_use) {
                              sortable.unbind();
                              sortable.in_use = false;
                            }
                            return;
                          }

                          if (!angular.isDefined(sortable.options)) {
                            sortable.options = {};
                          }

                          if (!angular.isDefined(sortable.options.allow_cross)) {
                            sortable.options.allow_cross = false
                          }

                          if (angular.isDefined(sortable.options.construct)) {
                            sortable.options.construct(ngModel.$modelValue);
                          }

                          element[0].classList.add('html5-sortable');
                          sortable.update();
                          $timeout(function () {
                            sortable.first_load = true;
                          })
                        }, true);

                        //Watch ngModel and narrate it
                        scope.$watch('ngModel', function (value) {
                          if (!sortable.first_load || sortable.options == 'destroy') {
                            //Ignore on first load
                            return;
                          }

                          $timeout(function () {
                            sortable.update();
                          });

                        }, true);

                      });
                    };
                  } else {
                    //                    $log.info('Missing ng-model in template');
                  }
                }
              };
            }])
          .controller('contactController', ['$scope', 'RestService', 'notificationService', '$window', function ($scope, RestService, notificationService, $window) {
              //data
                            
              $scope.data = {};
              $scope.idMail = idMail;
              $scope.idContact = idContact;
              $scope.sendSelected = [];
              $scope.prueba = {};
              $scope.items = [];
              $scope.arrServices = [];
              $scope.arrConfigDashboard = [];
              $scope.arrSubs = [];
              $scope.arrUnsubs = [];
              $scope.validateView = false; 
              $scope.responsemessage = "";
              $scope.data.option = "Ninguno";
              
              $scope.data.options = [
                {id: 1, name:"Nunca me suscribí a esta lista"},
                {id: 2, name:"El contenido de estos emails no me interesa"},
                {id: 3, name:"Recibo estos emails con demasiada frecuencia"},
                {id: 4, name:"Estos emails son spam"},
                {id: 5, name:"Ninguno"},
                {id: 6, name:"Otro"}
              ];
              $scope.data.other = "";
              $scope.data.selectOption = [
                {id: "one", name: "Desuscribirse de esta Base de datos."},
                {id: "all", name: "Desuscribirse de todas las Bases de datos."}
              ];
              $scope.data.click = "";
              $scope.disabled = false;
              $scope.optionDroppable = {
                opacity: 0.4,
              };
              
              $scope.data.idMail = idMail;
              $scope.data.idContact = idContact;
                
              //Misc
              $scope.misc = {};
              $scope.misc.advanceUnsuscribe = false;
              
              //typeView
              if( typeView == 1 ){
                    $scope.validateView = true;   
              }else{
                    $scope.validateView = false;
              }
              
              //Functions
              $scope.functions = {
                changeAdvanceUnsuscribe: function () {
                  if ($scope.misc.advanceUnsuscribe) {
                    $scope.misc.advanceUnsuscribe = false;
                  } else {
                    $scope.misc.advanceUnsuscribe = true;
                  }
                }
              };

              $scope.restService = {
                sendUnsubscribeSimple: function () {
                  document.getElementById('simpleDesuscribeBtn').disabled = true;
                  RestService.sendUnsubscribeSimple($scope.data).then(function (data) {
                    notificationService.success(data.message);
                  });
                }
              }

              $scope.draggableOptions = {
                remove: function (index) {
                }
              }

              //Action Droppable
              $scope.actionDroppableSubs = {
                onOver: function (event, ui) {
                  element = angular.element(document.getElementById('subs'));
                  $(element[0])
                          .removeClass("boder-droppable")
                          .addClass("hover-droppable");
                },
                onDrop: function (event, ui) {
                  element = angular.element(document.getElementById('subs'));
                  $(element[0])
                          .removeClass("hover-droppable")
                          .addClass("boder-droppable")
                          .find("p")
                          .html("");
                },
                remove: function (index) {
                  objList = $scope.arrConfigDashboard[index];
                  for (i in $scope.arrDefaultDashboard.items) {
                    if (objList.title == $scope.arrDefaultDashboard.items[i].title) {
                      $scope.arrConfigDashboard.splice(index, 1);
                      $scope.arrServices.splice(objList.jqyoui_pos, 1, $scope.arrDefaultDashboard.items[i]);
                    }
                  }
                }
              }

              $scope.actionDroppableUnSubs = {
                onOver: function (event, ui) {
                  element = angular.element(document.getElementById('unsubs'));
                  $(element[0])
                          .removeClass("boder-droppable")
                          .addClass("hover-droppable");
                },
                onDrop: function (event, ui) {
                  element = angular.element(document.getElementById('unsubs'));
                  $(element[0])
                          .removeClass("hover-droppable")
                          .addClass("boder-droppable")
                          .find("p")
                          .html("");
                },
                remove: function (index) {
                  objList = $scope.arrConfigDashboard[index];
                  for (i in $scope.arrDefaultDashboard.items) {
                    if (objList.title == $scope.arrDefaultDashboard.items[i].title) {
                      $scope.arrConfigDashboard.splice(index, 1);
                      $scope.arrServices.splice(objList.jqyoui_pos, 1, $scope.arrDefaultDashboard.items[i]);
                    }
                  }
                }
              }
              RestService.getContact($scope.idContact, $scope.idMail).then(function (data) {

                $scope.contact = data;                
                $scope.data.contact = data.contact;
                $scope.data.typeView = $scope.validateView;
                if (!angular.isDefined($scope.contact.contact.name)) {
                  $scope.name = $scope.contact.contact.email;
                } else if (!angular.isDefined($scope.contact.contact.lastname)) {
                  $scope.name = $scope.contact.contact.name;
                } else {
                  $scope.name = $scope.contact.contact.name + " " + $scope.contact.contact.lastname;
                }

                for (var i = 0; i < $scope.contact.arrUnsubscribedCategories.length; i++) {
                  $scope.contact.arrUnsubscribedCategories[i].jqyoui_pos = i;
                  $scope.contact.arrUnsubscribedCategories[i].drag = true;
                }

                for (var i = 0; i < $scope.contact.arrSubscribedCategories.length; i++) {
                  $scope.contact.arrSubscribedCategories[i].jqyoui_pos = i;
                  $scope.contact.arrSubscribedCategories[i].drag = true;
                }

                $scope.arrSubs = angular.copy($scope.contact.arrSubscribedCategories);
                $scope.arrUnsubs = angular.copy($scope.contact.arrUnsubscribedCategories);
                
                //var arrayCategory = $scope.arrSubsConcat.concat($scope.arrUnsubs);
                //console.log("arrayCategory",arrayCategory);
                //$scope.arrUnsubs = arrayCategory;
                
                if(data.message != ''){
                  $scope.disabled = true;
                  notificationService.primary(data.message);
                }
              })

              $scope.not = false;
              
              
              $scope.opeModalMoreCa = function () {
                
                $('#alertMoreCaracter').removeClass('modal');
                if($scope.data.other.length > 0 && $scope.data.option === 'Otro'){
                    $("#textmessage").text("La desuscripción fue realizada correctamente, muchas gracias por informarnos el motivo de la desuscripción, esperamos mejorar para usted.");
                }else{
                    $("#textmessage").text("La desuscripción fue realizada correctamente.");
                } 
                $('#alertMoreCaracter').addClass('dialog dialog--open'); 
              }
              
              $scope.closeModalMoreCa = function () {
                
                if($scope.data.typeView == 0){
                    $scope.disabled = true;  
                }
                $('#alertMoreCaracter').removeClass('dialog dialog--open');
                $('#alertMoreCaracter').addClass('modal'); 
              }

              $scope.sendUnsubscribe = function () {
                 
                $scope.data.subscribe = $scope.arrSubs;
                $scope.data.unsubscribe = $scope.arrUnsubs;
                RestService.sendUnsubscribe($scope.data, $scope.idContact).then(function (data) {
                  //notificationService.success(data.message);
                  $scope.opeModalMoreCa();
                  if($scope.data.typeView == 0){
                    $scope.disabled = true;  
                  }else{
                    $scope.disabled = true;
                    $scope.data.other = "";
                  }
                  
                });

              }

              $scope.subscribe = function (category) {
                for (var i = 0; i < $scope.arrUnsubs.length; i++) {
                  if ($scope.arrUnsubs[i].idContactlistCategory == category.idContactlistCategory) {
                    $scope.arrUnsubs.splice(i, 1);
                  }
                }
                $scope.arrSubs.push(category);
              }
              $scope.unsubscribe = function (category) {
                for (var i = 0; i < $scope.arrSubs.length; i++) {
                  if ($scope.arrSubs[i].idContactlistCategory == category.idContactlistCategory) {
                    $scope.arrSubs.splice(i, 1);
                  }
                }
                $scope.arrUnsubs.push(category);
              }
              $scope.selectOption = function (name){
                $scope.data.option = name;
                if($scope.data.option != 'Otro'){
                    $scope.data.other ='';
                }
              }
              $scope.selectButton = function (name){
                $scope.data.click = name;
              }
            
            }])
            
            .controller('advanceController', ['$scope', 'RestService', 'notificationService', '$window', function ($scope, RestService, notificationService, $window) {
                
                $scope.initial = 0;
                $scope.page = 1;
                $scope.stringsearch = -1;
                $scope.shownewblockade = true;
                $scope.showblockade = true;
                $scope.search = "";
                                
              //data
                $scope.getAll = function () {
                    RestService.getAll($scope.initial, $scope.stringsearch).then(function (data) {                
                        if (data.total > 0) {
                            $scope.showblockade = false;
                        } else {
                            $scope.shownewblockade = false;
                        }
                        $scope.blockade = data;
                    })
                };
                $scope.getAll();
                $scope.forward = function () {
                    $scope.progressbar = false;
                    $scope.initial += 1;
                    $scope.page += 1;
                    $scope.getAll();
                };
                $scope.fastforward = function () {
                    $scope.progressbar = false;
                    $scope.initial = ($scope.blockade.total_pages - 1);
                    $scope.page = $scope.blockade.total_pages;
                    $scope.getAll();
                };
                $scope.backward = function () {
                    $scope.progressbar = false;
                    $scope.initial -= 1;
                    $scope.page -= 1;
                    $scope.getAll();
                };
                $scope.fastbackward = function () {
                    $scope.progressbar = false;
                    $scope.initial = 0;
                    $scope.page = 1;
                    $scope.getAll();
                };
                
                              
                $scope.searchContact = function (param) {
                    if(param == 1){                        
                        if($scope.search != ''){
                            $scope.stringsearch = $scope.search;
                            $scope.getAll();   
                        }else{
                            $scope.getAll();
                        }
                                                         
                    }else if (param == 2){
                        $scope.search = "";
                        $scope.stringsearch = -1;
                        $scope.getAll();  
                    }    
                };
                
                $scope.deleteUnsub = function (idContact) {
                    RestService.deleteUnsub(idContact).then(function (data) {
                    notificationService.success(data.message);
                    $scope.getAll();
                    });
                }
                
            }])
            .controller('createController', ['$scope', 'RestService', 'notificationService', '$window', function ($scope, RestService, notificationService, $window) {
              $scope.allcategories = false; 
              $scope.services = [];
                            
              $scope.initComponents = function () {
                RestService.listindicative().then(function (response) {
                  $scope.listindicative = response;
                }).catch(function (error) {
                  notificationService.error(error.message);
                });
                
                RestService.listCategories().then(function (response) {              
                  $scope.listAllAddressee = response;
                  console.log("data----->",response );
                }).catch(function (error) {
                  notificationService.error(error.message);
                });
                                                
              };
              $scope.initComponents();
                 
              $scope.addUnsub = function () {
                                               
                $scope.data.services = $scope.services;
                RestService.createUnsub($scope.data).then(function (response) {                    
                    var route=  fullUrlBase + templateBase+"/list";
                    $window.location.href = route;
                    notificationService.success(response.message);                    
                }).catch(function (error) {
                    notificationService.error(error.message);
                });

              }                                  
                
            }])
          .controller('contactautomaticController', ['$scope', 'RestService', 'notificationService', '$window', function ($scope, RestService, notificationService, $window) {
              $scope.idMail = idMail;
              $scope.idContact = idContact;
              $scope.sendSelected = [];
              $scope.prueba = {};
              $scope.items = [];
              $scope.arrServices = [];
              $scope.arrConfigDashboard = [];
              $scope.arrSubs = [];
              $scope.arrUnsubs = [];
              $scope.optionDroppable = {
                opacity: 0.4,
              };

              $scope.draggableOptions = {
                remove: function (index) {
                }
              }

              //Action Droppable
              $scope.actionDroppableSubs = {
                onOver: function (event, ui) {
                  element = angular.element(document.getElementById('subs'));
                  $(element[0])
                          .removeClass("boder-droppable")
                          .addClass("hover-droppable");
                },
                onDrop: function (event, ui) {
                  element = angular.element(document.getElementById('subs'));
                  $(element[0])
                          .removeClass("hover-droppable")
                          .addClass("boder-droppable")
                          .find("p")
                          .html("");
                },
                remove: function (index) {
                  objList = $scope.arrConfigDashboard[index];
                  for (i in $scope.arrDefaultDashboard.items) {
                    if (objList.title == $scope.arrDefaultDashboard.items[i].title) {
                      $scope.arrConfigDashboard.splice(index, 1);
                      $scope.arrServices.splice(objList.jqyoui_pos, 1, $scope.arrDefaultDashboard.items[i]);
                    }
                  }
                }
              }

              
              $scope.actionDroppableUnSubs = {
                onOver: function (event, ui) {
                  element = angular.element(document.getElementById('unsubs'));
                  $(element[0])
                          .removeClass("boder-droppable")
                          .addClass("hover-droppable");
                },
                onDrop: function (event, ui) {
                  element = angular.element(document.getElementById('unsubs'));
                  $(element[0])
                          .removeClass("hover-droppable")
                          .addClass("boder-droppable")
                          .find("p")
                          .html("");
                },
                remove: function (index) {
                  objList = $scope.arrConfigDashboard[index];
                  console.log(objList);
                  for (i in $scope.arrDefaultDashboard.items) {
                    if (objList.title == $scope.arrDefaultDashboard.items[i].title) {
                      $scope.arrConfigDashboard.splice(index, 1);
                      $scope.arrServices.splice(objList.jqyoui_pos, 1, $scope.arrDefaultDashboard.items[i]);
                    }
                  }
                }
              }
              RestService.getContact($scope.idContact,$scope.idMail).then(function (data) {

                $scope.contact = data;

                if (!angular.isDefined($scope.contact.contact.name)) {
                  $scope.name = $scope.contact.contact.email;
                } else if (!angular.isDefined($scope.contact.contact.lastname)) {
                  $scope.name = $scope.contact.contact.name;
                } else {
                  $scope.name = $scope.contact.contact.name + " " + $scope.contact.contact.lastname;
                }
                for (var i = 0; i < $scope.contact.arrUnsubscribedCategories.length; i++) {
                  $scope.contact.arrUnsubscribedCategories[i].jqyoui_pos = i;
                  $scope.contact.arrUnsubscribedCategories[i].drag = true;
                }

                for (var i = 0; i < $scope.contact.arrSubscribedCategories.length; i++) {
                  $scope.contact.arrSubscribedCategories[i].jqyoui_pos = i;
                  $scope.contact.arrSubscribedCategories[i].drag = true;
                }

                $scope.arrSubs = angular.copy($scope.contact.arrSubscribedCategories);
                $scope.arrUnsubs = angular.copy($scope.contact.arrUnsubscribedCategories);

              });

              $scope.sendUnsubscribe = function () {
                var sendunsubscribes = {idMail: $scope.idMail, arrSubs: $scope.arrSubs, arrUnsubs: $scope.arrUnsubs};
                RestService.sendUnsubscribeAutomatic(sendunsubscribes, $scope.idContact).then(function (data) {
                  notificationService.success(data.message);
                });

              }

              $scope.subscribe = function (category) {
                for (var i = 0; i < $scope.arrUnsubs.length; i++) {
                  if ($scope.arrUnsubs[i].idContactlistCategory == category.idContactlistCategory) {
                    $scope.arrUnsubs.splice(i, 1);
                  }
                }
                $scope.arrSubs.push(category);
              }

              $scope.unsubscribe = function (category) {
                for (var i = 0; i < $scope.arrSubs.length; i++) {
                  if ($scope.arrSubs[i].idContactlistCategory == category.idContactlistCategory) {
                    $scope.arrSubs.splice(i, 1);
                  }
                }
                $scope.arrUnsubs.push(category);
              }
            }]);
          
          
  angular.element().ready(function () {
    angular.bootstrap(document, ['unsubscribeApp'])
  });

})();
