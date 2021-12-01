(function () {
  angular
          .module("landingpage", [
            "ui.router",
            "landingpage.controllers",
            "landingpage.services",
            "ngMaterial",
            "ui.select",
            "ngSanitize",
            "ui.bootstrap",
            "angularFileUpload",
            "angularMoment",
            "moment-picker"
          ])
          .config([
            "$stateProvider",
            "$urlRouterProvider",
            "$interpolateProvider",
            "contantLandingPage",
            function (
                    $stateProvider,
                    $urlRouterProvider,
                    $interpolateProvider,
                    contantLandingPage
                    ) {
              $stateProvider
                      .state("index", {
                        url: "/",
                        templateUrl:
                                fullUrlBase + contantLandingPage.Misc.TemplateBase + "/list",
                        controller: "indexController"
                      })
                      .state("create", {
                        url: "/create",
                        templateUrl:
                                fullUrlBase + contantLandingPage.Misc.TemplateBase + "/create",
                        controller: "createController"
                      })
                      .state("create.describe", {
                        url: "/basicinformation/:idLandingPage",
                        templateUrl:
                                fullUrlBase +
                                contantLandingPage.Misc.TemplateBase +
                                "/basicinformation",
                        controller: "createBasicInformationController"
                      })
                      .state("create.content", {
                        url: "/content/:idLandingPage",
                        templateUrl:
                                fullUrlBase + contantLandingPage.Misc.TemplateBase + "/content",
                        controller: "contentController"
                      })
                      .state("create.confirmation", {
                        url: "/confirmation/:idLandingPage",
                        templateUrl:
                                fullUrlBase +
                                contantLandingPage.Misc.TemplateBase +
                                "/confirmation",
                        controller: "confirmationController"
                      })
                      .state("create.share", {
                        url: "/share/:idLandingPage",
                        templateUrl:
                                fullUrlBase + contantLandingPage.Misc.TemplateBase + "/share",
                        controller: "shareController"
                      });
              $urlRouterProvider.otherwise("/");
            }
          ])
          .constant("contantLandingPage", {
            permissionFBAdmin: "ADMINISTER",
            permissionFBBasicAdmin: "CREATE_CONTENT",
            permissionFBCreateContent: "BASIC_ADMIN",
            messagepublic: "La Landing Page y la publicación fueron guardados exitosamente.",
            errorpubliclinklanding: "Se ha producido un error con el link para compartir, contacte con administrador.",
            NotificationsService: {
              Errors: {
                error: "glyphicon glyphicon-remove-circle",
                success: "glyphicon glyphicon-ok-circle",
                warning: "glyphicon glyphicon-exclamation-sign",
                notice: "glyphicon glyphicon-exclamation-sign",
                primary: "glyphicon glyphicon-exclamation-sign"
              }
            },
            Notifications: {
              Errors: {
                errorServices: "Se ha producido un error",
                errorview: "La fecha de expiración no debe ser menor a 60 minutos a la fecha de inicio",
                ApiFacebook: "Ha ocurrido un problema. Por favor intente de nuevo",
                LengthFanPage: "No tiene fan page asociadas. Por favor intente de nuevo."
              }
            },
            Notificationsshare: {
              message: {
                error: "Por favor diligenciar el formulario.",
                errordate1: "la fecha de programacion no puede ser menor a la fecha inicial de la landing.",
                errordate2: "la fecha de programacion no puede ser mayor a la fecha final de la landing.",
                errordate3: "la fecha de programacion no puede ser menor a la fecha actual.",
              }
            },
            UrlPeticion: {
              Urls: {
                listlanding: "api/landingpage/listlanding/",
                getlandingcategory: "/api/landingpage/getlandingcategory",
                createlandingcategory: "api/landingpage/createlandingcategory",
                createlandingpage: "api/landingpage/createlandingpage",
                findlanding: "api/landingpage/findlanding/",
                editlandingpage: "api/landingpage/editlandingpage/",
                createpublicview: "api/landingpage/createpublicview/",
                findlandingcountview: "api/landingpage/findlandingcountview/",
                findlandingcsc: "api/landingpage/findlandingcsc/",
                countries: "country/country",
                states: "country/state/",
                cities: "country/cities/",
                deletelandingpage: "api/landingpage/deletelandingpage/",
                linkgenerator: "api/landingpage/linkge/",
                linkfb: "api/landingpage/linkfb/",
                sendmaillandingpage: "api/landingpage/sendmaillandingpage",
                addEmailName: "mail/addemailname/",
                addEmailSender: "mail/addemailsender/",
                hascontent: "api/landingpage/hascontent/",
                emailsender: "mail/emailsender/",
                emailname: "mail/emailname/",
                getallmailtemplatelandingpage: "api/mailtemplate/getallmailtemplatelandingpage",
                getallmailtemplatelandingpagebyfilter: "api/mailtemplate/getallmailtemplatelandingpagebyfilter",
                getallmailcategory: "api/mailcategory/getallmailcategory",
                getcontactlist: "api/sendmail/getcontactlist",
                getsegment: "api/sendmail/getsegment",
                duplicate: "api/landingpage/duplicate/",
                savePost: "api/post/save",
                changestatus: "api/landingpage/changestatus/",
                changetype: "api/landingpage/changetype/",
              }
            },
            Misc: {
              undefined: "undefined",
              facebook: "facebook",
              published: "published",
              public: "public",
              DELETE: "DELETE",
              bar: "/",
              now: "now",
              POST: "POST",
              value: 4000,
              number: "number",
              connected: "connected",
              accounts: "/me/accounts",
              scope: "publish_actions,publish_pages,manage_pages,email",
              picture: "/picture",
              valueview: {
                valuevi: 60,
                format: "YYYY/MM/DD",
                HH: "HH",
                mm: "mm",
                valueAler: 3000
              },
              Icons: {
                infoSign: "glyphicon glyphicon-info-sign"
              },
              Alerts: {
                danger: "danger",
                success: "success",
                warning: "warning",
                notice: "notice",
                primary: "primary"
              },
              TemplateBase: "landingpage",
              img: {
                imgEmail: "images/general/emailShare.png",
                imgFb: "images/general/fbShare.png",
                imgLink: "images/general/linkShare.png"
              },
              datetimepickerformat: {
                format: "yyyy-MM-dd hh:mm",
                language: "es",
                time: "-0500",
                value: 1000
              },
              varReturn: {
                email: "email",
                link: "link"
              },
              listDestinatary: {
                namecontact: "Lista de contacto",
                namesegment: "Segmentos"
              },
              linkGenerator: {
                btnCopy: "btnCopy",
                link: "link",
                click: "click",
                Copy: "copy",
                success: "El link ha sido copiado exitosamente",
                error: "No se pudo copiar el link",
                querySelector: ".linkgen",
                modal1: "hide",
                modal2: "show",

              }

            },
            templateModalEmail:
                    '<md-dialog aria-label="">' +
                    //Toolbar
                    "<md-toolbar>" +
                    '<div class="md-toolbar-tools">' +
                    '<h4 class="modal-title" id="exampleModalLabel">Configuración envio email</h4>' +
                    "<span flex></span>" +
                    '<md-button class="md-icon-button" ng-click="cancel()">' +
                    '<md-icon  aria-label="Close dialog"></md-icon>' +
                    "</md-button>" +
                    "</div>" +
                    "</md-toolbar>" +
                    //Dialog Content
                    "<md-dialog-content >" +
                    '<md-content style="min-width:1000px">' +
                    "<br>" +
                    '<div class="container-fluid">' +
                    '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 control-label">' +
                    "<p><label>Fecha de programación de la Landing Page</label></p>" +
                    "<span><label>Fecha Inicial: </label>{{landing.startDate}}</span>" +
                    '<span class="padding-left-20px"><label>Fecha Final: </label>{{landing.endDate}}</span>' +
                    "</div>" +
                    '<div class="form-horizontal">' +
                    '<div class="form-group" >' +
                    '<label for="" class="col-md-2 control-label">Fecha de publicación</label>' +
                    '<div class="col-md-10">' +
                    '<div id="datetimepicker1" class="none-padding input-append date">' +
                    '<span class="input-append date add-on input-group none-padding">' +
                    '<input id="scheduleDate" data-format="yyyy-MM-dd hh:mm " type="text" class="undeline-input">' +
                    '<span class="add-on input-group-addon">' +
                    '<i data-time-icon="glyphicon glyphicon-time" data-date-icon="glyphicon glyphicon-calendar"></i>' +
                    "</span>" +
                    "</span>" +
                    "</div>" +
                    "</div>" +
                    "</div>" +
                    '<div class="form-group">' +
                    '<label for="" class="col-md-2 control-label">Lista de destinatarios</label>' +
                    '<div class="col-md-10">' +
                    '<ui-select name="senderEmail" ng-model="data.listDestinatary" ng-change="resServices.changeDestinatary(data.listDestinatary)"theme="select2" sortable="false" close-on-select="true" style="width:100%">' +
                    '<ui-select-match placeholder="Seleccione uno">{{$select.selected.name}}</ui-select-match>' +
                    '<ui-select-choices repeat="key as key in data.listDestinatarya | propsFilter: {name: $select.search}">' +
                    '<div ng-bind-html="key.name | highlight: $select.search"></div>' +
                    "</ui-select-choices>" +
                    "</ui-select> " +
                    "</div>" +
                    "</div>" +
                    '<div class="form-group">' +
                    '<label for="" class="col-md-2 control-label">*Destinatarios</label>' +
                    '<div class="col-md-10">' +
                    '<ui-select multiple ng-model="data.destinatary" ng-required="true"  ui-select-required  class="min-width-100" theme="select2" title=""  sortable="false" close-on-select="true">' +
                    "<ui-select-match >{{$item.name}}</ui-select-match>" +
                    '<ui-select-choices repeat="key in data.destinatarya | propsFilter: {name: $select.search}">' +
                    '<div ng-bind-html="key.name | highlight: $select.search"></div>' +
                    "</ui-select-choices>" +
                    "</ui-select> " +
                    "</div>" +
                    "</div>" +
                    '<div class="form-group" >' +
                    '<label for="" class="col-md-2 control-label">*Plantilla de correo</label>' +
                    '<div class="col-md-10">' +
                    '<ui-select name="senderName" ng-model="data.mailtemplate" theme="select2" sortable="false" close-on-select="true" style="width:100%" reset-search-input="false">' +
                    '<ui-select-match placeholder="Seleccione uno">{{$select.selected.name}}</ui-select-match>' +
                    '<ui-select-choices repeat="key in data.listSMailTemplate track by $index | propsFilter: {name: $select.search}" refresh="resServices.changeSelectedMailTemplate($select.search)" refresh-delay="0" >' +
                    '<div ng-bind-html="key.name | highlight: $select.search"></div>' +
                    '</ui-select-choices>' +
                    '</ui-select>' +
                    '</div>' +
                    '</div>' +
                    '<div class="form-group" >' +
                    ' <label for="" class="col-md-2 control-label">*Categoria de correo</label>' +
                    '<div class="col-md-10">' +
                    '<ui-select name="senderName" ng-model="data.mailcategory"  theme="select2" sortable="false" close-on-select="true" style="width:100%">' +
                    '<ui-select-match  placeholder="Seleccione uno">{{$select.selected.name}}</ui-select-match>' +
                    '<ui-select-choices repeat="key in data.listSMailCategory | propsFilter: {name: $select.search}">' +
                    '<div ng-bind-html="key.name | highlight: $select.search"></div>' +
                    '</ui-select-choices>' +
                    '</ui-select>' +
                    '</div>' +
                    '</div>' +
                    '<div class="form-group">' +
                    '<label for="" class="col-md-2 control-label">*Nombre del remitente</label>' +
                    '<div class="col-md-9">' +
                    '<div data-ng-show="misc.showInputNamea">' +
                    '<input placeholder="*Nombre del remitente" data-ng-model="misc.senderName" maxlength="200" class="undeline-input">' +
                    "</div>" +
                    '<div data-ng-show="misc.showSelectNamea">' +
                    '<ui-select name="senderName" ng-model="data.senderName" theme="select2" sortable="false"  close-on-select="true" style="width:100%">' +
                    '<ui-select-match placeholder="Seleccione uno">{{$select.selected.name}}</ui-select-match>' +
                    '<ui-select-choices repeat="key as key in data.emailname | propsFilter: {name: $select.search}">' +
                    '<div ng-bind-html="key.name | highlight: $select.search"></div>' +
                    "</ui-select-choices>" +
                    "</ui-select>" +
                    "</div>" +
                    "</div>" +
                    '<div class="col-xs-2 col-sm-2 col-md-1 col-lg-1 ">' +
                    '<div data-ng-show="misc.showIconsNamea">' +
                    '<a class="color-primary" data-ng-click="functions.changeStatusInputName()" href=""><span class="glyphicon glyphicon-plus " title="Agregar otro nombre"></span></a>' +
                    "</div>" +
                    '<div data-ng-show="misc.showIconsSaveNamea" class="margin-top">' +
                    '<a class="negative" data-ng-click="functions.changeStatusInputName()" href=""><span class="glyphicon glyphicon-remove" title="Cancelar"></span></a>' +
                    '<a class="positive" data-ng-click="resServices.saveName()" href=""><span class="glyphicon glyphicon-ok margin-left-10" title="Guardar"></span></a>' +
                    "</div>" +
                    "</div>" +
                    "</div>" +
                    '<div class="form-group">' +
                    '<label for="" class="col-md-2 control-label">*Correo del remitente</label>' +
                    '<div class="col-md-9">' +
                    '<div data-ng-show="misc.showInputName">' +
                    '<input placeholder="*Correo del remitente" data-ng-model="misc.senderEmail" maxlength="200" class="undeline-input">' +
                    "</div>" +
                    '<div data-ng-show="misc.showSelectName">' +
                    '<ui-select name="senderEmail" ng-model="data.senderEmail" theme="select2" sortable="false" close-on-select="true" style="width:100%">' +
                    '<ui-select-match placeholder="Seleccione uno">{{$select.selected.email}}</ui-select-match>' +
                    '<ui-select-choices repeat="key as key in data.emailsend | propsFilter: {email: $select.search}">' +
                    '<div ng-bind-html="key.email | highlight: $select.search"></div>' +
                    "</ui-select-choices>" +
                    "</ui-select> " +
                    "</div>" +
                    "</div>" +
                    '<div class="col-xs-2 col-sm-2 col-md-1 col-lg-1 ">' +
                    '<div data-ng-show="misc.showIconsName">' +
                    '<a class="color-primary" data-ng-click="functions.changeStatusInputNameemail()" href=""><span class="glyphicon glyphicon-plus " title="Agregar otro nombre"></span></a>' +
                    "</div>" +
                    '<div data-ng-show="misc.showIconsSaveName" class="margin-top">' +
                    '<a class="negative" data-ng-click="functions.changeStatusInputNameemail()" href=""><span class="glyphicon glyphicon-remove" title="Cancelar"></span></a>' +
                    '<a class="positive" data-ng-click="resServices.saveEmail()" href=""><span class="glyphicon glyphicon-ok margin-left-10" title="Guardar"></span></a>' +
                    "</div>" +
                    "</div>" +
                    "</div>" +
                    '<div class="form-group" >' +
                    '<label for="" class="col-md-2 control-label">*Asunto</label>' +
                    '<div class="col-md-10">' +
                    '<input name="subject" type="text" placeholder="Asunto"  class="form-control" ng-model="data.subject"/>' +
                    "</div>" +
                    "</div>" +
                    '<div class="form-group" >' +
                    '<label for="" class="col-md-2 control-label">Responder a</label>' +
                    '<div class="col-md-10">' +
                    '<input name="replyto" type="text" placeholder="Responder a"  class="form-control" ng-model="data.replyto"/>' +
                    "</div>" +
                    "</div>" +
                    "</div>" +
                    "</div>" +
                    "<br>" +
                    '<div class="container-fluid">' +
                    '<md-dialog-actions class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap text-right">' +
                    '<button type="button" class="btn btn-default margin-5px"  ng-click="functions.closeDialog()">Cerrar</button>' +
                    '<button type="button" class="btn btn-info margin-5px"  ng-click="resServices.validate(true)">Enviar ahora</button>' +
                    "</md-dialog-actions>" +
                    "</div>" +
                    "</md-dialog-content>" +
                    "</md-dialog>",
            templateEnlace:
                    '<md-dialog aria-label="">' +
                    //Toolbar
                    "<md-toolbar>" +
                    '<div class="md-toolbar-tools">' +
                    '<h4 class="modal-title" id="exampleModalLabel">Enlace de la Landing Page</h4>' +
                    "<span flex></span>" +
                    '<md-button class="md-icon-button" ng-click="cancel()">' +
                    '<md-icon  aria-label="Close dialog"></md-icon>' +
                    "</md-button>" +
                    "</div>" +
                    "</md-toolbar>" +
                    //Dialog Content
                    "<md-dialog-content >" +
                    '<md-content style="min-width:1000px;overflow-x:hidden;">' +
                    "<br>" +
                    '<div class="container-fluid">' +
                    '<div class="col-sm-12">' +
                    '<p class="small-text">' +
                    " Este es el link que podrá compartir" +
                    '<div class="form-group">' +
                    ' <div class="row">' +
                    '<div class="col-sm-10">' +
                    '<input type="text" id="link" class="form-control" readonly="true" data-ng-model="data.linksurv" />' +
                    "</div>" +
                    '<div class="col-sm-2">' +
                    '<button type="button" class="btn btn-info" id="btnCopy">' +
                    '<i class="fa fa-copy"></i> Copiar' +
                    "</button>" +
                    "</div>" +
                    "</div>" +
                    "</div>" +
                    "</p>" +
                    " </div>" +
                    "</div>" +
                    '<div class="container-fluid">' +
                    '<md-dialog-actions class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap text-right">' +
                    '<button type="button" class="btn btn-default"  ng-click="functions.closeDialog()">Cerrar</button>' +
                    "</md-dialog-actions>" +
                    "</div>" +
                    "</md-dialog-content>" +
                    "</md-dialog>",
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
                    '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" ng-if="misc.pages.length <= 0">' +
                    '<h1>No tiene actualmente Fan Page para seleccionar.</h1>' +
                    '</div>' +
                    '<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4 cursor-pointer" ng-class="{\'selected\':misc.pageSelected.id == page.id}"  ng-repeat="page in misc.pages" ng-click="functions.selectedPage(page)"> ' +
                    '<div class="card facebook-list">' +
                    '<div class="text-center">' +
                    '<a ><img src="{{page.picture}}" class="img-circle"/></a>' +
                    '<h3 class="text-center">{{page.name}}</h3>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '<div ng-if="misc.pageSelected" class="animate-if ">' +
                    '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">' +
                    '<p><label>Fecha de programación de la Landing Page</label></p>' +
                    '<span><label>Fecha Inicial: </label>{{data.landing.startDate}}</span>' +
                    '<span class="padding-left-20px"><label>Fecha Final: </label>{{data.landing.endDate}}</span>' +
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
                    "<button type=\"button\" class=\"btn btn-default margin-5px\"  ng-click=\"functions.closeDialog()\">Cerrar</button>" +
                    "<button type=\"button\" class=\"btn btn-primary margin-5px\" ng-disabled=\"misc.isDisabled\" ng-model=\"isDisabled\" ng-click=\"functions.validate()\">Programar</button>" +
                    "<button type=\"button\" class=\"btn btn-info margin-5px\" ng-disabled=\"misc.isDisabled\" ng-model=\"isDisabled\"   ng-click=\"functions.validate(true)\">Pulicar ahora</button>" +
                    '</md-dialog-actions>' +
                    '</div>' +
                    '</md-dialog-content>' +
                    '</md-dialog>'
          });
})();
