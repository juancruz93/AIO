(function () {
  angular.module('contact.services', [])
    .factory('restService', ['$http', '$q', 'notificationService', function ($http, $q, notificationService) {

      function getAll(page, idContactlist, stringsearch, stateend) {
        
        var deferred = $q.defer();
        var url = fullUrlBase + 'api/contact/getcontacts/' + page + "/" + idContactlist;

        $http.post(url,{stringsearch: stringsearch,stateend:stateend} )
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

      function contactcsv(filecsv) {
        var url = fullUrlBase + 'api/contact/contacts/';
        var deferred = $q.defer();
        var formData = new FormData();
        formData.append('filecsv', filecsv);

        $http.post(url, formData, {
          headers: {
            'Content-type': undefined
          },
          transformRequest: angular.identity
        })
          .success(function (res) {
            deferred.resolve(res);
          })
          .error(function (res) {
            deferred.resolve(res);
            //notificationService.error(res.message);
          });

        return deferred.promise;
      }

      function changestatus(data) {
        var deferred = $q.defer();
        $http.post(fullUrlBase + 'api/contact/changestatus', data)
          .success(function (data) {
            deferred.resolve(data);
          })
          .error(function (data) {
            deferred.reject(data);
            notificationService.error(data.message);
          });

        return deferred.promise;
      }
      function changesuscribeselected(idsContact, idContactlist, valueSuscribe) {
        var deferred = $q.defer();
        var datas = [idsContact, idContactlist, valueSuscribe];
        $http.post(fullUrlBase + 'api/contact/changesuscribeselected', datas)
          .success(function (data) {
            deferred.resolve(data);
          })
          .error(function (data) {
            deferred.reject(data);
            notificationService.error(data.message);
          });

        return deferred.promise;
      }

      function addcontactbatch(batchcontact, idContactlist) {
        var deferred = $q.defer();
        $http.post(fullUrlBase + 'api/contact/addcontactbatch/' + idContactlist, batchcontact)
          .success(function (data) {
            deferred.resolve(data);
          })
          .error(function (data) {
            deferred.reject(data);
            notificationService.error(data.message);
          });

        return deferred.promise;
      }

      function customfield(idContactlist) {
        var deferred = $q.defer();
        $http.get(fullUrlBase + 'api/contact/customfield/' + idContactlist)
          .success(function (data) {
            deferred.resolve(data);
          })
          .error(function (data) {
            deferred.reject(data);
            notificationService.error(data.message);
          });

        return deferred.promise;
      }

      function customfieldselect(idContactlist) {
        var deferred = $q.defer();
        $http.get(fullUrlBase + 'api/contact/customfieldselect/' + idContactlist)
          .success(function (data) {
            deferred.resolve(data);
          })
          .error(function (data) {
            deferred.reject(data);
            notificationService.error(data.message);
          });

        return deferred.promise;
      }

      function addContact(contact, idContactlist) {
        var deferred = $q.defer();
        $http.post(fullUrlBase + 'api/contact/addcontact/' + idContactlist, contact)
          .success(function (data) {
            deferred.resolve(data);
          })
          .error(function (data) {
            deferred.reject(data);
            if (!data.code || (data.code != 409 && data.code != 410)) {
              notificationService.error(data.message);
            }
          });

        return deferred.promise;
      }

      function editContact(idContact, key, value, idContactlist, idCustomfield) {
        var deferred = $q.defer();
        var data = {
          idContact: idContact,
          key: key,
          value: value,
          idCustomfield: idCustomfield
        };
        $http.post(fullUrlBase + 'api/contact/editcontact/' + idContactlist, data)
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


      function validatecontactbatch(batchcontact, idContactlist) {
        var deferred = $q.defer();
        var datas = [batchcontact, idContactlist];
        $http.post(fullUrlBase + 'api/contact/validatecontactbatch', datas)
          .success(function (data) {
            deferred.resolve(data);
          })
          .error(function (data) {
            deferred.reject(data);
            notificationService.error(data.message);
          });

        return deferred.promise;
      }

      function deleteContact(data) {
        var deferred = $q.defer();
        $http.post(fullUrlBase + 'api/contact/deletecontact', data)
          .success(function (data) {
            deferred.resolve(data);
          })
          .error(function (data) {
            deferred.reject(data);
            notificationService.error(data.message);
          });
        return deferred.promise;
      }

      function deleteContactSelected(data, idContactlist) {
        var deferred = $q.defer();
        datas = [data, idContactlist];
        $http.post(fullUrlBase + 'api/contact/deleteselected', datas)
          .success(function (data) {
            deferred.resolve(data);
          })
          .error(function (data) {
            deferred.reject(data);
            notificationService.error(data.message);
          });

        return deferred.promise;
      }

      function getOneContactlist(id) {
        var deferred = $q.defer();

        $http.get(fullUrlBase + 'api/contactlist/getcontactlistinfo/' + id)
          .success(function (data) {
            deferred.resolve(data);
          })
          .error(function (data) {
            deferred.reject(data);
            notificationService.error(data.message);
          });

        return deferred.promise;
      }
      function getContactlistToMoveSelected(idContactlist) {
        var deferred = $q.defer();

        $http.get(fullUrlBase + 'api/contact/getcontactlisttomoveselected/' + idContactlist)
          .success(function (data) {
            deferred.resolve(data);
          })
          .error(function (data) {
            deferred.reject(data);
            notificationService.error(data.message);
          });

        return deferred.promise;
      }
      function moveContactSelected(selectedOne, contacts, idContaclistfrom) {
        var deferred = $q.defer();
        var datas = [selectedOne, contacts, idContaclistfrom];
        $http.post(fullUrlBase + 'api/contact/movecontactselected', datas)
          .success(function (data) {
            deferred.resolve(data);
          })
          .error(function (data) {
            deferred.reject(data);
            notificationService.error(data.message);
          });

        return deferred.promise;
      }
      function validateCopyContactSelected(selectedOne, contacts, idContaclistfrom) {
        var deferred = $q.defer();
        var datas = [selectedOne, contacts, idContaclistfrom];
        $http.post(fullUrlBase + 'api/contact/validatecopycontactselected', datas)
          .success(function (data) {
            deferred.resolve(data);
          })
          .error(function (data) {
            deferred.reject(data);
            notificationService.error(data.message);
          });

        return deferred.promise;
      }
      function copyContactSelected(selectedOne, contacts, idContaclistfrom) {
        var deferred = $q.defer();
        var datas = [selectedOne, contacts, idContaclistfrom];
        $http.post(fullUrlBase + 'api/contact/copycontactselected', datas)
          .success(function (data) {
            deferred.resolve(data);
          })
          .error(function (data) {
            deferred.reject(data);
            notificationService.error(data.message);
          });

        return deferred.promise;
      }
      function getOneContact(idContact) {
        var deferred = $q.defer();

        $http.get(fullUrlBase + 'api/contact/getonecontact/' + idContact)
          .success(function (data) {
            deferred.resolve(data);
          })
          .error(function (data) {
            deferred.reject(data);
            notificationService.error(data.message);
          });

        return deferred.promise;
      }
      function getAllSMS(idContact, page, name) {
        var deferred = $q.defer();
        var data = [idContact, page, name];
        $http.post(fullUrlBase + 'api/contact/getallsms', data)
          .success(function (data) {
            deferred.resolve(data);
          })
          .error(function (data) {
            deferred.reject(data);
            notificationService.error(data.message);
          });

        return deferred.promise;
      }
      function getAllMAIL(idContact, page, name) {
        var deferred = $q.defer();
        var data = [idContact, page, name];
        $http.post(fullUrlBase + 'api/contact/getallmail', data)
          .success(function (data) {
            deferred.resolve(data);
          })
          .error(function (data) {
            deferred.reject(data);
            notificationService.error(data.message);
          });

        return deferred.promise;
      }
      function previewMailTemplateContent(id) {
        var deferred = $q.defer();
        var url = fullUrlBase + 'api/contact/preview/' + id;
        $http.post(url)
          .success(function (data) {
            deferred.resolve(data);
          })
          .error(function (data) {
            deferred.reject(data);
          });

        return deferred.promise;
      }

      function getAllIndicatives() {
        var deferred = $q.defer();
        var url = fullUrlBase + "api/country/indicatives";

        $http.get(url)
          .success(function (response) {
            deferred.resolve(response);
          })
          .error(function (error) {
            deferred.reject(error);
          });

        return deferred.promise;
      }

      return {
        contactcsv: contactcsv,
        getAll: getAll,
        validateTotalContacts: validateTotalContacts,
        exportMoreContacts: exportMoreContacts,
        changestatus: changestatus,
        addcontactbatch: addcontactbatch,
        customfield: customfield,
        addContact: addContact,
        editContact: editContact,
        getAllIndicative: getAllIndicative,
        validatecontactbatch: validatecontactbatch,
        deleteContact: deleteContact,
        deleteContactSelected: deleteContactSelected,
        getOneContactlist: getOneContactlist,
        customfieldselect: customfieldselect,
        getContactlistToMoveSelected: getContactlistToMoveSelected,
        moveContactSelected: moveContactSelected,
        validateCopyContactSelected: validateCopyContactSelected,
        copyContactSelected: copyContactSelected,
        changesuscribeselected: changesuscribeselected,
        getOneContact: getOneContact,
        getAllSMS: getAllSMS,
        getAllMAIL: getAllMAIL,
        previewMailTemplateContent: previewMailTemplateContent,
        getAllIndicatives:getAllIndicatives
      };
    }])
    .factory('setData', function () {
      var obj = {};
      var datareturn = { name: "hola" };
      obj.setData = function (data) {
        datareturn = data;
      }

      obj.getData = function () {
        return datareturn;
      }
      return obj;
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
    .factory('arrayConstruct', function () {
      function toObject(arr) {
        var data = {};
        for (var k in arr) {
          if (arr.hasOwnProperty(k)) {
            data[k] = {
              email: arr[k]['email'],
              indicative: arr[k]['indicative'],
              phone: arr[k]['phone'],
              name: arr[k]['name'],
              lastname: arr[k]['lastname'],
              birthdate: arr[k]['birthdate']
            };
          }
        }
        return data;
      }
      return {
        toObject: toObject
      };
    });
})();
