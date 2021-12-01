(function () {
    angular.module("LandingPageTemplate.services", [])
    .service("RestServices", function ($http, $q, notificationService, constantLPTemplate){
        this.getAll = function (data,page) {
            var deferred = $q.defer();
            var url = fullUrlBase + constantLPTemplate.UrlRequest.Urls.listlptemplate + page;

            $http.post(url, data)
                .success(function (response) {
                    deferred.resolve(response);
                })
                .error(function (error) {
                    deferred.reject(error);
                });

            return deferred.promise;
        };

        this.getAllCategories = function () {
          var deferred = $q.defer();
          var url = fullUrlBase + constantLPTemplate.UrlRequest.Urls.listlptcategory;

          $http.get(url)
            .success(function (response) {
              deferred.resolve(response);
            })
            .error(function (error) {
              deferred.reject(response);
            });

            return deferred.promise;
        } ;       
    })
    .factory('notificationService', function (constantLPTemplate) {
        function error(message) {
          slideOnTop(message, 4000, constantLPTemplate.NotificationsService.Errors.error, constantLPTemplate.Misc.Alerts.danger);
        }

        function success(message) {
          slideOnTop(message, 4000, constantLPTemplate.NotificationsService.Errors.success, constantLPTemplate.Misc.Alerts.success);
        }

        function warning(message) {
          slideOnTop(message, 4000, constantLPTemplate.NotificationsService.Errors.warning, constantLPTemplate.Misc.Alerts.warning);
        }

        function notice(message) {
          slideOnTop(message, 4000, constantLPTemplate.NotificationsService.Errors.notice, constantLPTemplate.Misc.Alerts.notice);
        }

        function primary(message) {
          slideOnTop(message, 4000, constantLPTemplate.NotificationsService.Errors.primary, constantLPTemplate.Misc.Alerts.primary);
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