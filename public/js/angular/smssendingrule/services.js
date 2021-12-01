(function () {
  angular.module("smssendingrule.services", [])
    .service("restServices", ["$http", "$q", "notificationService", function ($http, $q, notificationService) {
      this.list = function (page, data) {
        var defered = $q.defer();
        var url = fullUrlBase + "api/smssendingrule/list/" + page + "/" + data;
        $http.get(url)
          .success(function (data) {
            defered.resolve(data);
          })
          .error(function (data) {
            defered.reject(data);
          });

        return defered.promise;
      };

      this.create = function (data) {
        var defered = $q.defer();
        var url = fullUrlBase + "api/smssendingrule/create";
        $http.post(url, data)
          .success(function (data) {
            defered.resolve(data);
          })
          .error(function (data) {
            defered.reject(data);
          });

        return defered.promise;
      };

      // this.listindicative = function () {
      //   var defered = $q.defer();
      //   var url = fullUrlBase + "api/smssendingrule/listindicative";
      //   $http.get(url)
      //     .success(function (data) {
      //       defered.resolve(data);
      //     })
      //     .error(function (data) {
      //       defered.reject(data);
      //     });

      //   return defered.promise;
      // };

      this.listindicative = function () {
        let defered = $q.defer();
        let url = fullUrlBase + "api/country/indicatives";

        $http.get(url)
          .success(function (response) {
            defered.resolve(response);
          })
          .error(function (error) {
            defered.reject(error);
          });

          return defered.promise;
      };

      this.adapter = function () {
        var defered = $q.defer();
        var url = fullUrlBase + "adapter/listfulladapter";
        $http.get(url)
          .success(function (data) {
            defered.resolve(data);
          })
          .error(function (data) {
            defered.reject(data);
            notificationService.error(data.message)
          });

        return defered.promise;
      };

      this.show = function (id) {
        var defered = $q.defer();
        var url = fullUrlBase + "api/smssendingrule/show/" + id;
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
        var defered = $q.defer();
        var url = fullUrlBase + "api/smssendingrule/edit";
        $http.put(url, data)
          .success(function (data) {
            defered.resolve(data);
          })
          .error(function (data) {
            defered.reject(data);
          });

        return defered.promise;
      };

      this.delete = function (id) {
        var defered = $q.defer();
        var url = fullUrlBase + "api/smssendingrule/delete/" + id;
        $http.delete(url)
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