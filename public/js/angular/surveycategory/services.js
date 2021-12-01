(function () {
  angular.module("surveycategory.services", [])
    .service("restServices", ["$http", "$q", "notificationService", function ($http, $q, notificationService) {
        this.list = function (page, data) {
          var url = fullUrlBase + "api/surveycategory/list/" + page + "/" + data;
          var defer = $q.defer();

          $http.get(url)
            .success(function (data) {
              defer.resolve(data);
            })
            .error(function (data) {
              defer.reject(data);
            });

          return defer.promise;
        };

        this.create = function (data) {
          var url = fullUrlBase + "api/surveycategory/create";
          var defer = $q.defer();

          $http.post(url, data)
            .success(function (data) {
              defer.resolve(data);
            })
            .error(function (data) {
              defer.reject(data);
            });

          return defer.promise;
        };

        this.getOne = function (id) {
          var url = fullUrlBase + "api/surveycategory/getone/" + id;
          var defer = $q.defer();

          $http.get(url)
            .success(function (data) {
              defer.resolve(data);
            })
            .error(function (data) {
              defer.reject(data);
            });

          return defer.promise;
        };

        this.edit = function (data) {
          var url = fullUrlBase + "api/surveycategory/edit";
          var defer = $q.defer();

          $http.put(url, data)
            .success(function (data) {
              defer.resolve(data);
            })
            .error(function (data) {
              defer.reject(data);
            });

          return defer.promise;
        };

        this.delete = function (id) {
          var url = fullUrlBase + "api/surveycategory/delete/" + id;
          var defer = $q.defer();

          $http.delete(url)
            .success(function (data) {
              defer.resolve(data);
            })
            .error(function (data) {
              defer.reject(data);
            });

          return defer.promise;
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
  ;
})();