(function () {
    angular.module('wpptemplate.services', [])
        .service('RestServices', function ($http, $q, notificationService) {
            //DEVUELVE EL LISTADO DE LAS CATEGORIAS DE PLANTILLAS HSM
            this.listWppTemplateCategory = function () {
                var deferred = $q.defer();
                $http.get(fullUrlBase + "api/wpptemplate/listwpptempcategory/")
                    .success(function (data) {
                        deferred.resolve(data);
                    })
                    .error(function (data) {
                        deferred.reject(data);
                        notificationService.error(data.message);
                    });

                return deferred.promise;
            };
            //OBTIENE TODAS LAS PLANTILLAS HSM
            this.getAll = function (page) {
                var deferred = $q.defer();
                $http.post(fullUrlBase + 'api/wpptemplate/listwpptemplate/' + page)
                    .success(function (data) {
                        deferred.resolve(data);
                    })
                    .error(function (data) {
                        deferred.reject(data);
                        notificationService.error(data.message);
                    });

                return deferred.promise;
            };
            //OBTIENE TODAS LAS PLANTILLAS HSM APLICANDOLE PARAMETRO DE FILTRO
            this.listWppTemplate = function (page, data) {
                var deferred = $q.defer();
                $http.post(fullUrlBase + "api/wpptemplate/listwpptemplate/" + page, data)
                    .success(function (data) {
                        deferred.resolve(data);
                    })
                    .error(function (data) {
                        deferred.reject(data);
                        notificationService.error(data.message);
                    });

                return deferred.promise;
            };

            //GUARDAR PLANTILLA HSM
            this.saveWppTemplate = function (data) {
                var url = fullUrlBase + "api/wpptemplate/savewpptemplate";
                var deferred = $q.defer();
                $http.post(url, data)
                        .success(function (data) {
                          deferred.resolve(data);
                        })
                        .error(function (data) {
                          deferred.reject(data);
                          notificationService.error(data.message);
                        });
                return deferred.promise;
            };

            //EDITAR PLANTILLA HSM
            this.editWppTemplate = function (data) {
                var url = fullUrlBase + "api/wpptemplate/editwpptemplate";
                var deferred = $q.defer();
                $http.post(url, data)
                        .success(function (data) {
                          deferred.resolve(data);
                        })
                        .error(function (data) {
                          deferred.reject(data);
                          notificationService.error(data.message);
                        });
                return deferred.promise;
            };

            //ELIMINAR PLANTILLA HSM
            this.deleteWpptemplate = function (id) {
                var url = fullUrlBase + 'api/wpptemplate/deletewpptemplate/' + id;
                var deferred = $q.defer();
                $http.delete(url)
                    .success(function (data) {
                        deferred.resolve(data);
                    })
                    .error(function (data) {
                        deferred.reject(data);
                        notificationService.error(data.message);
                    });

                return deferred.promise;
            };

        })
        .factory('notificationService', function () {
            function error(message) {
                slideOnTop(message, 3000, 'glyphicon glyphicon-remove-circle', 'danger');
            }

            function success(message) {
                slideOnTop(message, 3000, 'glyphicon glyphicon-ok-circle', 'success');
            }

            function warning(message) {
                slideOnTop(message, 3000, 'glyphicon glyphicon-exclamation-sign', 'warning');
            }

            function notice(message) {
                slideOnTop(message, 3000, 'glyphicon glyphicon-exclamation-sign', 'notice');
            }

            function info(message) {
                slideOnTop(message, 3000, 'glyphicon glyphicon-exclamation-sign', 'info');
            }

            return {
                error: error,
                success: success,
                warning: warning,
                notice: notice,
                info: info
            };
        });
    ;
})();