/**
 * Autor: Kevin Andres Ramirez Alzate
 * Comment: Pana si lo ve? esta todo bonito y organizado si lo va a tocar dejelo asi de bonito 
 */
(function () {
  angular.module('dashboardconfigService', [])
          .service('restService',['$q', '$http', 'constantDashboardConfig', function ($q, $http, constantDashboardConfig) {
            this.getImageAccountDashboard = function (idAccount, page) {
              var deferred = $q.defer();
              if (typeof page == "undefined")
                page = 0;
              $http.get(constantDashboardConfig.urlPeticion.getImage + idAccount + '/' + page)
                      .success(function (data) {
                        deferred.resolve(data);
                      })
                      .error(function (data) {
                        deferred.reject(data);
                        notificationService.error(data.message);
                      });

              return deferred.promise;
            }

            this.getConfigDashboard = function (idAccount) {
              var deferred = $q.defer();
              $http.get(constantDashboardConfig.urlPeticion.getConfigDashboard + idAccount)
                      .success(function (data) {
                        deferred.resolve(data);
                      })
                      .error(function (data) {
                        deferred.reject(data);
                      });

              return deferred.promise;
            }

            this.getConfigDefaultDashboard = function () {
              var deferred = $q.defer();
              $http.get(constantDashboardConfig.urlPeticion.getDefaultDashboard)
                      .success(function (data) {
                        deferred.resolve(data);
                      })
                      .error(function (data) {
                        deferred.reject(data);
                      });

              return deferred.promise;
            }

            this.saveConfig = function (idAccount, data) {
              var deferred = $q.defer();
              $http.post(constantDashboardConfig.urlPeticion.saveConfigDashboard + idAccount, data)
                      .success(function (data) {
                        deferred.resolve(data);
                      })
                      .error(function (data) {
                        deferred.reject(data);
                      });

              return deferred.promise;
            }

            this.getConfigDashboardClient = function (idAccount) {
              var deferred = $q.defer();
              $http.get(constantDashboardConfig.urlPeticion.getConfigDashboardClient + idAccount)
                      .success(function (data) {
                        deferred.resolve(data);
                      })
                      .error(function (data) {
                        deferred.reject(data);
                      });

              return deferred.promise;
            }

            this.getservices = function () {
              var defer = $q.defer();
              var url = fullUrlBase + "api/saxs/getall";
              $http.get(url)
                      .success(function (data) {
                        defer.resolve(data);
                      })
                      .error(function (data) {
                        defer.reject(data);
                      });
              return defer.promise;
            }
        this.saveDKIM = function (idSubaccount,domain) {
            var deferred = $q.defer();
            var url = fullUrlBase + 'api/saxs/savedkim/'+idSubaccount;
            $http.post(url,{domain: domain})

              .success(function (data) {
                deferred.resolve(data);
              })
              .error(function (data) {
                deferred.reject(data);
              });
            return deferred.promise;
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
