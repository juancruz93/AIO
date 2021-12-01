'use strict';
(function () {
    angular.module("pricelist.controller", [])
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

            .filter("formatPrice", function () {
                return function (num) {
                    return parseFloat(num);
                };
            })
            .controller("listController", ["$scope", "RestServices", "notificationService", function ($scope, RestServices, notificationServices) {
                    $scope.initial = 0;
                    $scope.page = 1;

                    $scope.forward = function () {
                        $scope.initial += 1;
                        $scope.page += 1;
                        $scope.listPriceList();
                    };
                    $scope.fastforward = function () {
                        $scope.initial = ($scope.list.total_pages - 1);
                        $scope.page = $scope.list.total_pages;
                        $scope.listPriceList();
                    };
                    $scope.backward = function () {
                        $scope.initial -= 1;
                        $scope.page -= 1;
                        $scope.listPriceList();
                    };
                    $scope.fastbackward = function () {
                        $scope.initial = 0;
                        $scope.page = 1;
                        $scope.listPriceList();
                    };

                    $scope.listPriceList = function () {
                        RestServices.listPriceList($scope.initial, "").then(function (data) {
                            $scope.list = data;
                        });
                    };

                    $scope.listPriceList();

                    $scope.searchForName = function () {
                        RestServices.listPriceList($scope.initial, $scope.filterName).then(function (data) {
                            $scope.list = data;
                        });
                    };

                    $scope.openMod = function (id) {
                        $scope.idPriceList = id;
                        openModal();
                    };

                    $scope.deletePriceList = function () {
                        RestServices.deletePriceList($scope.idPriceList).then(function (data) {
                            notificationServices.warning(data.message);
                            closeModal();
                            $scope.listPriceList();
                        });
                    };
                }])
            .controller("createController", ["$scope", "$state", "RestServices", "notificationService", function ($scope, $state, RestServices, notificationServices) {
                    $scope.accountingModes = [
                        {key: "contact", name: "Por contacto"},
                        {key: "sending", name: "Por envío"},
                    ];

                    $scope.data = {};
                    $scope.data.idCountry = null;
                    $scope.data.idService = null;
                    $scope.data.accountingMode = null;
                    $scope.data.status = true;
                    $scope.listCountry = function () {
                        RestServices.listCountry().then(function (data) {
                            $scope.countries = data;
                        });
                    };
                    $scope.listCountry();

                    $scope.services = function () {
                        RestServices.services().then(function (data) {
                            $scope.listServices = data;
                        });
                    };
                    $scope.services();

                    $scope.fulllistmode = function () {
                        $scope.listaccountingMode = [
                            {value: "contact", name: "Contacto"},
                            {value: "sending", name: "Envío"}
                        ];
                    };
                    $scope.fulllistmode();

                    $scope.viewMode = true;
                    $scope.viewMailTester = true;
                    $scope.changeService = function () {
                        $scope.data.idServices = $scope.data.service.idServices;
                        for (var i = 0; i < $scope.listServices.length; i++) {
                            if ($scope.listServices[i].idServices === $scope.data.service.idServices) {
                                if ($scope.listServices[i].name.toLowerCase() === "sms") {
                                    $scope.data.accountingMode = null;
                                    $scope.viewMode = false;
                                    $scope.viewMailTester = true;
                                } else if ($scope.listServices[i].name.toLowerCase() === "email marketing"){
                                    $scope.data.accountingMode = null;
                                    $scope.viewMode = true;
                                    $scope.viewMailTester = true;
                                } else if ($scope.listServices[i].name.toLowerCase() === "mail tester" || $scope.listServices[i].name.toLowerCase() === "adjuntar archivos" || $scope.listServices[i].name.toLowerCase() === "survey"){
                                    $scope.data.accountingMode = null;
                                    $scope.data.maxValue = 0;
                                    $scope.data.minValue = 0;
                                    $scope.viewMode = false;
                                    $scope.viewMailTester = false;
                                }
                            }
                        }
                    };

                    $scope.savePriceList = function () {
                        if ($scope.viewMode) {
                            if ($scope.data.accountingMode == null) {
                                notificationServices.error("Debe seleccionar un modo de contabilidad");
                                return false;
                            } else {
                                $scope.data.accountingMode = $scope.data.accountingMode.key;
                            }
                        }
                        
                        $scope.data.idCountry = $scope.data.country.idCountry;

                        RestServices.createPriceList($scope.data).then(function (data) {
                            notificationServices.success(data.message);
                            $state.go("index");
                        });
                    };
                }])
            .controller("editController", ["$scope", "$state", "RestServices", "notificationService", "$stateParams", function ($scope, $state, RestServices, notificationServices, $stateParams) {
                    $scope.data = {};
                    $scope.accountingModes = [
                        {key: "contact", name: "Por contacto"},
                        {key: "sending", name: "Por envío"},
                    ];
                    $scope.getPriceList = function () {
                        RestServices.getPriceList($stateParams.idPriceList).then(function (data) {
                            $scope.data = data;
                            $scope.selectAccountingMode();
                            $scope.copyData = angular.copy($scope.data);
                            $scope.data.status = (data.status == 1);
                            $scope.listCountry();
                            $scope.services();
                        });
                    };

                    $scope.getPriceList();

                    $scope.listCountry = function () {
                        RestServices.listCountry().then(function (data) {
                            $scope.countries = data;
                            $scope.selectCountry();
                        });
                    };
                    $scope.listCountry();

                    $scope.services = function () {
                        RestServices.services().then(function (data) {
                            $scope.listServices = data;
                            $scope.selectService();
                        });
                    };


                    $scope.listaccountingMode = [
                        {value: "contact", name: "Contacto"},
                        {value: "sending", name: "Envío"}
                    ];

                    $scope.selectCountry = function () {
                        for (var i = 0; i < $scope.countries.length; i++) {
                            if ($scope.countries[i].idCountry === $scope.data.idCountry) {
                                $scope.data.country = {idCountry: $scope.data.idCountry, name: $scope.countries[i].name};
                            }
                        }
                    }

                    $scope.selectAccountingMode = function() {
                        for (var i = 0; i < $scope.accountingModes.length; i++) {
                            if ($scope.accountingModes[i].key === $scope.data.accountingMode) {
                                $scope.data.accountingMode = {key: $scope.data.accountingMode, name: $scope.accountingModes[i].name};
                            }
                        }
                    }
                    
                    $scope.viewMode = false;
                    $scope.viewMailTester = true;
                    $scope.selectService = function () {
                        for (var i = 0; i < $scope.listServices.length; i++) {
                            if ($scope.listServices[i].idServices === $scope.data.idServices) {
                                $scope.data.service = {idServices: $scope.data.idServices, name: $scope.listServices[i].name};
                                if ($scope.listServices[i].name.toLowerCase() === "sms") {
                                    $scope.viewMode = false;
                                } else if ($scope.listServices[i].name.toLowerCase() === "email marketing"){
                                    $scope.viewMode = true;
                                } else if ($scope.listServices[i].name.toLowerCase() === "mail tester" || $scope.listServices[i].name.toLowerCase() === "adjuntar archivos" || $scope.listServices[i].name.toLowerCase() === "survey"){
                                    $scope.viewMode = false;
                                    $scope.viewMailTester = false;
                                }
                            }
                        }
                    };

                    $scope.changeService = function () {
                        console.log($scope.listServices);
                        for (var i = 0; i < $scope.listServices.length; i++) {
                            $scope.data.idServices = $scope.data.service.idServices;
                            if ($scope.listServices[i].idServices === $scope.data.idServices) {
                                if ($scope.listServices[i].name.toLowerCase() === "sms") {
                                    $scope.viewMode = false;
                                    $scope.viewMailTester = true;
                                } else if ($scope.listServices[i].name.toLowerCase() === "email marketing"){
                                    $scope.data.accountingMode = null;
                                    $scope.viewMode = true;
                                    $scope.viewMailTester = true;
                                } else if ($scope.listServices[i].name.toLowerCase() === "mail tester" || $scope.listServices[i].name.toLowerCase() === "adjuntar archivos" || $scope.listServices[i].name.toLowerCase() === "survey"){
                                    $scope.data.accountingMode = null;
                                    $scope.data.maxValue = 0;
                                    $scope.data.minValue = 0;
                                    $scope.viewMode = false;
                                    $scope.viewMailTester = false;
                                }
                            }
                        }
                    };

                    $scope.editPriceList = function () {
                        if ($scope.viewMode) {
                            if ($scope.data.accountingMode == null) {
                                notificationServices.error("Debe seleccionar un modo de contabilidad");
                                return false;
                            } else {
                                $scope.data.accountingMode = $scope.data.accountingMode.key;
                            }
                        } else {
                            $scope.data.accountingMode = null;
                        }
                        
                        $scope.data.idCountry = $scope.data.country.idCountry;
                        
                        RestServices.editPriceList($scope.data).then(function (data) {
                            notificationServices.info(data.message);
                            $state.go("index");
                        });
                    };

                }]);
})();