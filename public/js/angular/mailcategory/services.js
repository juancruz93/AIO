angular.module('mailcategory.services',[])
        .service('RestServices',function($http,$q,constantMailCategory,notificationService){
          this.getAll = function (page, filter) {
            var deferred = $q.defer();
            var url = constantMailCategory.urlPeticion.getmailcategory + page
            $http.post(url, filter)
                    .success(function (data) {
                      deferred.resolve(data);
                    })
                    .error(function (data) {
                      deferred.reject(data);
                      notificationService.error(data.message);
                    });
            return deferred.promise;
          },
          this.saveCategory = function (data) {
            var url = constantMailCategory.urlPeticion.savemailcategory ;
            var deferred = $q.defer();
            $http.post(url,data)
                    .success(function (data) {
                      deferred.resolve(data);
                    })
                    .error(function (data) {
                      deferred.reject(data);
                      notificationService.error(data.message);
                    });

            return deferred.promise;
          }
          this.getOneMailCategory = function (idMailCategory){
            var url = constantMailCategory.urlPeticion.getonemailcategory + idMailCategory;
            var deferred = $q.defer();
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
          this.editCategory = function (data) {
            var url = constantMailCategory.urlPeticion.editmailcategory;
            var deferred = $q.defer();
            $http.post(url,data)
                    .success(function (data) {
                      deferred.resolve(data);
                    })
                    .error(function (data) {
                      deferred.reject(data);
                      notificationService.error(data.message);
                    });

            return deferred.promise;
          }
          this.deleteCategory = function (data){
            var url = constantMailCategory.urlPeticion.deletemailcategory;
            var deferred = $q.defer();
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
        })
        .factory('notificationService', function (constantMailCategory){
          var duration = constantMailCategory.messageNotification.duration;
          var styles = constantMailCategory.messageNotification.styles;
          function error(message) {
            slideOnTop(message, duration, styles.error.icon, styles.error.color);
          }
          function success(message) {
            slideOnTop(message, duration, styles.success.icon, styles.success.color);
          }
          function warning(message) {
            slideOnTop(message, duration, styles.warning.icon, styles.warning.color);
          }
          function notice(message) {
            slideOnTop(message, duration, styles.notice.icon, styles.notice.color);
          }
          function info(message) {
            slideOnTop(message, duration, styles.info.icon, styles.info.color);
          }
          return {
            error: error,
            success: success,
            warning: warning,
            notice: notice,
            info: info
          };
        });
