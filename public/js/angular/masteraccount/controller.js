angular.module('aio', ['ngMaterial', "ui.select", "ngSanitize"])
    .filter('propsFilter', function () {
        return function (items, props) {
            var out = [];

            if (angular.isArray(items)) {
                var keys = Object.keys(props);

                items.forEach(function (item) {
                    var itemMatches = false;

                    for (var i = 0; i < keys.length; i++) {
                        var prop = keys[i];
                        var text = props[prop].toLowerCase();
                        if (item[prop].toString().toLowerCase().indexOf(text) !== -1) {
                            itemMatches = true;
                            break;
                        }
                    }

                    if (itemMatches) {
                        out.push(item);
                    }
                });
            } else {
                // Let the output be the input untouched
                out = items;
            }

            return out;
        };
    })
    .factory('main', ['$http', '$window', function ($http, $window) {
        return {
            newMasterUser: function (data, success, error) {
                $http.post('create', data).success(success).error(error);
            },
            newmasteraccount: function (data, success, error) {
                $http.post('create', data).success(success).error(error);
            },
            country: function (success, error) {
                $http.get($window.myBaseURL + 'country/country').success(success).error(error);
            },
            state: function (data, success, error) {
                $http.get($window.myBaseURL + 'country/state/' + data).success(success).error(error);
            },
            city: function (data, success, error) {
                $http.get($window.myBaseURL + 'country/cities/' + data).success(success).error(error);
            },
            mta: function (success, error) {
                $http.get('getallmta').success(success).error(error);
            },
            adapter: function (success, error) {
                $http.get('adapter').success(success).error(error);
            },
            urldomain: function (success, error) {
                $http.get('urldomain').success(success).error(error);
            },
            mailclass: function (success, error) {
                $http.get('mailclass').success(success).error(error);
            },
            planbycountry: function (idCounty, success, error) {
                $http.get('planbycountry/' + idCounty).success(success).error(error);
            },
            categories: function (success, error) {
                $http.get($window.myBaseURL + 'api/accountcategory/getaccountcategories').success(success).error(error);
            },
            smsSendingRule: function (success, error) {
                $http.get($window.myBaseURL + 'api/smssendingrule/listall').success(success).error(error);
            },
            mxssr: function (idMasterAccount, success, error) {
                $http.get($window.myBaseURL + 'masteraccount/listmxssr/' + idMasterAccount).success(success).error(error);
            },
            getMasteraccount: function (idMasterAccount, success, error) {
                $http.get($window.myBaseURL + 'masteraccount/getmasteraccount/' + idMasterAccount).success(success).error(error);
            },
            editMasteraccount: function (idMasterAccount, data, success, error) {
                $http.post($window.myBaseURL + 'masteraccount/edit/' + idMasterAccount, data).success(success).error(error);
            }
        }
    }])
    .controller('ctrlMasteraccount', ['$rootScope', '$scope', '$http', 'main', '$window', '$interval', function ($rootScope, $scope, $http, main, $window, $interval) {

        $scope.email = false;
        $scope.sms = false;
        $scope.status = true;
        $scope.showDetail = false;
        $scope.idSmsSendingRule = [];
        $scope.pp = [];

        if (typeof (idCategory) != 'undefined') {
            $scope.idAccountCategory = idCategory;
        }
        if (typeof (idMasterAccount) != 'undefined') {
            $scope.idMasterAccount = idMasterAccount;
            let rules = []
            main.mxssr($scope.idMasterAccount, function (data) {
                data.forEach(function (item, index) {
                    rules.push(item.idSmsSendingRule);
                });
                $scope.pp.idSmsSendingRule = rules;
            }, function (res) {
                $rootScope.error = 'fail';
            });
            main.getMasteraccount($scope.idMasterAccount, function (data) {
                $scope.name = data.name;
                $scope.description = data.description;
                $scope.address = data.address;
                $scope.nit = data.nit;
                $scope.phone = data.phone;
            }, function (res) {
                $rootScope.error = 'fail';
            });
        }

        main.country(function (res) {
            $scope.country = res;
        }, function (res) {
            $rootScope.error = 'fail';
        });

        main.categories(function (res) {
            $scope.categories = res;
        }, function (res) {
            $rootScope.error = 'fail';
        });

        main.smsSendingRule(function (res) {
            $scope.smsSendingRule = res.smsSendingRule;
        }, function (res) {
            $rootScope.error = 'fail';
        });

        $scope.newmasteraccount = function () {
            var plan = jQuery.parseJSON($scope.paymentPlanSelected);
            var data = {
                nameMasterAccount: $scope.name,
                description: $scope.description,
                nit: $scope.nit,
                address: $scope.address,
                phone: $scope.phone,
                paymentPlan: plan.idPaymentPlan,
                city: $scope.citySelected,
                idAccountCategory: $scope.idAccountCategory,
                idSmsSendingRule: $scope.pp.idSmsSendingRule,
                status: $scope.status
            };

            main.newmasteraccount(data, function (res) {
                $window.location = "createuser/" + res.idMasterAccount;
            }, function (res) {
                slideOnTop(res[0], 3000, "glyphicon glyphicon-remove-sign", "danger");
                $rootScope.error = 'fail';
            });
        };

        $scope.selectCountry = function (id) {
            if (!id) {
                id = $scope.countrySelected;
            }
            $scope.showDetail = false;
            $scope.state = {};
            $scope.cities = {};
            $scope.paymentPlan = {};
            main.state(id, function (res) {
                $scope.state = res;
            }, function (res) {
                $rootScope.error = 'fail';
            });

            main.planbycountry(id, function (res) {
                $scope.paymentPlan = res;
            }, function (err) {
                $rootScope.error = 'fail';
            });
        };

        $scope.descriptionPlan = function () {
            $scope.plan = jQuery.parseJSON($scope.paymentPlanSelected);
            $scope.showDetail = true;
        };

        $scope.selectState = function (id) {
            if (!id) {
                id = $scope.stateSelected
            }
            $scope.cities = {};
            main.city(id, function (res) {
                $scope.cities = res;
            }, function (res) {
                $rootScope.error = 'fail';
            });
        };
        $scope.selectCountryUser = function () {
            $scope.stateUser = {};
            $scope.citiesUser = {};
            main.state($scope.countrySelectedUser, function (res) {
                $scope.stateUser = res;
            }, function (res) {
                $rootScope.error = 'fail';
            });
        };
        $scope.selectStateUser = function () {
            $scope.citiesUser = {};
            main.city($scope.stateSelectedUser, function (res) {
                $scope.citiesUser = res;
            }, function (res) {
                $rootScope.error = 'fail';
            });
        };

        $scope.editmasteraccount = function () {
            var data = {
                nameMasterAccount: $scope.name,
                description: $scope.description,
                nit: $scope.nit,
                address: $scope.address,
                phone: $scope.phone,
                city: $scope.citySelectedUser,
                idAccountCategory: $scope.idAccountCategory,
                idSmsSendingRule: $scope.pp.idSmsSendingRule,
                status: $scope.status
            };

            main.editMasteraccount($scope.idMasterAccount, data, function (data) {
                $window.location = $window.myBaseURL + "masteraccount/index";
            }, function (res) {
                slideOnTop(res[0], 3000, "glyphicon glyphicon-remove-sign", "danger");
                $rootScope.error = 'fail';
            });
        };

    }])
    .controller('ctrlConfig', ['$scope', 'main', function ($scope, main) {

    }])
