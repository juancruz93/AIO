(function () {
  angular.module('report.controllers', [])
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
          .controller('indexController', ['$scope', 'restService', 'notificationService', 'constantReport', function ($scope, restService, notificationService, constantReport) {
              $('#dateInitial').datetimepicker({
                format: 'yyyy-MM-dd',
                language: 'es'
              }).on('changeDate', function (ev) {
                $scope.tick();
                $scope.$apply();
              });
              $('#dateFinal').datetimepicker({
                format: 'yyyy-MM-dd',
                language: 'es'
              }).on('changeDate', function (ev) {
                $scope.tick();
                $scope.$apply();
              });

              $scope.tick = function () {
                $scope.search.dateInitial = $("#valuedateInitial").val();
                $scope.search.dateFinal = $("#valuedateFinal").val();
              };

              $scope.title = constantReport.Titles.reportmail;
              $scope.initial = 0;
              $scope.page = 1;
              $scope.search = {};
              $scope.accounts = [];
              $scope.forward = function () {
                $scope.initial += 1;
                $scope.page += 1;
                $scope.getAll();
              };
              $scope.fastforward = function () {
                $scope.initial = ($scope.report.total_pages - 1);
                $scope.page = $scope.report.total_pages;
                $scope.getAll();
              };
              $scope.backward = function () {
                $scope.initial -= 1;
                $scope.page -= 1;
                $scope.getAll();
              };
              $scope.fastbackward = function () {
                $scope.initial = 0;
                $scope.page = 1;
                $scope.getAll();
              };

              restService.getAllAccount().then(function (data) {
                $scope.accounts = data;
              });

              $scope.searchReport = function () {
                $scope.getAll();
              };

              $scope.cleanFilters = function () {
                $scope.search = {};
                $("#valuedateInitial").val("");
                $("#valuedateFinal").val("");
                $scope.getAll();
              };

              $scope.searchDate = function () {
                if ($scope.search.dateFinal && $scope.search.dateInitial) {
                  $scope.getAll();
                }
              };

              $scope.dowloadReport = function () {
                restService.downloadReport($scope.search).then(function () {
                  var url = fullUrlBase + 'statistic/download'
                  location.href = url;
                });
              };

              $scope.getAll = function () {
                restService.getAllReportEmail($scope.initial, $scope.search).then(function (data) {
                  $scope.report = data;
                });
              };
              $scope.getAll();
            }])
          .controller('indexControllerRecharge', ['$scope', 'restService', 'notificationService', 'constantReport', function ($scope, restService, notificationService, constantReport) {
              $('#dateInitial').datetimepicker({
                format: 'yyyy-MM-dd',
                language: 'es'
              }).on('changeDate', function (ev) {
                $scope.tick();
                $scope.$apply();
              });
              $('#dateFinal').datetimepicker({
                format: 'yyyy-MM-dd',
                language: 'es'
              }).on('changeDate', function (ev) {
                $scope.tick();
                $scope.$apply();
              });

              $scope.tick = function () {
                $scope.search.dateInitial = $("#valuedateInitial").val();
                $scope.search.dateFinal = $("#valuedateFinal").val();
              };

              $scope.title = constantReport.Titles.reportrecharge;
              $scope.initial = 0;
              $scope.page = 1;
              $scope.misc = {};
              $scope.misc.progressbar = true;
              $scope.search = {};
              $scope.accounts = [];
              $scope.forward = function () {
                $scope.initial += 1;
                $scope.page += 1;
                $scope.getAllRecharge();
              };
              $scope.fastforward = function () {
                $scope.initial = ($scope.report.total_pages - 1);
                $scope.page = $scope.report.total_pages;
                $scope.getAllRecharge();
              };
              $scope.backward = function () {
                $scope.initial -= 1;
                $scope.page -= 1;
                $scope.getAllRecharge();
              };
              $scope.fastbackward = function () {
                $scope.initial = 0;
                $scope.page = 1;
                $scope.getAllRecharge();
              };

              restService.getAllAccount().then(function (data) {
                console.log(data);
                $scope.accounts = data;
              });

              $scope.searchReport = function () {
                $scope.getAllRecharge();
              };

              $scope.searchReportRecharge = function () {
                $scope.getAllRecharge();
              };

              $scope.cleanFilters = function () {
                $scope.search = {};
                $("#valuedateInitial").val("");
                $("#valuedateFinal").val("");
                $scope.getAllRecharge();
              };

              $scope.searchDate = function () {
                if ($scope.search.dateFinal && $scope.search.dateInitial) {
                  $scope.getAllRecharge();
                }
              };
              $scope.searchDateRecharge = function () {
                if ($scope.search.dateFinal && $scope.search.dateInitial) {
                  $scope.getAllRecharge();
                }
              };
              $scope.dowloadReport = function () {
                $scope.misc.progressbar = false;
                restService.downloadReportRecharge($scope.search, $scope.title).then(function (data) {
                  var url = fullUrlBase + 'statistic/downloadexcel/' + $scope.title
                  location.href = url;
                  $scope.misc.progressbar = true;
                });
              };              
              $scope.getAllRecharge = function () {
                restService.getAllReportRecharge($scope.initial, $scope.search).then(function (data) {
                  $scope.report = data;
                  console.log($scope.report[0].items[0].history.length)
                });
              }
              $scope.getAllRecharge();
            }])
          .controller('indexControllerPlan', ['$scope', 'restService', 'notificationService', 'constantReport', function ($scope, restService, notificationService, constantReport) {
              $('#dateInitial').datetimepicker({
                format: 'yyyy-MM-dd',
                language: 'es'
              }).on('changeDate', function (ev) {
                $scope.tick();
                $scope.$apply();
              });
              $('#dateFinal').datetimepicker({
                format: 'yyyy-MM-dd',
                language: 'es'
              }).on('changeDate', function (ev) {
                $scope.tick();
                $scope.$apply();
              });

              $scope.tick = function () {
                $scope.search.dateInitial = $("#valuedateInitial").val();
                $scope.search.dateFinal = $("#valuedateFinal").val();
              };

              $scope.title = constantReport.Titles.reportchangeplan;
              $scope.initial = 0;
              $scope.page = 1;
              $scope.search = {};
              $scope.accounts = [];
              $scope.forward = function () {
                $scope.initial += 1;
                $scope.page += 1;
                $scope.getAllRecharge();
              };
              $scope.fastforward = function () {
                $scope.initial = ($scope.report.total_pages - 1);
                $scope.page = $scope.report.total_pages;
                $scope.getAllRecharge();
              };
              $scope.backward = function () {
                $scope.initial -= 1;
                $scope.page -= 1;
                $scope.getAllRecharge();
              };
              $scope.fastbackward = function () {
                $scope.initial = 0;
                $scope.page = 1;
                $scope.getAllRecharge();
              };

              restService.getAllAccount().then(function (data) {
                $scope.accounts = data;
              });

              $scope.searchReport = function () {
                $scope.getAllRecharge();
              };

              $scope.searchReportRecharge = function () {
                $scope.getAllRecharge();
              };

              $scope.cleanFilters = function () {
                $scope.search = {};
                $("#valuedateInitial").val("");
                $("#valuedateFinal").val("");
                $scope.getAllRecharge();
              };

              $scope.searchDate = function () {
                if ($scope.search.dateFinal && $scope.search.dateInitial) {
                  $scope.getAllRecharge();
                }
              };
              $scope.searchDateRecharge = function () {
                if ($scope.search.dateFinal && $scope.search.dateInitial) {
                  $scope.getAllRecharge();
                }
              };
              $scope.dowloadReport = function () {
                restService.downloadReportChangePlan($scope.search, $scope.title).then(function () {
                  var url = fullUrlBase + 'statistic/downloadexcel/' + $scope.title;
                  location.href = url;
                });
              };
              $scope.getAllRecharge = function () {
                restService.getAllChangePlan($scope.initial, $scope.search).then(function (data) {
                  $scope.report = data;
                });
              }
              $scope.getAllRecharge();
            }])
          .controller('indexControllerStadistics', ['$scope', 'restService', 'notificationService', '$state', '$location', '$window', function ($scope, restService, notificationService, $state, $location, $window) {

              $scope.today = function () {
                $scope.dt = new Date();
              };
              $scope.today();

              $scope.clear = function () {
                $scope.dt = null;
              };

              $scope.inlineOptions = {
                customClass: getDayClass,
                minDate: new Date(),
                showWeeks: true
              };

              $scope.dateOptions = {
                formatYear: 'yy',
                maxDate: new Date(),
                minDate: new Date(),
                startingDay: 1
              };

              function disabled(data) {
                var date = data.date,
                        mode = data.mode;
                return mode === 'day' && (date.getDay() === 0 || date.getDay() === 6);
              }

              $scope.toggleMin = function () {
                $scope.inlineOptions.minDate = $scope.inlineOptions.minDate ? null : new Date();
                $scope.dateOptions.minDate = $scope.inlineOptions.minDate;
              };

              $scope.toggleMin();

              $scope.openDatePickerI = function ($index) {
                if ($scope.listDropMail[$index].openedInital) {
                  $scope.listDropMail[$index].openedInital = false;
                } else {
                  $scope.listDropMail[$index].openedInital = true;
                }
              }

              $scope.openDatePickerF = function ($index) {
                if ($scope.listDropMail[$index].openedeFinal) {
                  $scope.listDropMail[$index].openedeFinal = false;
                } else {
                  $scope.listDropMail[$index].openedeFinal = true;
                }
              }

              $scope.setDate = function (year, month, day) {
                $scope.dt = new Date(year, month, day);
              };

              $scope.formats = ['dd-MMMM-yyyy', 'yyyy/MM/dd', 'dd.MM.yyyy', 'shortDate'];
              $scope.format = $scope.formats[0];
              $scope.altInputFormats = ['M!/d!/yyyy'];

              var tomorrow = new Date();
              tomorrow.setDate(tomorrow.getDate() + 1);
              var afterTomorrow = new Date();
              afterTomorrow.setDate(tomorrow.getDate() + 1);
              $scope.events = [
                {
                  date: tomorrow,
                  status: 'full'
                },
                {
                  date: afterTomorrow,
                  status: 'partially'
                }
              ];

              function getDayClass(data) {
                var date = data.date,
                        mode = data.mode;
                if (mode === 'day') {
                  var dayToCheck = new Date(date).setHours(0, 0, 0, 0);

                  for (var i = 0; i < $scope.events.length; i++) {
                    var currentDay = new Date($scope.events[i].date).setHours(0, 0, 0, 0);

                    if (dayToCheck === currentDay) {
                      return $scope.events[i].status;
                    }
                  }
                }
                return '';
              }

              $scope.currentNavItem = 'page1';
              $scope.listDropMail = [];
              $scope.listDropSms = [];
              $scope.listFilters = [
                {'title': 'MAIL', 'class': 'glyphicon glyphicon-send', 'value': 1, 'nameId': 'divListOptionMail', 'category': 'm'},
                {'title': 'SMS', 'class': 'glyphicon glyphicon-phone', 'value': 2, 'nameId': 'divListOptionSms', 'category': 's'}
              ];

              $scope.tabs = [
                {title: 'Mes actual', value: '1', 'pos': 0, 'timeFindSpecific': 'd', 'valueoption': 1},
                {title: 'Mes Anterior', value: '2', 'pos': 1, 'timeFindSpecific': 'd', 'valueoption': 1},
                {title: 'Último año', value: '3', 'pos': 2, 'timeFindSpecific': 'M', 'valueoption': 1},
                {title: 'Hoy', value: '4', 'pos': 3, 'timeFindSpecific': 'H', 'valueoption': 2},
                {title: 'Ayer', value: '5', 'pos': 4, 'timeFindSpecific': 'H', 'valueoption': 2},
                {title: 'Días del mes', value: '6', 'pos': 5, 'timeFindSpecific': 'd', 'valueoption': 2},
                {title: 'Hoy', value: '7', 'pos': 6, 'timeFindSpecific': 'H', 'valueoption': 3},
                {title: 'Ayer', value: '8', 'pos': 7, 'timeFindSpecific': 'H', 'valueoption': 3},
                {title: 'Días del mes', value: '9', 'pos': 8, 'timeFindSpecific': 'd', 'valueoption': 3},
                {title: 'Hoy', value: '10', 'pos': 9, 'timeFindSpecific': 'H', 'valueoption': 4},
                {title: 'Ayer', value: '11', 'pos': 10, 'timeFindSpecific': 'H', 'valueoption': 4},
                {title: 'Días del mes', value: '12', 'pos': 11, 'timeFindSpecific': 'd', 'valueoption': 4},
                {title: 'Hoy', value: '13', 'pos': 12, 'timeFindSpecific': 'H', 'valueoption': 5},
                {title: 'Ayer', value: '14', 'pos': 13, 'timeFindSpecific': 'H', 'valueoption': 5},
                {title: 'Días del mes', value: '15', 'pos': 14, 'timeFindSpecific': 'd', 'valueoption': 5},
                {title: 'Hoy', value: '16', 'pos': 15, 'timeFindSpecific': 'H', 'valueoption': 6},
                {title: 'Ayer', value: '17', 'pos': 16, 'timeFindSpecific': 'H', 'valueoption': 6},
                {title: 'Días del mes', value: '18', 'pos': 17, 'timeFindSpecific': 'd', 'valueoption': 6}
              ];

              $scope.dayAgo = new Date();
              $scope.dayDateAgo = moment().subtract(1, 'Days').toDate();
              $scope.lisFiltersOpsMail = [
                {'title': 'Campañas enviadas de email', 'drag': true, 'value': 1, 'class': 'glyphicon glyphicon-send', 'category': 'm', 'idDateInitial': 'dateinitialm1', 'idDateFinal': 'datefinalm1', 'openedInital': false, 'openedeFinal': false, 'valueDateInitial': "", 'valueDateFinal': "", 'modelDataIni': {'dateIni': $scope.dayDateAgo, 'dateFin': new Date()}, 'tabsDate': $scope.tabs},
                {'title': 'Envíos de mail por día', 'drag': true, 'value': 2, 'class': 'glyphicon glyphicon-phone', 'category': 'm', 'idDateInitial': 'dateinitialm2', 'idDateFinal': 'datefinalm2', 'openedInital': false, 'openedeFinal': false, 'valueDateInitial': "", 'valueDateFinal': "", 'modelDataIni': {'dateIni': $scope.dayDateAgo, 'dateFin': new Date()}, 'tabsDate': $scope.tabs},
                {'title': 'Aperturas de Mail por día', 'drag': true, 'value': 3, 'class': 'glyphicon glyphicon-phone', 'category': 'm', 'idDateInitial': 'dateinitialm3', 'idDateFinal': 'datefinalm3', 'openedInital': false, 'openedeFinal': false, 'valueDateInitial': "", 'valueDateFinal': "", 'modelDataIni': {'dateIni': $scope.dayDateAgo, 'dateFin': new Date()}, 'tabsDate': $scope.tabs},
                {'title': 'Clics de Mail por día', 'drag': true, 'value': 4, 'class': 'glyphicon glyphicon-phone', 'category': 'm', 'idDateInitial': 'dateinitialm3', 'idDateFinal': 'datefinalm3', 'openedInital': false, 'openedeFinal': false, 'valueDateInitial': "", 'valueDateFinal': "", 'modelDataIni': {'dateIni': $scope.dayDateAgo, 'dateFin': new Date()}, 'tabsDate': $scope.tabs},
                {'title': 'Sms enviados por día', 'drag': true, 'value': 5, 'class': 'glyphicon glyphicon-send', 'category': 's', 'idDateInitial': 'dateinitials1', 'idDateFinal': 'datefinals1', 'openedInital': false, 'openedeFinal': false, 'valueDateInitial': "", 'valueDateFinal': "", 'modelDataIni': {'dateIni': $scope.dayDateAgo, 'dateFin': new Date()}, 'tabsDate': $scope.tabs},
                {'title': 'Campañas de sms por día', 'drag': true, 'value': 6, 'class': 'glyphicon glyphicon-send', 'category': 's', 'idDateInitial': 'dateinitials2', 'idDateFinal': 'datefinals2', 'openedInital': false, 'openedeFinal': false, 'valueDateInitial': "", 'valueDateFinal': "", 'modelDataIni': {'dateIni': $scope.dayDateAgo, 'dateFin': new Date()}, 'tabsDate': $scope.tabs}
//                {'title': 'Sms recibidos', 'drag': true, 'value': 7, 'class': 'glyphicon glyphicon-send', 'category': 's', 'idDateInitial':'dateinitials3', 'idDateFinal':'datefinals3', 'openedInital': false, 'openedeFinal': false, 'valueDateInitial': "", 'valueDateFinal': "", 'modelDataIni':{'dateIni': $scope.dayDateAgo, 'dateFin': new Date()}, 'tabsDate':$scope.tabs}
              ];

              $scope.selectTab = function (tabValue, category, filMail, posTab, timespecific, valueoption, titleOption) {
                restService.getDataTabDate(tabValue, category, timespecific, valueoption).then(function (data) {
                  $scope.getTotalCampMails(data, category, filMail, posTab, titleOption, timespecific);
                });
              }

              $scope.lisFiltersSms = [
                {'title': 'Campañas Totales sms', 'drag': true, 'value': 4, 'class': 'glyphicon glyphicon-send', 'category': 's'}
              ];

              $scope.countTotalMails = 0;
              $scope.countTotaEmailLinks = 0;
              $scope.countTotalUniqueClicks = 0;

              $scope.optionsList = {
                startCallback: function (event, ui, title) {

                },
                onOut: function (event, ui) {
                  if (ui.draggable.scope() != null) {
                    /*document.getElementById("divChooseOptions").style.backgroundColor = "white";
                     document.getElementById("divChooseOptions").style.border = "none";*/
                  }
                },
                onDrop: function (event, ui, obtAction, $index) {
                  if (ui.draggable.scope() != null) {
                    /*document.getElementById("divChooseOptions").style.backgroundColor = "white";
                     document.getElementById("divChooseOptions").style.border = "none";*/

                    var obtjDrop = ui.draggable.scope().dndDragItem;
                    if (obtjDrop.category == "m") {
                      if (obtjDrop.value == 1) {
                        $scope.getAllTotalCampMails(obtjDrop.category, obtjDrop.value);
                      } else if (obtjDrop.value == 2) {
                        $scope.getAllClickMails(obtjDrop.category, obtjDrop.value);
                      } else if (obtjDrop.value == 3) {
                        $scope.getAllLinksCampMails(obtjDrop.category, obtjDrop.value);
                      }
                      $scope.actionAutoHeightDivChar();
                    } else if (obtjDrop.category == "s") {
                      if (obtjDrop.value == 4) {
                        $scope.getTotalSms(obtjDrop.category, obtjDrop.value);
                        //$scope.subscribe(obtjDrop);
                      } else if (obtjDrop.value == 5) {
                        $scope.getSentSms(obtjDrop.category, obtjDrop.value);
                      } else if (obtjDrop.value == 6) {
                        $scope.getSentTotalSms(obtjDrop.category, obtjDrop.value);
                      }
                      $scope.actionAutoHeightDivChar();
                    }
                  }
                },
                onOver: function (event, ui, title, $index, item) {
                  if (ui.draggable.scope() != null) {
                    /*document.getElementById("divChooseOptions").style.backgroundColor = "#333333";
                     document.getElementById("divChooseOptions").style.border = "dashed white";*/
                  }
                }
              };

              $scope.actionDroppableOptions = {
                startCallback: function (event, ui, title) {

                },
                onOut: function (event, ui) {
                  if (ui.draggable.scope() != null) {
                    document.getElementById("divOptions").style.backgroundColor = "#ff6e00";
                    document.getElementById("divOptions").style.border = "none";
                  }
                },
                dropCallback: function (evt, ui) {

                },
                onOver: function (event, ui, title, $index, item) {
                  if (ui.draggable.scope() != null) {
                    document.getElementById("divOptions").style.backgroundColor = "#333333";
                    document.getElementById("divOptions").style.border = "dashed white";
                  }
                },
                onDrop: function (event, ui, obtAction, $index) {
                  if (ui.draggable.scope() != null) {
                    var obtjDrop = ui.draggable.scope().dndDragItem;
                    document.getElementById("divOptions").style.backgroundColor = "#ff6e00";
                    document.getElementById("divOptions").style.border = "none";
                  }
                }
              }

              $scope.actionAutoHeightDivChar = function () {
                document.getElementById("divOptions").style.height += "'" + $scope.divHeghtContentChar + "px;'";
                document.getElementById("divChooseOptions").style.height += "'" + $scope.divHeghtContentChar + "px;'";
              }

              $scope.chargeInitialChar = function () {
                listoption = $scope.lisFiltersOpsMail[1];
                listoption2 = $scope.lisFiltersOpsMail[2];
                $scope.lisFiltersOpsMail.splice(1, 1);
                $scope.lisFiltersOpsMail.splice(1, 1);
                $scope.subscribe(listoption);
                $scope.subscribe(listoption2);
              }

              $scope.subscribe = function (listoption) {
                if (listoption.category == 'm') {

                  for (var i = 0; i < $scope.lisFiltersOpsMail.length; i++) {
                    if ($scope.lisFiltersOpsMail[i].value == listoption.value) {
                      $scope.lisFiltersOpsMail.splice(i, 1);
                    }
                  }
                  $scope.listDropMail.push(listoption);
                } else if (listoption.category == 's') {
                  for (var i = 0; i < $scope.lisFiltersOpsMail.length; i++) {
                    if ($scope.lisFiltersOpsMail[i].value == listoption.value) {
                      $scope.lisFiltersOpsMail.splice(i, 1);
                    }
                  }
                  $scope.listDropMail.push(listoption);
                }
              }

              $scope.unsubscribe = function (item) {
                if (item.category == 'm') {
                  for (var i = 0; i < $scope.listDropMail.length; i++) {
                    if ($scope.listDropMail[i].value == item.value) {
                      $scope.listDropMail.splice(i, 1);
                      $scope.arrayDataCharMail.splice(i, 1);
                      $scope.arrayTitleCharMail.splice(i, 1);
                    }
                  }
                  $scope.lisFiltersOpsMail.push(item);
                } else if (item.category == 's') {
                  for (var i = 0; i < $scope.listDropMail.length; i++) {
                    if ($scope.listDropMail[i].value == item.value) {
                      $scope.listDropMail.splice(i, 1);
                      $scope.arrayDataCharSms.splice(i, 1);
                      $scope.arrayTitleCharSms.splice(i, 1);
                    }
                  }
                  $scope.lisFiltersOpsMail.push(item);
                }
              }

              $scope.arrayTitleCharMail = [];
              $scope.arrayDataCharMail = [];
              $scope.titleEjeY = "";
              $scope.titleEstatics = "";
              $scope.typeChart = "column";

              $scope.countTotalCampMail = 0;
              $scope.titleTotalMail = "";
              $scope.timeDetailX = "Horas";
              $scope.getTotalCampMails = function (data, valFilCategory, valFilMail, posTab, titleOption, timespecific) {
                $scope.countTotalCampMail = data.info[0]['totalLinkCamp'];
                $scope.titleTotalMail = "Total de Campañas";
                $scope.titleEjeY = "# Campañas";
                $scope.titleEstatics = titleOption;
                $scope.arrayDataCharMail = [];
                $scope.arrayTitleCharMail = [];
                $scope.arrayDataSent = [];
                $scope.arrayTitleMail = [];
                $scope.arrayDataMessages = [];
                $scope.arrayDataTotalMessages = [];
                $scope.totalMessagesSent = 0;

                $scope.arrayDataFunction = [];

                if (timespecific == 'H') {
                  $scope.timeDetailX = "Horas";
                } else if (timespecific == 'd') {
                  $scope.timeDetailX = "Días";
                } else if (timespecific == 'M') {
                  $scope.timeDetailX = "Meses";
                }

                if (data.info[0].dataLinks.length > 0) {
                  $.each(data.info[0].dataLinks, function (index, value) {
                    $scope.arrayTitleCharMail.push(value['timeSpecific']);
                    $scope.arrayDataFunction.push(value['timeSpecific'], parseInt(value['messagesSent']));
                    $scope.arrayDataMessages.push($scope.arrayDataFunction);
                    $scope.arrayDataFunction = [];
                  });
                  $scope.arrayDataCharMail.push(
                          {
                            colorByPoint: true,
                            showInLegend: false,
                            name: 'Envios de Mail',
                            data: $scope.arrayDataMessages
                          }
                  );
                } else {
                  $scope.titleEstatics = "No se encontrarón campañas en estas fechas";
                }
                $scope.drawGraphicsMail(valFilCategory, valFilMail, posTab);
              }

              $scope.getAllTotalCampMails = function (valFilCategory, valFilMail) {
                restService.getAllCampMail(valFilMail).then(function (data) {
                  $scope.countTotalCampMail = data.info[0]['totalCampSubAccount'];
                  $scope.titleTotalMail = "Total de Campañas";
                  $scope.titleEjeY = "Mails";
                  $scope.titleEstatics = "Estadísticas de Mail";
                  $scope.arrayDataCharMail = [];
                  $scope.arrayTitleCharMail = [];
                  $scope.arrayTitleCharMail.push($scope.titleTotalMail);
                  $scope.arrayDataSent = [];
                  $scope.arrayTitleMail = [];
                  $scope.totalMessagesSent = 0;
                  if (data.info[0].dataMail.length > 0) {
                    $.each(data.info[0].dataMail, function (index, value) {
                      $scope.totalMessagesSent += parseInt(value['messagesSent']);
                      $scope.arrayDataCharMail.push({name: "Campaña " + (parseInt(index) + 1), data: [parseInt(value['messagesSent'])]});
                    });
                    $scope.arrayDataCharMail.push({name: "Total Mails enviados", data: [parseInt($scope.totalMessagesSent)]});
                  } else {
                    $scope.titleEstatics = "No se encontrarón campañas en estas fechas";
                  }
                  $scope.drawGraphicsMail(valFilCategory, valFilMail);
                });
              }

              $scope.countTotalLinkCamp = 0;
              $scope.titleTotalLinksCamp = "";
              $scope.getAllLinksCampMails = function (valFilCategory, valFilMail) {
                restService.getAllCampMail(valFilMail).then(function (data) {
                  $scope.countTotalLinkCamp = 0;
                  $scope.titleTotalLinksCamp = "Links";
                  $scope.titleEjeY = "Cantidad de Links en la campaña";
                  $scope.titleEstatics = "Estadísticas de Links";
                  $scope.arrayDataCharMail = [];
                  $scope.arrayTitleCharMail = [];
                  $scope.arrayTitleCharMail.push($scope.titleTotalLinksCamp);
                  if (data.info[0].dataLinks.length > 0) {
                    $.each(data.info[0].dataLinks, function (index, value) {
                      $scope.countTotalLinkCamp += parseInt(value['NUM_LINKS_MAIL']);
                      $scope.arrayDataCharMail.push({name: "Campaña " + (parseInt(index) + 1), data: [parseInt(value['NUM_LINKS_MAIL'])]});
                    });
                    $scope.arrayDataCharMail.push({name: "Total Links", data: [parseInt($scope.countTotalLinkCamp)]});
                  } else {
                    $scope.titleEstatics = "No se encontrarón campañas en estas fechas";
                  }
                  $scope.drawGraphicsMail(valFilCategory, valFilMail);
                });
              }

              $scope.getAllClickMails = function (valFilCategory, valFilMail) {
                restService.getAllCampMail(valFilMail).then(function (data) {
                  $scope.titleTotalMail = "Total de Clics unicos";
                  $scope.titleEjeY = "Cantidad de Clics unicos por mail";
                  $scope.titleEstatics = "Estadísticas de clics por Link";
                  $scope.arrayDataCharMail = [];
                  $scope.arrayTitleCharMail = [];
                  $scope.arrayTitleCharMail.push($scope.titleTotalMail);
                  if (data.info[0].dataClickLink.length > 0) {
                    $.each(data.info[0].dataClickLink, function (index, value) {
                      $scope.arrayDataCharMail.push({name: "Campaña " + (parseInt(index) + 1), data: [parseInt(value['uniqueClicks'])]});
                    });
                  } else {
                    $scope.titleEstatics = "No se encontrarón campañas en estas fechas";
                  }
                  $scope.drawGraphicsMail(valFilCategory, valFilMail);
                });
              }
              var chartMail = null;
              $scope.drawGraphicsMail = function (valFilCategory, valFilMail, posTab) {
                chartMail =
                        {
                          chart: {
                            type: $scope.typeChart,
                            width: 464,
                            height: 350,
                            /*margin: 0,*/
                            defaultSeriesType: 'areaspline',
                            backgroundColor: {
                              linearGradient: [0, 0, 500, 500],
                              stops: [
                                [0, 'rgb(255, 255, 255)'],
                                [1, 'rgb(240, 240, 255)']
                              ]
                            },
                          },
                          plotOptions: {
                            series: {
                              stacking: 'normal'
                            }
                          },
                          credits: {
                            enabled: false
                          },
                          title: {
                            text: $scope.titleEstatics,
                            style: {
                              color: '#000',
                              font: 'bold 16px "Trebuchet MS", Verdana, sans-serif'
                            }
                          },
                          xAxis: {
                            title: {
                              enabled: true,
                              text: 'Tiempo en ' + $scope.timeDetailX,
                              style: {
                                fontWeight: 'normal'
                              }
                            },
                            categories: $scope.arrayTitleCharMail
                          },
                          yAxis: {
                            title: {
                              text: $scope.titleEjeY
                            }
                          },
                          series: $scope.arrayDataCharMail,
                          colors: ['#058DC7', '#50B432', '#ED561B', '#DDDF00', '#24CBE5', '#64E572',
                            '#FF9655', '#FFF263', '#6AF9C4'],
                          subtitle: {
                            style: {
                              color: '#666666',
                              font: 'bold 12px "Trebuchet MS", Verdana, sans-serif'
                            }
                          },
                          legend: {
                            itemStyle: {
                              font: '9pt Trebuchet MS, Verdana, sans-serif',
                              color: 'black'
                            },
                            itemHoverStyle: {
                              color: 'gray'
                            }
                          },
                          exporting: {
                            buttons: {
                              contextButton: {
                                menuItems: [
                                  {
                                    text: 'Exportar to PDF',
                                    onclick: function () {
                                      var result = this.exportChart({
                                        type: 'application/pdf'
                                      });
                                    }
                                  },
                                  {
                                    text: 'Exportar to JPEG',
                                    onclick: function () {
                                      var result = this.exportChart({
                                        type: 'image/jpeg'
                                      });
                                    }
                                  },
                                  {
                                    text: 'Exportar to PNG',
                                    onclick: function () {
                                      var result = this.exportChart();
                                    },
                                    separator: false
                                  },
                                  {
                                    text: 'Estilo Normal',
                                    onclick: function () {
                                      var result = $scope.themeDefault(valFilMail.category + valFilMail.value + valFilMail.tabsDate[posTab].pos);
                                    },
                                    separator: false
                                  },
                                  {
                                    text: 'Estilo Sand',
                                    onclick: function () {
                                      var result = $scope.themeChartSand(valFilMail.category + valFilMail.value + valFilMail.tabsDate[posTab].pos);
                                    },
                                    separator: false
                                  },
                                  {
                                    text: 'Estilo Grid',
                                    onclick: function () {
                                      var result = $scope.themeChartGridLight(valFilMail.category + valFilMail.value + valFilMail.tabsDate[posTab].pos);
                                    },
                                    separator: false
                                  },
                                ]
                              }
                            }
                          }
                        }
                chartMail.chart.renderTo = 'divCharFil' + valFilMail.category + valFilMail.value + valFilMail.tabsDate[posTab].pos;
                chartMail.chart.type = 'column';
                var chart1 = new Highcharts.Chart(chartMail);
              }

              $scope.themeDefault = function (nameChart) {
                $scope.chartToChange = angular.element(document.getElementById("divCharFil" + nameChart)).highcharts();
                $scope.chartToChange.theme = {
                  colors: ['#058DC7', '#50B432', '#ED561B', '#DDDF00', '#24CBE5', '#64E572',
                    '#FF9655', '#FFF263', '#6AF9C4'],
                  chart: {
                    backgroundColor: {
                      linearGradient: [0, 0, 500, 500],
                      stops: [
                        [0, 'rgb(255, 255, 255)'],
                        [1, 'rgb(240, 240, 255)']
                      ]
                    },
                  },
                  title: {
                    style: {
                      color: '#000',
                      font: 'bold 16px "Trebuchet MS", Verdana, sans-serif'
                    }
                  },
                  subtitle: {
                    style: {
                      color: '#666666',
                      font: 'bold 12px "Trebuchet MS", Verdana, sans-serif'
                    }
                  },

                  legend: {
                    itemStyle: {
                      font: '9pt Trebuchet MS, Verdana, sans-serif',
                      color: 'black'
                    },
                    itemHoverStyle: {
                      color: 'gray'
                    }
                  }
                }
                Highcharts.setOptions($scope.chartToChange.theme);
                $scope.chartToChange.update($scope.chartToChange.theme);
              }

              $scope.themeDarkUnica = function (nameChart) {
                $scope.chartToChange = angular.element(document.getElementById("divCharFil" + nameChart)).highcharts();
                $scope.chartToChange.theme = {
                  colors: ['#2b908f', '#90ee7e', '#f45b5b', '#7798BF', '#aaeeee', '#ff0066', '#eeaaee',
                    '#55BF3B', '#DF5353', '#7798BF', '#aaeeee'],
                  chart: {
                    backgroundColor: {
                      linearGradient: {x1: 0, y1: 0, x2: 1, y2: 1},
                      stops: [
                        [0, '#2a2a2b'],
                        [1, '#3e3e40']
                      ]
                    },
                    style: {
                      fontFamily: '\'Unica One\', sans-serif'
                    },
                    plotBorderColor: '#606063'
                  },
                  title: {
                    style: {
                      color: '#E0E0E3',
                      textTransform: 'uppercase',
                      fontSize: '20px'
                    }
                  },
                  subtitle: {
                    style: {
                      color: '#E0E0E3',
                      textTransform: 'uppercase'
                    }
                  },
                  xAxis: {
                    gridLineColor: '#707073',
                    labels: {
                      style: {
                        color: '#E0E0E3'
                      }
                    },
                    lineColor: '#707073',
                    minorGridLineColor: '#505053',
                    tickColor: '#707073',
                    title: {
                      style: {
                        color: '#A0A0A3'

                      }
                    }
                  },
                  yAxis: {
                    gridLineColor: '#707073',
                    labels: {
                      style: {
                        color: '#E0E0E3'
                      }
                    },
                    lineColor: '#707073',
                    minorGridLineColor: '#505053',
                    tickColor: '#707073',
                    tickWidth: 1,
                    title: {
                      style: {
                        color: '#A0A0A3'
                      }
                    }
                  },
                  tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.85)',
                    style: {
                      color: '#F0F0F0'
                    }
                  },
                  plotOptions: {
                    series: {
                      dataLabels: {
                        color: '#B0B0B3'
                      },
                      marker: {
                        lineColor: '#333'
                      }
                    },
                    boxplot: {
                      fillColor: '#505053'
                    },
                    candlestick: {
                      lineColor: 'white'
                    },
                    errorbar: {
                      color: 'white'
                    }
                  },
                  legend: {
                    itemStyle: {
                      color: '#E0E0E3'
                    },
                    itemHoverStyle: {
                      color: '#FFF'
                    },
                    itemHiddenStyle: {
                      color: '#606063'
                    }
                  },
                  credits: {
                    style: {
                      color: '#666'
                    }
                  },
                  labels: {
                    style: {
                      color: '#707073'
                    }
                  },

                  drilldown: {
                    activeAxisLabelStyle: {
                      color: '#F0F0F3'
                    },
                    activeDataLabelStyle: {
                      color: '#F0F0F3'
                    }
                  },

                  navigation: {
                    buttonOptions: {
                      symbolStroke: '#DDDDDD',
                      theme: {
                        fill: '#505053'
                      }
                    }
                  },

                  // scroll charts
                  rangeSelector: {
                    buttonTheme: {
                      fill: '#505053',
                      stroke: '#000000',
                      style: {
                        color: '#CCC'
                      },
                      states: {
                        hover: {
                          fill: '#707073',
                          stroke: '#000000',
                          style: {
                            color: 'white'
                          }
                        },
                        select: {
                          fill: '#000003',
                          stroke: '#000000',
                          style: {
                            color: 'white'
                          }
                        }
                      }
                    },
                    inputBoxBorderColor: '#505053',
                    inputStyle: {
                      backgroundColor: '#333',
                      color: 'silver'
                    },
                    labelStyle: {
                      color: 'silver'
                    }
                  },

                  navigator: {
                    handles: {
                      backgroundColor: '#666',
                      borderColor: '#AAA'
                    },
                    outlineColor: '#CCC',
                    maskFill: 'rgba(255,255,255,0.1)',
                    series: {
                      color: '#7798BF',
                      lineColor: '#A6C7ED'
                    },
                    xAxis: {
                      gridLineColor: '#505053'
                    }
                  },

                  scrollbar: {
                    barBackgroundColor: '#808083',
                    barBorderColor: '#808083',
                    buttonArrowColor: '#CCC',
                    buttonBackgroundColor: '#606063',
                    buttonBorderColor: '#606063',
                    rifleColor: '#FFF',
                    trackBackgroundColor: '#404043',
                    trackBorderColor: '#404043'
                  },

                  // special colors for some of the
                  legendBackgroundColor: 'rgba(0, 0, 0, 0.5)',
                  background2: '#505053',
                  dataLabelsColor: '#B0B0B3',
                  textColor: '#C0C0C0',
                  contrastTextColor: '#F0F0F3',
                  maskColor: 'rgba(255,255,255,0.3)'
                }
                Highcharts.setOptions($scope.chartToChange.theme);
                $scope.chartToChange.update($scope.chartToChange.theme);
              }

              $scope.themeChartGridLight = function (nameChart) {
                $scope.chartToChange = angular.element(document.getElementById("divCharFil" + nameChart)).highcharts();
                $scope.chartToChange.theme = {
                  colors: ['#7cb5ec', '#f7a35c', '#90ee7e', '#7798BF', '#aaeeee', '#ff0066', '#eeaaee',
                    '#55BF3B', '#DF5353', '#7798BF', '#aaeeee'],
                  chart: {
                    backgroundColor: null,
                    style: {
                      fontFamily: 'Dosis, sans-serif'
                    }
                  },
                  title: {
                    style: {
                      fontSize: '16px',
                      fontWeight: 'bold',
                      textTransform: 'uppercase'
                    }
                  },
                  tooltip: {
                    borderWidth: 0,
                    backgroundColor: 'rgba(219,219,216,0.8)',
                    shadow: false
                  },
                  legend: {
                    itemStyle: {
                      fontWeight: 'bold',
                      fontSize: '13px'
                    }
                  },
                  xAxis: {
                    gridLineWidth: 1,
                    labels: {
                      style: {
                        fontSize: '12px'
                      }
                    }
                  },
                  yAxis: {
                    minorTickInterval: 'auto',
                    title: {
                      style: {
                        textTransform: 'uppercase'
                      }
                    },
                    labels: {
                      style: {
                        fontSize: '12px'
                      }
                    }
                  },
                  plotOptions: {
                    candlestick: {
                      lineColor: '#404048'
                    }
                  },

                  // General
                  background2: '#F0F0EA'

                };
                Highcharts.setOptions($scope.chartToChange.theme);
                $scope.chartToChange.update($scope.chartToChange.theme);
              }

              $scope.themeChartSand = function (nameChart) {
                $scope.chartToChange = angular.element(document.getElementById("divCharFil" + nameChart)).highcharts();
                $scope.chartToChange.theme = {
                  colors: ['#f45b5b', '#8085e9', '#8d4654', '#7798BF', '#aaeeee', '#ff0066', '#eeaaee',
                    '#55BF3B', '#DF5353', '#7798BF', '#aaeeee'],
                  chart: {
                    backgroundColor: null,
                    style: {
                      fontFamily: 'Signika, serif'
                    }
                  },
                  title: {
                    style: {
                      color: 'black',
                      fontSize: '16px',
                      fontWeight: 'bold'
                    }
                  },
                  subtitle: {
                    style: {
                      color: 'black'
                    }
                  },
                  tooltip: {
                    borderWidth: 0,
                  },
                  legend: {
                    itemStyle: {
                      fontWeight: 'bold',
                      fontSize: '13px'
                    }
                  },
                  xAxis: {
                    labels: {
                      style: {
                        color: '#6e6e70'
                      }
                    }
                  },
                  yAxis: {
                    labels: {
                      style: {
                        color: '#6e6e70'
                      }
                    }
                  },
                  plotOptions: {
                    series: {
                      shadow: true
                    },
                    candlestick: {
                      lineColor: '#404048'
                    },
                    map: {
                      shadow: false
                    }
                  },

                  // Highstock specific
                  navigator: {
                    xAxis: {
                      gridLineColor: '#D0D0D8'
                    }
                  },
                  rangeSelector: {
                    buttonTheme: {
                      fill: 'white',
                      stroke: '#C0C0C8',
                      'stroke-width': 1,
                      states: {
                        select: {
                          fill: '#D0D0D8'
                        }
                      }
                    }
                  },
                  scrollbar: {
                    trackBorderColor: '#C0C0C8'
                  },

                  // General
                  background2: '#E0E0E8'
                }
                Highcharts.setOptions($scope.chartToChange.theme);
                $scope.chartToChange.update($scope.chartToChange.theme);
              }

              $scope.arrayTitleCharSms = [];
              $scope.arrayDataCharSms = [];
              $scope.titleEjeYSms = "";
              $scope.titleEstatics = "";

              $scope.titleTotalSms = "";
              $scope.totalSms = 0;
              $scope.countCampSms = 0;
              $scope.getTotalSms = function (valFilCategory, valFilSms) {
                restService.getAllCampSms(valFilSms).then(function (data) {
                  $scope.countCampSms = data.info[0]['totalCountSms'];
                  $scope.titleTotalSms = "Total de Campañas sms";
                  $scope.titleEjeYSms = "Sms";
                  $scope.titleEstatics = "Estadísticas de Sms";
                  $scope.arrayDataCharSms = [];
                  $scope.arrayTitleCharSms = [];
                  $scope.arrayTitleCharSms.push($scope.titleTotalSms);
                  $scope.arrayDataCharSms.push({name: "Total campañas sms", data: [parseInt($scope.countCampSms)]});
                  $scope.drawGraphicsSms(valFilCategory, valFilSms);
                });
              }

              $scope.getSentSms = function (valFilCategory, valFilSms) {
                restService.getAllCampSms(valFilSms).then(function (data) {
                  $scope.countCampSms = data.info[0]['totalCountSms'];
                  $scope.titleTotalSms = "Total de Sms Enviados";
                  $scope.titleEjeYSms = "Sms";
                  $scope.titleEstatics = "Estadísticas de Sms";
                  $scope.arrayDataCharSms = [];
                  $scope.arrayTitleCharSms = [];
                  $scope.arrayTitleCharSms.push($scope.titleTotalSms);
                  $.each(data.info[0].dataSmsSent, function (index, value) {
                    $scope.arrayDataCharSms.push({name: "Campaña " + (parseInt(index) + 1), data: [parseInt(value['sent'])]});
                  });
                  $scope.drawGraphicsSms(valFilCategory, valFilSms);
                });
              }

              $scope.getSentTotalSms = function (valFilCategory, valFilSms) {
                restService.getAllCampSms(valFilSms).then(function (data) {
                  $scope.countCampSms = data.info[0]['totalCountSms'];
                  $scope.titleTotalSms = "";
                  $scope.titleEjeYSms = "Sms";
                  $scope.titleEstatics = "Estadísticas de Sms";
                  $scope.arrayDataCharSms = [];
                  $scope.arrayTitleCharSms = [];
                  //$scope.arrayTitleCharSms.push($scope.titleTotalSms);
                  $scope.arrayDataSmsSent = [];
                  $scope.arrayDataSmsTotal = [];
                  $.each(data.info[0].dataSmsSent, function (index, value) {
                    $scope.titleTotalSms = "Campaña " + (parseInt(index) + 1);
                    $scope.arrayTitleCharSms.push($scope.titleTotalSms);
                    $scope.arrayDataSmsSent.push(parseInt(value['sent']));
                    $scope.arrayDataSmsTotal.push(parseInt(value['total']));
                  });
                  $scope.arrayDataCharSms.push(
                          {
                            name: "Cantidad de sms enviados",
                            data: $scope.arrayDataSmsSent
                          },
                          {
                            name: "Cantidad de sms recibidos",
                            data: $scope.arrayDataSmsTotal
                          }
                  );
                  $scope.drawGraphicsSms(valFilCategory, valFilSms);
                });
              }

              $scope.drawGraphicsSms = function (valFilCategory, valFilSms) {
                var chartMail = Highcharts.chart('divCharFil' + valFilCategory + valFilSms, {
                  chart: {
                    type: 'column'
                  },
                  title: {
                    text: $scope.titleEstatics
                  },
                  xAxis: {
                    categories: $scope.arrayTitleCharSms
                  },
                  yAxis: {
                    title: {
                      text: $scope.titleEjeYSms
                    }
                  },
                  series: $scope.arrayDataCharSms
                });
              }

              $scope.mailOpenMonth = 0;
              $scope.boundedHardMonth = 0;
              $scope.boundedSoftMonth = 0;
              $scope.quantityPollMonth = 0;
              $scope.smsMonth = 0;
              $scope.chargeInitial = function () {
                restService.getChargeInitial().then(function (data) {
                  $scope.mailOpenMonth = data.info[0]['mailOpenMonth'];
                  $scope.boundedHardMonth = data.info[0]['bouncedHard'];
                  $scope.boundedSoftMonth = data.info[0]['bouncedSoft'];
                  $scope.quantityPollMonth = data.info[0]['qualitypoll'];
                  $scope.smsMonth = data.info[0]['quantitySmsMonth'];
                });
              }

              $scope.countTotalSmsSents = 0;
              $scope.countTotalSmsOk = 0;
              $scope.countTotalCampSms = 0;
              $scope.totalCampnias = (parseInt($scope.countTotalMails) + parseInt($scope.countTotalCampSms));

              $scope.dateInitial = "";
              $scope.dateFinal = "";
              $scope.findDataDate = function (findDataDate) {
                $scope.dateInitial = angular.element("#dateInitial" + findDataDate.category + findDataDate.value).val();
                $scope.dateFinal = angular.element("#dateFinal" + findDataDate.category + findDataDate.value).val();
                findDataDate.valueDateInitial = $scope.dateInitial;
                findDataDate.valueDateFinal = $scope.dateFinal;
                if ($scope.dateInitial == "" || $scope.dateFinal == "") {
                  alert("Por favor complete los campos de las fechas");
                } else {
                  if (findDataDate.category == "m")
                  {
                    $scope.arrayDataCharMail = [];
                    $scope.arrayTitleCharMail = [];
                    restService.getChargeDataDateMail(findDataDate).then(function (data) {
                      if (findDataDate.value == 1) {
                        $scope.countTotalCampMail = data.info[0]['totalCampSubAccount'];
                        $scope.titleTotalMail = "Total de Campañas";
                        $scope.titleEjeY = "Mails";
                        $scope.titleEstatics = "Estadísticas de Mail";
                        $scope.arrayDataSent = [];
                        $scope.arrayTitleMail = [];
                        $scope.totalMessagesSent = 0;
                        if (data.info[0].dataMail.length > 0) {
                          $.each(data.info[0].dataMail, function (index, value) {
                            $scope.totalMessagesSent += parseInt(value['messagesSent']);
                            $scope.arrayDataCharMail.push({name: "Campaña " + (parseInt(index) + 1), data: [parseInt(value['messagesSent'])]});
                          });
                          $scope.arrayDataCharMail.push({name: "Total Mails enviados", data: [parseInt($scope.totalMessagesSent)]});
                        } else {
                          $scope.titleEstatics = "No se encontrarón campañas en estas fechas";
                        }
                        $scope.arrayTitleCharMail.push($scope.titleTotalMail);
                      } else if (findDataDate.value == 2) {
                        $scope.titleTotalMail = "Total de Clics unicos";
                        $scope.titleEjeY = "Cantidad de Clics unicos por mail";
                        $scope.titleEstatics = "Estadísticas de clics por Link";
                        if (data.info[0].dataClickLink.length > 0) {
                          $.each(data.info[0].dataClickLink, function (index, value) {
                            $scope.arrayDataCharMail.push({name: "Campaña " + (parseInt(index) + 1), data: [parseInt(value['uniqueClicks'])]});
                          });
                        } else {
                          $scope.titleEstatics = "No se encontrarón campañas en estas fechas";
                        }
                        $scope.arrayTitleCharMail.push($scope.titleTotalMail);
                      } else if (findDataDate.value == 3) {
                        $scope.countTotalLinkCamp = 0;
                        $scope.titleTotalLinksCamp = "Links";
                        $scope.titleEjeY = "Cantidad de Links en la campaña";
                        $scope.titleEstatics = "Estadísticas de Links";
                        if (data.info[0].dataLinks.length > 0) {
                          $.each(data.info[0].dataLinks, function (index, value) {
                            $scope.countTotalLinkCamp += parseInt(value['NUM_LINKS_MAIL']);
                            $scope.arrayDataCharMail.push({name: "Campaña " + (parseInt(index) + 1), data: [parseInt(value['NUM_LINKS_MAIL'])]});
                          });
                          $scope.arrayDataCharMail.push({name: "Total Links", data: [parseInt($scope.countTotalLinkCamp)]});
                        } else {
                          $scope.titleEstatics = "No se encontrarón campañas en estas fechas";
                        }
                        $scope.arrayTitleCharMail.push($scope.titleTotalLinksCamp);
                      }
                      $scope.drawGraphicsMail(findDataDate.category, findDataDate.value);
                    });
                  } else if (findDataDate.category == "s") {
                    $scope.arrayDataCharSms = [];
                    $scope.arrayTitleCharSms = [];
                    restService.getChargeDataDateSms(findDataDate).then(function (data) {
                      if (findDataDate.value == 4) {
                        $scope.countCampSms = data.info[0]['totalCountSms'];
                        $scope.titleTotalSms = "Total de Campañas sms";
                        $scope.titleEjeYSms = "Sms";
                        $scope.titleEstatics = "Estadísticas de Sms";
                        if (data.info[0].dataSms.length > 0) {
                          $scope.arrayDataCharSms.push({name: "Total campañas sms", data: [parseInt($scope.countCampSms)]});
                        } else {
                          $scope.titleEstatics = "No se encontrarón campañas en estas fechas";
                        }
                        $scope.arrayTitleCharSms.push($scope.titleTotalSms);
                      } else if (findDataDate.value == 5) {
                        $scope.countCampSms = data.info[0]['totalCountSms'];
                        $scope.titleTotalSms = "Total de Sms Enviados";
                        $scope.titleEjeYSms = "Sms";
                        $scope.titleEstatics = "Estadísticas de Sms";
                        if (data.info[0].dataSmsSent.length > 0) {
                          $.each(data.info[0].dataSmsSent, function (index, value) {
                            $scope.arrayDataCharSms.push({name: "Campaña " + (parseInt(index) + 1), data: [parseInt(value['sent'])]});
                          });
                        } else {
                          $scope.titleEstatics = "No se encontrarón campañas en estas fechas";
                        }
                        $scope.arrayTitleCharSms.push($scope.titleTotalSms);
                      } else if (findDataDate.value == 6) {
                        $scope.countCampSms = data.info[0]['totalCountSms'];
                        $scope.titleTotalSms = "";
                        $scope.titleEjeYSms = "Sms";
                        $scope.titleEstatics = "Estadísticas de Sms";
                        $scope.arrayDataSmsSent = [];
                        $scope.arrayDataSmsTotal = [];
                        if (data.info[0].dataSmsSent.length > 0) {
                          $.each(data.info[0].dataSmsSent, function (index, value) {
                            $scope.titleTotalSms = "Campaña " + (parseInt(index) + 1);
                            $scope.arrayTitleCharSms.push($scope.titleTotalSms);
                            $scope.arrayDataSmsSent.push(parseInt(value['sent']));
                            $scope.arrayDataSmsTotal.push(parseInt(value['total']));
                          });
                          $scope.arrayDataCharSms.push(
                                  {
                                    name: "Cantidad de sms enviados",
                                    data: $scope.arrayDataSmsSent
                                  },
                                  {
                                    name: "Cantidad de sms recibidos",
                                    data: $scope.arrayDataSmsTotal
                                  }
                          );
                        }
                        $scope.arrayTitleCharSms.push($scope.titleTotalSms);
                      }
                      $scope.drawGraphicsSms(findDataDate.category, findDataDate.value);
                    });
                  }
                }
              }

              $scope.divHeghtContentChar = angular.element("#divOptions").prop('offsetHeight');

              $scope.chargeCharInitial = function () {
                $scope.chargeInitial();
                $scope.chargeCharPagInitial();
                $scope.findRolServices();
              }

              $scope.chargeCharPagInitial = function () {
                $scope.subscribe($scope.lisFiltersOpsMail[0]);
                $scope.subscribe($scope.lisFiltersOpsMail[1]);
              }

              $scope.InvertirChart = function (nameChart, valTypeChart) {

                var chartDraw = angular.element(document.getElementById("divCharFil" + nameChart)).highcharts();
                var dataChartooptions = {};
                if (valTypeChart == 1) {
                  $scope.typeChart = "column";
                  dataChartooptions.type = "column";
                } else if (valTypeChart == 2) {
                  $scope.typeChart = "pie";
                  dataChartooptions.type = $scope.typeChart;
                  dataChartooptions.plotOptions = {
                    pie: {
                      allowPointSelect: true,
                      cursor: 'pointer',
                      dataLabels: {
                        enabled: true,
                        format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                        style: {
                          color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                        }
                      }
                    },
                    series: {
                      animation: {
                        duration: 1000
                      }
                    }
                  }
                } else if (valTypeChart == 3) {
                  $scope.typeChart = "line";
                  dataChartooptions.type = $scope.typeChart;
                  dataChartooptions.legend = {
                    layout: 'vertical',
                    align: 'right',
                    verticalAlign: 'middle'
                  }
                }
                chartDraw.update({
                  chart: dataChartooptions,
                  plotOptions: dataChartooptions.plotOptions,
                  legend: dataChartooptions.legend
                });
              }

              $scope.redirect = function (key) {
                $window.location = '#/' + key;
              }
              $scope.redirectModulo = function (key) {
                $window.location.href = key;
              }

              $scope.rolServices = [];
              $scope.findRolServices = function () {
                restService.getFindRolServices().then(function (data) {
                  var numObject = Object.keys(data).length;
                  if (numObject > 0) {
                    $.each(data, function (index, value) {
                      $scope.rolServices.push(value.idService);
                    });
                  } else {
                    alert("El usuario no tiene ningun permiso para acceder a esta página");
                  }

                });
              }

              //Charge initial
              $scope.chargeCharInitial();
            }])
          .controller('indexSmsController', ['$scope', 'restService', 'notificationService', 'constantReport', function ($scope, restService, notificationService, constantReport) {

              $scope.dowloadReportSms = function () {
                restService.downloadReportSms($scope.search).then(function () {
                  var url = fullUrlBase + 'statistic/download'
                  location.href = url;
                });
              };

              $('#dateInitial').datetimepicker({
                format: 'yyyy-MM-dd',
                language: 'es'
              }).on('changeDate', function (ev) {
                $scope.tick();
                $scope.$apply();
              });
              $('#dateFinal').datetimepicker({
                format: 'yyyy-MM-dd',
                language: 'es'
              }).on('changeDate', function (ev) {
                $scope.tick();
                $scope.$apply();
              });

              $scope.tick = function () {
                $scope.search.dateInitial = $("#valuedateInitial").val();
                $scope.search.dateFinal = $("#valuedateFinal").val();
              };

              $scope.title = constantReport.Titles.reportsms;
              $scope.initial = 0;
              $scope.page = 1;
              $scope.search = {};
              $scope.accounts = [];
              $scope.forward = function () {
                $scope.initial += 1;
                $scope.page += 1;
                $scope.getAll();
              };
              $scope.fastforward = function () {
                $scope.initial = ($scope.report.total_pages - 1);
                $scope.page = $scope.report.total_pages;
                $scope.getAll();
              };
              $scope.backward = function () {
                $scope.initial -= 1;
                $scope.page -= 1;
                $scope.getAll();
              };
              $scope.fastbackward = function () {
                $scope.initial = 0;
                $scope.page = 1;
                $scope.getAll();
              };
              restService.getAllAccount().then(function (data) {
                $scope.accounts = data;
              });

              $scope.searchReport = function () {
                $scope.getAll();
              };

              $scope.cleanFilters = function () {
                $scope.search = {};
                $("#valuedateInitial").val("");
                $("#valuedateFinal").val("");
                $scope.getAll();
              };
              $scope.searchDate = function () {
                if ($scope.search.dateFinal && $scope.search.dateInitial) {
                  $scope.getAll();
                }
              };
              $scope.getAll = function () {
                restService.getAllReportSms($scope.initial, $scope.search).then(function (data) {
                  $scope.report = data;
                });
              };
              $scope.getAll();
            }])
          .controller('graphController', ['$scope', 'restService', 'notificationService', function ($scope, restService, notificationService) {
              $scope.services = [{idService: 1, name: "Correo"}, {idService: 2, name: "Sms"}];
              $scope.graphres = [];
              $scope.data = {};
              $scope.data.selected = 1;
              $scope.search = {};
              $("#dateInitial").datepicker({
                format: "yyyy-mm",
                viewMode: "months",
                minViewMode: "months",
                autoclose: true
              }).on('changeDate', function (ev) {
                $scope.tick();
                $scope.$apply();
              });
              $('#dateFinal').datepicker({
                format: "yyyy-mm",
                viewMode: "months",
                minViewMode: "months",
                autoclose: true
              }).on('changeDate', function (ev) {
                $scope.tick();
                $scope.$apply();
              });

              $scope.tick = function () {
                $scope.search.dateInitial = $("#valuedateInitial").val();
                $scope.search.dateFinal = $("#valuedateFinal").val();
              };

              $scope.cleanFilters = function () {
                $scope.search = {};
                $("#valuedateInitial").val("");
                $("#valuedateFinal").val("");
                $scope.getAll();
              };

              $scope.searchGraph = function () {
                if ($scope.data.selected == 1) {
                  $scope.mail();
                } else if ($scope.data.selected == 2) {
                  $scope.sms();
                }
              };

              $scope.searchDateMail = function () {
                if ($scope.search.dateFinal && $scope.search.dateInitial) {
                  if ($scope.data.selected == 1) {
                    $scope.mail();
                  } else if ($scope.data.selected == 2) {
                    $scope.sms();
                  }
                }
              };

              $scope.title = function () {
                var title = "";
                if ($scope.data.selected == 1) {
                  title = "de correo";
                } else if ($scope.data.selected == 2) {
                  title = "de sms";
                }
                return title;
              };

              $scope.sms = function () {
                restService.graphSms($scope.search).then(function (data) {
                  $scope.graphres = data;
                  $scope.graph(data, "Sms");
                });
              };

              $scope.mail = function () {
                $scope.getGraphMail = function () {
                  restService.graphMail($scope.search).then(function (data) {
                    $scope.graphres = data;
                    $scope.graph(data, "Correos");
                  });
                };
                $scope.getGraphMail();
              };

              $scope.graph = function (data, title) {
                $scope.chart = {
                  options: {
                    chart: {
                      type: "column"
                    },
                    xAxis: {
                      type: 'category'
                    },
                    plotOptions: {
                      series: {
                        lineWidth: 1,
                        fillOpacity: 0.5,
                        borderWidth: 0
                      }
                    },
                    legend: {
                      enabled: true
                    },
                    title: {
                      text: ' '
                    },
                    credits: {
                      enabled: false
                    }
                  },
                  series: [
                    {
                      name: title,
                      data: data,
                      color: "#7cb5ec"
                    }
                  ]
                };
              };

              $scope.mail();
              $scope.total = function () {
                var count = 0;
                for (var i = 0; i < $scope.graphres.length; i++) {
                  count += $scope.graphres[i].y;
                }
                return count;
              };

            }])
          .controller('excelsmsController', ['$scope', 'restService', 'notificationService', function ($scope, restService, notificationService) {
              $scope.search = {};
              $("#dateInitial").datepicker({
                format: "yyyy-mm",
                viewMode: "months",
                minViewMode: "months",
                autoclose: true
              }).on('changeDate', function (ev) {
                $scope.tick();
                $scope.$apply();
              });
              $('#dateFinal').datepicker({
                format: "yyyy-mm",
                viewMode: "months",
                minViewMode: "months",
                autoclose: true
              }).on('changeDate', function (ev) {
                $scope.tick();
                $scope.$apply();
              });

              $scope.tick = function () {
                $scope.search.dateInitial = $("#valuedateInitial").val();
                $scope.search.dateFinal = $("#valuedateFinal").val();
              };

              $scope.cleanFilters = function () {
                $scope.search = {};
                $("#valuedateInitial").val("");
                $("#valuedateFinal").val("");
                $scope.getAll();
              };


              $scope.searchDateMail = function () {
                if ($scope.search.dateFinal && $scope.search.dateInitial) {
                  $scope.getAll();
                }
              };

              $scope.initial = 0;
              $scope.page = 1;
              $scope.forward = function () {
                $scope.initial += 1;
                $scope.page += 1;
                $scope.getAll();
              };
              $scope.fastforward = function () {
                $scope.initial = ($scope.infosms.total_pages - 1);
                $scope.page = $scope.infosms.total_pages;
                $scope.getAll();
              };
              $scope.backward = function () {
                $scope.initial -= 1;
                $scope.page -= 1;
                $scope.getAll();
              };
              $scope.fastbackward = function () {
                $scope.initial = 0;
                $scope.page = 1;
                $scope.getAll();
              };

              $scope.downloadSms = function () {
                restService.downloadSms($scope.search).then(function () {
                  var url = fullUrlBase + 'statistic/download'
                  location.href = url;
                });
              };

              $scope.getAll = function () {
                restService.getInfoExcelSms($scope.initial, $scope.search).then(function (data) {
                  $scope.infosms = data;
                });
              };
              $scope.getAll();
            }])
          .controller('excelsmsdayController', ['$scope', 'restService', 'notificationService', 'constantReport', function ($scope, restService, notificationService, constantReport) {
              $scope.title = constantReport.Titles.reportexcelsmsday;

              $scope.downloadSms = function () {
                restService.downloadSmsbyday($scope.search, $scope.title).then(function () {
                  var url = fullUrlBase + 'statistic/downloadexcel/' + $scope.title;
                  location.href = url;
                });
              };

              $scope.cleanFilters = function () {
                $scope.search = {};
                $scope.search.valuedateInitial = '';
                $scope.search.valuedateFinal = '';
                $scope.getAll();
              };


              $scope.searchDateMail = function () {
                $scope.search.dateInitial = $scope.search.valuedateInitial;
                $scope.search.dateFinal = $scope.search.valuedateFinal;
                $scope.getAll();

              };


              $scope.initial = 0;
              $scope.page = 1;
              $scope.forward = function () {
                $scope.initial += 1;
                $scope.page += 1;
                $scope.getAll();
              };
              $scope.fastforward = function () {
                $scope.initial = ($scope.infosms.total_pages - 1);
                $scope.page = $scope.infosms.total_pages;
                $scope.getAll();
              };
              $scope.backward = function () {
                $scope.initial -= 1;
                $scope.page -= 1;
                $scope.getAll();
              };
              $scope.fastbackward = function () {
                $scope.initial = 0;
                $scope.page = 1;
                $scope.getAll();
              };

              $scope.getAll = function () {
                restService.getInfoExcelDaySms($scope.initial, $scope.search).then(function (data) {
                  if (data.items.length > 0) {
                    $scope.infosms = data;
                  } else {
                    notificationService.warning(constantReport.Notifications.Errors.errorNoneSmsDataMonth);
                  }
                });
              };
              $scope.getAll();

            }])
          .controller('infosmsController', ['$scope', 'restService', 'notificationService', 'constantReport', function ($scope, restService, notificationService, constantReport) {
              $scope.title = constantReport.Titles.reportsms;

              $scope.dowloadReport = function () {
                restService.dowloadReportInfoDetailSms($scope.search, $scope.title).then(function () {
                  var url = fullUrlBase + 'statistic/downloadexcel/' + $scope.title;
                  location.href = url;
                });
              };

              $scope.search = {};

              $scope.cleanFilters = function () {
                $scope.search = {};
                $scope.search.valuedateInitial = '';
                $scope.search.valuedateFinal = '';
                $scope.getAll();
              };

              $scope.searchReport = function () {
                $scope.search.dateInitial = $scope.search.valuedateInitial;
                $scope.search.dateFinal = $scope.search.valuedateFinal;
                $scope.getAll();
              };

              $scope.searchDate = function () {
                $scope.search.dateInitial = $scope.search.valuedateInitial;
                $scope.search.dateFinal = $scope.search.valuedateFinal;
                $scope.getAll();

              };

              $scope.initial = 0;
              $scope.page = 1;
              $scope.forward = function () {
                $scope.initial += 1;
                $scope.page += 1;
                $scope.getAll();
              };
              $scope.fastforward = function () {
                $scope.initial = ($scope.report.total_pages - 1);
                $scope.page = $scope.report.total_pages;
                $scope.getAll();
              };
              $scope.backward = function () {
                $scope.initial -= 1;
                $scope.page -= 1;
                $scope.getAll();
              };
              $scope.fastbackward = function () {
                $scope.initial = 0;
                $scope.page = 1;
                $scope.getAll();
              };
              restService.getSubaccount().then(function (data) {
                $scope.subaccount = data;
              });
              restService.getEmailUsers().then(function (data) {
                $scope.users = data;
              });
              $scope.getAll = function () {
                restService.getInfoDetailSms($scope.initial, $scope.search).then(function (data) {
                  $scope.report = data;
                });
              };
              $scope.getAll();

            }])
          .controller('infomailController', ['$scope', 'restService', 'notificationService', 'constantReport', function ($scope, restService, notificationService, constantReport) {
              $scope.title = constantReport.Titles.reportinfomail;
              $scope.dowloadReport = function () {
                $scope.misc.progressbar = false ;
                restService.dowloadReportInfoDetailMail($scope.search, $scope.title).then(function () {
                  var url = fullUrlBase + 'statistic/downloadexcel/' + $scope.title;
                  location.href = url;
                  $scope.misc.progressbar = true ;
                });
              };

              $scope.search = {};
              $scope.misc = {};

              $scope.cleanFilters = function () {
                $scope.search = {};
                $scope.search.valuedateInitial = '';
                $scope.search.valuedateFinal = '';
                $scope.getAll();
              };

              $scope.searchReport = function () {
                $scope.getAll();
              };

              $scope.searchDate = function () {
                $scope.initial = 0;
                $scope.page = 1;
                if((angular.isDefined($scope.search.valuedateInitial) && $scope.search.valuedateInitial !== "")||
                   (angular.isDefined($scope.search.valuedateFinal) && $scope.search.valuedateFinal !== "")){
                  $scope.search.dateInitial = $scope.search.valuedateInitial;
                  $scope.search.dateFinal = $scope.search.valuedateFinal;
                  $scope.getAll();
                }else{
                  notificationService.warning(constantReport.Notifications.Errors.emptyData);
                }
              };

              $scope.initial = 0;
              $scope.page = 1;
              $scope.forward = function () {
                $scope.initial += 1;
                $scope.page += 1;
                $scope.getAll();
              };
              $scope.fastforward = function () {
                $scope.initial = ($scope.report.total_pages - 1);
                $scope.page = $scope.report.total_pages;
                $scope.getAll();
              };
              $scope.backward = function () {
                $scope.initial -= 1;
                $scope.page -= 1;
                $scope.getAll();
              };
              $scope.fastbackward = function () {
                $scope.initial = 0;
                $scope.page = 1;
                $scope.getAll();
              };
              restService.getSubaccount().then(function (data) {
                $scope.subaccount = data;
              });
              restService.getEmailUsers().then(function (data) {
                $scope.users = data;
              });
              $scope.getAll = function () {
                $scope.misc.progressbar = false ;
                restService.getInfoDetailMail($scope.initial, $scope.search).then(function (data) {
                  $scope.report = data;
                  $scope.misc.progressbar = true ;
                });
              };
              $scope.getAll();
            }])
          .controller('reportvalidation', ['$scope', 'restService', 'notificationService', 'constantReport', '$window', function ($scope, restService, notificationService, constantReport, $window) {

              //Universal Data
              $scope.data = {};
              //set misc
              $scope.misc = {
                progressbar: false,
                initial: 0,
                page: 1,
                nameForm: "",
                totalValidations: 0,
                total_pages: 0,
                search: {},
                accounts: [],
                categories: [{name: 'A'}, {name: 'B'}, {name: 'D'}],
                progressbar: true,
                categoriesBad: [{name: 'F'}]
              };

              $scope.functions = {
                forward: function () {
                  $scope.misc.initial += 1;
                  $scope.misc.page += 1;
                  $scope.resService.getAllMailValidation();
                },
                fastforward: function () {
                  $scope.misc.initial = ($scope.misc.total_pages - 1);
                  $scope.misc.page = $scope.misc.total_pages;
                  $scope.resService.getAllMailValidation();
                },
                backward: function () {
                  $scope.misc.initial -= 1;
                  $scope.misc.page -= 1;
                  $scope.resService.getAllMailValidation();
                },
                fastbackward: function () {
                  $scope.misc.initial = 0;
                  $scope.misc.page = 1;
                  $scope.resService.getAllMailValidation();
                },
                cleanFilters: function () {
                  $scope.misc.search.dateInitial = "";
                  $scope.misc.search.dateFinal = "";
                  $scope.misc.initial = 0;
                  $scope.misc.page = 1;
                  $scope.misc.search = {};
                  this.getAllMailValidation();
                },
                cleanFiltersBad: function () {
                  $scope.misc.search.dateInitialTwo = "";
                  $scope.misc.search.dateFinalTwo = "";
                  $scope.misc.initial = 0;
                  $scope.misc.page = 1;
                  $scope.misc.search = {};
                  this.getAllMailBounced();
                },

                searchDate: function () {
                  if ((!$scope.misc.search.dateFinal && $scope.misc.search.dateInitial) || ($scope.misc.search.dateFinal && !$scope.misc.search.dateInitial)) {
                    notificationService.warning(constantReport.Notifications.Errors.dateComplete);
                    return false;
                  }
                  if ($scope.misc.search.email) {
                    $scope.re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                    if (!$scope.misc.search.email.match($scope.re)) {
                      notificationService.warning(constantReport.Notifications.Errors.emailNotCorrect);
                      return false;
                    } else {
                      $scope.data.initial = 0;
                      $scope.data.page = 1;
                      $scope.resService.getAllMailValidation();
                    }
                  } else {
                    this.getAllMailValidation();
                  }
                },

                searchDateTwo: function () {
//                  if ($scope.misc.search.dateFinalTwo && $scope.misc.search.dateInitialTwo) {
//                    this.getAllMailBounced();
//                  }

                  if ((!$scope.misc.search.dateFinalTwo && $scope.misc.search.dateInitialTwo) || ($scope.misc.search.dateFinalTwo && !$scope.misc.search.dateInitialTwo)) {
                    notificationService.warning(constantReport.Notifications.Errors.dateComplete);
                    return false;
                  }
                  if ($scope.misc.search.email) {
                    $scope.re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                    if (!$scope.misc.search.email.match($scope.re)) {
                      notificationService.warning(constantReport.Notifications.Errors.emailNotCorrect);
                      return false;
                    } else {
                      $scope.data.initial = 0;
                      $scope.data.page = 1;
                      $scope.resService.getAllMailBounced();
                    }
                  } else {
                    this.getAllMailBounced();
                  }

                },

                searchDateBadEmail: function () {
                  if ($scope.misc.search.dateFinal && $scope.misc.search.dateInitial) {
                    this.getAllMailBounced();
                  }
                },

                getAllMailValidation: function () {
                  $scope.resService.getAllMailValidation();
                },

                getAllMailBounced: function () {
                  $scope.resService.getAllMailBounced();
                },

                getAllAccount: function () {
                  $scope.resService.getAllAccount();
                },

                searchReport: function () {
                  $scope.misc.initial = 0;
                  $scope.misc.page = 1;
                  $scope.resService.getAllMailValidation();
                },

                searchReportBounced: function () {
                  $scope.misc.initial = 0;
                  $scope.misc.page = 1;
                  $scope.resService.getAllMailBounced();
                },

                dowloadReport: function () {
                  $scope.resService.dowloadReport();
                },

                dowloadReportBounced: function () {
                  $scope.resService.dowloadReportBounced();
                },

                changeOptionEmailValidation: function () {
                  $scope.misc.initial = 0;
                  $scope.misc.page = 1;
                  $scope.misc.search = {};
                  $scope.resService.getAllMailValidation();
                },

                changeOptionEmailBounced: function () {
                  $scope.misc.initial = 0;
                  $scope.misc.page = 1;
                  $scope.misc.search = {};
                  $scope.resService.getAllMailBounced();
                },

                startDateOnSetTime: function () {
                  $scope.$broadcast("start-date-changed");
                },
                endDateOnSetTime: function () {
                  $scope.$broadcast("end-date-changed");
                },
                startDateBeforeRender: function ($dates) {
                  if ($scope.misc.search.dateFinal) {
                    var activeDate = moment($scope.misc.search.dateFinal);

                    $dates
                            .filter(function (date) {
                              return date.localDateValue() >= activeDate.valueOf();
                            })
                            .forEach(function (date) {
                              date.selectable = false;
                            });
                  }
                },

                endDateBeforeRender: function ($view, $dates) {
                  if ($scope.misc.search.dateInitial) {
                    var activeDate = moment($scope.misc.search.dateInitial)
                            .subtract(1, $view)
                            .add(1, "minute");

                    $dates
                            .filter(function (date) {
                              return date.localDateValue() <= activeDate.valueOf();
                            })
                            .forEach(function (date) {
                              date.selectable = false;
                            });
                  }
                },

                startDateBeforeRenderTwo: function ($dates) {
                  if ($scope.misc.search.dateFinalTwo) {
                    var activeDate = moment($scope.misc.search.dateFinalTwo);

                    $dates
                            .filter(function (date) {
                              return date.localDateValue() >= activeDate.valueOf();
                            })
                            .forEach(function (date) {
                              date.selectable = false;
                            });
                  }
                },

                endDateBeforeRenderTwo: function ($view, $dates) {
                  if ($scope.misc.search.dateInitialTwo) {
                    var activeDate = moment($scope.misc.search.dateInitialTwo)
                            .subtract(1, $view)
                            .add(1, "minute");
                    $dates
                            .filter(function (date) {
                              return date.localDateValue() <= activeDate.valueOf();
                            })
                            .forEach(function (date) {
                              date.selectable = false;
                            });
                  }
                },

                filterDate: function () {
                  $scope.data.initial = 0;
                  $scope.data.page = 1;
                  $scope.resService.getAllMailValidation();
                },

                findEmail: function () {
                  $scope.re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                  if (!$scope.misc.search.email) {
                    notificationService.warning(constantReport.Notifications.Errors.completeEmail);
                  } else if (!$scope.misc.search.email.match($scope.re)) {
                    notificationService.warning(constantReport.Notifications.Errors.emailNotCorrect);
                  } else {
                    $scope.data.initial = 0;
                    $scope.data.page = 1;
                    $scope.resService.getAllMailValidation();
                  }
                },

                findEmailBounced: function () {
                  $scope.re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                  if (!$scope.misc.search.email) {
                    notificationService.warning(constantReport.Notifications.Errors.completeEmail);
                  } else if (!$scope.misc.search.email.match($scope.re)) {
                    notificationService.warning(constantReport.Notifications.Errors.emailNotCorrect);
                  } else {
                    $scope.data.initial = 0;
                    $scope.data.page = 1;
                    $scope.resService.getAllMailBounced();
                  }
                },

                forwardTwo: function () {
                  $scope.misc.initial += 1;
                  $scope.misc.page += 1;
                  $scope.resService.getAllMailBounced();
                },
                fastforwardTwo: function () {
                  $scope.misc.initial = ($scope.misc.total_pages - 1);
                  $scope.misc.page = $scope.misc.total_pages;
                  $scope.resService.getAllMailBounced();
                },
                backwardTwo: function () {
                  $scope.misc.initial -= 1;
                  $scope.misc.page -= 1;
                  $scope.resService.getAllMailBounced();
                },
                fastbackwardTwo: function () {
                  $scope.misc.initial = 0;
                  $scope.misc.page = 1;
                  $scope.resService.getAllMailBounced();
                }
              }

              $scope.resService = {
                getAllMailValidation: function () {
                  $scope.misc.progressbar = false;
                  restService.getAllMailValidation($scope.misc.initial, $scope.misc.search).then(function (data) {
                    if (data.totalValidations > 0) {
                      $scope.loading = true;
                      $scope.data = data.data;
                      $scope.misc.totalValidations = data.totalValidations;
                      $scope.misc.total_pages = data.total_pages;
                      $scope.misc.progressbar = true;
                    } else {
                      $scope.misc.totalValidations = 0;
                      $scope.misc.total_pages = 0;
                      $scope.loading = false;
                      $scope.misc.progressbar = true;
                      notificationService.warning(constantReport.Notifications.Errors.errorNoneContactsGetMailValidation);
                    }
                  }).catch(function (error) {
                    notificationService.error(error.message);
                  });
                },

                getAllMailBounced: function () {
                  $scope.misc.progressbar = false;
                  restService.getAllMailBounced($scope.misc.initial, $scope.misc.search).then(function (data) {
                    if (data.totalValidations > 0) {
                      $scope.loading = true;
                      $scope.data = data.data;
                      $scope.misc.totalValidations = data.totalValidations;
                      $scope.misc.total_pages = data.total_pages;
                      $scope.misc.progressbar = true;
                    } else {
                      $scope.misc.totalValidations = 0;
                      $scope.misc.total_pages = 0;
                      $scope.loading = false;
                      $scope.misc.progressbar = true;
                      notificationService.warning(constantReport.Notifications.Errors.errorNoneContactsGetMailValidation);
                    }
                  }).catch(function (error) {
                    notificationService.error(error.message);
                  });
                },

                getAllAccount: function () {
                  restService.getAllAccount().then(function (data) {
                    if (data.length > 0) {
                      $scope.misc.accounts = data;
                    } else {
                      notificationService.warning(constantReport.Notifications.Errors.noGetAccount);
                    }
                  })
                },

                dowloadReport: function () {
                  $scope.misc.progressbar = false;
                  restService.downloadMailValidation($scope.misc.initial, $scope.misc.search).then(function (data) {
                    sessionStorage.setItem("var", data.respuest);
                    if (data.respuest === 1) {
                      $scope.misc.progressbar = true;
                      var nameFile = "reporte_de_correos_validados";
                      var url = fullUrlBase + 'statistic/downloadexcel/' + nameFile;
                      $window.location.href = url;
                    } else {
                      $scope.misc.progressbar = false;
                    }
                  });
                },

                dowloadReportBounced: function () {
                  $scope.misc.progressbar = false;
                  restService.downloadMailBounced($scope.misc.initial, $scope.misc.search).then(function (data) {
                    sessionStorage.setItem("var", data.respuest);
                    if (data.respuest === 1) {
                      $scope.misc.progressbar = true;
                      var nameFile = "reporte_de_correos_no_validados";
                      var url = fullUrlBase + 'statistic/downloadexcel/' + nameFile;
                      $window.location.href = url;
                    } else {
                      $scope.misc.progressbar = false;
                    }
                  });
                },

              }

              $scope.functions.getAllAccount();
              $scope.functions.getAllMailValidation();

            }])
          .controller('smsxemailController', ['$scope', 'restService', 'notificationService', 'constantReport', '$window', function ($scope, restService, notificationService, constantReport, $window) {
              //set data
              $scope.data = {};
              $scope.data.initial = 0;
              $scope.data.page = 1;

              $scope.data.filter = {};
              //Set misc
              $scope.misc = {};
              //Set functions universal
              $scope.functions = {
                setList: function (data) {
                  $scope.misc.list = data;
                },
                setMethodMisc: function (item, value) {
                  $scope.misc[item] = value;
                },
                setMethodData: function (item, data) {
                  $scope.data[item] = data;
                },
                searchname: function () {
                  $scope.restServices.getAll();
                },
                Pagination: {
                  forward: function () {
                    $scope.data.initial += 1;
                    $scope.data.page += 1;
                    $scope.restServices.getAll();
                  },
                  fastforward: function () {
                    $scope.data.initial = ($scope.misc.list.total_pages - 1);
                    $scope.data.page = $scope.misc.list.total_pages;
                    $scope.restServices.getAll();
                  },
                  backward: function () {
                    $scope.data.initial -= 1;
                    $scope.data.page -= 1;
                    $scope.restServices.getAll();
                  },
                  fastbackward: function () {
                    $scope.data.initial = 0;
                    $scope.data.page = 1;
                    $scope.restServices.getAll();
                  },
                  filterDate: function () {
                    $scope.data.initial = 0;
                    $scope.data.page = 1;
                    $scope.restServices.getAll();
                  }
                },
                cleanFilters: function () {
                  $scope.data.filter.valuedateInitial = '';
                  $scope.data.filter.valuedateFinal = '';
                  $scope.restServices.getAll();
                },
                searchDate: function () {
                  $scope.restServices.getAll();
                }
              };
              //set functions api
              $scope.restServices = {
                getAll: function () {
                  restService.getAllReportSmsxemail($scope.data.initial, $scope.data).then(function (data) {
                    $scope.functions.setList(data);
                  })
                          .catch(function (error) {
                            notificationService.error(error.data.message);
                          });
                }
              };
              $scope.restServices.getAll();
            }])
          .controller('listsmschannel', ['$scope', 'restService', 'notificationService', 'constantReport', '$window', function ($scope, restService, notificationService, constantReport, $window) {
              $scope.data = {
                data: {}
              };
              //set misc
              $scope.misc = {
                progressbar: false,
                initial: 0,
                page: 1,
                nameForm: "",
                totalValidations: 0,
                total_pages: 0,
                search: {},
                accounts: [],
                categories: [{name: 'A'}, {name: 'B'}, {name: 'D'}],
                progressbar: true,
                categoriesBad: [{name: 'F'}],
                columnsChannel: [],
                dataTotalChannel: 0,
                dateChoose: moment(),
                dateFormat: ""
              };

              //$scope.misc.dateFormat = $scope.misc.dateChoose.format('YYYY-MM');

              $scope.functions = {

                getDataSmsChannel: function () {
                  $scope.services.getDataSmsChannel();
                },

                searchDate: function () {
                  if ($scope.misc.search.dateFinal) {
                    this.getDataSmsChannel();
                  } else if (!$scope.misc.search.dateFinal) {
                    notificationService.warning(constantReport.Notifications.Errors.dateComplete);
                    return false;
                  }
                },

                cleanFilters: function () {
                  $scope.misc.search.dateInitial = "";
                  $scope.misc.search.dateFinal = "";
                  $scope.misc.initial = 0;
                  $scope.misc.page = 1;
                  $scope.misc.search = {};
                  this.getDataSmsChannel();
                }

              }

              $scope.services = {
                getDataSmsChannel: function () {
                  $scope.misc.progressbar = false;
                  restService.getDataSmsChannel($scope.misc.initial, $scope.misc.search).then(function (data) {
                    if (data.totalValidations > 0) {
                      $scope.loading = true;
                      $scope.data = data.data;
                      $scope.columnsChannel = data.columnsChannel;
                      $scope.dataTotalChannel = data.dataTotalChannel;
                      $scope.misc.totalValidations = data.totalValidations;
                      $scope.misc.total_pages = data.total_pages;
                      $scope.misc.progressbar = true;
                      $scope.misc.dateFormat = $scope.misc.search.dateFinal;
                      $scope.misc.dateFormat = data.dateInfo;
                    } else {
                      $scope.misc.totalValidations = 0;
                      $scope.misc.total_pages = 0;
                      $scope.loading = false;
                      $scope.misc.progressbar = true;
                      notificationService.warning(constantReport.Notifications.Errors.errorNoneSms);
                    }
                  }).catch(function (error) {
                    notificationService.error(error.message);
                  });
                },
              }

              $scope.functions.getDataSmsChannel();
            }])
          .controller('infosmsbydestinatariesController', ['$scope', 'restService', 'notificationService', 'constantReport', function ($scope, restService, notificationService, constantReport) {
              $scope.titleInfosmsbydestinataries = constantReport.Titles.infosmsbydestinataries;
              $scope.downloadReportSmsByDestinataries = function () {
                
                $scope.searchDataReport = {};
                if(angular.isDefined($scope.misc.filterNameCampaign) && $scope.misc.filterNameCampaign !== ""){
                  $scope.searchDataReport.filterNameCampaign = $scope.misc.filterNameCampaign;
                }
                if(angular.isDefined($scope.misc.filterPhoneNumber) && $scope.misc.filterPhoneNumber !== ""){
                  $scope.searchDataReport.filterPhoneNumber = $scope.misc.filterPhoneNumber;
                }
                if(angular.isDefined($scope.filter.dateInitial) && $scope.filter.dateInitial !== ""){
                  $scope.searchDataReport.dateInitial = $scope.filter.dateInitial;
                }
                if(angular.isDefined($scope.filter.dateEnd) && $scope.filter.dateEnd !== ""){
                  $scope.searchDataReport.dateEnd = $scope.filter.dateEnd;
                }
                if(angular.isDefined($scope.filter.dataTab) && $scope.filter.dataTab !== ""){
                  $scope.searchDataReport.dataTab = $scope.filter.dataTab;
                }
                $scope.misc.progressbar = false ;
                restService.downloadReportSmsDestinataries($scope.searchDataReport).then(function() {
                  var url = fullUrlBase + 'statistic/downloadexcel/' + $scope.titleInfosmsbydestinataries;
                  location.href = url;
                  $scope.misc.progressbar = true;
                });

              };
              $scope.misc = {};
              $scope.misc.progressbar = false;
              $scope.filter = {};
              $scope.initial = 0;
              $scope.page = 1;
              $scope.misc.searchData = function () {
                $scope.initial = 0;
                $scope.page = 1;
                $scope.misc.progressbar = true;
                if ((angular.isDefined($scope.misc.filterNameCampaign) && $scope.misc.filterNameCampaign !== "") ||
                        (angular.isDefined($scope.misc.filterPhoneNumber) && $scope.misc.filterPhoneNumber !== "") ||
                        ((angular.isDefined($scope.filter.dateInitial) && $scope.filter.dateInitial !== "") &&
                                (angular.isDefined($scope.filter.dateEnd) && $scope.filter.dateEnd !== ""))) {
                  $scope.disabled = true;
                  $scope.getAllInfoSmsByDestinataries();
                } else {
                  notificationService.warning(constantReport.Notifications.Errors.emptyData);
                  return;
                }
                $scope.misc.progressbar = false;
              };
              $scope.cleanFilters = function () {
                $scope.disabled = false;
                $scope.misc.progressbar = false;
                $scope.filter.dateInitial = null;
                $scope.filter.dateEnd = null;
                $scope.initial = 0;
                $scope.page = 1;
                $scope.misc.filterNameCampaign = "";
                $scope.misc.filterPhoneNumber = "";
                this.getAllInfoSmsByDestinataries();
              };

              $scope.forward = function () {
                $scope.misc.progressbar = false;
                $scope.initial += 1;
                $scope.page += 1;
                $scope.getAllInfoSmsByDestinataries();
              };
              $scope.fastforward = function () {
                $scope.misc.progressbar = false;
                $scope.initial = ($scope.data.page - 1);
                $scope.page = $scope.data.page;
                $scope.getAllInfoSmsByDestinataries();
              };
              $scope.backward = function () {
                $scope.misc.progressbar = false;
                $scope.initial -= 1;
                $scope.page -= 1;
                $scope.getAllInfoSmsByDestinataries();
              };
              $scope.fastbackward = function () {
                $scope.misc.progressbar = false;
                $scope.initial = 0;
                $scope.page = 1;
                $scope.getAllInfoSmsByDestinataries();
              };

              $scope.dataTab = function ($event, dataTab) {
                $scope.misc.progressbar = false;
                $scope.initial = 0;
                $scope.page = 1;
                var i, tabcontent, tablinks;
                tabcontent = document.getElementsByClassName("tabcontent");
                for (i = 0; i < tabcontent.length; i++) {
                  tabcontent[i].style.display = "none";
                }
                tablinks = document.getElementsByClassName("tablinks");
                for (i = 0; i < tablinks.length; i++) {
                  tablinks[i].className = tablinks[i].className.replace(" active", " ");
                }
                document.getElementById(dataTab).style.display = "block";
                $event.currentTarget.className += " active";
                $scope.filter.dataTab = dataTab;
                if ($scope.filter.dataTab == "loteCsv") {
                  $scope.initial = 0;
                  $scope.page = 1;
                  $scope.getAllInfoSmsByDestinataries();
                } else if ($scope.filter.dataTab == "contact") {
                  $scope.initial = 0;
                  $scope.page = 1;
                  $scope.getAllInfoSmsByDestinataries();
                }
              }

              $scope.getAllInfoSmsByDestinataries = function () {
                restService.getAllSmsByDestinataries($scope.initial, $scope.misc.filterNameCampaign, $scope.misc.filterPhoneNumber, $scope.filter.dateInitial, $scope.filter.dateEnd, $scope.filter.dataTab).then(function (data) {
                  $scope.data = data;
//                  console.log($scope.data);
                  $scope.misc.progressbar = true;
                }).catch(function (error) {
                  notificationService.error(error.message);
                  $scope.misc.progressbar = true;
                  $scope.disabled = true;
                })
              };
              $scope.translateStatus = function (status) {
                var string = "";
                switch (status) {
                  case "sent":
                    string = "Enviado";
                    break;
                  case "undelivered":
                    string = "No enviado";
                    break;
                  case "scheduled":
                    string = "Programado";
                    break;
                  case "canceled":
                    string = "Cancelado";
                    break;
                }
                return string;
              }
              //Funcion que ejecuta un click simulado sobtre el 1er tab de Data LoteCsv 
              window.setTimeout(function () {
                $("#lc").trigger("click");
              }, constantReport.timeoutTab.loteCsvTab);

              $scope.getAllInfoSmsByDestinataries();
            }]);
})();
