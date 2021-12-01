(function () {
  angular.module('report.services', [])
    .factory('restService', ['$http', '$q', 'notificationService', 'constantReport', function ($http, $q, notificationService, constantReport) {

        function getAllReportEmail(page, search) {
          var deferred = $q.defer();
          var url = fullUrlBase + 'api/report/getallreportemail/' + page;
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

        function getAllReportSms(page, search) {
          var deferred = $q.defer();
          var url = fullUrlBase + 'api/report/getallreportsms/' + page;
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

        function getAllAccount() {
          var deferred = $q.defer();
          var url = fullUrlBase + 'api/report/getallaccountbyallied';
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

        function downloadReport(search) {
          var deferred = $q.defer();
          var url = fullUrlBase + 'api/report/downloadreport';
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

        function downloadReportSms(search) {
          var deferred = $q.defer();
          var url = fullUrlBase + 'api/report/downloadreportsms';
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
        
        function downloadReportRecharge(search,title) {
            var deferred = $q.defer();
            var url = fullUrlBase + 'api/report/downloadreportrecharge/' + title;
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
        
        function downloadReportChangePlan(search, title) {
            var deferred = $q.defer();
            var url = fullUrlBase + 'api/report/downloadreportchangeplan/' + title;
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

        function graphMail(search) {
          var deferred = $q.defer();
          var url = fullUrlBase + 'api/report/graphmail';
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

        function graphSms(search) {
          var deferred = $q.defer();
          var url = fullUrlBase + 'api/report/graphsms';
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

        function getInfoExcelSms(page, search) {
          var deferred = $q.defer();
          var url = fullUrlBase + 'api/report/getinfoexcelsms/' + page;
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

        function getInfoExcelDaySms(page, search) {
          var deferred = $q.defer();
          var url = fullUrlBase + 'api/report/getinfoexcelsmsday/' + page;
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

        function downloadSms(search) {
          var deferred = $q.defer();
          var url = fullUrlBase + 'api/report/downloadsms';
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

        function downloadSmsbyday(search, title) {
          var deferred = $q.defer();
          var url = fullUrlBase + 'api/report/downloadsmsbyday/' + title;
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

        function getInfoDetailSms(page, search) {
          var deferred = $q.defer();
          var url = fullUrlBase + 'api/report/infosms/' + page;
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

        function getInfoDetailMail(page, search) {
          var deferred = $q.defer();
          var url = fullUrlBase + 'api/report/infomail/' + page;
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

        function getSubaccount() {
          var deferred = $q.defer();
          var url = fullUrlBase + 'api/report/getallsubaccount';
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

        function getEmailUsers() {
          var deferred = $q.defer();
          var url = fullUrlBase + 'api/report/getemailusers';
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

        function dowloadReportInfoDetailSms(search, title) {
          var deferred = $q.defer();
          var url = fullUrlBase + 'api/report/dowloadreportinfodetailsms/' + title;
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

        function dowloadReportInfoDetailMail(search, title) {
          var deferred = $q.defer();
          var url = fullUrlBase + 'api/report/dowloadreportinfodetailmail/' + title;
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
        
        function getAllReportRecharge(page, search){
            var deferred = $q.defer();
            var url = fullUrlBase + 'api/report/reportrecharge/' + page;
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
        
        function getAllChangePlan(page, search){
            var deferred = $q.defer();
            var url = fullUrlBase + 'api/report/reportchangeplan/' + page
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
        
        function getAllClickMail(){
            var deferred = $q.defer();
            var url = fullUrlBase + 'api/statics/staticsmailsmessages';
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
        
        function getAllCampMail(valFilMail){
            var deferred = $q.defer();
            var url = fullUrlBase + 'api/statics/staticsmailstotalcamp/'+ valFilMail;
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
        
        function getAllCampSms(valFilSms){
            var deferred = $q.defer();
            var url = fullUrlBase + 'api/statics/staticssmssents/'+ valFilSms;
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
        
        function getAllSmsSents(){
          var deferred = $q.defer();
            var url = fullUrlBase + 'api/statics/staticssmssents';
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
        
        function getChargeInitial(){
          var deferred = $q.defer();
            var url = fullUrlBase + 'api/statics/camptotaldata';
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
        
        function getChargeDataDateMail(data){
          var deferred = $q.defer();
            var url = fullUrlBase + 'api/statics/staticsmailstotalcamp/'+data.value;
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
        
        function getChargeDataDateSms(data){
          var deferred = $q.defer();
            var url = fullUrlBase + 'api/statics/staticsmstotalcamp/'+data.value;
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
        
        function getDataTabDate(tabValue, category, timespecific, valueoption){
          var deferred = $q.defer();
            var url = fullUrlBase + 'api/statics/datetabdata/'+tabValue+'/'+timespecific+'/'+category+'/'+valueoption;
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
        
        function getFindRolServices(){
            var deferred = $q.defer();
            var url = fullUrlBase + 'api/statics/rolservices'
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
        
        function getAllMailValidation(page, stringsearch){
                var deferred = $q.defer();
              $http.post(constantReport.UrlPeticion.Urls.getAllMailValidation + '/'+page, stringsearch)
                .success(function (data) {
                  deferred.resolve(data);
                })
                .error(function (data) {
                  deferred.reject(data);
                });
                return deferred.promise;
              }
              
        function getAllMailBounced(page, stringsearch){
                var deferred = $q.defer();
              $http.post(constantReport.UrlPeticion.Urls.getAllMailBounced + '/'+page, stringsearch)
                .success(function (data) {
                  deferred.resolve(data);
                })
                .error(function (data) {
                  deferred.reject(data);
                });
                return deferred.promise;
              }
              
        function downloadMailValidation(page, search) {
          var deferred = $q.defer();
          $http.post(constantReport.UrlPeticion.Urls.downloadMailValidation+ '/'+page, search)
            .success(function (data) {
              deferred.resolve(data);
            })
            .error(function (data) {
              deferred.reject(data);
              notificationService.error(data.message);
            });
          return deferred.promise;
        }
        
        function downloadMailBounced(page, search) {
          var deferred = $q.defer();
          $http.post(constantReport.UrlPeticion.Urls.downloadMailBounced+ '/'+page, search)
            .success(function (data) {
              deferred.resolve(data);
            })
            .error(function (data) {
              deferred.reject(data);
              notificationService.error(data.message);
            });
          return deferred.promise;
        }
        
        function getAllReportSmsxemail(page, search) {
          var deferred = $q.defer();
          var url = fullUrlBase + 'api/report/getallreportsmsxemail/' + page;
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
        
        function getDataSmsChannel(page, search) {
          var deferred = $q.defer();
          $http.post(constantReport.UrlPeticion.Urls.getDataSmsChannel+ '/'+page, search)

            .success(function (data) {
              deferred.resolve(data);
            })
            .error(function (data) {
              deferred.reject(data);
              notificationService.error(data.message);
            });
          return deferred.promise;
        }
        
        function getAllSmsByDestinataries(page,nameCampaign,phoneNumber,dateInitial,dateEnd,type){
          var deferred = $q.defer();
          var url = fullUrlBase + 'api/report/getdatasmsbydestinataries/'+ page ;
          $http.post(url,{nameCampaign:nameCampaign,phoneNumber:phoneNumber,dateInitial:dateInitial,dateEnd:dateEnd,type:type })
            .success(function (data) {
              deferred.resolve(data);
            })
            .error(function (data) {
              deferred.reject(data);
              notificationService.error(data.message);
              
            });
          return deferred.promise;
        }
        
        function downloadReportSmsDestinataries(search){
          var deferred = $q.defer();
          var url = fullUrlBase + 'api/report/downloadreportsmsbydestinataries';
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
        
        return {
          getAllReportEmail: getAllReportEmail,
          getAllAccount: getAllAccount,
          downloadReport: downloadReport,
          getAllReportSms: getAllReportSms,
          downloadReportSms: downloadReportSms,
          graphMail: graphMail,
          graphSms: graphSms,
          getInfoExcelSms: getInfoExcelSms,
          downloadSms: downloadSms,
          getInfoExcelDaySms: getInfoExcelDaySms,
          downloadSmsbyday: downloadSmsbyday,
          getInfoDetailSms: getInfoDetailSms,
          getSubaccount: getSubaccount,
          dowloadReportInfoDetailSms: dowloadReportInfoDetailSms,
          getInfoDetailMail: getInfoDetailMail,
          dowloadReportInfoDetailMail: dowloadReportInfoDetailMail,
          getEmailUsers: getEmailUsers,
          getAllReportRecharge: getAllReportRecharge,
          downloadReportRecharge: downloadReportRecharge,
          getAllChangePlan: getAllChangePlan,
          downloadReportChangePlan: downloadReportChangePlan,
          getAllClickMail: getAllClickMail,
          getAllSmsSents: getAllSmsSents,
          getAllCampMail: getAllCampMail,
          getAllCampSms: getAllCampSms,
          getChargeInitial: getChargeInitial,
          getChargeDataDateMail: getChargeDataDateMail,
          getChargeDataDateSms: getChargeDataDateSms,
          getDataTabDate: getDataTabDate,
          getFindRolServices: getFindRolServices,
          getAllMailValidation: getAllMailValidation,
          downloadMailValidation: downloadMailValidation,
          getAllMailBounced: getAllMailBounced,
          downloadMailBounced: downloadMailBounced,
          getAllReportSmsxemail: getAllReportSmsxemail,
          getDataSmsChannel: getDataSmsChannel,
          getAllSmsByDestinataries: getAllSmsByDestinataries,
          downloadReportSmsDestinataries: downloadReportSmsDestinataries
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
