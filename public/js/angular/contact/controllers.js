(function () {
  angular.module('contact.controllers', [])
          .controller('ContactController', ['$scope', 'restService', 'notificationService', '$timeout', '$rootScope', 'setData', function ($scope, restService, notificationService, $timeout, $rootScope, setData) {
              $scope.fullUrlBase = fullUrlBase;
              $scope.templateBase = templateBase;
              $scope.show = true;
              $scope.showList = true;
              $scope.contactlist = [{}];
              $scope.progressbar = false;
              $scope.changestatus = true;
              $scope.suscribe = true;
              $scope.progressbar3 = true;
              $scope.stateend = "";
              $scope.statePrin = "Todos";
              $scope.idcontactUnsubscribe = null;
              $scope.idcontactlistUnsubscribe = null;
              $scope.typeUnsubscribe = null;
              $scope.estatusUnsuscribe = null;
              $scope.typeDeleted = null;
              $scope.typeExport = 0;
              $scope.flagEmail = false;
              $scope.idContactlist = "";

              // Se listan los estados para realizar el filtro
              $scope.status = [
                "Todos",
                "Activos",
                "Desuscritos",
                "Rebotados",
                "Spam",
                "Bloqueados"
              ]
              
              $scope.state = "Todos";

              //Seleccionar uno o todos
              $scope.items = [];
              $scope.selected = [];
              $scope.toggle = function (item, list) {
                var idx = list.indexOf(item);
                if (idx > -1) {
                  list.splice(idx, 1);
                } else {
                  list.push(item);
                }
                $scope.evaluateSuscribe();
              };
              $scope.exists = function (item, list) {
                return list.indexOf(item) > -1;
              };
              $scope.isIndeterminate = function () {
                return ($scope.selected.length !== 0 &&
                        $scope.selected.length !== $scope.items.length);
              };
              $scope.isChecked = function () {
                return $scope.selected.length === $scope.items.length;
              };
              $scope.toggleAll = function () {

                if ($scope.selected.length === $scope.items.length) {
                  $scope.idSelected = $scope.selected;
                  $scope.selected = [];
                } else if ($scope.selected.length === 0 || $scope.selected.length > 0) {
                  $scope.selected = $scope.items.slice(0);
                }
                $scope.evaluateSuscribe();
              };
              //Fin Seleccionar uno o todos
              $scope.evaluateSuscribe = function () {
                var cont = 0;
                for (i = 0; i < $scope.contacts[0].items.length; i++) {
                  for (j = 0; j < $scope.selected.length; j++) {
                    if (($scope.contacts[0].items[i].idContact == $scope.selected[j]) && ($scope.contacts[0].items[i].status == "unsubscribed")) {
                      cont++;
                    }
                  }
                  $scope.items[i] = $scope.contacts[0].items[i].idContact;
                }

                if (cont == $scope.selected.length) {
                  $scope.suscribe = false;
                } else {
                  $scope.suscribe = true;
                }
              }
              $scope.getAllInfoContactlist = function () {
                restService.getOneContactlist(idContactlist).then(function (data) {
                  setData.setData(data);
                  $rootScope.contactlist = data;
                });
              }
              //              $scope.progressbar = ngProgressFactory.createInstance();
              //              $scope.progressbar.setColor("#ff6e00");

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

              $scope.updateUser = function (data, key, idContact, idCustomfield) {
                //          console.log(idCustomfield);
                restService.editContact(idContact, key, data, idContactlist, idCustomfield).then(function (data) {
                  notificationService.primary(data.message);
                  //            $scope.getAll();
                  $scope.getAllInfoContactlist();
                }).catch(function (data) {
                  notificationService.error(data.message);
                  //            $scope.getAll();
                });
              }

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
                    string = "Fecha de nacimiento";
                    break;
                  case "indicative":
                    string = "Indicativo";
                    break;
                  case "description":
                    string = "Descripción";
                    break;
                }
                return string;
              }
              
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

              $scope.stringsearch = -1;
              $scope.searchcontacts = function () {
                $scope.stringsearch = $scope.search;
                $scope.page = 1;
                $scope.getAll();
              };
              $scope.clear = function () {
                $scope.stringsearch = -1
                $scope.search = undefined;
                $scope.getAll();
              };
              $scope.initial = 0;
              $scope.page = 1;
              $scope.idContactlist = idContactlist;

              $scope.forward = function () {
                //                console.log($scope.contacts);
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
                if($scope.statePrin != $scope.stateend){
                  $scope.initial = 0;
                  $scope.page = 1;
                }
                
                $scope.progressbar = false;
                restService.getAll($scope.initial, $scope.idContactlist, $scope.stringsearch, $scope.stateend).then(function (data) {
                  
                  $scope.statePrin = $scope.stateend;
                  
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
                  for (i = 0; i < $scope.contacts[0].items.length; i++) {
                    $scope.items[i] = $scope.contacts[0].items[i].idContact;
                  }
                  $scope.progressbar = true;
                });
                restService.getAllIndicatives().then(function (data) {
                  $scope.indicative = data;
                });
                restService.customfieldselect(idContactlist).then(function (data) {
                  $scope.arr = [];
                  for (var i = 0; i < data.length; i++) {
                    if (data[i].type == "Select" || data[i].type == "Multiselect")
                      $scope.arr[data[i].idCustomfield] = data[i].value;
                  }

                  //                  $scope.to();
                });
              };
              $scope.changestatus = function (contactlist, idContactlist, status) {
                $scope.estatusUnsuscribe = status;
                $scope.idcontactUnsubscribe = contactlist.idContact;
                $scope.idcontactlistUnsubscribe = idContactlist;
                $scope.email = contactlist.email;
                $scope.indicative = contactlist.indicative;
                $scope.phone = contactlist.phone;
                openModal('deletedOption');
              };
              $scope.unsubscribe = function () {
                if (!angular.isUndefined($scope.unsubscribeOnly) && $scope.unsubscribeOnly == true) {
                  $scope.typeUnsubscribe = 'unsubscribeOnly';
                } else if (!angular.isUndefined($scope.unsubscribeAll) && $scope.unsubscribeAll == true) {
                  $scope.typeUnsubscribe = 'unsubscribeAll';
                } else {
                  notificationService.error("Debe seleccionar un modo para desuscripció");
                  return;
                }

                closeModal('deletedOption');
                $scope.progressbar3 = false;
                openModal('waiting');
                let data = {
                  idContact: $scope.idcontactUnsubscribe,
                  idContactlist: $scope.idcontactlistUnsubscribe,
                  email: $scope.email,
                  indicative: $scope.indicative,
                  phone: $scope.phone,
                  type: $scope.typeUnsubscribe,
                  status: $scope.estatusUnsuscribe
                };

                restService.changestatus(data).then(function (data) {
                  closeModal('waiting');
                  $scope.progressbar3 = true;
                  $scope.getAll();
                  $scope.getAllInfoContactlist();
                  notificationService.primary(data.menssage);
                });
              }

              $scope.setSuscribe = function (data) {
                $scope.suscribe = data;
                $scope.changeSuscribeSelected();
                //                console.log(data);
              }
              $scope.changeSuscribeSelected = function () {
                openModal('waiting');
                restService.changesuscribeselected($scope.selected, $scope.idContactlist, $scope.suscribe).then(function (data) {
                  $scope.selected = [];
                  $scope.getAllInfoContactlist();
                  $scope.getAll();
                  closeModal('waiting');
                  notificationService.primary(data.message);
                });

              };
              $scope.getAll();
              //console.log(relativeUrlBase + " " + fullUrlBase + " " + templateBase);
              $scope.confirmDelete = function (contactlist, idContactlist) {
                $scope.idContact = contactlist.idContact;
                $scope.idContactlist = idContactlist;
                $scope.email = contactlist.email;
                $scope.indicative = contactlist.indicative;
                $scope.phone = contactlist.phone;
                openModal('confirmDelete');
              };

              $scope.confirmDeleteSelected = function () {
                openModal('confirmDeleteSelected');
              };

              $scope.deleteContact = function () {
                if (!angular.isUndefined($scope.deletedOnly) && $scope.deletedOnly == true) {
                  $scope.typeDeleted = 'deletedOnly';
                } else if (!angular.isUndefined($scope.deletedAll) && $scope.deletedAll == true) {
                  $scope.typeDeleted = 'deletedAll';
                } else {
                  notificationService.error("Debe seleccionar un metodo para eliminación del contacto");
                  return;
                }

                let data = {
                  idContact: $scope.idContact,
                  idContactlist: $scope.idContactlist,
                  email: $scope.email,
                  indicative: $scope.indicative,
                  phone: $scope.phone,
                  type: $scope.typeDeleted
                }
                closeModal('confirmDelete');
                $scope.progressbar3 = false;
                openModal('waiting');
                restService.deleteContact(data).then(function (data) {
                  notificationService.warning(data.message);
                  closeModal('confirmDelete');
                  $scope.getAllInfoContactlist();
                  $scope.getAll();
                  closeModal('waiting');

                });
              };
              $scope.deleteContactSelected = function () {
                openModal('waiting');
                restService.deleteContactSelected($scope.selected, $scope.idContactlist).then(function (data) {
                  notificationService.warning(data.message);
                  closeModal('confirmDeleteSelected');
                  $scope.selected = [];
                  $scope.getAllInfoContactlist();
                  $scope.getAll();
                  closeModal('waiting');
                });
              };
              $scope.getAllInfoContactlist();
              $rootScope.deleteContact = function () {

              };

              $scope.confirmMoveSelected = function () {
                openModal('confirmMoveSelected');
                restService.getContactlistToMoveSelected($scope.idContactlist).then(function (data) {
                  $scope.contactliststomove = data.contactliststomove;
                });
              }
              $scope.confirmCopySelected = function () {
                openModal('confirmCopySelected');
                restService.getContactlistToMoveSelected($scope.idContactlist).then(function (data) {
                  $scope.contactliststomove = data.contactliststomove;
                });
              }

              $scope.validateContactSelected = function (type) {
                openModal('waiting');
                restService.validateCopyContactSelected($scope.selectedOne, $scope.selected, $scope.idContactlist).then(function (data) {
                  closeModal('confirmCopySelected');
                  closeModal('confirmMoveSelected');
                  $scope.type = type;
                  if (data.error == 'isError') {
                    $scope.arrayError = data.arrayError;
                    closeModal('waiting');
                    openModal('validateCopySelected');
                  } else if (data.error == 'noError') {
                    if (type == 'copy') {
                      $scope.copyContactSelected();
                    } else if (type == 'move') {
                      $scope.moveContactSelected();
                    }
                  }
                });
              }
              $scope.executeSelected = function () {
                if ($scope.type == 'copy') {
                  $scope.copyContactSelected();
                } else if ($scope.type == 'move') {
                  $scope.moveContactSelected();
                }
              }
              $scope.copyContactSelected = function () {
                openModal('waiting');
                restService.copyContactSelected($scope.selectedOne, $scope.selected, $scope.idContactlist).then(function (data) {
                  $scope.selected = [];
                  $scope.selectedOne = [];
                  closeModal('confirmCopySelected');
                  closeModal('validateCopySelected');
                  notificationService.success(data.message);
                  $scope.getAllInfoContactlist();
                  $scope.getAll();
                  closeModal('waiting');
                });
                closeModal('waiting');
              }
              $scope.moveContactSelected = function () {
                openModal('waiting');
                restService.moveContactSelected($scope.selectedOne, $scope.selected, $scope.idContactlist).then(function (data) {
                  $scope.selected = [];
                  $scope.selectedOne = [];
                  closeModal('confirmMoveSelected');
                  closeModal('validateCopySelected');
                  notificationService.success(data.message);
                  $scope.getAllInfoContactlist();
                  $scope.getAll();
                  closeModal('waiting');
                });
                closeModal('waiting');
              }
              $scope.setSelectedEmpty = function () {
                $scope.selectedOne = [];
              }

              //Seleccionar solo uno
              //              $scope.itemsOne = [];
              $scope.selectedOne = [];

              $scope.select = function (item) {
                $scope.selectedOne = [];
                $scope.selectedOne.push(item);
              };

              $scope.exists = function (item, list) {
                return list.indexOf(item) > -1;
              };
              //Fin Seleccionar solo uno

              $scope.searchstate = function () {
                if ($scope.state != null) {
                  //console.log($scope.state);
                  if ($scope.state == "Todos"){
                    $scope.stateend = "";
                    $scope.typeExport = 0;
                  }else{
                    $scope.stateend = $scope.state;
                    switch ($scope.stateend) {
                      case "Activos":
                        $scope.typeExport = 1;
                        break;
                      case "Desuscritos":
                        $scope.typeExport = 2;
                        break;
                      case "Rebotados":
                        $scope.typeExport = 3;
                        break;
                      case "Spam":
                        $scope.typeExport = 4;
                        break;
                      case "Bloqueados":
                        $scope.typeExport = 5;
                        break;
                      default:
                        $scope.typeExport = 0;
                        break;
                    }
                  }
                  $scope.getAll();
                }
              };
              
              $scope.closeModalUnsub = function () {
                closeModal('deletedOption');
                $scope.getAll();
              };
            }])
          .controller('ContactImportController', ['$scope', 'restService', function ($scope, restService) {


              $scope.uploadCsv = function () {
                var data = {
                  filecsv: $scope.importCsv
                };
                restService.contactcsv(data).then(function (res) {
                  console.log(res);

                });
              };

              $scope.typevalues = [];

              if (typeof (customfield) == 'undefined') {
                $scope.customfield = 0;
              } else {
                $scope.customfield = customfield;
              }

              if (typeof (dataCsv) == 'undefined') {
                $scope.dataCsv = 0;
              } else {
                var array = [];
                $scope.delimiter = ',';
                $scope.head = 0;
                for (var i = $scope.head; i < dataCsv.length; i++) {
                  var sepa = dataCsv[i].split($scope.delimiter);
                  array.push(sepa);
                }
                $scope.dataCsv = array;
                //console.log($scope.delimiter);
              }

              $scope.adjustDelimiter = function () {
                if (typeof ($scope.header) == 'undefined') {
                  $scope.showArrayCsv(0, $scope.delimiter);
                } else {
                  if ($scope.header) {
                    $scope.showArrayCsv(0, $scope.delimiter);
                  } else {
                    $scope.showArrayCsv(0, $scope.delimiter);
                  }

                }
              }

              $scope.showArrayCsv = function (start, delimiter) {
                var array = [];
                for (var i = start; i < dataCsv.length; i++) {
                  var sepa = dataCsv[i].split(delimiter);
                  array.push(sepa);
                }
                $scope.dataCsv = array;
              }

            }])
          .controller('NewbatchController', ['$scope', 'restService', 'notificationService', '$window', 'arrayConstruct', 'setData', '$rootScope', function ($scope, restService, notificationService, $window, arrayConstruct, setData, $rootScope) {
              $scope.initComponents = function () {
                restService.getAllIndicatives().then(function (response) {
                  $scope.listIndicatives = response;
                }).catch(function (data) {
                  notificationService.error(data.message);
                });
              };
              $rootScope.contactlist = setData.getData();
              $scope.limit = 30;
              $scope.batch = [];
              $scope.batchs = [];
              $scope.isScreenSize = $(window).width();

              $scope.misc = {
                loaderSaveShow: false,
                disabledSaveButton: false
              }

              $scope.functions = {
                dateBeforeRender: function ($dates) {
                  for (var i = 0; i < $dates.length; i++) {
                    if (new Date().getTime() < $dates[i].utcDateValue) {
                      $dates[i].selectable = false;
                    }
                  }
                }
              };

              $scope.validateEmptyBatch = function () {
                if ((typeof $scope.batch.email == "undefined" || $scope.batch.email == "") && (typeof $scope.batch.indicative == "undefined" || $scope.batch.indicative == "") && (typeof $scope.batch.phone == "undefined" || $scope.batch.phone == "")) {
                  notificationService.error("Debes ingresar por lo menos el email o el teléfono con su indicativo");
                  return false;
                } else if ($scope.batch.indicative && (typeof $scope.batch.phone == "undefined" || $scope.batch.phone == "")) {
                  notificationService.error("Debes ingresar el número de teléfono");
                  return false;
                } else if ($scope.batch.phone && (typeof $scope.batch.indicative == "undefined" || $scope.batch.indicative == "")) {
                  notificationService.error("Debes seleccionar el indicativo");
                  return false;
                } else if ($scope.batchs.length >= $scope.limit) {
                  notificationService.error("Sólo puede agregar " + $scope.limit + " contactos rápidamente");
                  return false;
                } else if (typeof $scope.batch.indicative !== "undefined" && typeof $scope.batch.phone !== "undefined") {
                  var length = $scope.listIndicatives.length;
                  var country;
                  for (var index = 0; index < length; index++) {
                    if ($scope.batch.indicative == $scope.listIndicatives[index].idCountry) {
                      country = $scope.listIndicatives[index];
                      break;
                    }
                  }
                  $scope.batch.nameIndicative = "(+" + country.phoneCode + ") " + country.name;
                  var phone = String($scope.batch.phone);
                  if (phone.length < country.minDigits || phone.length > country.maxDigits) {
                    notificationService.error("El número telefónico no cumple con la cantidad de digitos mínimos y máximos de acuerdo al país");
                    return false;
                  }
                }
                $scope.addBatchtoBatchs();

              };

              $scope.addBatchtoBatchs = function () {
                $scope.batchs.push($scope.batch);
                $scope.batch = [];
                $("#batchemail").focus();
              }

              $scope.removeBatchtoBatchs = function (index) {
                $scope.batchs.splice(index, 1);
              }
              $scope.validateContactBatch = function () {

                if ($scope.batchs.length <= 0) {
                  notificationService.error("Debes ingresar por lo menos un contacto");
                } else {
                  $scope.misc.disabledSaveButton = true;
                  $scope.misc.loaderSaveShow = true;
                  restService.validatecontactbatch(arrayConstruct.toObject($scope.batchs), $rootScope.contactlist.idContactlist).then(function (data) {
                    $scope.contacterror = data;
                    if ($scope.contacterror.length > 0) {
                      $scope.misc.disabledSaveButton = false;
                      $scope.misc.loaderSaveShow = false;
                      openModal();
                    } else {
                      $scope.addcontactbatch();
                    }
                  });
                }
              }

              $scope.addcontactbatch = function () {
                if (!$scope.validatePhones()) {
                  $scope.misc.disabledSaveButton = false;
                  $scope.misc.loaderSaveShow = false;
                }

                restService.addcontactbatch(arrayConstruct.toObject($scope.batchs), idContactlist).then(function (data) {
                  $window.location.href = '#/';
                  notificationService.success(data.message);
                });
              };

              $scope.validatePhones = function () {
                var batchsLength = $scope.batchs.length;
                for (var i = 0; i < batchsLength; i++) {
                  if (typeof $scope.batchs[i].indicative !== "undefined" && typeof $scope.batchs[i].phone !== "undefined") {
                    var length = $scope.listIndicatives.length;
                    var country;
                    for (var index = 0; index < length; index++) {
                      if ($scope.batchs[i].indicative == $scope.listIndicatives[index].idCountry) {
                        country = $scope.listIndicatives[index];
                        break;
                      }
                    }
                    var phone = String($scope.batchs[i].phone);
                    if (phone.length < country.minDigits || phone.length > country.maxDigits) {
                      notificationService.error("El número <b>" + $scope.batchs[i].phone + "</b> telefónico no cumple con la cantidad de digitos mínimos y máximos de acuerdo al país");
                      return false;
                    }
                  }
                }

                return true;
              };

              $scope.updateNameIndicative = function (i) {
                var length = $scope.listIndicatives.length;
                var country;
                for (var index = 0; index < length; index++) {
                  if ($scope.batchs[i].indicative == $scope.listIndicatives[index].idCountry) {
                    country = $scope.listIndicatives[index];
                    break;
                  }
                }
                $scope.batchs[i].nameIndicative = "(+" + country.phoneCode + ") " + country.name;
              };
            }])
          .controller('HistoryController', ['$scope', 'restService', 'notificationService', '$window', 'arrayConstruct', 'setData', '$rootScope', function ($scope, restService, notificationService, $window, arrayConstruct, setData, $rootScope) {
              $scope.fullUrlBase = fullUrlBase;
              $scope.templateBase = templateBase;
              $scope.idContactlist = idContactlist;
              $scope.idContact = idContact;

              $scope.getOneContact = function () {
                restService.getOneContact($scope.idContact).then(function (data) {
                  $scope.contact = data;
                });
              }

              //TODO SOBRE ENVIOS DE SMS
              $scope.initialSMS = 0;
              $scope.pageSMS = 1;
              $scope.nameSMS = "";
              $scope.forwardSMS = function () {
                $scope.initialSMS += 1;
                $scope.pageSMS += 1;
                $scope.getAllSMS();
              };
              $scope.fastforwardSMS = function () {
                $scope.initialSMS = ($scope.sms.total_pages - 1);
                $scope.pageSMS = $scope.sms.total_pages;
                $scope.getAllSMS();
              };
              $scope.backwardSMS = function () {
                $scope.initialSMS -= 1;
                $scope.pageSMS -= 1;
                $scope.getAllSMS();
              };
              $scope.fastbackwardSMS = function () {
                $scope.initialSMS = 0;
                $scope.pageSMS = 1;
                $scope.getAllSMS();
              };

              $scope.getAllSMS = function () {
                name = "1";
                if ($scope.nameSMS) {
                  var name = $scope.nameSMS;
                }
                restService.getAllSMS($scope.idContact, $scope.initialSMS, name).then(function (data) {
                  $scope.sms = data;
                });
              }
              //FIN DE TODO SOBRE ENVIOS DE SMS

              //TODO SOBRE ENVIOS DE CORREO
              $scope.initialMAIL = 0;
              $scope.pageMAIL = 1;
              $scope.nameMAIL = "";
              $scope.forwardMAIL = function () {
                $scope.initialMAIL += 1;
                $scope.pageMAIL += 1;
                $scope.getAllMAIL();
              };
              $scope.fastforwardMAIL = function () {
                $scope.initialMAIL = ($scope.mail.total_pages - 1);
                $scope.pageMAIL = $scope.mail.total_pages;
                $scope.getAllMAIL();
              };
              $scope.backwardMAIL = function () {
                $scope.initialMAIL -= 1;
                $scope.pageMAIL -= 1;
                $scope.getAllMAIL();
              };
              $scope.fastbackwardMAIL = function () {
                $scope.initialMAIL = 0;
                $scope.pageMAIL = 1;
                $scope.getAllMAIL();
              };

              $scope.getAllMAIL = function () {
                name = "1";
                if ($scope.nameMAIL) {
                  var name = $scope.nameMAIL;
                }
                restService.getAllMAIL($scope.idContact, $scope.initialMAIL, name).then(function (data) {
                  $scope.mail = data;
                });
              }
              //FIN DE TODO SOBRE ENVIOS DE CORREO

              //Previsualizar
              $scope.previewmailtempcont = function (id) {
                restService.previewMailTemplateContent(id).then(function (data) {

                  var e = data.template;
                  $('#content-preview').empty();
                  $('<iframe frameborder="0" width="100%" height="100%"/>').appendTo('#content-preview').contents().find('body').append(e);
                  $('#myModal').modal('show');
                });
              };
              //Fin previsualizar

              $scope.getOneContact();
              $scope.getAllSMS();
              $scope.getAllMAIL();
            }])
          .controller('NewcontactController', ['$scope', 'restService', 'notificationService', '$window', '$rootScope', 'setData', function ($scope, restService, notificationService, $window, $rootScope, setData) {
              $scope.progressbar = true;
              $scope.contact = new Object();

              $scope.initComponents = function () {
                restService.getAllIndicatives().then(function (response) {
                  $scope.listIndicatives = response;
                }).catch(function (data) {
                  notificationService.error(data.message);
                });
              };

              $scope.functions = {
                dateBeforeRender: function ($dates) {
                  for (var i = 0; i < $dates.length; i++) {
                    if (new Date().getTime() < $dates[i].utcDateValue) {
                      $dates[i].selectable = false;
                    }
                  }
                }
              };

              $scope.addContact = function () {
                $scope.progressbar = false;
                $scope.contact.validateConfirm = false;
                if($scope.contact.email != undefined){
                  $scope.contact.email = $scope.contact.email.toLowerCase();
                }
                if (typeof $scope.contact.indicative !== "undefined" && typeof $scope.contact.phone !== "undefined") {
                  var length = $scope.listIndicatives.length;
                  var country;
                  for (var index = 0; index < length; index++) {
                    if ($scope.contact.indicative == $scope.listIndicatives[index].idCountry) {
                      country = $scope.listIndicatives[index];
                      break;
                    }
                  }
                  if ($scope.contact.phone.length < country.minDigits || $scope.contact.phone.length > country.maxDigits) {
                    notificationService.error("El número telefónico no cumple con la cantidad de digitos mínimos y máximos de acuerdo al país");
                    $scope.progressbar = true;
                    $scope.contact.validateConfirm = true;
                    return false;
                  }
                }

                restService.addContact($scope.contact, idContactlist).then(function (data) {
                  notificationService.success(data.message);
                  $scope.getAllInfoContactlist();
                  $window.location.href = '#/';
                }).catch(function (data) {
                  $scope.progressbar = true;
                  if (data.code && (data.code == 409 || data.code == 410)) {
                    $scope.errorCreateContact = data;
                    openModalConfirm();
                  }

                });
              };

              $scope.getAllInfoContactlist = function () {
                restService.getOneContactlist(idContactlist).then(function (data) {
                  setData.setData(data);
                  $rootScope.contactlist = data;
                });
              };

              $scope.saveContactConfirm = function () {
                $scope.progressbar = false;
                $scope.contact.validateConfirm = true;
                restService.addContact($scope.contact, idContactlist).then(function (data) {
                  $scope.getAllInfoContactlist();
                  notificationService.success(data.message);
                  $window.location.href = '#/';
                }).catch(function (data) {
                  $scope.progressbar = true;
                  notificationService.error(data.message);
                });
              };
            }])
          .controller('indexController', ['$scope', '$routeParams', '$window', 'restService', 'notificationService', 'setData', '$rootScope', function ($scope, $routeParams, $window, restService, notificationService, setData, $rootScope) {
              restService.getOneContactlist(idContactlist).then(function (data) {
                //                console.log(data);
                setData.setData(data);
                $rootScope.contactlist = setData.getData();

              });

            }]);
})();
