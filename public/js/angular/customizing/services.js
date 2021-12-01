(function () {
  angular.module('customizing.services', [])
          .factory('restService', ['$http', '$q', 'notificationService', function ($http, $q, notificationService) {
              function getAll() {
                var deferred = $q.defer();
                $http.get(fullUrlBase + 'api/customizing/getcustomizing/')
                        .success(function (data) {
                          deferred.resolve(data);
                        })
                        .error(function (data) {
                          deferred.reject(data);
                          notificationService.error(data.message);
                        });

                return deferred.promise;
              }

              function saveItemBlockInfo(data) {
                var deferred = $q.defer();
                $http.post(fullUrlBase + 'api/customizing/setItemBlockInfo', data)
                        .success(function (data) {
                          deferred.resolve(data);
                        })
                        .error(function (data) {
                          deferred.reject(data);
                          notificationService.error(data.message);
                        });

                return deferred.promise;
              }

              function save(data) {
                var url = fullUrlBase + 'api/customizing/add';
                var deferred = $q.defer();
                var fd = new FormData();
                fd.append(data.file.id, data.file[0]);
                delete data.file;
                fd.append("data", JSON.stringify(data));

                $http.post(url, fd, {
                  withCredentials: false,
                  headers: {
                    'Content-Type': undefined
                  },
                  transformRequest: angular.identity
                })
                        .success(function (data) {
                          deferred.resolve(data);
                        })
                        .error(function (data) {
                          deferred.reject(data);
                          notificationService.error(data.message);
                        });

                return deferred.promise;
              }
              function deleteTheme(id) {
                var url = fullUrlBase + 'api/customizing/delete/' + id;
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
              function selectTheme(id) {
                var url = fullUrlBase + 'api/customizing/select/' + id;
                var deferred = $q.defer();
                $http.put(url)
                        .success(function (data) {
                          deferred.resolve(data);
                        })
                        .error(function (data) {
                          deferred.reject(data);
                          notificationService.error(data.message);
                        });

                return deferred.promise;
              }
              function edit(data) {
                var data = {
                  id: data.id,
                  name: data.name,
                  description: data.description,
                  title: data.title,
                  headerColor: data.headerColor,
                  mainColor: data.mainColor,
                  linkColor: data.linkColor,
                  linkHoverColor: data.linkHoverColor,
                  footerColor: data.footerColor,
                  headerTextColor: data.headerTextColor,
                  mainTitle: data.mainTitle,
                  footerIconColor: data.footerIconColor,
                  userBoxColor: data.userBoxColor,
                  userBoxHoverColor: data.userBoxHoverColor,
                  socialsordered: data.socials,
                  infosordered: data.infos,
                  socials: data.socialsordered,
                  infos: data.infosordered,
                  infoBlockId: data.infoBlockId,
                  socialBlockId: data.socialBlockId,
                  socialBlockPosition: data.socialBlockPosition,
                  infoBlockPosition: data.infoBlockPosition,
                  socialsDeleted: data.socialsDeleted,
                  infosDeleted: data.infosDeleted,
                  file: data.file,
                };
                var url = fullUrlBase + 'api/customizing/edit/' + data.id;
                var deferred = $q.defer();
                var fd = new FormData();
                fd.append(data.file.id, data.file[0]);
                delete data.file;
                fd.append("data", JSON.stringify(data));

                $http.post(url, fd, {
                  withCredentials: false,
                  headers: {
                    'Content-Type': undefined
                  },
                  transformRequest: angular.identity
                })
                        .success(function (data) {
                          deferred.resolve(data);
                        })
                        .error(function (data) {
                          notificationService.error(data.message);
                          deferred.reject(data);
                        });

                return deferred.promise;
              }
              function getOne(id) {
                var deferred = $q.defer();

                $http.get(fullUrlBase + 'api/customizing/getonecustomizing/' + id)
                        .success(function (data) {
                          deferred.resolve(data);
                        })
                        .error(function (data) {
                          deferred.reject(data);
                          notificationService.error(data.message);
                        });

                return deferred.promise;
              }
              function getSocialNetworks() {
                var deferred = $q.defer();

                $http.get(fullUrlBase + 'api/customizing/getsocialnetworks')
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
                save: save,
                deleteTheme: deleteTheme,
                selectTheme: selectTheme,
                getOne: getOne,
                edit: edit,
                getSocialNetworks: getSocialNetworks,
                saveItemBlockInfo: saveItemBlockInfo
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
})();
