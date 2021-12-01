(function () {
  angular.module('knowledgebase.services', [])
          .factory('restService', ['$http', '$q', 'notificationService', function ($http, $q, notificationService) {
              function importcsv(data) {
                var deferred = $q.defer();
                   $http.post(fullUrlBase + 'api/knowledgebase/importcsv', data)
                        .success(function (data) {
                          deferred.resolve(data);
                        })
                        .error(function (data) {
                          deferred.reject(data);
                          notificationService.error(data.message);
                        });

                return deferred.promise;
              }
              
              function getAll(page) {
                var deferred = $q.defer();
                   $http.post(fullUrlBase + 'api/knowledgebase/getimports/' + page)
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
                importcsv: importcsv,
                getAll: getAll
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
