(function () {
  angular.module('scheduled.services', [])
          .factory('restService', ['$http', '$q', 'notificationService', function ($http, $q, notificationService) {
              function getAll(initialSMS,initialMail,filter) {
                var deferred = $q.defer();
                var url = fullUrlBase + 'api/scheduled/getscheduled/' + initialSMS + '/' + initialMail; 
                $http.post(url, filter)
                        .success(function (data) {
                          deferred.resolve(data);
                        })
                        .error(function (data) {
                          deferred.reject(data);
                          notificationService.error(data.message);
                        });

                return deferred.promise;
              }

              function cancelMail(idMail) {
                  var url = fullUrlBase + 'api/mail/cancelmail/' + idMail;
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
              }

              return {
                getAll: getAll,
                cancelMail: cancelMail
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

            function info(message) {
              slideOnTop(message, 4000, 'glyphicon glyphicon-exclamation-sign', 'info');
            }

            return {
              error: error,
              success: success,
              warning: warning,
              notice: notice,
              info: info
            };
          });
})();
