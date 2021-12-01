/**
 * Autor: Kevin Andres Ramirez Alzate
 * Comment: Pana si lo ve? esta todo bonito y organizado si lo va a tocar dejelo asi de bonito 
 */
(function () {
  angular.module('dashboardconfigController', [])
    .controller('index', ['$scope', '$mdSidenav', '$log', 'constantDashboardConfig', '$mdDialog', 'FileUploader', '$stateParams', 'restService', 'notificationService', function ($scope, $mdSidenav, $log, constantDashboardConfig, $mdDialog, FileUploader, $stateParams, restService, notificationService) {

        //Validacion idAccount
        if (typeof $stateParams.id == "undefined" || $stateParams.id == "") {
          window.location.href = constantDashboardConfig.urlPeticion.account;
        }

        // Definicion las variables
        $scope.arrConfigDashboardDefault = constantDashboardConfig.configDashboardDefault;
        $scope.arrConfigDashboardDefault = {};
        $scope.arrServices = [];
        $scope.idAccount = $stateParams.id;
        $scope.urlAccountList = constantDashboardConfig.urlPeticion.account;
        $scope.arrConfigDashboard = [];

        //Sidenav Options
        $scope.sideNavOptions = {
          open: function (index) {
            $scope.objItemSelected = {};
            $scope.objItemSelected.config = angular.copy($scope.arrConfigDashboard[index]);
            $scope.objItemSelected.index = index;
            $scope.objItemSelected.changeIcon = false;
            $mdSidenav('right').toggle();
          },
          close: function () {
            $scope.arrConfigDashboard[$scope.objItemSelected.index] = angular.copy($scope.objItemSelected.config);
            delete $scope.objItemSelected;
            $mdSidenav('right').close();
          },
          forDefault: function () {
            for (var i in $scope.arrDefaultDashboard.items) {
              if ($scope.objItemSelected.config.title == $scope.arrDefaultDashboard.items[i].title) {
                $scope.arrConfigDashboard[$scope.objItemSelected.index] = $scope.arrDefaultDashboard.items[i];
              }
            }
            delete $scope.objItemSelected;
            $mdSidenav('right').close();
          },
          saveConfig: function () {
            if (!$scope.objItemSelected.changeIcon) {
              $scope.arrConfigDashboard[$scope.objItemSelected.index] = angular.copy($scope.objItemSelected.config);
            } else {
              $scope.objItemSelected.config.imageDashboard = $scope.objItemSelected.changeIcon;
              $scope.arrConfigDashboard[$scope.objItemSelected.index] = angular.copy($scope.objItemSelected.config);
            }
            $scope.sideNavOptions.close();
            delete $scope.objItemSelected;
          },
        };
        //Draggable Options
        $scope.draggableOptions = {
          remove: function (index) {
            console.log(index);
          }
        }

        //Controller Dialog Adjuntos
        function DialogController($scope, $mdDialog, constantDashboardConfig, items) {
          $parent = $scope.$parent;
          $scope.imageAccount = $parent.imageAccount;
          $scope.idAccount = $parent.idAccount;
          $scope.page = 0;
          $scope.UrlImageBase = constantDashboardConfig.urlBaseFolderImage + $scope.idAccount + "/";

          $scope.funcUniversal = {
            selectedImage: function ($index) {
              if (items == 'top') {
                $parent.topImage = $scope.UrlImageBase + $scope.imageAccount.items[$index].name;
              } else if (items == 'bottom') {
                $parent.bottomImage = $scope.UrlImageBase + $scope.imageAccount.items[$index].name;
              } else {
                var imageSelected = $scope.imageAccount.items[$index];
                $parent.arrConfigDashboard[items].imageDashboard = $scope.UrlImageBase + imageSelected.name;
                $parent.objItemSelected.changeIcon = $scope.UrlImageBase + imageSelected.name;
              }
            }
          }
          $scope.closeDialog = function () {
            $mdDialog.hide();
            if (!items) {
              $parent.universalAction.getImagen();
            }
          }
        }

        //Controller Dialog Preview
        function DialogControllerPreview($scope, $mdDialog) {
          $scope.dashboarPreview = {};
          $scope.dashboarPreview.items = angular.copy($scope.$parent.arrConfigDashboard);
          $scope.dashboarPreview.topImage = angular.copy($scope.$parent.topImage);
          $scope.dashboarPreview.bottomImage = angular.copy($scope.$parent.bottomImage);

          $scope.closeDialog = function () {
            $mdDialog.hide();
          }
        }

        //Actions scope
        $scope.universalAction = {
          openModalAdj: function ($event) {
            document.body.scrollTop = 0;
            var parentEl = angular.element(document.body);
            $mdDialog.show({
              scope: $scope.$new(),
              parent: parentEl,
              targetEvent: $event,
              template: constantDashboardConfig.templates.templateUpload,
              controller: DialogController,
              locals: {
                items: false
              }
            });
          },
          openModalImage: function (provider, $event) {
            document.body.scrollTop = 0;
            var parentEl = angular.element(document.body);
            $mdDialog.show({
              scope: $scope.$new(),
              parent: parentEl,
              targetEvent: $event,
              template: constantDashboardConfig.templates.templateSelectedImage,
              controller: DialogController,
              locals: {
                items: provider
              }
            });
          },
          openModalPreview: function ($event) {
            document.body.scrollTop = 0;
            var parentEl = angular.element(document.body);
            $mdDialog.show({
              scope: $scope.$new(),
              parent: parentEl,
              targetEvent: $event,
              template: constantDashboardConfig.templates.templatePreview,
              controller: DialogControllerPreview
            });
          },
          getImagen: function () {
            try {
              restService.getImageAccountDashboard($scope.idAccount)
                .then(function (data) {
                  $scope.imageAccount = data;
                  $scope.loadignComplete = true;
                })
                .catch(function (err) {
                  notificationService.error(err);
                });
            } catch (err) {
              console.Error('getImageCatch', err);
            }

          },
          getConfigDashboard: function () {
            try {
              restService.getConfigDashboard($scope.idAccount)
                .then(function (data) {
                  if (!data) {
                    $scope.universalAction.setScope(false);
                  } else {
                    $scope.universalAction.setScope(data.content);
                  }
                })
                .catch(function (err) {
                  notificationService.error(err);
                });
            } catch (err) {
              console.Error('getImageCatch', err);
            }
          },
          getConfigDefaultDashboard: function () {
            try {
              restService.getConfigDefaultDashboard()
                .then(function (data) {
                  $scope.arrConfigDashboardDefault = JSON.parse(data.content);
                })
                .catch(function (err) {
                  notificationService.error(err.message);
                })
                .finally(function () {
                  $scope.universalAction.getConfigDashboard();
                });
            } catch (err) {
              console.Error('getImageCatch', err);
            }
          },
          setScope: function (configAccount) {
            if (!configAccount) {
              $scope.arrDefaultDashboard = angular.copy($scope.arrConfigDashboardDefault);
            } else {
              $scope.arrDefaultDashboard = JSON.parse(configAccount);
            }

            $scope.arrConfigDashboard = angular.copy($scope.arrDefaultDashboard.items);
            $scope.topImage = angular.copy($scope.arrDefaultDashboard.topImage);
            $scope.bottomImage = angular.copy($scope.arrDefaultDashboard.bottomImage);
          },
          saveConfig: function () {
            try {
              data = {items: $scope.arrConfigDashboard, topImage: $scope.topImage, bottomImage: $scope.bottomImage};
              restService.saveConfig($scope.idAccount, data)
                .then(function (data) {
                  notificationService.info(data.message);
                })
                .catch(function (e) {
                  notificationService.error(e.message);
                });
            } catch (err) {
              console.log("CatchSaveConfig", err);
            }
          }
        }

        //Uploader
        var uploader = $scope.uploader = new FileUploader({
          url: constantDashboardConfig.urlPeticion.uploadImage + $scope.idAccount
        });

        uploader.onAfterAddingFile = function (fileItem) {
          $scope.uploader.queue[$scope.uploader.queue.length - 1].upload();
        };

        //option Droppable
        $scope.optionDroppable = {
//                accept: function (dragEl) {
//                  if ($scope.list1.length >= 1) {
//                    return false;
//                  } else {
//                    return true;
//                  }
//                },
//                classes: {
//                  "ui-droppable-active": "hover-droppable",
//                  "ui-droppable-hover": "hover-droppable"
//                },
          opacity: 0.4,
        };

        //Action Droppable
        $scope.actionDroppable = {
          onOver: function (event, ui) {
            element = angular.element(document.getElementById('droppable'));
            $(element[0])
              .removeClass("boder-droppable")
              .addClass("hover-droppable");
          },
          onDrop: function (event, ui) {
            element = angular.element(document.getElementById('droppable'));
            $(element[0])
              .removeClass("hover-droppable")
              .addClass("boder-droppable")
              .find("p")
              .html("");
          },
          remove: function (index) {
            objList = $scope.arrConfigDashboard[index];
            for (i in $scope.arrDefaultDashboard.items) {
              if (objList.title == $scope.arrDefaultDashboard.items[i].title) {
                $scope.arrConfigDashboard.splice(index, 1);
                $scope.arrServices.splice(objList.jqyoui_pos, 1, $scope.arrDefaultDashboard.items[i]);
              }
            }
          }
        }

        $scope.$watch('arrConfigDashboardDefault', function () {
          console.log($scope.arrConfigDashboardDefault);
        }, true);

      }])
    .controller('dashboard', ['$scope', 'constantDashboardConfig', 'restService', '$q', 'notificationService', function ($scope, constantDashboardConfig, restService, $q, notificationService) {
        $scope.arrConfigDashboard = [];
        $scope.domain = "";
        $scope.idSubaccount = 0;
        $scope.validatedomain = false;
        $scope.go = function (url) {
          window.location.href = (typeof url != "undefined") ? url : "#";
        }
        $scope.setUrlItem = function (item) {
          var defer = $q.defer();
          var promise = defer.promise;

        }
        $scope.closeModalDKIM = function () {
            $('#adjun').modal('hide');
        };
        $scope.getConfigAccount = function (idAccount) {
          var _this = this;
          var dataService;
          try {
            restService.getservices().then(function (dataServices) {
                _this.dataService = dataServices;
                restService.getConfigDashboardClient(idAccount)
                  .then(function (dataConfig) {
                    var arrConfigDashboard = JSON.parse(dataConfig.content);
                    if (_this.dataService.length > 0) {
                      for (var i = 0; i < arrConfigDashboard.items.length; i++) {
                        var item = angular.copy(arrConfigDashboard.items[i]);

                        //No son servicios pero existen (Base de datos, reportes)
                        if (typeof item.ref !== "undefined" && typeof item.idService === "undefined") {
                          var key = item.ref;
                          item.hrefEnlace = constantDashboardConfig.urlServices[key];
                          arrConfigDashboard.items.splice(i, 1, item);
                          continue;
                        }

                        //No existe el servicio
                        if (typeof item.idService === "undefined") {
                          item.hrefEnlace = (typeof item.hrefEnlaceNewServices != "undefined") ? item.hrefEnlaceNewServices : "#";
                          arrConfigDashboard.items.splice(i, 1, item);
                          continue;
                        }

                        //SI ESTA ASIGNADO EL SERVICIO 
                        for (j in _this.dataService) {
                          //Existe el servicio y lo tiene asignado
                          if (_this.dataService[j].idService == item.idService) {
                            switch (item.idService) {
                              case 1:
                                item.hrefEnlace = constantDashboardConfig.urlServices.sms;
                                break;
                              case 2:
                                item.hrefEnlace = constantDashboardConfig.urlServices.email;
                                $scope.ShowModalDkim(_this.dataService[j].validatedkim,_this.dataService[j].idSubaccount);
                                break;
                              case 4:
                                item.hrefEnlace = constantDashboardConfig.urlServices.automatic_campaign;
                                break;
                              case 5:
                                item.hrefEnlace = constantDashboardConfig.urlServices.survey;
                                break;
                              case 7:
                                item.hrefEnlace = constantDashboardConfig.urlServices.smstwoway;
                                break;
                            }
                          }
                        }

                        arrConfigDashboard.items.splice(i, 1, item);
                      }
                    } else {

                    }
                    $scope.arrConfigDashboard = arrConfigDashboard;
                  });
              })
          } catch (err) {
            console.log("Error", err);
          }

        }
        
        $scope.ShowModalDkim = function (prm,prm2) {
           if(prm == false){
            $scope.idSubaccount = prm2;
            setTimeout(function(){
                 $('#adjun').modal('toggle');
             }, 200);   
           }
        };
        
        $scope.save = function () {
            if($scope.domain =='' || $scope.domain == undefined){
                notificationService.error("El campo dominio no puede estar vacío");
                $scope.validatedomain = true;
                //$('#domain').focus();
                $("#domain").focus(function(){ $(this).addClass("focused")}).focus();
            }else{
                restService.saveDKIM($scope.idSubaccount,$scope.domain).then(function (data) {
                    $scope.closeModalDKIM();
                    notificationService.success(data.message);                    
                }).catch(function (error) {
                    $scope.closeModalDKIM();
                    notificationService.error(error.message);
                });  
            }

        };
      }]);
})();


