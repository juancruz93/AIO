angular.module('smstwoway.services', [])
        .service('restservices', ['$http', '$q', 'contantSmstwoway', 'notificationService', function ($http, $q, contantSmstwoway, notificationService) {
            //Trae el total de registros de smstwoway para mostrarlas en el index de smstwoway
            this.getAllSmsTwoway = function (page, filter) {
              var deferred = $q.defer();

              var url = contantSmstwoway.urlPeticion.indexLoteTwoway + page;
              $http.post(url, filter)
                      .then(function (data) {
                        deferred.resolve(data);
                      })
                      .catch(function (data) {
                        deferred.reject(data.data);
                      });
              return deferred.promise;
            }
            //Retorna listado de las categorias de SMS para filtrar por las mismas
            this.getCategory = function () {
              var defer = $q.defer();
              var promise = defer.promise;

              $http.get(contantSmstwoway.urlPeticion.getCategory)
                      .then(function (data) {
                        defer.resolve(data.data);
                      })
                      .catch(function (data) {
                        defer.reject(data.data);
                      });

              return promise;
            }
            //Trae en el select de envio rapido y csv el listado de timezones
            this.getlisttimezone = function () {
              var defer = $q.defer();
              var promise = defer.promise;

              $http.get(contantSmstwoway.urlPeticion.getTimezone)
                      .then(function (data) {
                        defer.resolve(data);
                      })
                      .catch(function (data) {
                        defer.reject(data);
                      });

              return promise;
            }
            this.create = function (data) {
              var defer = $q.defer();
              $http.post(contantSmstwoway.urlPeticion.createLoteTwoway, data)
                      .success(function (data) {
                        defer.resolve(data);
                      })
                      .error(function (data) {
                        defer.reject(data);
                      });

              return defer.promise;
            };
            this.edit = function (data) {
              var defer = $q.defer();
              $http.put(contantSmstwoway.urlPeticion.editLoteTwoway, data)
                      .success(function (data) {
                        defer.resolve(data);
                      })
                      .error(function (data) {
                        defer.reject(data.message);
                      });

              return defer.promise;
            };

            this.getcontactlist = function () {
              var defer = $q.defer();
              $http.get(contantSmstwoway.urlPeticion.getcontactlist)
                      .success(function (data) {
                        defer.resolve(data);
                      })
                      .error(function (data) {
                        defer.reject(data);
                      });

              return defer.promise;
            };
            this.getsegments = function () {
              var defer = $q.defer();
              $http.get(contantSmstwoway.urlPeticion.getsegments)
                      .success(function (data) {
                        defer.resolve(data);
                      })
                      .error(function (data) {
                        defer.reject(data);
                      });

              return defer.promise;
            };
            this.createcsv = function (data) {
              var defer = $q.defer();
              var config = {};
              config.headers = {};
              config.transformRequest = angular.identity;
              config.headers['Content-Type'] = undefined;
              var formData = new FormData();
              for (var key in data) {
                if (!(data[key] instanceof File)) {
                  if (typeof data[key] == "object") {
                    formData.append(key, JSON.stringify(data[key]));
                  } else {
                    formData.append(key, data[key]);
                  }

                } else {
                  formData.append(key, data[key]);
                }
              }
              $http.post(contantSmstwoway.urlPeticion.createCsvTwoway, formData, config)
                      .success(function (data) {
                        defer.resolve(data);
                      })
                      .error(function (data) {
                        defer.reject(data);
                      });

              return defer.promise;
            };
            this.countContact = function (data) {
              var defer = $q.defer();

              $http.post(contantSmstwoway.urlPeticion.countContact, data)
                      .success(function (data) {
                        defer.resolve(data);
                      })
                      .error(function (data) {
                        defer.reject(data);
                      });

              return defer.promise;
            };
            this.listfullsmstemplate = function () {
              var defer = $q.defer();
              $http.get(contantSmstwoway.urlPeticion.listSmsTemplate)
                      .success(function (data) {
                        defer.resolve(data);
                      });
              return defer.promise;
            }

            this.savesmstwowaycontact = function (data) {
              var defer = $q.defer();
              $http.post(contantSmstwoway.urlPeticion.saveSmstowwayContact, data)
                      .success(function (data) {
                        defer.resolve(data);
                      })
                      .error(function (data) {
                        defer.reject(data);
                      });
              return defer.promise;
            };
            this.changestatus = function (id, status) {
              var defer = $q.defer();
              $http.post(contantSmstwoway.urlPeticion.changeStatusTwoway + id, {status: status})
                      .success(function (data) {
                        defer.resolve(data);
                      })
                      .error(function (data) {
                        defer.reject(data);
                      });

              return defer.promise;
            };
            //Funcion que trae los datos al editar en el envio rapido
            this.getDataAll = function (id) {
              var defer = $q.defer();
              $http.post(contantSmstwoway.urlPeticion.changeDataEditAll, id)
                      .success(function (data) {
                        defer.resolve(data);
                      })
                      .error(function (data) {
                        defer.reject(data);
                      });
              return defer.promise;
            };
            this.getOne = function (id) {
              var defer = $q.defer();
              $http.post(contantSmstwoway.urlPeticion.changeDataEditAll, id)
                      .success(function (data) {
                        defer.resolve(data);
                      })
                      .error(function (data) {
                        defer.reject(data);
                      });
              return defer.promise;
            };

            this.validateDate = function (id) {
              var defer = $q.defer();
              $http.post(contantSmstwoway.urlPeticion.getValidationDate, id)
                      .success(function (data) {
                        defer.resolve(data);
                      })
                      .error(function (data) {
                        defer.reject(data);
                      });
              return defer.promise;
            };

            this.getEdit = function (id) {
              var defer = $q.defer();

              $http.post(contantSmstwoway.urlPeticion.getInforEdit, id)
                      .success(function (data) {
                        defer.resolve(data);
                      })
                      .error(function (data) {
                        defer.reject(data);
                      });
              return defer.promise;
            };

            this.cancelEdit = function (id) {
              var defer = $q.defer();

              $http.post(contantSmstwoway.urlPeticion.calcelEdit, id)
                      .success(function (data) {
                        defer.resolve(data);
                      })
                      .error(function (data) {
                        defer.reject(data);
                      });
              return defer.promise;
            };
            this.editcsv = function (data) {
              var defer = $q.defer();
              $http.post(contantSmstwoway.urlPeticion.editcsv, data)
                      .success(function (data) {
                        defer.resolve(data);
                      })
                      .error(function (data) {
                        defer.reject(data);
                      });

              return defer.promise;

            };
            this.getAvalaibleCountry = function () {
              var defer = $q.defer();
              $http.post(contantSmstwoway.urlPeticion.getavalaiblecountry)
                      .success(function (data) {
                        defer.resolve(data);
                      })
                      .error(function (data) {
                        defer.reject(data);
                      });

              return defer.promise;
            };
          }
        ])
        .factory('notificationService', function (contantSmstwoway) {
          function error(message) {
            slideOnTop(message, contantSmstwoway.milliSeconds.fourThousand, contantSmstwoway.slideOnTop.classSlideRemoveCircle, contantSmstwoway.classToogle.danger);
          }

          function success(message) {
            slideOnTop(message, contantSmstwoway.milliSeconds.fourThousand, contantSmstwoway.slideOnTop.classSlideOkCircle, contantSmstwoway.classToogle.success);
          }

          function warning(message) {
            slideOnTop(message, contantSmstwoway.milliSeconds.fourThousand, contantSmstwoway.slideOnTop.classSlideExclamationSign, contantSmstwoway.classToogle.warning);
          }

          function notice(message) {
            slideOnTop(message, contantSmstwoway.milliSeconds.fourThousand, contantSmstwoway.slideOnTop.classSlideExclamationSign, contantSmstwoway.classToogle.notice);
          }

          function info(message) {
            slideOnTop(message, contantSmstwoway.milliSeconds.fourThousand, contantSmstwoway.slideOnTop.classSlideExclamationSign, contantSmstwoway.classToogle.info);
          }

          return {
            error: error,
            success: success,
            warning: warning,
            notice: notice,
            info: info
          };
        })
        .factory('misc', function ($q, contantSmstwoway) {
          return {
            validationGeneral: function (data) {
              var defer = $q.defer();
              var promise = defer.promise;
              var name = data.name;
              var cat = (data.category) ? data.category : data.idSmsCategory;
              var gmt = data.gmt;
              var dtp = data.dtpicker = $('#dtpicker').val();
              var sentNow = (data.sentNow) ? data.sentNow : data.dateNow;
              var optionsAvanced = (data.optionsAvanced) ? data.optionsAvanced : data.advancedoptions;
              var sendNotification = (data.sendNotification) ? data.sendNotification : data.notification;
              var emailNotification = (data.emailNotification) ? data.emailNotification : data.email;

              if (angular.isUndefined(name) || !name) {
                defer.reject(contantSmstwoway.error.messages.msgNameSent);
              }
              if (angular.isUndefined(cat) || !cat) {
                defer.reject(contantSmstwoway.error.messages.msgCategory);
              }
              if (!sentNow || sentNow != true) {
                if (angular.isUndefined(gmt) || !gmt) {
                  defer.reject(contantSmstwoway.error.messages.msgTimezone);
                }
                if (angular.isUndefined(dtp) || !dtp) {
                  defer.reject(contantSmstwoway.error.messages.msgDateTime);
                }
              }
              if (data.typeResponse.length == 0) {
                defer.reject(contantSmstwoway.error.messages.msgTypeResponseNotClicked);
              } else {
                for (var key in data.typeResponse) {
                  if ((!data.typeResponse[key].homologate || data.typeResponse[key].homologate == "") || (!data.typeResponse[key].response || data.typeResponse[key].response == "")) {
                    defer.reject(contantSmstwoway.error.messages.msgTypeResponseEmpty);
                  } else if (data.typeResponse.length < contantSmstwoway.values.messages.msgMinTypeResponseValue) {
                    defer.reject(contantSmstwoway.error.messages.msgTypeResponseMinLength);
                  } else if (data.typeResponse[key].response || data.typeResponse[key].homologate) {
                    var homologatecontent = data.typeResponse[key].homologate.split(",");
                    var patternBlankSpaces = contantSmstwoway.patterns.blankSpacesResponseAndHomologate;
                    var patternAccents = contantSmstwoway.patterns.accentsResponseAndHomologate;
                    if (patternAccents.test(data.typeResponse[key].response) || patternBlankSpaces.test(homologatecontent) || patternAccents.test(homologatecontent)) {
                      defer.reject(contantSmstwoway.error.messages.msgBlankSpaces);
                    }
                    if (homologatecontent.length > contantSmstwoway.values.messages.msgMaxHomologateContentValue) {
                      defer.reject(contantSmstwoway.error.messages.msgTypeResponseHomologateMinLength);
                    }
                  }
                }
              }
              if (optionsAvanced == true) {
                if (sendNotification == true) {
                  if (!emailNotification) {
                    defer.reject(contantSmstwoway.error.messages.msgEmailEmpty);
                  } else if (emailNotification) {
                    var email = emailNotification.split(",");
                    if (email.length == 0) {
                      defer.reject(contantSmstwoway.error.messages.msgEmailEmpty2);
                    }
                    if (email.length > contantSmstwoway.values.messages.msgMaxEmailValue) {
                      defer.reject(contantSmstwoway.error.messages.msgMaxEmail);
                    }
                    for (var i = 0; i < email.length; i++) {
                      var re = contantSmstwoway.patterns.verifyCorrectEmail;
                      email[i] = email[i].trim();
                      if (!email[i].match(re)) {
                        defer.reject(contantSmstwoway.error.messages.msgInvalidMail1 + email[i] + contantSmstwoway.error.messages.msgInvalidMail2);
                      }
                    }
                  }
                }
                if (data.divideSending == true) {
                  if (!data.quantity) {
                    defer.reject(contantSmstwoway.error.messages.msgIntervalos);
                  } else if (!data.sendingTime) {
                    defer.reject(contantSmstwoway.error.messages.msgSentTime);
                  } else if (!data.timeFormat) {
                    defer.reject(contantSmstwoway.error.messages.msgTimeFormat);
                  }
                }
              }
              defer.resolve();
              return promise;
            }
          }
        });
