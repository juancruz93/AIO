(function () {
  angular.module('mailtemplate.controller', ['mailtemplate.services', "ngMaterial", "ui.select", "ngSanitize"])
          .constant('constantMailTemplate', {
            validationSaveType: {
              newTemplate: "new",
              oldTemplate: "old",
            },
            menssages: {
              saveExist: "Debes seleccionar una plantilla de mail existente ",
            }
          })
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
          .controller('listController', ['$scope', 'RestServices', '$stateParams', 'notificationService', function ($scope, RestServices, $stateParams, notificationService) {
              $scope.initial = 0;
              $scope.page = 1;
              $scope.loader = true;
              $scope.imagenDate = new Date();
              $scope.imagenTime = $scope.imagenDate.getTime();

              $scope.listmailtemplate = function () {
                RestServices.listMailTemp($scope.initial, $scope.data).then(function (data) {
                  $scope.list = data;
                  $scope.loader = false;
                });
              };
              $scope.listmailtemplate();

              $scope.forward = function () {
                $scope.initial += 1;
                $scope.page += 1;
                $scope.loader = true;
                $scope.listmailtemplate();
              };
              $scope.fastforward = function () {
                $scope.initial = ($scope.list.total_pages - 1);
                $scope.page = $scope.list.total_pages;
                $scope.loader = true;
                $scope.listmailtemplate();
              };
              $scope.backward = function () {
                $scope.initial -= 1;
                $scope.page -= 1;
                $scope.loader = true;
                $scope.listmailtemplate();
              };
              $scope.fastbackward = function () {
                $scope.initial = 0;
                $scope.page = 1;
                $scope.loader = true;
                $scope.listmailtemplate();
              };
//-----------------------------------------------
              $scope.previewmailtempcont = function (id) {
                RestServices.previewMailTemplateContent(id).then(function (data) {
                  var e = data.template;
                  document.getElementById("preview-modal").innerHTML = "";
                  $('<iframe frameborder="0" width="100%" height="100%"/>').appendTo('#preview-modal').contents().find('body').append(e);
                  $('#myModal').modal('show');
                });
              };
//-----------------------------------------------
              $scope.loadmailtemplatecategory = function () {
                RestServices.getmailtempcategfilt().then(function (data) {
                  $scope.liscateg = data;
                });
              };

              $scope.loadmailtemplatecategory();
//-----------------------------------------------
              $scope.filterCateg = function () {
                $scope.loader = true;
                RestServices.listMailTemp($scope.initial, $scope.data).then(function (data) {
                  $scope.list = data;
                  $scope.loader = false;
                });
              };

              $scope.filtername = function () {
                $scope.loader = true;
                RestServices.listMailTemp($scope.initial, $scope.data).then(function (data) {
                  $scope.list = data;
                  $scope.loader = false;
                });
              };
//-----------------------------------------------
              $scope.confirmDelete = function (idMailTemplete) {
                $scope.idMailTemplate = idMailTemplete;
                openModal();
              };

              $scope.delete = function () {
                $scope.loader = true;
                RestServices.deleteMailTemplate($scope.idMailTemplate).then(function (data) {
                  notificationService.warning(data.message);
                  closeModal();
                  $scope.listmailtemplate();
                });
              };
            }])
          .controller('createController', ['$scope', 'RestServices', '$stateParams', 'notificationService', '$state', function ($scope, RestServices, $stateParams, notificationService, $state) {
              $scope.newcategorytemplatemail = false;
              $scope.loadmailtemplatecategory = function () {
                RestServices.getmailtempcateg().then(function (data) {
                  $scope.liscateg = data;
                });
              };

              $scope.loadmailtemplatecategory();

              $scope.newCateg = function () {
                $scope.newcategorytemplatemail = true;
              };

              $scope.cancelCateg = function () {
                $scope.newmailtempcat = '';
                $scope.newcategorytemplatemail = false;
              };

              $scope.saveCateg = function () {
                if (angular.isUndefined($scope.newmailtempcat) || $scope.newmailtempcat === '') {
                  notificationService.error("El campo de nueva categoría no puede estar vacío");
                  return;
                }
                if ($scope.newmailtempcat.length < 2 || $scope.newmailtempcat.length > 80) {
                  notificationService.error("El campo de nueva categoría debe tener mínimo 2 caracteres y máximo 80");
                  return;
                }

                var data = {
                  name: $scope.newmailtempcat
                };

                RestServices.saveMailTempCateg(data).then(function (data) {
                  notificationService.success(data.message);
                  $scope.newmailtempcat = '';
                  $scope.newcategorytemplatemail = false;
                  $scope.loadmailtemplatecategory();
                  $scope.mailtempcat = data.idMailTemplateCategory;
                });
              };

              $scope.idMailTemplate = null;
              $scope.saveMailTemplate = function (se) {//Save and continue editing  
                if (angular.isUndefined($scope.namemailtempcat) || $scope.namemailtempcat === '') {
                  notificationService.error("El campo de nombre de la plantilla no puede estar vacío");
                  return;
                }
                if ($scope.namemailtempcat.length < 2 || $scope.namemailtempcat.length > 80) {
                  notificationService.error("El campo de nombre de la plantilla debe tener mínimo 2 caracteres y máximo 80");
                  return;
                }
                if (angular.isUndefined($scope.mailtempcat)) {
                  notificationService.error("Debes seleccionar o crear una categoría para la plantilla");
                  return;
                }

                var data = {
                  nameMailTemplate: $scope.namemailtempcat,
                  mailTemplateCateg: $scope.mailtempcat,
                  globalTemp: $('#globalTemp').prop('checked'),
                  editor: document.getElementById('iframeEditor').contentWindow.catchEditorData()
                };

                document.getElementById('iframeEditor').contentWindow.RecreateEditor();
                RestServices.saveMailTemp(data, $scope.idMailTemplate).then(function (data) {
                  $scope.idMailTemplate = data.idMailTemplate;
                  notificationService.success(data.message);
                });

                if (se !== 1) {
                  $state.go("index");
                }
              };
            }])
          .controller('editController', ['$scope', 'RestServices', 'notificationService', 'constantMailTemplate', function ($scope, RestServices, notificationService, constantMailTemplate) {
              $scope.data = {};
              $scope.newcategorytemplatemail = false;
              $scope.loadmailtemplatecategory = function () {
                RestServices.getmailtempcateg().then(function (data) {
                  $scope.liscateg = data;
                });
              };

              $scope.loadmailtemplatecategory();

              RestServices.getAccounts().then(function (data) {
                $scope.accounts = data;
              }).catch(function (data) {
                notificationService.error(data.message);
              });
/////////////////////////////////////////////////
              $scope.loaddata = function () {
                //El idMailTemplate que se pasa por parametro aquí es el que se está enviando desde la vista edit con phlacon
                RestServices.editMailTemplate(idMailTemplate).then(function (data) {
                  $scope.data = data;
                  $scope.data.owner = data.idAccount;
                });
              };
              $scope.loaddata();
/////////////////////////////////////////////////
              $scope.newCateg = function () {
                $scope.newcategorytemplatemail = true;
              };

              $scope.cancelCateg = function () {
                $scope.newmailtempcat = '';
                $scope.newcategorytemplatemail = false;
              };

              $scope.saveCateg = function () {
                if (angular.isUndefined($scope.newmailtempcat) || $scope.newmailtempcat === '') {
                  notificationService.error("El campo de nueva categoría no puede estar vacío");
                  return;
                }
                if ($scope.newmailtempcat.length < 2 || $scope.newmailtempcat.length > 80) {
                  notificationService.error("El campo de nueva categoría debe tener mínimo 2 caracteres y máximo 80");
                  return;
                }

                var data = {
                  name: $scope.newmailtempcat
                };

                RestServices.saveMailTempCateg(data).then(function (data) {
                  notificationService.success(data.message);
                  $scope.newmailtempcat = '';
                  $scope.newcategorytemplatemail = false;
                  $scope.loadmailtemplatecategory();
                  $scope.mailtempcat = data.idMailTemplateCategory;
                });
              };

              $scope.saveMailTemplate = function (se) {//Save and continue editing

                if (angular.isUndefined($scope.data.name) || $scope.data.name === '') {
                  notificationService.error("El campo de nombre de la plantilla no puede estar vacío");
                  return;
                }
                if ($scope.data.name.length < 2 || $scope.data.name.length > 80) {
                  notificationService.warning("El campo de nombre de la plantilla debe tener mínimo 2 caracteres y máximo 80");
                  return;
                }
                if (angular.isUndefined($scope.data.idMailTemplateCategory)) {
                  notificationService.warning("Debes seleccionar o crear una categoría para la plantilla");
                  return;
                }

                $scope.data.content = document.getElementById('iframeEditor').contentWindow.catchEditorData();

                document.getElementById('iframeEditor').contentWindow.RecreateEditor();

                RestServices.saveMailTemp($scope.data, $scope.data.idMailTemplate, se).then(function (data) {
                  $scope.idMailTemplate = data.idMailTemplate;
                  notificationService.info(data.message);
                  $scope.saveExist = false;
                  $scope.saveNew = false;

                });

                if (se !== 1) {
                  window.location = fullUrlBase + "mailtemplate#/";
                }
              };

              //Jordan Zapata Mora
              //funcion que hace la peticion para retornar las plantillas prediseñadas de Mail
              //creado el 31/07/2017
              //update el 31/07/2017
              $scope.getTemplateMail = function (flag) {

                if ($scope.saveExist == true) {
                  RestServices.getAllTemplateMail(flag).then(function (data) {
                    $scope.MailTemplateSaveAs = data;
                  });
                }
              };

              //Jordan Zapata Mora
              //funcion que valida las opciones de guardar como, en editar plantilla prediseñada
              //creado el 31/07/2017
              //update el 31/07/2017
              $scope.saveAsMailTemplete = function () {
                if ($scope.saveNew == true) {
                  let type = constantMailTemplate.validationSaveType.newTemplate;
                  $scope.saveMailTemplate(type);
                }
                if ($scope.saveExist == true) {
                  if (angular.isUndefined($scope.idMailTemplateSaveas)) {
                    notificationService.warning(constantMailTemplate.menssages.saveExist);
                    return;
                  }
                  let type = constantMailTemplate.validationSaveType.oldTemplate;
                  $scope.data.idMailTemplate = $scope.idMailTemplateSaveas;
                  $scope.saveMailTemplate(type);
                }

              };

              //Jordan Zapata Mora
              //funcion encargada de linpiar los checkbox de la modal de guardar como.
              //creado el 31/07/2017
              //update el 31/07/2017
              $scope.clearModal = function () {
                $scope.saveNew = false;
                $scope.saveExist = false;
              };

            }])
          .controller('selectController', ['$scope', '$stateParams', 'RestServices', 'notificationService', function ($scope, $stateParams, RestServices, notificationService) {
              $scope.initial = 0;
              $scope.page = 1;
              $scope.loader = true;

              if (typeof $stateParams.idmail == "undefined" || $stateParams.idmail == "") {

              } else {
                $scope.idMail = $stateParams.idmail;
              }

              RestServices.getMail($stateParams.idmail).then(function (data) {
                if (data.mail == false) {
                  notificationService.warning("El mail no se encuentra registrado.");

                }
              });

              $scope.listmailtemplate = function () {
                $scope.loader = true;
                RestServices.listMailTemp($scope.initial, $scope.data).then(function (data) {
                  $scope.list = data;
                  $scope.loader = false;
                });
              };

              $scope.listmailtemplate();

              $scope.forward = function () {
                $scope.initial += 1;
                $scope.page += 1;
                $scope.listmailtemplate();
              };
              $scope.fastforward = function () {
                $scope.initial = ($scope.list.total_pages - 1);
                $scope.page = $scope.list.total_pages;
                $scope.listmailtemplate();
              };
              $scope.backward = function () {
                $scope.initial -= 1;
                $scope.page -= 1;
                $scope.listmailtemplate();
              };
              $scope.fastbackward = function () {
                $scope.initial = 0;
                $scope.page = 1;
                $scope.listmailtemplate();
              };
//-----------------------------------------------
              $scope.previewmailtempcont = function (id) {
                RestServices.previewMailTemplateContent(id).then(function (data) {
                  var e = data.template;
                  $('#preview-modal').empty();
                  $('<iframe frameborder="0" width="100%" height="100%"/>').appendTo('#preview-modal').contents().find('body').append(e);
                  $('#myModal').modal('show');
                });
              };

              $scope.loadmailtemplatecategory = function () {
                RestServices.getmailtempcategfilt().then(function (data) {
                  $scope.liscateg = data;
                });
              };

              $scope.loadmailtemplatecategory();
//-----------------------------------------------
              $scope.filterCateg = function () {
                $scope.loader = true;
                RestServices.listMailTemp($scope.initial, $scope.data).then(function (data) {
                  $scope.list = data;
                  $scope.loader = false;
                });
              };

              $scope.filtername = function () {
                $scope.loader = true;
                RestServices.listMailTemp($scope.initial, $scope.data).then(function (data) {
                  $scope.list = data;
                  $scope.loader = false;
                });
              };

            }])
          .controller('selectautorespController', ['$scope', '$stateParams', 'RestServices', 'notificationService', function ($scope, $stateParams, RestServices, notificationService) {
              $scope.initial = 0;
              $scope.page = 1;
              $scope.loader = true;

              if (typeof $stateParams.idautoresponder == "undefined" || $stateParams.idautoresponder == "") {

              } else {
                $scope.idAutoresponder = $stateParams.idautoresponder;
              }

              RestServices.getAutoresponder($stateParams.idautoresponder).then(function (data) {
                if (data.autoresponder == false) {
                  notificationService.warning("La autorespuesta no se encuentra registrada.");
                }
              });

              $scope.listmailtemplate = function () {
                $scope.loader = true;
                RestServices.listMailTemp($scope.initial, $scope.data).then(function (data) {
                  $scope.list = data;
                  $scope.loader = false;
                });
              };

              $scope.listmailtemplate();

              $scope.forward = function () {
                $scope.initial += 1;
                $scope.page += 1;
                $scope.listmailtemplate();
              };
              $scope.fastforward = function () {
                $scope.initial = ($scope.list.total_pages - 1);
                $scope.page = $scope.list.total_pages;
                $scope.listmailtemplate();
              };
              $scope.backward = function () {
                $scope.initial -= 1;
                $scope.page -= 1;
                $scope.listmailtemplate();
              };
              $scope.fastbackward = function () {
                $scope.initial = 0;
                $scope.page = 1;
                $scope.listmailtemplate();
              };
//-----------------------------------------------
              $scope.previewmailtempcont = function (id) {
                RestServices.previewMailTemplateContent(id).then(function (data) {
                  var e = data.template;
                  $('#preview-modal').empty();
                  $('<iframe frameborder="0" width="100%" height="100%"/>').appendTo('#preview-modal').contents().find('body').append(e);
                  $('#myModal').modal('show');
                });
              };

              $scope.loadmailtemplatecategory = function () {
                RestServices.getmailtempcategfilt().then(function (data) {
                  $scope.liscateg = data;
                });
              };

              $scope.loadmailtemplatecategory();
//-----------------------------------------------
              $scope.filterCateg = function () {
                $scope.loader = true;
                RestServices.listMailTemp($scope.initial, $scope.data).then(function (data) {
                  $scope.list = data;
                  $scope.loader = false;
                });
              };

              $scope.filtername = function () {
                $scope.loader = true;
                RestServices.listMailTemp($scope.initial, $scope.data).then(function (data) {
                  $scope.list = data;
                  $scope.loader = false;
                });
              };

            }])
})();
