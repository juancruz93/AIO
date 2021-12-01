(function () {
  angular.module("currency", ["ui.router", "currency.controller", "currency.services","ngMaterial"])
    .config(["$stateProvider", "$urlRouterProvider", function ($stateProvider, $urlRouterProvider) {
        $stateProvider
          .state("index", {
            url: "/",
            templateUrl: fullUrlBase + templateBase + "/list",
            controller: "listController"
          })
          .state("create", {
            url: "/create",
            templateUrl: fullUrlBase + templateBase + "/create",
            controller: "createController"
          })
          .state("edit", {
            url: "/edit/:idCurrency",
            templateUrl: fullUrlBase + templateBase + "/edit",
            controller: "editController"
          });
      }]);
})();