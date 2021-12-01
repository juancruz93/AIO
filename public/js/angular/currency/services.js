(function () {
  angular.module("currency.services", [])
    .service("RestServices", function ($http, $q, notificationService) {
      this.listCurrency = function (page, data) {
        var defered = $q.defer();
        var url = fullUrlBase + "api/currency/listcurrency/" + page + "/" + data;
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

      this.saveCurrency = function (data) {
        var defered = $q.defer();
        var url = fullUrlBase + "api/currency/create";
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

      this.getCurrency = function (id) {
        var defered = $q.defer();
        var url = fullUrlBase + "api/currency/getone/" + id;
        $http.get(url)
          .success(function (data) {
            defered.resolve(data);
          })
          .error(function (data) {
            defered.reject(data);
          });

        return defered.promise;
      };

      this.updateCurrency = function (data) {
        var defered = $q.defer();
        var url = fullUrlBase + "api/currency/edit/" + data.idCurrency;
        $http.put(url, data)
          .success(function (data) {
            defered.resolve(data);
          })
          .error(function (data) {
            defered.reject(data);
            notificationService.error(data.message);
          });

        return defered.promise;
      };

      this.deleteCurrency = function (id) {
        var defered = $q.defer();
        var url = fullUrlBase + "api/currency/delete/" + id;
        $http.delete(url)
          .success(function (data) {
            defered.resolve(data);
          })
          .error(function (data) {
            defered.reject(data.message);
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