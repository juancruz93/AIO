/**
 * Autor: Kevin Andres Ramirez Alzate
 * Comment: Pana si lo ve? esta todo bonito y organizado si lo va a tocar dejelo asi de bonito 
 */
angular.element(function () {

  var dependience = ['dashboardconfigService', 'dashboardconfigController', 'dashboardconfigDirective'];
  if (typeof client == "undefined" || client == false) {
    dependience = ['ngDragDrop', 'dashboardconfigService', 'dashboardconfigController', 'dashboardconfigDirective', 'ngMaterial', 'ngSanitize', 'angularFileUpload', 'ui.router'];
  }
  var app = angular.module('dashboardconfigApp', dependience);
  app.constant('constantDashboardConfig', {
    relativeUrlBase: relativeUrlBase,
    fullUrlBase: fullUrlBase,
    templateBase: templateBase,
    urlBaseDefault: urlBaseDefault,
    urlPeticion: {uploadImage: fullUrlBase + 'api/' + templateBase + '/uploadimage/',
      account: fullUrlBase + 'account/',
      getImage: fullUrlBase + 'api/' + templateBase + '/getallimage/',
      getConfigDashboard: fullUrlBase + 'api/' + templateBase + '/getcondigdashboard/',
      saveConfigDashboard: fullUrlBase + 'api/' + templateBase + '/savecondigdashboard/',
      getConfigDashboardClient: fullUrlBase + 'api/' + templateBase + '/getconfigdashboardclient/',
      getDefaultDashboard: fullUrlBase + 'api/' + templateBase + '/getdefaultdashboard',
      urlMailBaiscInformation: fullUrlBase + 'mail/create#/basicinformation/',
      urlSmsTools: fullUrlBase + 'sms/tools'},
    idAllied: idAllied,
    urlBaseFolderImage: fullUrlBase + "images/image_dashboard/" + idAllied + "/",
    templates: {templateUpload: "<md-dialog aria-label=\"\">" +
              //Toolbar  
              "<md-toolbar>" +
              "<div class=\"md-toolbar-tools\">" +
              "<h4 class=\"modal-title\" id=\"exampleModalLabel\">Adjuntar archivos</h4>" +
              "<span flex></span>" +
              "<md-button class=\"md-icon-button\" ng-click=\"cancel()\">" +
              "<md-icon  aria-label=\"Close dialog\"></md-icon>" +
              "</md-button>" +
              "</div>" +
              "</md-toolbar>" +
              //Dialog Content
              "<md-dialog-content >" +
              '<md-content style="min-width:1000px">' +
              '<br>' +
              '<div>' +
              '<input type="file" nv-file-select="" uploader="uploader" multiple  /><br/>' +
              '</div>' +
              '<div  style="margin-bottom: 40px">' +
              '<p>Cantidad de archivos seleccionados: {{ uploader.queue.length }}</p>' +
              '<table class="table table-bordered"  >' +
              '<thead>' +
              '<tr>' +
              '<th width="50%">Nombre</th>' +
              '<th ng-show="uploader.isHTML5">Tama??o</th>' +
              '<th ng-show="uploader.isHTML5">Progreso</th>' +
              '<th>Estado</th>' +
              '</tr>' +
              '</thead>' +
              '<tbody>' +
              '<tr ng-repeat="item in uploader.queue">' +
              '<td><strong>{{ item.file.name }}</strong></td>' +
              '<td ng-show="uploader.isHTML5" nowrap>{{ item.file.size/1024/1024|number:2 }} MB</td>' +
              '<td ng-show="uploader.isHTML5">' +
              '<div class="progress" style="margin-bottom: 0;">' +
              "<div class=\"progress-bar\" role=\"progressbar\" ng-style=\"{ 'width': item.progress + '%' }\"></div>" +
              '</div>' +
              '</td>' +
              '<td class="text-center">' +
              '<span ng-show="item.isSuccess"><i class="glyphicon glyphicon-ok"></i></span>' +
              '<span ng-show="item.isCancel"><i class="glyphicon glyphicon-ban-circle"></i></span>' +
              '<span ng-show="item.isError"><i class="glyphicon glyphicon-remove"></i></span>' +
              '</td>' +
              '</tr>' +
              '</tbody>' +
              '</table>' +
              '<div>' +
              '<div>' +
              '</div>' +
              '</div>' +
              '</div>' +
              '<div class=row>' +
              '<md-dialog-actions class="col-lg-12 text-right">' +
              "<button type=\"button\" class=\"btn btn-default\"  ng-click=\"closeDialog()\">Cerrar</button>" +
              '</md-dialog-actions>' +
              '</div>' +
              '</md-dialog-content>' +
              '</md-dialog>',

      templateSelectedImage: "<md-dialog aria-label=\"\">" +
              //Toolbar  
              "<md-toolbar>" +
              "<div class=\"md-toolbar-tools\">" +
              "<h4 class=\"modal-title\" id=\"exampleModalLabel\">Seleccionar Imagen</h4>" +
              "<span flex></span>" +
              "<md-button class=\"md-icon-button\" ng-click=\"cancel()\">" +
              "<md-icon  aria-label=\"Close dialog\"></md-icon>" +
              "</md-button>" +
              "</div>" +
              "</md-toolbar>" +
              //Dialog Body
              "<md-dialog-content >" +
              //Dialog Content
              '<md-content style="min-width:1000px" >' +
              "<div id=\"pagination\" class=\"text-center\">" +
              "<ul class=\"pagination\">" +
              "<li ng-class=\"page == 1 ? 'disabled'  : ''\">" +
              "<a   ng-click=\"page == 1 ? true  : false || fastbackward()\" class=\"new-element\"><i class=\"disabled glyphicon glyphicon-fast-backward\"></i></a>" +
              "</li>" +
              "<li  ng-class=\"page == 1 ? 'disabled'  : ''\">" +
              "<a ng-click=\"page == 1 ? true  : false || backward()\" class=\"new-element\"><i class=\"glyphicon glyphicon-step-backward\"></i></a>" +
              "</li>" +
              "<li>" +
              "<span>" +
              "<b> {{imageAccount.total }}" +
              "</b> registros </span><span>P??gina <b>{{ page }}" +
              "</b> de <b>" +
              "{{(imageAccount.total_pages )}}" +
              " </b>\n\
                                            </span>" +
              "</li>" +
              "<li   ng-class=\"page == (imageAccount.total_pages) || imageAccount.total_pages == 0 ? 'disabled'  : ''\">" +
              '<a ng-click="page == (imageAccount.total_pages)  || imageAccount.total_pages == 0  ? true  : false || page == (imageAccount.total_pages)  || imageAccount.total_pages == 0  ? true  : false || forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>' +
              "</li>" +
              "<li ng-class=\"page == (imageAccount.total_pages)  || imageAccount.total_pages == 0 ? 'disabled'  : ''\">" +
              '<a ng-click="page == (imageAccount.total_pages)  || imageAccount.total_pages == 0  ? true  : false || fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>' +
              "</li>\n\
                                        </ul>\n\
                                      </div>" +
              '<div class="main text-center">' +
              '<div class="container-fluid">' +
              '<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 equal " ng-repeat="key in imageAccount.items" >' +
              '<div class="center-block">' +
              "<img class=\"img-static\"  ng-click=\"funcUniversal.selectedImage($index)\"  ng-style=\"key.class\" id=\"{{key.idDashboardImage}}\" alt=\"{{key.name}}\" ng-src=\"{{ UrlImageBase + key.name}}\" alt=\"{{key.name}}\"/>" +
              '<p>{{key.name}}</p>' +
              '</div>' +
              '</div>' +
              '</div>' +
              '</div>' +
              '<div class="container-fluid">' +
              '<div id="pagination" class="text-center">' +
              '<ul class="pagination">' +
              "<li ng-class=\"page == 1 ? 'disabled'  : ''\">" +
              '<a   ng-click="page == 1 ? true  : false || fastbackward()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>' +
              '</li>' +
              "<li  ng-class=\"page == 1 ? 'disabled'  : ''\">" +
              '<a ng-click="page == 1 ? true  : false || backward()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>' +
              '</li>' +
              '<li>' +
              '<span><b>{{imageAccount.total }}' +
              '</b> registros </span><span>P??gina <b>{{ page }}' +
              '</b> de <b>' +
              '{{ (imageAccount.total_pages ) }}' +
              '</b></span>' +
              '</li>' +
              "<li   ng-class=\"page == (imageAccount.total_pages) || imageAccount.total_pages == 0 ? 'disabled'  : ''\">" +
              '<a ng-click="page == (imageAccount.total_pages)  || imageAccount.total_pages == 0  ? true  : false || page == (imageAccount.total_pages)  || imageAccount.total_pages == 0  ? true  : false || forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>' +
              '</li>' +
              "<li   ng-class=\"page == (imageAccount.total_pages)  || imageAccount.total_pages == 0 ? 'disabled'  : ''\">" +
              '<a ng-click="page == (imageAccount.total_pages)  || imageAccount.total_pages == 0  ? true  : false || fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>' +
              '</li>' +
              '</ul>' +
              '</div>' +
              '</div>' +
              //Fin Dialog Content      
              '</md-content>' +
              //Button actions
              '<md-dialog-actions class="col-lg-12 text-right padding-10px">' +
              "<button type=\"button\" class=\"btn btn-default\"  ng-click=\"closeDialog()\">Cerrar</button>" +
              '</md-dialog-actions>' +
              //end Dialog Body
              '</md-dialog-content>' +
              '</md-dialog>',
      templatePreview: "<md-dialog aria-label=\"\">" +
              //Toolbar  
              "<md-toolbar>" +
              "<div class=\"md-toolbar-tools\">" +
              "<h4 class=\"modal-title\" id=\"exampleModalLabel\">Previsualizacion</h4>" +
              "<span flex></span>" +
              "<md-button class=\"md-icon-button\" ng-click=\"cancel()\">" +
              "<md-icon  aria-label=\"Close dialog\"></md-icon>" +
              "</md-button>" +
              "</div>" +
              "</md-toolbar>" +
              //Dialog Body
              "<md-dialog-content >" +
              //Dialog Content
              '<md-content style="min-width:1000px" >' +
              '<div class="container-fluid padding-top-15px">' +
              '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 ">' +
              '<div class="col-xs-12 col-sm-12 col-md-9 col-lg-9 ">' +
              '<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 equal" ng-repeat="item in dashboarPreview.items track by $index">' +
              '<div class="center-block">' +
              '<div class="dashboard-item-image-center center-block " >' +
              "<img class=\"img-static \" ng-src=\"{{item.imageDashboard}}\" alt=\"{{item.title}}\" >" +
              '</div>' +
              '<div class="dashboard-item-title-center ">' +
              '<a href="" class="extra-small-text">{{item.textEnlace}}</a>' +
              '</div>' +
              '</div>' +
              '</div>' +
              '</div>' +
              '<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 ">' +
              '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 center-block equal">' +
              "<img class=\"img-static\" ng-src=\"{{dashboarPreview.topImage}}\" >" +
              '</div>' +
              '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 center-block equal">' +
              "<img class=\"img-static\" ng-src=\"{{dashboarPreview.bottomImage}}\" >" +
              '</div>' +
              '</div>' +
              '</div>' +
              '</div>' +
              //end Dialog Content      
              '</md-content>' +
              //Button actions
              '<md-dialog-actions class="col-lg-12 text-right padding-10px">' +
              "<button type=\"button\" class=\"btn btn-default\"  ng-click=\"closeDialog()\">Cerrar</button>" +
              '</md-dialog-actions>' +
              //end Dialog Body
              '</md-dialog-content>' +
              '</md-dialog>',
    },
    configDashboardDefault: {
      items: [{
          "title": "Email",
          "drag": true,
          "icon": "email.png",
          "textEnlace": "Email",
          "imageDashboard": fullUrlBase + "/images/general/email.png",
          "jqyoui_pos": 0,
          "idService": 2
        }, {
          "title": "SMS",
          "drag": true,
          "icon": "sms.png",
          "textEnlace": "SMS",
          "imageDashboard": fullUrlBase + "/images/general/sms.png",
          "jqyoui_pos": 1,
          "idService": 1
        }, {
          "title": "Encuesta",
          "drag": true,
          "icon": "survey.png",
          "textEnlace": "Encuesta",
          "imageDashboard": fullUrlBase + "/images/general/encuestas.png",
          "jqyoui_pos": 2,
          "idService": 5
        }, {
          "title": "Landing page",
          "drag": true,
          "icon": "landing.png",
          "textEnlace": "Landing",
          "imageDashboard": fullUrlBase + "/images/general/landing.png",
          "jqyoui_pos": 3
        }, {
          "title": "Base de datos",
          "drag": true,
          "icon": "database.png",
          "textEnlace": "Base de datos",
          "imageDashboard": fullUrlBase + "/images/general/basededatos.png",
          "jqyoui_pos": 4,
          "ref":"contact", 
        }, {
          "title": "Automatizacion",
          "drag": true,
          "icon": "automatization.png",
          "textEnlace": "Automatizacion",
          "imageDashboard": fullUrlBase + "/images/general/automatizacion.png",
          "jqyoui_pos": 5,
          "idService": 4
        }, {
          "title": "Informes",
          "drag": true,
          "icon": "reports.png",
          "textEnlace": "Revisar informes",
          "imageDashboard": fullUrlBase + "/images/general/informes.png",
          "jqyoui_pos": 6,
          "ref":"report", 
        }, {
          "title": "Redes sociales",
          "drag": true,
          "icon": "social.png",
          "textEnlace": "Redes sociales",
          "imageDashboard": fullUrlBase + "/images/general/social-network.png",
          "jqyoui_pos": 7
        }],
      topImage: urlBaseDefault + "images/general/AIO-3.png",
      bottomImage: urlBaseDefault + "images/general/sigma-logo.png"
    },
    urlServices: {
      email: fullUrlBase + "mail",
      sms: fullUrlBase + "sms",
      smstwoway: fullUrlBase + "smstwoway#/",
      automatic_campaign: fullUrlBase + "automaticcampaign#/",
      survey: fullUrlBase + "survey",
      contact: fullUrlBase + "contactlist/show#/",
      report: fullUrlBase + "reports/index",
    }
  });
  if (typeof client == "undefined" || client == false) {
    app.config(['$stateProvider', '$urlRouterProvider', function ($stateProvider, $urlRouterProvider) {
        $stateProvider
                .state('index', {
                  url: "/:id",
                  templateUrl: fullUrlBase + templateBase + '/configdashboard',
                  controller: 'index'
                })
      }]);
  }
  angular.bootstrap(document, ['dashboardconfigApp']);
});


