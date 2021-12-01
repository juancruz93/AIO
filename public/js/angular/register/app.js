(function () {
  angular.module("register", ["ui.router", "register.controllers", "register.services", "ngMaterial", "ngSanitize", "ui.select"])
    .config(["$stateProvider", "$urlRouterProvider", function ($stateProvider, $urlRouterProvider) {
        $stateProvider
          .state("index", {
            url: "/",
            templateUrl: fullUrlBase + templateBase + "/signup",
            controller: "signupController"
          })
          .state("payment", {
            url: "/payment",
            templateUrl: fullUrlBase + templateBase + "/payment",
            controller: "paymentController"
          })
          .state("payment.paymentplan", {
            url: "/paymentplan/:id",
            templateUrl: fullUrlBase + templateBase + "/paymentplan",
            controller: "paymentplanController"
          })
          .state("payment.paymentplan.detail", {
            url: "/detail/:idPaymentPlan",
            templateUrl: fullUrlBase + templateBase + "/paymentplandetail",
            controller: "detailController"
          })
          .state("payment.pay", {
            url: "/pay",
            templateUrl: fullUrlBase + templateBase + "/pay",
            controller: "payController"
          })
          .state("completeprofile", {
            url: "/completeprofile",
            templateUrl: fullUrlBase + templateBase + "/completeprofile",
            controller: "completeprofileController"
          });


        $urlRouterProvider.otherwise(function ($injector) {
          var $state = $injector.get("$state");
          $state.go("index");
        });
      }]);
})();