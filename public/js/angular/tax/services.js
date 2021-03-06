'use strict';
(function () {
    angular.module("tax.services", [])
            .service("RestServices", function ($http, $q, notificationService) {
                this.listTax = function (page, data) {
                    var defered = $q.defer();
                    var url = fullUrlBase + "api/tax/list/" + page + "/" + data;
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

                this.createTax = function (data) {
                    var defered = $q.defer();
                    var url = fullUrlBase + "api/tax/create";
                    $http.post(url, data)
                            .success(function (data) {
                                defered.resolve(data);
                            })
                            .error(function (data) {
                                notificationService.error(data.message);
                                defered.reject(data);
                            });

                    return defered.promise;
                };

                this.getTax = function (id) {
                    var defered = $q.defer();
                    var url = fullUrlBase + "api/tax/get/" + id;
                    $http.get(url)
                            .success(function (data) {
                                defered.resolve(data);
                            })
                            .error(function (data) {
                                notificationService.error(data.message);
                                defered.reject(data);
                            });

                    return defered.promise;
                };

                this.ediTax = function (data) {
                    var defered = $q.defer();
                    var url = fullUrlBase + "api/tax/edit";
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

                this.deleteTax = function (id) {
                    var defered = $q.defer();
                    var url = fullUrlBase + "api/tax/delete/" + id;
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