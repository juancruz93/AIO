(function () {
  angular.module('language.services', [])
          .factory('restService', ['$http', '$q', 'notificationService', function ($http, $q, notificationService) {
              function getAll(page) {
                var deferred = $q.defer();
                   $http.post(fullUrlBase + 'api/language/getlanguage/' + page)
                        .success(function (data) {
                          deferred.resolve(data);
                        })
                        .error(function (data) {
                          deferred.reject(data);
                          notificationService.error(data.message);
                        });

                return deferred.promise;
              }
              
              function getOne(id) {
                
                var deferred = $q.defer();

                $http.get(fullUrlBase + 'api/language/getlanguagefirst/' + id)
                        .success(function (data) {
                          deferred.resolve(data);
                        })
                        .error(function (data) {
                          deferred.reject(data);
                          notificationService.error(data.message);
                        });

                return deferred.promise;
              }
              
              function edit(id, name, shortName) {
                var url = fullUrlBase + 'api/language/edit/' + id;
                var deferred = $q.defer();

                var data = {
                  name: name,
                  shortName: shortName,
                };

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
              
              function deleteLanguage(id) {
                var url = fullUrlBase + 'api/language/delete/' + id;
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
                deleteLanguage: deleteLanguage,
                getOne: getOne,
                edit: edit
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
