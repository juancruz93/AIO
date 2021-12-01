angular.module('whatsapp.controllers', [])
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
    .factory('socket', function ($rootScope) {
        return {
            on: function (eventName, callback) {
                socket.on(eventName, function () {
                    var args = arguments;
                    $rootScope.$apply(function () {
                        callback.apply(socket, args);
                    });
                });
            },
            emit: function (eventName, data, callback) {
                socket.emit(eventName, data, function () {
                    var args = arguments;
                    $rootScope.$apply(function () {
                        if (callback) {
                            callback.apply(socket, args);
                        }
                    });
                })
            }
        }
    })
    .controller('main', ['$scope', '$state', 'contantWhatsapp', 'restservices', '$q', 'notificationService', 'socket', function ($scope, $state, contantWhatsapp, restservices, $q, notificationService, socket) {

        $scope.setAll = function (data) {
            $scope.misc.whatsapp = data;
        };

        $scope.search = function () {
            $scope.getInitial();
        };

        $scope.searchcategory = function () {
            if ($scope.data.filter.category.length >= contantWhatsapp.values.messages.initValueOne) {
                $scope.getInitial();
            } else {
                $scope.getInitial();
            }
        };

        $scope.statusFunc = function () {
            console.log("statusFunc: ", $scope.data.filter.wppStatus);
            $scope.getInitial();
        };

        $scope.getCategory = function () {
            restservices.getCategory().then(function (data) {
                $scope.wppCategory = data;
            });
        };

        $scope.initVariable = function () {
            $scope.data = {};
            $scope.node = {};
            $scope.misc = {};
            $scope.misc.initial = 0;
            $scope.misc.page = 1;
            $scope.data.filter = { category: [] };
        };

        $scope.getInitial = function () {
            var arrInitialPeticion = [restservices.getAllWhatsapp($scope.misc.initial, $scope.data.filter)];
            $q.all(arrInitialPeticion).then(function (data) {
                $scope.setAll(data[0].data);
            }).catch(function (error) {
                notificationService.error(error.message);
            })
        };
        /**
         * observador para filtros de fecha
         */
        $scope.$watch('[data.filter.dateinitial,data.filter.dateend]', function () {

            if (angular.isDefined($scope.data.filter.dateinitial) & angular.isDefined($scope.data.filter.dateend)) {
                //console.log($scope.data.filter);
                $scope.getInitial();
            }
        });
        $scope.initVariable();
        $scope.getCategory();
        $scope.getInitial();
    }])
    .controller('create', ['$scope', '$state', 'contantWhatsapp', 'restservices', '$q', 'notificationService', 'socket', function ($scope, $state, contantWhatsapp, restservices, $q, notificationService, socket) {
        //CONTROLADOR PARA CREACION DE ENVIOS WPP
        $.fn.datetimepicker.defaults = {
            maskInput: false,
            pickDate: true,
            pickTime: true,
            startDate: new Date()
        };
        $('.datetimepicker').datetimepicker({
            format: contantWhatsapp.dTPicker.frmt,
            language: contantWhatsapp.dTPicker.lng
        });

        //OBTIENE LAS CATEGORIAS DE LOS ENVIOS DE WPP
        $scope.getCategory = function () {
            restservices.getCategory().then(function (data) {
                $scope.wppCategory = data;
            });
        };

        //OBTIENE LAS LISTAS DE CONTACTO DE TIPO WPP, QUE EN BD TENGAN EL CAMPO "listWhatsApp" EN 1
        $scope.getContactListWpp = function () {
            restservices.getContactListWpp().then(function (data) {
                $scope.contactListWpp = data;
            });
        };
        //OBTIENE LAS PLANTILLAS HSM CREADAS
        $scope.getHsmTemplates = function () {
            restservices.getHsmTemplates().then(function (data) {
                $scope.listHsmTemplates = data;
            });
        };

        //CONTADOR DE CONTACTOS SEGUN LC ELEGIDA
        $scope.countContacts = function(){
            var data = {
                id: $scope.contactlist
            };
            console.log("data: ",data);
            restservices.countContacts($scope.contactlist).then(function (data) {
                $scope.listHsmTemplates = data;
                $scope.countContactsApproximate = data.data;
                console.log('countContacts:',data);
                if($scope.countContactsApproximate.counts > 40000 && !$scope.divide){
                  slideOnTop("Sobrepasa el maximo permitido de 40.000 Contactos", 3000, "glyphicon glyphicon-remove-sign", "warning");
                  return;
                }
                $scope.triggerEmailNotificationBalanceSmsContact($scope.countContactsApproximate.counts);
            });
        };

        $scope.getCategory();
        $scope.getContactListWpp();
        $scope.getHsmTemplates();

    }])
    .factory('notificationService', function () {
        function error(message) {
            slideOnTop(message, 4000, 'glyphicon glyphicon-remove-circle', 'danger');
        }

        function success(message) {
            slideOnTop(message, 4000, 'glyphicon glyphicon-ok-circle', 'success');
        }

        function warning(message) {
            slideOnTop(message, 4000, 'glyphicon glyphicon-exclamation-sign', 'warning');
        }

        function notice(message) {
            slideOnTop(message, 4000, 'glyphicon glyphicon-exclamation-sign', 'notice');
        }

        function primary(message) {
            slideOnTop(message, 4000, 'glyphicon glyphicon-exclamation-sign', 'primary');
        }

        return {
            error: error,
            success: success,
            warning: warning,
            notice: notice,
            primary: primary
        };
    });