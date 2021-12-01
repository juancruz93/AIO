(function () {
    angular.module('process.controllers', [])
        .controller('indexController', ['$scope', 'restService', '$interval', function ($scope, restService, $interval) {

            //var socket = io.connect('https://testtrack.sigmamovil.com/', {'forceNew': true});
            //var socket = io.connect('http://localhost:3000/', {'forceNew': true});
            var socket = io.connect('https://ws.sigmamovil.com/', {'forceNew': true});
            var arrayProcessMail = [];
            var arrayProcessSms = [];
            var arrayProcessImport = [];

            $scope.processMail = [];
            $scope.processSms = [];
            $scope.processImport = [];
            $scope.socket = socket;

            socket.on('process', function (data) {
                if (data.processMail.length != arrayProcessMail.length) {
                    arrayProcessMail = data.processMail;
                }
                if (data.processSms.length != arrayProcessSms.length) {
                    arrayProcessSms = data.processSms;
                }
                if (data.processImport.length != arrayProcessImport.length) {
                    arrayProcessImport = data.processImport;
                }
            });

            function messagesMailSent() {

                for(var i = 0; i < arrayProcessMail.length; i++){
                    (function () {
                        var temp = i;
                        restService.findMessagesSentMail(arrayProcessMail[temp].idMail).then(function (res) {
                            arrayProcessMail[temp].messagesSent = res.messagesSent;
                        });
                    })();
                }
            }

            function importProcessed() {

                for(var i = 0; i < arrayProcessImport.length; i++){
                    (function () {
                        var temp = i;
                        restService.findProcessedContact(arrayProcessImport[temp].idImportcontactfile).then(function (res) {
                            arrayProcessImport[temp].processed = res.processed;
                        });
                    })();
                }
            }

            $interval(function () {
                messagesMailSent();
                importProcessed();
                $scope.processMail = arrayProcessMail;
                $scope.processSms = arrayProcessSms;
                $scope.processImport = arrayProcessImport;
                //console.log($scope.processSms);
            }, 1500);
            
            $scope.pauseMailAn = function (idMail) {
                //console.log(idMail);
                var data = {
                    idMail: idMail,
                    nameFunc: "pause"
                };
                //console.log(data);
                socket.emit('pause-send-mail', data);
                slideOnTop("El envio de mail se ha pausado correctamente", 3000, "glyphicon glyphicon-warning-sign", "warning");
            };

            $scope.stopServerNode = function () {
                //console.log(idMail);
                var data = {
                    nameFunc: "stopServerNode"
                };
                //console.log(data);
                socket.emit('stop-server-node', data);
                slideOnTop("El servidor de nodeJS se a detenido correctamente", 3000, "glyphicon glyphicon-warning-sign", "warning");
            };

            $scope.restartServerNode = function () {
                //console.log(idMail);
                var data = {
                    nameFunc: "restartServerNode"
                };
                //console.log(data);
                socket.emit('restart-server-node', data);
                //slideOnTop("El servidor de nodeJS se a reiniciado correctamente", 3000, "glyphicon glyphicon-warning-sign", "warning");
            };

            $scope.cancelMailAn = function (idMail) {
                //console.log(idMail);
                var data = {
                    idMail: idMail,
                    nameFunc: "cancel"
                };
                //console.log(data);
                socket.emit('cancel-send-mail', data);
                slideOnTop("El envio de mail se ha cancelado correctamente", 3000, "glyphicon glyphicon-warning-sign", "warning");
            };

            $scope.pauseSmsAn = function (idSms) {
                var data = {
                    idSms: idSms,
                    nameFunc: "pause"
                };
                //console.log(data);
                socket.emit('pause-send-sms', data);
                slideOnTop("El envio de sms se ha pausado correctamente", 3000, "glyphicon glyphicon-warning-sign", "warning");
            }

            $scope.cancelSmsAn = function (idSms) {
                var data = {
                    idSms: idSms,
                    nameFunc: "cancel"
                };
                //console.log(data);
                socket.emit('cancel-send-sms', data);
                slideOnTop("El envio de sms se ha cancelado correctamente", 3000, "glyphicon glyphicon-warning-sign", "warning");
            }
        }])
        .controller('importDetailController', ['$scope', 'restService', function ($scope, restService) {

            if (typeof firstStatus == "undefined") {
                $scope.nameStaus = "finished";
                $scope.data = "";
            } else {
                $scope.nameStaus = firstStatus;
                $scope.data = {
                    rows: rows,
                    imported: imported,
                    repeated: repeated,
                    invalids: invalids
                };
            }


            $scope.progressbar = true;

            /*faltan algunas variables para calcular el total*/
            $scope.totals = function (invalids, repeated) {
                var suma = parseInt(invalids) + parseInt(repeated);
                return suma;
            }
            $scope.changeStatus = function (status) {
                var string = '';
                switch (status) {
                    case 'preprocessing':
                        string = "Preprocesando";
                        break;
                    case 'processing':
                        string = 'Procesando';
                        break;
                    case 'saving':
                        string = 'Guardando';
                        break;
                    case 'canceled':
                        string = 'Cancelado';
                        break;
                    case 'pending':
                        string = 'Pendiente';
                        break;
                    case 'finished':
                        string = 'Finalizado';
                        break;
                }
                return string;
            };

            if ($scope.nameStaus != 'finished' && $scope.nameStaus != 'canceled') {
                $scope.progressbar = false;
                var int = setInterval(function () {
                    restService.getStatus().then(function (data) {
                        //console.log(data);
                        $scope.data = data;
                        $scope.nameStaus = data.status;
                        if ($scope.nameStaus == 'finished') {
                            $scope.progressbar = true;
                            return clearInterval(int);
                        }
                    });
                }, 1000);
            }

        }])
})();
