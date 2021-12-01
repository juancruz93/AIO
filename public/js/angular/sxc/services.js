(function () {
  angular.module('sxc.services', [])
    .factory('restService', ['$http', '$q', 'notificationService', function ($http, $q, notificationService) {
        function getAll(page, idSegment, stringsearch) {
          var deferred = $q.defer();
          var url = fullUrlBase + 'api/sxc/findcontactsegment/' + page + "/" + idSegment;

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

        function changestatus(idContact, idContastlist) {
          var deferred = $q.defer();
          var url = fullUrlBase + 'api/sxc/changestatus/' + idContact + "/" + idContastlist;

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

        function customfield(idSegment) {
          var deferred = $q.defer();
          var url = fullUrlBase + 'api/sxc/customfield/' + idSegment;

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

        function getAllIndicative() {
          var deferred = $q.defer();
          $http.get(fullUrlBase + 'api/contact/getallindicative')
            .success(function (data) {
              deferred.resolve(data);
            })
            .error(function (data) {
              deferred.reject(data);
              notificationService.error(data.message);
            });

          return deferred.promise;
        }

        function editContact(idContact, key, value, idCustomfield) {
          var deferred = $q.defer();
          var data = {
            idContact: idContact,
            key: key,
            value: value,
            idCustomfield: idCustomfield
          };
          $http.post(fullUrlBase + 'api/contact/editcontact/1', data)
            .success(function (data) {
              deferred.resolve(data);
            })
            .error(function (data) {
              deferred.reject(data);
              notificationService.error(data.message);
            });

          return deferred.promise;
        }

        return {
          getAll: getAll,
          changestatus: changestatus,
          getAllIndicative: getAllIndicative,
          customfield: customfield,
          editContact: editContact
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
