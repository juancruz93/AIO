(function () {
    angular.module('apikey.services', [])
        .factory('restService', ['$http', '$q', 'notificationService', function ($http, $q, notificationService) {

            function getApikeyList(page) {
                var deferred = $q.defer();
                var url = fullUrlBase + 'api/apikey/' + page;
                $http.get(url)
                    .success(function (data) {
                        deferred.resolve(data);
                    })
                    .error(function (data) {
                        deferred.reject(data);
                        notificationService.error(data.message);
                    });

                return deferred.promise;
            }

            function addApikey(idUser) {
                var deferred = $q.defer();
                var url = fullUrlBase + 'api/apikey/create/' + idUser;
                $http.post(url)
                    .success(function (data) {
                        deferred.resolve(data);
                    })
                    .error(function (data) {
                        deferred.reject(data);
                        notificationService.error(data.message);
                    });

                return deferred.promise;
            }

            function editApikey(idUser) {
                var deferred = $q.defer();
                var url = fullUrlBase + 'api/apikey/update/' + idUser;
                $http.put(url)
                    .success(function (data) {
                        deferred.resolve(data);
                    })
                    .error(function (data) {
                        deferred.reject(data);
                        notificationService.error(data.message);
                    });

                return deferred.promise;
            }

            function changeStatusApikey(idUser, data) {
                var deferred = $q.defer();
                var url = fullUrlBase + 'api/apikey/updatestatus/' + idUser;
                $http.put(url, data)
                    .success(function (data) {
                        deferred.resolve(data);
                    })
                    .error(function (data) {
                        deferred.reject(data);
                        notificationService.error(data.message);
                    });

                return deferred.promise;
            }

            function deleteApikey(idUser, data) {
                var deferred = $q.defer();
                var url = fullUrlBase + 'api/apikey/delete/' + idUser;
                $http.delete(url, data)
                    .success(function (data) {
                        deferred.resolve(data);
                    })
                    .error(function (data) {
                        deferred.reject(data);
                        notificationService.error(data.message);
                    });

                return deferred.promise;
            }

            return {
                getApikeyList: getApikeyList,
                addApikey: addApikey,
                editApikey: editApikey,
                changeStatusApikey: changeStatusApikey,
                deleteApikey: deleteApikey
            };
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
})();
