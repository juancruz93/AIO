'use strict';
(function () {
  angular.module('mail.services', [])
          .factory('restService', ['$http', '$q', 'notificationService', function ($http, $q, notificationService) {

              function getContactlist() {
                var deferred = $q.defer();
                var url = fullUrlBase + 'api/sendmail/getcontactlist';
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

              function getMailFilters() {
                var deferred = $q.defer();
                var url = fullUrlBase + 'api/sendmail/getmailfilters';
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

              function getSegment() {
                var deferred = $q.defer();
                var url = fullUrlBase + 'api/sendmail/getsegment';
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

              function countContact(data) {
                var deferred = $q.defer();
                var url = fullUrlBase + 'api/sendmail/countcontact';
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

              function addAddressees(data) {
                var deferred = $q.defer();
                var url = fullUrlBase + 'api/sendmail/addaddressees';
                $http.post(url, data)
                        .success(function (data) {
                          deferred.resolve(data);
                        })
                        .error(function (data) {
                          deferred.reject(data);
                          if (data.type) {
                            notificationService.warning(data.message);
                          } else {
                            notificationService.error(data.message);
                          }
                        });

                return deferred.promise;
              }
              
              function only(data) {
                var deferred = $q.defer();
                var url = fullUrlBase + 'api/sendmail/only';
                $http.post(url, data)
                        .success(function (data) {
                          deferred.resolve(data);
                        })
                        .error(function (data) {
                          deferred.reject(data);
                          if (data.type) {
                            notificationService.warning(data.message);
                          } else {
                            notificationService.error(data.message);
                          }
                        });

                return deferred.promise;
              }
              
              function getlisttimezone() {
                var deferred = $q.defer();
                $http.get(fullUrlBase + 'mail/timezone/')
                        .success(function (data) {
                          deferred.resolve(data);
                        })
                        .error(function (data) {
                          deferred.reject(data);
                          notificationService.error(data);
                        });

                return deferred.promise;
              }

              function getemailsend() {
                var deferred = $q.defer();
                $http.get(fullUrlBase + 'mail/emailsender/')
                        .success(function (data) {
                          deferred.resolve(data);
                        })
                        .error(function (data) {
                          deferred.reject(data);
                          notificationService.error(data);
                        });

                return deferred.promise;
              }
              
              function getReplyTo() {
                var deferred = $q.defer();
                $http.get(fullUrlBase + 'mail/replyto/')
                        .success(function (data) {
                          deferred.resolve(data);
                        })
                        .error(function (data) {
                          deferred.reject(data);
                          notificationService.error(data);
                        });

                return deferred.promise;
              }

              function addBasicInformation(data) {
                var deferred = $q.defer();
                var url = fullUrlBase + 'mail/basicinformation/';
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

              function editBasicInformation(idMail, data) {
                var deferred = $q.defer();
                var url = fullUrlBase + 'mail/editbasicinformation/' + idMail;
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

              function saveAdvanceOptions(idMail, data) {

                var deferred = $q.defer();
                var url = fullUrlBase + 'api/sendmail/saveadvanceoptions/' + idMail;
                console.log(data);
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

              function getemailname() {
                var deferred = $q.defer();
                $http.get(fullUrlBase + 'mail/emailname/')
                        .success(function (data) {
                          deferred.resolve(data);
                        })
                        .error(function (data) {
                          deferred.reject(data);
                          notificationService.error(data);
                        });

                return deferred.promise;
              }

              function addEmailName(data) {
                var deferred = $q.defer();
                var url = fullUrlBase + 'mail/addemailname/';
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

              function addEmailSender(data) {
                var deferred = $q.defer();
                var url = fullUrlBase + 'mail/addemailsender/';
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
              
              function addReplyTo(data) {
                var deferred = $q.defer();
                var url = fullUrlBase + 'mail/addreplyto/';
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

              function findMail(idMail) {
                var deferred = $q.defer();
                var url = fullUrlBase + 'api/sendmail/findmail/' + idMail;
                $http.get(url)
                        .success(function (data) {
                          deferred.resolve(data);
                        })
                        .error(function (data) {
//              console.log(data.message);
                          deferred.reject(data);
                          notificationService.error(data.message);
                        });

                return deferred.promise;
              }

              function findEmailSender(idEmailsender) {
                var deferred = $q.defer();
                var url = fullUrlBase + 'api/sendmail/findemailsender/' + idEmailsender;
                $http.get(url)
                        .success(function (data) {
                          deferred.resolve(data);
                        })
                        .error(function (data) {
                          deferred.reject(data);
                          notificationService.error(data);
                        });

                return deferred.promise;
              }

              function findMailAttachment(idEmail) {
                var deferred = $q.defer();
                var url = fullUrlBase + 'api/sendmail/findmailattachment/' + idEmail;
                $http.get(url)
                        .success(function (data) {
                          deferred.resolve(data);
                        })
                        .error(function (data) {
                          deferred.reject(data);
                          notificationService.error(data);
                        });

                return deferred.promise;
              }

              function findEmailName(idEmailname) {
                var deferred = $q.defer();
                var url = fullUrlBase + 'api/sendmail/findemailname/' + idEmailname;
                $http.get(url)
                        .success(function (data) {
                          deferred.resolve(data);
                        })
                        .error(function (data) {
                          deferred.reject(data);
                          notificationService.error(data);
                        });

                return deferred.promise;
              }
              
              function findReplyto(idReplyto) {
                var deferred = $q.defer();
                var url = fullUrlBase + 'api/sendmail/findreplyto/' + idReplyto;
                $http.get(url)
                        .success(function (data) {
                          deferred.resolve(data);
                        })
                        .error(function (data) {
                          deferred.reject(data);
                          notificationService.error(data);
                        });

                return deferred.promise;
              }

              function getAllMail(page, filter) {
                var deferred = $q.defer();
                var url = fullUrlBase + 'api/sendmail/getallmail/' + page;
                //console.log(filter);
                $http.post(url, filter)
                        .success(function (data) {
                          deferred.resolve(data);
                        })
                        .error(function (data) {
                          deferred.reject(data);
                          notificationService.error(data);
                        });

                return deferred.promise;
              }

              function getContentMail(idMail) {
                var deferred = $q.defer();
                var url = fullUrlBase + 'api/sendmail/getcontentmail/' + idMail;
                $http.get(url)
                        .success(function (data) {
                          deferred.resolve(data);
                        })
                        .error(function (data) {
                          deferred.reject(data);
                          notificationService.error(data);
                        });
                return deferred.promise;
              }

              function getMailCategory() {
                var deferred = $q.defer();
                var url = fullUrlBase + 'api/sendmail/getmailcategory';
                console.log(url);
                $http.get(url)
                        .success(function (data) {
                          deferred.resolve(data);
                        })
                        .error(function (data) {
                          deferred.reject(data);
                          notificationService.error(data);
                        });
                return deferred.promise;
              }

              function addContentEditor(idMail, data) {
                var deferred = $q.defer();
                var url = fullUrlBase + 'mail/contenteditor/' + idMail;
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

              function getMailCategoryByIdMail(idMail) {
                var deferred = $q.defer();
                var url = fullUrlBase + 'api/sendmail/getmailcategoryidmail/' + idMail;
                $http.get(url)
                        .success(function (data) {
                          deferred.resolve(data);
                        })
                        .error(function (data) {
                          deferred.reject(data);
                          notificationService.error(data);
                        });
                return deferred.promise;
              }

              function findMailCategory(idMailcategory) {
                var deferred = $q.defer();
                var url = fullUrlBase + 'api/sendmail/findmailcategory/' + idMailcategory;
                $http.get(url)
                        .success(function (data) {
                          deferred.resolve(data);
                        })
                        .error(function (data) {
                          deferred.reject(data);
                          notificationService.error(data);
                        });
                return deferred.promise;
              }

              function deleteMail(idMail) {
                var deferred = $q.defer();
                var url = fullUrlBase + 'api/sendmail/deletemail/' + idMail;
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

              function addPlaintext(data) {
                var deferred = $q.defer();
                var url = fullUrlBase + 'api/sendmail/updateplainttext';
                $http.put(url, data)
                        .success(function (data) {
                          deferred.resolve(data);
                        })
                        .error(function (data) {
                          deferred.reject(data);
                          notificationService.error(data);
                        });
                return deferred.promise;
              }

              function getAllMailStructure(page, name) {
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

              function sendDataGoogleAnalitics(data) {
                var deferred = $q.defer();
                var url = fullUrlBase + 'api/sendmail/addgoogleanalitics';
                $http.post(url, data)
                        .success(function (data) {
                          deferred.resolve(data);
                        })
                        .error(function (data) {
                          deferred.reject(data);
                          notificationService.error(data);
                        });
                return deferred.promise;
              }

              function getAllGallery(page) {
                var deferred = $q.defer();
                var url = fullUrlBase + 'api/gallery/' + page;
                $http.get(url)
                        .success(function (data) {
                          deferred.resolve(data);
                        })
                        .error(function (data) {
                          deferred.reject(data);
                          notificationService.error(data);
                        });
                return deferred.promise;
              }

              function addAdjunt(data) {
                var deferred = $q.defer();
                var url = fullUrlBase + 'api/sendmail/addadjunt';
                $http.post(url, data)
                        .success(function (data) {
                          deferred.resolve(data);
                        })
                        .error(function (data) {
                          deferred.reject(data);
                          notificationService.error(data);
                        });
                return deferred.promise;
              }

              function getAllAttachment(idMail) {
                var deferred = $q.defer();
                var url = fullUrlBase + 'api/sendmail/getallattachment/' + idMail;
                $http.get(url)
                        .success(function (data) {
                          deferred.resolve(data);
                        })
                        .error(function (data) {
                          deferred.reject(data);
                          notificationService.error(data);
                        });
                return deferred.promise;
              }

              function sendDataComfirmationEmail(data) {
                var deferred = $q.defer();
                var url = fullUrlBase + 'api/sendmail/sendconfirmationmail';
                $http.post(url, data)
                        .success(function (data) {
                          deferred.resolve(data);
                        })
                        .error(function (data) {
                          deferred.reject(data.message);
                          notificationService.error(data.message);
                        });
                return deferred.promise;
              }

              function sendScheduleDateEmail(data) {
                var deferred = $q.defer();
                var url = fullUrlBase + 'api/sendmail/sendscheduledateemail';
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

              function saveMailAsMailTemplate(data) {
                var deferred = $q.defer();
                var url = fullUrlBase + 'api/mailtemplate/savemailtemp';
                $http.post(url, data)
                        .success(function (data) {
                          deferred.resolve(data);
                        })
                        .error(function (data) {
                          deferred.reject(data);
                          notificationService.error(data);
                        });
                return deferred.promise;
              }

              function sendTestMail(data) {
                var defered = $q.defer();
                var url = fullUrlBase + "api/sendmail/testmail";
                $http.post(url, data)
                        .success(function (data) {
                          defered.resolve(data);
                        })
                        .error(function (data) {
                          defered.reject(data);
                          notificationService.error(data.message);
                        });

                return defered.promise;
              }

              function previewMailTemplateContent(id) {
                var deferred = $q.defer();
                var url = 'api/mail/preview/' + id;
                $http.post(url)
                        .success(function (data) {
                          deferred.resolve(data);
                        })
                        .error(function (data) {
                          deferred.reject(data);
                        });

                return deferred.promise;
              }
              function getMailContentJSON(id) {
                var deferred = $q.defer();
                var url = 'api/mail/getmailcontentjson/' + id;
                $http.post(url)
                        .success(function (data) {
                          deferred.resolve(data);
                        })
                        .error(function (data) {
                          deferred.reject(data);
                        });

                return deferred.promise;
              }

              function deleteAttached(id) {
                var deferred = $q.defer();
                var url = fullUrlBase + 'api/sendmail/deleteattached/' + id;
                $http.get(url)
                        .success(function (data) {
                          deferred.resolve(data);
                        })
                        .error(function (data) {
                          deferred.reject(data);
                        });

                return deferred.promise;
              }

              function getSaxs() {
                var deferred = $q.defer();
                var url = fullUrlBase + 'api/saxs/getall';
                $http.get(url)
                        .success(function (data) {
                          deferred.resolve(data);
                        })
                        .error(function (data) {
                          deferred.reject(data);
                        });

                return deferred.promise;
              }

              function getThumbnailMail(idMail) {
                var defered = $q.defer();
                var url = fullUrlBase + "api/sendmail/getthumbnail/" + idMail;
                $http.get(url)
                        .success(function (data) {
                          defered.resolve(data);
                        })
                        .error(function (data) {
                          defered.reject(data);
                          notificationService.error(data.message);
                        });

                return defered.promise;
              }

              function saveCategory(data) {
                var url = fullUrlBase + 'api/mailcategory/savemailcategoryinmail';
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

              function changeTestMail(idMail, data) {
                var url = fullUrlBase + "api/sendmail/changetest";
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

              function cancelMail(idMail) {
                var url = fullUrlBase + 'api/mail/cancelmail/' + idMail;
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

              function getLinksByMail(idMail) {
                var url = fullUrlBase + 'api/sendmail/getlinksbymail/' + idMail;
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

              function getMailTemplateCategory() {
                var deferred = $q.defer();
                $http.get(fullUrlBase + 'api/mailtemplate/getmailtemplatecategory')
                        .success(function (data) {
                          deferred.resolve(data);
                        })
                        .error(function (data) {
                          deferred.reject(data);
                          notificationService.error(data);
                        });

                return deferred.promise;
              }
              function sendTesterMails(data, idMail) {
                var url = fullUrlBase + 'api/sendmail/sendtester/' + idMail;
                var defer = $q.defer();
                var promise = defer.promise;
                $http.post(url, data)
                        .success(function (data) {
                          defer.resolve(data);
                        })
                        .error(function (err) {
                          defer.reject(err.message);
                          notificationService.error(err.message);
                        });

                return promise;
              }

              function getMd5publish(data) {
                var url = fullUrlBase + 'api/mail/getmdgpublish/', data;
                var defer = $q.defer();
                var promise = defer.promise;
                $http.post(url, data)
                        .success(function (data) {
                          defer.resolve(data);
                        })
                        .error(function (err) {
                          defer.reject(err.message);
                          notificationService.error(err.message);
                        });

                return promise;
              }
              
              function downloadMailPreview(idMail) {
                var defered = $q.defer();
                var url = fullUrlBase + "api/sendmail/downloadmailpreview/" + idMail;
                $http.get(url)
                        .success(function (data) {
                          defered.resolve(data);
                        })
                        .error(function (data) {
                          defered.reject(data);
                          notificationService.error(data.message);
                        });

                return defered.promise;
              }

              function  getTableResults(id) {
                //console.log("esta llegando aqui service"); return;
                var url = fullUrlBase + 'mail/structurename/'+id;
                var defer = $q.defer();
                var promise = defer.promise;
                $http.get(url)
                        .success(function (data) {
                          defer.resolve(data);
                        })
                        .error(function (err) {
                          defer.reject(err.message);
                          notificationService.error(err.message);
                        });
                return promise;
              }
              
              function deleteCustomizedpdf(id){
                var url = fullUrlBase + 'mail/deletedall/'+id;
                console.log(url);
                var defer = $q.defer();
                var promise = defer.promise;
                $http.get(url)
                  .success(function (data) {
                    defer.resolve(data);
                  })
                  .error(function (err) {
                    defer.reject(err.message);
                    notificationService.error(err.message);
                  });
                return promise;
              }
              
              function getAllAttachmentpdf(idMail) {
                var deferred = $q.defer();
                var url = fullUrlBase + 'api/sendmail/getallattachmentpdf/' + idMail;
                $http.get(url)
                        .success(function (data) {
                          deferred.resolve(data);
                        })
                        .error(function (data) {
                          deferred.reject(data);
                          notificationService.error(data);
                        });
                return deferred.promise;
              }

              return {
                getContactlist: getContactlist,
                getSegment: getSegment,
                countContact: countContact,
                addAddressees: addAddressees,
                getlisttimezone: getlisttimezone,
                getemailname: getemailname,
                getemailsend: getemailsend,
                addBasicInformation: addBasicInformation,
                findMail: findMail,
                getAllMail: getAllMail,
                addEmailName: addEmailName,
                addEmailSender: addEmailSender,
                editBasicInformation: editBasicInformation,
                getContentMail: getContentMail,
                getMailCategory: getMailCategory,
                addContentEditor: addContentEditor,
                getMailCategoryByIdMail: getMailCategoryByIdMail,
                deleteMail: deleteMail,
                addPlaintext: addPlaintext,
                getAllMailStructure: getAllMailStructure,
                getAllGallery: getAllGallery,
                addAdjunt: addAdjunt,
                getAllAttachment: getAllAttachment,
                sendDataGoogleAnalitics: sendDataGoogleAnalitics,
                findEmailSender: findEmailSender,
                findEmailName: findEmailName,
                findMailCategory: findMailCategory,
                sendDataComfirmationEmail: sendDataComfirmationEmail,
                findMailAttachment: findMailAttachment,
                sendScheduleDateEmail: sendScheduleDateEmail,
                saveMailAsMailTemplate: saveMailAsMailTemplate,
                sendTestMail: sendTestMail,
                previewMailTemplateContent: previewMailTemplateContent,
                getMailContentJSON: getMailContentJSON,
                deleteAttached: deleteAttached,
                getSaxs: getSaxs,
                saveAdvanceOptions: saveAdvanceOptions,
                getThumbnailMail: getThumbnailMail,
                saveCategory: saveCategory,
                cancelMail: cancelMail,
                changeTestMail: changeTestMail,
                getMailFilters: getMailFilters,
                getLinksByMail: getLinksByMail,
                getMailTemplateCategory: getMailTemplateCategory,
                sendTesterMails: sendTesterMails,
                getMd5publish : getMd5publish,
                downloadMailPreview: downloadMailPreview,
                getReplyTo: getReplyTo,
                addReplyTo: addReplyTo,
                findReplyto: findReplyto,
                getTableResults: getTableResults,
                only: only,
                deleteCustomizedpdf: deleteCustomizedpdf,
                getAllAttachmentpdf: getAllAttachmentpdf
              };
            }])
          .service("incomplete", function () {
            var varincomplete = false;
            this.setincomplete = function (incomple) {
              varincomplete = incomple;
            };
            this.getincomplete = function () {
              return varincomplete;
            };
          })
          .service("wizard", function () {
            var describe = false;
            var addressees = false;
            var content = false;
            var advanceoptions = false;
            var shippingdate = false;

            this.setdescribe = function (incomple) {
              describe = incomple;
            };
            this.getdescribe = function () {
              return describe;
            };

            this.setaddressees = function (incomple) {
              addressees = incomple;
            };
            this.getaddressees = function () {
              return addressees;
            };

            this.setcontent = function (incomple) {
              content = incomple;
            };
            this.getcontent = function () {
              return content;
            };

            this.setadvanceoptions = function (incomple) {
              advanceoptions = incomple;
            };
            this.getadvanceoptions = function () {
              return advanceoptions;
            };

            this.setshippingdate = function (incomple) {
              shippingdate = incomple;
            };
            this.getshippingdate = function () {
              return shippingdate;
            };

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
          .factory('$FB', ['$rootScope', function ($rootScope) {
              var fbLoaded = false;
              // Our own customisations
              var _fb = {
                loaded: fbLoaded,
                _init: function (params) {
                  if (window.FB) {
                    // FIXME: Ugly hack to maintain both window.FB
                    // and our AngularJS-wrapped $FB with our customisations
                    angular.extend(window.FB, _fb);
                    angular.extend(_fb, window.FB);
                    // Set the flag
                    _fb.loaded = true;
                    // Initialise FB SDK
                    window.FB.init(params);
                    if (!$rootScope.$$phase) {
                      $rootScope.$apply();
                    }
                  }
                }

              }
              return _fb;
            }]);

})();
