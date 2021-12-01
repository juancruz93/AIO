(function () {
  angular.module('statistic.services', [])
          .factory('statisticService', ['$http', '$q', 'notificationService', '$window', function ($http, $q, notificationService, $window) {

              function getAllInfoMail(idMail, type) {

                if (type == "complete") {
                  type = 1;
                } else if (type == "summary") {
                  type = 2;
                } else {
                  type = 0;
                }
                var deferred = $q.defer();
                var url = fullUrlBase + 'api/statics/getallinfomail/' + idMail + '/' + type;
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

              function infoOpen(idMail, initial) {
                var deferred = $q.defer();
                var url = fullUrlBase + 'api/statics/infoopen/' + idMail + "/" + initial;
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

              function infoClic(idMail) {
                var deferred = $q.defer();
                var url = fullUrlBase + 'api/statics/infoclic/' + idMail;
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

              function infoUnsuscribed(idMail) {
                var deferred = $q.defer();
                var url = fullUrlBase + 'api/statics/infounsuscribed/' + idMail;
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

              function infoBounced(idMail) {
                var deferred = $q.defer();
                var url = fullUrlBase + 'api/statics/infobounced/' + idMail;
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

              function infoSpam(idMail) {
                var deferred = $q.defer();
                var url = fullUrlBase + 'api/statics/infospam/' + idMail;
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
              
              function infoBuzon(idMail) {
                var deferred = $q.defer();
                var url = fullUrlBase + 'api/statics/infobuzon/' + idMail;
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

              function dataInfo(idMail, page, route, filters, type) {
                var deferred = $q.defer();
                var arr = {route: route, filters: filters, type: type};
                var url = fullUrlBase + 'api/statics/datainfo/' + idMail + "/" + page;
                $http.post(url, arr)
                        .success(function (data) {
                          deferred.resolve(data);
                        })
                        .error(function (data) {
                          deferred.reject(data);
                          notificationService.error(data.message);
                        });
                return deferred.promise;
              }

              function infoClicDetail(idMail, page, filter) {
                var deferred = $q.defer();
                var url = fullUrlBase + 'api/statics/datainfoclic/' + idMail + "/" + page;
                $http.post(url, filter)
                        .success(function (data) {
                          deferred.resolve(data);
                        })
                        .error(function (data) {
                          deferred.reject(data);
                          notificationService.error(data.message);
                        });
                return deferred.promise;
              }

              function getAllCategoryBounced() {
                var deferred = $q.defer();
                var url = fullUrlBase + 'api/statics/getallcategorybounced';
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

              function getAllDomain() {
                var deferred = $q.defer();
                var url = fullUrlBase + 'api/statics/getalldomain';
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

              function getInfoSms(idSms) {
                var deferred = $q.defer();
                var url = fullUrlBase + 'api/statics/getinfosms/' + idSms;
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

              function getDetailSms(idSms, page, phonesearch) {
                var deferred = $q.defer();
                var url = fullUrlBase + 'api/statics/getdetailsms/' + idSms + "/" + page;
                $http.post(url,{phonesearch: phonesearch} )
                        .success(function (data) {
                          deferred.resolve(data);
                        })
                        .error(function (data) {
                          deferred.reject(data);
                          notificationService.error(data.message);
                        });
                return deferred.promise;
              }

              function reportStatics(idMail, type, title) {
                var deferred = $q.defer();
                var url = fullUrlBase + 'api/statics/reportstatics/' + idMail + "/" + title;
                $http.post(url, type)
                        .success(function (data) {
                          deferred.resolve(data);
                        })
                        .error(function (data) {
                          deferred.reject(data);
                          notificationService.error(data.message);
                        });
                return deferred.promise;
              }

              function reportStaticsSms(idSms, title) { 
                
                var deferred = $q.defer();
                var url = fullUrlBase + 'api/statics/reportstaticssms/' + idSms + "/" + title;
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

              function reportStaticsSmsTwoWay(idSmsTwoway, title) {
                var deferred = $q.defer();
                var url = fullUrlBase + 'api/statics/reportstaticssmstwoway/' + idSmsTwoway + "/" + title;
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

              function getAllInfoSurvey(idMail) {
                var deferred = $q.defer();
                var url = fullUrlBase + 'api/statics/getallinfosurvey/' + idMail;
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

              function reportStaticsSurvey(idSurvey, title) {
                var deferred = $q.defer();
                var url = fullUrlBase + 'api/statics/reportstaticssurvey/' + idSurvey + "/" + title;
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

              function getAllIconfiguration(idAutomaticcampaign) {

                var deferred = $q.defer();
                var url = fullUrlBase + 'api/statics/getallconfiguration/' + idAutomaticcampaign;
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

              function getAllSmsTwoway(idSmsTwoway, page, filter) {
                var deferred = $q.defer();
                var url = fullUrlBase + 'api/smstwoway/detailsms';
                $http.post(url, {idSmsTwoway: idSmsTwoway, page: page, filter: filter})
                        .success(function (data) {
                          deferred.resolve(data);
                        })
                        .error(function (data) {
                          deferred.reject(data);
                          notificationService.error(data.message);
                        });

                return deferred.promise;
              }

              function getDetailSmsTwoWay(idSmsTwoWay) {
                var deferred = $q.defer();
                var url = fullUrlBase + 'api/smstwoway/info/' + idSmsTwoWay;
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

              function getSmsTwoWay(idSmsTwoWay) {
                var deferred = $q.defer();
                var url = fullUrlBase + 'api/smstwoway/getone/' + idSmsTwoWay;
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

              function getAutomaticCampaignByNode(data) {
//                console.log(data);
//                return;
                var defer = $q.defer();
                var url = fullUrlBase + "api/statics/getAutomaticCampaignByNode";
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

              function getInfoStaticticsSms(idSms) {
                var deferred = $q.defer();
                var url = fullUrlBase + 'api/statics/getinfosms/' + idSms;
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
                getAllInfoMail: getAllInfoMail,
                infoOpen: infoOpen,
                infoClic: infoClic,
                infoUnsuscribed: infoUnsuscribed,
                infoBounced: infoBounced,
                infoSpam: infoSpam,
                infoBuzon: infoBuzon,
                dataInfo: dataInfo,
                infoClicDetail: infoClicDetail,
                getAllCategoryBounced: getAllCategoryBounced,
                getAllDomain: getAllDomain,
                getInfoSms: getInfoSms,
                getDetailSms: getDetailSms,
                reportStatics: reportStatics,
                reportStaticsSms: reportStaticsSms,
                previewMailTemplateContent: previewMailTemplateContent,
                getAllInfoSurvey: getAllInfoSurvey,
                reportStaticsSurvey: reportStaticsSurvey,
                getAllIconfiguration: getAllIconfiguration,
                getAllSmsTwoway: getAllSmsTwoway,
                getDetailSmsTwoWay: getDetailSmsTwoWay,
                getSmsTwoWay: getSmsTwoWay,
                reportStaticsSmsTwoWay: reportStaticsSmsTwoWay,
                getAutomaticCampaignByNode: getAutomaticCampaignByNode,
                getInfoStaticticsSms: getInfoStaticticsSms
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

