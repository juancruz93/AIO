'use strict';
(function () {
  angular.module("paymentplan", ["ui.router", "paymentplan.controller", "paymentplan.services", "ngMaterial", "ui.select", "ngSanitize"]).config(["$stateProvider", function ($stateProvider) {
      $stateProvider.state("index", {
        url: "/", templateUrl: fullUrlBase + templateBase + "/list", controller: "listController"
      }
      ).state("create", {
        url: "/create",
        templateUrl: fullUrlBase + templateBase + "/create",
        controller: "createController"
      }
      ).state("edit", {
        url: "/edit/:idPaymentPlan", templateUrl: fullUrlBase + templateBase + "/edit", controller: "editController"
      }
      ).state("show", {
        url: "/show/:idPaymentPlan", templateUrl: fullUrlBase + templateBase + "/show", controller: "showController"
      }
      )
    }
  ])
})()