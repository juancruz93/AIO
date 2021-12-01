'use strict';
(function () {
  angular.module('forms.controllers', [])
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
                out = items;
              }

              return out;
            };
          })
          .controller('indexController', ['$scope', 'restService', 'notificationService', '$builder', '$validator', '$window', '$timeout', function ($scope, restService, notificationService, $builder, $validatorm, $window, $timeout) {
              $scope.initial = 0;
              $scope.page = 1;
              $scope.previewShow = false;
              $scope.input = [];
              $scope.forward = function () {
                $scope.initial += 1;
                $scope.page += 1;
                $scope.listForms();
              };
              $scope.fastforward = function () {
                $scope.initial = ($scope.list.total_pages - 1);
                $scope.page = $scope.list.total_pages;
                $scope.listForms();
              };
              $scope.backward = function () {
                $scope.initial -= 1;
                $scope.page -= 1;
                $scope.listForms();
              };
              $scope.fastbackward = function () {
                $scope.initial = 0;
                $scope.page = 1;
                $scope.listForms();
              };
              $scope.listForms = function () {
                restService.listForms($scope.initial, $scope.filter).then(function (res) {
                  $scope.list = res;
                });
              };
              $scope.listForms();
              $scope.filter = {};

              $scope.filtername = function () {
                restService.listForms($scope.initial, $scope.filter).then(function (res) {
                  $scope.list = res;
                });
              };

              $scope.openModal = function (idForm) {
                $scope.idForm = idForm;
                $('#deleteDialog').addClass('dialog--open');
              }; 
              
              $scope.closeModal = function () {
                $('#deleteDialog').removeClass('dialog--open');
              };
              
              $scope.deleteForm = function () {
                restService.deleteForm({idForm: $scope.idForm}).then(function (res) { 
                  notificationService.warning(res.message);
                  $scope.listForms();
                });
                $scope.closeModal();
              };
              $scope.translateType = function (type) {
                switch (type) {
                  case 'suscription':
                    return 'Suscripción';
                  case 'updating':
                    return 'Actualización';
                }
              };
              getAllCategories();
              $scope.previsualizar = function ($index) {
                $scope.previewShow = false;
                var json = JSON.parse($scope.list.items[$index].content.content);
                $builder.setForm('sigmaForm', json.form);
                $("#preview").addClass('dialog--open');
                $scope.backgroundForm = json.background;

                $timeout(function () {
                  $scope.previewShow = true;
                }, 1000);
              }

              $scope.validateForm = function () {
                $validator.validate($scope, 'sigmaForm')
                        .success(function () {})
                        .error(function () {});
              }
              $scope.codeHtml = function ($index) {
                $("#codeHtml").addClass('dialog--open');
                $scope.codeHtmlString = angular.copy($scope.list.items[$index].html);
              }
              $scope.codeIframe = function ($index) {
                $("#iframe").addClass('dialog--open');
                $scope.codeIframeString = angular.copy($scope.list.items[$index].iframe);
              }

              $scope.removeDialog = function (modal) {
                $("#" + modal).removeClass('dialog--open');
              }

              $scope.reloadEditForm = function (idForm) {
                $window.location.href = fullUrlBase + templateBase + '/create#/basicinformation/' + idForm;
              }

              function getAllCategories() {
                restService.getAllFormCategory().then(function (res) {
                  $scope.category = res;
                });
              }
            }])
          .controller('createBasicInformationController', ['$scope', 'restService', 'notificationService', '$window', '$state', '$stateParams', '$rootScope', function ($scope, restService, notificationService, $window, $state, $stateParams, $rootScope) {
              $scope.data = {};
              $scope.data.doubleOptin = {};
              $scope.data.mailWelcome = {};
              $scope.data.notification = {};
              $scope.showInputCate = false;
              $scope.showSelectCate = true;
              $scope.showIconsCate = true;
              $scope.showIconsSaveCate = false;


              $scope.changeStatusInputCate = function () {
                if (!$scope.showInputCate) {
                  $scope.showInputCate = true;
                  $scope.showSelectCate = false;
                  $scope.showIconsCate = false;
                  $scope.showIconsSaveCate = true;
                } else {
                  $scope.showInputCate = false;
                  $scope.showSelectCate = true;
                  $scope.showIconsCate = true;
                  $scope.showIconsSaveCate = false;
                }
              }

              $scope.saveBasicInformation = function () {
                if (typeof $stateParams.id != "undefined" && $stateParams.id != "") {
                  restService.updatebasicinformation($stateParams.id, $scope.data).then(function (data) {
                    notificationService.success("Se ha creado la información basica del formulario");
                    $state.go('create.forms', {id: $stateParams.id});
                  });
                } else {
                  restService.saveBasicInformation($scope.data).then(function (data) {
                    notificationService.success("Se ha creado la información basica del formulario");
                    $state.go('create.forms', {id: data.idForm});
                  });
                }

              };
              $scope.previewmailtempcont = function (id) {
                if (id) {
                  restService.previewMailTemplateContent(id).then(function (data) {
                    var e = data.template;
                    document.getElementById("preview-modal").innerHTML = "";
                    $('<iframe frameborder="0" width="100%" height="100%"/>').appendTo('#preview-modal').contents().find('body').append(e);
                    $('#myModal').modal('show');
                  });
                }
              };
              $scope.getContactList = function () {
                restService.getContactlist().then(function (res) {
                  $scope.contactlist = res;
                });
              };

              getAllCategories();

              function getAllCategories() {
                restService.getAllFormCategory().then(function (res) {
                  $scope.category = res;
                });
              }
              ;

              $scope.saveCategory = function () {
                var data = {category: $scope.nameCategory};

                restService.addFormCategory(data).then(function (res) {
                  notificationService.success(res['msg']);
                  $scope.nameCategory = "";
                  getAllCategories();
                  $scope.changeStatusInputCate();
                  $scope.data.idFormCategory = res['idFormCategory'];
                });
              };

              $scope.getMailTemplate = function (search) {
                if (search) {
                  restService.getallmailtemplatebyfilter(search).then(function (data) {
                    $scope.mailtemplate = data;
                  });
                } else {
                  restService.getMailTemplate().then(function (res) {
                    $scope.mailtemplate = res;
                  });
                }
              };

              if (typeof $stateParams.id != "undefined" && $stateParams.id != "") {
                $rootScope.idForm = $stateParams.id;
                restService.getInformationForm($stateParams.id).then(function (data) {
                  $scope.data = data;
                  $scope.data.idFormCategory = data.idFormCategory;
                  $scope.data.doubleOptin = {};
                  $scope.data.mailWelcome = {};
                  $scope.data.notification = {};
                  if (data.optin == 1) {
                    restService.getOptin($stateParams.id).then(function (dataOptin) {
                      if (dataOptin != false) {
                        $scope.data.doubleOptin = dataOptin;
                        $scope.data.doubleOptin.active = true;
                        $("#divdoubleOptin").collapse('show');
                      }
                    });
                  }
                  if (data.welcomeMail == 1) {
                    restService.getWelcomeMail($stateParams.id).then(function (dataWelcome) {
                      if (dataWelcome != false) {
                        $scope.data.mailWelcome = dataWelcome;
                        $scope.data.mailWelcome.active = true;
                        $("#divwelcome").collapse('show');
                      }
                    });
                  }
                  if (data.notificationMail == 1) {
                    restService.getNotification($stateParams.id).then(function (dataNotification) {
                      if (dataNotification != false) {
                        $scope.data.notification = dataNotification;
                        $scope.data.notification.active = true;
                        $("#divnotification").collapse('show');
                      }
                    });
                  }
                  $scope.getContactList();
                  $scope.getMailTemplate();
                }).catch(function (data) {
                  $state.go('create.describe');
                });
              } else {
                $scope.getContactList();
                $scope.getMailTemplate();
              }
            }])
          .controller('formsController', ['$scope', 'restService', 'notificationService', '$builder', '$validator', '$q', '$stateParams', '$state', '$window', '$rootScope', '$injector', function ($scope, restService, notificationService, $builder, $validator, $q, $stateParams, $state, $window, $rootScope, $injector) {

              $injector.get('$drag').setConfigData('droppables', {});
              $rootScope.idForm = $stateParams.id;
              $scope.input = [];
              $builder.setForm('default', []);
              $scope.db = false;
              $scope.complet = false;
              $scope.arrFields = [
                {selected: false, id: "name", title: "Nombre", input: "textInput", field: "primary"},
                {selected: false, id: "lastname", title: "Apellido", input: "textInput", field: "primary"},
                {selected: false, id: "birthdate", title: "Fecha Nacimiento", input: "dateInput", field: "primary", validation: "[fecha]"},
                {selected: false, id: "email", title: "Correo", input: "textInput", field: "primary", objExt: {noDeleted: true}},
                {selected: false, id: "indicative", title: "Indicativo", input: "select", field: "primary", objExt: {noOptions: true, dependence: "phone"}},
                {selected: false, id: "phone", title: "Telefono", input: "numberInput", field: "primary", validation: "[numberEnteros]", objExt: {noOptions: true, dependence: "indicative"}},
                {selected: false, id: "encabezado", title: "Nombre de formulario", input: "encabezado", field: "encabezado"},
              ];

              $scope.init = function () {
                restService.getcontentform($stateParams.id).then(function (data) {
                  var objData = JSON.parse(data.content);
                  $scope.backgroundForm = objData.background;
                  $builder.setForm('default', objData.form);
                  $scope.form = $builder.forms['default'];
                  $scope.db = true;
                  $scope.initWatch();
                  $scope.getIndicative();
                  $scope.setConfigScope(objData);
                }).catch(function () {
                  $builder.addFormObject('default', {
                    id: 'button',
                    component: 'button',
                    label: 'Aceptar',
                    description: '',
                    placeholder: 'button',
                    required: true,
                    editable: true,
                    objExt: {fontOrien: "center"},
                  });
                  $scope.form = $builder.forms['default'];
                  $scope.initWatch();
                  $scope.getIndicative();
                });
              }

              $scope.setConfigScope = function (content) {
                if (typeof content.background != "undefined") {
                  $scope.backgroundForm = content.background;
                }
                if (typeof content.fontForm != "undefined") {
                  $scope.fontForm = content.fontForm;
                }
                if (typeof content.sizeForm != "undefined") {
                  $scope.sizeForm = content.sizeForm;
                }
                if (typeof content.fontStyle != "undefined") {
                  $scope.fontStyle = content.fontStyle;
                }
              }

              $scope.arrDirective = [];
              $scope.addComponent = function (Field) {
                var objExtra = {};
                var objComponent = {};
                if (Field.selected) {
                  return;
                }

                Field.selected = true;
                if (angular.isDefined(Field.objExt)) {
                  objExtra = Field.objExt;
                }
                if (Field.input == 'dateInput') {
                  objExtra = {openen: false};
                }
                if (angular.isDefined($scope.fontForm)) {
                  objExtra.fontColor = $scope.fontForm;
                }

                if (angular.isDefined($scope.sizeForm)) {
                  objExtra.sizeStyle = $scope.sizeForm;
                }

                if (angular.isDefined($scope.fontStyle)) {
                  objExtra.fontStyle = $scope.fontStyle;
                }

                if (Field.input == 'encabezado') {
                  Field.title = "Nombre de formulario";
                  objExtra = {sizeStyle: "24px", fontOrien: "center"};
                }


                objComponent = {
                  id: Field.id,
                  component: Field.input,
                  label: Field.title,
                  description: '',
                  placeholder: Field.title,
                  required: true,
                  editable: true,
                  objExt: objExtra,
                };

                if (Field.id == 'email') {
                  objComponent.objExt.validationOptionsHide = true;
                  objComponent.validation = "[email]";
                }
                if (typeof Field.validation != "undefined") {
                  objComponent.validation = Field.validation;
                }

                if (Field.input == 'checkbox') {
                  if (angular.isDefined(Field.valueDefault)) {
                    if (typeof Field.valueDefault == "string") {
                      objComponent.options = Field.valueDefault.split(",");
                    } else {
                      objComponent.options = Field.valueDefault;
                    }
                  }
                } else if (Field.input == 'select') {
                  if (angular.isDefined(Field.valueDefault)) {
                    if (typeof Field.valueDefault == "string") {
                      objComponent.options = Field.valueDefault.split(",");
                    } else {
                      objComponent.options = Field.valueDefault;
                    }
                   
                  }
                }
                $builder.addFormObject('default', objComponent);
                if (typeof Field.objExt != "undefined" && typeof Field.objExt.dependence != "undefined") {
                  var valid = false;
                  for (var i in $scope.form) {
                    if ($scope.form[i].id == Field.objExt.dependence) {
                      valid = true;
                    }
                  }
                  if (!valid) {
                    for (var i in $scope.arrFields) {
                      if ($scope.arrFields[i].id == Field.objExt.dependence) {
                        $scope.addComponent($scope.arrFields[i]);
                      }
                    }
                  }

                }
              }
              $scope.initWatch = function () {
                $scope.$watch('form', function (newv, old) {
                  if (old.length > newv.length) {
                    $scope.compareArrayObject(old, newv, 'id').then(function (data) {
                      for (var j in data) {
                        for (var i = 0; i < $scope.arrFields.length; i++) {
                          if ($scope.arrFields[i].id == data[j].id) {
                            $scope.arrFields[i].selected = false;
                          }
                        }
                      }
                    });
                  }
                }, true);
              }

              $scope.compareArrayObject = function (x, y, itemCompared) {
                var defer = $q.defer();
                var arrayReturn = [];
                for (var i in x) {
                  var objX = x[i];
                  if (angular.isDefined(objX[itemCompared])) {
                    var valid = false;
                    for (var j in y) {
                      var objY = y[j];
                      if (objX[itemCompared] == objY[itemCompared]) {
                        valid = true;
                      }
                    }
                    if (!valid) {
                      arrayReturn.push(objX);
                    }
                  }
                }
                defer.resolve(arrayReturn);
                return defer.promise;
              }

              $scope.setValueArrObj = function (x, y, item) {
                var defer = $q.defer();
                var arrayReturn = [];
                for (var i in x) {
                  var objX = x[i];
                  if (angular.isDefined(objX[item])) {
                    for (var j in y) {
                      var objY = y[j];
                      if (objX[item] == objY[item]) {
                        arrayReturn.push(objX);
                      }
                    }
                  }
                }
                defer.resolve(arrayReturn);
                return defer.promise;
              }


              $scope.getCustomField = function () {
                restService.getCustomfield($scope.idContactList).then(function (data) {
                  for (var i in data) {
                    var id = data[i].alternativename;
                    var title = data[i].name;
                    var input;
                    switch (data[i].type) {
                      case 'Text':
                        input = "textInput";
                        break;
                      case 'Date':
                        input = "dateInput";
                        break;
                      case 'Numerical':
                        input = "numberInput";
                        break;
                      case 'TextArea':
                        input = "textArea";
                        break;
                      case 'Multiselect':
                        input = "checkbox";
                        break;
                      case 'Select':
                        input = "select";
                        break;
                    }
                    $scope.arrFields.push({selected: false, id: id, title: title, input: input, field: "custom", valueDefault: data[i].value});
                  }
                  for (var i in $scope.form) {
                    for (var j in $scope.arrFields) {

                      if ($scope.arrFields[j].id == $scope.form[i].id) {
                        $scope.arrFields[j].selected = true;
                      }
                    }
                  }
                });
              }

              $scope.getIndicative = function () {
                restService.getAllIndicative().then(function (data) {
                  var array = [];
                  for (var i in data) {
                    if (data[i].phonecode != 1 && data[i].phonecode != 0 && data[i].phonecode != "") {
                      array.push("(+" + data[i].phonecode + ") " + data[i].name);
                    }
                  }
                  $scope.getCustomField();
                  if (!$scope.db) {
                    for (var i in $scope.arrFields) {
                      if ($scope.arrFields[i].id == "indicative") {
                        $scope.arrFields[i].valueDefault = array;
                      }
                      $scope.addComponent($scope.arrFields[i]);
                    }
                  }
                  $scope.complet = true;
                });
              }


              $scope.configForm = function (nameObj, valueObj) {
                $builder.updateConfig('default', nameObj, valueObj);
              }

              $scope.AddForm = function () {
                $scope.searchObjArray($scope.form, 'id', 'email').then(function (data) {
                  if (!data) {
                    notificationService.warning('El formulario debe tener por lo menos el correo');
                  }
                  var background = (angular.isDefined($scope.backgroundForm)) ? $scope.backgroundForm : null;
                  var objSend = {form: $scope.form, background: background};
                  if (typeof $scope.backgroundForm != "undefined") {
                    objSend.background = $scope.backgroundForm;
                  }
                  if (typeof $scope.fontForm != "undefined") {
                    objSend.fontForm = $scope.fontForm;
                  }
                  if (typeof $scope.sizeForm != "undefined") {
                    objSend.sizeForm = $scope.sizeForm;
                  }
                  if (typeof $scope.fontStyle != "undefined") {
                    objSend.fontStyle = $scope.fontStyle;
                  }

                  restService.saveForm($stateParams.id, objSend).then(function (data) {
                    $state.go('list');
                  });
                });
              }

              $scope.searchObjArray = function (array, item, compared) {
                var defer = $q.defer();
                var flag = false;
                for (var i in array) {
                  if (array[i][item] == compared) {
                    flag = true;
                    break;
                  }
                }
                defer.resolve(true);
                return defer.promise;
              }

              if (angular.isUndefined($stateParams.id) || $stateParams.id == "") {
                $state.go('create.describe');
              } else {
                restService.getInformationForm($stateParams.id).then(function (data) {
                  $scope.idContactList = data.idContactlist;
                  $scope.init();
                }).catch(function (data) {
                  $state.go('create.describe', {id: $rootScope.idForm});
                  notificationService.warning("El formulario no tiene una lista de contacto asignada.");
                });
              }
            }])
          .controller('ContactsController', ['$scope', 'restService', 'notificationService', '$stateParams', '$state', function ($scope, restService, notificationService, $stateParams, $state) {
              $scope.show = true;
              $scope.showList = true;
              $scope.contactlist = [{}];
              $scope.selected = [];
              $scope.progressbar = false;
              $scope.changestatus = true;
              $scope.form = {};
              $scope.showStatus = function (arr, value) {
                var selected = [];
                angular.forEach(arr, function (s) {
                  angular.forEach(value, function (c) {
                    if (s == c) {
                      selected.push(s);
                    }
                  });
                });
                return selected.length ? selected.join(', ') : 'empty';
              };
              $scope.updateUser = function (data, key, idContact) {
                restService.editContact(idContact, key, data, $scope.idContactlist).then(function (data) {
                  notificationService.primary(data.message);
                  $scope.getAll();
                }).catch(function (data) {
                  notificationService.error(data.message);
                  $scope.getAll();
                });
              };
              $scope.stringfieldsprimary = function (field) {
                var string = field;
                switch (field) {
                  case "name":
                    string = "Nombre";
                    break;
                  case "lastname":
                    string = "Apellido";
                    break;
                  case "email":
                    string = "Correo electronico";
                    break;
                  case "phone":
                    string = "Telefono";
                    break;
                  case "birthdate":
                    string = "Fecha de cumpleaños";
                    break;
                  case "indicative":
                    string = "Indicativo";
                    break;
                  case "description":
                    string = "Descripción";
                    break;
                }
                return string;
              };
              $scope.stringsearch = -1;
              $scope.searchcontacts = function () {
                $scope.stringsearch = $scope.search;
                $scope.getAll();
              };

              $scope.initial = 0;
              $scope.page = 1;
              $scope.forward = function () {
                $scope.progressbar = false;
                $scope.initial += 1;
                $scope.page += 1;
                $scope.getAll();
              };
              $scope.fastforward = function () {
                $scope.progressbar = false;
                $scope.initial = ($scope.contacts.total_pages - 1);
                $scope.page = $scope.contacts.total_pages;
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
              $scope.getAll = function () {
                $scope.progressbar = false;
                restService.getInformationForm($scope.idForm).then(function (data) {
                  $scope.form.name = data.name;
                  $scope.idContactlist = data.idContactlist;
                  restService.getAll($scope.initial, $scope.idContactlist, $scope.idForm, $scope.stringsearch).then(function (data) {
                    if (data.total == 0) {
                      $scope.loading = true;
                      $scope.show = false;
                      $scope.showList = true;
                    } else {
                      $scope.loading = false;
                      $scope.show = true;
                      $scope.showList = false;
                    }
                    $scope.contacts = data;
                    
                  });
                  restService.getAllIndicative().then(function (data) {
                    $scope.indicative = data;
                  });
                  restService.customfieldselect($scope.idContactlist).then(function (data) {
                    $scope.arr = [];
                    for (var i = 0; i < data.length; i++) {
                      if (data[i].type == "Select" || data[i].type == "Multiselect")
                        $scope.arr[data[i].idCustomfield] = data[i].value;
                    }
                    $scope.progressbar = true;
                  });
                });
              };
              $scope.changestatus = function (idContact) {
                restService.changestatus(idContact, $scope.idContactlist).then(function (data) {
                  $scope.getAll();
                  notificationService.primary(data.message);
                });
              };
              $scope.confirmDelete = function (idContact, idContactlist) {
                $scope.idContact = idContact;
                $scope.idContactlist = idContactlist;
                openModal();
              };

              if ($stateParams.id != '') {
                $scope.idForm = $stateParams.id;
                $scope.getAll();
              } else {
                $state.go('list');
              }
              $scope.deleteContact = function () {
                restService.deleteContact($scope.idContact, $scope.idContactlist).then(function (data) {
                  notificationService.warning(data.message);
                });
                $scope.getAll();
                closeModal();
              };

            }])
          .controller('main', ['$rootScope', '$scope', '$state', function ($rootScope, $scope, $state) {
              $scope.state = $state;
            }])
          .controller('reportController', ['$scope', 'notificationService', 'restService', '$stateParams', 'constantForms', function ($scope, notificationService, restService, $stateParams, constantForms) {

              
              $scope.data = {};
              $scope.misc = {
                show: true,
                showList: true,
                contactlist: [{}],
                selected: [],
                progressbar: false,
                changestatus: true,
                stringsearch: -1,
                initial: 0,
                page: 1,
                previewShow: false,
                input: [],
                categoriForm: "",
                createFor: "",
                dateCreated: "",
                numSuscription: "",
                updateFor: "",
                updateDate: "",
                fields: {},
                total: 0,
                nameForm: "",
                sizeArrayFields: 0,
                arrayTitlesFil: [],
                arrayDataContacts: []
              };

              $scope.functions = {
                searchcontacts: function () {
                  $scope.misc.stringsearch = $scope.search;
                  $scope.resService.suscripsforms($scope.idForm);
                },
                forward: function () {
                  $scope.misc.initial += 1;
                  $scope.misc.page += 1;
                  $scope.resService.suscripsforms($scope.idForm);
                },
                fastforward: function () {
                  $scope.misc.initial = ($scope.contacts[2].total_pages - 1);
                  $scope.misc.page = $scope.contacts[2].total_pages;
                  $scope.resService.suscripsforms($scope.idForm);
                },
                backward: function () {
                  $scope.misc.initial -= 1;
                  $scope.misc.page -= 1;
                  $scope.resService.suscripsforms($scope.idForm);
                },
                fastbackward: function () {
                  $scope.misc.initial = 0;
                  $scope.misc.page = 1;
                  $scope.resService.suscripsforms($scope.idForm);
                },
                listForms: function () {
                  restService.listForms($scope.misc.initial, $scope.filter).then(function (res) {
                    $scope.list = res;
                  });
                },
                getAllIndicative: function () {
                  restService.getAllIndicative().then(function (data) {
                    $scope.indicative = data;
                  });
                },
                dowloadReport: function () {
                  $scope.resService.dowloadReport();
                  var url = fullUrlBase + 'statistic/downloadexcel/' + 'Formulario_' + $scope.misc.nameForm;
                  location.href = url;
                }
              }

              $scope.resService = {
                dowloadReport: function () {
                  restService.dowloadReportContactsForm($scope.idForm).then(function () {
                  });
                },
                suscripsforms: function (paramIdForm) {
                  restService.suscripsforms(paramIdForm, $scope.misc.initial, $scope.misc.stringsearch).then(function (data) {
                    $scope.misc.total = data[1]['total'];

                    if (data[1].total == 0) {
                      $scope.loading = true;
                      $scope.misc.show = false;
                      $scope.misc.showList = true;
                    } else {
                      $scope.loading = false;
                      $scope.misc.show = true;
                      $scope.misc.showList = false;
                    }

                    if (data[0]['items'].idContacts != null) {
                      $scope.contacts = data;
                      $scope.misc.categoriForm = data[0].items['name'];
                      $scope.misc.createFor = data[0].items['createdBy'];
                      $scope.misc.dateCreated = data[0].items['created'];
                      $scope.misc.numSuscription = data[1]['total'];
                      $scope.misc.updateFor = data[0].items['updatedBy'];
                      $scope.misc.updateDate = data[0].items['updated'];
                      $scope.fieldsforms = data[3];
                      $scope.fieldspersonal = data[4].form;
                      $scope.contactInfoList = data[5].contactsinfo;
                      $scope.misc.nameForm = data[0].items['nameForm'];

                      angular.forEach($scope.fieldspersonal, function (value, key) {
                        if (value.id == 'encabezado' || value.id == 'button') {
                          $scope.fieldspersonal.splice(key, 1);
                        }
                      });
                      $scope.misc.arrayTitlesFil = [];
                      $scope.misc.sizeArrayFields = $scope.fieldspersonal.length;
                      angular.forEach($scope.fieldspersonal, function (value, key) {
                        if ($scope.misc.sizeArrayFields > 7) {
                          if (key <= 3) {
                            $scope.misc.arrayTitlesFil.push(value);
                          }
                        } else if ($scope.misc.sizeArrayFields <= 7) {
                          $scope.misc.arrayTitlesFil.push(value);
                        }
                      });

                      $scope.arrayAux = [];
                      $scope.misc.arrayDataContacts = [];
                      angular.forEach($scope.contactInfoList, function (value, key) {
                        var countContact = 0;
                        $scope.aux = {};
                        angular.forEach(value, function (value2, key2) {
                          if ($scope.misc.sizeArrayFields > 7) {
                            if (countContact <= 3) {
                              $scope.aux[key2] = value2;
                            }
                          } else {
                            $scope.aux[key2] = value2;
                            
                          }
                          countContact++;
                        });
                        $scope.misc.arrayDataContacts.push($scope.aux);
                      });
                    } else {
                      $scope.contacts = data;
                      $scope.misc.categoriForm = data[0].items['name'];
                      $scope.misc.createFor = data[0].items['createdBy'];
                      $scope.misc.dateCreated = data[0].items['created'];
                      $scope.misc.numSuscription = data[1]['total'];
                      $scope.misc.updateFor = data[0].items['updatedBy'];
                      $scope.misc.updateDate = data[0].items['updated'];
                      $scope.fieldsforms = data[3];
                      $scope.fieldspersonal = data[4].form;
                      $scope.contactInfoList = data[5].contactsinfo;
                      $scope.misc.nameForm = data[0].items['nameForm'];
                      notificationService.warning(constantForms.Notifications.Errors.errorNoneContactsInscriptForms);
                    }
                    $scope.misc.progressbar = true;
                  }).catch(function (error) {
                    console.log(error);
                    notificationService.error(error.message);
                  });
                }
              }

              if (typeof $stateParams.id != "undefined") {
                $scope.idForm = $stateParams.id;
                $scope.resService.suscripsforms($scope.idForm);
              }
            }])
})();
