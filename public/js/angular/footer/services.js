(function () {
  angular.module('footer.services', [])
      .factory('restService', ['$http', '$q', 'notificationService', function ($http, $q, notificationService) {

        function getFooterList(page) {
          var deferred = $q.defer();
          var url = fullUrlBase + 'api/footer/' + page;
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

        function getOneFooter(idFooter) {
          var deferred = $q.defer();
          var url = fullUrlBase + 'api/footer/findfooter/' + idFooter;
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

        function addFooter(data) {
          var deferred = $q.defer();
          var url = fullUrlBase + 'api/footer/create';
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

        function updateFooter(data) {
          var deferred = $q.defer();
          var url = fullUrlBase + 'api/footer/update';
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

        function deleteFooter(idFooter) {
          var deferred = $q.defer();
          var url = fullUrlBase + 'api/footer/delete/' + idFooter;
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
          getFooterList: getFooterList,
          addFooter: addFooter,
          getOneFooter: getOneFooter,
          updateFooter: updateFooter,
          deleteFooter: deleteFooter
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

        return {
          error: error,
          success: success,
          warning: warning,
          notice: notice,
          primary: primary
        };
      })

})();