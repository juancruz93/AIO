(function () {
  angular.module('survey.controllers', [])
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
          .controller('main', ['$rootScope', '$scope', function ($rootScope, $scope) { }])
          .controller('indexController', ['$scope', 'RestServices', 'notificationService', '$builder', '$validator', '$timeout', function ($scope, RestServices, notificationService, $builder, $validator, $timeout) {
              $scope.initial = 0;
              $scope.page = 1;
              $scope.previewShow = false;

              $scope.forward = function () {
                $scope.initial += 1;
                $scope.page += 1;
                $scope.listSurvey();
              };
              $scope.fastforward = function () {
                $scope.initial = ($scope.list.total_pages - 1);
                $scope.page = $scope.list.total_pages;
                $scope.listSurvey();
              };
              $scope.backward = function () {
                $scope.initial -= 1;
                $scope.page -= 1;
                $scope.listSurvey();
              };
              $scope.fastbackward = function () {
                $scope.initial = 0;
                $scope.page = 1;
                $scope.listSurvey();
              };

              $scope.listSurvey = function () {
                RestServices.listSurvey($scope.initial, $scope.filter).then(function (res) {
                  $scope.list = res;
                });
              };

              $scope.listSurvey();

              //-----------------------------------------------
              //Filtros
              $scope.filtername = function () {
                RestServices.listSurvey($scope.initial, $scope.filter).then(function (res) {
                  $scope.list = res;
                });
              };
              
               //-----------------------------------------------
              //Confirmacion Eliminar encuesta
              $scope.confirmDeleteSurvey = function (id) {
                $scope.idSurvey = id;
                $('#deletedSurvey').addClass('dialog--open');
              };

              //-----------------------------------------------
              //Eliminar encuesta
              $scope.deleteSurvey = function () {
//                RestServices.deleteCustomfield($scope.idCustomfield).then(function (data) {
////                 $window.location.href = '#/customfield/' + $scope.idContactlist;
////                 $window.location.href = '#/';
//                    notificationService.warning(data.message);
//                });
                RestServices.deleteSurvey($scope.idSurvey).then(function (data) {
                  $scope.listSurvey();
                  closeModal();
                  notificationService.warning(data.message);
                }).catch(function (data) {
                  notificationService.error(data.message);
                });
              };
              //-----------------------
              //Eliminar
              $scope.confirmDelete = function (id) {
                $scope.idAccountCategory = id;
                openModal();
              };

              $scope.deleteAccountCategory = function () {
                RestServices.delete($scope.idAccountCategory).then(function (res) {
                  notificationService.warning(res.message);
                  $scope.listSurvey();
                });
                closeModal();
              };

              $scope.previsualizar = function ($index) {
                $scope.previewShow = false;
                var json = JSON.parse($scope.list.items[$index].content.content);
                //console.log(json.content);
                $builder.setForm('sigmaSurvey', json.content);
                $("#preview").addClass('dialog--open');
                $scope.backgroundForm = json.background;

                $timeout(function () {
                  $scope.previewShow = true;
                }, 1000);
                //          $scope.previsualizar = angular.copy($scope.list.items[$index].html);
              };

              $scope.removeDialog = function (modal) {
                //          console.log(modal);
                $("#" + modal).removeClass('dialog--open');
              };

              $scope.validateSurvey = function () {
                $validator.validate($scope, 'sigmaSurvey')
                        .success(function () { })
                        .error(function () { });
              };

              $scope.linkGenerator = function (idSurvey) {
                RestServices.linkGenerator(idSurvey).then(function (data) {
                  $scope.linksurv = data.link;
                  btnCopy = document.getElementById("btnCopy");
                  link = document.getElementById("link");
                  
                  let changeSurvey = {status: "published", type: "public"};
                  RestServices.changeSurvey(changeSurvey, idSurvey).then(function (data) {
                    RestServices.changeType(changeSurvey, idSurvey).then(function (data) {
                      notificationService.primary("La encuesta se encuentra en estado publicado.");
                    });
                  });
                  

                  btnCopy.addEventListener('click', function (e) {
                    link.select();
                    if (document.execCommand("copy")) {
                      notificationService.success("El link ha sido copiado exitosamente");
                    } else {
                      notificationService.error("No se pudo copiar el link");
                    }
                    angular.element(document.querySelector(".linkgen")).modal('hide');
                  });
                  angular.element(document.querySelector(".linkgen")).modal('show');
                }).catch(function (data) {
                  notificationService.error(data.message);
                });
              };
              
              
              $scope.duplicateSurvey = function(idSurvey){
                RestServices.duplicateSurvey(idSurvey).then(function(data){
                  if (data.IdSurveyDuplicate !="" && data.IdSurveyDuplicate != null && typeof data.IdSurveyDuplicate != "undefined") {
                    location.href = fullUrlBase + 'survey/create#/basicinformation/'+data.IdSurveyDuplicate;
                  }else{
                     location.href = fullUrlBase + 'survey/';
                  }
                }).catch(function(data){
                  notificationService.error(data.message);
                });
              };
            }])
          .controller('createBasicInformationController', ['$scope', 'RestServices', 'notificationService', '$window', '$state', '$stateParams', '$rootScope', function ($scope, RestServices, notificationService, $window, $state, $stateParams, $rootScope) {

              $scope.initComponents = function () {
                $scope.data = {};
                $scope.data.status = true;
                $rootScope.status = true;
                $rootScope.route = $state.current.name;
                $scope.types = [{id: "public", name: "Pública"}, {id: "contact", name: "Por Contactos"}];
                $scope.showNewCategSurvey = false;
                $scope.newcategsurvey = "";

                getSurveyCategory();
              }

              function getSurveyCategory() {
                RestServices.getSurveyCategory().then(function (res) {
                  $scope.surveyCategory = res;
                });
              }
              ;

              $scope.saveBasicInformation = function () {

                if (typeof ($rootScope.idSurveyGet) != "undefined") {
                  RestServices.editSurvey($rootScope.idSurveyGet, $scope.data).then(function (res) {
                    notificationService.primary(res.message);
                    var view = "";
                    if (res.survey.status == "published" || res.survey.status == "expireded") {
                      view = "confirmation";
                    } else {
                      view = "survey";
                    }
                    $state.go(view, {
                      idSurvey: $rootScope.idSurveyGet
                    });
                  });
                } else {
                  RestServices.createSurvey($scope.data).then(function (res) {
                    notificationService.success(res.message);
                    $rootScope.idSurveyGet = res.survey.idSurvey;
                    $state.go("survey", {
                      idSurvey: $rootScope.idSurveyGet
                    });
                  });
                }
              };

              if ($stateParams.idSurvey) {
                if (!IsNumeric($stateParams.idSurvey)) {
                  notificationService.warning("No se ha encontrado la encuesta");
                  location.href = '#/basicinformation/';
                } else {
                  $rootScope.idSurveyGet = $stateParams.idSurvey;
                }
              }

              function IsNumeric(val) {
                return Number(parseFloat(val)) == val;
              }

              if ($rootScope.idSurveyGet && IsNumeric($rootScope.idSurveyGet)) {
                RestServices.findSurvey($rootScope.idSurveyGet).then(function (res) {
                  $scope.data.name = res.name;
                  $scope.data.description = res.description;
                  $scope.data.idCategorySurvey = res.idSurveyCategory;
                  $scope.data.status = res.status;
                  $scope.data.type = res.type;
                  $scope.data.messageFinal = res.messageFinal;
                  $scope.data.url = res.url;
                  $rootScope.status = ($scope.data.status == 'published' ? false : true);
                });
              }

              $scope.newCateg = {
                showNewCateg: function () {
                  $scope.showNewCategSurvey = true;
                },
                hideNewCateg: function () {
                  $scope.showNewCategSurvey = false;
                  $scope.newcategsurvey = "";
                },
                saveNewCateg: function () {
                  let data = {
                    name: $scope.newcategsurvey,
                    status: 1
                  };
                  RestServices.createCategorySurvey(data).then(function (response) {
                    getSurveyCategory();
                    $scope.data.idCategorySurvey = response.idSurveyCategory;
                    $scope.newCateg.hideNewCateg();
                    notificationService.success(response.message);
                  }).catch(function (error) {
                    notificationService.error(error.message);
                  });
                }
              };
            }])
          .controller("surveyController", ["$scope", "$rootScope", "RestServices", "notificationService", "$state", "$stateParams", '$builder', '$validator', '$mdSidenav', '$injector', '$mdDialog', 'surveyConstant', 'FileUploader', function ($scope, $rootScope, RestServices, notificationService, $state, $stateParams, $builder, $validator, $mdSidenav, $injector, $mdDialog, surveyConstant, FileUploader) {

              if (angular.isUndefined($stateParams.idSurvey) || $stateParams.idSurvey == "") {
                $state.go('describe');
                return false;
              } else {
                $rootScope.idSurveyGet = $stateParams.idSurvey;
              }
              $injector.get('$drag').setConfigData('droppables', {});
              $rootScope.route = $state.current.name;
              $builder.setConfig('titlePopover', '');
              $builder.setConfig('templateModalImage', surveyConstant.templateSelectedImage);
              $builder.setConfig('countIndex', true);
              $builder.setConfig('popoverPlacement', 'left');
              $builder.setConfig('templateFbBuilder', "<div>\n    <div class='fb-form-object-editable' ng-repeat=\"object in formObjects\"\n      fb-form-object-editable=\"object\"></div>\n</div>");
              $builder.setForm('default', []);
              $scope.formReady = false;
              $scope.fontColor = '';
              $scope.sizeStyle = '';
              $scope.fontStyle = '';
              $scope.countFont = 0;

              $scope.initWatch = function () {
                $scope.$watch('form', function (oldValue, newValue) {
                  if (oldValue !== newValue) {
                    $scope.form = $scope.form;
                  }
                }, true);
              };

              $scope.initForm = function () {
                RestServices.getSurveyContent($stateParams.idSurvey).then(function (data) {
                  if (data.response == "success") {
                    if (data.status == "published") {
                      $state.go("confirmation", {
                        idSurvey: $stateParams.idSurvey
                      });
                    }
                    var content = JSON.parse(data.content);
                    $builder.setForm('default', content.content);
                    $scope.initComponents();
                    $scope.initWatch();
                    
                    $scope.form = $builder.forms['default'];
                    $scope.setConfigScope(content);
                    $scope.formReady = true;
                  } else {
                    $builder.addFormObject('default', {
                      id: 'encabezado',
                      component: 'encabezado',
                      label: 'Titulo de la encuesta',
                      description: '',
                      placeholder: 'button',
                      required: true,
                      editable: true,
                      objExt: {fontOrien: "center", sizeStyle: "36px",notDb:true,location:"vertical-align"},
                      key: 'simple',
                    });
                    $builder.addFormObject('default', {
                      id: 'button',
                      component: 'button',
                      label: 'Aceptar',
                      description: '',
                      placeholder: 'button',
                      required: true,
                      editable: true,
                      objExt: {fontOrien: "center",notDb:true},
                    });
                    $scope.initComponents();
                    $scope.initWatch();
                    $scope.form = $builder.forms['default'];
                    $scope.formReady = true;
                  }
                }).catch(function (data) {
                  if (data == 403) {
                    $state.go('describe');
                  } else {
                    notificationService.error(data.message);
                  }
                });
              };

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
              };

              $scope.initForm();
              $scope.listOfLists = [];
              $scope.initComponents = function () {

                $scope.listItemsCollapse = [];
                _ref = $builder.components;
                _results = [];
                for (name in _ref) {
                  component = _ref[name];
                  if (component.group === 'Default') {
                    component.isNotTemplate = true;
                    if (component.objExt.configItem.id == "button") {
                      continue;
                    }
                    $scope.listItemsCollapse.push({
                      viewAdd: component.objExt.configItem.viewAdd,
                      id: component.objExt.configItem.id,
                      title: component.objExt.configItem.title,
                      icon: component.objExt.configItem.icon,
                      input: component.objExt.configItem.input,
                      hide: component.objExt.configItem.hide,
                      component: component
                    });
                  }
                }

                $scope.listItemsConfigVisual = [{
                    viewAdd: false,
                    title: "Color de fondo",
                    icon: "background-color.png",
                    html: "<h3>Fondo:</h3> <spectrum-colorpicker  ng-model=\"backgroundForm\" format=\"'rgb'\" " +
                            "options=\"{showInput: true, showAlpha: true, allowEmpty: true, showPalette: true, palette: [['black', 'white', 'blanchedalmond'],['rgb(255, 128, 0);', 'hsv 100 70 50', 'lightyellow']]}\">" +
                            "</spectrum-colorpicker>"
                  },
                  {
                    viewAdd: false,
                    title: "Color de fuente",
                    icon: "color-text.png",
                    html: "<spectrum-colorpicker  ng-model=\"fontForm\" format=\"'rgb'\" ng-change=\"configForm('fontColor',fontForm)\"" +
                            "options=\"{showInput: true, showAlpha: true, allowEmpty: true, showPalette: true, palette: [['black', 'white', 'blanchedalmond'],['rgb(255, 128, 0);', 'hsv 100 70 50', 'lightyellow']]}\">" +
                            "</spectrum-colorpicker>"
                  },
                  {
                    viewAdd: false,
                    title: "Tamaño de fuente",
                    icon: "size-text.png",
                    html: "<select class=\"form-control\" ng-model=\"sizeForm\" ng-change=\"configForm('sizeStyle',sizeForm)\">" +
                            "<option value=\"8px\">8</option>" +
                            "<option value=\"10px\">10</option>" +
                            "<option value=\"12px\">12</option>" +
                            "<option value=\"14px\">14</option>" +
                            "<option value=\"18px\">18</option>" +
                            "<option value=\"24px\">24</option>" +
                            "<option value=\"36px\">36</option>" +
                            "</select>"
                  },
                  {
                    viewAdd: false,
                    title: "Tipo de fuente",
                    icon: "text-font.png",
                    html: "<select class=\"form-control\" ng-model=\"fontStyle\" ng-change=\"configForm('fontStyle',fontStyle)\">" +
                            "<option value=\"Arial\">Arial</option>" +
                            "<option value=\"Courier New\">Courier New</option>" +
                            "<option value=\"Verdana\">Verdana</option>" +
                            "<option value=\"Comic Sans MS\">Comic Sans MS</option>" +
                            "<option value=\"Georgia\">Georgia</option>" +
                            "<option value=\"Times New Roman\">Times New Roman</option>" +
                            "</select>"
                  },
                  {
                    viewAdd: false,
                    title: "Subir imagen",
                    icon: "picture.png",
                    actions: {template: "imageUpload", modal: true}
                  }
                ];

                $scope.listItemsConfigEncabezado = [
                  {viewAdd: false, title: "Titulo", icon: "text-box.png", key: "simple"},
                  {viewAdd: false, title: "Titulo/Logo", icon: "text-box.png", key: "el"},
                  {viewAdd: false, title: "Logo/Titulo", icon: "text-box.png", key: "le"},
                  {viewAdd: false, title: "Logo", icon: "text-box.png", key: "logo"},
                ]

                $scope.listItemsConfigFooter = [
                  {viewAdd: false, title: "Pie Pagina", icon: "text-box.png", key: "simple"},
                  {viewAdd: false, title: "Pie Pagina/Logo", icon: "text-box.png", key: "fl"},
                  {viewAdd: false, title: "Logo/Pie Pagina", icon: "text-box.png", key: "lf"},
                  {viewAdd: false, title: "Logo", icon: "text-box.png", key: "logo"},
                ]

                $scope.listOfLists = [$scope.listItemsCollapse, $scope.listItemsConfigVisual, $scope.listItemsConfigEncabezado, $scope.listItemsConfigFooter];
              };

              $scope.htmlConf = "";
              $scope.titleConf = "";

              //Uploader
              var uploader = $scope.uploader = new FileUploader({
                url: surveyConstant.urlUploadFile
              });

              uploader.onAfterAddingFile = function (fileItem) {
                $scope.uploader.queue[$scope.uploader.queue.length - 1].upload();
              };

              $scope.viewBtnAddEnter = function (index, i) {
                $scope.listOfLists[i][index].viewAdd = true;
              };

              $scope.viewBtnAddLeave = function (index, i) {
                $scope.listOfLists[i][index].viewAdd = false;
              };

              $scope.configForm = function (nameObj, valueObj) {
                if(nameObj == 'fontColor'){
                  $scope.fontColor = valueObj;
                }
                if(nameObj == 'sizeStyle'){
                  $scope.sizeStyle = valueObj;
                }
                if(nameObj == 'fontStyle'){
                  $scope.fontStyle = valueObj;
                }
                $builder.updateConfig('default', nameObj, valueObj);
              };

              $scope.toggleRight = function (navID, index) {
                $scope.htmlConf = $scope.listItemsConfigVisual[index].html;
                $scope.titleConf = $scope.listItemsConfigVisual[index].title;
                // Component lookup should always be available since we are not using `ng-if`
                if (typeof $scope.listItemsConfigVisual[index].actions != "undefined") {
                  if ($scope.listItemsConfigVisual[index].actions.modal) {

                    var templateModal = surveyConstant[$scope.listItemsConfigVisual[index].actions.template];
                    //                    console.log(templateModal);

                    document.body.scrollTop = 0;
                    var parentEl = angular.element(document.body);

                    $mdDialog.show({
                      scope: $scope.$new(),
                      parent: parentEl,
                      template: templateModal,
                      controller: DialogController,
                      locals: {
                        items: false
                      }
                    });
                  }
                } else {
                  $mdSidenav(navID)
                          .toggle()
                          .then(function () {
                            //                            console.warn("toggle " + navID + " is done");
                          });
                }
              };

              $scope.isOpenRight = function () {
                return $mdSidenav('right').isOpen();
              };

              $scope.close = function () {
                // Component lookup should always be available since we are not using `ng-if`
                $mdSidenav('right').close()
                        .then(function () {
                          //                          console.warn("close LEFT is done");
                        });
              };

              $scope.AddSurveyContent = function () {
                $(angular.element(document.getElementsByClassName("popover in"))).popover('hide');
                var data = {
                  content: $scope.form
                };
                if (typeof $scope.backgroundForm != "undefined") {
                  data.background = $scope.backgroundForm;
                }
                if (typeof $scope.fontForm != "undefined") {
                  data.fontForm = $scope.fontForm;
                }
                if (typeof $scope.sizeForm != "undefined") {
                  data.sizeForm = $scope.sizeForm;
                }
                if (typeof $scope.fontStyle != "undefined") {
                  data.fontStyle = $scope.fontStyle;
                }

                RestServices.createContentSurvey($stateParams.idSurvey, data).then(function (data) {
                  notificationService.success(data.message);
                  $state.go("confirmation", {
                    idSurvey: $rootScope.idSurveyGet
                  });
                }).catch(function (data) {
                  notificationService.error(data.message);
                });
              };
              
              $scope.AddSurveyContentTmp = function () {
                //$(angular.element(document.getElementsByClassName("popover in"))).popover('hide');
                var data = {
                  content: $scope.form
                };
                if (typeof $scope.backgroundForm != "undefined") {
                  data.background = $scope.backgroundForm;
                }
                if (typeof $scope.fontForm != "undefined") {
                  data.fontForm = $scope.fontForm;
                }
                if (typeof $scope.sizeForm != "undefined") {
                  data.sizeForm = $scope.sizeForm;
                }
                if (typeof $scope.fontStyle != "undefined") {
                  data.fontStyle = $scope.fontStyle;
                }

                RestServices.createContentSurvey($stateParams.idSurvey, data)
                .then(function (data) {
                    
                }).catch(function (data) {
                  
                });
              };

              $scope.addComponent = function (index, event) {
                var selection = $scope.listItemsCollapse[index];
                $scope.listItemsCollapse[index].viewAdd = false;
                selection.component.component = selection.component.objExt.configItem.nameComponent;
                if (typeof selection.component.id == "number") {
                  delete(selection.component.id);
                }
                $builder.addFormObject('default', selection.component);
                if($scope.sizeStyle){
                  $builder.updateConfig('default', 'sizeStyle', $scope.sizeStyle);
                }
                if($scope.fontStyle){
                  $builder.updateConfig('default', 'fontStyle', $scope.fontStyle);
                }
                if($scope.fontColor){
                  $builder.updateConfig('default', 'fontColor', $scope.fontColor);
                }
              }
              $scope.selectionTittle = function (index) {
                var selection = $scope.listItemsConfigEncabezado[index];
                var objComponent = {
                  id: 'encabezado',
                  component: '',
                  label: 'Titulo de la encuesta',
                  description: '',
                  placeholder: 'button',
                  required: true,
                  editable: true,
                  objExt: {fontOrien: "center", sizeStyle: "36px",location:"vertical-align"}
                };
                if ($scope.form[0].id == "encabezado") {
                  objComponent = $scope.form[0];
                  $builder.removeFormObject('default', 0, $scope.form[0]);
                }
                if (typeof objComponent.objExt.srcImage == "undefined") {
                  objComponent.objExt.srcImage = fullUrlBase + "/images/general/logoDefault.png";
                }

                switch (selection.key) {
                  case "simple":
                    objComponent.component = 'encabezado';
                    $builder.addFormObject('default', objComponent);
                    break;
                  case "el":
                    objComponent.component = 'encabezado-logo';
                    $builder.addFormObject('default', objComponent);
                    break;
                  case "le":
                    objComponent.component = 'logo-encabezado';
                    $builder.addFormObject('default', objComponent);
                    break;
                  case "logo":
                    objComponent.component = 'logo';
                    $builder.addFormObject('default', objComponent);
                    break;
                }
              }

              $scope.selectionFooter = function (index) {
                var selection = $scope.listItemsConfigFooter[index];
                var objComponent = {
                  id: 'footer',
                  component: '',
                  label: 'Pie de pagina de la encuesta',
                  description: '',
                  placeholder: 'button',
                  required: true,
                  editable: true,
                  objExt: {fontOrien: "center", sizeStyle: "36px",notDb:true,location:"vertical-align"},
                };
                var lastForm = $scope.form.length - 1;
                if ($scope.form[lastForm].id == "footer") {
                  objComponent = $scope.form[lastForm];
                  $builder.removeFormObject('default', lastForm, $scope.form[lastForm]);
                }
                if (typeof objComponent.objExt.srcImage == "undefined") {
                  objComponent.objExt.srcImage = fullUrlBase + "/images/general/logoDefault.png";
                }

                switch (selection.key) {
                  case "simple":
                    objComponent.component = 'footer';
                    $builder.addFormObject('default', objComponent);
                    break;
                  case "fl":
                    objComponent.component = 'footer-logo';
                    $builder.addFormObject('default', objComponent);
                    break;
                  case "lf":
                    objComponent.component = 'logo-footer';
                    $builder.addFormObject('default', objComponent);
                    break;
                  case "logo":
                    objComponent.component = 'logofooter';
                    $builder.addFormObject('default', objComponent);
                    break;
                }

              }

              //Controller Dialog Adjuntos
              function DialogController($scope, $mdDialog, items) {
                $parent = $scope.$parent;
                $scope.imageAccount = $parent.imageAccount;
                $scope.idAccount = $parent.idAccount;
                $scope.page = 0;

                $scope.funcUniversal = {
                  selectedImage: function ($index) {


                  }
                }
                $scope.closeDialog = function () {
                  $mdDialog.hide();
                  if (!items) {
                    $parent.universalAction.getImagen();
                  }
                }
              }
              // Se hace un inverlo de 5 segundos para guardar la encuesta.
              setInterval(function(){ 
                $scope.AddSurveyContentTmp();
              }, 5000);  
            }])
          .controller('confirmationController', ['$scope', "$state", "$rootScope", 'RestServices', 'notificationService', '$window', '$stateParams', function ($scope, $state, $rootScope, RestServices, notificationService, $window, $stateParams) {
              if (angular.isUndefined($stateParams.idSurvey) || $stateParams.idSurvey == "") {
                $state.go('describe');
              } else {
                $rootScope.idSurveyGet = $stateParams.idSurvey;
              }
              $rootScope.route = $state.current.name;
              $scope.survey = {};
              if ($rootScope.idSurveyGet && angular.isNumber(parseInt($rootScope.idSurveyGet))) {
                RestServices.findSurvey($rootScope.idSurveyGet).then(function (res) {
                  $scope.survey = res;
                  document.getElementById("startDate").value = $scope.survey.startDate;
                  document.getElementById("endDate").value = $scope.survey.endDate;
                });
              }

              $scope.saveConfirmation = function (opt) {

                var data = {};
                if (opt == 1) {
                  data = {
                    idSurvey: $scope.survey.idSurvey,
                    startDate: document.getElementById("startDate").value,
                    endDate: document.getElementById("endDate").value,
                    status: 1
                  };
                } else if (opt == 2) {
                  var now = $("#startDate").val();
                  var then = $("#endDate").val();
                  if (then == "Invalid date" || now == "Invalid date") {
                    notificationService.error("La fecha de de inicio y expiración no puede estar vacía");
                    return;
                  }
                  if (now > then) {
                    notificationService.error("La fecha y hora de expiración no puede ser anterior a la fecha inicial");
                    return;
                  }
                  if (moment(now).format("YYYY/MM/DD") == moment(then).format("YYYY/MM/DD")) {
                    if (moment(now).format("HH") == moment(then).format("HH")) {
                      var minutesNoew = moment(now).format("mm");
                      var minutesThen = moment(then).format("mm");
                      var subtraction = minutesThen - minutesNoew;
                      if (subtraction < 30) {
                        notificationService.error("La fecha de expiración no debe ser menor a 30 minutos a la fecha de inicio");
                        return;
                      }
                    }
                  }
                  data = {
                    idSurvey: $scope.survey.idSurvey,
                    startDate: document.getElementById("startDate").value,
                    endDate: document.getElementById("endDate").value,
                    status: 3
                  };
                }

                RestServices.saveConfirmation(data).then(function (data) {
                  notificationService.success(data.message);
                  angular.element(document.querySelector(".bs-example-modal-lg")).modal('hide');
                  angular.element(document.querySelector(".published")).modal('hide');
                  $state.go('share', {idSurvey: $rootScope.idSurveyGet});
                }).catch(function (data) {
                  angular.element(document.querySelector(".bs-example-modal-lg")).modal('hide');
                  angular.element(document.querySelector(".published")).modal('hide');
                  notificationService.error(data.message);
                });
              };

              $scope.loader = false;
              $scope.linkGenerator = function () {
                $scope.loader = true;
                RestServices.linkGenerator($scope.survey.idSurvey).then(function (data) {
                  $scope.linksurv = data.link;
                  btnCopy = document.getElementById("btnCopy");
                  link = document.getElementById("link");

                  btnCopy.addEventListener('click', function (e) {
                    link.select();
                    if (document.execCommand("copy")) {
                      notificationService.success("El link ha sido copiado exitosamente");
                    } else {
                      notificationService.error("No se pudo copiar el link");
                    }
                    angular.element(document.querySelector(".linkgen")).modal('hide');
                  });
                  angular.element(document.querySelector(".linkgen")).modal('show');
                  $scope.loader = false;
                }).catch(function (data) {
                  notificationService.error(data.message);
                });
              };
              $scope.validationDateExpiration = function () {
                var data = moment($("#startDate").val()).format('YYYY-MM-DD HH:mm');
                var FormatDate = moment($("#endDate").val()).format('YYYY-MM-DD HH:mm');
                var m = moment(data).add('minutes', 30)._d;
                var dateObj = new Date(m);
                var momentObj = moment(dateObj);
                var momentString = momentObj.format('YYYY-MM-DD HH:mm');

                if (FormatDate == "Invalid date") {
                  $("#datetimepicker1").datetimepicker('setDate', momentString);
                }
              }
            }])
          .controller('share', ['$scope', "$state", "$rootScope", 'RestServices', 'notificationService', '$window', '$stateParams', '$mdDialog', 'surveyConstant', '$FB', '$q', '$timeout', 'moment', 'mailService', '$builder', function ($scope, $state, $rootScope, RestServices, notificationService, $window, $stateParams, $mdDialog, surveyConstant, $FB, $q, $timeout, $moment, mailService, $builder) {

              if (angular.isUndefined($stateParams.idSurvey) || $stateParams.idSurvey == "") {
                $state.go('describe');
                return;
              } else {
                $rootScope.idSurveyGet = $stateParams.idSurvey;
                $rootScope.route = $state.current.name;
              }

              $scope.global = {
                imgEmail: fullUrlBase + 'images/general/emailShare.png',
                imgFb: fullUrlBase + 'images/general/fbShare.png',
                imgLink: fullUrlBase + 'images/general/linkShare.png',
                idSurvey: $stateParams.idSurvey,
                searchFbActive: function () {
                  if ($FB.loaded) {
                    this.searchSesion();
                  } else {
                    $timeout($scope.global.searchFbActive, 1000);
                  }
                },
                searchSesion: function () {
                  //                  console.log($FB);
                },
                getSurvey: function () {
                  RestServices.findSurvey($scope.global.idSurvey).then(function (res) {
                    let endDate = $moment(res.endDate).utc("-0500");
                    let now = $moment().utc("-0500")
                    if (res.startDate == null || res.endDate == null) {
                      $state.go('confirmation', {idSurvey: $scope.global.idSurvey});
                    }
                    if (endDate.unix() <= now.unix()) {
                      $state.go('confirmation', {idSurvey: $scope.global.idSurvey});
                    }
                    $scope.global.infoSurvey = res;
                    RestServices.filterSurveyCategory(res.idSurveyCategory).then(function (data) {
                      $scope.global.infoSurvey.category = data.data;
                      //                      console.log($scope.global.infoSurvey);
                    });
                    RestServices.getSurveyContent($scope.global.idSurvey).then(function (data) {
                      if (data.content == "") {
                        $state.go('survey', {idSurvey: $scope.global.idSurvey});
                      }
                      $scope.global.infoSurvey.content = data;
                      //                      console.log($scope.global.infoSurvey);
                    })
                  });
                },
                previsualizar: function () {
                  $scope.previewShow = false;
                  var json = JSON.parse($scope.global.infoSurvey.content.content);
                  $builder.setForm('sigmaSurvey', json.content);
                  //$("#preview").addClass('dialog--open');
                  $('#preview').modal({show: 'false'});
                  $scope.backgroundForm = json.background;

                  $timeout(function () {
                    $scope.previewShow = true;
                  }, 1000);
                },
                validateSurvey: function () {
                  $validator.validate($scope, 'sigmaSurvey')
                          .success(function () { })
                          .error(function () { });
                },
                removeDialog: function (modal) {
                  //          console.log(modal);
                  $("#" + modal).removeClass('dialog--open');
                }
              }

              $scope.returnModal = {};
              /*
               Controllers modals
               */
              $scope.controllers = {
                email: function ($scope, $mdDialog) {
                  $scope.selected = {
                    mailtemplate: null,
                    mailcategory: null,
                    senderName: null,
                    senderEmail: null,
                    subject: null,
                    replyto: null,
                    listDestinatary: {id: 1, name: "Lista de contacto"},
                    destinatary: [],
                  };
                  $scope.lists = {
                    listSMailTemplate: [],
                    listSMailCategory: [],
                    emailname: [],
                    emailsend: [],
                    listDestinatary: [{id: 1, name: "Lista de contacto"}, {id: 2, name: "Segmentos"}],
                    destinatary: [],
                  };
                  $scope.emailSender = {
                    showInputName: false,
                    showSelectName: true,
                    showIconsSaveName: false,
                    showIconsName: true,
                    changeStatusInputName: function () {
                      if (!$scope.emailSender.showInputName) {
                        $scope.emailSender.showInputName = true;
                        $scope.emailSender.showSelectName = false;
                        $scope.emailSender.showIconsName = false;
                        $scope.emailSender.showIconsSaveName = true;
                      } else {
                        $scope.emailSender.showInputName = false;
                        $scope.emailSender.showSelectName = true;
                        $scope.emailSender.showIconsName = true;
                        $scope.emailSender.showIconsSaveName = false;
                      }
                    },
                    saveEmail: function () {
                      var data = {email: $scope.emailSender.senderEmail};
                      //                      console.log(data);
                      RestServices.addEmailSender(data).then(function (res) {
                        notificationService.success(res['msg']);
                        $scope.senderName = "";
                        $scope.func.getEmailSender(res['idEmailsender']);
                        $scope.emailSender.changeStatusInputName();
                      });
                    }
                  };

                  $scope.nameSender = {
                    showInputName: false,
                    showSelectName: true,
                    showIconsSaveName: false,
                    showIconsName: true,
                    changeStatusInputName: function () {
                      if (!$scope.nameSender.showInputName) {
                        $scope.nameSender.showInputName = true;
                        $scope.nameSender.showSelectName = false;
                        $scope.nameSender.showIconsName = false;
                        $scope.nameSender.showIconsSaveName = true;
                      } else {
                        $scope.nameSender.showInputName = false;
                        $scope.nameSender.showSelectName = true;
                        $scope.nameSender.showIconsName = true;
                        $scope.nameSender.showIconsSaveName = false;
                      }
                    },
                    saveName: function () {
                      var data = {name: $scope.nameSender.senderName};

                      RestServices.addEmailName(data).then(function (res) {
                        notificationService.success(res['msg']);
                        $scope.senderName = "";
                        $scope.func.getNameSender(res['idNameSender']);
                        $scope.nameSender.changeStatusInputName();
                      });
                    }
                  };
                  var $parent = $scope.$parent;
                  $scope.survey = angular.copy($parent.global.infoSurvey);
                  $scope.dateMoment = {
                    initialSurvey: $moment($scope.survey.startDate).utc("-0500"),
                    endSurvey: $moment($scope.survey.endDate).utc("-0500")
                  }
                  $scope.func = {
                    getallmailtemplate: function () {
                      mailService.getallmailtemplate().then(function (data) {
                        $scope.lists.listSMailTemplate = data;
                      });
                    },
                    changeSelectedMailTemplate: function (filter) {
                      mailService.getallmailtemplatebyfilter(filter).then(function (data) {
                        //                        console.log(data);
                        $scope.lists.listSMailTemplate = data;
                      });
                    },
                    getNameSender: function (id) {
                      mailService.getemailname().then(function (data) {
                        $scope.lists.emailname = data;
                        if (typeof id !== "undefined") {
                          for (i in $scope.lists.emailname) {
                            if ($scope.lists.emailname[i].idNameSender == id) {
                              $scope.selected.senderName = $scope.lists.emailname[i];
                            }
                          }
                        }
                      });
                    },
                    getEmailSender: function (id) {
                      mailService.getemailsend().then(function (data) {
                        $scope.lists.emailsend = data;
                        if (typeof id !== "undefined") {
                          for (i in $scope.lists.emailsend) {
                            if ($scope.lists.emailsend[i].idEmailsender == id) {
                              $scope.selected.senderEmail = $scope.lists.emailsend[i];
                            }
                          }
                        }
                      });
                    },
                    getMailCategory: function () {
                      mailService.getallmailcategory().then(function (data) {
                        $scope.lists.listSMailCategory = data;
                      });
                    },
                    getContactList: function () {
                      mailService.getContactlist().then(function (data) {
                        $scope.lists.destinatary = data;
                      });
                    },
                    getSegment: function () {
                      mailService.getSegment().then(function (data) {
                        $scope.lists.destinatary = data;
                      });
                    },
                    changeDestinatary: function (id) {
                      $scope.lists.destinatary = [];
                      //                      console.log(id);
                      switch (id.id) {
                        case 1:
                          $scope.func.getContactList();
                          break;

                        case 2:
                          $scope.func.getSegment();
                          break;
                      }
                    },
                    setLists: function (data) {
                      $scope.lists.listSMailTemplate = data[0];
                      $scope.lists.emailname = data[1];
                      $scope.lists.emailsend = data[2];
                      $scope.lists.listSMailCategory = data[3];
                      $scope.lists.destinatary = data[4];
                    },
                    init: function () {
                      let arrPromise = [];
                      arrPromise.push(mailService.getallmailtemplate());
                      arrPromise.push(mailService.getemailname());
                      arrPromise.push(mailService.getemailsend());
                      arrPromise.push(mailService.getallmailcategory());
                      arrPromise.push(mailService.getContactlist());

                      $q.all(arrPromise).then(function (data) {
                        $scope.func.setLists(data)
                      });
                    },
                    initInputTime: function () {
                      $("#datetimepicker,#datetimepicker1").datetimepicker({
                        format: 'yyyy-MM-dd hh:mm',
                        language: 'es',
                        startDate: new Date()
                      });
                    },
                    validate: function (now) {
                      let objMail = {};

                      if (now) {
                        angular.forEach($scope.selected, function (value, key) {
                          if (angular.equals(null, value) && key != "replyto") {
                            error = "Por favor diligenciar el formulario.";
                          }
                          if (key == "subject" && value == "") {
                            error = "Por favor diligenciar el formulario.";
                          }

                          if (key == "destinatary" && value.length <= 0) {
                            error = "Por favor diligenciar el formulario.";
                          }
                        });

                        if (error) {
                          notificationService.error(error);
                        } else {
                          objMail = $scope.selected;
                          objMail.survey = $scope.survey;
                          $scope.func.sendMail(objMail);
                        }

                      } else {
                        var scheduleDate = $moment($("#scheduleDate").val()).utc("-0500");
                        var now = $moment().utc("-0500");
                        var error = false;
                        if ($scope.dateMoment.initialSurvey.unix() > scheduleDate.unix()) {
                          error = "la fecha de programacion no puede ser menor a la fecha inicial de la encuesta.";
                        }

                        if ($scope.dateMoment.endSurvey.unix() < scheduleDate.unix()) {
                          error = "la fecha de programacion no puede ser mayor a la fecha final de la encuesta.";
                        }

                        if (scheduleDate.diff(now, 'minutes') < 0) {
                          error = "la fecha de programacion no puede ser menor a la fecha actual.";
                        }

                        angular.forEach($scope.selected, function (value, key) {
                          if (angular.equals(null, value) && key != "replyto") {
                            error = "Por favor diligenciar el formulario.";
                          }
                          if (key == "subject" && value == "") {
                            error = "Por favor diligenciar el formulario.";
                          }
                        });

                        if (error) {
                          notificationService.error(error);
                        } else {
                          objMail = $scope.selected;
                          objMail.survey = $scope.survey;
                          objMail.scheduleDate = $("#scheduleDate").val();
                          $scope.func.sendMail(objMail);
                        }
                      }
                    },
                    sendMail: function (mailObj) {
                      RestServices.sendMail(mailObj).then(function (data) {
                        $mdDialog.hide();
                        notificationService.primary(data.message);
                      });
                    }
                  }
                  $scope.hide = function () {
                    $mdDialog.hide();
                  };

                  $scope.closeDialog = function () {
                    $mdDialog.hide();
                  }
                  $scope.func.init();
                  $timeout($scope.func.initInputTime, 1000);
                },
                link: function ($scope, $mdDialog) {
                  var $parent = $scope.$parent;
                  RestServices.linkGenerator($parent.global.idSurvey).then(function (data) {
                    $scope.linksurv = data.link;
                    btnCopy = document.getElementById("btnCopy");
                    link = document.getElementById("link");

                    btnCopy.addEventListener('click', function (e) {
                      link.select();
                      if (document.execCommand("copy")) {
                        notificationService.success("El link ha sido copiado exitosamente");
                      } else {
                        notificationService.error("No se pudo copiar el link");
                      }
                      angular.element(document.querySelector(".linkgen")).modal('hide');
                    });
                    angular.element(document.querySelector(".linkgen")).modal('show');
                  }).catch(function (data) {
                    notificationService.error(data.message);
                  });

                  $scope.closeDialog = function () {
                    $mdDialog.hide();
                  }
                },
                social: function ($scope, $mdDialog, pages, $timeout) {
                  var $parent = $scope.$parent;
                  RestServices.linkGenerator($parent.global.idSurvey).then(function (data) {
                    $scope.linksurv = data.link;
                  }).catch(function (data) {
                    notificationService.error(data.message);
                  });

                  $scope.pages = pages
                  $scope.data = {};
                  $scope.pageSelected = false;
                  $scope.survey = angular.copy($parent.global.infoSurvey);
                  $scope.dateMoment = {
                    initialSurvey: $moment($scope.survey.startDate),
                    endSurvey: $moment($scope.survey.endDate)
                  }
                  $scope.selectedPage = function (page) {
                    $scope.pageSelected = page;
                    $timeout($scope.initInputTime, 1000);
                  }
                  $scope.initInputTime = function () {
                    $("#datetimepicker,#datetimepicker1").datetimepicker({
                      format: 'yyyy-MM-dd hh:mm',
                      language: 'es',
                      startDate: new Date()
                    });
                  }

                  $scope.validate = function (now) {
                    let objPublish = {};
                    let objInject = {};
                    if (now) {
                      if ($scope.data.description != "undefined" && $scope.data.description != "") {
                        objPublish.message = $scope.data.description;
                      }
                      //objPublish.link = "https://aio.sigmamovil.com/survey/showsurvey/18/";  para localhost
                      objPublish.link = $scope.linksurv;
                      objPublish.access_token = $scope.pageSelected.access_token;
                      objInject.scheduleDate = 'now';
                      $scope.publish(objPublish, $scope.pageSelected.id, objInject);
                    } else {
                      var scheduleDate = $moment($("#scheduleDate").val()).utc("-0500");
                      var now = $moment().utc("-0500");
                      //                      console.log(now.diff(scheduleDate, 'minutes'));
                      if ($scope.dateMoment.initialSurvey.unix() > scheduleDate.unix()) {
                        error = "la fecha de programacion no puede ser menor a la fecha inicial de la encuesta.";
                        notificationService.error(error);
                        return;
                      }

                      if ($scope.dateMoment.endSurvey.unix() < scheduleDate.unix()) {
                        error = "la fecha de programacion no puede ser mayor a la fecha final de la encuesta.";
                        notificationService.error(error);
                        return;
                      }

                      //                      if(now.unix() > scheduleDate.unix()){
                      //                        error = "la fecha de programacion no puede ser mayor a la fecha actual.";
                      //                        console.log(error);
                      //                        return;
                      //                      }
                      if (scheduleDate.diff(now, 'minutes') > 15) {
                        objPublish.published = false;
                        objPublish.scheduled_publish_time = $moment($("#scheduleDate").val()).unix();
                      } else {
                        if (scheduleDate.diff(now, 'minutes') < 0) {
                          error = "la fecha de programacion no puede ser menor a la fecha actual.";
                          notificationService.error(error);
                          return;
                        }
                      }

                      if ($scope.data.description != "undefined" && $scope.data.description != "") {
                        objPublish.message = $scope.data.description;
                      }
                      //objPublish.link = "https://aio.sigmamovil.com/survey/showsurvey/18/";  para localhost
                      objPublish.link = $scope.linksurv;
                      objPublish.access_token = $scope.pageSelected.access_token;
                      objInject.scheduleDate = scheduleDate.unix();
                      $scope.publish(objPublish, $scope.pageSelected.id, objInject);
                    }
                  }

                  $scope.isDisabled = false;
                  $scope.publish = function (data, id, objInject) {
                    var access_token_page = data.access_token;
                    let url = `/${id}/feed`;
                    $scope.isDisabled = true;
                    $FB.api(url,
                            "POST", data,
                            function (response) {
                              if (typeof response.error == "undefined") {
                                data.idPage = $scope.pageSelected.id;
                                data.idSurvey = $scope.survey.idSurvey;
                                data.scheduledDate = objInject.scheduleDate;
                                data.type = 'facebook';
                                data.idPublish = response.id;
                                data.description = data.message;
                                RestServices.savePost(data).then(function (data) {
                                  let changeSurvey = {status: "published", type: "public"};
                                  RestServices.changeSurvey(changeSurvey, $scope.survey.idSurvey).then(function (data) {
                                    RestServices.changeType(changeSurvey, $scope.survey.idSurvey).then(function (data) {

                                      $mdDialog.hide();
                                      notificationService.primary("La encuesta y la publicación fueron guardados exitosamente.");
                                    }).catch(function (data) {

                                      $FB.api(
                                              "/" + response.id,
                                              "DELETE",
                                              {access_token: access_token_page},
                                              function (response) {
                                                $scope.isDisabled = false;
                                                return false;
                                              }
                                      );
                                    });
                                  }).catch(function (data) {

                                    $FB.api(
                                            "/" + response.id,
                                            "DELETE",
                                            {access_token: access_token_page},
                                            function (response) {
                                              $scope.isDisabled = false;
                                              return false;

                                            }
                                    );
                                  });

                                });
                              }
                            });
                  }

                  $scope.hide = function () {
                    $mdDialog.hide();
                  };

                  $scope.closeDialog = function () {
                    $mdDialog.hide();
                  }
                }
              }
              /*
               *  Social AppFacebook
               */
              $scope.appFacebook = {
                checkPermissionFacebookPage: function (page) {
                  var defer = $q.defer();
                  var promise = defer.promise;
                  var arrPageReturn = {data: []};
                  for (var i in page) {
                    if (typeof page[i].perms.indexOf(surveyConstant.permissionFBAdmin) != "number" ||
                            typeof page[i].perms.indexOf(surveyConstant.permissionFBBasicAdmin) != "number" ||
                            typeof page[i].perms.indexOf(surveyConstant.permissionFBCreateContent) != "number") {
                      continue;
                    }
                    arrPageReturn.data.push(page[i]);
                  }
                  if (arrPageReturn.data.length <= 0) {
                    defer.reject(surveyConstant.Notifications.Error.LengthFanPage);
                  }
                  defer.resolve(arrPageReturn);
                  return promise;
                },
                login: function (objPage) {
                  $FB.getLoginStatus(function (response) {
                    if (response.status === 'connected') {
                      $FB.api('/me/accounts', function (response) {
                        if (response.error) {
                          notificationService.error(surveyConstant.Notifications.Error.ApiFacebook);
                          return;
                        }
                        $scope.appFacebook.checkPermissionFacebookPage(response.data).then(function (response) {
                          //console.log(response);
                          if (!objPage) {
                            $scope.appFacebook.showModalSelectedPage(response);

                          } else {
                            $scope.appFacebook.setFacebook(response, objPage);
                          }
                        }).catch(function (data) {
                          notificationService.error(data);
                        });

                      });
                    } else {
                      $FB.login(function () {
                        $FB.api('/me/accounts', function (response) {
                          if (response.error) {
                            notificationService.error(surveyConstant.Notifications.Error.ApiFacebook);
                            return;
                          }
                          $scope.appFacebook.checkPermissionFacebookPage(response.data).then(function (response) {
                            //console.log(response);
                            if (!objPage) {
                              $scope.appFacebook.showModalSelectedPage(response);

                            } /*else {
                             $scope.appFacebook.setFacebook(response, objPage);
                             }*/
                          }).catch(function (data) {
                            notificationService.error(data);
                          });
                        });
                      }, {
                        scope: 'publish_actions,publish_pages,manage_pages,email'
                      });
                    }
                  });
                },
                getPicturesPage: function (id) {
                  var defer = $q.defer();
                  var promise = defer.promise;
                  FB.api('/' + id + '/picture?redirect=false', function (response) {
                    defer.resolve(response);
                  });
                  return promise;
                },
                getFanPageArr: function (data) {
                  var defer = $q.defer();
                  var promises = [];
                  var response = data;
                  angular.forEach(response, function (value) {
                    promises.push($scope.appFacebook.getPicturesPage(value.id));
                  });
                  function setResolve(data) {
                    for (var i = 0; i < data.length; i++) {
                      response[i].picture = data[i].data.url;
                    }
                    defer.resolve(response);
                  }
                  $q.all(promises).then(function (data) {
                    setResolve(data)
                  });
                  return defer.promise;
                },
                showModalSelectedPage: function (data) {
                  var pages = data.data
                  //console.log("eh eh eppah colombia");
                  $scope.appFacebook.getFanPageArr(pages).then(function (data) {
                    document.body.scrollTop = 0;
                    $mdDialog.show({
                      scope: $scope.$new(),
                      controller: $scope.controllers.social,
                      template: surveyConstant.templateModalPageFacebook,
                      parent: angular.element(document.body),
                      clickOutsideToClose: true,
                      disableParentScroll: false,
                      locals: {
                        pages: data
                      },
                    }).then(function (response) {
                      $scope.appFacebook.fanPageSelected = response;
                    }, function () {
                      if (!$scope.appFacebook.fanPageSelected) {
                        $scope.appFacebook.facebook = false;
                      }
                    });
                  });
                },
                setFacebook: function (response, objPage) {
                  $scope.appFacebook.getPicturesPage(objPage.idPage).then(function (data) {
                    for (var i = 0; i < response.data.length; i++) {
                      let value = response.data[i];
                      //                      console.log(parseInt(value.id) == parseInt(objPage.idPage));
                      if (parseInt(value.id) === parseInt(objPage.idPage)) {
                        $scope.appFacebook.facebook = true;
                        $scope.appFacebook.fanPageSelected = value;
                        $scope.appFacebook.fanPageSelected.picture = data.data.url;
                        $scope.appFacebook.descriptionPublish = objPage.description;
                        //                        console.log($scope.appFacebook);
                        break;
                      }
                    }

                  });
                }
              };

              $scope.modal = {
                open: function (id) {
                  let objModal = {};
                  let modal = "";
                  switch (id) {
                    case 1:
                      objModal.templateModal = surveyConstant.templateModalEmail;
                      objModal.controller = $scope.controllers.email;
                      objModal.locals = {};
                      objModal.varReturn = 'email';
                      this.show(objModal);
                      break;
                    case 2:
                      $scope.appFacebook.login(false);
                      //                      objModal.templateModal = surveyConstant.imageUpload;
                      //                      objModal.controller = $scope.controllers.social;
                      //                      objModal.locals = {};
                      //                      objModal.varReturn = 'email';
                      //                      this.show(objModal);
                      break;
                    case 3:
                      objModal.templateModal = surveyConstant.templateEnlace;
                      objModal.controller = $scope.controllers.link;
                      objModal.locals = {};
                      objModal.varReturn = 'email';
                      this.show(objModal);
                      let changeSurvey = {status: "published", type: "public"};
                      RestServices.changeSurvey(changeSurvey, $scope.global.idSurvey).then(function (data) {
                        RestServices.changeType(changeSurvey, $scope.global.idSurvey).then(function (data) {
                          notificationService.primary("La encuesta se encuentra en estado publicado.");
                        });
                      });
                      break;
                  }

                },
                show: function (obj) {
                  $window.scrollTo(0, 0);
                  $mdDialog.show({
                    scope: $scope.$new(),
                    template: obj.templateModal,
                    controller: obj.controller,
                    locals: obj.locals
                  })
                          .then(function (data) {
                            //Todo salio bien en el modal
                            $scope.returnModal[obj.varReturn] = data;
                          }, function (err) {
                            //en caso de que cierre el modal

                          });
                }
              }

            }]);
})();
