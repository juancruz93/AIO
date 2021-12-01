(function () {
  angular.module('supportcontact.services', [])
    .factory('restService', ['$http', '$q', 'notificationService', function ($http, $q, notificationService) {

        function getalltechnical(id) {
          var deferred = $q.defer();
          var url = fullUrlBase + 'api/supportcontact/getalltechnical/' + id + "/" + idAllied;
          $http.get(url)
            .success(function (data) {
              deferred.resolve(data);
            })
            .error(function (data) {
              deferred.reject(data);
            });

          return deferred.promise;
        }

        function getfirsttechnical(id) {
          var deferred = $q.defer();
          var url = fullUrlBase + 'api/supportcontact/findfirsttechnical/' + idSupportContact;
          $http.get(url)
            .success(function (data) {
              deferred.resolve(data);
            })
            .error(function (data) {
              deferred.reject(data);
            });

          return deferred.promise;
        }

        function addtechnical(data) {
          var deferred = $q.defer();
          var url = fullUrlBase + 'api/supportcontact/addtechnical/' + idAllied;
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

        function edittechnical(data) {
          var deferred = $q.defer();
          var url = fullUrlBase + 'api/supportcontact/edittechnical/' + idAllied;
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

        return {
          getalltechnical: getalltechnical,
          addtechnical: addtechnical,
          getfirsttechnical: getfirsttechnical,
          edittechnical: edittechnical

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
    });
})();

