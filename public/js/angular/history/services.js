(function () {
  angular.module('history.services', [])
          .factory('restService', ['$http', '$q', 'notificationService', function ($http, $q, notificationService) {
              function getAll(page, data) {
                var deferred = $q.defer();
                var url = fullUrlBase + 'api/history/gethistory/' + page;
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
              function getMasteraccounts() {
                var deferred = $q.defer();
                var url = fullUrlBase + 'api/history/getmasteraccounts';
                   $http.get(url)
                        .success(function (data) {
                          deferred.resolve(data);
                        })
                        .error(function (data) {
                          deferred.reject(data);
                          notificationService.error(data.message);
                        });

                return deferred.promise;
              }
              function getAllieds(idMasteraccount) {
                var deferred = $q.defer();
                var url = fullUrlBase + 'api/history/getallieds/'+idMasteraccount;
                   $http.get(url)
                        .success(function (data) {
                          deferred.resolve(data);
                        })
                        .error(function (data) {
                          deferred.reject(data);
                          notificationService.error(data.message);
                        });

                return deferred.promise;
              }
              function getAccounts(idAllied) {
                var deferred = $q.defer();
                var url = fullUrlBase + 'api/history/getaccounts/'+idAllied;
                   $http.get(url)
                        .success(function (data) {
                          deferred.resolve(data);
                        })
                        .error(function (data) {
                          deferred.reject(data);
                          notificationService.error(data.message);
                        });

                return deferred.promise;
              }
              function getSubaccounts(idAccount) {
                var deferred = $q.defer();
                var url = fullUrlBase + 'api/history/getsubaccounts/'+idAccount;
                   $http.get(url)
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
                getMasteraccounts: getMasteraccounts,
                getAllieds: getAllieds,
                getAccounts: getAccounts,
                getSubaccounts: getSubaccounts
            
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
