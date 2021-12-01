(function () {
  angular.module('segment.services', [])
    .factory('restService', ['$http', '$q', 'notificationService', function ($http, $q, notificationService) {

        function getAllCustomField(contaclist) {

          var deferred = $q.defer();
          var url = fullUrlBase + 'api/segment/customfieldbycustomfields';

          $http.post(url, contaclist)
            .success(function (data) {
              deferred.resolve(data);
            })
            .error(function (data) {
              deferred.reject(data);
              notificationService.error(data.message);
            });

          return deferred.promise;
        }

        function addSegment(segment, filters) {
          var data = {
            filters: filters,
            information: segment
          };
          var deferred = $q.defer();
          var url = fullUrlBase + 'api/segment/addsegment';

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

        function getAllContactlistBySubaccount() {
          var deferred = $q.defer();
          var url = fullUrlBase + 'api/segment/getallcontactlistbysubaccount';

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


        function getAllSegment(page, stringsearch) {

          var deferred = $q.defer();
          var url = fullUrlBase + 'api/segment/getallsegment/' + page;

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

        function findSegment(id) {
          var deferred = $q.defer();
          var url = fullUrlBase + 'api/segment/findsegment/' + id;
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

        function editSegment(segment) {

          var deferred = $q.defer();
          var url = fullUrlBase + 'api/segment/editsegment';

          $http.put(url, segment)
            .success(function (data) {
              deferred.resolve(data);
              notificationService.primary(data.message);
            })
            .error(function (data) {
              deferred.reject(data);
              notificationService.error(data.message);
            });

          return deferred.promise;
        }

        function findCustomfield(id) {
          var deferred = $q.defer();
          var url = fullUrlBase + 'api/contactlist/getonecustomfield/' + id;
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

        function deleteSegment(idSegment) {
          var deferred = $q.defer();
          $http.delete(fullUrlBase + 'api/segment/deletesegmen/' + idSegment)
            .success(function (data) {
              deferred.resolve(data);
            })
            .error(function (data) {
              deferred.reject(data);
            });

          return deferred.promise;
        }
        return {
          getAllCustomField: getAllCustomField,
          addSegment: addSegment,
          getAllContactlistBySubaccount: getAllContactlistBySubaccount,
          getAllSegment: getAllSegment,
          findSegment: findSegment,
          editSegment: editSegment,
          findCustomfield: findCustomfield,
          deleteSegment: deleteSegment
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
