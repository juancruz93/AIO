(function () {
  angular.module('statisallied.services', [])
          .service('restService', ['$http', '$q', 'notificationService', function ($http, $q, notificationService) {
              function getAll(page) {
                var deferred = $q.defer();
                $http.get(fullUrlBase + 'api/statisallied/getstatisallied/' + page)
                        .success(function (data) {
                          deferred.resolve(data);
                        })
                        .error(function (data) {
                          deferred.reject(data);
                          notificationService.error(data.message);
                        });

                return deferred.promise;
              }

              function downloadReport(data) {
             
                var deferred = $q.defer();
                
                var url = fullUrlBase + 'api/statisallied/downloadreport';
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
              return {
                getAll: getAll,
                downloadReport: downloadReport
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
