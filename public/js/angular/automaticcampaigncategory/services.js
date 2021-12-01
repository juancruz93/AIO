(function () {
  angular.module('automaticcampaigncategory.services', [])
    .service('RestServices', function ($http, $q, notificationService) {
      this.save = function (data) {
        var url = fullUrlBase + "api/automacampcateg/save";
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

      this.list = function (page, data) {
        var url = fullUrlBase + "api/automacampcateg/list/" + page;
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

      this.get = function (id) {
        var url = fullUrlBase + "api/automacampcateg/get/" + id;
        var defered = $q.defer();
        $http.get(url)
          .success(function (data) {
            defered.resolve(data);
          })
          .error(function (data) {
            defered.reject(data);
          });

        return defered.promise;
      };

      this.edit = function (data) {
        var url = fullUrlBase + "api/automacampcateg/edit";
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
        var url = fullUrlBase + "api/automacampcateg/delete";
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