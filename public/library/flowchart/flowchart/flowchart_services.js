angularFlowchart.service('flowchartService', ['$templateCache', '$http', '$q', 'notificationService', function ($templateCache, $http, $q, notificationService) {
        this.setCacheTemplate = function (url) {
            return $http.get(url);
        }

        this.getContentTemplate = function (idTemplate) {
            var defer = $q.defer();
            var url = fullUrlBase + "/api/mailtemplate/getcontenttemplate/" + idTemplate;
            $http.get(url)
                    .success(function (data) {
                        //console.log('getContentTemplate', data);
                        if (data.length == 0) {
                            notificationService.error("El template seleccionado no contiene ningun enlace");
                            defer.reject(data);
                        }
                        defer.resolve(data);
                    })
                    .error(function (data) {
                        defer.reject(data);
                        notificationService.error(data.message);
                    });
            return defer.promise;
        }
        //OBTENER LOS SERVICIOS DE LA CUENTA
        this.getservices = function () {
            var defer = $q.defer();
            var url = fullUrlBase + "api/saxs/getall";
            $http.get(url)
                    .success(function (data) {
                      if (data.length == 0) {
                        notificationService.error("No tienes servicios disponibles, para adquirir uno por favor contacta a soporte.");
                        defer.reject(data);
                      }
                      defer.resolve(data);
                    })
                    .error(function (data) {
                      defer.reject(data);
                      notificationService.error(data.message);
                    });
            return defer.promise;
        }

        this.deleteAsset = function (id) {
            var deferred = $q.defer();
            var url = fullUrlBase + 'api/sendmail/deleteAsset';
            $http.post(url, id)
                    .success(function (data) {
                      deferred.resolve(data);
                    })
                    .error(function (data) {
                      deferred.reject(data);
                    });
            return deferred.promise;
        }

        this.getemailsend = function () {
            var deferred = $q.defer();
            $http.get(fullUrlBase + 'mail/emailsender/')
                    .success(function (data) {
                        //console.log('getemailsend', data);
                        if (data.length == 0) {
                            notificationService.error("No se encontro ningun email de remitente.");
                            deferred.reject(data);
                        }
                        deferred.resolve(data);
                    })
                    .error(function (data) {
                        deferred.reject(data);
                        notificationService.error(data);
                    });
            return deferred.promise;
        }

        this.getemailname = function () {
            var deferred = $q.defer();
            $http.get(fullUrlBase + 'mail/emailname/')
                    .success(function (data) {
                        //console.log('getemailname', data);
                        if (data.length == 0) {
                            notificationService.error("No se encontro ningun nombre de remitente.");
                            deferred.reject(data);
                        }
                        deferred.resolve(data);
                    })
                    .error(function (data) {
                        deferred.reject(data);
                        notificationService.error(data);
                    });
            return deferred.promise;
        }

        this.getPublicsurvey = function () {
            var deferred = $q.defer();
            $http.get(fullUrlBase + 'api/survey/getpublicsurvey')
                    .success(function (data) {
                        if (data.length == 0) {
                            deferred.reject(data);
                        }
                        deferred.resolve(data);
                    })
                    .error(function (data) {
                        deferred.reject(data);
                        notificationService.error(data);
                    });
            return deferred.promise;
        }

        this.getlinkmailtemplate = function (idTemplate) {
            var deferred = $q.defer();
            $http.get(fullUrlBase + 'api/mailtemplate/getlinkstemplate/' + idTemplate)
                    .success(function (data) {
                        //console.log('getlinkmailtemplate', data);
                        if (data.length == 0) {
                            notificationService.error("El template seleccionado no contiene ningun enlace");
                            deferred.reject(data);
                        }
                        deferred.resolve(data);
                    })
                    .error(function (data) {
                        deferred.reject(data);
                        notificationService.error(data);
                    });
            return deferred.promise;
        }

        this.getallmailtemplate = function () {
            var deferred = $q.defer();
            $http.get(fullUrlBase + 'api/mailtemplate/getallmailtemplate')
                    .success(function (data) {
                        //console.log('getallmailtemplate', data);
                        if (data.length == 0) {
                            notificationService.error("No se encontro ninguna plantilla de correo.");
                            deferred.reject(data);
                        }
                        deferred.resolve(data);
                    })
                    .error(function (data) {
                        deferred.reject(data);
                        notificationService.error(data);
                    });
            return deferred.promise;
        }

        this.getallmailtemplatebyfilter = function (search) {
            var deferred = $q.defer();

            $http.post(fullUrlBase + 'api/mailtemplate/getallmailtemplatebyfilter', search)
                    .success(function (data) {
                        //console.log('getallmailtemplate', data);
                        //if (data.length == 0) {
                        //    notificationService.error("No se encontro ninguna plantilla de correo.");
                        //    deferred.reject(data);
                        //}
                        deferred.resolve(data);
                    })
                    .error(function (data) {
                        deferred.reject(data);
                        notificationService.error(data);
                    });
            return deferred.promise;
        }

        this.getallmailcategory = function () {
            var deferred = $q.defer();
            $http.get(fullUrlBase + 'api/mailcategory/getallmailcategory')
                    .success(function (data) {
                        //console.log('getallmailcategory', data);
                        //if (data.length == 0) {
                        //    notificationService.error("No se encontro ninguna categoria de correo.");
                        //    deferred.reject(data);
                        //}
                        deferred.resolve(data);
                    })
                    .error(function (data) {
                        deferred.reject(data);
                        notificationService.error(data);
                    });
            return deferred.promise;
        }

        this.getallsmstemplate = function () {
            var deferred = $q.defer();
            $http.get(fullUrlBase + 'api/smstemplate/getallsmstemplate')
                    .success(function (data) {
                        //if (data.length == 0) {
                        //    notificationService.error("No se encontro ninguna plantilla de sms.");
                        //    deferred.reject(data);
                        //}
                        deferred.resolve(data);
                    })
                    .error(function (data) {
                        deferred.reject(data);
                        notificationService.error(data);
                    });
            return deferred.promise;
        }

        this.getContactlist = function () {
            var deferred = $q.defer();
            var url = fullUrlBase + 'api/sendmail/getcontactlist';
            $http.get(url)
                    .success(function (data) {
                        if (data.length == 0) {
                            notificationService.error("No se encontro ninguna lista de contacto.");
                            deferred.reject(data);
                        }
                        deferred.resolve(data);
                    })
                    .error(function (data) {
                        deferred.reject(data);
                        notificationService.error(data.message);
                    });
            return deferred.promise;
        }

        this.getSegment = function () {
            var deferred = $q.defer();
            var url = fullUrlBase + 'api/sendmail/getsegment';
            $http.get(url)
                    .success(function (data) {
                        if (data.length == 0) {
                            notificationService.error("No se encontro ningun segmento.");
                            deferred.reject(data);
                        }
                        deferred.resolve(data);
                    })
                    .error(function (data) {
                        deferred.reject(data);
                        notificationService.error(data.message);
                    });
            return deferred.promise;
        }

        this.getAllSmsCategory = function () {
            var deferred = $q.defer();
            var url = fullUrlBase + 'api/smscategory/getall';
            $http.get(url)
                    .success(function (data) {
                        if (data.length == 0) {
                            notificationService.error("No se encontro ninguna categoria de sms.");
                            deferred.reject(data);
                        }
                        deferred.resolve(data);
                    })
                    .error(function (data) {
                        deferred.reject(data);
                        notificationService.error(data.message);
                    });
            return deferred.promise;
        }


    }]);
angularFlowchart.factory('notificationService', function () {
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
angularFlowchart.factory("flowchartDataModal", function () {
    var obj = {};
    var data = {};
    obj.getData = function () {
        return data;
    }

    obj.setData = function (chart, idNode, method, idLink) {
        data = {chart: chart, idNode: idNode, method: method, idLink: idLink};
    }

    return obj;
});
angularFlowchart.factory("setDataSms", function () {
    var obj = {};
    var data = {};
    data.category = [];
    data.template = [];
    obj.getData = function () {
        return data;
    }

    obj.setData = function (arrSmsTemplate, category) {
        if (category) {
            data.category = arrSmsTemplate;
        } else {
            data.template = arrSmsTemplate;
        }

    }

    return obj;
});
angularFlowchart.factory("setDataMail", function () {
    var obj = {};
    var data = {};
    data.category = [];
    data.template = [];
    obj.getData = function () {
        return data;
    }

    obj.setData = function (arrMail, category) {
        if (category) {
            data.category = arrMail;
        } else {
            data.template = arrMail;
        }

    }

    return obj;
});
