(function () {
  angular.module("activitylog.services", [])
    .service('restServices', ['$http', '$q', 'notificationService', function ($http, $q, notificationService) {
        this.list = function (page, data) {
          var defered = $q.defer();
          var url = fullUrlBase + "api/activitylog/list/" + page;
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
        this.services = function () {
          var defered = $q.defer();
          var url = fullUrlBase + "services/listapi";
          $http.get(url)
            .success(function (data) {
              defered.resolve(data);
            })
            .error(function (data) {
              defered.reject(data);
              notificationService.error(data.message);
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

      return {
        error: error,
        success: success,
        warning: warning,
        notice: notice,
        primary: primary
      };
    });
})();