(function () {
  angular.module('automaticcampaign.services', [])

          .service('restServices', ['$http', '$q', 'notificationService', 'moment', function ($http, $q, notificationService, moment) {

              this.createAutomaticCampaign = function (data) {
                var defer = $q.defer();
                var url = fullUrlBase + "api/automacamp/save";
                $http.post(url, data)
                        .success(function (data) {
                          defer.resolve(data);
                        })
                        .error(function (data) {
                          defer.reject(data);
                          notificationService.error(data.message);
                        });
                return defer.promise;
              }

              this.cancelAutomaticCampaign = function (data) {
                var defer = $q.defer();
                var url = fullUrlBase + "api/automacamp/cancelautomaticcampaign";
                $http.post(url, data)
                        .success(function (data) {
                          defer.resolve(data);
                        })
                        .error(function (data) {
                          defer.reject(data);
                          notificationService.error(data.message);
                        });
                return defer.promise;
              }

              this.getservices = function () {
                var defer = $q.defer();
                var url = fullUrlBase + "api/saxs/getall";
                $http.get(url)
                        .success(function (data) {
                          if (data.length == 0) {
                            notificationService.error("No tienes servicios disponibles, para adquirir uno por favor contacta a soporte.");
                            defer.reject(data);
                          }
                          defer.resolve(data);
                        })
                        .error(function (data) {
                          defer.reject(data);
                          notificationService.error(data.message);
                        });
                return defer.promise;
              }

              this.getallcategory = function () {
                var defer = $q.defer();
                var url = fullUrlBase + "api/automacampcateg/allcategory";
                $http.get(url)
                        .success(function (data) {
                          if (data.length == 0) {
                            //notificationService.error("No se encontraron categorias para la campaña.");
                            defer.reject(data);
                          }
                          defer.resolve(data);
                        })
                        .error(function (data) {
                          defer.reject(data);
                          notificationService.error(data.message);
                        });
                return defer.promise;
              }

              this.getGmt = function () {
                var defer = $q.defer();
                var url = fullUrlBase + "mail/timezone/";
                $http.get(url)
                        .success(function (data) {
                          defer.resolve(data);
                        })
                        .error(function (data) {
                          defer.reject(data);
                          notificationService.error(data.message);
                        });
                return defer.promise;
              }

              this.editAutomaticCampaign = function (idAutomaticCampaign) {
                var defer = $q.defer();
                var url = fullUrlBase + "api/automacamp/getautomaticcampaign/" + idAutomaticCampaign;
                $http.get(url)
                        .success(function (data) {
                          defer.resolve(data);
                        })
                        .error(function (data) {
                          defer.reject(data);
                          notificationService.error(data.message);
                        });
                return defer.promise;
              }

              this.updateAutomaticCampaignAll = function (data, idAutomaticCampaign) {
                var defer = $q.defer();
                var url = fullUrlBase + "api/automacamp/updatecampaignall/" + idAutomaticCampaign;
                $http.put(url, data)
                        .success(function (data) {
                          defer.resolve(data);
                        })
                        .error(function (data) {
                          defer.reject(data);
                          notificationService.error(data.message);
                        });
                return defer.promise;
              }

              this.updateAutomaticCampaign = function (data, idAutomaticCampaign) {
                var defer = $q.defer();
                var url = fullUrlBase + "api/automacamp/updatecampaign/" + idAutomaticCampaign;
                $http.put(url, data)
                        .success(function (data) {
                          defer.resolve(data);
                        })
                        .error(function (data) {
                          defer.reject(data);
                          notificationService.error(data.message);
                        });
                return defer.promise;
              }

              this.updateAutomaticCampaignConfiguration = function (data, idAutomaticCampaign) {
                var defer = $q.defer();
                var url = fullUrlBase + "api/automacamp/updatecampaignconfiguration/" + idAutomaticCampaign;
                $http.put(url, data)
                        .success(function (data) {
                          defer.resolve(data);
                        })
                        .error(function (data) {
                          defer.reject(data);
                          notificationService.error(data.message);
                        });
                return defer.promise;
              }

              this.createAutomaticCampaignDraft = function (data) {
                var defer = $q.defer();
                var url = fullUrlBase + "api/automacamp/savedraft";
                $http.post(url, data)
                        .success(function (data) {
                          defer.resolve(data);
                        })
                        .error(function (data) {
                          defer.reject(data);
                          notificationService.error(data.message);
                        });
                return defer.promise;
              }

              this.updateStatusCampaign = function (data) {
                var defer = $q.defer();
                var url = fullUrlBase + "api/automacamp/updatestatus/" + data;
                var objSend = {status: 'draft'};
                $http.put(url, objSend)
                        .success(function (data) {
                          defer.resolve(data);
                        })
                        .error(function (data) {
                          defer.reject(data);
                          notificationService.error(data.message);
                        });
                return defer.promise;
              }

              this.listcampaign = function (page, data) {
                var defer = $q.defer();
                var url = fullUrlBase + "api/automacamp/list/" + page;
                $http.post(url, data)
                        .success(function (data) {
                          defer.resolve(data);
                        })
                        .error(function (data) {
                          defer.reject(data);
                        });
                return defer.promise;
              }  

              this.validateJsonAutomaticCampaign = function (chartViewModel, services) {
                var defer = $q.defer();
                if (chartViewModel.connections <= 0) {
                  notificationService.error("No se encontro ninguna conexion en la campaña.");
                  defer.reject();
                }
                if (chartViewModel.nodes.length < 3) {
                  notificationService.error("El numero de nodos debe ser mayor a 3.");
                  defer.reject();
                }
                if (chartViewModel.nodes.length > 61) {
                  notificationService.error("El numero de nodos debe ser mayor a 60.");
                  defer.reject();
                }
//          if (chartViewModel.nodes[chartViewModel.nodes.length - 1].theme == "operator") {
//            notificationService.error("La campaña no puede terminar en un nodo operador.");
//            defer.reject();
//          }
                if ((chartViewModel.nodes.length - 1) != chartViewModel.connections.length) {
                  notificationService.error("las conexiones no coinciden con los nodos, por favor revisar.");
                  defer.reject();
                }
                for (var i = 0; i < chartViewModel.nodes.length; i++) {
                  if (jQuery.isEmptyObject(chartViewModel.nodes[i].sendData) || chartViewModel.nodes[i].sendData.textTitle == "") {
                    notificationService.error("Ningun nodo debe estar vacio, por favor revisar.");
                    defer.reject();
                  }
                }
                for (var i = 0; i < chartViewModel.connections.length; i++) {
                  if (chartViewModel.connections[i].class == "negation") {
                    if (jQuery.isEmptyObject(chartViewModel.connections[i].sendData)) {
                      notificationService.error("Hay relaciones que no pueden estar vacias, por favor revisar.");
                      defer.reject();
                    }
                  }
                }
                this.validateService(chartViewModel, services).catch(function () {
                  notificationService.error("Ningun nodo debe estar vacio, por favor revisar.");
                  defer.reject();
                });

                defer.resolve(true);
                return defer.promise;
              }

              this.validateFormCampaign = function (formCampaign) {
                var defer = $q.defer();
                var startDate = moment(formCampaign.startDate).utc('-0500').add(30, 'minutes').format('YYYY-MM-DD HH:mm');
                var now = moment().utc('-0500').format('YYYY-MM-DD HH:mm');
                if (formCampaign.startDate == "" || formCampaign.startDate == null) {
                  notificationService.error("El campo de fecha inicial es obligatorio.");
                  defer.reject();
                }

                if (formCampaign.gmt == "" || formCampaign.gmt == null) {
                  notificationService.error("El campo de gmt es obligatorio.");
                  defer.reject();
                }
                if (startDate < now) {
                  notificationService.error("La fecha inicial no debe ser menor a la fecha actual");
                  defer.reject();
                }
                if (formCampaign.campaignCategory == "" || typeof formCampaign.campaignCategory == "undefined" || formCampaign.campaignCategory == null) {
                  notificationService.error("El campo de categoria de campaña es obligatorio.");
                  defer.reject();
                }
                if (formCampaign.descriptionCampaign == "" || typeof formCampaign.descriptionCampaign == "undefined" || formCampaign.descriptionCampaign == null) {
                  formCampaign.descriptionCampaign = '';
                }
                if (formCampaign.nameCampaign == "" || typeof formCampaign.nameCampaign == "undefined" || typeof formCampaign.nameCampaign == null) {
                  notificationService.error("El campo de nombre de campaña es obligatorio.");
                  defer.reject();
                }


                defer.resolve(formCampaign);
                return defer.promise;
              }

              this.validateService = function (chartViewModel, services) {
                var defer = $q.defer();
                if (services.length == 0) {
                  notificationService.error("La campaña tiene servicios que no estan habilitados, por favor contactar a soporte.");
                  defer.reject();
                }
                if (services.length < 2) {
                  for (var i = 0; i < chartViewModel.nodes.length; i++) {
                    if (chartViewModel.nodes[i].theme == "service") {
                      if (services[0].service != chartViewModel.nodes[i].method) {
                        notificationService.error("La campaña tiene servicios que no estan habilitados, por favor contactar a soporte.");
                        defer.reject();
                      }
                    }
                  }
                  defer.resolve();
                } else {
                  defer.resolve();
                }
                return defer.promise;
              }

              this.countContact = function (data) {
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

              this.getScheme = function (idAutomaticCampaign) {
                var defer = $q.defer();
                var url = fullUrlBase + "api/automacamp/getscheme/" + idAutomaticCampaign;
                $http.get(url)
                        .success(function (data) {
                          defer.resolve(data);
                        })
                        .error(function (data) {
                          defer.reject(data);
                          notificationService.error(data.message);
                        });
                return defer.promise;
              }
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
          })
          .factory('setData', function () {
            var obj = {};
            var data = {};

            obj.getData = function () {
              return data;
            }

            obj.setData = function (objData) {
              data = objData;
            }

            return obj;
          })
})();
