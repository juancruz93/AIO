
  angular.module('survey', ['ui.router', 'survey.controllers', 'survey.services', 'ngMaterial', 'ui.select', 'ngSanitize', 'builder', 'builder.components', 'validator.rules', 'angularSpectrumColorpicker', 'angular-bind-html-compile', 'ui.bootstrap', 'angularFileUpload', 'angularMoment'])
          .config(['$stateProvider', '$urlRouterProvider', '$interpolateProvider', function ($stateProvider, $urlRouterProvider, $interpolateProvider) {
              $stateProvider
                      .state('describe', {
                        url: "/basicinformation/:idSurvey",
                        templateUrl: fullUrlBase + templateBase + '/basicinformation',
                        controller: 'createBasicInformationController'
                      })
                      .state('survey', {
                        url: "/survey/:idSurvey",
                        templateUrl: fullUrlBase + templateBase + '/survey',
                        controller: 'surveyController'
                      })
                      .state('confirmation', {
                        url: "/confirmation/:idSurvey",
                        templateUrl: fullUrlBase + templateBase + '/confirmation',
                        controller: 'confirmationController'
                      })
                      .state('share', {
                        url: "/share/:idSurvey",
                        templateUrl: fullUrlBase + templateBase + '/share',
                        controller: 'share'
                      });
              $urlRouterProvider.otherwise('/');
//              $interpolateProvider.startSymbol('%%'); // __ instead of {{
//              $interpolateProvider.endSymbol('%%'); // __ instead of }}
            }])
          .constant('surveyConstant', {
            Notifications:{
              Error:{
                  ApiFacebook:"Ha ocurrido un problema. Por favor intente de nuevo",
                  LengthFanPage:"No tiene fan page asociadas. Por favor intente de nuevo."
              }  
            },
            permissionFBAdmin:"ADMINISTER",
            permissionFBBasicAdmin:"CREATE_CONTENT",
            permissionFBCreateContent:"BASIC_ADMIN",
            urlUploadFile: fullUrlBase + 'api/' + templateBase + "/uploadimage",
            imageUpload: "<md-dialog aria-label=\"\">" +
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
                    '<div class="container-fluid">' +
                    '<div>' +
                    '<input type="file" nv-file-select="" uploader="uploader" multiple  /><br/>' +
                    '</div>' +
                    '<div  style="margin-bottom: 40px">' +
                    '<p>Cantidad de archivos seleccionados: {{ uploader.queue.length }}</p>' +
                    '<table class="table table-bordered"  >' +
                    '<thead>' +
                    '<tr>' +
                    '<th width="50%">Nombre</th>' +
                    '<th ng-show="uploader.isHTML5">Tamaño</th>' +
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
                    '</div>' +
                    '<div class="container-fluid margin-botton">' +
                    '<md-dialog-actions class="text-right">' +
                    "<button type=\"button\" class=\"btn btn-default\"  ng-click=\"closeDialog()\">Cerrar</button>" +
                    '</md-dialog-actions>' +
                    '</div>' +
                    '</md-dialog-content>' +
                    '</md-dialog>',
            templateSelectedImage:
                    '<div class="modal-header modal-header-primary">' +
                    '<h3 class="modal-title" id="modal-title">Seleccione una imagen</h3>' +
                    '</div>' +
                    '<div class="modal-body" id="modal-body">' +
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
                    "</b> registros </span><span>Página <b>{{ page }}" +
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
                    "<img class=\"img-static\"  ng-click=\"funcUniversal.selectedImage($index)\"  ng-style=\"key.class\" id=\"{{key.id}}\" alt=\"{{key.title}}\" ng-src=\"{{key.thumb}}\" alt=\"{{key.title}}\"/>" +
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
                    '</b> registros </span><span>Página <b>{{ page }}' +
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
                    '</div>' +
                    '<div class="modal-footer">' +
                    '<button class="btn btn-info" type="button" ng-click="funcUniversal.openUploadImage()">Subir imagen</button>' +
                    '<button class="btn btn-warning" type="button" ng-click="$ctrl.cancel()">Cancel</button>' +
                    '</div>',
            templateEnlace: "<md-dialog aria-label=\"\">" +
                    //Toolbar  
                    "<md-toolbar>" +
                    "<div class=\"md-toolbar-tools\">" +
                    "<h4 class=\"modal-title\" id=\"exampleModalLabel\">Enlace de encuesta</h4>" +
                    "<span flex></span>" +
                    "<md-button class=\"md-icon-button\" ng-click=\"cancel()\">" +
                    "<md-icon  aria-label=\"Close dialog\"></md-icon>" +
                    "</md-button>" +
                    "</div>" +
                    "</md-toolbar>" +
                    //Dialog Content
                    "<md-dialog-content >" +
                    '<md-content style="min-width:1000px;overflow-x:hidden;">' +
                    '<br>' +
                    '<div class="container-fluid">' +
                    '<div class="col-sm-12">' +
                    '<p class="small-text">' +
                    ' Este es el link que podrá compartir dependiendo del tipo de encuesta que haya elegido' +
                    '<div class="form-group">' +
                    ' <div class="row">' +
                    '<div class="col-sm-10">' +
                    '<input type="text" id="link" class="form-control" readonly="true" data-ng-model="linksurv" />' +
                    '</div>' +
                    '<div class="col-sm-2">' +
                    '<button type="button" class="btn btn-info" id="btnCopy">' +
                    '<i class="fa fa-copy"></i> Copiar' +
                    '</button>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</p>' +
                    ' </div>' +
                    '</div>' +
                    '<div class="container-fluid">' +
                    '<md-dialog-actions class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap text-right">' +
                    "<button type=\"button\" class=\"btn btn-default\"  ng-click=\"closeDialog()\">Cerrar</button>" +
                    '</md-dialog-actions>' +
                    '</div>' +
                    '</md-dialog-content>' +
                    '</md-dialog>',
            templateModalPageFacebook: '<md-dialog aria-label="">' +
                    //Toolbar  
                    "<md-toolbar>" +
                    "<div class=\"md-toolbar-tools\">" +
                    "<h4 class=\"modal-title\" id=\"exampleModalLabel\">Seleccionar fan page</h4>" +
                    "<span flex></span>" +
                    "<md-button class=\"md-icon-button\" ng-click=\"cancel()\">" +
                    "<md-icon  aria-label=\"Close dialog\"></md-icon>" +
                    "</md-button>" +
                    "</div>" +
                    "</md-toolbar>" +
                    //Dialog Content
                    "<md-dialog-content >" +
                    '<md-content style="min-width:1000px;overflow-x:hidden;">' +
                    '<br>' +
                    '<div class="container-fluid">' +
                    '<div class="row row-flex row-flex-wrap">' +
                    '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" ng-if="pages.length <= 0">' +
                    '<h1>No tiene actualmente Fan Page para seleccionar.</h1>' +
                    '</div>' +
                    '<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4 cursor-pointer" ng-class="{\'selected\':pageSelected.id == page.id}"  ng-repeat="page in pages" ng-click="selectedPage(page)"> ' +
                    '<div class="card facebook-list">' +
                    '<div class="text-center">' +
                    '<a ><img src="{{page.picture}}" class="img-circle"/></a>' +
                    '<h3 class="text-center">{{page.name}}</h3>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '<div ng-if="pageSelected" class="animate-if ">' +
                    '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">' +
                    '<p><label>Fecha de programación de la encuesta</label></p>' +
                    '<span><label>Fecha Inicial: </label>{{survey.startDate}}</span>' +
                    '<span class="padding-left-20px"><label>Fecha Final: </label>{{survey.endDate}}</span>' +
                    '</div>' +
                    '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">' +
                    '<label class="control-label col-xs-12 col-sm-12 col-md-2 col-lg-2 none-padding">Fecha de publicación</label>' +
                    '<div id="datetimepicker1" class="col-xs-12 col-sm-12 col-md-10 col-lg-10  none-padding input-append date">' +
                    '<span class="input-append date add-on input-group none-padding">' +
                    '<input id="scheduleDate" data-format="yyyy-MM-dd hh:mm " type="text" class="undeline-input">' +
                    '<span class="add-on input-group-addon">' +
                    '<i data-time-icon="glyphicon glyphicon-time" data-date-icon="glyphicon glyphicon-calendar"></i>' +
                    '</span>' +
                    '</span>' +
                    '</div>' +
                    '</div>' +
                    '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">' +
                    '<div class="form-group">' +
                    '<label> Descripcion (opcional)</label>' +
                    '<textarea row="3" class="form-control" ng-model="data.description" />' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '<br>' +
                    '<div class="container-fluid">' +
                    '<md-dialog-actions class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap text-right">' +
                    "<button type=\"button\" class=\"btn btn-default margin-5px\"  ng-click=\"closeDialog()\">Cerrar</button>" +
                    "<button type=\"button\" class=\"btn btn-primary margin-5px\" ng-disabled=\"isDisabled\" ng-model=\"isDisabled\" ng-click=\"validate()\">Programar</button>" +
                    "<button type=\"button\" class=\"btn btn-info margin-5px\" ng-disabled=\"isDisabled\" ng-model=\"isDisabled\"   ng-click=\"validate(true)\">Pulicar ahora</button>" +
                    '</md-dialog-actions>' +
                    '</div>' +
                    '</md-dialog-content>' +
                    '</md-dialog>',
            templateModalEmail: '<md-dialog aria-label="">' +
                    //Toolbar  
                    "<md-toolbar>" +
                    "<div class=\"md-toolbar-tools\">" +
                    "<h4 class=\"modal-title\" id=\"exampleModalLabel\">Configuración envio email</h4>" +
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
                    '<div class="container-fluid">' +
                    '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 control-label">' +
                    '<p><label>Fecha de programación de la encuesta</label></p>' +
                    '<span><label>Fecha Inicial: </label>{{survey.startDate}}</span>' +
                    '<span class="padding-left-20px"><label>Fecha Final: </label>{{survey.endDate}}</span>' +
                    '</div>' +
                    '<div class="form-horizontal">' +
                    '<div class="form-group" >' +
                    '<label for="" class="col-md-2 control-label">Fecha de publicación</label>' +
                    '<div class="col-md-10">' +
                    '<div id="datetimepicker1" class="none-padding input-append date">' +
                    '<span class="input-append date add-on input-group none-padding">' +
                    '<input id="scheduleDate" data-format="yyyy-MM-dd hh:mm " type="text" class="undeline-input">' +
                    '<span class="add-on input-group-addon">' +
                    '<i data-time-icon="glyphicon glyphicon-time" data-date-icon="glyphicon glyphicon-calendar"></i>' +
                    '</span>' +
                    '</span>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '<div class="form-group">' +
                    '<label for="" class="col-md-2 control-label">Lista de destinatarios</label>' +
                    '<div class="col-md-10">' +
                    '<ui-select name="senderEmail" ng-model="selected.listDestinatary" ng-change="func.changeDestinatary(selected.listDestinatary)"theme="select2" sortable="false" close-on-select="true" style="width:100%">' +
                    '<ui-select-match placeholder="Seleccione uno">{{$select.selected.name}}</ui-select-match>' +
                    '<ui-select-choices repeat="key as key in lists.listDestinatary | propsFilter: {name: $select.search}">' +
                    '<div ng-bind-html="key.name | highlight: $select.search"></div>' +
                    '</ui-select-choices>' +
                    '</ui-select> ' +
                    '</div>' +
                    '</div>' +
                    '<div class="form-group">' +
                    '<label for="" class="col-md-2 control-label">*Destinatarios</label>' +
                    '<div class="col-md-10">' +
                    '<ui-select multiple ng-model="selected.destinatary" ng-required="true"  ui-select-required  class="min-width-100" theme="select2" title=""  sortable="false" close-on-select="true">' +
                    '<ui-select-match >{{$item.name}}</ui-select-match>' +
                    '<ui-select-choices repeat="key in lists.destinatary | propsFilter: {name: $select.search}">' +
                    '<div ng-bind-html="key.name | highlight: $select.search"></div>' +
                    '</ui-select-choices>' +
                    '</ui-select> ' +
                    '</div>' +
                    '</div>' +
                    '<div class="form-group" >' +
                    '<label for="" class="col-md-2 control-label">*Plantilla de correo</label>' +
                    '<div class="col-md-10">' +
                    '<ui-select name="senderName" ng-model="selected.mailtemplate" theme="select2" sortable="false" close-on-select="true" style="width:100%" reset-search-input="false">' +
                    '<ui-select-match placeholder="Seleccione uno">{{$select.selected.name}}</ui-select-match>' +
                    '<ui-select-choices repeat="key in lists.listSMailTemplate track by $index | propsFilter: {name: $select.search}" refresh="func.changeSelectedMailTemplate($select.search)" refresh-delay="0" >' +
                    '<div ng-bind-html="key.name | highlight: $select.search"></div>' +
                    '</ui-select-choices>' +
                    '</ui-select>' +
                    '</div>' +
                    '</div>' +
                    '<div class="form-group" >' +
                    ' <label for="" class="col-md-2 control-label">*Categoria de correo</label>' +
                    '<div class="col-md-10">' +
                    '<ui-select name="senderName" ng-model="selected.mailcategory"  theme="select2" sortable="false" close-on-select="true" style="width:100%">' +
                    '<ui-select-match  placeholder="Seleccione uno">{{$select.selected.name}}</ui-select-match>' +
                    '<ui-select-choices repeat="key in lists.listSMailCategory | propsFilter: {name: $select.search}">' +
                    '<div ng-bind-html="key.name | highlight: $select.search"></div>' +
                    '</ui-select-choices>' +
                    '</ui-select>' +
                    '</div>' +
                    '</div>' +
                    '<div class="form-group">' +
                    '<label for="" class="col-md-2 control-label">*Nombre del remitente</label>' +
                    '<div class="col-md-9">' +
                    '<div data-ng-show="nameSender.showInputName">' +
                    '<input placeholder="*Nombre del remitente" data-ng-model="nameSender.senderName" maxlength="200" class="undeline-input">' +
                    '</div>' +
                    '<div data-ng-show="nameSender.showSelectName">' +
                    '<ui-select name="senderName" ng-model="selected.senderName" theme="select2" sortable="false"  close-on-select="true" style="width:100%">' +
                    '<ui-select-match placeholder="Seleccione uno">{{$select.selected.name}}</ui-select-match>' +
                    '<ui-select-choices repeat="key as key in lists.emailname | propsFilter: {name: $select.search}">' +
                    '<div ng-bind-html="key.name | highlight: $select.search"></div>' +
                    '</ui-select-choices>' +
                    '</ui-select>' +
                    '</div>' +
                    '</div>' +
                    '<div class="col-xs-2 col-sm-2 col-md-1 col-lg-1 ">' +
                    '<div data-ng-show="nameSender.showIconsName">' +
                    '<a class="color-primary" data-ng-click="nameSender.changeStatusInputName()" href=""><span class="glyphicon glyphicon-plus " title="Agregar otro nombre"></span></a>' +
                    '</div>' +
                    '<div data-ng-show="nameSender.showIconsSaveName" class="margin-top">' +
                    '<a class="negative" data-ng-click="nameSender.changeStatusInputName()" href=""><span class="glyphicon glyphicon-remove" title="Cancelar"></span></a>' +
                    '<a class="positive" data-ng-click="nameSender.saveName()" href=""><span class="glyphicon glyphicon-ok margin-left-10" title="Guardar"></span></a>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '<div class="form-group">' +
                    '<label for="" class="col-md-2 control-label">*Correo del remitente</label>' +
                    '<div class="col-md-9">' +
                    '<div data-ng-show="emailSender.showInputName">' +
                    '<input placeholder="*Correo del remitente" data-ng-model="emailSender.senderEmail" maxlength="200" class="undeline-input">' +
                    '</div>' +
                    '<div data-ng-show="emailSender.showSelectName">' +
                    '<ui-select name="senderEmail" ng-model="selected.senderEmail" theme="select2" sortable="false" close-on-select="true" style="width:100%">' +
                    '<ui-select-match placeholder="Seleccione uno">{{$select.selected.email}}</ui-select-match>' +
                    '<ui-select-choices repeat="key as key in lists.emailsend | propsFilter: {email: $select.search}">' +
                    '<div ng-bind-html="key.email | highlight: $select.search"></div>' +
                    '</ui-select-choices>' +
                    '</ui-select> ' +
                    '</div>' +
                    '</div>' +
                    '<div class="col-xs-2 col-sm-2 col-md-1 col-lg-1 ">' +
                    '<div data-ng-show="emailSender.showIconsName">' +
                    '<a class="color-primary" data-ng-click="emailSender.changeStatusInputName()" href=""><span class="glyphicon glyphicon-plus " title="Agregar otro nombre"></span></a>' +
                    '</div>' +
                    '<div data-ng-show="emailSender.showIconsSaveName" class="margin-top">' +
                    '<a class="negative" data-ng-click="emailSender.changeStatusInputName()" href=""><span class="glyphicon glyphicon-remove" title="Cancelar"></span></a>' +
                    '<a class="positive" data-ng-click="emailSender.saveEmail()" href=""><span class="glyphicon glyphicon-ok margin-left-10" title="Guardar"></span></a>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '<div class="form-group" >' +
                    '<label for="" class="col-md-2 control-label">*Asunto</label>' +
                    '<div class="col-md-10">' +
                    '<input name="subject" type="text" placeholder="Asunto"  class="form-control" ng-model="selected.subject"/>' +
                    '</div>' +
                    '</div>' +
                    '<div class="form-group" >' +
                    '<label for="" class="col-md-2 control-label">Responder a</label>' +
                    '<div class="col-md-10">' +
                    '<input name="replyto" type="text" placeholder="Responder a"  class="form-control" ng-model="selected.replyto"/>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '<br>' +
                    '<div class="container-fluid">' +
                    '<md-dialog-actions class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap text-right">' +
                    "<button type=\"button\" class=\"btn btn-default margin-5px\"  ng-click=\"closeDialog()\">Cerrar</button>" +
                    "<button type=\"button\" class=\"btn btn-primary margin-5px\"  ng-click=\"func.validate()\">Programar</button>" +
                    "<button type=\"button\" class=\"btn btn-info margin-5px\"  ng-click=\"func.validate(true)\">Enviar ahora</button>" +
                    '</md-dialog-actions>' +
                    '</div>' +
                    '</md-dialog-content>' +
                    '</md-dialog>',
          }
          );

