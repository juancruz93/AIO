(function () {
  angular.module('landingpage.services', [])
          .service('RestServices', function ($http, $q, notificationService, contantLandingPage) {

            function listLanding(page, data) {
              var deferred = $q.defer();
              var url = fullUrlBase + contantLandingPage.UrlPeticion.Urls.listlanding + page;
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
            ;

            function getLandingCategory() {
              var route = fullUrlBase + contantLandingPage.UrlPeticion.Urls.getlandingcategory;
              var defer = $q.defer();
              $http.get(route)
                      .success(function (data) {
                        defer.resolve(data);
                      })
                      .error(function (res) {
                        deferred.reject(res);
                        notificationService.error(res.message);
                      });
              return defer.promise;
            }
            ;


            function createCategoryLanding(data) {
              let url = fullUrlBase + contantLandingPage.UrlPeticion.Urls.createlandingcategory;
              let defer = $q.defer();

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
            ;

            function createLandingpage(data) {
              var deferred = $q.defer();
              var url = fullUrlBase + contantLandingPage.UrlPeticion.Urls.createlandingpage;
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
            ;

            function findLanding(idLandingpage) {
              var deferred = $q.defer();
              var url = fullUrlBase + contantLandingPage.UrlPeticion.Urls.findlanding + idLandingpage;
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
            ;

            function findLandingcsc(idLandingpage) {
              var deferred = $q.defer();
              var url = fullUrlBase + contantLandingPage.UrlPeticion.Urls.findlandingcsc + idLandingpage;
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
            ;

            function editLandingpage(idLanding, data) {
              var deferred = $q.defer();
              var url = fullUrlBase + contantLandingPage.UrlPeticion.Urls.editlandingpage + idLanding;
              $http.put(url, data)
                      .success(function (res) {
                        deferred.resolve(res);
                      })
                      .error(function (res) {
                        deferred.reject(res);
                        notificationService.error(res.message);
                      });
              return deferred.promise;
            }
            ;

            function createPublicView(data, idLanding) {
              var deferred = $q.defer();
              var url = fullUrlBase + contantLandingPage.UrlPeticion.Urls.createpublicview + idLanding;
              $http.put(url, data)
                      .success(function (res) {
                        deferred.resolve(res);
                      })
                      .error(function (res) {
                        deferred.reject(res);
                        notificationService.error(res.message);
                      });
              return deferred.promise;
            }
            ;

            function findLandingCountView(idLandingpage) {
              var deferred = $q.defer();
              var url = fullUrlBase + contantLandingPage.UrlPeticion.Urls.findlandingcountview + idLandingpage;
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
            ;

            function countries() {
              var defer = $q.defer();
              var url = fullUrlBase + contantLandingPage.UrlPeticion.Urls.countries;

              $http.get(url)
                      .success(function (data) {
                        defer.resolve(data);
                      })
                      .error(function (data) {
                        defer.reject(data);
                      });

              return defer.promise;
            }
            ;

            function states(idCountry) {
              var defer = $q.defer();
              var url = fullUrlBase + contantLandingPage.UrlPeticion.Urls.states + idCountry;

              $http.get(url)
                      .success(function (data) {
                        defer.resolve(data);
                      })
                      .error(function (data) {
                        defer.reject(data);
                      });

              return defer.promise;
            }
            ;

            function cities(idState) {
              var defer = $q.defer();
              var url = fullUrlBase + contantLandingPage.UrlPeticion.Urls.cities + idState;

              $http.get(url)
                      .success(function (data) {
                        defer.resolve(data);
                      })
                      .error(function (data) {
                        defer.reject(data);
                      });

              return defer.promise;
            }
            ;

            function deleteLandingPage(idLandingPage) {
              var deferred = $q.defer();
              var url = fullUrlBase + contantLandingPage.UrlPeticion.Urls.deletelandingpage + idLandingPage;
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

            function linkGenerator(idLandingpage) {
              var deferred = $q.defer();
              var url = fullUrlBase + contantLandingPage.UrlPeticion.Urls.linkgenerator + idLandingpage;

              $http.get(url)
                      .success(function (response) {
                        deferred.resolve(response);
                      })
                      .error(function (error) {
                        deferred.reject(error);
                      });

              return deferred.promise;
            }
            
            function linkFB(idLandingpage) {
              var deferred = $q.defer();
              var url = fullUrlBase + contantLandingPage.UrlPeticion.Urls.linkfb + idLandingpage;

              $http.get(url)
                      .success(function (response) {
                        deferred.resolve(response);
                      })
                      .error(function (error) {
                        deferred.reject(error);
                      });

              return deferred.promise;
            }

            function addEmailName(data) {
              var deferred = $q.defer();
              var url = fullUrlBase + contantLandingPage.UrlPeticion.Urls.addEmailName;
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
              var url = fullUrlBase + contantLandingPage.UrlPeticion.Urls.addEmailSender;
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
            ;

            function sendMail(data) {
              var defer = $q.defer();
              var url = fullUrlBase + contantLandingPage.UrlPeticion.Urls.sendmaillandingpage;

              $http.post(url, data)
                      .success(function (data) {
                        defer.resolve(data);
                      })
                      .error(function (data) {
                        defer.reject(data);
                      });

              return defer.promise;
            }
            ;

            function hasContent(idLandingPage) {
              var defer = $q.defer();
              var url = fullUrlBase + contantLandingPage.UrlPeticion.Urls.hascontent + idLandingPage;

              $http.get(url)
                      .success(function (response) {
                        defer.resolve(response);
                      })
                      .error(function (errors) {
                        defer.reject(errors);
                      });

              return defer.promise;
            }

            function duplicate(idLandingPage) {
              var defer = $q.defer();
              var url = fullUrlBase + contantLandingPage.UrlPeticion.Urls.duplicate + idLandingPage;

              $http.get(url)
                .success(function (response) {
                  defer.resolve(response);
                })
                .error(function (error) {
                  defer.reject(error)
                })

              return defer.promise;
            }

            function savePost(data) {
              var defer = $q.defer();
              var url = fullUrlBase + contantLandingPage.UrlPeticion.Urls.savePost;

              $http.post(url, data)
                      .success(function (data) {
                        defer.resolve(data);
                      })
                      .error(function (data) {
                        defer.reject(data);
                      });

              return defer.promise;
            }
            ;

            function changeLanding(data, idLanding) {
              var defer = $q.defer();
              var url = fullUrlBase + contantLandingPage.UrlPeticion.Urls.changestatus + idLanding;

              $http.post(url, data)
                      .success(function (data) {
                        defer.resolve(data);
                      })
                      .error(function (data) {
                        defer.reject(data);
                      });

              return defer.promise;
            }

            function changeType(data, idLanding) {
              var defer = $q.defer();
              var url = fullUrlBase + contantLandingPage.UrlPeticion.Urls.changetype + idLanding;

              $http.post(url, data)
                      .success(function (data) {
                        defer.resolve(data);
                      })
                      .error(function (data) {
                        defer.reject(data);
                      });

              return defer.promise;
            }



            return {
              listLanding: listLanding,
              getLandingCategory: getLandingCategory,
              createCategoryLanding: createCategoryLanding,
              createLandingpage: createLandingpage,
              findLanding: findLanding,
              editLandingpage: editLandingpage,
              createPublicView: createPublicView,
              findLandingCountView: findLandingCountView,
              countries: countries,
              states: states,
              cities: cities,
              findLandingcsc: findLandingcsc,
              deleteLandingPage: deleteLandingPage,
              linkGenerator: linkGenerator,
              addEmailName: addEmailName,
              addEmailSender: addEmailSender,
              sendMail: sendMail,
              hasContent:hasContent,
              duplicate:duplicate,
              hasContent: hasContent,
              savePost: savePost,
              changeLanding: changeLanding,
              changeType: changeType,
              linkFB: linkFB
              
            };

          })

          .service('mailService', function ($q, $http, notificationService, contantLandingPage) {
            this.getemailsend = function () {
              var deferred = $q.defer();
              $http.get(fullUrlBase + contantLandingPage.UrlPeticion.Urls.emailsender)
                      .success(function (data) {
                        if (data.length == 0) {
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
              $http.get(fullUrlBase + contantLandingPage.UrlPeticion.Urls.emailname)
                      .success(function (data) {
                        //console.log('getemailname', data);
                        if (data.length == 0) {
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
              $http.get(fullUrlBase + contantLandingPage.UrlPeticion.Urls.getallmailtemplatelandingpage)
                      .success(function (data) {
                        //console.log(data);
                        if (data.length == 0) {
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
              //console.log(search);
              $http.post(fullUrlBase + contantLandingPage.UrlPeticion.Urls.getallmailtemplatelandingpagebyfilter, search)
                      .success(function (data) {
                        //console.log(data);
                        if (data.length == 0) {
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
              $http.get(fullUrlBase + contantLandingPage.UrlPeticion.Urls.getallmailcategory)
                      .success(function (data) {
                        //console.log(data);
                        if (data.length == 0) {
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
              var url = fullUrlBase + contantLandingPage.UrlPeticion.Urls.getcontactlist;
              $http.get(url)
                      .success(function (data) {
                        if (data.length == 0) {
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
              var url = fullUrlBase + contantLandingPage.UrlPeticion.Urls.getsegment;
              $http.get(url)
                      .success(function (data) {
                        if (data.length == 0) {
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
            var content = false;
            var advanceoptions = false;
            var shippingdate = false;

            this.setdescribe = function (incomple) {
              describe = incomple;
            };
            this.getdescribe = function () {
              return describe;
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
          .factory('notificationService', function (contantLandingPage) {
            function error(message) {
              slideOnTop(message, contantLandingPage.Misc.value, contantLandingPage.NotificationsService.Errors.error, contantLandingPage.Misc.Alerts.danger);
            }

            function success(message) {
              slideOnTop(message, contantLandingPage.Misc.value, contantLandingPage.NotificationsService.Errors.success, contantLandingPage.Misc.Alerts.success);
            }

            function warning(message) {
              slideOnTop(message, contantLandingPage.Misc.value, contantLandingPage.NotificationsService.Errors.warning, contantLandingPage.Misc.Alerts.warning);
            }

            function notice(message) {
              slideOnTop(message, contantLandingPage.Misc.value, contantLandingPage.NotificationsService.Errors.notice, contantLandingPage.Misc.Alerts.notice);
            }

            function primary(message) {
              slideOnTop(message, contantLandingPage.Misc.value, contantLandingPage.NotificationsService.Errors.primary, contantLandingPage.Misc.Alerts.primary);
            }

            return {
              error: error,
              success: success,
              warning: warning,
              notice: notice,
              primary: primary
            };
          })
          ;

})();
