(function () {
  angular.module("register.services", [])
    .service("restServices", ["$http", "$q", "notificationService", function ($http, $q, notificationService) {

      this.countries = function () {
        var defer = $q.defer();
        var url = fullUrlBase + "country/country";

        $http.get(url)
          .success(function (data) {
            defer.resolve(data);
          })
          .error(function (data) {
            defer.reject(data);
          });

        return defer.promise;
      };

      this.states = function (idCountry) {
        var defer = $q.defer();
        var url = fullUrlBase + "country/state/" + idCountry;

        $http.get(url)
          .success(function (data) {
            defer.resolve(data);
          })
          .error(function (data) {
            defer.reject(data);
          });

        return defer.promise;
      };

      this.cities = function (idState) {
        var defer = $q.defer();
        var url = fullUrlBase + "country/cities/" + idState;

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
        var defer = $q.defer();
        var url = fullUrlBase + "api/register/create";

        $http.post(url, data)
          .success(function (data) {
            defer.resolve(data);
          })
          .error(function (data) {
            defer.reject(data);
          });

        return defer.promise;
      };

      this.paymentsplans = function () {
        var defer = $q.defer();
        var url = fullUrlBase + "api/register/listpay";

        $http.get(url)
          .success(function (data) {
            defer.resolve(data);
          })
          .error(function (data) {
            defer.reject(data);
          });

        return defer.promise;
      };

      this.paymentpladetail = function (idPaymentPlan) {
        var defer = $q.defer();
        var url = fullUrlBase + "api/register/detailpay/" + idPaymentPlan;

        $http.get(url)
          .success(function (data) {
            defer.resolve(data);
          })
          .error(function (data) {
            defer.reject(data);
          });

        return defer.promise;
      };

      this.verifyAccount = function (id) {
        var defer = $q.defer();
        var url = fullUrlBase + "api/register/verify/" + id;

        $http.get(url)
          .success(function (data) {
            defer.resolve(data);
          })
          .error(function (data) {
            defer.reject(data);
          });

        return defer.promise;
      };

      this.assignPaymentPlan = function (data) {
        var defer = $q.defer();
        var url = fullUrlBase + "api/register/assign";

        $http.post(url, data)
          .success(function (data) {
            defer.resolve(data);
          })
          .error(function (data) {
            defer.reject(data);
          });

        return defer.promise;
      };

      this.getAppIdFB = function () {
        let defer = $q.defer();
        let url = fullUrlBase + "api/register/appidfb";

        $http.get(url)
          .success(function (data) {
            defer.resolve(data);
          })
          .error(function (data) {
            defer.reject(data);
          });

        return defer.promise;
      };

      this.createAccountFB = function (data,termsConditions) {
        let defer = $q.defer();
        let url = fullUrlBase + "api/register/accountfb";
        data["termsConditions"] =termsConditions;
        $http.post(url, data)
          .success(function (data) {
            defer.resolve(data);
          })
          .error(function (data) {
            defer.reject(data);
          });

        return defer.promise;
      }

      this.verifyStatusUser = function () {
        let defer = $q.defer();
        let url = fullUrlBase + "api/session/verifyStatus";

        $http.get(url)
          .success(function (response) {
            defer.resolve(response);
          })
          .error(function (error) {
            defer.reject(error);
          });

        return defer.promise;
      }

      this.completeProfileUser = function (data) {
        let defer = $q.defer();
        let url = fullUrlBase + "api/register/completeprofile";

        $http.post(url, data)
          .success(function (response) {
            defer.resolve(response);
          })
          .error(function (error) {
            defer.reject(error);
          });

        return defer.promise;
      }

    }])
    .factory('notificationService', function () {
      function error(message) {
        slideOnTop(message, 5000, 'glyphicon glyphicon-remove-circle', 'danger');
      }

      function success(message) {
        slideOnTop(message, 5000, 'glyphicon glyphicon-ok-circle', 'success');
      }

      function warning(message) {
        slideOnTop(message, 5000, 'glyphicon glyphicon-exclamation-sign', 'warning');
      }

      function notice(message) {
        slideOnTop(message, 5000, 'glyphicon glyphicon-exclamation-sign', 'notice');
      }

      function primary(message) {
        slideOnTop(message, 5000, 'glyphicon glyphicon-exclamation-sign', 'primary');
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
