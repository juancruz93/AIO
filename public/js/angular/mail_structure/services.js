(function () {
  angular.module('mail_structure.services', [])
    .factory('restService', ['$http', '$q', 'notificationService', function ($http, $q, notificationService) {

        function addPredeterminedstructure(data) {
          var deferred = $q.defer();
          var fd = new FormData();
          if (data.preview) {
            fd.append("preview", data.preview[0]);
          }
          fd.append("editor", data.editor);
          fd.append("name", data.name);
          fd.append("category", data.category);
//          angular.forEach(data.preview, function (file) {
//            fd.append('' + file.id, file[0]);
//          });
//delete data.preview;
//          fd.append("data", JSON.stringify(data));
//          console.log(fd);
          var url = fullUrlBase + 'api/mailstructure/create';
          $http.post(url, fd, {
            withCredentials: false,
            headers: {
              'Content-Type': undefined
            },
            transformRequest: angular.identity
          })
            .success(function (data) {
              deferred.resolve(data);
              notificationService.success(data.message);
            })
            .error(function (data) {
              deferred.reject(data);
              notificationService.error(data.message);
            });
//          fd.append("data", JSON.stringify(data));
//          $http.post(url, data, {
//            withCredentials: false,
//            headers: {
//              'Content-Type': undefined
//            },
//            transformRequest: angular.identity
//          })
//            .success(function (data) {
//              deferred.resolve(data);
//            })
//            .error(function (data) {
//              deferred.reject(data);
//              notificationService.show(data.message, 6000, 'glyphicon glyphicon-remove', 'danger');
//            });

          return deferred.promise;
        }


//        function addPredeterminedstructure(data) {
//
//
//
//          var deferred = $q.defer();
//          var url = fullUrlBase + 'api/mailstructure/create';
//          $http.post(url, data)
//            .success(function (data) {
//              deferred.resolve(data);
//            })
//            .error(function (data) {
//              deferred.reject(data);
//              notificationService.error(data);
//            });
//          return deferred.promise;
//        }

        function getAll(page, name) {
          var deferred = $q.defer();
          var url = fullUrlBase + 'api/mailstructure/getall/' + page;
          $http.post(url, name)
            .success(function (data) {
              deferred.resolve(data);
            })
            .error(function (data) {
              deferred.reject(data);
              notificationService.error(data);
            });
          return deferred.promise;
        }

        function deleteStructure(id) {
          var deferred = $q.defer();
          var url = fullUrlBase + 'api/mailstructure/deletestructure/' + id;
          $http.delete(url)
            .success(function (data) {
              deferred.resolve(data);
            })
            .error(function (data) {
              deferred.reject(data);
              notificationService.error(data);
            });
          return deferred.promise;
        }

        function editMailStructure(data) {
          var deferred = $q.defer();
          var fd = new FormData();
          if (data.preview) {
            fd.append("preview", data.preview[0]);
          }
          fd.append("editor", data.editor);
          fd.append("name", data.name);
          fd.append("category", data.category);
          fd.append("idMailstructure", idMailstructure);
//          console.log(data.name);
          var url = fullUrlBase + 'api/mailstructure/editmailstructure';
          $http.post(url, fd, {
            withCredentials: false,
            headers: {
              'Content-Type': undefined
            },
            transformRequest: angular.identity
          })
            .success(function (data) {
              deferred.resolve(data);
              notificationService.success(data.message);
            })
            .error(function (data) {
              deferred.reject(data);
              notificationService.error(data.message);
            });
          return deferred.promise;
        }

        return {
          addPredeterminedstructure: addPredeterminedstructure,
          getAll: getAll,
          deleteStructure: deleteStructure,
          editMailStructure: editMailStructure
        };
      }])
    .service("incomplete", function () {
      var varincomplete = false;

      this.setincomplete = function (incomple) {
        var incomplete = incomple;
      }

      this.getincomplete = function () {
        return varincomplete;
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
