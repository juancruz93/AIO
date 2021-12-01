(function() {
  angular
    .module("LandingPageTemplate", [
      "ui.router",
      "LandingPageTemplate.controllers",
      "LandingPageTemplate.services",
      "ui.bootstrap.datetimepicker",
      "ui.select",
      "ngMaterial",
      "ngSanitize"
    ])
    .config([
      "$stateProvider",
      "$urlRouterProvider",
      "$interpolateProvider",
      "constantLPTemplate",
      function(
        $stateProvider,
        $urlRouterProvider,
        $interpolateProvider,
        constantLPTemplate
      ) {
        $stateProvider
          .state("list", {
            url: "/",
            templateUrl:
              fullUrlBase + constantLPTemplate.Misc.TemplateBase + "/list",
            controller: "listCtrl"
          })
          .state("selecttemplate", {
            url: "/selecttemplate/:idLandingPage",
            templateUrl:  fullUrlBase + constantLPTemplate.Misc.TemplateBase + "/selecttemplate",
            controller: "selectCtrl"
          });
        $urlRouterProvider.otherwise("/");

        $interpolateProvider.startSymbol("[[");
        $interpolateProvider.endSymbol("]]");
      }
    ])
    .constant("constantLPTemplate", {
      NotificationsService: {
        Errors: {
          error: "glyphicon glyphicon-remove-circle",
          success: "glyphicon glyphicon-ok-circle",
          warning: "glyphicon glyphicon-exclamation-sign",
          notice: "glyphicon glyphicon-exclamation-sign",
          primary: "glyphicon glyphicon-exclamation-sign"
        }
      },
      Notifications: {
        Errors: {
          errorServices: "Se ha producido un error",
          errorview: "Indique la cantidad de visualizaciones",
          errorview1: "Seleccione una fecha de inicio",
          errorview2: "Seleccione una fecha de expiración",
          errorview3:
            "Fecha errada, la fecha inicio no debe ser mayor a la fecha de expiración"
        }
      },
      UrlRequest: {
        Urls: {
          listlptemplate: "api/lptemplate/getall/",
          listlptcategory: "api/lptemplatecategory/getall"
        }
      },
      Misc: {
        Alerts: {
          danger: "danger",
          success: "success",
          warning: "warning",
          notice: "notice",
          primary: "primary"
        },
        TemplateBase: "landingpagetemplate"
      }
    });
  angular.element(document).ready(function() {
    angular.bootstrap(document, ["LandingPageTemplate"]);
  });
})();