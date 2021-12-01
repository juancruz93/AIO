(function () {
  angular.module('customizing.controllers', [])
          .controller('customizingController', ['$scope', '$window', 'restService', 'notificationService', '$state', function ($scope, $window, restService, notificationService, $state) {
              $scope.customizing = [];
              $scope.getAll = function () {
                restService.getAll().then(function (data) {
//                  console.log("Datos: ");
//                  console.log(data);
                  $scope.customizing = data;
                });
              };

              $scope.addBlockInfo = function () {
                var data = {
                  textItemBlockInfo: $scope.textItemBlockInfo,
                  positionItemBlockInfo: $scope.positionItemBlockInfo
                };
                restService.saveItemBlockInfo(data).then(function () {
                });
              }

              $scope.mouseover = function (id) {
                $('#theme' + id).find(".menutheme").show();
              }

              $scope.mouseleave = function (id) {
                $('#theme' + id).find(".menutheme").hide();
              }

              $scope.getAll();

              $scope.confirmDelete = function (idPersonalizationThemes) {
                $scope.idPersonalizationThemes = idPersonalizationThemes;
                openModal();
              };
              $scope.deleteTheme = function () {
                restService.deleteTheme($scope.idPersonalizationThemes).then(function (data) {
                  closeModal();
                  notificationService.warning(data.message);
                  $scope.getAll();
                });
              };
              $scope.selectTheme = function (idPersonalizationThemes) {

                $scope.idPersonalizationThemes = idPersonalizationThemes;
                restService.selectTheme($scope.idPersonalizationThemes).then(function (data) {
                  notificationService.info(data.message);
                  $scope.getAll();
                  window.location.reload();
                });
              };

              $scope.openModalPreview = function (theme) {
                $scope.linkColor = theme.linkColor;
                $scope.linkHoverColor = theme.linkHoverColor;
                $scope.mainColor = theme.mainColor;
                $scope.userBoxColor = theme.userBoxColor;
                $scope.userBoxHoverColor = theme.userBoxHoverColor;
                $scope.footerIconColor = theme.footerIconColor;
                $scope.headerTextColor = theme.headerTextColor;
                $scope.headerColor = theme.headerColor;
                $scope.footerColor = theme.footerColor;
                $scope.mainTitle = theme.mainTitle;

                if (theme.logoRoute) {
                  $('.imgOnLoad').attr('src', theme.logoRoute);
                  $(".imgOnLoad").removeClass("hidden");
                  $("#tittletext").removeClass("hidden");
                  $("#mainTitle").hide();
                } else {
                  $(".imgOnLoad").addClass("hidden");
                  $("#mainTitle").show();
                }
                $("#preview").addClass('dialog--open');
              }

              $scope.closeModalPreview = function () {
                $("#preview").removeClass('dialog--open');
              }
            }])
          .controller('customizingAddController', ['$scope', '$window', 'restService', 'notificationService', function ($scope, $window, restService, notificationService) {

              $scope.title = 'Index';
              $scope.headerColor = '#fff';
              $scope.mainColor = '#ff6e00';
              $scope.linkColor = '#00bede';
              $scope.linkHoverColor = '#ff6e00';
              $scope.footerColor = '#ddd';
              $scope.headerTextColor = '#777777';
              $scope.mainTitle = 'LOGO';
              $scope.footerIconColor = '#777777';
              $scope.userBoxColor = '#ddd';
              $scope.userBoxHoverColor = '#eeeeee';

              $scope.socials = [{}];
              $scope.infos = [{}];
              $scope.socialsordered = [{}];
              $scope.infosordered = [{}];

              $scope.showSocial = false;
              $scope.showBlockSocial = false;
              $scope.showInfo = false;
              $scope.showBlockInfo = false;

              $scope.socialBlockPosition = "right";
              $scope.infoBlockPosition = "left";

              $scope.idPersonalizationThemes = null;
              $scope.socialNetworks = function () {
                restService.getSocialNetworks().then(function (data) {
                  $scope.socialnetworks = data;
//                console.log($scope.socialnetworks[0].items.length);
                });
              };
              $scope.orderBlocks = function () {
//                console.log($scope.socialsordered);
                var infoBlock = '';
                if ($scope.infos) {

                  for (x = 0; x < $scope.infos.length; x++) {
                    if (typeof $scope.infos[x].textInfo != "undefined") {
                      infoBlock += '<div class="item-info">' + $scope.infos[x].textInfo + '</div>';
                    }
                  }
                }

                var socialBlock = '<div class="social-network" ng-if="showBlockSocial">';
                if ($scope.socialsordered) {

                  for (x = 0; x < $scope.socialsordered.length; x++) {
//                  console.log($scope.socialsordered[x].idSocial);
//                  console.log($scope.socialnetworks[0].items);
                    if ($scope.showBlockSocial) {
                      socialBlock += '<a href="' + $scope.socialsordered[x].urlSocial + '" class="social-item" target="_blank" data-toggle="tooltip" data-placement="top" title="' + $scope.socialsordered[x].titleSocial + '">';
                      socialBlock += '<img style="width: 15px;" src="' + $scope.socialnetworks[1].url + 'themes/default/images/social-networks/' + $scope.socialnetworks[0].items[$scope.socialsordered[x].idSocial - 1].img + '" /></a>';
                    }
                  }
                }
                socialBlock += '</div>';
                if ($scope.socialBlockPosition == "right") {
                  $('.left-position').html(infoBlock);
                  $('.right-position').html(socialBlock);
                } else {
                  $('.left-position').html(socialBlock);
                  $('.right-position').html(infoBlock);
                }
              }
              $scope.viewImg = function (input) {
                if (input.files && input.files[0]) {
                  var reader = new FileReader();
                  reader.onload = function (e) {
                    $('.imgOnLoad').attr('src', e.target.result);
                    $(".imgOnLoad").removeClass("hidden");
                    $("#tittletext").removeClass("hidden");
                    $("#mainTitle").hide();
                  };
                  reader.readAsDataURL(input.files[0]);
                }
              }
              $scope.file = [];
              $scope.pushFiles = function (element, name) {
                $scope.$apply(function ($scope) {
                  element.files.id = name;
                  $scope.file = element.files;
                });
              };
              $scope.alterSocialBlockPosition = function () {
                if ($scope.socialBlockPosition == "right") {
                  $scope.infoBlockPosition = "left";
                } else if ($scope.socialBlockPosition == "left") {
                  $scope.infoBlockPosition = "right";
                }

              }
              $scope.alterInfoBlockPosition = function () {
                if ($scope.infoBlockPosition == "right") {
                  $scope.socialBlockPosition = "left";
                } else if ($scope.infoBlockPosition == "left") {
                  $scope.socialBlockPosition = "right";
                }

              }
              $scope.orderSocial = function () {
                if (!$scope.socialsordered) {
                  $scope.socialsordered = [];
                }
                if ($scope.socials) {
                  var cont = $scope.socials.length;
                  for (x = 1; x <= cont; x++) {
                    for (y = 0; y < $scope.socials.length; y++) {
                      if ($scope.socials[y].positionSocial == x) {
                        $scope.socialsordered[x - 1] = $scope.socials[y];
                      }

                    }
                  }
                }
              }
              $scope.orderInfo = function () {
                if (!$scope.infosordered) {
                  $scope.infosordered = [];
                }
                if ($scope.infos) {
                  var cont = $scope.infos.length;
                  for (x = 1; x <= cont; x++) {
                    for (y = 0; y < $scope.infos.length; y++) {
                      if ($scope.infos[y].positionInfo == x) {
                        $scope.infosordered[x - 1] = $scope.infos[y];
                      }

                    }
                  }
                }
              }
              $scope.isEmptyPosition = function () {
                $(".positionEmptyError").remove();
                $scope.showBlockSocial = true;
                cont = 0;
                for (x = 0; x < $scope.socials.length; x++) {
                  if ($scope.socials[x].positionSocial == null) {
                    cont++;
                  }
                }
                if (cont > 0) {
                  $("#errors").append('<h6 class="positionEmptyError" style="color:red;"><b>Error:</b> Hay posiciones vacías o alguna de las posiciones no es válida</h6>');
                  $scope.showBlockSocial = false;
                }
              }
              $scope.isEmptyInfoPosition = function () {
                $(".positionInfoEmptyError").remove();
                $scope.showBlockInfo = true;
                cont = 0;
                for (x = 0; x < $scope.infos.length; x++) {
                  if ($scope.infos[x].positionInfo == null) {
                    cont++;
                  }
                }
                if (cont > 0) {
                  $("#errorsInfo").append('<h6 class="positionInfoEmptyError" style="color:red;"><b>Error:</b> Hay posiciones vacías o alguna de las posiciones no es válida</h6>');
                  $scope.showBlockInfo = false;
                }
              }
              $scope.isOrderedPosition = function () {
                $(".positionOrderedError").remove();
                $scope.showBlockSocial = true;
//                console.log($scope.socials);
                for (x = 1; x <= $scope.socials.length; x++) {
                  for (y = 0; y < $scope.socials.length; y++) {
                    if ($scope.socials[y].positionSocial == x) {
                      cont++;
                    }
                  }
                }
                if (cont != $scope.socials.length) {
                  $("#errors").append('<h6 class="positionOrderedError" style="color:red;"><b>Error:</b> Posiciones en desorden</h6>');
                  $scope.showBlockSocial = false;
                }
              }
              $scope.isOrderedInfoPosition = function () {
                $(".positionInfoOrderedError").remove();
                $scope.showBlockInfo = true;
                for (x = 1; x <= $scope.infos.length; x++) {
                  for (y = 0; y < $scope.infos.length; y++) {
                    if ($scope.infos[y].positionInfo == x) {
                      cont++;
                    }
                  }
                }
                if (cont != $scope.infos.length) {
                  $("#errorsInfo").append('<h6 class="positionInfoOrderedError" style="color:red;"><b>Error:</b> Posiciones en desorden</h6>');
                  $scope.showBlockInfo = false;
                }
              }
              $scope.getInfoSocial = function (id, idSocialNetwork) {

//                console.log("SocialNetwork: "+SocialNetwork);
//                console.log(JSON.parse(SocialNetwork).idSocialNetwork);
//                console.log(JSON.parse(SocialNetwork).name);
//                var idSocialNetwork = JSON.parse(SocialNetwork).idSocialNetwork;
//                console.log("$scope.socialnetworks[0]"+$scope.socialnetworks[0].items[idSocialNetwork-1].img);
                $("#imgSocial" + id).html('<img style="width: 60%;" src="' + $scope.socialnetworks[1].url + 'themes/default/images/social-networks/' + $scope.socialnetworks[0].items[idSocialNetwork - 1].img + '" />');

              }
              $scope.validateSocialPosition = function (index) {
                $(".positionError").remove();
                $scope.showBlockSocial = true;
                cont = 0;
                if ($scope.socials.length > 1) {
                  for (x = 0; x < $scope.socials.length; x++) {
                    for (y = 0; y < $scope.socials.length; y++) {
//                    console.log("x: "+$scope.socials[x].positionSocial);
//                    console.log("y: "+$scope.socials[y].positionSocial);
                      if ($scope.socials[x].positionSocial == $scope.socials[y].positionSocial)
                        cont++;
//                    console.log(cont);
                    }
                  }
                }
                if (cont > $scope.socials.length) {
                  $("#errors").append('<h6 class="positionError" style="color:red;"><b>Error:</b> posición repetida</h6>');
                  $scope.showBlockSocial = false;
                }

              }
              $scope.validateInfoPosition = function (index) {
                $(".positionInfoError").remove();
                $scope.showBlockInfo = true;
                cont = 0;
                if ($scope.infos.length > 1) {
                  for (x = 0; x < $scope.infos.length; x++) {
                    for (y = 0; y < $scope.infos.length; y++) {
                      if ($scope.infos[x].positionInfo == $scope.infos[y].positionInfo)
                        cont++;
                    }
                  }
                }
                if (cont > $scope.infos.length) {
                  $("#errorsInfo").append('<h6 class="positionInfoError" style="color:red;"><b>Error:</b> posición repetida</h6>');
                  $scope.showBlockInfo = false;
                }

              }
              $scope.newOne = function () {
                if ($scope.socials.length < $scope.socialnetworks[0].items.length) {
                  $scope.socials.push({});
                }
              }
              $scope.newOneAdditionalInfo = function () {
                if ($scope.infos.length < 2) {
                  $scope.infos.push({});
                }
              }
              $scope.deleteOne = function (index, idSocial) {
                $scope.socials.splice(index, 1);
                if ($scope.socials.length == 0) {
                  $scope.showSocial = false;
                  $scope.showBlockSocial = false;
                }
                $(".positionEmptyError").remove();
                $(".positionOrderedError").remove();
                $(".positionError").remove();
              }
              $scope.deleteOneInfo = function (index) {
                $scope.infos.splice(index, 1);
                if ($scope.infos.length == 0) {
                  $scope.showInfo = false;
                  $scope.showBlockInfo = false;
                }
                $(".positionInfoEmptyError").remove();
                $(".positionInfoOrderedError").remove();
                $(".positionInfoError").remove();
              }
              $scope.registerSocialNetwork = function () {
                $scope.showSocial = true;
                $scope.showBlockSocial = true;
                if ($scope.socials.length == 0) {
                  $scope.socials.push({});
                }
              }
//              $scope.registerSocialNetwork = function () {
//                $scope.showSocial = true;
//                $scope.showBlockSocial = true;
//                if (Array.isArray($scope.customizing.socials)) {
//                  if ($scope.customizing.socials.length == 0) {
//                    $scope.customizing.socials.push({});
//                  }
//                } else {
//                  if ($scope.customizing.socials == null) {
//                    $scope.customizing.socials = [];
//                    $scope.customizing.socials.push({});
//                  }
//                }
//              }
              $scope.registerAdditionalInfo = function () {
                $scope.showInfo = true;
                $scope.showBlockInfo = true;
                if ($scope.infos.length == 0) {
                  $scope.infos.push({});
                }
              }
              $scope.openModal = function (id) {
                $("#" + id).addClass('dialog--open');
              }
              $scope.closeModal = function (id) {
                $("#" + id).removeClass('dialog--open');
              }
              $scope.getContrastYIQ = function (hexcolor) {
                if (hexcolor == "#fff") {
                  hexcolor = "#ffff"
                }
//                console.log(hexcolor);
                var r = parseInt(hexcolor.substr(1, 2), 16);
//                console.log("r"+r);
                var g = parseInt(hexcolor.substr(2, 2), 16);
//                console.log("g"+g);
                var b = parseInt(hexcolor.substr(4, 2), 16);
//                console.log("b"+hexcolor.substr(4, 2), 16);
                var yiq = ((r * 299) + (g * 587) + (b * 114)) / 1000;
                return (yiq >= 128) ? 'black' : 'white';
              }

              $scope.addTheme = function () {
                var data = {
                  id: $scope.idCustomizing,
                  name: $scope.name,
                  description: $scope.description,
                  title: $scope.title,
                  headerColor: $scope.headerColor,
                  mainColor: $scope.mainColor,
                  linkColor: $scope.linkColor,
                  linkHoverColor: $scope.linkHoverColor,
                  footerColor: $scope.footerColor,
                  headerTextColor: $scope.headerTextColor,
                  mainTitle: $scope.mainTitle,
                  footerIconColor: $scope.footerIconColor,
                  userBoxColor: $scope.userBoxColor,
                  userBoxHoverColor: $scope.userBoxHoverColor,
                  socialsordered: $scope.socials,
                  infosordered: $scope.infos,
                  socials: $scope.socialsordered,
                  infos: $scope.infosordered,
                  socialBlockPosition: $scope.socialBlockPosition,
                  infoBlockPosition: $scope.infoBlockPosition,
                  file: $scope.file,
                };

                restService.save(data).then(function (data) {
                  $window.location.href = '#/';
                  notificationService.success(data.message);
                });
              };
              $scope.saveAndContinue = function () {
                var data = {
                  id: $scope.idPersonalizationThemes,
                  name: $scope.name,
                  description: $scope.description,
                  title: $scope.title,
                  headerColor: $scope.headerColor,
                  mainColor: $scope.mainColor,
                  linkColor: $scope.linkColor,
                  linkHoverColor: $scope.linkHoverColor,
                  footerColor: $scope.footerColor,
                  headerTextColor: $scope.headerTextColor,
                  mainTitle: $scope.mainTitle,
                  footerIconColor: $scope.footerIconColor,
                  userBoxColor: $scope.userBoxColor,
                  userBoxHoverColor: $scope.userBoxHoverColor,
                  socialsordered: $scope.socials,
                  infosordered: $scope.infos,
                  socials: $scope.socialsordered,
                  infos: $scope.infosordered,
                  socialBlockPosition: $scope.socialBlockPosition,
                  infoBlockPosition: $scope.infoBlockPosition,
                  file: $scope.file,
                };

                restService.save(data).then(function (data) {
                  $scope.idPersonalizationThemes = data.customizing.idPersonalizationThemes;
                  notificationService.success(data.message);
                });
              };
              $scope.socialNetworks();
            }])

          .controller('customizingEditController', ['$scope', '$stateParams', '$window', 'restService', 'notificationService', function ($scope, $stateParams, $window, restService, notificationService) {
              $scope.customizing = [];
              $scope.socialsDeleted = [];
              $scope.infosDeleted = [];
              var id = $stateParams.id;
              var data = {};

              $scope.idSocialNet = "";
//              $scope.socials = [{}];
//              $scope.infos = [{}];
//              $scope.socialsordered = [{}];
//              $scope.infosordered = [{}];
              $scope.showSocial = false;
              $scope.showBlockSocial = false;
              $scope.showInfo = false;
              $scope.showBlockInfo = false;



              $scope.customizing.socialBlockPosition = "right";
              $scope.customizing.infoBlockPosition = "left";

              restService.getOne(id).then(function (data) {
                $scope.customizing = data;
                restService.getSocialNetworks().then(function (data) {

                  $scope.customizing.socialnetworks = data;
//                console.log($scope.socialnetworks[0].items.length);
                  if (Array.isArray($scope.customizing.socials)) {
                    if ($scope.customizing.socials.length > 0) {
                      $scope.showSocial = true;
                      $scope.showBlockSocial = true;
                    }
                  } else {
                    if ($scope.customizing.socials != null) {
                      $scope.showSocial = true;
                      $scope.showBlockSocial = true;
                    }
                  }
                  if (Array.isArray($scope.customizing.infos)) {
                    if ($scope.customizing.infos.length > 0) {
                      $scope.showInfo = true;
                      $scope.showBlockInfo = true;
                    }
                  } else {
                    if ($scope.customizing.infos != null) {
                      $scope.showInfo = true;
                      $scope.showBlockInfo = true;
                    }
                  }
                });
//                for(var i in $scope.customizing.socials){
//                  $scope.getInfoSocial(i,$scope.customizing.socials[i].idSocial);
//                }
                if ($scope.customizing.socialBlockPosition == "right") {
                  $scope.customizing.infoBlockPosition = "left";
                } else if ($scope.customizing.socialBlockPosition == "left") {
                  $scope.customizing.infoBlockPosition = "right";
                } else if ($scope.customizing.infoBlockPosition == "right") {
                  $scope.customizing.socialBlockPosition = "left";
                } else if ($scope.customizing.infoBlockPosition == "left") {
                  $scope.customizing.socialBlockPosition = "right";
                } else if ($scope.customizing.socialBlockPosition == null && $scope.customizing.infoBlockPosition == null) {
                  $scope.customizing.socialBlockPosition = "right";
                  $scope.customizing.infoBlockPosition = "left";
                }
//                console.log($scope.customizing);
//                console.log($scope.customizing.logoRoute);
                if ($scope.customizing.logoRoute) {
                  $('.imgOnLoad').attr('src', $scope.customizing.logoRoute);
                  $(".imgOnLoad").removeClass("hidden");
                  $("#tittletext").removeClass("hidden");
                  $("#mainTitle").hide();
                }
              });
              $scope.orderBlocks = function () {
                var infoBlock = '';
                if ($scope.customizing.infos) {
                  for (x = 0; x < $scope.customizing.infos.length; x++) {
                    if (typeof $scope.customizing.infos[x].textInfo != "undefined") {
                      infoBlock += '<div class="item-info">' + $scope.customizing.infos[x].textInfo + '</div>';
                    }
                  }
                }


                var socialBlock = '<div class="social-network" ng-if="showBlockSocial">';
                if ($scope.customizing.socialsordered) {
                  for (x = 0; x < $scope.customizing.socialsordered.length; x++) {
//                  console.log($scope.socialsordered[x].idSocial);
                    if ($scope.showBlockSocial) {
                      socialBlock += '<a href="' + $scope.customizing.socialsordered[x].urlSocial + '" class="social-item" target="_blank" data-toggle="tooltip" data-placement="top" title="' + $scope.customizing.socialsordered[x].titleSocial + '">';
                      socialBlock += '<img style="width: 23px;" src="' + $scope.customizing.socialnetworks[1].url + 'themes/default/images/social-networks/' + $scope.customizing.socialnetworks[0].items[$scope.customizing.socialsordered[x].idSocial - 1].img + '" /></a>';
                    }
                  }
                }
                socialBlock += '</div>';

                if ($scope.customizing.socialBlockPosition == "right") {
                  $('.left-position').html(infoBlock);
                  $('.right-position').html(socialBlock);
                } else {
                  $('.left-position').html(socialBlock);
                  $('.right-position').html(infoBlock);
                }
              }
              $scope.viewImg = function (input) {
                if (input.files && input.files[0]) {
                  var reader = new FileReader();
                  reader.onload = function (e) {
                    $('.imgOnLoad').attr('src', e.target.result);
                    $(".imgOnLoad").removeClass("hidden");
                    $("#tittletext").removeClass("hidden");
                    $("#mainTitle").hide();

                  };
                  reader.readAsDataURL(input.files[0]);
                }
              }
              $scope.file = [];

              $scope.pushFiles = function (element, name) {
                $scope.$apply(function ($scope) {
                  element.files.id = name;
                  $scope.file = element.files;
                });
              };

              $scope.alterSocialBlockPosition = function () {
                if ($scope.customizing.socialBlockPosition == "right") {
                  $scope.customizing.infoBlockPosition = "left";
                } else if ($scope.customizing.socialBlockPosition == "left") {
                  $scope.customizing.infoBlockPosition = "right";
                }

              }
              $scope.alterInfoBlockPosition = function () {
                if ($scope.customizing.infoBlockPosition == "right") {
                  $scope.customizing.socialBlockPosition = "left";
                } else if ($scope.customizing.infoBlockPosition == "left") {
                  $scope.customizing.socialBlockPosition = "right";
                }

              }
              $scope.orderSocial = function () {
                if (!$scope.customizing.socialsordered) {
                  $scope.customizing.socialsordered = [];
                }
                if ($scope.customizing.socials) {
                  var cont = $scope.customizing.socials.length;
                  for (x = 1; x <= cont; x++) {
                    for (y = 0; y < $scope.customizing.socials.length; y++) {
                      if ($scope.customizing.socials[y].positionSocial == x) {
                        $scope.customizing.socialsordered[x - 1] = $scope.customizing.socials[y];
                      }

                    }
                  }
                }
              }
              $scope.orderInfo = function () {
                if (!$scope.customizing.infosordered) {
                  $scope.customizing.infosordered = [];
                }
                if ($scope.customizing.infos) {
                  var cont = $scope.customizing.infos.length;
                  for (x = 1; x <= cont; x++) {
                    for (y = 0; y < $scope.customizing.infos.length; y++) {
                      if ($scope.customizing.infos[y].positionInfo == x) {
                        $scope.customizing.infosordered[x - 1] = $scope.customizing.infos[y];
                      }

                    }
                  }
                }
              }
//              $scope.isEmptyPosition = function () {
//                $(".positionEmptyError").remove();
//                $scope.showBlockSocial = true;
//                cont = 0;
//                for (x = 0; x < $scope.customizing.socials.length; x++) {
//                  if ($scope.customizing.socials[x].positionSocial == null) {
//                    cont++;
//                  }
//                }
//                if (cont > 0) {
//                  $("#errors").append('<h6 class="positionEmptyError" style="color:red;"><b>Error:</b> Hay posiciones vacías o alguna de las posiciones no es válida</h6>');
//                  $scope.showBlockSocial = false;
//                }
//              }
//              $scope.isEmptyInfoPosition = function () {
//                $(".positionInfoEmptyError").remove();
//                $scope.showBlockInfo = true;
//                cont = 0;
//                for (x = 0; x < $scope.customizing.infos.length; x++) {
//                  if ($scope.customizing.infos[x].positionInfo == null) {
//                    cont++;
//                  }
//                }
//                if (cont > 0) {
//                  $("#errorsInfo").append('<h6 class="positionInfoEmptyError" style="color:red;"><b>Error:</b> Hay posiciones vacías o alguna de las posiciones no es válida</h6>');
//                  $scope.showBlockInfo = false;
//                }
//              }
//              $scope.isOrderedPosition = function () {
//                $(".positionOrderedError").remove();
//                $scope.showBlockSocial = true;
////                console.log($scope.socials);
//                for (x = 1; x <= $scope.customizing.socials.length; x++) {
//                  for (y = 0; y < $scope.customizing.socials.length; y++) {
//                    if ($scope.customizing.socials[y].positionSocial == x) {
//                      cont++;
//                    }
//                  }
//                }
//                if (cont != $scope.customizing.socials.length) {
//                  $("#errors").append('<h6 class="positionOrderedError" style="color:red;"><b>Error:</b> Posiciones en desorden</h6>');
//                  $scope.showBlockSocial = false;
//                }
//              }
//              $scope.isOrderedInfoPosition = function () {
//                $(".positionInfoOrderedError").remove();
//                $scope.showBlockInfo = true;
//                for (x = 1; x <= $scope.customizing.infos.length; x++) {
//                  for (y = 0; y < $scope.customizing.infos.length; y++) {
//                    if ($scope.customizing.infos[y].positionInfo == x) {
//                      cont++;
//                    }
//                  }
//                }
//                if (cont != $scope.customizing.infos.length) {
//                  $("#errorsInfo").append('<h6 class="positionInfoOrderedError" style="color:red;"><b>Error:</b> Posiciones en desorden</h6>');
              //                  $scope.showBlockInfo = false;
//                }
//              }
              $scope.getInfoSocial = function (id, idSocialNetwork) {
//                console.log("SocialNetwork: "+SocialNetwork);
//                console.log(JSON.parse(SocialNetwork).idSocialNetwork);
//                console.log(JSON.parse(SocialNetwork).name);
//                var idSocialNetwork = JSON.parse(SocialNetwork).idSocialNetwork;
                //                console.log("$scope.socialnetworks[0]"+$scope.socialnetworks[0].items[idSocialNetwork-1].img);
                $("#imgSocial" + id).html('<img style="width: 60%;" src="' + $scope.customizing.socialnetworks[1].url + 'themes/default/images/social-networks/' + $scope.customizing.socialnetworks[0].items[idSocialNetwork - 1].img + '" />');

              }
              $scope.validateSocialPosition = function (index) {
                $(".positionError").remove();
                $scope.showBlockSocial = true;
                cont = 0;
                if ($scope.customizing.socials.length > 1) {
                  for (x = 0; x < $scope.customizing.socials.length; x++) {
                    for (y = 0; y < $scope.customizing.socials.length; y++) {
//                    console.log("x: "+$scope.socials[x].positionSocial);
                      //                    console.log("y: "+$scope.socials[y].positionSocial);
                      if ($scope.customizing.socials[x].positionSocial == $scope.customizing.socials[y].positionSocial)
                        cont++;
                      //                    console.log(cont);
                    }
                  }
                }
                if (cont > $scope.customizing.socials.length) {
                  $("#errors").append('<h6 class="positionError" style="color:red;"><b>Error:</b> posición repetida</h6>');
                  $scope.showBlockSocial = false;
                }
                //                console.log($scope.showBlockSocial);

              }
              $scope.validateInfoPosition = function (index) {
                $(".positionInfoError").remove();
                $scope.showBlockInfo = true;
                cont = 0;
                if ($scope.customizing.infos.length > 1) {
                  for (x = 0; x < $scope.customizing.infos.length; x++) {
                    for (y = 0; y < $scope.customizing.infos.length; y++) {
                      if ($scope.customizing.infos[x].positionInfo == $scope.customizing.infos[y].positionInfo)
                        cont++;
                    }
                  }
                }
                if (cont > $scope.customizing.infos.length) {
                  $("#errorsInfo").append('<h6 class="positionInfoError" style="color:red;"><b>Error:</b> posición repetida</h6>');
                  $scope.showBlockInfo = false;
                }

              }
              $scope.validateInfo = function () {
//                console.log($scope.customizing.infos[0].textInfo.length);
//                if (typeof $scope.customizing.infos[0].textInfo != 'undefined') {
//                  if ($scope.customizing.infos[0].textInfo.length > 50) {
                //                    $("#errorsInfo").append('<h6 class="positionInfoError" style="color:red;"><b>Error:</b> El texto no puede superar los 50 caracteres</h6>');
//                  }
//                }
                //Se cancela la validación porque angular convierte la variable en undefined cuando se elimina la etiqueta max-length
              }
              $scope.newOne = function () {
                if (Array.isArray($scope.customizing.socials)) {
                  if ($scope.customizing.socials.length < $scope.customizing.socialnetworks[0].items.length) {
                    $scope.customizing.socials.push({});
                  }
                } else {
                  if ($scope.customizing.socials == null) {
                    $scope.customizing.socials = [];
                    $scope.customizing.socials.push({});
                  }
                }
              }
              $scope.newOneAdditionalInfo = function () {
                if (Array.isArray($scope.customizing.infos)) {
                  if ($scope.customizing.infos.length < 2) {
                    $scope.customizing.infos.push({});
                  }
                }
              }
              $scope.deleteOne = function (index, idSocialDeleted) {
                $scope.socialsDeleted.push(idSocialDeleted);
                $scope.customizing.socials.splice(index, 1);
                if ($scope.customizing.socials.length == 0) {
                  $scope.showSocial = false;
                  $scope.showBlockSocial = false;
                }
                $(".positionEmptyError").remove();
                $(".positionOrderedError").remove();
                $(".positionError").remove();
              }
              $scope.deleteOneInfo = function (index, idInfoDeleted) {
                $scope.infosDeleted.push(idInfoDeleted);
                $scope.customizing.infos.splice(index, 1);
                if ($scope.customizing.infos.length == 0) {
                  $scope.showInfo = false;
                  $scope.showBlockInfo = false;
                }
                $(".positionInfoEmptyError").remove();
                $(".positionInfoOrderedError").remove();
                $(".positionInfoError").remove();
              }
              $scope.registerSocialNetwork = function () {
                $scope.showSocial = true;
                $scope.showBlockSocial = true;
                if (Array.isArray($scope.customizing.socials)) {
                  if ($scope.customizing.socials.length == 0) {
                    $scope.customizing.socials.push({});
                  }
                } else {
                  if ($scope.customizing.socials == null) {
                    $scope.customizing.socials = [];
                    $scope.customizing.socials.push({});
                  }
                }
              }
              $scope.registerAdditionalInfo = function () {
                $scope.showInfo = true;
                $scope.showBlockInfo = true;
                if (Array.isArray($scope.customizing.infos)) {
                  if ($scope.customizing.infos.length == 0) {
                    $scope.customizing.infos.push({});
                  }
                } else {
                  if ($scope.customizing.infos == null) {
                    $scope.customizing.infos = [];
                    $scope.customizing.infos.push({});
                  }
                }
              }
              $scope.openModal = function (id) {
                $("#" + id).addClass('dialog--open');
              }

              $scope.closeModal = function (id) {
                $("#" + id).removeClass('dialog--open');
              }

              $scope.editTheme = function () {
                data = {
                  id: $stateParams.id,
                  name: $scope.customizing.name,
                  description: $scope.customizing.description,
                  title: $scope.customizing.title,
                  headerColor: $scope.customizing.headerColor,
                  mainColor: $scope.customizing.mainColor,
                  linkColor: $scope.customizing.linkColor,
                  linkHoverColor: $scope.customizing.linkHoverColor,
                  footerColor: $scope.customizing.footerColor,
                  headerTextColor: $scope.customizing.headerTextColor,
                  mainTitle: $scope.customizing.mainTitle,
                  footerIconColor: $scope.customizing.footerIconColor,
                  userBoxColor: $scope.customizing.userBoxColor,
                  userBoxHoverColor: $scope.customizing.userBoxHoverColor,
                  socialsordered: $scope.customizing.socials,
                  infosordered: $scope.customizing.infos,
                  socials: $scope.customizing.socialsordered,
                  infos: $scope.customizing.infos,
                  infoBlockId: $scope.customizing.infoBlockId,
                  socialBlockId: $scope.customizing.socialBlockId,
                  socialBlockPosition: $scope.customizing.socialBlockPosition,
                  infoBlockPosition: $scope.customizing.infoBlockPosition,
                  socialsDeleted: $scope.socialsDeleted,
                  infosDeleted: $scope.infosDeleted,
                  file: $scope.file,
                };

                restService.edit(data).then(function (data) {
                  $window.location.href = '#/';
                  window.location.reload();
                  notificationService.info(data.message);
                });
              };
              $scope.saveAndContinue = function () {
                data = {
                  id: $stateParams.id,
                  name: $scope.customizing.name,
                  description: $scope.customizing.description,
                  title: $scope.customizing.title,
                  headerColor: $scope.customizing.headerColor,
                  mainColor: $scope.customizing.mainColor,
                  linkColor: $scope.customizing.linkColor,
                  linkHoverColor: $scope.customizing.linkHoverColor,
                  footerColor: $scope.customizing.footerColor,
                  headerTextColor: $scope.customizing.headerTextColor,
                  mainTitle: $scope.customizing.mainTitle,
                  footerIconColor: $scope.customizing.footerIconColor,
                  userBoxColor: $scope.customizing.userBoxColor,
                  userBoxHoverColor: $scope.customizing.userBoxHoverColor,
                  socialsordered: $scope.customizing.socials,
                  infosordered: $scope.customizing.infos,
                  socials: $scope.customizing.socialsordered,
                  infos: $scope.customizing.infos,
                  infoBlockId: $scope.customizing.infoBlockId,
                  socialBlockId: $scope.customizing.socialBlockId,
                  socialBlockPosition: $scope.customizing.socialBlockPosition,
                  infoBlockPosition: $scope.customizing.infoBlockPosition,
                  socialsDeleted: $scope.socialsDeleted,
                  infosDeleted: $scope.infosDeleted,
                  file: $scope.file,
                };

                restService.edit(data).then(function (data) {
                  notificationService.info(data.message);
                });
              };
            }])
})();
