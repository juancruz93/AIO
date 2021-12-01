(function () {
  angular.module('smstemplate.services', [])

          .service('RestServices', function ($http, $q, notificationService) {
            this.listSmsTemplateCategory = function () {
              var deferred = $q.defer();
              $http.get(fullUrlBase + "api/smstemplatecategory/listsmstempcategory")
                      .success(function (data) {
                        deferred.resolve(data);
                      })
                      .error(function (data) {
                        deferred.reject(data);
                        notificationService.error(data.message);
                      });

              return deferred.promise;
            };

            this.saveSmsTempCateg = function (data) {
              var url = fullUrlBase + "api/smstemplatecategory/savemailtempcategory";
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
            };

            this.listSmsTemplate = function (page, data) {
              var deferred = $q.defer();
              $http.post(fullUrlBase + "api/smstemplate/listsmstemp/" + page, data)
                      .success(function (data) {
                        deferred.resolve(data);
                      })
                      .error(function (data) {
                        deferred.reject(data);
                        notificationService.error(data.message);
                      });

              return deferred.promise;
            };

            this.saveSmsTemplate = function (data) {
              var url = fullUrlBase + "api/smstemplate/savesmstemp";
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
            };

            this.getSmsTemplate = function (id) {
              var deferred = $q.defer();
              $http.get(fullUrlBase + "api/smstemplate/getsmstemp/" + id)
                      .success(function (data) {
                        deferred.resolve(data);
                      })
                      .error(function (data) {
                        deferred.reject(data);
                        notificationService.error(data.message);
                      });

              return deferred.promise;
            };

            this.editSmsTemplate = function (data) {
              var deferred = $q.defer();
              $http.post(fullUrlBase + "api/smstemplate/editsmstemp", data)
                      .success(function (data) {
                        deferred.resolve(data);
                      })
                      .error(function (data) {
                        deferred.reject(data);
                        notificationService.error(data.message);
                      });

              return deferred.promise;
            };

            this.getAll = function (page) {
              var deferred = $q.defer();
              $http.post(fullUrlBase + 'api/smstemplate/listsmstemp/' + page)
                      .success(function (data) {
                        deferred.resolve(data);
                      })
                      .error(function (data) {
                        deferred.reject(data);
                        notificationService.error(data.message);
                      });

              return deferred.promise;
            }


            this.deleteSmstemplate = function (id) {
              var url = fullUrlBase + 'api/smstemplate/deletesmstemplate/' + id;
              var deferred = $q.defer();
              $http.delete(url)
                      .success(function (data) {
                        deferred.resolve(data);
                      })
                      .error(function (data) {
                        deferred.reject(data);
                        notificationService.error(data.message);
                      });

              return deferred.promise;
            }
            
            this.getTags = function () {
              var deferred = $q.defer();
              $http.get(fullUrlBase + 'api/smstemplate/gettags')
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
