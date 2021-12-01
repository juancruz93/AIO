(function () {
  angular.module('statistic', ['ui.router', 'ui.bootstrap', 'statistic.controllers', 'statistic.services', 'ngMaterial', 'ui.select', 'ngSanitize', 'statistic.directives', 'builder', 'builder.components', 'validator.rules', 'flowChart', 'oc.lazyLoad'])
          .config(['$stateProvider', '$urlRouterProvider', function ($stateProvider, $urlRouterProvider) {
              $stateProvider
                      .state('mail', {
                        url: "/mail/:id",
                        templateUrl: fullUrlBase + templateBase + '/mail',
                        controller: 'mailcontroller'
                      })
                      .state('nodo', {
                        url: "/nodo/:idAutomaticCampaign/:idNodo/:type",
                        templateUrl: fullUrlBase + templateBase + '/nodo',
                        controller: 'nodo'
                      })
                      .state('sms', {
                        url: "/sms/:id",
                        templateUrl: fullUrlBase + templateBase + '/sms',
                        controller: 'smscontroller'
                      })
                      .state('survey', {
                        url: "/survey/:id",
                        templateUrl: fullUrlBase + templateBase + '/survey',
                        controller: 'surveycontroller'
                      })
                      .state('automaticcampaign', {
                        url: "/automaticcampaign/:id",
                        templateUrl: fullUrlBase + templateBase + '/automaticcampaign',
                        controller: 'automaticcampaigncontroller'
                      })
                      .state('smstwoway', {
                        url: "/smstwoway/:id",
                        templateUrl: fullUrlBase + templateBase + '/smstwoway',
                        controller: 'smstwowaycontroller'
                      })
            }])
          .constant('constantStatistic', {
            Titles: {
              statisSms: "Estadística de envío de SMS",
              statisMail: "Estadística de envío de correo",
              statisSurvey: "Estadística de encuesta",
              statisSmstwoway: "Estadística de envío de SMS DOBLE VIA",

            }
          });
})();

