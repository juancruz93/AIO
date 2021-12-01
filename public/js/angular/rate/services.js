angular.module('rate.services', [])
        .service('RestServices', function ($http, $q, constantPageRate,notificationService) {
          this.services = function () {
            var defered = $q.defer();
            var url = fullUrlBase + "services/getallservice";
            $http.get(url).success(function (data) {
              defered.resolve(data);
            })
            .error(function (data) {
              defered.reject(data);
              notificationService.error(data.message)
            });
            return defered.promise;
          };
          this.country = function () {
            var defered = $q.defer();
            var url = fullUrlBase + "country/getallcountry";
            $http.get(url).success(function (data) {
              defered.resolve(data);
            })
            .error(function (data) {
              defered.reject(data);
              notificationService.error(data.message)
            });
            return defered.promise;
          };
          this.getAllRate = function (page, data) {
            var defer = $q.defer();
            var promise = defer.promise;
            let url = constantPageRate.UrlPeticion.getall+"/"+page;
            $http.post(url,data).then(function (resolve) {
                defer.resolve(resolve);
            }).catch(function (reject) {
                defer.reject(reject);
            });
            return promise;
          };
          this.createRate = function (data){
            var defer = $q.defer();
            var promise = defer.promise;
            let url = constantPageRate.UrlPeticion.create;
            $http.post(url, data).then(function (resolve){
              defer.resolve(resolve);
            }).catch(function (reject){
              defer.reject(reject);
            });
            return promise;
          };
          this.oneRate = function (data) {
            var defer = $q.defer();
            var promise = defer.promise;
            let url = constantPageRate.UrlPeticion.getone+'/'+data;
            $http.get(url).then(function (resolve) {
                defer.resolve(resolve);
            }).catch(function (reject) {
                defer.reject(reject);
            });
            return promise;
          };
          this.editRatexrange = function (data) {
            var defer = $q.defer();
            var promise = defer.promise;
            let url = constantPageRate.UrlPeticion.edit+'/'+data.idRate;
            $http.put(url, data).then(function (resolve) {
                defer.resolve(resolve);
            }).catch(function (reject) {
                defer.reject(reject);
            });
            return promise;
          };
          this.deletedRate = function (id) {
            var defer = $q.defer();
            var promise = defer.promise;
            let url = constantPageRate.UrlPeticion.delete+'/'+id;
            $http.delete(url).then(function (resolve) {
                defer.resolve(resolve);
            }).catch(function (reject) {
                defer.reject(reject);
            });
            return promise;
          };
          this.getReportMail = function (idMail){
            var deferred = $q.defer();
            var url = fullUrlBase + 'api/report/reportmail';
            $http.post(url, idMail)
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
        .factory('notificationService', function () {
            function error(message) {
                slideOnTop(message, 4000, 'glyphicon glyphicon-remove-circle', 'danger');
            }

            function success(message) {
                slideOnTop(message, 10000, 'glyphicon glyphicon-ok-circle', 'success');
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