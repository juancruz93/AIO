(function () {
  angular.module('report', ['ngAnimate','ngDragDrop','ui.router', 'ngRoute', 'report.controllers', 'report.services', 'ui.select', 'ngSanitize', "ngMaterial", "moment-picker", 'highcharts-ng','ui.bootstrap', 'googlechart', 'ui.bootstrap.datetimepicker', 'angularMoment'])
    .config(['$stateProvider', '$routeProvider', function ($stateProvider, $routeProvider) {
        $routeProvider
          .when('/mail', {
            templateUrl: fullUrlBase + templateBase + '/list',
            controller: 'indexController'
          })
          .when('/sms', {
            templateUrl: fullUrlBase + templateBase + '/listsms',
            controller: 'indexSmsController'
          })
          .when('/recharge', {
              templateUrl: fullUrlBase + templateBase + '/listrecharge',
              controller: 'indexControllerRecharge'
          })
          .when('/changeplan', {
              templateUrl: fullUrlBase + templateBase + '/changeplanuser',
              controller: 'indexControllerPlan'
          })
          .when('/stadisticsgeneral', {
              templateUrl: fullUrlBase + templateBase + '/statisticmail',
              controller: 'indexControllerStadistics'
          })
          .when('/graph', {
            templateUrl: fullUrlBase + templateBase + '/listgraph',
            controller: 'graphController'
          })
          .when('/excelsms', {
            templateUrl: fullUrlBase + templateBase + '/excelsms',
            controller: 'excelsmsController'
          })
          .when('/excelsmsday', {
            templateUrl: fullUrlBase + templateBase + '/excelsmsday',
            controller: 'excelsmsdayController'
          })
          .when('/infosms', {
            templateUrl: fullUrlBase + templateBase + '/infosms',
            controller: 'infosmsController'
          })
          .when('/infosmsbydestinataries', {
            templateUrl: fullUrlBase + templateBase + '/infosmsbydestinataries',
            controller: 'infosmsbydestinatariesController'
          })
          .when('/infomail', {
            templateUrl: fullUrlBase + templateBase + '/infomail',
            controller: 'infomailController'
          })
          .when('/reportvalidation', {
            templateUrl: fullUrlBase + templateBase + '/reportvalidation',
            controller: 'reportvalidation'
          })
          .when('/smsxemail', {
            templateUrl: fullUrlBase + templateBase + '/smsxemail',
            controller: 'smsxemailController'
          })
          .when('/listsmschannel', {
            templateUrl: fullUrlBase + templateBase + '/listsmschannel',
            controller: 'listsmschannel'
          })
          .otherwise({
            redirectTo: '/'
          });
      }])
    .constant('constantReport', {
      timeoutTab:{
        loteCsvTab: 200
      },
      Titles:{
        reportmail: "Detalle de envíos de correo de todas las cuentas",
        reportsms: "Detalle de envíos de SMS de todas las cuentas",
        infosmsbydestinataries: "Detalle de envíos de SMS por Celular",
        reportrecharge: "Historial de Recargas por Cuenta",
        reportchangeplan: "Detalle de los cambios de planes realizados",
        reportexcelsmsday: "Detalles de reporte de envíos de sms por día",     
        reportinfomail: "Detalle de envíos de Email por Subcuenta",
        listsmscanalTitle: "Detalle de los envíos por canales"
      },
      Notifications: {
        Errors: {
          errorNoneContactsGetMailValidation: "No se tienen registros en Validation",
          noGetAccount: "No se tienen cuentas en el aliado",
          emailNotCorrect: "El correo no es valido",
          completeEmail: "Complete el correo porfavor",
          dateComplete: "Debe completar ambas fechas si desea realizar la busqueda",
          errorNoneSms: "No se tienen registros en sms",
          emptyData:"No se tienen datos para buscar."
        }
      },
      UrlPeticion: {
        Urls: {
          listlanding: 'api/landingpage/listlanding/',
          //services.js
          getAllMailValidation: fullUrlBase+'api/report/getallmailvalidation/',
          getContactlist: fullUrlBase+'api/sendmail/getcontactlist',
          downloadMailValidation: fullUrlBase+'api/report/downloadmailvalidation/',
          getAllMailBounced: fullUrlBase+'api/report/getallmailbounced/',
          downloadMailBounced:fullUrlBase+'api/report/downloadgetallmailbounced/',
          getDataSmsChannel:fullUrlBase+'api/report/getdatasmschannel/'
        }
      }
    });

})();
