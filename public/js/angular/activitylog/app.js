(function () {
  angular.module("activitylog", ["ui.router", "activitylog.controllers", "activitylog.services"])
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