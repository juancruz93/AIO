(function () {
  angular.module("smssendingrule", ["smssendingrule.controllers", "smssendingrule.services", "ui.router", "ui.select", "ngMaterial", "ngSanitize"])
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
            url: "/edit/:id",
            templateUrl: fullUrlBase + templateBase + "/edit",
            controller: "editController"
          })
          .state("show", {
            url: "/show/:id",
            templateUrl: fullUrlBase + templateBase + "/show",
            controller: "showController"
          });
          
        $urlRouterProvider.otherwise(function ($injector) {
          var $state = $injector.get('$state');
          $state.go('index');
        });
      }]);
})();