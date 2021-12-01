  angular.module('contactlist.services', [])
    .factory('restService', ['$http', '$q', 'constantContactList', 'notificationService', function ($http, $q, constantContactList, notificationService) {
        function getAll(page, data) {
          var deferred = $q.defer();
          var url = constantContactList.urlPeticion.getcontactlists + page;
          //$http.get(fullUrlBase + 'api/contactlist/getcontactlists/' + page + "/" + name)
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

        function getOne(id) {
          var deferred = $q.defer();

          //$http.get(fullUrlBase + 'api/contactlist/getcontactlist/' + id)
          var url = constantContactList.urlPeticion.getcontactlist + id;
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
        
        function getTotals() {
          var deferred = $q.defer();
          var url = constantContactList.urlPeticion.gettotalcontactlist;
          $http.post(url)
            .success(function (data) {
                console.log("AQUI1",data);
              deferred.resolve(data);
            })
            .error(function (data) {
                console.log("AQUI2",data.message);
              deferred.reject(data);
              notificationService.error(data.message);
            });

          return deferred.promise;
        }
        function getContactlistCategory() {
          var deferred = $q.defer();
          //$http.get(fullUrlBase + 'api/contactlist/getcontactlistcategory' )
          var url = constantContactList.urlPeticion.getcontactlistcategory
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
        function getOneCustomField(id) {
          var deferred = $q.defer();
          //$http.get(fullUrlBase + 'api/contactlist/getonecustomfield/' + id)
          var url = constantContactList.urlPeticion.getonecustomfield
          $http.get(url + id)
            .success(function (data) {
              deferred.resolve(data);
            })
            .error(function (data) {
              deferred.reject(data);
              notificationService.error(data.message);
            });

          return deferred.promise;
        }

        function validateTotalContacts(data) {
          var deferred = $q.defer();
          var url = fullUrlBase + "contact/validatetotalcontacts/";
  
          $http.post(url, data)
            .success(function (response) {
              deferred.resolve(response);
            })
            .error(function (error) {
              deferred.reject(error);
            });
  
          return deferred.promise;
        }
  
        function exportMoreContacts(data) {
          var deferred = $q.defer();
          var url = fullUrlBase + "api/contact/exportmorecontacts/";
  
          $http.post(url, data)
            .success(function (response) {
              deferred.resolve(response);
            })
            .error(function (error) {
              deferred.reject(error);
            });
  
          return deferred.promise;
        }

        function exportContacts(idContactlist) {
          var deferred = $q.defer();
          //$http.get(fullUrlBase + 'api/contactlist/exportcontacts/' + idContactlist)
          var url = constantContactList.urlPeticion.exportcontacts;
          $http.get(url + idContactlist)
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
          //var url = fullUrlBase + 'api/contactlist/add';
          var url = constantContactList.urlPeticion.addcontactlist;
          var deferred = $q.defer();

//          var data = {
//            name: name,
//            description: description
//          };

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
        function addcustomfield(data) {
          //var url = fullUrlBase + 'api/contactlist/addcustomfield';
          var url = constantContactList.urlPeticion.addcustomfield;
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
        function edit(id, name, description, idContactlistCategory) {
          //var url = fullUrlBase + 'api/contactlist/edit/' + id;
          var url = constantContactList.urlPeticion.editcontactlist + id;
          var deferred = $q.defer();

          var data = {
            name: name,
            description: description,
            idContactlistCategory: idContactlistCategory
          };

          $http.put(url, data)
            .success(function (data) {
              deferred.resolve(data);
            })
            .error(function (data) {
              deferred.reject(data);
              notificationService.error(data.message);
            });

          return deferred.promise;
        }
        function editCustomfield(data) {
          //var url = fullUrlBase + 'api/contactlist/editcustomfield/' + data.idCustomfield;
          var url = constantContactList.urlPeticion.editcustomfield + data.idCustomfield;
          var deferred = $q.defer();

          $http.put(url, data)
            .success(function (data) {
              deferred.resolve(data);
            })
            .error(function (data) {
              deferred.reject(data);
              notificationService.error(data.message);
            });

          return deferred.promise;
        }
        function deleteContactlist(id) {
          //var url = fullUrlBase + 'api/contactlist/delete/' + id;
          var url = constantContactList.urlPeticion.deleteContactlist + id;
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
        function deleteCustomfield(id) {
          //var url = fullUrlBase + 'api/contactlist/deletecustomfield/' + id;
          var url = constantContactList.urlPeticion.deletecustomfield + id;
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
        function listcustomfield(id, page) {
          //var url = fullUrlBase + 'api/contactlist/listcustomfield/' + id + "/" + page;
          var url = constantContactList.urlPeticion.listcustomfield + id + "/" + page;
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
        function getContactList() {
          //var url = fullUrlBase + 'api/contactlist/getcontactlistbysubaccount';
          var url = constantContactList.urlPeticion.getcontactlistbysubaccount;
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
        function saveCategory(data) {
          //var url = fullUrlBase + 'api/contactlist/savecategory';
          var url = constantContactList.urlPeticion.savecontactlistcategory;
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
        function permissionCustomfield(id) {          
          var url = constantContactList.urlPeticion.permissionCustomfield + id;
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
        return {
          save: save,
          validateTotalContacts: validateTotalContacts,
          exportMoreContacts: exportMoreContacts,
          exportContacts: exportContacts,
          getAll: getAll,
          getOne: getOne,
          getContactlistCategory: getContactlistCategory,
          edit: edit,
          deleteContactlist: deleteContactlist,
          listcustomfield: listcustomfield,
          addcustomfield: addcustomfield,
          getOneCustomField: getOneCustomField,
          editCustomfield: editCustomfield,
          deleteCustomfield: deleteCustomfield,
          getContactList: getContactList,
          saveCategory: saveCategory,
          permissionCustomfield: permissionCustomfield,
          getTotals: getTotals
        };

      }])
    .factory('notificationService', function (constantContactList) {
      var duration = constantContactList.messageNotification.duration;
      var styles = constantContactList.messageNotification.styles;
      function error(message) {
        slideOnTop(message, 2000, styles.danger.icon, styles.danger.color);
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

