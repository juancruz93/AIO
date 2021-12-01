(function () {
  angular.module("templateControllers", ['ui.select', 'ngSanitize'])
  .config(["$interpolateProvider", function($interpolateProvider){
    $interpolateProvider.startSymbol("[[");
    $interpolateProvider.endSymbol("]]");
  }])
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
    .controller("createCtrl", ["$scope", "$http", "$q", function ($scope, $http, $q) {
      //Initialize
      (function () {
        //Set Data
        $scope.data = {};
        $scope.data.new = {};
        $scope.idLandingPageTemplate = 0;
      })();

      $scope.misc = {
        viewCategories: true,
        viewNewCategory: false
      };

      $scope.functions = {
        viewModal: function (option) {
          angular.element(document.querySelector("#formCreateTemplate")).modal(option);
        },
        cancelCreate: function () {
          window.location.href = baseAio + "landingpagetemplate#/";
        },
        viewNewCategory: function () {
          $scope.misc.viewNewCategory = !$scope.misc.viewNewCategory;
          $scope.misc.viewCategories = !$scope.misc.viewCategories;
        },
        saveNewCategory: function () {
          $scope.functions.apiSaveNewCategory($scope.data.new).then(function (response){
            $scope.functions.notification(800, "center", "success", response.message, "fa-check-circle");
            $scope.functions.setLandingPagesTemplateCategories();
            $scope.functions.viewNewCategory();
            $scope.data.idLandingPageTemplateCategory = response.idLandingPageTemplateCategory;
            $scope.data.new.name = "";
          }).catch(function (error){
            $scope.functions.notification(800, 'center', 'error', error.message, 'exclamation-circle');
          });
        },
        apiSaveNewCategory: function (data) {
          var deferred = $q.defer();
          var url = baseAio + "/api/lptemplatecategory/savesimple";
          $http.post(url, data)
            .success(function (response) {
              deferred.resolve(response);
            })
            .error(function (error) {
              deferred.reject(error);
            });

          return deferred.promise;
        },
        getLandingPagesTemplateCategories: function () {
          var deferred = $q.defer();
          var url = baseAio + "/api/lptemplatecategory/getall";
          $http.get(url)
            .success(function (response) {
              deferred.resolve(response);
            })
            .error(function (error) {
              deferred.reject(error);
            });

          return deferred.promise;
        },
        setLandingPagesTemplateCategories: function () {
          $scope.functions.getLandingPagesTemplateCategories().then(function (response) {
            $scope.landingpagetempcategory = response;
          }).catch(function (error) {
            $scope.functions.notification();
          });
        },
        getLandingPageTemplate: function (idlpt) {
          $scope.functions.apiLandingPageTemplate.get(idlpt).then(function (response) {
            $scope.data = response;
          }).catch(function (error) {
            $scope.functions.notification(800, 'center', 'error', error.message, 'exclamation-circle');
          });
        },
        createLandingPageTemplate: function () {
          $scope.functions.apiLandingPageTemplate.create(idGeneral, $scope.data).then(function (response) {
            $scope.res = response;
            window.history.replaceState('Editor', 'Editor', $scope.res.idLandingPageTemplate);
            sessionStorage.setItem('idLandingPageTemplate', $scope.res.idLandingPageTemplate);

            if ($scope.res.idLandingPageTemplate) {
              idGeneral = $scope.res.idLandingPageTemplate;
              $scope.idLandingPageTemplate = idGeneral;
              $scope.urlactionForm = baseAio+'landingpagetemplate/preview/'+idGeneral;
              $scope.functions.viewModal('hide');
              $scope.functions.notification(800, "center", "success", response.message, "fa-check-circle");
            }
          }).catch(function (error) {
            $scope.functions.notification(800, 'center', 'error', error.message, 'exclamation-circle');
          });
        },
        apiLandingPageTemplate: {
          get: function (idLandingPageTemplate) {
            var deferred = $q.defer();
            var url = baseAio + "/api/lptemplate/getlpt/" + idLandingPageTemplate;
            $http.get(url)
              .success(function (response) {
                deferred.resolve(response);
              })
              .error(function (error) {
                deferred.reject(error);
              });

              return deferred.promise;
          },
          create: function (idlpt, data) {
            var deferred = $q.defer();
            var url = baseAio + "/api/lptemplate/create/" + idlpt;
            $http.post(url, data)
              .success(function (response) {
                deferred.resolve(response);
              })
              .error(function (error) {
                deferred.reject(error);
              });

            return deferred.promise;
          }
        },
        addEventListenerBtns: function () {
          document.getElementById("cancelTemplate").addEventListener('click', function (e) {
            $scope.functions.cancelCreate();
          });
          document.getElementById("btnSubmitFormCreate").addEventListener('click', function (e) {
            $scope.functions.createLandingPageTemplate();
          })
        },
        notification: function (width, position, type, message, icon ,autohide = true){
          notif({
            msg: "<span class='medium-text'><i class='fa "+icon+"'></i> " + message + "</span>",
            type: type,
            position: position,
            width: width,
            height: 60,
            autohide: autohide
          });
        }
      };


      angular.element(document).ready(function () {
        $scope.functions.addEventListenerBtns();
        $scope.functions.setLandingPagesTemplateCategories();
        if (idGeneral != 0) {
          $scope.functions.getLandingPageTemplate(idGeneral);
        }
        $scope.functions.viewModal('show');
      });
    }]);

  angular.element(document).ready(function () {
    angular.bootstrap(document, ["templateControllers"]);
  });
})();