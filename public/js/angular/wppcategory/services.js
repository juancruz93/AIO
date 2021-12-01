angular.module('wppcategory.services', [])
    .service('RestServices', function ($http, $q, constantWppCategory, notificationService) {
        this.getAll = function (page, filter) {
            var deferred = $q.defer();
            var url = constantWppCategory.urlPeticion.getWppCategory + page;
            $http.post(url, filter)
                .success(function (data) {
                    deferred.resolve(data);
                })
                .error(function (data) {
                    deferred.reject(data);
                    notificationService.error(data.message);
                });
            return deferred.promise;
        },
        this.deleteCategory = function (data){
            var url = constantWppCategory.urlPeticion.deletewppcategory;
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
        }
    })
    .factory('notificationService', function (constantWppCategory) {
        var duration = constantWppCategory.messageNotification.duration;
        var styles = constantWppCategory.messageNotification.styles;
        function error(message) {
            slideOnTop(message, 3000, styles.danger.icon, styles.danger.color);
        }
        function success(message) {
            slideOnTop(message, 3000, styles.success.icon, styles.success.color);
        }
        function warning(message) {
            slideOnTop(message, 3000, styles.warning.icon, styles.warning.color);
        }
        function notice(message) {
            slideOnTop(message, 3000, styles.notice.icon, styles.notice.color);
        }
        function info(message) {
            slideOnTop(message, 3000, styles.info.icon, styles.info.color);
        }
        return {
            error: error,
            success: success,
            warning: warning,
            notice: notice,
            info: info
        };
    });