/* 
 * Moduio services
 */

angular.module('LandingPageCategoryApp.services', [])
        .service('restServices', function ($http, $q, constantPageCategory) {
            this.createCategory = function (data) {
                var defer = $q.defer();
                var promise = defer.promise;
                let url = constantPageCategory.UrlPeticion.create;
                $http.post(url, data).then(function (resolve) {
                    defer.resolve(resolve);
                }).catch(function (reject) {
                    defer.reject(reject);
                });
                return promise;
            }
            
            this.editCategory = function (data) {
                var defer = $q.defer();
                var promise = defer.promise;
                let url = constantPageCategory.UrlPeticion.edit+'/'+data.idLandingPageCategory;
                $http.post(url, data).then(function (resolve) {
                    defer.resolve(resolve);
                }).catch(function (reject) {
                    defer.reject(reject);
                });
                return promise;
            }
            
            this.oneCategory = function (data) {
                var defer = $q.defer();
                var promise = defer.promise;
                let url = constantPageCategory.UrlPeticion.getone+'/'+data;
                $http.get(url).then(function (resolve) {
                    defer.resolve(resolve);
                }).catch(function (reject) {
                    defer.reject(reject);
                });
                return promise;
            }
            
            this.deleteCategory = function (id) {
                var defer = $q.defer();
                var promise = defer.promise;
                let url = constantPageCategory.UrlPeticion.delete+'/'+id;
                $http.get(url).then(function (resolve) {
                    defer.resolve(resolve);
                }).catch(function (reject) {
                    defer.reject(reject);
                });
                return promise;
            }
            
            this.getAllCategory = function (page, data) {
                var defer = $q.defer();
                var promise = defer.promise;
                let url = constantPageCategory.UrlPeticion.getall+"/"+page;
                $http.post(url,data).then(function (resolve) {
                    defer.resolve(resolve);
                }).catch(function (reject) {
                    defer.reject(reject);
                });
                return promise;
            }
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

            function primary(message) {
                slideOnTop(message, 4000, 'glyphicon glyphicon-exclamation-sign', 'primary');
            }

            return {
                error: error,
                success: success,
                warning: warning,
                notice: notice,
                primary: primary
            };
        });

