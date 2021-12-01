(function () {
    angular.module('footer', ['footer.controllers', 'footer.directives', 'footer.services'])
        /*.config(['$stateProvider', '$urlRouterProvider', function ($stateProvider, $urlRouterProvider) {
            $stateProvider
                .state('describe', {
                    url: "/basicinformation/:id",
                    templateUrl: fullUrlBase + templateBase + '/basicinformation',
                    controller: 'basicinformationController'
                })
                .state('addressees', {
                    url:"/addressees/:id",
                    templateUrl: fullUrlBase + templateBase + '/addressees',
                    controller: 'addAddresseesController'
                })
                .state('content', {
                    url:"/content/:id",
                    templateUrl: fullUrlBase + templateBase + '/content',
                    controller: 'contentController'
                })
                .state('advanceoptions', {
                    url:"/advanceoptions",
                    templateUrl: fullUrlBase + templateBase + '/advanceoptions'
                    //controller: 'formController'
                })
                .state('shippingdate', {
                    url:"/shippingdate",
                    templateUrl: fullUrlBase + templateBase + '/shippingdate',
                    controller: 'shippingdateController'
                })

            }]);*/

})();
