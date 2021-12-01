  angular.module('smscategory.services', [])
          .service('RestServices', function ($http, $q, constantSmsCategory, notificationService) {
            this.getAll = function (page, filter) {
              var deferred = $q.defer();
              var url = constantSmsCategory.urlPeticion.getSmsCategory + page;
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
              var url = constantSmsCategory.urlPeticion.deletesmscategory;
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
          .factory('notificationService', function (constantSmsCategory) {
            var duration = constantSmsCategory.messageNotification.duration;
            var styles = constantSmsCategory.messageNotification.styles;
            function error(message) {
              slideOnTop(message, duration, styles.danger.icon, styles.danger.color);
            }
            function success(message) {
              slideOnTop(message, duration, styles.success.icon, styles.success.color);
            }
            function warning(message) {
              slideOnTop(message, duration, styles.warning.icon, styles.warning.color);
            }
            function notice(message) {
              slideOnTop(message, duration, styles.notice.icon, styles.notice.color);
            }
            function info(message) {
              slideOnTop(message, duration, styles.info.icon, styles.info.color);
            }
            return {
              error: error,
              success: success,
              warning: warning,
              notice: notice,
              info: info
            };
          });
