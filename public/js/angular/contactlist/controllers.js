angular.module('contactlist.controllers', [])
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
        .controller('ContactlistController', ['$scope', 'restService', 'notificationService', 'constantContactList', function ($scope, restService, notificationService, constantContactList) {
            $scope.data = {};
            $scope.misc = {};
            $scope.misc.filter = {};
            $scope.misc.filter.emailInvalid = false;
            $scope.progressbar = false;
            $scope.functions = {
              toggle: function (item, list) {
                var idx = list.indexOf(item);
                if (idx > -1) {
                  list.splice(idx, 1);
                } else {
                  list.push(item);
                }
              },
              exists: function (item, list) {
                return list.indexOf(item) > -1;
              },
              isIndeterminate: function () {
                return ($scope.data.selected.length !== 0 &&
                        $scope.data.selected.length !== $scope.data.items.length);
              },
              isChecked: function () {
                return $scope.data.selected.length === $scope.data.items.length;
              },
              toggleAll: function () {
                if ($scope.data.selected.length === $scope.data.items.length) {
                  //$scope.data.selected = [];
                  $scope.functions.setMethodData("selected", []);
                } else if ($scope.data.selected.length === 0 || $scope.data.selected.length > 0) {
                  //$scope.data.selected = $scope.data.items.slice(0);
                  $scope.functions.setMethodData("selected", $scope.data.items.slice(0))
                }
              },
              initializeVars: function () {
                $scope.data = {};
                $scope.data.items = [];
                $scope.data.selected = [];
                $scope.data.demo = {showTooltip: false, tipDirection: ''};
                $scope.data.demo.delayTooltip = undefined;
                $scope.data.initial = 0;
                $scope.data.page = 1;
                $scope.data.filter = {};
                $scope.data.name = "";
                $scope.data.listCategories = [];
                $scope.data.totals = [];
              },
              confirmDelete: function (idContactlist) {
                //$scope.data.idContactlist = idContactlist;
                $scope.functions.setMethodData("idContactlist", idContactlist);
                openModal('somedialog');
              },
              filter: {
                name: function () {
                  $scope.data.initial = 0;
                  $scope.data.page = 1;
                  $scope.data.contactlists = {};
                  $scope.restServices.getAll();
                },
                refresh: function () {
                  $scope.data.filter.name = "";
                  delete($scope.data.filter.idContactlistCategory);
                  $scope.data.filter.email = "";
                  $scope.data.initial = 0;
                  $scope.data.page = 1;
                  $scope.data.contactlists = {};
                  $scope.restServices.getAll();
                  $scope.restServices.getTotals();
                },
                email: function () {
                  var exp = constantContactList.misc.regexEmail;
                  $scope.data.initial = 0;
                  $scope.data.page = 1;
                  if ($scope.data.filter.email.length > 0) {
                    if (exp.test($scope.data.filter.email)) {
                      $scope.misc.filter.emailInvalid = false;
                      $scope.data.initial = 0;
                      $scope.data.page = 1;
                      //$scope.progressbar = true;
                      $scope.data.contactlists = {};
                      $scope.restServices.getAll();
                    } else {
                      $scope.data.contactlists = {};
                      $scope.misc.filter.emailInvalid = true;
                    }
                  } else {
                    $scope.progressbar = false;
                    $scope.misc.filter.emailInvalid = true;
                    //$scope.data.contactlists = {};
                    $scope.restServices.getAll();
                  }
                }
              },
              forward: function () {
                $scope.progressbar = false;
                $scope.data.initial += 1;
                $scope.data.page += 1;
                $scope.restServices.getAll();
              },
              fastforward: function () {
                $scope.progressbar = false;
                $scope.data.initial = ($scope.data.contactlists.total_pages - 1);
                $scope.data.page = $scope.data.contactlists.total_pages;
                $scope.restServices.getAll();
              },
              backward: function () {
                $scope.progressbar = false;
                $scope.data.initial -= 1;
                $scope.data.page -= 1;
                $scope.restServices.getAll();
              },
              fastbackward: function () {
                $scope.progressbar = false;
                $scope.data.initial = 0;
                $scope.data.page = 1;
                $scope.restServices.getAll();
              },
              exportContacts: function (idContactlist) {
                restService.exportContacts(idContactlist).then(function (data) {
                  notificationService.success(data.message);
                })
              },
              initialFunctions: function () {
                $scope.functions.initializeVars();
                $scope.restServices.getAll();
                $scope.restServices.getListCategories();
                $scope.restServices.getTotals();
              },
              setMethodData: function (item, data) {
                $scope.data[item] = data;
              }
            };

            //CONTAR LA CANTIDAD DE CONTACTOS EN LA LISTA DE CONTACTOS
            $scope.validateTotalContacts = function(idContactlist, typeExport){
              $scope.idContactlist = idContactlist;
              $scope.typeExport = typeExport;
              var data = {
                idContactlist: idContactlist,
                typeExport: typeExport
              };
              restService.validateTotalContacts(data).then(function (res) {
                $scope.totalContacts = res["totalContacts"];
                if($scope.totalContacts >= 15000){
                  openModal('moreExport');
                }else{
                  var url = fullUrlBase + "contact/export/"+$scope.idContactlist+"/"+$scope.typeExport;
                  window.location.href =url;
                }
              });
            };

            $scope.validateEmail = function() {
              const re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
              var test = re.test($scope.emailExport);
              if(test){
                $scope.flagEmail = false;
                var data = {
                  idContactlist: $scope.idContactlist,
                  typeExport: $scope.typeExport,
                  email: $scope.emailExport
                };
                restService.exportMoreContacts(data).then(function (res) {
                  $scope.closeModalMoreExport();
                  openModal('moreExportConfirmation');
                });
              }else{
                $scope.flagEmail = true;
              }
            }

            $scope.closeModalMoreExport =  function(){
              closeModal('moreExport');
            };

            $scope.closeModalMoreExportConfirmation =  function(){
              $scope.emailExport = "";
              closeModal('moreExportConfirmation');
            };

            $scope.restServices = {
              deleteContactlist: function () {
                restService.deleteContactlist($scope.data.idContactlist).then(function (data) {
                  closeModal('somedialog');
                  notificationService.warning(data.message);
                  $scope.restServices.getAll();
                });
              },
              getAll: function () {
                if ($scope.misc.filter.emailInvalid) {
                  $scope.data.filter.email = "";
                }
                $scope.progressbar = false;
                restService.getAll($scope.data.initial, $scope.data.filter).then(function (data) {
                  $scope.functions.setMethodData("contactlists", data.contactlistxpage);
                  $scope.functions.setMethodData("allcontactlists", data.allcontactlist);
                  if ($scope.data.allcontactlists) {
                    for (i = 0; i < $scope.data.allcontactlists[0].items.length; i++) {
                      $scope.data.items[i] = $scope.data.allcontactlists[0].items[i].idContactlist;
                    }
                    $scope.progressbar = true;
                  }
                  $scope.progressbar = true;
                }).catch(function (data) {
                  $scope.progressbar = true;
                  notificationService.error(data.message);
                });
              },
              getListCategories: function () {
                restService.getContactlistCategory().then(function (data) {
                  $scope.data.listCategories = data.categories;
                });
              },
              getTotals: function () {
                restService.getTotals().then(function (data) {
                  $scope.data.totals = data.totals;
                });
              }
            }

            $scope.functions.initialFunctions();

            $scope.$watch('data.demo.delayTooltip', function (val) {
              $scope.data.demo.delayTooltip = parseInt(val, 10) || 0;
            });
            $scope.$watch('data.demo.tipDirection', function (val) {
              if (val && val.length) {
                $scope.data.demo.showTooltip = true;
              }
            })


          }])
        .controller('ContactlistAddController', ['$scope', '$window', 'restService', 'notificationService', function ($scope, $window, restService, notificationService) {
            $scope.contactlist = [];
            $scope.contactlist.idContactlistCategory = 0;
            $scope.showInputCategory = false;
            $scope.showCategoryName = true;
            $scope.showIconsCategory = true;
            $scope.showIconsSaveCategory = false;

            $scope.changeStatusNameCategory = function () {
              if (!$scope.showInputCategory) {
                $scope.showInputCategory = true;
                $scope.showCategoryName = false;
                $scope.showIconsCategory = false;
                $scope.showIconsSaveCategory = true;
              } else {
                $scope.showInputCategory = false;
                $scope.showCategoryName = true;
                $scope.showIconsCategory = true;
                $scope.showIconsSaveCategory = false;
              }
            };

            $scope.saveCategory = function () {
              var data = {name: $scope.categoryName};
              $scope.changeStatusNameCategory();
              restService.saveCategory(data).then(function (res) {
                notificationService.success(res.message);
                $scope.categoryName = "";
                getContactlistCategory();
                $scope.contactlist.idContactlistCategory = res.category.idContactlistCategory;
              });
            };

            function getContactlistCategory() {
              restService.getContactlistCategory().then(function (data) {
                $scope.categories = data.categories;
              });
            }

            getContactlistCategory();

            $scope.getContactList = function () {
              restService.getContactList().then(function (data) {
                $scope.contactlists = data;
              });
            };
            $scope.addContactlist = function () {
              if ($scope.contactlist.idContactlistCategory == 0) {
                notificationService.error("Debe elegir una categoría");
              }
              if (!$scope.contactlist) {
                notificationService.error("El campo nombre no puede estar vacio");
              }
              var data = {
                name: $scope.contactlist.name,
                idContactlistCategory: $scope.contactlist.idContactlistCategory,
                description: $scope.contactlist.description,
                disabledCustomField: $scope.contactlist.disabledCustomField,
                idContactlist: $scope.contactlist.idContactlist
              };
              restService.save(data).then(function (data) {
                $window.location.href = '#/';
                notificationService.success(data.message);
              });
            };
          }])
        .controller('ContactlistEditController', ['$scope', '$routeParams', '$window', 'restService', 'notificationService', function ($scope, $routeParams, $window, restService, notificationService) {
            var id = $routeParams.id;
            restService.getOne(id).then(function (data) {
              $scope.contactlist = data;
            });
            $scope.showInputCategory = false;
            $scope.showCategoryName = true;
            $scope.showIconsCategory = true;
            $scope.showIconsSaveCategory = false;

            $scope.changeStatusNameCategory = function () {
              if (!$scope.showInputCategory) {
                $scope.showInputCategory = true;
                $scope.showCategoryName = false;
                $scope.showIconsCategory = false;
                $scope.showIconsSaveCategory = true;
              } else {
                $scope.showInputCategory = false;
                $scope.showCategoryName = true;
                $scope.showIconsCategory = true;
                $scope.showIconsSaveCategory = false;
              }
            };

            $scope.saveCategory = function () {
              var data = {name: $scope.categoryName};
              $scope.changeStatusNameCategory();
              restService.saveCategory(data).then(function (res) {
                notificationService.success(res.message);
                $scope.categoryName = "";
                getContactlistCategory();
                $scope.contactlist.idContactlistCategory = res.category.idContactlistCategory;
              });
            };

            function getContactlistCategory() {
              restService.getContactlistCategory().then(function (data) {
                $scope.categories = data.categories;
              });
            }

            getContactlistCategory();
            $scope.editContactlist = function () {
              if (!$scope.contactlist) {
                notificationService.error("El campo nombre no puede estar vacio");
              }
              restService.edit(id, $scope.contactlist.name, $scope.contactlist.description, $scope.contactlist.idContactlistCategory).then(function (data) {
                $window.location.href = '#/';
                notificationService.info(data.message);
              });
            };
          }])
        .controller('EditCustomFieldController', ['$scope', '$routeParams', '$window', 'restService', 'notificationService', function ($scope, $routeParams, $window, restService, notificationService) {
            $scope.customfield = {};
            $scope.value = [];
            $scope.selectType = function () {
              $scope.value = [];
              $scope.valueSelected = true;
              if ($scope.customfield.type == "Select" || $scope.customfield.type == "Multiselect") {
                $scope.valueSelected = false;
              }
            };

            $scope.idContactlist = $routeParams.id;


            restService.getOneCustomField($scope.idContactlist).then(function (data) {
              $scope.customfield = data;
              $scope.btndisabled = false;
              if ($scope.customfield.value) {
                $scope.value = $scope.customfield.value.split(",");
              }
              $scope.valueSelected = true;
              if ($scope.customfield.type == "Select" || $scope.customfield.type == "Multiselect") {
                $scope.valueSelected = false;
              }

            });

            $scope.editCustomfield = function () {
              $scope.btndisabled = true;
              var data = {
                idCustomfield: $scope.idContactlist,
                name: $scope.customfield.name,
                alternativename: $scope.customfield.alternativename,
                defaultvalue: $scope.customfield.defaultvalue,
                type: $scope.customfield.type,
                value: $scope.value
              };
              restService.editCustomfield(data).then(function (data) {
                $window.location.href = '#/customfield/' + data['customfield'].idContactlist;
                notificationService.info(data.message);
              }).catch(function (data) {
                $scope.btndisabled = false;
                notificationService.error(data.message);
              });
            };
          }])
        .controller('customfield', ['$scope', '$routeParams', '$window', 'restService', 'notificationService', function ($scope, $routeParams, $window, restService, notificationService) {

            $scope.traslateCustomfield = function (string) {
              var custom = "";
              switch (string) {
                case "Text":
                  custom = "Texto";
                  break;
                case "Date":
                  custom = "Fecha";
                  break;
                case "Numerical":
                  custom = "Numerico";
                  break;
                case "TextArea":
                  custom = "Area de texto";
                  break;
                case "Select":
                  custom = "Selección";
                  break;
                case "Multiselect":
                  custom = "Selección multiple";
                  break;
                default :

                  break;
              }
              return custom;
            };

            $scope.initial = 0;
            $scope.page = 1;
            $scope.getAll = function () {
              $scope.idContactlist = $routeParams.id;
              restService.listcustomfield($scope.idContactlist, $scope.initial).then(function (data) {
                $scope.customfield = data;
              });

            };
            $scope.forward = function () {
              $scope.initial += 1;
              $scope.page += 1;
              $scope.getAll();
            };
            $scope.fastforward = function () {
              $scope.initial = ($scope.contactlists.total_pages - 1);
              $scope.page = $scope.contactlists.total_pages;
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
            $scope.confirmDeleteCustomfield = function (idCustomfield) {
              $scope.idCustomfield = idCustomfield;
              $('#somedialog').addClass('dialog--open');
            };
            $scope.deleteCustomfield = function () {
              restService.deleteCustomfield($scope.idCustomfield).then(function (data) {
//                  console.log(data);
                $window.location.href = '#/customfield/' + $scope.idContactlist;
                //                  $window.location.href = '#/';
                notificationService.warning(data.message);
              });
            };
            $scope.getAll();

            $scope.permissionCustomfield = function () {
              $scope.idContactlist = $routeParams.id;
              restService.permissionCustomfield($scope.idContactlist).then(function (data) {
                console.log(data);
                if (parseInt(data) >= 15) {
                  $('#dialogcustom').addClass('dialog--open');
                } else {
                  $window.location.href = '#/addcustomfield/' + $scope.idContactlist;
                }


              });

            }


//              var id = $routeParams.id;
//              restService.getOne(id).then(function (data) {
//                $scope.contactlist = data;
//              });
//
//              $scope.editContactlist = function () {
//                restService.edit(id, $scope.contactlist.name, $scope.contactlist.description).then(function (data) {
//                  $window.location.href = '#/';
//                  notificationService.info(data.message);
//                });
//              };
          }])
        .controller('AddcustomfieldController', ['$scope', '$routeParams', '$window', 'restService', 'notificationService', function ($scope, $routeParams, $window, restService, notificationService) {
            $scope.vars = {
              listTypeFields: [],
              data: {}
            };

            $scope.functions = {
              initComponents: function () {
                $scope.vars.data.value = [];
                $scope.vars.listTypeFields = [
                  {label: "Texto", value: "Text"},
                  {label: "Fecha", value: "Date"},
                  {label: "Área de texto", value: "TextArea"},
                  {label: "Selección", value: "Select"},
                  {label: "Selección multiple", value: "Multiselect"},
                  {label: "Numerico", value: "Numerical"}
                ];
              }
            };

            $scope.btndisabled = false;
            $scope.valueSelected = true;
            $scope.selectType = function () {
              $scope.vars.data.value = [];
              $scope.valueSelected = true;
              if ($scope.vars.data.typefield == "Select" || $scope.vars.data.typefield == "Multiselect") {
                $scope.valueSelected = false;
              }
            };

            $scope.idContactlist = $routeParams.id;

            $scope.addcustomfield = function () {


              restService.permissionCustomfield($scope.idContactlist).then(function (data) {
                console.log(data);
                if (parseInt(data) >= 15) {
                  $('#dialogcustom').addClass('dialog--open');
                } else {
                  $scope.btndisabled = true;
                  $scope.vars.data.idContactlist = $scope.idContactlist;

                  restService.addcustomfield($scope.vars.data).then(function (data) {
                    $window.location.href = '#/customfield/' + data['customfield'].idContactlist;
                    notificationService.success(data.message);
                  }).catch(function (data) {
                    $scope.btndisabled = false;
                    notificationService.error(data.message);
                  });
                }
              });
            };
          }])
        .controller('ContactlistDeleteController', ['$scope', '$routeParams', '$window', 'restService', 'notificationService', function ($scope, $routeParams, $window, restService, notificationService) {
            var id = $routeParams.id;
            restService.getOne(id).then(function (data) {
              $scope.contactlist = data;
            });

            $scope.deleteContactlist = function () {
              restService.deleteContactlist(id).then(function (data) {
                $window.location.href = '#/';
                notificationService.warning(data.message);
              });
            };
          }])
        .controller('CustomfieldDeleteController', ['$scope', '$routeParams', '$window', 'restService', 'notificationService', function ($scope, $routeParams, $window, restService, notificationService) {

            var id = $routeParams.id;
            restService.getOneCustomField(id).then(function (data) {
              $scope.customfield = data;
            });

            $scope.deleteCustomfield = function () {
              restService.deleteCustomfield(id).then(function (data) {
                console.log(data);
//                  $window.location.href = '#/customfield/' + data['customfield'].idContactlist;
                notificationService.warning(data.message);
              });
            };
          }]);





