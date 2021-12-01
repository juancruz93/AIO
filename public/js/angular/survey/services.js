(function () {
  angular.module('survey.services', [])
          .service('RestServices', function ($http, $q, notificationService) {

            this.listSurvey = function (page, data) {
              var deferred = $q.defer();
              var url = fullUrlBase + 'api/survey/list/' + page;
              $http.post(url, data)
                      .success(function (res) {
                        deferred.resolve(res);
                      })
                      .error(function (res) {
                        deferred.reject(res);
                        notificationService.error(res.message);
                      });
              return deferred.promise;
            };

            this.getSurveyCategory = function () {
              var deferred = $q.defer();
              var url = fullUrlBase + 'api/survey/listcategory';
              $http.get(url)
                      .success(function (res) {
                        deferred.resolve(res);
                      })
                      .error(function (res) {
                        deferred.reject(res);
                        notificationService.error(res.message);
                      });
              return deferred.promise;
            };

            this.filterSurveyCategory = function (idCategory) {
              var deferred = $q.defer();
              var url = fullUrlBase + 'api/survey/getcategory/' + idCategory;
              $http.get(url)
                      .success(function (res) {
                        deferred.resolve(res);
                      })
                      .error(function (res) {
                        deferred.reject(res);
                        notificationService.error(res.message);
                      });
              return deferred.promise;
            }

            this.createSurvey = function (data) {
              var deferred = $q.defer();
              var url = fullUrlBase + 'api/survey/createsurvey';
              $http.post(url, data)
                      .success(function (res) {
                        deferred.resolve(res);
                      })
                      .error(function (res) {
                        deferred.reject(res);
                        notificationService.error(res.message);
                      });
              return deferred.promise;
            };

            this.editSurvey = function (idSurvey, data) {
              var deferred = $q.defer();
              var url = fullUrlBase + 'api/survey/editsurvey/' + idSurvey;
              $http.put(url, data)
                      .success(function (res) {
                        deferred.resolve(res);
                      })
                      .error(function (res) {
                        deferred.reject(res);
                        notificationService.error(res.message);
                      });
              return deferred.promise;
            };

            this.findSurvey = function (idSurvey) {
              var deferred = $q.defer();
              var url = fullUrlBase + 'api/survey/findsurvey/' + idSurvey;
              $http.get(url)
                      .success(function (res) {
                        deferred.resolve(res);
                      })
                      .error(function (res) {
                        deferred.reject(res);
                        notificationService.error(res.message);
                      });
              return deferred.promise;
            };

            this.delete = function (idAccountCategory) {
              var url = fullUrlBase + "api/accountcategory/delete/" + idAccountCategory;
              var defered = $q.defer();
              $http.delete(url)
                      .success(function (data) {
                        defered.resolve(data);
                      })
                      .error(function (data) {
                        defered.reject(data);
                      });

              return defered.promise;
            };

            this.createContentSurvey = function (idSurvey, data) {
              var deferred = $q.defer();
              var url = fullUrlBase + 'api/survey/savecontent/' + idSurvey;
              $http.post(url, data)
                      .success(function (res) {
                        deferred.resolve(res);
                      })
                      .error(function (res) {
                        deferred.reject(res);
                        notificationService.error(res.message);
                      });
              return deferred.promise;
            };

            this.getSurveyContent = function (idSurvey) {
              var defered = $q.defer();
              var url = fullUrlBase + 'api/survey/getcontent/' + idSurvey;

              $http.get(url)
                      .success(function (data) {
                        defered.resolve(data);
                      })
                      .error(function (data, status) {
                        if (status == 403) {
                          defered.reject(status);
                        } else {
                          defered.reject(data);
                        }
                      })

              return defered.promise;
            }

            this.saveConfirmation = function (data) {
              var defered = $q.defer();
              var url = fullUrlBase + "api/survey/saveconf"

              $http.post(url, data)
                      .success(function (data) {
                        defered.resolve(data);
                      })
                      .error(function (data) {
                        defered.reject(data);
                      });

              return defered.promise;
            };

            this.linkGenerator = function (idSurvey) {
              var defer = $q.defer();
              var url = fullUrlBase + "api/survey/linkgene/" + idSurvey;

              $http.get(url)
                      .success(function (data) {
                        defer.resolve(data);
                      })
                      .error(function (data) {
                        defer.reject(data);
                      });

              return defer.promise;
            };

            this.savePost = function (data) {
              var defer = $q.defer();
              var url = fullUrlBase + "api/post/save";

              $http.post(url, data)
                      .success(function (data) {
                        defer.resolve(data);
                      })
                      .error(function (data) {
                        defer.reject(data);
                      });

              return defer.promise;
            };

            this.sendMail = function (data) {
              var defer = $q.defer();
              var url = fullUrlBase + "api/survey/sendmail";

              $http.post(url, data)
                      .success(function (data) {
                        defer.resolve(data);
                      })
                      .error(function (data) {
                        defer.reject(data.message);
                        notificationService.error(data.message);
                      });

              return defer.promise;
            };

            this.changeSurvey = function (data, idSurvey) {
              var defer = $q.defer();
              var url = fullUrlBase + "api/survey/changestatus/" + idSurvey;

              $http.post(url, data)
                      .success(function (data) {
                        defer.resolve(data);
                      })
                      .error(function (data) {
                        defer.reject(data);
                      });

              return defer.promise;
            }

            this.changeType = function (data, idSurvey) {
              var defer = $q.defer();
              var url = fullUrlBase + "api/survey/changetype/" + idSurvey;

              $http.post(url, data)
                      .success(function (data) {
                        defer.resolve(data);
                      })
                      .error(function (data) {
                        defer.reject(data);
                      });

              return defer.promise;
            }

            this.addEmailName = function (data) {
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

            this.addEmailSender = function (data) {
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
            };

            this.createCategorySurvey = function (data) {
              let url = fullUrlBase + "api/surveycategory/create";
              let defer = $q.defer();

              $http.post(url, data)
                      .success(function (data) {
                        defer.resolve(data);
                      })
                      .error(function (data) {
                        defer.reject(data);
                      });

              return defer.promise;
            };

            this.duplicateSurvey = function (idSurvey) {
     
              var deferred = $q.defer();
              var url = fullUrlBase + 'api/survey/duplicatesurvey/' + idSurvey;
              $http.post(url, idSurvey)
                      .success(function (data) {
                        deferred.resolve(data);
                      })
                      .error(function (data) {
                        deferred.resolve(data);
                        notificationService.error(data.message);
                      });
              return deferred.promise;
            };
            
            this.deleteSurvey = function (idSurvey) {
              var url = fullUrlBase + 'api/survey/deletesurvey/' + idSurvey;
              var deferred = $q.defer();
              console.log("SERVICES--",idSurvey);
              $http.delete(url)
                      .success(function (data) {
                        deferred.resolve(data);
                      })
                      .error(function (data) {
                        deferred.reject(data);
                        notificationService.error(data.message);
                      });
              return deferred.promise;
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
          .directive('fb', ['$FB', function ($FB) {
              return {
                restrict: "E",
                replace: true,
                template: "<div id='fb-root'></div>",
                compile: function (tElem, tAttrs) {
                  return {
                    post: function (scope, iElem, iAttrs, controller) {
                      var fbAppId = iAttrs.appId || '';

                      var fb_params = {
                        appId: iAttrs.appId || "",
                        cookie: iAttrs.cookie || true,
                        status: iAttrs.status || true,
                        xfbml: iAttrs.xfbml || true
                      };

                      // Setup the post-load callback
                      window.fbAsyncInit = function () {
                        $FB._init(fb_params);

                        if ('fbInit' in iAttrs) {
                          iAttrs.fbInit();
                        }
                      };

                      (function (d, s, id, fbAppId) {
                        var js, fjs = d.getElementsByTagName(s)[0];
                        if (d.getElementById(id))
                          return;
                        js = d.createElement(s);
                        js.id = id;
                        js.async = true;
                        js.src = "//connect.facebook.net/en_US/all.js";
                        fjs.parentNode.insertBefore(js, fjs);
                      }(document, 'script', 'facebook-jssdk', fbAppId));
                    }
                  }
                }
              };
            }])
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
            }])
          .service('mailService', function ($q, $http, notificationService) {
            this.getemailsend = function () {
              var deferred = $q.defer();
              $http.get(fullUrlBase + 'mail/emailsender/')
                      .success(function (data) {
                        //console.log('getemailsend', data);
                        if (data.length == 0) {
                          notificationService.error("No se encontro ningun email de remitente.");
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

            this.getemailname = function () {
              var deferred = $q.defer();
              $http.get(fullUrlBase + 'mail/emailname/')
                      .success(function (data) {
                        //console.log('getemailname', data);
                        if (data.length == 0) {
                          notificationService.error("No se encontro ningun nombre de remitente.");
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

            this.getallmailtemplate = function () {
              var deferred = $q.defer();
              $http.get(fullUrlBase + 'api/mailtemplate/getallmailtemplatesurvey')
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

            this.getallmailtemplatebyfilter = function (search) {
              var deferred = $q.defer();

              $http.post(fullUrlBase + 'api/mailtemplate/getallmailtemplatesurveybyfilter', search)
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

            this.getallmailcategory = function () {
              var deferred = $q.defer();
              $http.get(fullUrlBase + 'api/mailcategory/getallmailcategory')
                      .success(function (data) {
                        //console.log('getallmailcategory', data);
                        if (data.length == 0) {
                          notificationService.error("No se encontro ninguna categoria de correo.");
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

            this.getContactlist = function () {
              var deferred = $q.defer();
              var url = fullUrlBase + 'api/sendmail/getcontactlist';
              $http.get(url)
                      .success(function (data) {
                        if (data.length == 0) {
                          notificationService.error("No se encontro ninguna lista de contacto.");
                          deferred.reject(data);
                        }
                        deferred.resolve(data);
                      })
                      .error(function (data) {
                        deferred.reject(data);
                        notificationService.error(data.message);
                      });
              return deferred.promise;
            }

            this.getSegment = function () {
              var deferred = $q.defer();
              var url = fullUrlBase + 'api/sendmail/getsegment';
              $http.get(url)
                      .success(function (data) {
                        if (data.length == 0) {
                          notificationService.error("No se encontro ningun segmento.");
                          deferred.reject(data);
                        }
                        deferred.resolve(data);
                      })
                      .error(function (data) {
                        deferred.reject(data);
                        notificationService.error(data.message);
                      });
              return deferred.promise;
            }

          })
})();
