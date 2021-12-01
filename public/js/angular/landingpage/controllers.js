(function () {
  angular.module('landingpage.controllers', [])
          .filter('propsFilter', function () {
            return function (items, props) {
              var out = [];
              if (angular.isArray(items)) {
                var keys = Object.keys(props);
                items.forEach(function (item) {
                  var itemMatches = false;
                  for (var i = 0; i < keys.length; i++) {
                    var prop = keys[i];
                    var text = props[prop].toLowerCase();
                    if (item[prop].toString().toLowerCase().indexOf(text) !== -1) {
                      itemMatches = true;
                      break;
                    }
                  }

                  if (itemMatches) {
                    out.push(item);
                  }
                });
              } else {
                // Let the output be the input untouched
                out = items;
              }

              return out;
            };
          })
          .controller('main', ['$scope', 'incomplete', '$rootScope', 'wizard', 'RestServices', '$state', function ($scope, incomplete, $rootScope, wizard, RestServices, $state) {

            }])
          .controller('indexController', ['$scope', '$state', 'RestServices', 'notificationService', '$timeout', 'contantLandingPage', function ($scope, $state, RestServices, notificationService, $timeout, contantLandingPage) {

              //Universal Data
              $scope.data = {};
              //set misc
              $scope.misc = {};
              $scope.data.initial = 0;
              $scope.data.page = 1;
              $scope.misc.loader = true;
              $scope.data.filter = {};
              $scope.data.list = {};

              $scope.functions = {
                forward: function () {
                  $scope.data.initial += 1;
                  $scope.data.page += 1;
                  $scope.misc.loader = true;
                  $scope.resServices.listLanding();
                },
                fastforward: function () {
                  $scope.data.initial = ($scope.data.list.total_pages - 1);
                  $scope.data.page = $scope.data.list.total_pages;
                  $scope.misc.loader = true;
                  $scope.resServices.listLanding();
                },
                backward: function () {
                  $scope.data.initial -= 1;
                  $scope.data.page -= 1;
                  $scope.misc.loader = true;
                  $scope.resServices.listLanding();
                },
                fastbackward: function () {
                  $scope.data.initial = 0;
                  $scope.data.page = 1;
                  $scope.misc.loader = true;
                  $scope.resServices.listLanding();
                },
                listLanding: function (data) {
                  $scope.data.list = data;
                  $scope.misc.loader = false;
                },
                landingCategory: function (data) {
                  $scope.data.landingCategory = data;
                },
                confirmDelete: function (id) {
                  $scope.data.idLandingPage = id;
                  openModal();
                },
                linkgenerator: function (idLandingPage) {
                  RestServices.linkGenerator(idLandingPage).then(function (data) {
                    $scope.linklp = data.link;
                    btnCopy = document.getElementById(contantLandingPage.Misc.linkGenerator.btnCopy);
                    link = document.getElementById(contantLandingPage.Misc.linkGenerator.link);
                    btnCopy.addEventListener(contantLandingPage.Misc.linkGenerator.click, function (e) {
                      link.select();
                      if (document.execCommand(contantLandingPage.Misc.linkGenerator.Copy)) {
                        notificationService.success(contantLandingPage.Misc.linkGenerator.success);
                      } else {
                        notificationService.error(contantLandingPage.Misc.linkGenerator.error);
                      }
                      angular.element(document.querySelector(contantLandingPage.Misc.linkGenerator.querySelector)).modal(contantLandingPage.Misc.linkGenerator.modal1);
                    });
                    angular.element(document.querySelector(contantLandingPage.Misc.linkGenerator.querySelector)).modal(contantLandingPage.Misc.linkGenerator.modal2);
                  }).catch(function (data) {
                    notificationService.error(data.message);
                  });
                },
                duplicate: function () {
                  $state.reload();
                }
              }

              $scope.resServices = {
                listLanding: function () {
                  RestServices.listLanding($scope.data.initial, $scope.data.filter).then(function (res) {
                    $scope.functions.listLanding(res);
                  });
                },
                getLandingCategory: function () {
                  RestServices.getLandingCategory().then(function (data) {
                    $scope.functions.landingCategory(data);
                  });
                },
                searchcategory: function () {
                  $scope.resServices.listLanding();
                },
                getall: function () {
                  $scope.resServices.listLanding();
                  $scope.resServices.getLandingCategory();
                },
                deleteLandingPage: function () {
                  RestServices.deleteLandingPage($scope.data.idLandingPage).then(function (res) {
                    notificationService.warning(res.message);
                    closeModal();
                    $scope.resServices.listLanding();
                  });
                },
                duplicate: function (idLandingPage) {
                  $scope.misc.loader = true;
                  RestServices.duplicate(idLandingPage).then(function (response) {
                    $scope.misc.loader = false;
                    notificationService.success(response.message);
                    $scope.functions.duplicate();
                  }).catch(function (error) {
                    notificationService.error(error.message);
                  })
                }
              }

              $scope.resServices.getall();

              $scope.$watch('[data.filter.dateinitial,data.filter.dateend]', function () {
                if (typeof $scope.data.filter.dateinitial != 'undefined' & $scope.data.filter.dateinitial != '' & typeof $scope.data.filter.dateend != 'undefined' & $scope.data.filter.dateend != '') {
                  $scope.resServices.listLanding();
                }
              });

            }])
          .controller('createBasicInformationController', ['$scope', 'RestServices', 'notificationService', '$window', '$state', '$stateParams', '$rootScope', 'wizard', function ($scope, RestServices, notificationService, $window, $state, $stateParams, $rootScope, wizard) {
              $rootScope.status = true;
              $rootScope.route = $state.current.name;
              if (typeof $stateParams.idLandingPage != 'undefined' || $stateParams.idLandingPage != '') {
                $rootScope.idLandingPageGet = $stateParams.idLandingPage;
              }
              //Universal Data
              $scope.data = {};
              $scope.data.status = true;
              //set misc
              $scope.misc = {};
              $scope.misc.showNewCateg = false;
              $scope.misc.newcateg = "";

              $scope.functions = {
                saveBasicInformation: function () {
                  if ($stateParams.idLandingPage != "") {
                    $scope.resServices.editLandingpage();
                  } else {
                    $scope.resServices.createLandingpage();
                  }
                },
                getall: function () {
                  $scope.resServices.findLanding();
                  $scope.resServices.getLandingCategory();
                  $scope.resServices.countries();
                },
                showNewCateg: function () {
                  $scope.misc.showNewCateg = true;
                },
                hideNewCateg: function () {
                  $scope.misc.showNewCateg = false;
                  $scope.misc.newcateg = "";
                },
                saveNewCateg: function () {
                  let data = {
                    name: $scope.misc.newcateg,
                    status: 1
                  };
                  $scope.resServices.createCategorylanding(data);
                },
                states: function (data) {
                  $scope.data.liststates = data;
                  $scope.misc.showstate = false;
                },
                cities: function (data) {
                  $scope.data.listcities = data;
                  $scope.misc.showcity = false;
                },
                countries: function (data) {
                  $scope.data.listcountry = data;
                },
                getLandingCategory: function (res) {
                  $scope.data.landingCategory = res;
                },
                findLanding: function (res) {
                  $scope.data = res;
                },
              }

              $scope.resServices = {
                createCategorylanding: function (data) {
                  RestServices.createCategoryLanding(data).then(function (response) {
                    $scope.resServices.getLandingCategory();
                    $scope.data.idCategoryLanding = response.idLandingPageCategory;
                    $scope.functions.hideNewCateg();
                    notificationService.success(response.message);
                  }).catch(function (error) {
                    notificationService.error(error.message);
                  });
                },
                editLandingpage: function () {
                  RestServices.editLandingpage($stateParams.idLandingPage, $scope.data).then(function (res) {
                    notificationService.primary(res.message);
                    $state.go("create.content", {idLandingPage: $stateParams.idLandingPage});
                  });
                },
                createLandingpage: function () {
                  RestServices.createLandingpage($scope.data).then(function (res) {
                    notificationService.success(res.message);
                    $state.go("create.content", {idLandingPage: res.landing.idLandingPage});
                  });
                },
                states: function (idCountry) {
                  RestServices.states(idCountry).then(function (data) {
                    $scope.functions.states(data);
                  }).catch(function (data) {
                    notificationService.error(data.message);
                  });
                },
                cities: function (state) {
                  RestServices.cities(state).then(function (data) {
                    $scope.functions.cities(data);
                  }).catch(function (data) {
                    notificationService.error(data.message);
                  });
                },
                findLanding: function () {
                  if ($stateParams.idLandingPage != "") {
                    RestServices.findLanding($stateParams.idLandingPage).then(function (res) {
                      $scope.functions.findLanding(res);
                      $scope.resServices.states(res.idCountry);
                      $scope.resServices.cities(res.idState)
                    });
                  }
                },
                getLandingCategory: function () {
                  RestServices.getLandingCategory().then(function (res) {
                    $scope.functions.getLandingCategory(res);
                  });
                },
                countries: function () {
                  RestServices.countries().then(function (data) {
                    $scope.functions.countries(data);
                  }).catch(function (data) {
                    notificationService.error(data.message);
                  });
                },
              }

              $scope.functions.getall();

            }])
          .controller('contentController', ['$scope', 'RestServices', '$stateParams', '$rootScope', 'notificationService', '$filter', 'wizard', '$state', function ($scope, RestServices, $stateParams, $rootScope, notificationService, $filter, wizard, $state) {
              $rootScope.route = $state.current.name;
              if (typeof $stateParams.idLandingPage != 'undefined' || $stateParams.idLandingPage != '') {
                $rootScope.idLandingPageGet = $stateParams.idLandingPage;
              }

              (function () {
                $scope.misc = {
                  loader: true,
                  options: false,
                  content: false
                }
              })();

              $scope.functionsApi = {
                hasContent: function (idLandingPage) {
                  RestServices.hasContent(idLandingPage).then(function (response) {
                    if (response.hasContent) {
                      $scope.misc.content = true;
                      $scope.thumbnail = response.thumbnail;
                    } else {
                      $scope.misc.options = true;
                    }
                    $scope.misc.loader = false;
                  }).catch(function (error) {
                    notificationService.error(error.message);
                  });
                }
              }

              $scope.functionsApi.hasContent($stateParams.idLandingPage);

            }])
          .controller('confirmationController', ['$scope', "$state", "$rootScope", 'RestServices', 'notificationService', '$window', '$stateParams', '$state', 'contantLandingPage', function ($scope, $state, $rootScope, RestServices, notificationService, $window, $stateParams, $state, contantLandingPage) {

              $rootScope.route = $state.current.name;
              if (typeof $stateParams.idLandingPage != 'undefined' || $stateParams.idLandingPage != '') {
                $rootScope.idLandingPageGet = $stateParams.idLandingPage;
              }

              $scope.data = {};
              $scope.misc = {};
              $scope.data.sentNow = false;
              $scope.misc.valid = false;

              $scope.functions = {
                sentNow: function () {
                  if (!$scope.data.sentNow || typeof $scope.data.sentNow == "undefined") {
                    $scope.data.sentNow = true;
                  } else {
                    $scope.data.sentNow = false;
                  }
                },
                valid: function (res) {
                  document.getElementById("startDate").value = res.startDate;
                  document.getElementById("endDate").value = res.endDate;
                  $scope.data.totalview = res.totalview;

                  if (res.countview == 0) {
                    $scope.data.status = true;
                    $scope.data.sentNow = true;
                  } else {
                    $scope.data.status = false;
                    $scope.data.sentNow = false;
                    $scope.data.countview = res.countview;
                  }
                },
                validsavepubliview: function () {
                  $scope.data.startDate = document.getElementById("startDate").value;
                  $scope.data.endDate = document.getElementById("endDate").value;
                  var now = $("#startDate").val();
                  var then = $("#endDate").val();
                  $scope.functions.validbool(false);

                  if (moment(now).format(contantLandingPage.Misc.valueview.format) == moment(then).format(contantLandingPage.Misc.valueview.format)) {
                    if (moment(now).format(contantLandingPage.Misc.valueview.HH) == moment(then).format(contantLandingPage.Misc.valueview.HH)) {
                      var minutesNoew = moment(now).format(contantLandingPage.Misc.valueview.mm);
                      var minutesThen = moment(then).format(contantLandingPage.Misc.valueview.mm);
                      var subtraction = minutesThen - minutesNoew;
                      if (subtraction < contantLandingPage.Misc.valueview.valuevi) {
                        $scope.functions.validbool(true);
                        slideOnTop(contantLandingPage.Notifications.Errors.errorview, contantLandingPage.Misc.valueview.valueAler, contantLandingPage.Misc.Icons.infoSign, contantLandingPage.Misc.Alerts.danger);
                      }
                    }
                  }
                },
                validbool: function (bool) {
                  $scope.misc.valid = bool;
                }
              }

              $scope.resServices = {
                findLanding: function () {
                  if ($stateParams.idLandingPage != "") {
                    RestServices.findLandingCountView($stateParams.idLandingPage).then(function (res) {
                      $scope.functions.valid(res);
                    });
                  } else {
                    slideOnTop(contantLandingPage.Notifications.Errors.errorServices, contantLandingPage.Misc.valueview.valueAler, contantLandingPage.Misc.Icons.infoSign, contantLandingPage.Misc.Alerts.danger);
                  }
                },
                savePublicationView: function () {
                  $scope.functions.validsavepubliview();
                  if ($scope.misc.valid == false) {
                    RestServices.createPublicView($scope.data, $stateParams.idLandingPage).then(function (res) {
                      notificationService.success(res.message);
                      $state.go("create.share", {
                        idLandingPage: $stateParams.idLandingPage
                      });
                    });
                  } else {
                    $scope.functions.validbool(false);
                  }
                }
              }
              $scope.resServices.findLanding();
            }])
          .controller('shareController', ['$scope', "$state", "$rootScope", 'RestServices', 'notificationService', '$window', '$stateParams', '$state', 'contantLandingPage', '$mdDialog', 'moment', '$timeout', 'mailService', '$q', '$FB', function ($scope, $state, $rootScope, RestServices, notificationService, $window, $stateParams, $state, contantLandingPage, $mdDialog, $moment, $timeout, mailService, $q, $FB) {
              $rootScope.route = $state.current.name;
              if (typeof $stateParams.idLandingPage != 'undefined' || $stateParams.idLandingPage != '') {
                $rootScope.idLandingPageGet = $stateParams.idLandingPage;
              }

              $scope.data = {};
              $scope.misc = {
                imgEmail: fullUrlBase + contantLandingPage.Misc.img.imgEmail,
                imgFb: fullUrlBase + contantLandingPage.Misc.img.imgFb,
                imgLink: fullUrlBase + contantLandingPage.Misc.img.imgLink,

              };

              $scope.functions = {

                open: function (id) {
                  let objModal = {};
                  let modal = "";
                  switch (id) {
                    case 1:
                      objModal.templateModal = contantLandingPage.templateModalEmail;
                      objModal.controller = $scope.controllers.email;
                      objModal.locals = {};
                      objModal.varReturn = contantLandingPage.Misc.varReturn.email;
                      this.show(objModal);
                      break;
                    case 2:
                      // espacio para facebook
                      $scope.controllers.appFacebook();
                      break;
                    case 3:
                      // espacio para enlace web
                      objModal.templateModal = contantLandingPage.templateEnlace;
                      objModal.controller = $scope.controllers.link;
                      objModal.locals = {};
                      objModal.varReturn = contantLandingPage.Misc.varReturn.link;
                      this.show(objModal);
                      break;
                  }

                },
                show: function (obj) {
                  $window.scrollTo(0, 0);
                  $mdDialog.show({
                    scope: $scope.$new(),
                    template: obj.templateModal,
                    controller: obj.controller,
                    locals: obj.locals
                  })
                          .then(function (data) {
                            //Todo salio bien en el modal
                            $scope.misc.returnModal[obj.varReturn] = data;
                          }, function (err) {
                            //en caso de que cierre el modal

                          });
                },
                infoLandingPage: function (res) {
                  $scope.data.infoLandingPage = res;
                },
              }

              $scope.resServices = {
                getLandingPage: function () {
                  if ($stateParams.idLandingPage != "") {
                    RestServices.findLanding($stateParams.idLandingPage).then(function (res) {
                      $scope.functions.infoLandingPage(res);
                    });
                  }
                },
              }

              $scope.misc.returnModal = {};

              /*
               Controllers modals
               */
              $scope.controllers = {

                email: function ($scope, $mdDialog) {

                  $scope.data = {
                    mailtemplate: null,
                    mailcategory: null,
                    senderName: null,
                    senderEmail: null,
                    subject: null,
                    replyto: null,
                    listDestinatary: {id: 1, name: contantLandingPage.Misc.listDestinatary.namecontact},
                    destinatary: [],
                    link: null,

                    listSMailTemplate: [],
                    listSMailCategory: [],
                    emailname: [],
                    emailsend: [],
                    listDestinatarya: [{id: 1, name: contantLandingPage.Misc.listDestinatary.namecontact}, {id: 2, name: contantLandingPage.Misc.listDestinatary.namesegment}],
                    destinatarya: [],
                  };
                  $scope.misc = {
                    showInputName: false,
                    showSelectName: true,
                    showIconsSaveName: false,
                    showIconsName: true,
                    showInputNamea: false,
                    showSelectNamea: true,
                    showIconsSaveNamea: false,
                    showIconsNamea: true, };


                  $scope.functions = {
                    changeStatusInputName: function () {
                      if (!$scope.misc.showInputNamea) {
                        $scope.misc.showInputNamea = true;
                        $scope.misc.showSelectNamea = false;
                        $scope.misc.showIconsNamea = false;
                        $scope.misc.showIconsSaveNamea = true;
                      } else {
                        $scope.misc.showInputNamea = false;
                        $scope.misc.showSelectNamea = true;
                        $scope.misc.showIconsNamea = true;
                        $scope.misc.showIconsSaveNamea = false;
                      }
                    },
                    changeStatusInputNameemail: function () {
                      if (!$scope.misc.showInputName) {
                        $scope.misc.showInputName = true;
                        $scope.misc.showSelectName = false;
                        $scope.misc.showIconsName = false;
                        $scope.misc.showIconsSaveName = true;
                      } else {
                        $scope.misc.showInputName = false;
                        $scope.misc.showSelectName = true;
                        $scope.misc.showIconsName = true;
                        $scope.misc.showIconsSaveName = false;
                      }
                    },
                    datetimepicker: function () {
                      $("#datetimepicker,#datetimepicker1").datetimepicker({
                        format: contantLandingPage.Misc.datetimepickerformat.format,
                        language: contantLandingPage.Misc.datetimepickerformat.language,
                        startDate: new Date()
                      });
                    },
                    validatesendmail: function (now) {
                      let objMail = {};
                      objMail = $scope.data;
                      objMail.landing = $scope.landing;
                      $scope.resServices.sendMail(objMail);
                    },
                    hide: function () {
                      $mdDialog.hide();
                    },
                    closeDialog: function () {
                      $mdDialog.hide();
                    },
                    landingmanager: function () {
                      var $parent = $scope.$parent;
                      $scope.landing = angular.copy($parent.data.infoLandingPage);

                      $scope.dateMoment = {
                        initialLanding: $moment($scope.landing.startDate).utc(contantLandingPage.Misc.datetimepickerformat.time),
                        endLanding: $moment($scope.landing.endDate).utc(contantLandingPage.Misc.datetimepickerformat.time)
                      }
                    },
                    getallmailtemplate: function (data) {
                      $scope.data.listSMailTemplate = data;
                    },
                    changeSelectedMailTemplate: function (data) {
                      $scope.data.listSMailTemplate = data;
                    },
                    getNameSender: function (data, id) {
                      $scope.data.emailname = data;
                      if (typeof id !== contantLandingPage.Misc.undefined) {
                        for (i in $scope.data.emailname) {
                          if ($scope.data.emailname[i].idNameSender == id) {
                            $scope.data.senderName = $scope.data.emailname[i];
                          }
                        }
                      }
                    },
                    getEmailSender: function (data, id) {
                      $scope.data.emailsend = data;
                      if (typeof id !== contantLandingPage.Misc.undefined) {
                        for (i in $scope.data.emailsend) {
                          if ($scope.data.emailsend[i].idEmailsender == id) {
                            $scope.data.senderEmail = $scope.data.emailsend[i];
                          }
                        }
                      }
                    },
                    getMailCategory: function (data) {
                      $scope.data.listSMailCategory = data;
                    },
                    getContactList: function (data) {
                      $scope.data.destinatary = data;
                    },
                    getSegment: function (data) {
                      $scope.data.destinatarya = data;
                    },
                  }

                  $scope.resServices = {
                    getallmailtemplate: function () {
                      mailService.getallmailtemplate().then(function (data) {
                        $scope.functions.getallmailtemplate(data);
                      });
                    },
                    changeSelectedMailTemplate: function (filter) {
                      mailService.getallmailtemplatebyfilter(filter).then(function (data) {
                        $scope.functions.changeSelectedMailTemplate(data);
                      });
                    },
                    getNameSender: function (id) {
                      mailService.getemailname().then(function (data) {
                        $scope.functions.getNameSender(data, id);
                      });
                    },
                    getEmailSender: function (id) {
                      mailService.getemailsend().then(function (data) {
                        $scope.functions.getEmailSender(data, id);
                      });
                    },
                    getMailCategory: function () {
                      mailService.getallmailcategory().then(function (data) {
                        $scope.functions.getMailCategory(data);
                      });
                    },
                    getContactList: function () {
                      mailService.getContactlist().then(function (data) {
                        $scope.functions.getContactList(data);
                      });
                    },
                    getSegment: function () {
                      mailService.getSegment().then(function (data) {
                        $scope.functions.getSegment(data);
                      });
                    },
                    changeDestinatary: function (id) {
                      $scope.data.destinatarya = [];
                      switch (id.id) {
                        case 1:
                          $scope.resServices.getContactList();
                          break;
                        case 2:
                          $scope.resServices.getSegment();
                          break;
                      }
                    },
                    setLists: function (data) {
                      $scope.data.listSMailTemplate = data[0];
                      $scope.data.emailname = data[1];
                      $scope.data.emailsend = data[2];
                      $scope.data.listSMailCategory = data[3];
                      $scope.data.destinatarya = data[4];
                    },
                    init: function () {
                      let arrPromise = [];
                      arrPromise.push(mailService.getallmailtemplate());
                      arrPromise.push(mailService.getemailname());
                      arrPromise.push(mailService.getemailsend());
                      arrPromise.push(mailService.getallmailcategory());
                      arrPromise.push(mailService.getContactlist());

                      $q.all(arrPromise).then(function (data) {
                        $scope.resServices.setLists(data)
                      });
                    },
                    initInputTime: function () {
                      $scope.functions.datetimepicker();
                    },
                    validate: function (now) {
                      $scope.functions.validatesendmail(now);
                    },
                    sendMail: function (mailObj) {
                      RestServices.sendMail(mailObj).then(function (data) {
                        $scope.functions.hide();
                        notificationService.success(data.message);
                      }).catch(function (error) {
                        notificationService.error(error.message);
                      });
                    },
                    saveName: function () {
                      var data = {name: $scope.misc.senderName};
                      RestServices.addEmailName(data).then(function (res) {
                        notificationService.success(res['msg']);
                        $scope.resServices.getNameSender(res['idNameSender']);
                        $scope.functions.changeStatusInputName();
                      });
                    },
                    saveEmail: function () {
                      var data = {email: $scope.misc.senderEmail};
                      RestServices.addEmailSender(data).then(function (res) {
                        notificationService.success(res['msg']);
                        $scope.resServices.getEmailSender(res['idEmailsender']);
                        $scope.functions.changeStatusInputNameemail();
                      });
                    },
                  }

                  $timeout($scope.resServices.initInputTime, contantLandingPage.Misc.datetimepickerformat);
                  $scope.functions.landingmanager();
                  $scope.resServices.init();

                },
                link: function ($scope, $mdDialog) {
                  $scope.data = {};
                  $scope.functions = {
                    closeDialog: function () {
                      $mdDialog.hide();
                    },
                    linkGenerator: function (data) {
                      $scope.data.linksurv = data.link;
                      btnCopy = document.getElementById(contantLandingPage.Misc.linkGenerator.btnCopy);
                      link = document.getElementById(contantLandingPage.Misc.linkGenerator.link);
                      btnCopy.addEventListener(contantLandingPage.Misc.linkGenerator.click, function (e) {
                        link.select();
                        if (document.execCommand(contantLandingPage.Misc.linkGenerator.Copy)) {
                          notificationService.success(contantLandingPage.Misc.linkGenerator.success);
                        } else {
                          notificationService.error(contantLandingPage.Misc.linkGenerator.error);
                        }
                        angular.element(document.querySelector(contantLandingPage.Misc.linkGenerator.querySelector)).modal(contantLandingPage.Misc.linkGenerator.modal1);
                      });
                      angular.element(document.querySelector(contantLandingPage.Misc.linkGenerator.querySelector)).modal(contantLandingPage.Misc.linkGenerator.modal2);
                    }
                  }

                  $scope.resServices = {
                    linkGenerator: function () {
                      var $parent = $scope.$parent;
                      RestServices.linkGenerator($stateParams.idLandingPage).then(function (data) {
                        $scope.functions.linkGenerator(data);
                      }).catch(function (data) {
                        notificationService.error(data.message);
                      });
                    }
                  }

                  $scope.resServices.linkGenerator();
                },

                social: function ($scope, $mdDialog, pages, $timeout) {

                  $scope.misc = {};
                  $scope.data = {};

                  $scope.misc.pageSelected = false;

                  $scope.misc.pages = pages;

                  $scope.functions = {
                    landingmanager: function () {
                      var $parent = $scope.$parent;
                      $scope.data.landing = angular.copy($parent.data.infoLandingPage);

                      $scope.dateMoment = {
                        initialLanding: $moment($scope.data.landing.startDate),
                        endLanding: $moment($scope.data.landing.endDate)
                      }
                    },
                    selectedPage: function (page) {
                      $scope.misc.pageSelected = page;
                      $timeout($scope.functions.initInputTime, contantLandingPage.Misc.datetimepickerformat);
                    },
                    initInputTime: function () {
                      $("#datetimepicker,#datetimepicker1").datetimepicker({
                        format: contantLandingPage.Misc.datetimepickerformat.format,
                        language: contantLandingPage.Misc.datetimepickerformat.language,
                        startDate: new Date()
                      });
                    },
                    validate: function (now) {
                      let objPublish = {};
                      let objInject = {};
                      if ($scope.data.description != contantLandingPage.Misc.undefined && $scope.data.description != "") {
                        objPublish.message = $scope.data.description;
                      }

                      objPublish.link = $scope.data.linklanding;
                      

                      //objPublish.link = 'https://aio.sigmamovil.com/lp/carol/8';
                      objPublish.access_token = $scope.misc.pageSelected.access_token;
                      objInject.scheduleDate = contantLandingPage.Misc.now;
                      $scope.resServices.publish(objPublish, $scope.misc.pageSelected.id, objInject);

                    },
                    hide: function () {
                      $mdDialog.hide();
                    },
                    closeDialog: function () {
                      $mdDialog.hide();
                    },
                    linkGenerator: function (data) {
                      $scope.data.linklanding = data.link;
                    },
                    publish: function (data, id, objInject) {

                      var access_token_page = data.access_token;
                      let url = `/${id}/feed`;
                      $scope.misc.isDisabled = true;
                      $FB.api(url,
                              contantLandingPage.Misc.POST, data,
                              function (response) {

                                if (typeof response.error == contantLandingPage.Misc.undefined) {

                                  data.idPage = $scope.misc.pageSelected.id;
                                  data.idLandingPage = $scope.data.landing.idLandingPage;
                                  data.scheduledDate = objInject.scheduleDate;
                                  data.type = contantLandingPage.Misc.facebook;
                                  data.idPublish = response.id;
                                  data.description = data.message;

                                  RestServices.savePost(data).then(function (data) {

                                    let changeLanding = {status: contantLandingPage.Misc.published, type: contantLandingPage.Misc.published.public};
                                    RestServices.changeLanding(changeLanding, $scope.data.landing.idLandingPage).then(function (data) {
                                      RestServices.changeType(changeLanding, $scope.data.landing.idLandingPage).then(function (data) {

                                        $mdDialog.hide();
                                        notificationService.primary(contantLandingPage.messagepublic);
                                      }).catch(function (data) {

                                        $FB.api(
                                                contantLandingPage.Misc.bar + response.id,
                                                contantLandingPage.Misc.DELETE,
                                                {access_token: access_token_page},
                                                function (response) {
                                                  $scope.misc.isDisabled = false;
                                                  return false;
                                                }
                                        );
                                      });
                                    }).catch(function (data) {
                                      $FB.api(
                                              contantLandingPage.Misc.bar + response.id,
                                              contantLandingPage.Misc.DELETE,
                                              {access_token: access_token_page},
                                              function (response) {
                                                $scope.misc.isDisabled = false;
                                                return false;
                                              }
                                      );
                                    });
                                  });
                                } else {
                                  $mdDialog.hide();
                                  notificationService.error(contantLandingPage.errorpubliclinklanding);
                                  setTimeout("location.reload(true);", 5000);
                                }
                              });
                    },
                  };

                  $scope.misc.isDisabled = false;
                  $scope.resServices = {
                    linkGenerator: function () {
                      var $parent = $scope.$parent;
                      RestServices.linkFB($stateParams.idLandingPage).then(function (data) {
                        $scope.functions.linkGenerator(data);
                      }).catch(function (data) {
                        notificationService.error(data.message);
                      });
                    },
                    publish: function (data, id, objInject) {
                      $scope.functions.publish(data, id, objInject);
                    }
                  };

                  $scope.resServices.linkGenerator();
                  $scope.functions.landingmanager();
                },

                appFacebook: function () {

                  $scope.functions = {
                    checkPermissionFacebookPage: function (page) {
                      var defer = $q.defer();
                      var promise = defer.promise;
                      var arrPageReturn = {data: []};
                      for (var i in page) {
                        if (typeof page[i].perms.indexOf(contantLandingPage.permissionFBAdmin) != contantLandingPage.Misc.number ||
                                typeof page[i].perms.indexOf(contantLandingPage.permissionFBBasicAdmin) != contantLandingPage.Misc.number ||
                                typeof page[i].perms.indexOf(contantLandingPage.permissionFBCreateContent) != contantLandingPage.Misc.number) {
                          continue;
                        }
                        arrPageReturn.data.push(page[i]);
                      }
                      if (arrPageReturn.data.length <= 0) {
                        defer.reject(contantLandingPage.Notifications.Errors.LengthFanPage);
                      }
                      defer.resolve(arrPageReturn);
                      return promise;
                    },
                    searchSesion: function () {
                      //console.log($FB);
                    },
                    login: function (objPage) {
                      if ($FB.loaded) {
                        this.searchSesion();
                      } else {
                        $timeout($scope.functions.searchFbActive, 1000);
                      }
                      $FB.getLoginStatus(function (response) {

                        if (response.status === contantLandingPage.Misc.connected) {

                          $FB.api(contantLandingPage.Misc.accounts, function (response) {

                            if (response.error) {
                              notificationService.error(contantLandingPage.Notifications.Errors.ApiFacebook);
                              return;
                            }
                            $scope.functions.checkPermissionFacebookPage(response.data).then(function (response) {
                              //console.log(response);
                              if (!objPage) {

                                $scope.functions.showModalSelectedPage(response);

                              } else {
                                $scope.functions.setFacebook(response, objPage);
                              }
                            }).catch(function (data) {
                              notificationService.error(data);
                            });

                          });
                        } else {
                          $FB.login(function () {
                            $FB.api(contantLandingPage.Misc.accounts, function (response) {
                              if (response.error) {
                                notificationService.error(contantLandingPage.Notifications.Errors.ApiFacebook);
                                return;
                              }
                              $scope.functions.checkPermissionFacebookPage(response.data).then(function (response) {
                                //console.log(response);
                                if (!objPage) {
                                  $scope.functions.showModalSelectedPage(response);

                                } /*else {
                                 $scope.appFacebook.setFacebook(response, objPage);
                                 }*/
                              }).catch(function (data) {
                                notificationService.error(data);
                              });
                            });
                          }, {
                            scope: contantLandingPage.Misc.scope
                          });
                        }
                      });
                    },
                    getPicturesPage: function (id) {
                      var defer = $q.defer();
                      var promise = defer.promise;
                      FB.api(contantLandingPage.Misc.bar + id + contantLandingPage.Misc.picture, function (response) {
                        defer.resolve(response);
                      });
                      return promise;
                    },
                    getFanPageArr: function (data) {

                      var defer = $q.defer();
                      var promises = [];
                      var response = data;
                      angular.forEach(response, function (value) {
                        promises.push($scope.functions.getPicturesPage(value.id));
                      });
                      function setResolve(data) {
                        for (var i = 0; i < data.length; i++) {
                          response[i].picture = data[i].data.url;
                        }
                        defer.resolve(response);
                      }
                      $q.all(promises).then(function (data) {
                        setResolve(data)
                      });
                      return defer.promise;
                    },
                    showModalSelectedPage: function (data) {
                      var pages = data.data

                      $scope.functions.getFanPageArr(pages).then(function (data) {
                        document.body.scrollTop = 0;
                        $mdDialog.show({
                          scope: $scope.$new(),
                          controller: $scope.controllers.social,
                          template: contantLandingPage.templateModalPageFacebook,
                          parent: angular.element(document.body),
                          clickOutsideToClose: true,
                          locals: {
                            pages: data
                          },
                        }).then(function (response) {
                          $scope.functions.fanPageSelected = response;
                        }, function () {
                          if (!$scope.functions.fanPageSelected) {
                            $scope.functions.facebook = false;
                          }
                        });
                      });
                    },
                    setFacebook: function (response, objPage) {
                      $scope.functions.getPicturesPage(objPage.idPage).then(function (data) {
                        for (var i = 0; i < response.data.length; i++) {
                          let value = response.data[i];
                          //                      console.log(parseInt(value.id) == parseInt(objPage.idPage));
                          if (parseInt(value.id) === parseInt(objPage.idPage)) {
                            $scope.functions.facebook = true;
                            $scope.functions.fanPageSelected = value;
                            $scope.functions.fanPageSelected.picture = data.data.url;
                            $scope.functions.descriptionPublish = objPage.description;
                            //                        console.log($scope.appFacebook);
                            break;
                          }
                        }

                      });
                    }
                  }

                  $scope.functions.login(false);

                }
              }

            }])
          .controller('createController', ['$scope', 'incomplete', '$rootScope', 'wizard', 'RestServices', '$state', function ($scope, incomplete, $rootScope, wizard, RestServices, $state) {

            }])
})();