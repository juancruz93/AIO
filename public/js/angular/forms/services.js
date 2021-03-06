'use strict';
(function () {
  angular.module('forms.services', [])
          .factory('restService', ['$http', '$q', 'notificationService','constantForms', function ($http, $q, notificationService, constantForms) {

              function getAllReportEmail(page, search) {
                var deferred = $q.defer();
                var url = constantForms.UrlPeticion.Urls.getAllReportEmail + page;
                $http.post(url, search)
                        .success(function (data) {
                          deferred.resolve(data);
                        })
                        .error(function (data) {
                          deferred.reject(data);
                          notificationService.error(data.message);
                        });
                return deferred.promise;
              }

              function getContactlist() {
                var deferred = $q.defer();
                var url = constantForms.UrlPeticion.Urls.getContactlist;
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

              function getMailTemplate() {
                var deferred = $q.defer();
                var url = constantForms.UrlPeticion.Urls.getMailTemplate;
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


              function previewMailTemplateContent(id) {
                var deferred = $q.defer();
                var url = constantForms.UrlPeticion.Urls.previewMailTemplateContent + id;
                $http.post(url)
                        .success(function (data) {
                          deferred.resolve(data);
                        })
                        .error(function (data) {
                          deferred.reject(data);
                          notificationService.error(data.message);
                        });
                return deferred.promise;
              }

              function saveBasicInformation(data) {
                var deferred = $q.defer();
                var url = constantForms.UrlPeticion.Urls.saveBasicInformation;
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

              function updatebasicinformation(idForm, data) {
                var deferred = $q.defer();
                var url = constantForms.UrlPeticion.Urls.updatebasicinformation+ idForm;
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
              function getCustomfield(idContactlist) {
                var deferred = $q.defer();
                var url = constantForms.UrlPeticion.Urls.getCustomfield + idContactlist;
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

              function listForms(page, data) {
                var deferred = $q.defer();
                var url = constantForms.UrlPeticion.Urls.listForms + page;
                $http.post(url, data)
                        .success(function (res) {
                          deferred.resolve(res);
                        })
                        .error(function (res) {
                          deferred.reject(res);
                          notificationService.error(res.message);
                        });
                return deferred.promise;
              }

              function getAllFormCategory() {
                var deferred = $q.defer();
                var url = constantForms.UrlPeticion.Urls.getAllFormCategory;
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

              function getInformationForm(id) {
                var deferred = $q.defer();
                var url = constantForms.UrlPeticion.Urls.getInformationForm + id;
                $http.get(url)
                        .success(function (data) {
                          deferred.resolve(data);
                        })
                        .error(function (data) {
                          deferred.reject(data);
                        });
                return deferred.promise;
              }

              function saveForm(id, data) {
                var deferred = $q.defer();
                var url = constantForms.UrlPeticion.Urls.saveForm + id;
                $http.post(url, data)
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

              function getAllIndicative() {
                var deferred = $q.defer();
                $http.get(constantForms.UrlPeticion.Urls.getAllIndicative)
                        .success(function (data) {
                          deferred.resolve(data);
                        })
                        .error(function (data) {
                          deferred.reject(data);
                          notificationService.error(data.message);
                        });

                return deferred.promise;
              }

              function getAll(page, idContactlist, idForm, stringsearch) {
                var deferred = $q.defer();
                var url = constantForms.UrlPeticion.Urls.getAll + page + "/" + idContactlist + "/" + idForm;

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

              function customfieldselect(idContactlist) {
                var deferred = $q.defer();
                $http.get(constantForms.UrlPeticion.Urls.customfieldselect + idContactlist)
                        .success(function (data) {
                          deferred.resolve(data);
                        })
                        .error(function (data) {
                          deferred.reject(data);
                          notificationService.error(data.message);
                        });

                return deferred.promise;
              }

              function editContact(idContact, key, value, idContactlist) {
                var deferred = $q.defer();
                var data = {
                  idContact: idContact,
                  key: key,
                  value: value
                };
                $http.post(constantForms.UrlPeticion.Urls.editContact + idContactlist, data)
                        .success(function (data) {
                          deferred.resolve(data);
                        })
                        .error(function (data) {
                          deferred.reject(data);
                          notificationService.error(data.message);
                        });

                return deferred.promise;
              }

              function changestatus(idContact, idContactlist) {
                var deferred = $q.defer();
                $http.get(constantForms.UrlPeticion.Urls.changestatus + idContact + "/" + idContactlist)
                        .success(function (data) {
                          deferred.resolve(data);
                        })
                        .error(function (data) {
                          deferred.reject(data);
                          notificationService.error(data.message);
                        });

                return deferred.promise;
              }

              function deleteContact(idContact, idContactlist) {
                var deferred = $q.defer();
                $http.delete(constantForms.UrlPeticion.Urls.deleteContact + idContact + '/' + idContactlist)
                        .success(function (data) {
                          deferred.resolve(data);
                        })
                        .error(function (data) {
                          deferred.reject(data);
                          notificationService.error(data.message);
                        });

                return deferred.promise;
              }

              function getOptin(idForm) {
                var deferred = $q.defer();
                $http.get(constantForms.UrlPeticion.Urls.getOptin + idForm)
                        .success(function (data) {
                          deferred.resolve(data);
                        })
                        .error(function (data) {
                          deferred.reject(data);
                          notificationService.error(data.message);
                        });

                return deferred.promise;
              }

              function getWelcomeMail(idForm) {
                var deferred = $q.defer();
                $http.get(constantForms.UrlPeticion.Urls.getWelcomeMail + idForm)
                        .success(function (data) {
                          deferred.resolve(data);
                        })
                        .error(function (data) {
                          deferred.reject(data);
                          notificationService.error(data.message);
                        });

                return deferred.promise;
              }

              function getNotification(idForm) {
                var deferred = $q.defer();
                $http.get(constantForms.UrlPeticion.Urls.getNotification + idForm)
                        .success(function (data) {
                          deferred.resolve(data);
                        })
                        .error(function (data) {
                          deferred.reject(data);
                          notificationService.error(data.message);
                        });

                return deferred.promise;
              }

              function getcontentform(idForm) {
                var deferred = $q.defer();
                $http.get(constantForms.UrlPeticion.Urls.getcontentform + idForm)
                        .success(function (data) {
                          deferred.resolve(data);
                        })
                        .error(function (data) {
                          deferred.reject(data);
                        });

                return deferred.promise;
              }

              function addFormCategory(data) {
                var deferred = $q.defer();
                var url = constantForms.UrlPeticion.Urls.addFormCategory;

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

              function getallmailtemplatebyfilter(search) {
                var deferred = $q.defer();
             
                $http.post(constantForms.UrlPeticion.Urls.getallmailtemplatebyfilter, search)
                        .success(function (data) {
                          //console.log('getallmailtemplate', data);
                          if (data.length == 0) {
                            notificationService.error("No se encontro ninguna plantilla de correo.");
                            deferred.reject(data);
                          }
                          deferred.resolve(data);
                        })
                        .error(function (data) {
                          deferred.reject(data);
                          notificationService.error(data);
                        });
                return deferred.promise;
              }
              
              function suscripsforms(idForm, page, stringsearch){
                var deferred = $q.defer();
                $http.post(constantForms.UrlPeticion.Urls.suscripsforms + idForm+ '/'+page, stringsearch)
                  .success(function (data) {
                    deferred.resolve(data);
                  })
                  .error(function (data) {
                    deferred.reject(data);
                  });
                  
                  return deferred.promise;
              }
              
              function dowloadReportContactsForm(idForm) {
                var deferred = $q.defer();
                var url = constantForms.UrlPeticion.Urls.dowloadReportContactsForm+idForm;
                $http.post(url)
                        .success(function (data) {
                          deferred.resolve(data);
                        })
                        .error(function (data) {
                          deferred.reject(data);
                          notificationService.error(data.message);
                        });
                return deferred.promise;
              }
              
              function deleteForm(data){
                var url = constantForms.UrlPeticion.Urls.deleteForm;
                var deferred = $q.defer();
                $http.post(url, data)
                        .success(function(data){
                          deferred.resolve(data);
                        }).error(function(data){
                          deferred.reject(data);
                          notificationService.error(data.message);
                        });
                return deferred.promise;
              }

              return {
                getAllReportEmail: getAllReportEmail,
                getContactlist: getContactlist,
                getMailTemplate: getMailTemplate,
                previewMailTemplateContent: previewMailTemplateContent,
                saveBasicInformation: saveBasicInformation,
                getCustomfield: getCustomfield,
                listForms: listForms,
                getAllFormCategory: getAllFormCategory,
                saveForm: saveForm,
                getInformationForm: getInformationForm,
                getAllIndicative: getAllIndicative,
                getAll: getAll,
                getAllIndicative: getAllIndicative,
                customfieldselect: customfieldselect,
                editContact: editContact,
                changestatus: changestatus,
                deleteContact: deleteContact,
                getOptin: getOptin,
                getWelcomeMail: getWelcomeMail,
                getNotification: getNotification,
                updatebasicinformation: updatebasicinformation,
                getcontentform: getcontentform,
                addFormCategory: addFormCategory,
                getallmailtemplatebyfilter:getallmailtemplatebyfilter,
                suscripsforms: suscripsforms,
                dowloadReportContactsForm: dowloadReportContactsForm,
                deleteForm: deleteForm
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
