(function () {
  angular.module('mailtemplate.services', [])
          .service('RestServices', function ($http, $q, notificationService) {
            this.getmailtempcateg = function () {
              var deferred = $q.defer();
              $http.get(fullUrlBase + 'api/mailcategorytemplatecategory/getmailtempcategory')
                      .success(function (data) {
                        deferred.resolve(data);
                      })
                      .error(function (data) {
                        deferred.reject(data);
                        notificationService.error(data.message);
                      });

              return deferred.promise;
            };

            this.getmailtempcategfilt = function () {
              var deferred = $q.defer();
              $http.get(fullUrlBase + "api/mailcategorytemplatecategory/getmailtempcategfilt")
                      .success(function (data) {
                        deferred.resolve(data);
                      })
                      .error(function (data) {
                        deferred.reject(data);
                        notificationService.error(data.message);
                      });

              return deferred.promise;
            };

            this.saveMailTempCateg = function (data) {
              var url = fullUrlBase + "api/mailcategorytemplatecategory/savemailtempcategory";
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

            this.saveMailTemp = function (data, idMailTemplate, type) {
              var url = fullUrlBase + "api/mailtemplate/savemailtemp";
              if (idMailTemplate != null) {
                url = fullUrlBase + "api/mailtemplate/editmailtemp/" + idMailTemplate;
              }
              if (type == "new") {
                url = fullUrlBase + "api/mailtemplate/saveastemplatemailnew";
              }

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

            this.listMailTemp = function (page, data) {
              var deferred = $q.defer();
              $http.post(fullUrlBase + 'api/mailtemplate/listmailtemp/' + page, data)
                      .success(function (data) {
                        deferred.resolve(data);
                      })
                      .error(function (data) {
                        deferred.reject(data);
                        notificationService.error(data.message);
                      });

              return deferred.promise;
            };

            this.previewMailTemplateContent = function (id) {
              var deferred = $q.defer();
              var url = 'api/mailtemplate/preview/' + id;
              $http.post(url)
                      .success(function (data) {
                        deferred.resolve(data);
                      })
                      .error(function (data) {
                        deferred.reject(data);
                      });

              return deferred.promise;
            };

            this.editMailTemplate = function (idMailTemplate) {
              var deferred = $q.defer();
              var url = fullUrlBase + 'api/mailtemplate/getmailtemp/' + idMailTemplate;
              $http.get(url)
                      .success(function (data) {
                        deferred.resolve(data);
                      })
                      .error(function (data) {
                        deferred.reject(data);
                      });

              return deferred.promise;
            };

            this.deleteMailTemplate = function (idMailTemplate) {
              var deferred = $q.defer();
              var url = fullUrlBase + 'api/mailtemplate/deletemailtemp';
              $http.post(url, {idMailTemplate: idMailTemplate})
                      .success(function (data) {
                        deferred.resolve(data);
                      })
                      .error(function (data) {
                        deferred.reject(data);
                      });

              return deferred.promise;
            };

            this.getMail = function (idMail) {
              var deferred = $q.defer();
              var url = fullUrlBase + 'api/sendmail/getmail/' + idMail;
              $http.get(url)
                      .success(function (data) {
                        deferred.resolve(data);
                      })
                      .error(function (data) {
                        deferred.reject(data);
                      });

              return deferred.promise;
            };

            this.getAccounts = function () {
              var defered = $q.defer();
              var url = fullUrlBase + "api/mailtemplate/accounts";

              $http.get(url)
                      .success(function (data) {
                        defered.resolve(data);
                      })
                      .error(function (data) {
                        defered.reject(data);
                      });

              return defered.promise;
            };

            this.getAutoresponder = function (idAutoresponder) {
              var deferred = $q.defer();
              var url = fullUrlBase + 'api/autoresponder/getautoresponder/' + idAutoresponder;
              $http.get(url)
                      .success(function (data) {
                        deferred.resolve(data);
                      })
                      .error(function (data) {
                        deferred.reject(data);
                      });

              return deferred.promise;
            };

            this.getTempleteMail = function () {
              var deferred = $q.defer();
              var url = fullUrlBase + 'api/mailtemplate/gettemplatemail';
              $http.get(url)
                      .success(function (data) {
                        deferred.resolve(data);
                      })
                      .error(function (data) {
                        deferred.reject(data);
                      });
              return deferred.promise;
            };

            this.getAllTemplateMail = function (data) {
              var deferred = $q.defer();
              var url = fullUrlBase + 'api/mailtemplate/getalltemplatemail';
              $http.post(url,data)
                      .success(function (data) {
                        deferred.resolve(data);
                      })
                      .error(function (data) {
                        deferred.reject(data);
                      });
              return deferred.promise;
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
  ;
})();
