(function () {
  angular.module('smstemplate.controller', [])
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
              $scope.errorChart = false;
//-----------------------------------------------
              //Se cargan las categorias según la cuenta
              $scope.loadsmstemplatecategory = function () {
                RestServices.listSmsTemplateCategory().then(function (data) {
                  $scope.listcateg = data;
                });
              };

              $scope.loadsmstemplatecategory();
//-----------------------------------------------
              $scope.initial = 0;
              $scope.page = 1;

              $scope.forward = function () {
                $scope.initial += 1;
                $scope.page += 1;
                $scope.listsmstemplate();
              };
              $scope.fastforward = function () {
                $scope.initial = ($scope.list.total_pages - 1);
                $scope.page = $scope.list.total_pages;
                $scope.listsmstemplate();
              };
              $scope.backward = function () {
                $scope.initial -= 1;
                $scope.page -= 1;
                $scope.listsmstemplate();
              };
              $scope.fastbackward = function () {
                $scope.initial = 0;
                $scope.page = 1;
                $scope.listsmstemplate();
              };

              $scope.listsmstemplate = function () {
                RestServices.listSmsTemplate($scope.initial, $scope.data).then(function (data) {
                  $scope.list = data;
                });
              };
              $scope.listsmstemplate();
//-----------------------------------------------
//Filtros
              $scope.filterCateg = function () {
                RestServices.listSmsTemplate($scope.initial, $scope.data).then(function (data) {
                  $scope.list = data;
                });
              };

              $scope.filtername = function () {
                RestServices.listSmsTemplate($scope.initial, $scope.data).then(function (data) {
                  $scope.list = data;
                });
              };
//-----------------------------------------------
//Validar contenido SMS


//-----------------------------------------------
//Eliminar
              $scope.confirmDelete = function (idSmsTemplate) {
                console.log(idSmsTemplate);
                $scope.idSmsTemplate = idSmsTemplate;
                openModal();
              };
              $scope.deleteSmstemplate = function () {
                RestServices.deleteSmstemplate($scope.idSmsTemplate).then(function (data) {
                  closeModal();
                  notificationService.warning(data.message);
                  $scope.getAll();
                });
              };

              $scope.getAll = function () {
                RestServices.getAll($scope.initial).then(function (data) {
                  $scope.list = data;
                });
              };
            }])
          .controller('createController', ['$scope', 'RestServices', '$stateParams', 'notificationService', '$state', function ($scope, RestServices, $stateParams, notificationService, $state) {
              //-----------------------------------------------
              //Se cargan las categorias según la cuenta
              $scope.newcategorytemplatesms = false;
              $scope.showTags = false;
              $scope.morecaracter = false;
              $scope.existTags = false;
              $scope.loadsmstemplatecategory = function () {
                RestServices.listSmsTemplateCategory().then(function (data) {
                  $scope.listcateg = data;
                });
              };
              $scope.loadsmstemplatecategory();

              $scope.validateContent = function () {
                $scope.errorChart = false;
                if (!$scope.contenttempsms.match(/^[0-9A-Za-z_ \\\/\\'\-!#$%&()*+,.:;<=>?@]{1,160}$/i)) {
                  $scope.errorChart = true;
                  return;
                }
              };
              
             $scope.opeModalMoreCa = function () {
              if( $('#morecaracter').prop('checked') ) {
                   $('#alertMoreCaracter').removeClass('modal'); 
                   $('#alertMoreCaracter').addClass('dialog dialog--open'); 
                }
              }

              $scope.newCateg = function () {
                $scope.newcategorytemplatesms = true;
              };

              $scope.cancelCateg = function () {
                $scope.newsmstempcateg = '';
                $scope.newcategorytemplatesms = false;
              };

              $scope.saveCateg = function () {
                if (angular.isUndefined($scope.newsmstempcateg) || $scope.newsmstempcateg === '') {
                  notificationService.error("El campo de nueva categoría no puede estar vacío");
                  return;
                }
                if ($scope.newsmstempcateg.length < 2 || $scope.newsmstempcateg.length > 80) {
                  notificationService.error("El campo de nueva categoría debe tener mínimo 2 caracteres y máximo 80");
                  return;
                }

                var data = {
                  name: $scope.newsmstempcateg
                };

                RestServices.saveSmsTempCateg(data).then(function (data) {
                  notificationService.success(data.message);
                  $scope.newsmstempcateg = '';
                  $scope.newcategorytemplatesms = false;
                  $scope.loadsmstemplatecategory();
                  $scope.smstempcateg = data.idSmsTemplateCategory;
                });


              };

//-----------------------------------------------

              $scope.saveSmsTemplate = function () {
                var message = $('#contenttempsms').val(); 
                $scope.contenttempsms = message;
                var data = {
                  name: $scope.nametempsms,
                  categ: $scope.smstempcateg,
                  morecaracter : $scope.morecaracter,
                  content: $scope.contenttempsms
                };

                RestServices.saveSmsTemplate(data).then(function (data) {
                  notificationService.success(data.message);
                  $state.go("index");
                });
              };
              $scope.getTags = function () {
                RestServices.getTags().then(function (data) {
                  $scope.tags = data;
                });
                console.log($scope.tags);
              };
              $scope.addTag = function (tag) {
                if (typeof $scope.contenttempsms == "undefined") {
                  $scope.contenttempsms = "";
                  $scope.contenttempsms += tag;
                } else {
                  $scope.contenttempsms += " " + tag;
                }

                $scope.validateInLine();
              }

              $scope.validateInLine = function () {
                $scope.taggedMessage = $scope.contenttempsms;
                var tags = /%%+[a-zA-Z0-9_]+%%/;
                if (tags.test($scope.contenttempsms)) {
                  $scope.existTags = true;
                  $scope.taggedMessage = "";
                  var words = $scope.contenttempsms.split(" ");
                  for (var cont = 0; cont < words.length; cont++) {
                    var word = words[cont];
                    if (word.substr(0, 2) == "%%" && (word.substr(-2) == "%%" || word.substr(-3) == "%%," || word.substr(-3) == "%%." || word.substr(-3) == "%%;")) {
                      word = word.substr(2);
                      word = "<b><i>" + word;
                      if (word.substr(-2) == "%%") {
                        word = word.substr(0, word.length - 2);
                      } else if (word.substr(-3) == "%%," || word.substr(-3) == "%%." || word.substr(-3) == "%%;") {
                        word = word.substr(0, word.length - 3);
                      }
                      word = word + "</i></b>";
                    }
                    $scope.taggedMessage += word + " ";
                  }

                }

              };
              $scope.openPreview = function () {
                $("#preview").addClass('dialog--open');
              }
              $scope.closePreview = function () {
                $("#preview").removeClass('dialog--open');
              }
              $scope.toggleAllTags = function () {
                $(".allTags").toggle('slow');
              }
              $scope.getTags();
            }])
          .controller('editController', ['$scope', 'RestServices', '$stateParams', 'notificationService', '$state', function ($scope, RestServices, $stateParams, notificationService, $state) {
              $scope.errorChart = false;
              $scope.showTags = false;
//-----------------------------------------------
              //Se cargan las categorias según la cuenta
              $scope.loadsmstemplatecategory = function () {
                RestServices.listSmsTemplateCategory().then(function (data) {
                  $scope.listcateg = data;
                });
              };

              $scope.loadsmstemplatecategory();

              $scope.newCateg = function () {
                $scope.newcategorytemplatesms = true;
              };

              $scope.cancelCateg = function () {
                $scope.newsmstempcateg = '';
                $scope.newcategorytemplatesms = false;
              };

              $scope.validateContent = function () {
                $scope.errorChart = false;
                if (!$scope.data.contenttempsms.match(/^[0-9A-Za-z_ \\\/\\'\-!#$%&()*+,.:;<=>?@]{1,160}$/i)) {
                  $scope.errorChart = true;
                  return;
                }
              };
              
              $scope.opeModalMoreCa = function () {
                if( $('#morecaracter').prop('checked') ) {
                   $('#alertMoreCaracter').removeClass('modal'); 
                   $('#alertMoreCaracter').addClass('dialog dialog--open'); 
                }
              }
              
              $scope.saveCateg = function () {
                if (angular.isUndefined($scope.newsmstempcateg) || $scope.newsmstempcateg === '') {
                  notificationService.error("El campo de nueva categoría no puede estar vacío");
                  return;
                }
                if ($scope.newsmstempcateg.length < 2 || $scope.newsmstempcateg.length > 80) {
                  notificationService.error("El campo de nueva categoría debe tener mínimo 2 caracteres y máximo 80");
                  return;
                }

                var data = {
                  name: $scope.newsmstempcateg
                };

                RestServices.saveSmsTempCateg(data).then(function (data) {
                  notificationService.success(data.message);
                  $scope.newsmstempcateg = '';
                  $scope.newcategorytemplatesms = false;
                  $scope.loadsmstemplatecategory();
                  $scope.data.smstempcateg = data.idSmsTemplateCategory;
                });
              };
//-----------------------------------------------
              $scope.loadDataOneSmsTemplate = function () {
                RestServices.getSmsTemplate($stateParams.idsmstemplate).then(function (data) {
                  $scope.idSmsTemplate = data.idSmsTemplate;
                  $scope.data = data;
                  $scope.validateInLine();
                });
              };

              $scope.loadDataOneSmsTemplate();
//-----------------------------------------------
              $scope.editSmsTemplate = function () {
                var message = $('#contenttempsms').val(); 
                $scope.data.contenttempsms = message;
                message = null;
                var datap = {
                  idSmsTemplate: $scope.idSmsTemplate,
                  name: $scope.data.nametempsms,
                  categ: $scope.data.smstempcateg,
                  morecaracter : $scope.data.morecaracter,
                  content: $scope.data.contenttempsms
                };
                RestServices.editSmsTemplate(datap).then(function (data) {
                  notificationService.success(data.message);
                  $state.go("index");
                });
              };

              $scope.getTags = function () {
                RestServices.getTags().then(function (data) {
                  $scope.tags = data;
                });
                console.log($scope.tags);
              };
              $scope.addTag = function (tag) {
                $scope.data.contenttempsms += " " + tag;
                $scope.validateInLine();
              }

              $scope.validateInLine = function () {
//                $scope.invalidCharacters = false;
//                $scope.existTags = false;
                $scope.taggedMessage = $scope.data.contenttempsms;
                var tags = /%%+[a-zA-Z0-9_]+%%/;

                if (tags.test($scope.data.contenttempsms)) {
                  $scope.existTags = true;
                  $scope.taggedMessage = "";
                  var words = $scope.data.contenttempsms.split(" ");
                  for (var cont = 0; cont < words.length; cont++) {
                    var word = words[cont];
                    if (word.substr(0, 2) == "%%" && (word.substr(-2) == "%%" || word.substr(-3) == "%%," || word.substr(-3) == "%%." || word.substr(-3) == "%%;")) {
                      word = word.substr(2);
                      word = "<b><i>" + word;
                      if (word.substr(-2) == "%%") {
                        word = word.substr(0, word.length - 2);
                      } else if (word.substr(-3) == "%%," || word.substr(-3) == "%%." || word.substr(-3) == "%%;") {
                        word = word.substr(0, word.length - 3);
                      }
                      word = word + "</i></b>";
                    }
                    $scope.taggedMessage += word + " ";
                  }

                }

              };
              $scope.openPreview = function () {
                $("#preview").addClass('dialog--open');
              }
              $scope.closePreview = function () {
                $("#preview").removeClass('dialog--open');
              }
              $scope.toggleAllTags = function () {
                $(".allTags").toggle('slow');
              }
              $scope.getTags();
            }]);
})();
