(function () {
    angular.module('process.services', [])
        .factory('restService', ['$http', '$q', 'notificationService', function ($http, $q, notificationService) {

            function getStatus() {
                var deferred = $q.defer();
                var url = fullUrlBase + 'process/getstatus/' + idImportcontactfile;

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

            function findMessagesSentMail(idMail) {
                var deferred = $q.defer();
                var url = fullUrlBase + 'api/sendmail/findmailmessagessent/' + idMail;
                $http.get(url)
                    .success(function (data) {
                        deferred.resolve(data);
                    })
                    .error(function (data) {
                        deferred.reject(data);
                        notificationService.error(data);
                    });
                return deferred.promise;
            }

            function findProcessedContact(idImportcontactfile) {
                var deferred = $q.defer();
                var url = fullUrlBase + 'api/sendmail/findprocessedcontact/' + idImportcontactfile;
                $http.get(url)
                    .success(function (data) {
                        deferred.resolve(data);
                    })
                    .error(function (data) {
                        deferred.reject(data);
                        notificationService.error(data);
                    });
                return deferred.promise;
            }

            return {
                getStatus: getStatus,
                findMessagesSentMail: findMessagesSentMail,
                findProcessedContact: findProcessedContact
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
