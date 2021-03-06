'use strict';
(function () {
  angular.module('blockade.services', [])
    .factory('restService', ['$http', '$q', 'notificationService', function ($http, $q, notificationService) {

      function getAll(page, stringsearch) {
        var deferred = $q.defer();
        var url = fullUrlBase + 'api/blockade/getallblock/' + page;
        $http.post(url, stringsearch)
          .success(function (data) {
            deferred.resolve(data);
          })
          .error(function (data) {
            deferred.reject(data);
            notificationService.error(data.message);
          });
        return deferred.promise;
      }

      function addBlockade(block) {
        var deferred = $q.defer();
        var url = fullUrlBase + 'api/blockade/addblockade';
        $http.post(url, block)
          .success(function (data) {
            deferred.resolve(data);
          })
          .error(function (data) {
            deferred.reject(data);
            notificationService.error(data.message);
          });
        return deferred.promise;
      }

      function deleteBlockade(idBlock) {
        var deferred = $q.defer();
        var url = fullUrlBase + 'api/blockade/deleteblockade/' + idBlock;
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

      function listindicative () {
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

      return {
        getAll: getAll,
        addBlockade: addBlockade,
        deleteBlockade: deleteBlockade,
        listindicative: listindicative,
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
    });
})();
