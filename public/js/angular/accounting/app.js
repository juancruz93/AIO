(function () {
  angular.module("accounting", ["ui.router", "accounting.controllers", "accounting.services", "ngSanitize", "ngMaterial"])
    .config(["$stateProvider", "$urlRouterProvider", function ($stateProvider, $urlRouterProvider) {
      $stateProvider
        .state("index", {
          url: "/",
          templateUrl: fullUrlBase + templateBase + "/list",
          controller: "listController"
        });
      $urlRouterProvider.otherwise(function ($injector, $location) {
        var $state = $injector.get('$state');
        $state.go('index');
      });
    }]);
})();