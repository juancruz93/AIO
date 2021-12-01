(function () {
  angular.module("session", ["ui.router", "session.controllers", "session.services", "ngMaterial", "ngSanitize", "ui.select"])
    .config(["$stateProvider", "$urlRouterProvider", function ($stateProvider, $urlRouterProvider) {
        $stateProvider
          .state("index", {
            url: "/",
            templateUrl: fullUrlBase + templateBase + "/login",
            controller: "loginController"
          })
          .state("loginpass", {
            url: "/loginpass",
            templateUrl: fullUrlBase + templateBase + "/loginpass",
            controller: "loginpassController"
          })
          .state("recoverpass",{
            url: "/recoverpass",
            templateUrl: fullUrlBase + templateBase + "/recoverpass",
            controller: "recoverpassController"
          });

        $urlRouterProvider.otherwise(function ($injector, $location) {
          var $state = $injector.get("$state");
          $state.go("index");
        });
      }]);
})();
