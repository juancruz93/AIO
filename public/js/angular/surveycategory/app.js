(function () {
  angular.module("surveycategory", ["surveycategory.controllers", "surveycategory.services", "ui.router", "ngMaterial", "ngSanitize"])
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
            controller: "createController",
          })
          .state("edit", {
            url: "/edit/:idSurveyCategory",
            templateUrl: fullUrlBase + templateBase + "/edit",
            controller: "editController"
          });

        $urlRouterProvider.otherwise(function ($injector) {
          var $state = $injector.get('$state');
          $state.go('index');
        });

      }]);
})();