(function () {
    angular.module('accountcategory.services', [])
        .service('RestServices', function ($http, $q, notificationService) {

            this.listAccountCategorys = function (page, data) {
                var deferred = $q.defer();
                var url = fullUrlBase + 'api/accountcategory/list/' + page;
                $http.post(url, data)
                    .success(function (res) {
                        deferred.resolve(res);
                    })
                    .error(function (res) {
                        deferred.reject(res);
                        notificationService.error(res.message);
                    });
                return deferred.promise;
            };

            this.createAccountCategory = function (data) {
                var deferred = $q.defer();
                var url = fullUrlBase + 'api/accountcategory/save';
                $http.post(url, data)
                    .success(function (res) {
                        deferred.resolve(res);
                    })
                    .error(function (res) {
                        deferred.reject(res);
                        notificationService.error(res.message);
                    });
                return deferred.promise;
            };

            this.get = function (idAccountCategory) {
                var deferred = $q.defer();
                var url = fullUrlBase + 'api/accountcategory/get/' + idAccountCategory;
                $http.get(url)
                    .success(function (res) {
                        deferred.resolve(res);
                    })
                    .error(function (res) {
                        deferred.reject(res);
                        notificationService.error(res.message);
                    });
                return deferred.promise;
            };

            this.editAccountCategory = function (data) {
                var url = fullUrlBase + "api/accountcategory/edit";
                var defered = $q.defer();
                $http.put(url, data)
                    .success(function (data) {
                        defered.resolve(data);
                    })
                    .error(function (data) {
                        defered.reject(data);
                        notificationService.error(data.message);
                    });

                return defered.promise;
            };

            this.delete = function (idAccountCategory) {
                var url = fullUrlBase + "api/accountcategory/delete/" + idAccountCategory;
                var defered = $q.defer();
                $http.delete(url)
                    .success(function (data) {
                        defered.resolve(data);
                    })
                    .error(function (data) {
                        defered.reject(data);
                    });

                return defered.promise;
            };

        })
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
        })
})();