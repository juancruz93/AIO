'use strict';
(function () {
    angular.module("pricelist.services", [])
            .service("RestServices", function ($http, $q, notificationService) {
                this.listPriceList = function (page, data) {
                    var defered = $q.defer();
                    var url = fullUrlBase + "api/pricelist/list/" + page + "/" + data;
                    $http.get(url)
                            .success(function (data) {
                                defered.resolve(data);
                            })
                            .error(function (data) {
                                defered.reject(data);
                                notificationService.error(data.message);
                            });

                    return defered.promise;
                };

                this.listCountry = function () {
                    var defered = $q.defer();
                    var url = fullUrlBase + "country/country";
                    $http.get(url)
                            .success(function (data) {
                                defered.resolve(data);
                            })
                            .error(function (data) {
                                defered.reject(data);
                                notificationService.error(data);
                            });

                    return defered.promise;
                };

                this.createPriceList = function (data) {
                    var defered = $q.defer();
                    var url = fullUrlBase + "api/pricelist/create";
                    $http.post(url, data)
                            .success(function (data) {
                                defered.resolve(data);
                            })
                            .error(function (data) {
                                defered.reject(data);
                                notificationService.error(data.message);
                            });

                    return defered.promise;
                };

                this.getPriceList = function (id) {
                    var defered = $q.defer();
                    var url = fullUrlBase + "api/pricelist/get/" + id;
                    $http.get(url)
                            .success(function (data) {
                                defered.resolve(data);
                            })
                            .error(function (data) {
                                defered.reject(data);
                                notificationService.error(data.message);
                            });

                    return defered.promise;
                };

                this.editPriceList = function (data) {
                    var defered = $q.defer();
                    var url = fullUrlBase + "api/pricelist/edit";
                    $http.put(url, data)
                            .success(function (data) {
                                defered.resolve(data);
                            })
                            .error(function (data) {
                                notificationService.error(data.message);
                                defered.reject(data.message);
                            });

                    return defered.promise;
                };

                this.deletePriceList = function (id) {
                    var defered = $q.defer();
                    var url = fullUrlBase + "api/pricelist/delete/" + id;
                    $http.delete(url)
                            .success(function (data) {
                                defered.resolve(data);
                            })
                            .error(function (data) {
                                defered.reject(data);
                                notificationService.error(data.message);
                            });

                    return defered.promise;
                };

                this.services = function () {
                    var defered = $q.defer();
                    var url = fullUrlBase + "services/listapi";
                    $http.get(url)
                            .success(function (data) {
                                defered.resolve(data);
                            })
                            .error(function (data) {
                                defered.reject(data);
                                notificationService.error(data.message)
                            });

                    return defered.promise;
                };
            })
            .factory('notificationService', function () {
                function error(message) {
                    slideOnTop(message, 4000, 'glyphicon glyphicon-remove-circle', 'danger');
                }

                function success(message) {
                    slideOnTop(message, 4000, 'glyphicon glyphicon-ok-circle', 'success');
                }

                function warning(message) {
                    slideOnTop(message, 4000, 'glyphicon glyphicon-exclamation-sign', 'warning');
                }

                function notice(message) {
                    slideOnTop(message, 4000, 'glyphicon glyphicon-exclamation-sign', 'notice');
                }

                function info(message) {
                    slideOnTop(message, 4000, 'glyphicon glyphicon-exclamation-sign', 'info');
                }

                return {
                    error: error,
                    success: success,
                    warning: warning,
                    notice: notice,
                    info: info
                };
            });
})();