(function () {
  angular.module("paymentplan.services", [])
          .service("RestServices", function ($http, $q, notificationService) {
    this.listPaymentPlan = function (page, data) {
      var defered = $q.defer();
      var url = fullUrlBase + "api/paymentplan/list/" + page + "/" + data;
      $http.get(url).success(function (data) {
        defered.resolve(data)
      }).error(function (data) {
        defered.reject(data);
        notificationService.error(data.message)
      });
      return defered.promise
    };
    this.listCountry = function () {
      var defered = $q.defer();
      var url = fullUrlBase + "country/country";
      $http.get(url).success(function (data) {
        defered.resolve(data)
      }).error(function (data) {
        defered.reject(data);
        notificationService.error(data)
      });
      return defered.promise
    };
    this.createPaymentPlan = function (data) {
      var defered = $q.defer();
      var url = fullUrlBase + "api/paymentplan/create";
      $http.post(url, data).success(function (data) {
        defered.resolve(data)
      }).error(function (data) {
        defered.reject(data);
        notificationService.error(data.message)
      });
      return defered.promise
    };
    this.getPaymentPlan = function (id) {
      var defered = $q.defer();
      var url = fullUrlBase + "api/paymentplan/get/" + id;
      $http.get(url).success(function (data) {
        defered.resolve(data)
      }).error(function (data) {
        defered.reject(data);
        notificationService.error(data.message)
      });
      return defered.promise
    };
    this.editPaymentPlan = function (data) {
      var defered = $q.defer();
      var url = fullUrlBase + "api/paymentplan/edit";
      $http.put(url, data).success(function (data) {
        defered.resolve(data)
      }).error(function (data) {
        defered.reject(data);
        notificationService.error(data.message)
      });
      return defered.promise
    };
    this.deletePaymentPlan = function (id) {
      var defered = $q.defer();
      var url = fullUrlBase + "api/paymentplan/delete/" + id;
      $http.delete(url).success(function (data) {
        defered.resolve(data)
      }).error(function (data) {
        defered.reject(data);
        notificationService.error(data.message)
      });
      return defered.promise
    };
    this.listTax = function (idCountry) {
      var defered = $q.defer();
      var url = fullUrlBase + "api/tax/listfull/" + idCountry;
      $http.get(url).success(function (data) {
        defered.resolve(data)
      }).error(function (data) {
        defered.reject(data);
        notificationService.error(data.message)
      });
      return defered.promise
    };
    this.services = function () {
      var defered = $q.defer();
      var url = fullUrlBase + "api/paymentplan/listservices";
      $http.get(url).success(function (data) {
        defered.resolve(data)
      }).error(function (data) {
        defered.reject(data);
        notificationService.error(data.message)
      });
      return defered.promise
    };
    this.plantypes = function () {
      var defered = $q.defer();
      var url = fullUrlBase + "plantype/listplanttype";
      $http.get(url).success(function (data) {
        defered.resolve(data)
      }).error(function (data) {
        defered.reject(data);
        notificationService.error(data.message)
      });
      return defered.promise
    };
    this.pricelist = function (idServices, data) {
      var defered = $q.defer();
      var url = fullUrlBase + "api/pricelist/listfull/" + idServices + "/" + data;
      $http.get(url).success(function (data) {
        defered.resolve(data)
      }).error(function (data) {
        defered.reject(data);
        notificationService.error(data.message)
      });
      return defered.promise
    };
    this.adapter = function () {
      var defered = $q.defer();
      var url = fullUrlBase + "adapter/listfulladapter";
      $http.get(url).success(function (data) {
        defered.resolve(data)
      }).error(function (data) {
        defered.reject(data);
        notificationService.error(data.message)
      });
      return defered.promise
    };
    this.mta = function () {
      var defered = $q.defer();
      var url = fullUrlBase + "mta/listfullmta";
      $http.get(url).success(function (data) {
        defered.resolve(data)
      }).error(function (data) {
        defered.reject(data);
        notificationService.error(data.message)
      });
      return defered.promise
    };
    this.urldomain = function () {
      var defered = $q.defer();
      var url = fullUrlBase + "urldomain/listfullurldomain";
      $http.get(url).success(function (data) {
        defered.resolve(data)
      }).error(function (data) {
        defered.reject(data);
        notificationService.error(data.message)
      });
      return defered.promise
    };
    this.mailclass = function () {
      var defered = $q.defer();
      var url = fullUrlBase + "mailclass/listfullmailclass";
      $http.get(url).success(function (data) {
        defered.resolve(data)
      }).error(function (data) {
        defered.reject(data);
        notificationService.error(data.message)
      });
      return defered.promise
    };
    this.getconfigServices = function (idPaymentPlan) {
      var defered = $q.defer();
      var url = fullUrlBase + "api/paymentplan/show/" + idPaymentPlan;
      $http.get(url).success(function (data) {
        defered.resolve(data)
      }).error(function (data) {
        defered.reject(data);
        notificationService.error(data.message)
      });
      return defered.promise
    };
    this.ValidateCourtesyPlan = function (courtesy, country) {
      var data = {};
      data.courtesy = courtesy;
      data.country = country;
      var defered = $q.defer();
      var url = fullUrlBase + "api/paymentplan/validatecourtesyplan";
      $http.post(url, data).success(function (data) {
        defered.resolve(data)
      }).error(function (data) {
        defered.reject(data)
      })
      return defered.promise
    }
  })
          .factory('notificationService', function () {
    function error(message) {
      slideOnTop(message, 4000, 'glyphicon glyphicon-remove-circle', 'danger')
    }

    function success(message) {
      slideOnTop(message, 4000, 'glyphicon glyphicon-ok-circle', 'success')
    }

    function warning(message) {
      slideOnTop(message, 4000, 'glyphicon glyphicon-exclamation-sign', 'warning')
    }

    function notice(message) {
      slideOnTop(message, 4000, 'glyphicon glyphicon-exclamation-sign', 'notice')
    }

    function info(message) {
      slideOnTop(message, 4000, 'glyphicon glyphicon-exclamation-sign', 'info')
    }
    return {
      error: error,
      success: success,
      warning: warning,
      notice: notice,
      info: info
    }
  })
})()