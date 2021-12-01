angular.module('smsxemail.services', [])
        .service('RestServices', function ($http, $q, constantPageSmsxemail,notificationService) {
          this.generateKey = function () {
            var defered = $q.defer();
            var url = fullUrlBase + "smsxemail/generatekey";
            $http.get(url).success(function (data) {
              defered.resolve(data);
            })
            .error(function (data) {
              defered.reject(data);
              notificationService.error(data.message)
            });
            return defered.promise;
          };
          this.smsCategory = function () {
            var defered = $q.defer();
            var url = fullUrlBase + "smsxemail/smscategory";
            $http.get(url).success(function (data) {
              defered.resolve(data);
            })
            .error(function (data) {
              defered.reject(data);
              notificationService.error(data.message)
            });
            return defered.promise;
          };
          this.copyKey = function (idSmsxEmail) {
            var defer = $q.defer();
            var promise = defer.promise;
            let url = constantPageSmsxemail.UrlPeticion.copykey + idSmsxEmail;
            $http.get(url).then(function (resolve) {
                defer.resolve(resolve);
            }).catch(function (reject) {
                defer.reject(reject);
            });
            return promise;
          };
          this.createSmsxemail = function (data){
            var defer = $q.defer();
            var promise = defer.promise;
            let url = constantPageSmsxemail.UrlPeticion.create;
            $http.post(url, data).then(function (resolve){
              defer.resolve(resolve);
            }).catch(function (reject){
              defer.reject(reject);
            });
            return promise;
          };
          this.oneSmsxemail = function () {
            var defer = $q.defer();
            var promise = defer.promise;
            let url = constantPageSmsxemail.UrlPeticion.getone;
            $http.get(url).then(function (resolve) {
                defer.resolve(resolve);
            }).catch(function (reject) {
                defer.reject(reject);
            });
            return promise;
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