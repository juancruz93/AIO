'use strict';
(function () {
  angular.module("pricelist", ["ui.router", "pricelist.controller", "pricelist.services", "ngMaterial", 'ui.select','ngSanitize'])
    .config(["$stateProvider", function ($stateProvider) {
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
            url: "/edit/:idPriceList",
            templateUrl: fullUrlBase + templateBase + "/edit",
            controller: "editController"
          });
      }]);
})();