(function () {
    angular.module('wpptemplate.controller', [])
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
        .controller('listController', ['$scope', 'RestServices', '$stateParams', 'notificationService', '$state', function ($scope, RestServices, $stateParams, notificationService, $state) {

            //FILTROS
            $scope.filterCateg = function () {
                RestServices.listWppTemplate($scope.initial, $scope.data).then(function (data) {
                    var items =  data.items;
                    items.forEach(function(elemento1, indice1) {
                        $scope.listcateg.forEach(function(elemento2, indice2) { 
                            if(elemento1.wppTemplateCategory == elemento2.code){
                                elemento1.wppTemplateCategory = elemento2.name;
                            }
                        });
                    });
                    $scope.list = data;
                });
            };

            $scope.filtername = function () {
                RestServices.listWppTemplate($scope.initial, $scope.data).then(function (data) {
                    $scope.list = data;
                });
            };

            //Se cargan las categorias de plantilla seg??n la cuenta
            /*$scope.loadwpptemplatecategory = function () {
                RestServices.listWppTemplateCategory().then(function (data) {
                    console.log("loadwpptemplatecategory: ",data);
                    $scope.listcateg = data;
                });
            };*/


            $scope.initVariables = function () {
                $scope.initial = 0;
                $scope.page = 1;
                $scope.listcateg = [
                    { code: "ACCOUNT_UPDATE", name: "ACTUALIZACI??N DE CUENTA" },
                    { code: "SHIPPING_UPDATE", name: "ACTUALIZACI??N DE ENV??O" },
                    { code: "PERSONAL_FINANCE_UPDATE", name: "ACTUALIZACI??N DE FINANZAS PERSONALES" },
                    { code: "PAYMENT_UPDATE", name: "ACTUALIZACI??N DE PAGO" },
                    { code: "RESERVATION_UPDATE", name: "ACTUALIZACI??N DE RESERVAS" },
                    { code: "TICKET_UPDATE", name: "ACTUALIZACI??N DE TICKET" },
                    { code: "TRANSPORTATION_UPDATE", name: "ACTUALIZACI??N DE TRANSPORTE" },
                    { code: "ALERT", name: "ALERTA" },
                    { code: "APPOINTMENT_UPDATE", name: "CITA ACTUALIZADA" },
                    { code: "ISSUE_RESOLUTION", name: "RESOLUCI??N DE PROBLEMAS" },
                ];
            };

            $scope.getAll = function () {
                RestServices.getAll($scope.initial).then(function (data) {
                    var items =  data.items;
                    items.forEach(function(elemento1, indice1) {
                        $scope.listcateg.forEach(function(elemento2, indice2) { 
                            if(elemento1.wppTemplateCategory == elemento2.code){
                                elemento1.wppTemplateCategory = elemento2.name;
                            }
                        });
                    });
                    
                    $scope.list = data;
                });
            };

            //EDITAR
            $scope.confirmEdit = function(idWppTemplate, nameWppTemplate){
                $scope.idWppTemplate = idWppTemplate;
                $scope.editnametempwpp = nameWppTemplate;
                openModalEdit();
            };
            $scope.editWpptemplate = function () {
                var data = {
                    id: $scope.idWppTemplate,
                    name: $scope.editnametempwpp
                };
                RestServices.editWppTemplate(data).then(function (data) {
                    closeModalEdit();
                    notificationService.success(data.message);
                    $state.go("index");
                    $scope.getAll();
                });
            };

            //ELIMINAR
            $scope.confirmDelete = function (idWppTemplate) {
                $scope.idWppTemplate = idWppTemplate;
                openModalDelete();
            };
            $scope.deleteWpptemplate = function () {
                RestServices.deleteWpptemplate($scope.idWppTemplate).then(function (data) {
                    closeModalDelete();
                    notificationService.warning(data.message);
                    $state.go("index");
                    $scope.getAll();
                });
            };

            $scope.initVariables();
            //$scope.loadwpptemplatecategory();
            $scope.getAll();
            

            $scope.openPreview = function () {
                $("#preview").addClass('dialog--open');
            }
            $scope.closePreview = function () {
                $("#preview").removeClass('dialog--open');
            }

        }])
        .controller('createController', ['$scope', 'RestServices', '$stateParams', 'notificationService', '$state', function ($scope, RestServices, $stateParams, notificationService, $state) {

            $scope.newcategorytemplatewpp = false;
            $scope.morecaracter = false;
            $scope.validateNameWpp = false;
            $scope.validateTempWpp = false;
            $scope.validateContentWpp = false;
            $scope.listcateg = [
                { code: "ACCOUNT_UPDATE", name: "ACTUALIZACI??N DE CUENTA" },
                { code: "SHIPPING_UPDATE", name: "ACTUALIZACI??N DE ENV??O" },
                { code: "PERSONAL_FINANCE_UPDATE", name: "ACTUALIZACI??N DE FINANZAS PERSONALES" },
                { code: "PAYMENT_UPDATE", name: "ACTUALIZACI??N DE PAGO" },
                { code: "RESERVATION_UPDATE", name: "ACTUALIZACI??N DE RESERVAS" },
                { code: "TICKET_UPDATE", name: "ACTUALIZACI??N DE TICKET" },
                { code: "TRANSPORTATION_UPDATE", name: "ACTUALIZACI??N DE TRANSPORTE" },
                { code: "ALERT", name: "ALERTA" },
                { code: "APPOINTMENT_UPDATE", name: "CITA ACTUALIZADA" },
                { code: "ISSUE_RESOLUTION", name: "RESOLUCI??N DE PROBLEMAS" },
            ];

            $scope.dataMoreInformation = [
                { name: "ACTUALIZACI??N DE CUENTA", description: "Notificar al destinatario del mensaje de un cambio en la configuraci??n de su cuenta.", examples: ["El perfil ha cambiado.", "Las preferencias se actualizan.", "Los ajustes han cambiado.", "La membres??a ha expirado.","La contrase??a ha cambiado."] },
                { name: "ACTUALIZACI??N DE ENV??O", description: "Notificar al destinatario del mensaje de un cambio en el estado de env??o de un producto que ya se ha comprado.", examples: ["El producto se env??a.","Cambios de estado en tr??nsito.","El producto se entrega.","El env??o se retrasa"] },
                { name: "ACTUALIZACI??N DE FINANZAS PERSONALES", description: "Confirmar la actividad financiera de un destinatario de mensajes.", examples: ["Recordatorios de pago de facturas.","Recordatorio de pago programado.","Notificaci??n de recibo de pago.","Confirmaci??n de transferencia de fondos o actualizaci??n.","Otras actividades transaccionales en servicios financieros."] },
                { name: "ACTUALIZACI??N DE PAGO", description: "Notificar al destinatario del mensaje de una actualizaci??n de pago para una transacci??n existente.", examples: ["Enviar un recibo.","Enviar una notificaci??n de inventario agotado.","Notificar que una subasta ha finalizado.","El estado de una transacci??n de pago ha cambiado."] },
                { name: "ACTUALIZACI??N DE RESERVAS", description: "Notificar al destinatario del mensaje de las actualizaciones de una reserva existente.", examples: ["Cambios de itinerario.","Cambios de ubicaci??n","Se confirma la cancelaci??n.","La reserva del hotel se cancela.","Cambios en la hora de recogida del alquiler de coches.","Se confirma la mejora de la habitaci??n."] },
                { name: "ACTUALIZACI??N DE TICKET", description: "Env??a actualizaciones o recordatorios al destinatario del mensaje para un evento para el que una persona ya tiene un ticket.", examples: ["Cambios en la hora de inicio del concierto.","Cambios en la ubicaci??n del evento.","El espect??culo se cancela.","Se ofrece una oportunidad de reembolso."] },
                { name: "ACTUALIZACI??N DE TRANSPORTE", description: "Notificar al destinatario del mensaje acerca de actualizaciones de una reserva de transporte existente.", examples: ["Cambios en el estado del vuelo.","El viaje se cancela.","El viaje se inicia Ferry ha llegado."] },
                { name: "ALERTA", description: "Notificar al destinatario del mensaje de algo informativo.", examples: ["Horario comercial/horas de disponibilidad.","Horas de check-in/check-out."] },
                { name: "CITA ACTUALIZADA", description: "Notificar al destinatario del mensaje de un cambio en una cita existente.", examples: ["Cambios en la hora de la cita.","Cambios en la ubicaci??n de la cita.","La cita se cancela."] },
                { name: "RESOLUCI??N DE PROBLEMAS", description: "Notificar al destinatario del mensaje de una actualizaci??n de un problema de servicio al cliente que se inici?? en una conversaci??n de Messenger, despu??s de una transacci??n.", examples: ["El problema se resuelve.","El estado del problema se actualiza.","El problema requiere una solicitud de informaci??n adicional."] }
            ];

            /*$scope.loadwpptemplatecategory = function () {
                RestServices.listWppTemplateCategory().then(function (data) {
                    //$scope.listcateg = data;
                });
            };
            $scope.loadwpptemplatecategory();*/

            //----------Guardar categoria wpp
            $scope.newCateg = function () {
                $scope.newcategorytemplatewpp = true;
            };

            $scope.cancelCateg = function () {
                $scope.newwpptempcateg = '';
                $scope.newcategorytemplatewpp = false;
            };

            $scope.saveCateg = function () {

                if (angular.isUndefined($scope.newwpptempcateg) || $scope.newwpptempcateg === '') {
                    notificationService.error("El campo de nueva categor??a no puede estar vac??o");
                    return;
                }
                if ($scope.newwpptempcateg.length < 2 || $scope.newwpptempcateg.length > 80) {
                    notificationService.error("El campo de nueva categor??a debe tener m??nimo 2 caracteres y m??ximo 80");
                    return;
                }

                var data = {
                    name: $scope.newwpptempcateg
                };

                RestServices.saveWppTempCateg(data).then(function (data) {
                    notificationService.success(data.message);
                    $scope.newwpptempcateg = '';
                    $scope.newcategorytemplatesms = false;
                    $scope.loadwpptemplatecategory();
                    $scope.smstempcateg = data.idSmsTemplateCategory;
                });

            };

            $scope.opeModalMoreInfo = function () {
                $('#moreInfo').removeClass('modal');
                $('#moreInfo').addClass('dialog dialog--open');
            };

            $scope.validateWppTmpForm = function(){
                if( typeof $scope.nametempwpp == 'undefined'){
                    $scope.validateNameWpp = true;
                    return false;
                }else{
                    $scope.validateNameWpp = false;
                }
                if( typeof $scope.wpptempcateg == 'undefined'){
                    $scope.validateTempWpp = true;
                    return false;
                }else{
                    $scope.validateTempWpp = false;
                }
                if( typeof $scope.contenttempwpp == 'undefined'){
                    $scope.validateContentWpp = true;
                    return false;
                }else{
                    $scope.validateContentWpp = false;
                }
                return true;
            };

            $scope.saveWppTemplate = function () {
                //if($scope.validateWppTmpForm()){
                    var data = {
                    name: $scope.nametempwpp,
                    categ: $scope.wpptempcateg,
                    content: $scope.contenttempwpp
                    };
                    console.log("esta es la data a guardar: ",data);
                    RestServices.saveWppTemplate(data).then(function (data) {
                        notificationService.success(data.message);
                        $state.go("index");
                    });
                //}
                
            };

            $scope.openPreview = function () {
                $("#preview").addClass('dialog--open');
            }
            $scope.closePreview = function () {
                $("#preview").removeClass('dialog--open');
            }

        }])
})();