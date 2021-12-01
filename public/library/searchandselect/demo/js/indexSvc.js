appRoot.service(
            "indexSvc", ['$http', '$q',
            function ($http, $q) {

                var urlBase = 'http://localhost/aio/api/mailtemplate/getallmailtemplate';
                return ({
                    getCountries: getCountries
                });

                function getCountries(searchKey, pageNumber) {
                    console.log(pageNumber);
                    var deferred = $q.defer();
                    var request = $http({
                        method: "get",
                        url: urlBase + "country",
                        params: { searchKey: searchKey, pageNumber: pageNumber }
                    });

                    return (request.then(handleSuccess, handleError));

                }

                function handleError(response) {

                    if (
                        !angular.isObject(response.data) ||
                        !response.data.message
                        ) {
                        return ($q.reject("An unknown error occurred."));
                    }
                    return ($q.reject(response.data.message));
                }


                function handleSuccess(response) {
                    return (response.data);
                }
            }]);

