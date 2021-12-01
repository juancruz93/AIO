(function () {
  angular.module('unsubscribe.service', [])
    .service('RestService',['$http','$q','notificationService', function ($http, $q, notificationService) {

      this.getContact = function (idContact,idMail) {
        var deferred = $q.defer();
        var url = fullUrlBase + 'api/unsubscribe/getcontact/' + idContact + '/' + idMail;
        $http.get(url)
          .success(function (data) {
            deferred.resolve(data);
          })
          .error(function (data) {
            deferred.reject(data);
            notificationService.primary(data.message);
          });
        return deferred.promise;
      }

      this.sendUnsubscribe = function (data, idContact) {
        var deferred = $q.defer();
        var url = fullUrlBase + 'api/unsubscribe/insunsubscribe/' + idContact;
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
      
      this.getAll = function (page, stringsearch) {
        var deferred = $q.defer();
        var url = fullUrlBase + 'api/unsubscribe/getcontactsunsubscribe/'+page;
        $http.post(url,{stringsearch: stringsearch})
       
          .success(function (data) {
            deferred.resolve(data);
          })
          .error(function (data) {
            deferred.reject(data);
            notificationService.error(data.message);
          });
        return deferred.promise;
      }
      
      this.deleteUnsub = function (idContact) {
        var deferred = $q.defer();
        var url = fullUrlBase + 'api/unsubscribe/deleteunsub/' + idContact;
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
      this.sendUnsubscribeAutomatic = function (data, idContact) {
        let deferred = $q.defer();
        let url = fullUrlBase + "api/unsubscribe/insunsubscribeautomatic/" + idContact;

        $http.post(url, data)
          .success(function (response) {
            deferred.resolve(response);
          })
          .error(function (error) {
            deferred.reject(error);
          });
        return deferred.promise;
      }
      
      this.sendUnsubscribeSimple = function(data){
        let deferred = $q.defer();
        let url = fullUrlBase + "api/unsubscribe/insunsubscribesimple/" + data.idMail+"/"+data.idContact;

        $http.get(url)
          .success(function (response) {
            deferred.resolve(response);
          })
          .error(function (error) {
            deferred.reject(error);
          });
        return deferred.promise;
      }
      
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
      }
      
      this.listCategories = function () {
        let defered = $q.defer();
        let url = fullUrlBase + "api/unsubscribe/getcategories";
        $http.get(url)
          .success(function (response) {
            defered.resolve(response);
          })
          .error(function (error) {
            defered.reject(error);
          });

        return defered.promise;
      }
      
      this.createUnsub = function (data) {
        var deferred = $q.defer();
        var url = fullUrlBase + 'api/unsubscribe/createcontactunsub';
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
