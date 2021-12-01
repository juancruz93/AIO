(function () {
    angular.module("accounting.services", [])
        .service('restServices', ['$http', '$q', 'notificationService', function ($http, $q, notificationService) {
            this.list = function (page) {
                let defer = $q.defer();
                let url = fullUrlBase + "api/accounting/list/" + page;

                $http.get(url)
                    .success(function (response) {
                        defer.resolve(response);
                    })
                    .error(function (error) {
                        defer.reject(error);
                    });

                return defer.promise;
            }
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