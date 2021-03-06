'use strict';
(function () {
  angular.module('forms', ['ui.router', 'forms.controllers', 'forms.services', 'ngMaterial', 'ui.select', 'ngSanitize', 'builder', 'builder.components', 'validator.rules', 'ui.bootstrap', 'colorpicker.module', 'angularSpectrumColorpicker', 'xeditable'])
          .run(function (editableOptions, editableThemes) {
            editableThemes.bs3.inputClass = 'input-sm';
            editableThemes.bs3.buttonsClass = 'btn-sm';
            editableOptions.theme = 'bs3';
          })
          .config([
            '$validatorProvider', function ($validatorProvider) {
              $validatorProvider.register('required', {
                invoke: 'watch',
                validator: /.+/,
                error: 'El campo es requerido.'
              });
              $validatorProvider.register('number', {
                invoke: 'watch',
                validator: /^[-+]?[0-9]*[\.]?[0-9]*$/,
                error: 'El campo solo pude ingresar numeros.'
              });
              $validatorProvider.register('email', {
                invoke: 'blur',
                validator: /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/,
                error: 'El formato del correo no es valido.'
              });
              $validatorProvider.register('numberEnteros', {
                invoke: 'blur',
                validator: /^[0-9]+$/,
                error: 'Solo se es permitido numeros enteros'
              });
              $validatorProvider.register('fecha', {
                invoke: 'blur',
                validator: /.+/,
                error: 'El formato de la fecha no es valido.'
              });
              return $validatorProvider.register('url', {
                invoke: 'blur',
                validator: /((([A-Za-z]{3,9}:(?:\/\/)?)(?:[-;:&=\+\$,\w]+@)?[A-Za-z0-9.-]+|(?:www.|[-;:&=\+\$,\w]+@)[A-Za-z0-9.-]+)((?:\/[\+~%\/.\w-_]*)?\??(?:[-\+=&;%@.\w_]*)#?(?:[\w]*))?)/,
                error: 'El formato de la url no es valido.'
              });
            }])
          .config(['$stateProvider', '$urlRouterProvider', function ($stateProvider, $urlRouterProvider) {
              $stateProvider
                  .state('list', {
                    url: "/",
                    templateUrl: fullUrlBase + templateBase + '/list',
                    controller: 'indexController'
                  })
                  .state('create', {
                     url: "/create",
                    templateUrl: fullUrlBase + templateBase + '/create',
                    controller: 'main',
                  })
                  .state('create.describe', {
                    url: "/basicinformation/:id",
                    templateUrl: fullUrlBase + templateBase + '/basicinformation',
                    controller: 'createBasicInformationController'
                  })
                  .state('create.forms', {
                    url: "/forms/:id",
                    templateUrl: fullUrlBase + templateBase + '/forms',
                    controller: 'formsController'
                  })
                  .state('reportforms', {
                    url: "/report/:id",
                    templateUrl: fullUrlBase + templateBase + '/report',
                    controller: 'reportController'
                  })
                  .state('create.edit', {
                    url:"/basicinformation/:id",
                    templeateUrl: fullUrlBase + templateBase + '/basicinformation',
                    controller: 'indexController'
                  })
                  .state('contacts', {
                    url: "/contacts/:id",
                    templateUrl: fullUrlBase + templateBase + '/contacts',
                    controller: 'ContactsController'
                  })
            }])
            .constant('constantForms', {
            Notifications: {
              Errors: {
                errorNoneContactsInscriptForms: "No se tienen contactos registrados con este formulario"
              }
            },
            UrlPeticion: {
              Urls: {
                listlanding: 'api/landingpage/listlanding/',
                getAllReportEmail: fullUrlBase+'api/report/getallreportemail/',
                getContactlist: fullUrlBase+'api/sendmail/getcontactlist',
                getMailTemplate: fullUrlBase+'api/mailtemplate/getallmailtemplate',
                previewMailTemplateContent: fullUrlBase+'api/mailtemplate/preview/',
                saveBasicInformation: fullUrlBase+'api/forms/savebasicinformation',
                updatebasicinformation: fullUrlBase+'api/forms/updatebasicinformation/',
                getCustomfield: fullUrlBase+'api/sendmail/getcustomfield/',
                listForms: fullUrlBase+'api/forms/listforms/',
                getAllFormCategory: fullUrlBase+'api/forms/getallformscategories',
                getInformationForm: fullUrlBase+'api/forms/getinformationform/',
                saveForm: fullUrlBase+'api/forms/saveforms/',
                getAllIndicative: fullUrlBase+'api/contact/getallindicative',
                getAll: fullUrlBase+'api/contact/getcontactsform/',
                customfieldselect: fullUrlBase+'api/contact/customfieldselect/',
                editContact: fullUrlBase+'api/contact/editcontact/',
                changestatus: fullUrlBase+'api/contact/changestatus/',
                deleteContact: fullUrlBase+'api/contact/deleconta/',
                getOptin: fullUrlBase+'api/forms/getoptin/',
                getWelcomeMail: fullUrlBase+'api/forms/getwelcomemail/',
                getNotification: fullUrlBase+'api/forms/getnotificationform/',
                getcontentform: fullUrlBase+'api/forms/getcontentform/',
                addFormCategory: fullUrlBase+'api/forms/addformcategory',
                getallmailtemplatebyfilter: fullUrlBase+'api/mailtemplate/getallmailtemplatebyfilter',
                suscripsforms: fullUrlBase+'api/forms/getsuscriptsform/',
                dowloadReportContactsForm: fullUrlBase+'api/forms/dowloadreportcontactsform/',
                deleteForm: fullUrlBase + 'api/forms/deleteform',
              }
            }
          });
})();
