(function () {
  angular.module('emailsender.services', [])
          .service('RestServices', function ($http, $q, notificationService, contantemailsender) {
            this.list = function (page, data) {
              var url = fullUrlBase + contantemailsender.UrlPeticion.list + page;
              var defered = $q.defer();
              $http.post(url, data)
                      .success(function (data) {
                        defered.resolve(data);
                      })
                      .error(function (data) {
                        defered.reject(data);
                        notificationService.error(data.message);
                      });

              return defered.promise;
            };

            this.delete = function (id) {
              var url = fullUrlBase + contantemailsender.UrlPeticion.delete;
              var defered = $q.defer();
              $http.post(url, id)
                      .success(function (data) {
                        defered.resolve(data);
                      })
                      .error(function (data) {
                        defered.reject(data);
                      });

              return defered.promise;
            };

            this.save = function (data) {
              var url = fullUrlBase + contantemailsender.UrlPeticion.save;
              var defered = $q.defer();
              $http.post(url, data)
                      .success(function (data) {
                        defered.resolve(data);
                      })
                      .error(function (data) {
                        defered.reject(data);
                        notificationService.error(data.message);
                      });

              return defered.promise;
            };

            this.getemailsender = function (id) {
              var defer = $q.defer();
              var promise = defer.promise;
              let url = fullUrlBase + contantemailsender.UrlPeticion.get + id;
              $http.get(url).then(function (resolve) {
                defer.resolve(resolve);
              }).catch(function (reject) {
                defer.reject(reject);
              });
              return promise;
            };

            this.edit = function (data) {
              var url = fullUrlBase + contantemailsender.UrlPeticion.edit;
              var defered = $q.defer();
              $http.post(url, data)
                      .success(function (data) {
                        defered.resolve(data);
                      })
                      .error(function (data) {
                        defered.reject(data);
                        notificationService.error(data.message);
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