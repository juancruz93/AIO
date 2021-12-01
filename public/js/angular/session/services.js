(function () {
  angular.module("session.services", [])
          .service("restServices", ["$http", "$q", "notificationService", function ($http, $q, notificationService) {
              this.loginEmail = function (data) {
                var defered = $q.defer();
                var url = fullUrlBase + "api/session/login";
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

              this.loginPass = function (data) {
                var defered = $q.defer();
                var url = fullUrlBase + "api/session/loginp";
                $http.post(url, data)
                        .success(function (data) {
                          defered.resolve(data);
                        })
                        .error(function (data) {
                          defered.reject(data);
                        });

                return defered.promise;
              };

              this.getAppIdFB = function () {
                let defer = $q.defer();
                let url = fullUrlBase + "api/register/appidfb";

                $http.get(url)
                        .success(function (data) {
                          defer.resolve(data);
                        })
                        .error(function (data) {
                          defer.reject(data);
                        });

                return defer.promise;
              };

              this.loginWithFacebook = function (data) {
                let defer = $q.defer();
                let url = fullUrlBase + "api/session/loginfb";

                $http.post(url, data)
                        .success(function (response) {
                          defer.resolve(response);
                        })
                        .error(function (response) {
                          defer.reject(response);
                        });

                return defer.promise;
              };

              this.emailRecoverpass = function (data) {
                var defered = $q.defer();
                var url = fullUrlBase + "api/session/login";
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

              this.recoverpassGenerateMail = function (email, rol) {
                var data = {
                  email:email,
                  rol:rol
                };
                var defered = $q.defer();
                var url = fullUrlBase + "api/session/recoverpassgenerate";
                $http.post(url, data)
                        .success(function (data) {
                          defered.resolve(data);
                        })
                        .error(function (data) {
                          defered.reject(data);
                        });

                return defered.promise;
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
            
            function successSession(message) {
              slideOnTop(message, 6000, 'glyphicon glyphicon-ok-circle', 'success');
            }

            return {
              error: error,
              success: success,
              warning: warning,
              notice: notice,
              primary: primary,
              successSession:successSession
            };
          });
})();
