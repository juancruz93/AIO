(function () {
  angular.module('statistic.controllers', [])
          .controller('mailcontroller', ['$scope', 'statisticService', "$stateParams", "notificationService", "$location", "$anchorScroll", '$ocLazyLoad', '$compile', 'constantStatistic', function ($scope, statisticService, $stateParams, notificationService, $location, $anchorScroll, $ocLazyLoad, $compile, constantStatistic) {

              $scope.title = constantStatistic.Titles.statisMail;
              $ocLazyLoad.load([
                'library/highstock/highcharts-ng.js',
                'library/drilldown/highstock.src.min.js',
              ]).then(function () {
                if (typeof Highcharts != "undefined") {
                  $ocLazyLoad.load([
                    'library/drilldown/drilldown.js',
                  ]).then(function () {
                    var text = '<highchart config="chartConfig"  style="min-width: 100%;  margin: 0 auto" ></highchart>';
                    var elToAppendOpen = $compile(text)($scope);
                    var elToAppendClic = $compile(text)($scope);
                    var elToAppendUnsus = $compile(text)($scope);
                    var elToAppendBoun = $compile(text)($scope);
                    var elToAppendSpam = $compile(text)($scope);
                    var elToAppendBuzon = $compile(text)($scope);

                    $('#highchartOpen').append(elToAppendOpen);
                    $('#highchartClic').append(elToAppendClic);
                    $('#highchartUnsuscribe').append(elToAppendUnsus);
                    $('#highchartBounced').append(elToAppendBoun);
                    $('#highchartSpam').append(elToAppendSpam);
                    $('#highchartBuzon').append(elToAppendBuzon);
                    $scope.getAll();
                    $scope.opening();
                  });
                }
              }, function (e) {
                console.log('errr');
                console.error(e);
              });
              $scope.goOpen = function () {
                $location.hash('open');
                $anchorScroll();
                $scope.opening();
              };
              $scope.goClic = function () {
                $location.hash('clic');
                $anchorScroll();
                $scope.clic();
              };
              $scope.goUnsuscribe = function () {
                $location.hash('unsuscribe');
                $anchorScroll();
                $scope.unsuscribe();
              };
              $scope.goBounced = function () {
                $location.hash('bounced');
                $anchorScroll();
                $scope.bounced();
              };
              $scope.goSpam = function () {
                $location.hash('spam');
                $anchorScroll();
                $scope.spam();
              };
              $scope.goBuzon = function () {
                $location.hash('buzon');
                $anchorScroll();
                $scope.buzon();
              };
              $scope.previewmailtempcont = function (id) {
                statisticService.previewMailTemplateContent(id).then(function (data) {
                  var e = data.template;
                  $('#content-preview').empty();
                  $('<iframe frameborder="0" width="100%" height="100%"/>').appendTo('#content-preview').contents().find('body').append(e);
                  $('#myModal').modal('show');
                });
              };
              $scope.reportClic = function () {
                var route = "";
                if ($scope.selectOpen) {
                  route = "open";
                } else if ($scope.selectClic) {
                  route = "clic";
                } else if ($scope.selectUnsuscribed) {
                  route = "unsuscribe";
                } else if ($scope.selectBounced) {
                  route = "bounced";
                } else if ($scope.selectSpam) {
                  route = "spam";
                } else if ($scope.selectBuzon) {
                  route = "buzon";
                }

                statisticService.reportStatics($scope.idMail, route, $scope.title).then(function () {
                  var url = fullUrlBase + 'statistic/downloadexcel/' + $scope.title;
                  location.href = url;
                });
              };
              $scope.chartConfig = {};
              $scope.filter = {};
              $scope.filter.selected = -1;
              $scope.filterSelected = function () {
                $scope.filter.selected = [];
                switch ($scope.filter.setvalue) {
                  case "type":
                    $scope.filter.filters = [{id: -1, name: "Todos"}, {id: 1, name: "Soft"}, {id: 2, name: "Hard"}];
                    break;
                  case "category":
                    statisticService.getAllCategoryBounced($scope.idMail).then(function (data) {
                      $scope.filter.filters = data;
                      $scope.filter.filters.unshift({id: -1, name: "Todos"});
                    });
                    break;
                  case "domain":
                    statisticService.getAllDomain($scope.idMail).then(function (data) {
                      $scope.filter.filters = data;
                      $scope.filter.filters.unshift({id: -1, name: "Todos"});
                    });
                    break;
                }
              };
              $scope.filter.setvalue = "type";
              $scope.filter.filters = [{id: -1, name: "Todos"}, {id: 1, name: "soft"}, {id: 2, name: "hard"}];
              $scope.selectOpen = false;
              $scope.selectClic = false;
              $scope.selectUnsuscribed = false;
              $scope.selectBounced = false;
              $scope.selectSpam = false;
              $scope.selectBuzon = false;
              $scope.initial = 0;
              $scope.page = 1;
              $scope.initialClic = 0;
              $scope.pageClic = 1;
              $scope.forward = function () {
                $scope.prueba = true;
                $scope.initial += 1;
                $scope.page += 1;
                $scope.getData();
              };
              $scope.fastforward = function () {
                $scope.initial = ($scope.graph.info[1].total_pages - 1);
                $scope.page = $scope.graph.info[1].total_pages;
                $scope.getData();
              };
              $scope.backward = function () {
                $scope.initial -= 1;
                $scope.page -= 1;
                $scope.getData();
              };
              $scope.fastbackward = function () {
                $scope.initial = 0;
                $scope.page = 1;
                $scope.getData();
              };
              $scope.forwardClic = function () {
                $scope.prueba = true;
                $scope.initialClic += 1;
                $scope.pageClic += 1;
                $scope.infoDataClic();
              };
              $scope.fastforwardClic = function () {
                $scope.initialClic = ($scope.graph.info[1].total_pages - 1);
                $scope.pageClic = $scope.graph.info[1].total_pages;
                $scope.infoDataClic();
              };
              $scope.backwardClic = function () {
                $scope.initialClic -= 1;
                $scope.pageClic -= 1;
                $scope.infoDataClic();
              };
              $scope.fastbackwardClic = function () {
                $scope.initialClic = 0;
                $scope.pageClic = 1;
                $scope.infoDataClic();
              };
              if ($stateParams.id) {
                $scope.idMail = $stateParams.id;
              } else {
                notificationService.error("Por favor revise la informacion enviada");
              }

              $scope.getAll = function () {
                statisticService.getAllInfoMail($scope.idMail).then(function (data) {
                  $scope.stactics = data;
                  if($scope.stactics.mail.replyto == null){
                    var notAssigned = "No asignado";
                    $scope.stactics.mail.replyto = notAssigned;
                  }
                });
              };
              $scope.calculatePercentage = function (total, value) {
                if (total == 0) {
                  return 0;
                }
                var res = (value / total) * 100;
                if (res % 1 == 0) {
                  return res;
                } else {
                  return res.toFixed(2);
                }
              };
              $scope.clic = function () {
                $scope.initial = 0;
                $scope.page = 1;
                $scope.selectOpen = false;
                $scope.selectClic = true;
                $scope.selectUnsuscribed = false;
                $scope.selectBounced = false;
                $scope.selectSpam = false;
                $scope.selectBuzon = false;
                var type = "column";
                $scope.graph = {};
                $scope.chartConfig = {};
                statisticService.infoClic($scope.idMail).then(function (data) {
                  $scope.graph = data;
                  $scope.graphOpen(type, "#009fb2");
                  $scope.getData();
                });
                $scope.infoDataClic();
              };
              $scope.unsuscribe = function () {
                $scope.initial = 0;
                $scope.page = 1;
                $scope.selectOpen = false;
                $scope.selectClic = false;
                $scope.selectUnsuscribed = true;
                $scope.selectBounced = false;
                $scope.selectSpam = false;
                $scope.selectBuzon = false;
                var type = "column";
                $scope.graph = {};
                $scope.chartConfig = {};
                statisticService.infoUnsuscribed($scope.idMail).then(function (data) {
                  $scope.graph = data;
                  $scope.graphOpen(type, "#777");
                  $scope.getData();
                });
              };
              $scope.spam = function () {
                $scope.initial = 0;
                $scope.page = 1;
                $scope.selectOpen = false;
                $scope.selectClic = false;
                $scope.selectUnsuscribed = false;
                $scope.selectBounced = false;
                $scope.selectSpam = true;
                $scope.selectBuzon = false;
                var type = "column";
                $scope.graph = {};
                $scope.chartConfig = {};
                statisticService.infoSpam($scope.idMail).then(function (data) {
                  $scope.graph = data;
                  $scope.graphOpen(type, "#ff2400");
                  $scope.getData();
                });
              };
              $scope.bounced = function () {
                $scope.initial = 0;
                $scope.page = 1;
                $scope.selectOpen = false;
                $scope.selectClic = false;
                $scope.selectUnsuscribed = false;
                $scope.selectBounced = true;
                $scope.selectSpam = false;
                $scope.selectBuzon = false;
                var type = "pie";
                $scope.graph = {};
                $scope.chartConfig = {};
                statisticService.infoBounced($scope.idMail).then(function (data) {
                  $scope.graphPie = data;
                  $scope.countTotal = parseInt($scope.graphPie[0].hard) + parseInt($scope.graphPie[0].soft);
                  $scope.graphOpenPie(type);
                  $scope.getData();
                });
              };
              $scope.opening = function () {
                $scope.initial = 0;
                $scope.page = 1;
                $scope.selectOpen = true;
                $scope.selectClic = false;
                $scope.selectUnsuscribed = false;
                $scope.selectBounced = false;
                $scope.selectSpam = false;
                $scope.selectBuzon = false;
                var type = "column";
                $scope.graph = {};
                $scope.chartConfig = {};
                statisticService.infoOpen($scope.idMail, $scope.initial).then(function (data) {
                  $scope.graph = data;
                  $scope.graphOpen(type, "#00c1a5");
                  $scope.getData();
                });
              };
              $scope.buzon = function () {
                $scope.initial = 0;
                $scope.page = 1;
                $scope.selectOpen = false;
                $scope.selectClic = false;
                $scope.selectUnsuscribed = false;
                $scope.selectBounced = false;
                $scope.selectSpam = false;
                $scope.selectBuzon = true;
                var type = "column";
                $scope.graph = {};
                $scope.chartConfig = {};
                statisticService.infoBuzon($scope.idMail, $scope.initial).then(function (data) {
                  $scope.graph = data;
                  $scope.graphOpen(type, "#b700c1");
                  $scope.getData();
                });
              };
              $scope.infoDataClic = function () {
                statisticService.infoClicDetail($scope.idMail, $scope.initialClic, $scope.filter.link).then(function (data) {
                  $scope.infolink = data;
                });
              };
              $scope.getData = function () {
                
                var route = "";
                if ($scope.selectOpen) {
                  route = "open";
                } else if ($scope.selectClic) {
                  route = "clic";
                } else if ($scope.selectUnsuscribed) {
                  route = "unsuscribe";
                } else if ($scope.selectBounced) {
                  route = "bounced";
                } else if ($scope.selectSpam) {
                  route = "spam";
                } else if ($scope.selectBuzon) {
                  route = "buzon";
                }
                statisticService.dataInfo($scope.idMail, $scope.initial, route, $scope.filter.selected, $scope.filter.setvalue).then(function (data) {
                  $scope.graph = data;
                });
              };
              $scope.graphOpenPie = function (type) {
                var week = [];
                var day = [];
                $scope.chartConfig = {
                  options: {
                    chart: {
                      type: type
                    },
                    navigator: {
                      enabled: false,
                      series: {
                        data: []
                      }
                    },
                    rangeSelector: {
                      enabled: false
                    },
                    plotOptions: {
                      series: {
                        lineWidth: 1,
                        fillOpacity: 0.5
                      },
                      column: {
                        stacking: 'normal'
                      },
                      area: {
                        stacking: 'normal',
                        marker: {
                          enabled: false
                        }
                      }

                    },
                    exporting: false,
                    xAxis: [{
                        type: 'datetime',
                      }],
                    yAxis: [
                      {// Primary yAxis
                        type: 'datetime',
                        min: 0,
                        allowDecimals: false,
                        title: {
                          text: '',
                          style: {
                            color: '#80a3ca'
                          }
                        },
                        labels: {
                          format: '{value}',
                          style: {
                            color: '#80a3ca'
                          }
                        }
                      },
                    ],
                    legend: {
                      enabled: false
                    },
                    title: {
                      text: ' '
                    },
                    credits: {
                      enabled: false
                    },
                    loading: false,
                    tooltip: {
                      crosshairs: [
                        {
                          width: 1,
                          dashStyle: 'dash',
                          color: '#898989'
                        },
                        {
                          width: 1,
                          dashStyle: 'dash',
                          color: '#898989'
                        }
                      ]

                    }
                  },
                  series: [
                    {
                      name: "Cantidad",
//                colorByPoint: true,
                      data: [
                        {
                          color: "#ff6e00",
                          name: "Rebotes duros",
                          visible: true,
                          y: $scope.graphPie[0].hard},
                        {
                          color: "#f28d41",
                          name: "Rebotes suaves",
                          visible: true,
                          y: $scope.graphPie[0].soft}
                      ]
                    }
                  ]
                };
              };
              $scope.graphOpen = function (type, color) {
                var week = [];
                var day = [];
                $scope.countTotal = 0;
                if ($scope.graph.statics) {
                  var count = 1;
                  angular.forEach($scope.graph.statics, function (value, key) {
                    week.push({drilldown: value.week.week, name: value.week.interval,
                      y: parseInt(value.week.count)});
                    var pp = [];
                    $scope.countTotal = (parseInt(value.week.count) + parseInt($scope.countTotal));
                    angular.forEach(value.week.day, function (value2, key2) {
                      pp.push({name: value2.interval,
                        y: parseInt(value2.count),
                        drilldown: value2.interval});
                      var hour = [];
                      angular.forEach(value2.hour, function (value3, key3) {
                        var asas = [value3.interval, parseInt(value3.count)];
                        hour.push(asas);
                      })
                      day.push({id: value2.interval, name: "Hora(s)", data: hour});
                    })
                    day.push({id: value.week.week, name: "Dia(s)",
                      data: pp});
                  });
                }


                $scope.chartConfig = {
                  options: {
                    chart: {
                      type: type
                    },
                    drilldown: {
                      series: day
                    },
                    xAxis: {
                      type: 'category'
                    },
                    plotOptions: {
                      series: {
                        lineWidth: 1,
                        fillOpacity: 0.5,
                        borderWidth: 0
                      },
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
                  series: [{
                      name: 'Semana(s)',
                      data: week,
                      color: color
                    }]
                };

              };

            }])
          .controller('nodo', ['$scope', 'statisticService', "$stateParams", "notificationService", "$location", "$anchorScroll", '$ocLazyLoad', '$compile', '$stateParams', function ($scope, statisticService, $stateParams, notificationService, $location, $anchorScroll, $ocLazyLoad, $compile, $stateParams) {
              //hacer consulta de la campaÃ±a automatica por el nodo
              statisticService.getAutomaticCampaignByNode($stateParams).then(function (res) {
                //console.log(res);
                $scope.data = res;
                //console.log($scope.data[0].idMail);
                if ($scope.data[0].idMailTemplate != null && angular.isDefined($scope.data[0].idMailTemplate)) {
                }
                if ($scope.data[0].idSmsTemplate != null && angular.isDefined($scope.data[0].idSmsTemplate)) {
                  $scope.url = fullUrlBase + templateBase + '/sms';
                  $scope.loadHchart = function () {
                    $ocLazyLoad.load([
                      'library/drilldown/highstock.src.min.js',
                      'library/highstock/highcharts-ng.js',
                    ]).then(function () {
                      var el, elToAppend;
                      elToAppend = $compile('<highchart  config="chartConfig"  style="min-width: 100%;  margin: 0 auto" ></highchart>')($scope);
                      el = angular.element('#highchart');
                      el.append(elToAppend);
                    }, function (e) {
                      console.log('errr');
                      console.error(e);
                    });
                  }
                  //peticion al service que trae la informacion de estadistica de sms
                  $scope.initial = 0;
                  $scope.page = 1;
                  $scope.traslateStatus = function (status) {
                    var string = "";
                    switch (status) {
                      case "sent":
                        string = "Enviado";
                        break;
                      case "undelivered":
                        string = "No enviado";
                        break;
                    }
                    return string;
                  }
                  $scope.getData = function () {
                    statisticService.getDetailSms($scope.data[0].idSms, $scope.initial).then(function (data) {
                      $scope.listsms = data;
                      $scope.loadHchart();
                      //console.log($scope.listsms);
                    });
                  }
                  $scope.getData();
                  statisticService.getInfoStaticticsSms($scope.data[0].idSms).then(function (res) {
                    $scope.forward = function () {
                      $scope.initial += 1;
                      $scope.page += 1;
                      $scope.getData();
                    };
                    $scope.fastforward = function () {
                      $scope.initial = ($scope.listsms.detail[1].total_pages - 1);
                      $scope.page = $scope.listsms.detail[1].total_pages;
                      $scope.getData();
                    };
                    $scope.backward = function () {
                      $scope.initial -= 1;
                      $scope.page -= 1;
                      $scope.getData();
                    };
                    $scope.fastbackward = function () {
                      $scope.initial = 0;
                      $scope.page = 1;
                      $scope.getData();
                    };
                    $scope.reportSms = function () {
                      statisticService.reportStaticsSms($scope.data[0].idSms).then(function () {
                        var url = fullUrlBase + 'statistic/download'
                        location.href = url;
                      });
                    };
//                    console.log(res);
                    $scope.sms = {};
                    $scope.sms = res;

                    $scope.chartConfig = {
                      options: {
                        colors: ['#00BF6F', '#2DCCD3', '#F79F0F'],
                        chart: {
                          type: 'pie',
                          backgroundColor: null,
                        },
                        tooltip: {
                          formatter: function () {
                            return '<b>Total SMS: </b>' + parseInt($scope.sms.sent + $scope.sms.undelivered) + '<br><b>Enviados: </b>' + parseInt($scope.sms.sent) + '<br><b>No Enviados: </b>' + parseInt($scope.sms.undelivered);
                          }
                        }
                      },
                      series: [{
                          data: [["Enviados", parseInt($scope.sms.sent)], ["No enviados", parseInt($scope.sms.undelivered)]],
                          innerSize: '70%',
                          showInLegend: true,
                          dataLabels: {
                            enabled: false
                          }
                        }],
                      title: {
                        text: 'Estadisticas',
                        style: {
                          color: 'black',
                          fontSize: '16px',
                          fontWeight: 'bold'
                        }
                      },
                      legend: {
                        itemStyle: {
                          fontWeight: 'bold',
                          fontSize: '13px'
                        }
                      },
                      loading: false
                    };

                  })
                }
              });
            }])
          .controller('smscontroller', ['$scope', 'statisticService', "$stateParams", "notificationService", '$ocLazyLoad', '$compile', 'constantStatistic', function ($scope, statisticService, $stateParams, notificationService, $ocLazyLoad, $compile, constantStatistic) {
              
              $scope.title = constantStatistic.Titles.statisSms;
              $scope.phone ="";
              $scope.validateData = false;
              $scope.validatePhone = false;
              $scope.validateSearch = {
                valueInitial : false,
                valueFinal : false,
                search : false
              }
              $scope.misc = {
                totalLote : 0,
                progressbar: false
              }
              
              $ocLazyLoad.load([
                'library/drilldown/highstock.src.min.js',
                'library/highstock/highcharts-ng.js',
              ]).then(function () {
                if (typeof Highcharts != "undefined") {
                  $ocLazyLoad.load([
                    'library/drilldown/drilldown.js',
                  ]).then(function () {
                    var el, elToAppend;
                    elToAppend = $compile('<highchart  config="chartConfig"  style="min-width: 100%;  margin: 0 auto" ></highchart>')($scope);
                    el = angular.element('#highchart');
                    el.append(elToAppend);
                  });
                }

              }, function (e) {
                console.log('errr');
                console.error(e);
              });
              $scope.getData = function () { 
                statisticService.getDetailSms($scope.idSms, $scope.initial, $scope.phone).then(function (data) {
                  if(data.detail[0]!=null){
                    $scope.misc.totalLote = data.detail[0].length;                  
                    
                    if($scope.phone != '' && $scope.misc.totalLote == 0 && $scope.validateSearch.search == true){

                        $scope.validateData = true;
                        $scope.validatePhone = true;
                        console.log("AQUI -1",$scope.validatePhone); 
                    }else if($scope.phone != '' && $scope.misc.totalLote != 0 && $scope.validateSearch.search == true){

                        $scope.validateData = true;
                        $scope.validatePhone = false;
                        console.log("AQUI -2",$scope.validateData);                                             
                    }else if( $scope.misc.totalLote != 0 && $scope.phone == '' && $scope.validateSearch.search == false ){
                        
                        $scope.validateData = true;
                        $scope.validatePhone = false;
                        console.log("AQUI -3",$scope.validateData);
                    }else if( $scope.misc.totalLote == 0 && $scope.phone == '' && $scope.validateSearch.search == false ){
                        
                        $scope.validateData = false;
                        $scope.validatePhone = false;
                        console.log("AQUI -4",$scope.validateData);
                    }
                    
                    $scope.misc.progressbar = true;
                    $scope.listsms = data;
                  }
                  else{
                    $scope.misc.progressbar = true;
                    $scope.validateData = true;
                    $scope.validatePhone = true;
                  }
                  
                });
              };

              $scope.forward = function () {
                $scope.initial += 1;
                $scope.page += 1;
                $scope.getData();
              };
              $scope.fastforward = function () {
                $scope.initial = ($scope.listsms.detail[1].total_pages - 1);
                $scope.page = $scope.listsms.detail[1].total_pages;
                $scope.getData();
              };
              $scope.backward = function () {
                $scope.initial -= 1;
                $scope.page -= 1;
                $scope.getData();
              };
              $scope.fastbackward = function () {
                $scope.initial = 0;
                $scope.page = 1;
                $scope.getData();
              };

              $scope.reportSms = function () {
                statisticService.reportStaticsSms($scope.idSms, $scope.title).then(function () {
                  var url = fullUrlBase + 'statistic/downloadexcel/' + $scope.title;
                  location.href = url;
                });
              };
              
              $scope.searchNumber = function (param) {
                if(param == 1){
                    
                    if($scope.phone != ''){
                        $scope.listsms.detail[0] = null;
                        $scope.validateSearch.search = true;
                        $scope.getData();   
                    }else{
                        $scope.validateData = true;
                        $scope.validatePhone = false;
                        $scope.listsms.detail[0] = null;
                        $scope.getData();
                    }
                                                     
                }else if (param == 2){
                    $scope.phone = "";
                    $scope.validateSearch.search = false;
                    $scope.getData();  
                }

              };
              $scope.initial = 0;
              $scope.page = 1;
              $scope.traslateStatus = function (status) {
                var string = "";
                switch (status) {
                  case "sent":
                    string = "Enviado";
                    break;
                  case "undelivered":
                    string = "No enviado";
                    break;
                }
                return string;
              }

              if ($stateParams.id) {
                $scope.idSms = $stateParams.id;
              } else {
                notificationService.error("Por favor revise la informacion enviada");
              }
              $scope.getData();
              statisticService.getInfoSms($scope.idSms).then(function (data) {
                //En caso de que no existan registros en lote
                if(data.sms.target==""||data.sms.target==null){
                  data.sms.target = 0;
                }
                $scope.sms = data;
                $scope.chartConfig = {
                  options: {
                    colors: ['#00BF6F', '#2DCCD3', '#F79F0F'],
                    chart: {
                      type: 'pie',
                      backgroundColor: null,
                    },
                    tooltip: {
                      formatter: function () {
                        return '<b>Total SMS:</b>' + parseInt($scope.sms.sent + $scope.sms.undelivered) + '<br><b>Enviados:</b>' + parseInt($scope.sms.sent) + '<br><b>No Enviados</b>' + parseInt($scope.sms.undelivered);
                      }
                    }
                  },
                  series: [{
                      data: [["Enviados", parseInt($scope.sms.sent)], ["No enviados", parseInt($scope.sms.undelivered)]],
                      innerSize: '70%',
                      showInLegend: true,
                      dataLabels: {
                        enabled: false
                      }
                    }],
                  title: {
                    text: 'Estadisticas',
                    style: {
                      color: 'black',
                      fontSize: '16px',
                      fontWeight: 'bold'
                    }
                  },
                  legend: {
                    itemStyle: {
                      fontWeight: 'bold',
                      fontSize: '13px'
                    }
                  },
                  loading: false
                };
              });
            }])
          .controller('smssharecontroller', ['$scope', 'statisticService', "$stateParams", "notificationService", function ($scope, statisticService, $stateParams, notificationService) {
              $scope.type = type;
              $scope.reportSms = function () {
                statisticService.reportStaticsSms($scope.idSms).then(function () {
                  var url = fullUrlBase + 'statistic/download'
                  location.href = url;
                });
              };
              $scope.initial = 0;
              $scope.page = 1;
              $scope.traslateStatus = function (status) {
                var string = "";
                switch (status) {
                  case "sent":
                    string = "Enviado";
                    break;
                  case "undelivered":
                    string = "No enviado";
                    break;
                }
                return string;
              }

              if (idSms) {
                $scope.idSms = idSms;
              } else {
                notificationService.error("Por favor revise la informacion enviada");
              }
              if ($scope.type == "complete") {
                statisticService.getDetailSms($scope.idSms, $scope.initial).then(function (data) {
                  $scope.listsms = data;
                });
              }
              statisticService.getInfoSms($scope.idSms).then(function (data) {
                $scope.sms = data;
                $scope.chartConfig = {
                  options: {
                    chart: {
                      type: 'pie'
                    }
                  },
                  series: [{
                      data: [["Enviados", parseInt($scope.sms.sent)], ["No enviados", parseInt($scope.sms.undelivered)]]
                    }],
                  title: {
                    text: 'Estadisticas'
                  },
                  loading: false
                };
              });
            }])
          .controller('shareController', ['$scope', 'statisticService', "$stateParams", "notificationService", "$location", "$anchorScroll", '$ocLazyLoad', '$compile', function ($scope, statisticService, $stateParams, notificationService, $location, $anchorScroll, $ocLazyLoad, $compile) {

              $ocLazyLoad.load([
                fullUrlBase + 'library/highstock/highcharts-ng.js',
                fullUrlBase + 'library/drilldown/highstock.src.min.js',
              ]).then(function () {
                if (typeof Highcharts != "undefined") {
                  $ocLazyLoad.load([
                    fullUrlBase + 'library/drilldown/drilldown.js',
                  ]).then(function () {
                    var text = '<highchart config="chartConfig"  style="min-width: 100%;  margin: 0 auto" ></highchart>';
                    var elToAppendOpen = $compile(text)($scope);
                    var elToAppendClic = $compile(text)($scope);
                    var elToAppendUnsus = $compile(text)($scope);
                    var elToAppendBoun = $compile(text)($scope);
                    var elToAppendSpam = $compile(text)($scope);
                    var elToAppendBuzon = $compile(text)($scope);

                    $('#highchartOpen').append(elToAppendOpen);
                    $('#highchartClic').append(elToAppendClic);
                    $('#highchartUnsuscribe').append(elToAppendUnsus);
                    $('#highchartBounced').append(elToAppendBoun);
                    $('#highchartSpam').append(elToAppendSpam);
                    $('#highchartBuzon').append(elToAppendBuzon);
                    $scope.getAll();
                    $scope.opening();
                  });
                }
              }, function (e) {
                console.log('errr');
                console.error(e);
              });

              $scope.idMail = idMail;
              $scope.type = type;
              $scope.getAll = function () {
                statisticService.getAllInfoMail($scope.idMail, $scope.type).then(function (data) {
                  $scope.stactics = data;
                  if($scope.stactics.mail.replyto == null){
                    var notAssigned = "No asignado";
                    $scope.stactics.mail.replyto = notAssigned;
                  }
                });
              };

              $scope.opening = function () {
                $scope.initial = 0;
                var type = "column";
                $scope.graph = {};
                $scope.chartConfig = {};
                statisticService.infoOpen($scope.idMail, $scope.initial).then(function (data) {
                  $scope.graph = data;
                  $scope.graphOpen(type, "#00c1a5");
                });
              };

              $scope.clic = function () {
                var type = "column";
                $scope.graph = {};
                $scope.chartConfig = {};
                statisticService.infoClic($scope.idMail).then(function (data) {
                  $scope.graph = data;
                  $scope.graphOpen(type, "#009fb2");
                });
//          $scope.infoDataClic();
              };

              $scope.unsuscribe = function () {
                var type = "column";
                $scope.graph = {};
                $scope.chartConfig = {};
                statisticService.infoUnsuscribed($scope.idMail).then(function (data) {
                  $scope.graph = data;
                  $scope.graphOpen(type, "#777");
                });
              };

              $scope.bounced = function () {
                var type = "pie";
                $scope.graph = {};
                $scope.chartConfig = {};
                statisticService.infoBounced($scope.idMail).then(function (data) {
                  $scope.graphPie = data;
                  $scope.countTotal = parseInt($scope.graphPie[0].hard) + parseInt($scope.graphPie[0].soft);
                  $scope.graphOpenPie(type);
                });
              };

              $scope.spam = function () {
                var type = "column";
                $scope.graph = {};
                $scope.chartConfig = {};
                statisticService.infoSpam($scope.idMail).then(function (data) {
                  $scope.graph = data;
                  $scope.graphOpen(type, "#ff2400");
                });
              };

              $scope.goOpen = function () {
                $location.hash('open');
                $anchorScroll();
                $scope.opening();
              };
              $scope.goClic = function () {
                $location.hash('clic');
                $anchorScroll();
                $scope.clic();
              };
              $scope.goUnsuscribe = function () {
                $location.hash('unsuscribe');
                $anchorScroll();
                $scope.unsuscribe();
              };
              $scope.goBounced = function () {
                $location.hash('bounced');
                $anchorScroll();
                $scope.bounced();
              };
              $scope.goSpam = function () {
                $location.hash('spam');
                $anchorScroll();
                $scope.spam();
              };
              $scope.goBuzon = function () {
                $location.hash('buzon');
                $anchorScroll();
                $scope.buzon();
              };

              $scope.calculatePercentage = function (total, value) {
                if (total == 0) {
                  return 0;
                }
                var res = (value / total) * 100;
                if (res % 1 == 0) {
                  return res;
                } else {
                  return res.toFixed(2);
                }
              };

              $scope.graphOpen = function (type, color) {
                var week = [];
                var day = [];
                $scope.countTotal = 0;
                if ($scope.graph.statics) {
                  var count = 1;
                  angular.forEach($scope.graph.statics, function (value, key) {
                    week.push({drilldown: value.week.week, name: value.week.interval,
                      y: parseInt(value.week.count)});
                    var pp = [];
                    $scope.countTotal = (parseInt(value.week.count) + parseInt($scope.countTotal));
                    angular.forEach(value.week.day, function (value2, key2) {
                      pp.push({name: value2.interval,
                        y: parseInt(value2.count),
                        drilldown: value2.interval});
                      var hour = [];
                      angular.forEach(value2.hour, function (value3, key3) {
                        var asas = [value3.interval, parseInt(value3.count)];
                        hour.push(asas);
                      })
                      day.push({id: value2.interval, name: "Hora(s)", data: hour});
                    })
                    day.push({id: value.week.week, name: "Dia(s)",
                      data: pp});
                  });
                }

                $scope.chartConfig = {
                  options: {
                    chart: {
                      type: type
                    },
                    drilldown: {
                      series: day
                    },
                    xAxis: {
                      type: 'category'
                    },
                    plotOptions: {
                      series: {
                        lineWidth: 1,
                        fillOpacity: 0.5,
                        borderWidth: 0
                      },
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
                  series: [{
                      name: 'Semana(s)',
                      data: week,
                      color: color
                    }]
                };
              };

              $scope.graphOpenPie = function (type) {
                var week = [];
                var day = [];
                $scope.chartConfig = {
                  options: {
                    chart: {
                      type: type
                    },
                    navigator: {
                      enabled: false,
                      series: {
                        data: []
                      }
                    },
                    rangeSelector: {
                      enabled: false
                    },
                    plotOptions: {
                      series: {
                        lineWidth: 1,
                        fillOpacity: 0.5
                      },
                      column: {
                        stacking: 'normal'
                      },
                      area: {
                        stacking: 'normal',
                        marker: {
                          enabled: false
                        }
                      }

                    },
                    exporting: false,
                    xAxis: [{
                        type: 'datetime',
                      }],
                    yAxis: [
                      {// Primary yAxis
                        type: 'datetime',
                        min: 0,
                        allowDecimals: false,
                        title: {
                          text: '',
                          style: {
                            color: '#80a3ca'
                          }
                        },
                        labels: {
                          format: '{value}',
                          style: {
                            color: '#80a3ca'
                          }
                        }
                      },
                    ],
                    legend: {
                      enabled: false
                    },
                    title: {
                      text: ' '
                    },
                    credits: {
                      enabled: false
                    },
                    loading: false,
                    tooltip: {
                      crosshairs: [
                        {
                          width: 1,
                          dashStyle: 'dash',
                          color: '#898989'
                        },
                        {
                          width: 1,
                          dashStyle: 'dash',
                          color: '#898989'
                        }
                      ]

                    }
                  },
                  series: [
                    {
                      name: "Cantidad",
//                colorByPoint: true,
                      data: [
                        {
                          color: "#ff6e00",
                          name: "Rebotes duros",
                          visible: true,
                          y: $scope.graphPie[0].hard},
                        {
                          color: "#f28d41",
                          name: "Rebotes suaves",
                          visible: true,
                          y: $scope.graphPie[0].soft}
                      ]
                    }
                  ]
                };
              };

//              $scope.getAll();
//              $scope.opening();
            }])
          .controller('surveycontroller', ['$scope', 'statisticService', "$stateParams", "notificationService", "$location", "$anchorScroll", '$builder', '$validator', '$timeout', '$ocLazyLoad', '$compile', 'constantStatistic', function ($scope, statisticService, $stateParams, notificationService, $location, $anchorScroll, $builder, $validator, $timeout, $ocLazyLoad, $compile, constantStatistic) {

              $scope.chartConfig = "";
              $scope.title = constantStatistic.Titles.statisSurvey;

              $scope.chartBar = function (question, totalSurvey) {
                //console.log(question, totalSurvey);
                question.chartConfig = {
                  options: {
                    chart: {
                      type: "bar"
                    },
                    xAxis: {
                      type: 'category'
                    },
                    yAxis: {
                      labels: {
                        formatter: function () {
                          var pcnt = (this.value / totalSurvey) * 100;
                          return Highcharts.numberFormat(pcnt, 0, ',') + '%';
                        }
                      }
                    },
                    plotOptions: {
                      series: {
                        lineWidth: 1,
                        fillOpacity: 0.5,
                        borderWidth: 0,
                        dataLabels: {
                          enabled: true,
                          formatter: function () {
                            var pcnt = (this.y / totalSurvey) * 100;
                            return Highcharts.numberFormat(pcnt) + '%';
                          }
                        }
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
                  series: [question.chart]
                          /*[{
                           name: 'Te gustan los hombres',
                           data:[{name:"si",y:6},{name:"no",y:2}]
                           }]*/
                };
              };


              function getAll() {
                statisticService.getAllInfoSurvey($stateParams.id).then(function (data) {
                  $scope.stactics = data;
                  $scope.loadFile();

                  //$scope.chartBar($scope.stactics.questions[3].chart)
                });
              }

              getAll();

              $scope.translationType = function (type) {
                var string = "";
                switch (type) {
                  case 'contact' :
                    string = "Contacto";
                    break;
                  case 'public' :
                    string = "Publica";
                    break;
                }
                return string;
              };

              $scope.previsualizar = function (content) {
                $scope.previewShow = false;
                var json = JSON.parse(content);
                //console.log(json.content);
                $builder.setForm('sigmaSurvey', json.content);
                $("#preview").addClass('dialog--open');
                $scope.backgroundForm = json.background;

                $timeout(function () {
                  $scope.previewShow = true;
                }, 1000);
              };

              $scope.removeDialog = function (modal) {
                $("#" + modal).removeClass('dialog--open');
              };

              $scope.validateSurvey = function () {
                $validator.validate($scope, 'sigmaSurvey')
                        .success(function () {})
                        .error(function () {});
              };

              $scope.reportDetail = function () {
                statisticService.reportStaticsSurvey($stateParams.id, $scope.title).then(function (res) {
                  var url = fullUrlBase + 'statistic/downloadexcel/' + $scope.title;
                  location.href = url;
                })
              };

              $scope.printDetail = function () {
                window.print();
              };

              $scope.back = function () {
                var url = fullUrlBase + 'survey#/';
                location.href = url;
              };
              $scope.loadFile = function () {
                $ocLazyLoad.load([
                  'library/drilldown/highstock.src.min.js',
                  'library/highstock/highcharts-ng.js',
                ]).then(function () {
                  if (typeof Highcharts != "undefined") {
                    $ocLazyLoad.load([
                      'library/drilldown/drilldown.js',
                    ]).then(function () {
                      $scope.showDom = true;
                    });
                  }
                }, function (e) {
                  console.log('errr');
                  console.error(e);
                });
              }
            }])
          .controller('automaticcampaigncontroller', ['$scope', 'statisticService', "$stateParams", "notificationService", "$location", "$anchorScroll", function ($scope, statisticService, $stateParams, notificationService, $location, $anchorScroll) {
//              $scope.chartViewModel = {"nodes": [{"name": "Destinatario(s)", "id": 0, "x": 170, "y": 25, "width": 100, "image": "http:\/\/localhost\/\/aio\/images\/automatic\/destinatariosa-01.jpg", "theme": "primary", "method": "primary", "templatepopover": "http:\/\/localhost\/\/aio\/flowchart\/popoversegment", "titlepopover": "Selecciona a quien quiere enviar esta campa\u00f1a autom\u00e1tica.", "outputConnectors": [{"name": ""}], "sendData": {"list": {"id": 1, "name": "Listas de contactos"}, "selecteds": [{"idContactlist": "125", "name": "Desarrollo", "idContactlistCategory": "2"}], "textTitle": "Listas de contactos", "text": "Desarrollo"}, "element": {"0": {"jQuery111204709562130347751": 79}, "context": {"jQuery111204709562130347751": 79}, "length": 1}, "showPopover": false, "dataForm": {"hrefCreateMail": "http:\/\/localhost\/\/aio\/mailtemplate#\/create", "hrefCreateSms": "http:\/\/localhost\/\/aio\/smstemplate#\/create", "hrefCreateSurvey": "http:\/\/localhost\/\/aio\/survey\/create#\/basicinformation\/", "list": {"id": 1, "name": "Listas de contactos"}, "selected": [{"idContactlist": "125", "name": "Desarrollo", "idContactlistCategory": "2"}], "error": false}}, {"name": "Mail", "id": 10, "x": 659, "y": 31, "width": 100, "theme": "service", "method": "email", "image": "http:\/\/localhost\/\/aio\/images\/automatic\/correoa-01.jpg", "templatepopover": "http:\/\/localhost\/\/aio\/flowchart\/popovermail", "templatepopoveraction": "http:\/\/localhost\/\/aio\/flowchart\/popoveraction", "inputConnectors": [{"name": ""}], "outputConnectors": [{"name": ""}], "sendData": {"mailtemplate": {"idMailTemplate": "24", "name": "Prueba"}, "subject": "prueba survey local", "senderEmail": {"idEmailsender": "9", "email": "kevin@roldan.com"}, "senderName": {"idNameSender": "17", "name": "Kevin Roldan"}, "mailcategory": {"name": "Musica", "idMailCategory": "5", "description": null, "deleted": "0"}, "replyto": "", "text": "Categoria:Musica", "textTitle": "Prueba"}, "element": {"0": {"jQuery111204709562130347751": 569}, "context": {"jQuery111204709562130347751": 569}, "length": 1}, "showPopover": false, "dataForm": {"hrefCreateMail": "http:\/\/localhost\/\/aio\/mailtemplate#\/create", "hrefCreateSms": "http:\/\/localhost\/\/aio\/smstemplate#\/create", "hrefCreateSurvey": "http:\/\/localhost\/\/aio\/survey\/create#\/basicinformation\/", "mailtemplate": {"idMailTemplate": "24", "name": "Prueba"}, "flagSelected": true, "hrefSelectedMail": "http:\/\/localhost\/\/aio\/mailtemplate\/edit\/24", "mailcategory": {"name": "Musica", "idMailCategory": "5", "description": null, "deleted": "0"}, "senderName": {"idNameSender": "17", "name": "Kevin Roldan"}, "senderEmail": {"idEmailsender": "9", "email": "kevin@roldan.com"}, "subject": "prueba survey local", "error": false}}, {"name": "Tiempo", "id": 11, "x": 173, "y": 182, "width": 100, "theme": "operator", "method": "time", "image": "http:\/\/localhost\/\/aio\/images\/automatic\/tiempoa-01.jpg", "templatepopover": "http:\/\/localhost\/\/aio\/flowchart\/popovertime", "inputConnectors": [{"name": ""}], "outputConnectors": [{"name": ""}], "sendData": {"time": {"id": 1, "name": "1"}, "timetwo": {"id": 2, "name": "Hora(s)"}, "text": "1 Hora(s) Despu\u00e9s.", "textTitle": "Tiempo Programado"}, "element": {"0": {"jQuery111204709562130347751": 531}, "context": {"jQuery111204709562130347751": 531}, "length": 1}, "showPopover": false, "dataForm": {"hrefCreateMail": "http:\/\/localhost\/\/aio\/mailtemplate#\/create", "hrefCreateSms": "http:\/\/localhost\/\/aio\/smstemplate#\/create", "hrefCreateSurvey": "http:\/\/localhost\/\/aio\/survey\/create#\/basicinformation\/", "time": {"id": 1, "name": "1"}, "timetwo": {"id": 2, "name": "Hora(s)"}, "error": false}}, {"name": "Encuestas", "id": 13, "x": 653, "y": 175, "width": 100, "theme": "service", "method": "survey", "image": "http:\/\/localhost\/\/aio\/images\/general\/forms.png", "templatepopover": "http:\/\/localhost\/\/aio\/flowchart\/popoversurvey", "templatepopoveraction": "http:\/\/localhost\/\/aio\/flowchart\/popoveractionsurvey", "inputConnectors": [{"name": ""}], "outputConnectors": [{"name": ""}], "sendData": {"publicsurvey": {"idSurvey": "60", "name": "Prueb Asurvey"}, "mailtemplate": {"idMailTemplate": "24", "name": "Prueba"}, "subject": "yguj", "senderEmail": {"idEmailsender": "18", "email": "kevin@roldan1.com"}, "senderName": {"idNameSender": "28", "name": "Pablo"}, "mailcategory": {"name": "Musica", "idMailCategory": "5", "description": null, "deleted": "0"}, "replyto": "", "text": "Categoria:Musica", "textTitle": "Prueba"}, "element": {"0": {"jQuery111204709562130347751": 446}, "context": {"jQuery111204709562130347751": 446}, "length": 1}, "showPopover": false, "dataForm": {"hrefCreateMail": "http:\/\/localhost\/\/aio\/mailtemplate#\/create", "hrefCreateSms": "http:\/\/localhost\/\/aio\/smstemplate#\/create", "hrefCreateSurvey": "http:\/\/localhost\/\/aio\/survey\/create#\/basicinformation\/", "publicsurvey": {"idSurvey": "60", "name": "Prueb Asurvey"}, "flagSelected": true, "hrefSelectedSurvey": "http:\/\/localhost\/\/aio\/survey\/create#\/basicinformation\/60", "mailtemplate": {"idMailTemplate": "24", "name": "Prueba"}, "hrefSelectedMail": "http:\/\/localhost\/\/aio\/mailtemplate\/edit\/24", "mailcategory": {"name": "Musica", "idMailCategory": "5", "description": null, "deleted": "0"}, "senderName": {"idNameSender": "28", "name": "Pablo"}, "senderEmail": {"idEmailsender": "18", "email": "kevin@roldan1.com"}, "subject": "yguj", "error": false}}, {"name": "Accion", "id": 14, "x": 175, "y": 351, "width": 100, "theme": "operator", "method": "actions", "image": "http:\/\/localhost\/\/aio\/images\/automatic\/accion-01.jpg", "templatepopover": "http:\/\/localhost\/\/aio\/flowchart\/popoveraction", "inputConnectors": [{"name": ""}], "outputConnectors": [{"name": ""}, {"name": ""}], "sendData": {"selectAction": {"id": 6, "name": "Respuesta"}, "time": {"id": 1, "name": "1"}, "timetwo": {"id": 2, "name": "Hora(s)"}, "quest": {"idQuestion": "1497027528226", "question": "animal favorito"}, "condition": {"id": 1, "name": "Igual a"}, "answer": {"idAnswer": 1129, "answer": "gato"}, "text": "Respuesta 1 Hora(s) Despu\u00e9s.", "textTitle": "Tiempo Programado"}, "element": {"0": {"jQuery111204709562130347751": 2553}, "context": {"jQuery111204709562130347751": 2553}, "length": 1}, "showPopover": false, "dataForm": {"hrefCreateMail": "http:\/\/localhost\/\/aio\/mailtemplate#\/create", "hrefCreateSms": "http:\/\/localhost\/\/aio\/smstemplate#\/create", "hrefCreateSurvey": "http:\/\/localhost\/\/aio\/survey\/create#\/basicinformation\/", "selectAction": {"id": 6, "name": "Respuesta"}, "time": {"id": 1, "name": "1"}, "timetwo": {"id": 2, "name": "Hora(s)"}, "quest": {"idQuestion": "1497027528226", "question": "animal favorito"}, "condition": {"id": 1, "name": "Igual a"}, "answer": {"idAnswer": 1129, "answer": "gato"}, "error": false}}, {"name": "Sms", "id": 15, "x": 648, "y": 429, "width": 100, "theme": "service", "method": "sms", "image": "http:\/\/localhost\/\/aio\/images\/automatic\/SMSA-01.jpg", "templatepopover": "http:\/\/localhost\/\/aio\/flowchart\/popoversms", "inputConnectors": [{"name": ""}], "outputConnectors": [{"name": ""}], "sendData": {"smstemplate": {"idSmsTemplate": "23", "idSmsTemplateCategory": "27", "idAccount": "32", "name": "Sms Con Campos Personalizados", "content": "Hola %%NOMBRE%% %%APELLIDO%% se le informa que se acabo la campana automatica, gracias por participar.%%EDAD%%", "created": "2017-01-06", "updated": "2017-01-20", "createdBy": "kevin.ramirez@sigmamovil.com", "updatedBy": "kevin.ramirez@sigmamovil.com"}, "smscategory": {"idSmsCategory": "32", "idAccount": "32", "name": "Xz<z", "description": "<zx<<x", "createdBy": "kevin.ramirez@sigmamovil.com", "updatedBy": "kevin.ramirez@sigmamovil.com"}, "text": "Categoria:Xz<z", "textTitle": "Sms Con Campos Personalizados"}, "element": {"0": {"jQuery111204709562130347751": 3348}, "context": {"jQuery111204709562130347751": 3348}, "length": 1}, "showPopover": false, "dataForm": {"hrefCreateMail": "http:\/\/localhost\/\/aio\/mailtemplate#\/create", "hrefCreateSms": "http:\/\/localhost\/\/aio\/smstemplate#\/create", "hrefCreateSurvey": "http:\/\/localhost\/\/aio\/survey\/create#\/basicinformation\/", "smstemplate": {"idSmsTemplate": "23", "idSmsTemplateCategory": "27", "idAccount": "32", "name": "Sms Con Campos Personalizados", "content": "Hola %%NOMBRE%% %%APELLIDO%% se le informa que se acabo la campana automatica, gracias por participar.%%EDAD%%", "created": "2017-01-06", "updated": "2017-01-20", "createdBy": "kevin.ramirez@sigmamovil.com", "updatedBy": "kevin.ramirez@sigmamovil.com"}, "flagSelected": true, "hrefSelectedSms": "http:\/\/localhost\/\/aio\/smstemplate#\/edit\/23", "smscategory": {"idSmsCategory": "32", "idAccount": "32", "name": "Xz<z", "description": "<zx<<x", "createdBy": "kevin.ramirez@sigmamovil.com", "updatedBy": "kevin.ramirez@sigmamovil.com"}, "error": false}}, {"name": "Sms", "id": 16, "x": 275, "y": 540, "width": 100, "theme": "service", "method": "sms", "image": "http:\/\/localhost\/\/aio\/images\/automatic\/SMSA-01.jpg", "templatepopover": "http:\/\/localhost\/\/aio\/flowchart\/popoversms", "inputConnectors": [{"name": ""}], "outputConnectors": [{"name": ""}], "sendData": {"smstemplate": {"idSmsTemplate": "23", "idSmsTemplateCategory": "27", "idAccount": "32", "name": "Sms Con Campos Personalizados", "content": "Hola %%NOMBRE%% %%APELLIDO%% se le informa que se acabo la campana automatica, gracias por participar.%%EDAD%%", "created": "2017-01-06", "updated": "2017-01-20", "createdBy": "kevin.ramirez@sigmamovil.com", "updatedBy": "kevin.ramirez@sigmamovil.com"}, "smscategory": {"idSmsCategory": "33", "idAccount": "32", "name": "Hola Mund", "description": "Sin descripci\u00f3n", "createdBy": "ricardo.mayorga@sigmamovil.com", "updatedBy": "ricardo.mayorga@sigmamovil.com"}, "text": "Categoria:Hola Mund", "textTitle": "Sms Con Campos Personalizados"}, "element": {"0": {"jQuery111204709562130347751": 3236}, "context": {"jQuery111204709562130347751": 3236}, "length": 1}, "showPopover": false, "dataForm": {"hrefCreateMail": "http:\/\/localhost\/\/aio\/mailtemplate#\/create", "hrefCreateSms": "http:\/\/localhost\/\/aio\/smstemplate#\/create", "hrefCreateSurvey": "http:\/\/localhost\/\/aio\/survey\/create#\/basicinformation\/", "smstemplate": {"idSmsTemplate": "23", "idSmsTemplateCategory": "27", "idAccount": "32", "name": "Sms Con Campos Personalizados", "content": "Hola %%NOMBRE%% %%APELLIDO%% se le informa que se acabo la campana automatica, gracias por participar.%%EDAD%%", "created": "2017-01-06", "updated": "2017-01-20", "createdBy": "kevin.ramirez@sigmamovil.com", "updatedBy": "kevin.ramirez@sigmamovil.com"}, "flagSelected": true, "hrefSelectedSms": "http:\/\/localhost\/\/aio\/smstemplate#\/edit\/23", "smscategory": {"idSmsCategory": "33", "idAccount": "32", "name": "Hola Mund", "description": "Sin descripci\u00f3n", "createdBy": "ricardo.mayorga@sigmamovil.com", "updatedBy": "ricardo.mayorga@sigmamovil.com"}, "error": false}}], "connections": [{"source": {"nodeID": 0, "connectorIndex": 0}, "dest": {"nodeID": 10, "connectorIndex": 0}, "class": null, "sendData": {}, "dataForm": {}}, {"source": {"nodeID": 10, "connectorIndex": 0}, "dest": {"nodeID": 11, "connectorIndex": 0}, "class": null, "sendData": {}, "dataForm": {}}, {"source": {"nodeID": 11, "connectorIndex": 0}, "dest": {"nodeID": 13, "connectorIndex": 0}, "class": null, "sendData": {}, "dataForm": {}}, {"source": {"nodeID": 13, "connectorIndex": 0}, "dest": {"nodeID": 14, "connectorIndex": 0}, "class": null, "sendData": {}, "dataForm": {}}, {"source": {"nodeID": 14, "connectorIndex": 1}, "dest": {"nodeID": 15, "connectorIndex": 0}, "class": "success", "sendData": {}, "dataForm": {}}, {"source": {"nodeID": 14, "connectorIndex": 0}, "dest": {"nodeID": 16, "connectorIndex": 0}, "class": "negation", "sendData": {"selectAction": {"id": 6, "name": "Respuesta"}, "time": {"id": 1, "name": "1"}, "timetwo": {"id": 2, "name": "Hora(s)"}, "quest": {"idQuestion": "1497027528226", "question": "animal favorito"}, "condition": {"id": 1, "name": "Igual a"}, "answer": {"idAnswer": 1129, "answer": "gato"}, "text": "1 Hora(s)Despu\u00e9s.", "textTitle": "Tiempo Programado"}, "dataForm": {"hrefCreateMail": "http:\/\/localhost\/\/aio\/mailtemplate#\/create", "hrefCreateSms": "http:\/\/localhost\/\/aio\/smstemplate#\/create", "hrefCreateSurvey": "http:\/\/localhost\/\/aio\/survey\/create#\/basicinformation\/", "selectAction": {"id": 6, "name": "Respuesta"}, "time": {"id": 1, "name": "1"}, "timetwo": {"id": 2, "name": "Hora(s)"}, "quest": {"idQuestion": "1497027528226", "question": "animal favorito"}, "condition": {"id": 1, "name": "Igual a"}, "answer": {"idAnswer": 1129, "answer": "gato"}, "error": false}}]});

              if ($stateParams.id) {
                $scope.idAutomaticcampaign = $stateParams.id;
              } else {
                notificationService.error("Por favor revise la informacion enviada");
              }
              $scope.getAllconfiguration = function () {
                statisticService.getAllIconfiguration($scope.idAutomaticcampaign).then(function (data) {
                  $scope.chartViewModel = new flowchart.ChartViewModel(data);

                });
              };
              $scope.getAllconfiguration();
            }])
          .controller('smstwowaycontroller', ['$scope', 'statisticService', "$stateParams", "notificationService", '$q', '$ocLazyLoad', '$compile', 'constantStatistic', function ($scope, statisticService, $stateParams, notificationService, $q, $ocLazyLoad, $compile, constantStatistic) {

              $scope.title = constantStatistic.Titles.statisSmstwoway;

              $ocLazyLoad.load([
                'library/drilldown/highstock.src.min.js',
                'library/highstock/highcharts-ng.js',
              ]).then(function () {
                if (typeof Highcharts != "undefined") {
                  $ocLazyLoad.load([
                    'library/drilldown/drilldown.js',
                  ]).then(function () {
                    var el, elToAppend;
                    elToAppend = $compile('<highchart  config="misc.chartConfig"  style="min-width: 100%;  margin: 0 auto" ></highchart>')($scope);
                    el = angular.element('#highchart');
                    el.append(elToAppend);
                  });
                }
              }, function (e) {
                console.log('errr');
                console.error(e);
              });
              //Data
              $scope.data = {};
              $scope.data.initial = 0;
              $scope.data.page = 1;
              $scope.data.filter = "";
              //Misc
              $scope.misc = {};

              //Services peticion Initial
              $scope.misc.arrSerInitial = [statisticService.getDetailSmsTwoWay, statisticService.getAllSmsTwoway];

              if ($stateParams.id != "" && typeof $stateParams.id != "undefined") {
                $scope.data.idSmsTwoway = $stateParams.id;
              }

              // function universal
              $scope.functions = {
                initial: function () {
                  $scope.functions.showModalLoading();
                  $scope.funRestServices.getListPage();
                  $scope.funRestServices.getInfoPage();
                  $scope.funRestServices.getDetailSms();
                },
                setDetail: function (data) {
                  $scope.data.detail = data;
                },
                setList: function (data) {
                  //console.log(data);
                  $scope.data.list = data;
                }, 
                setHighChart: function () {
                  var arrStatitics = [];
                  angular.forEach($scope.data.detail, function (value, key) {
                    if (value.userResponseGroup == null) {
                      value.userResponseGroup = "Sin responder"
                    }
                    var arrFor = [value.userResponseGroup, parseInt(value.count)];
                    arrStatitics.push(arrFor);
                  });
                  //console.log(arrStatitics);
                  $scope.misc.chartConfig = {
                    options: {
                      chart: {
                        type: 'pie'
                      },
                      tooltip: {
                        formatter: function () {
                          return '<b>Cantidad Total:</b>' + this.total + '<br><b>Cantidad Grupo:</b>' + this.y + '<br><b>' + this.point.name + '</b>: ' + (this.y / this.total * 100) + ' %';
                        }
                      }
                    },
                    series: [{
                        data: arrStatitics,
                        innerSize: '70%',
                        showInLegend: true,
                        dataLabels: {
                          enabled: false
                        }
                      }],
                    title: {
                      text: 'Estadisticas'
                    },
                    loading: false
                  };
                }, 
                setSms: function (data) {
                  $scope.data.sms = data;
                },
                forward: function () {
                  $scope.data.initial += 1;
                  $scope.data.page += 1;
                  $scope.funRestServices.getListPage();
                },
                fastforward: function () {
                  $scope.data.initial = ($scope.data.list.detail.total_pages - 1);
                  $scope.data.page = $scope.data.list.detail.total_pages;
                  $scope.funRestServices.getListPage();
                },
                backward: function () {
                  $scope.data.initial -= 1;
                  $scope.data.page -= 1;
                  $scope.funRestServices.getListPage();
                },
                fastbackward: function () {
                  $scope.data.initial = 0;
                  $scope.data.page = 1;
                  $scope.funRestServices.getListPage();
                },
                traslateStatus: function (status) {
                  var string = "";
                  switch (status) {
                    case "sent":
                      string = "Enviado";
                      break;
                    case "undelivered":
                      string = "No enviado";
                      break;
                  }
                  return string;
                },
                showModalLoading: function () {
                
                },
                hideModalLoading: function () {

                },
                search: function (data) {
                  $scope.funRestServices.getListPage();
                }
              };

              // function restServices
              $scope.funRestServices = {
                getListPage: function () {
                  statisticService.getAllSmsTwoway($scope.data.idSmsTwoway, $scope.data.initial, $scope.data.filter).then(function (data) {
                    $scope.functions.setList(data);
                  });
                },
                getInfoPage: function () {
                  statisticService.getDetailSmsTwoWay($scope.data.idSmsTwoway).then(function (data) {
                    $scope.functions.setDetail(data);
                    $scope.functions.setHighChart(data);
                  });
                },
                getDetailSms: function () {
                  statisticService.getSmsTwoWay($scope.data.idSmsTwoway).then(function (data) {
                    $scope.functions.setSms(data);
                  });
                },
                reportSms: function () {
                  statisticService.reportStaticsSmsTwoWay($scope.data.idSmsTwoway, $scope.title).then(function () {
                    var url = fullUrlBase + 'statistic/downloadexcel/' + $scope.title;
                    location.href = url;
                  });
                }
              };

              $scope.functions.initial();


            }]);
})();
