(function () {
  angular.module('autoresponder.services', [])
    .service('RestServices', function ($http, $q, notificationService) {

      this.getContactlist = function () {
        var deferred = $q.defer();
        var url = fullUrlBase + 'api/sendmail/getcontactlist';
        $http.get(url)
          .success(function (data) {
            deferred.resolve(data);
          })
          .error(function (data) {
            deferred.reject(data);
            notificationService.error(data.message);
          });

        return deferred.promise;
      };

      this.getSegment = function () {
        var deferred = $q.defer();
        var url = fullUrlBase + 'api/sendmail/getsegment';
        $http.get(url)
          .success(function (data) {
            deferred.resolve(data);
          })
          .error(function (data) {
            deferred.reject(data);
            notificationService.error(data.message);
          });

        return deferred.promise;
      };

      this.countContact = function (data) {
        var deferred = $q.defer();
        var url = fullUrlBase + 'api/sendmail/countcontact';
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
      
      this.countContactFromSms = function (data) {
        var deferred = $q.defer();
        var url = fullUrlBase + 'api/sms/countcontact';
        
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
           
      this.getemailname = function () {
        var deferred = $q.defer();
        $http.get(fullUrlBase + 'mail/emailname/')
          .success(function (data) {
            deferred.resolve(data);
          })
          .error(function (data) {
            deferred.reject(data);
            notificationService.error(data);
          });

        return deferred.promise;
      };

      this.getemailsend = function () {
        var deferred = $q.defer();
        $http.get(fullUrlBase + 'mail/emailsender/')
          .success(function (data) {
            deferred.resolve(data);
          })
          .error(function (data) {
            deferred.reject(data);
            notificationService.error(data);
          });

        return deferred.promise;
      };

      this.addEmailName = function (data) {
        var deferred = $q.defer();
        var url = fullUrlBase + 'mail/addemailname/';
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

      this.addEmailSender = function (data) {
        var deferred = $q.defer();
        var url = fullUrlBase + 'mail/addemailsender/';
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

      this.createAutoresponder = function (data, idAutoresponder) {
        var deferred = $q.defer();
        var url = fullUrlBase + 'api/autoresponder/save/' + idAutoresponder;
        $http.post(url, data)
          .success(function (res) {
            deferred.resolve(res);
          })
          .error(function (res){
            deferred.reject(res);
            notificationService.error(res.message);
          });
        return deferred.promise;
      };
      
      //SERVICIO PARA CREAR CAMPO PERSONALIZADO COMBINADO
      this.addcustomfield = function (data) {
          var deferred = $q.defer();
          var url = fullUrlBase + 'api/autoresponder/addcustomfield';
          $http.post(url, data)
            .success(function (res) {
              deferred.resolve(res);
            })
            .error(function (res) {
              deferred.reject(res);
              notificationService.error(res.message);
            });

          return deferred.promise;
      };
      
      this.createAutorespdesms = function (data, idAutoresponder) {
        var deferred = $q.defer();
        var url = fullUrlBase + 'api/autoresponder/saveautosms/' + idAutoresponder;
        $http.post(url, data)
          .success(function (res) {
            deferred.resolve(res);
          })
          .error(function (res) {
            deferred.reject(res);
            notificationService.error(res.message);
          });
        return deferred.promise;
      };
      
      this.getAllSmsCategories = function(){
        var deferred = $q.defer();
        //var url = constantForms.UrlPeticion.Urls.getAllFormCategory;
        var url = fullUrlBase + 'api/smscategory/getall';
        $http.get(url)
                .success(function (data) {
                  deferred.resolve(data);
                })
                .error(function (data) {
                  deferred.reject(data);
                  notificationService.error(data.message);
                });
        return deferred.promise;
      };
      
      this.createContentEditorAutoresponder = function (data, idAutoresponder) {
        var deferred = $q.defer();
        var url = fullUrlBase + 'api/autoresponder/savecontenteditor/' + idAutoresponder;
        $http.post(url, data)
          .success(function (res) {
            deferred.resolve(res);
          })
          .error(function (res) {
            deferred.reject(res);
            notificationService.error(res.message);
          });
        return deferred.promise;
      };

      this.getAutoresponder = function (idAutoresponder) {
        var deferred = $q.defer();
        var url = fullUrlBase + 'api/autoresponder/getautoresponse/' + idAutoresponder;
        $http.get(url)
          .success(function (res) {
            deferred.resolve(res);
          })
          .error(function (res) {
            deferred.reject(res);
            notificationService.error(res.message);
          });
        return deferred.promise;
      };

      this.getAllAutoresponder = function (page, data) {
        var deferred = $q.defer();
        var url = fullUrlBase + 'api/autoresponder/getallautoresponder/' + page;
        $http.post(url, data)
          .success(function (res) {
            deferred.resolve(res);
          })
          .error(function (res) {
            deferred.reject(res);
            notificationService.error(res.message);
          });
        return deferred.promise;
      };

      this.delete = function (idAutoresponder) {
        var url = fullUrlBase + "api/autoresponder/delete/" + idAutoresponder;
        var defered = $q.defer();
        $http.delete(url)
          .success(function (data) {
            defered.resolve(data);
          })
          .error(function (data) {
            defered.reject(data);
          });

        return defered.promise;
      };

      this.getMailFilters = function (filter) {
        var deferred = $q.defer();
        var url = fullUrlBase + 'api/sendmail/getmailfilters/' + filter;
        $http.get(url)
          .success(function (data) {
            deferred.resolve(data);
          })
          .error(function (data) {
            deferred.reject(data);
            notificationService.error(data.message);
          });

        return deferred.promise;
      };

      this.getLinksByMail = function (idMail) {
        var url = fullUrlBase + 'api/sendmail/getlinksbymail/' + idMail;
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
      };
      
      //Funcion que trae los datos al editar en el envio rapido
      this.getDataAll = function (id) {
        var defer = $q.defer();
          //$http.post(contantSmstwoway.urlPeticion.changeDataEditAll, id)
          var url = fullUrlBase + 'api/autoresponder/getalledit/';
          $http.post(url, id)
            .success(function (data) {
              defer.resolve(data);
            })
            .error(function (data) {
              defer.reject(data);
            });
          return defer.promise;
      };
      
      this.findcustomfields = function (idContactlist) {
        var deferred = $q.defer();
        var url = fullUrlBase + 'api/autoresponder/getallcustomfield/' + idContactlist;
        $http.get(url)
          .success(function (res) {
            deferred.resolve(res);
          })
          .error(function (res) {
            deferred.reject(res);
            notificationService.error(res.message);
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

      function primary(message) {
        slideOnTop(message, 4000, 'glyphicon glyphicon-exclamation-sign', 'primary');
      }

      return {
        error: error,
        success: success,
        warning: warning,
        notice: notice,
        primary: primary
      };
    })
})();