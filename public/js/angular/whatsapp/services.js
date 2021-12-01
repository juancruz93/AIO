angular.module('whatsapp.services', [])
  .service('restservices', ['$http', '$q', 'contantWhatsapp', 'notificationService', function ($http, $q, contantWhatsapp, notificationService) {
    //Trae el total de registros de whatsapp para mostrarlas en el indexc
    this.getAllWhatsapp = function (page, filter) {
      var deferred = $q.defer();
      var url = contantWhatsapp.urlPeticion.listWpp + page;
      $http.post(url, filter)
        .then(function (data) {
          deferred.resolve(data);
        })
        .catch(function (data) {
          deferred.reject(data.data);
        });
      return deferred.promise;
    }
    //Retorna listado de las categorias de WPP para filtrar por las mismas
    this.getCategory = function () {
      var defer = $q.defer();
      var promise = defer.promise;

      $http.get(contantWhatsapp.urlPeticion.getCategory)
        .then(function (data) {
          defer.resolve(data.data);
        })
        .catch(function (data) {
          defer.reject(data.data);
        });

      return promise;
    }
    //OBTIENE TOLAS LAS LC DE WPP
    this.getContactListWpp = function () {
      var defer = $q.defer();
      $http.get(contantWhatsapp.urlPeticion.getcontactlist)
              .success(function (data) {
                defer.resolve(data);
              })
              .error(function (data) {
                defer.reject(data);
              });

      return defer.promise;
    };
    //OBTIENE TODAS LAS PLANTILLAS HSM
    this.getHsmTemplates = function () {
      var deferred = $q.defer();
      $http.get(contantWhatsapp.urlPeticion.getHsmTemplates)
          .success(function (data) {
              deferred.resolve(data);
          })
          .error(function (data) {
              deferred.reject(data);
              notificationService.error(data.message);
          });

      return deferred.promise;
    };
    //OBITNE LA CANTIDAD DE CONTACTOS Y LOS CAMPOS PERSONALIZADOS RELACIONADOS A LA LC SELECCIONADA
    /*this.countContacts = function (idContactList) {
      var deferred = $q.defer();
      var url = contantWhatsapp.urlPeticion.countContacts + idContactList;
      $http.post(url, idContactList)
        .then(function (data) {
          deferred.resolve(data);
        })
        .catch(function (data) {
          deferred.reject(data.data);
        });
      return deferred.promise;
    }*/
    //EDITAR PLANTILLA HSM
    this.countContacts = function (data) {
      var url = fullUrlBase + "api/whatsapp/countcontacts";
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

  }
  ])