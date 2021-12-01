angular.module('smstwoway', ['ui.router', 'smstwoway.controllers', 'smstwoway.services', 'ui.select', "ngSanitize", 'moment-picker'])
        .config(['$stateProvider', '$urlRouterProvider', function ($stateProvider, $urlRouterProvider) {
            $stateProvider
                    .state('toolstwoway', {
                      url: "/tools",
                      templateUrl: fullUrlBase + templateBase + '/toolstwoway',
                      controller: 'tools'
                    })
                    .state('indextwoway', {
                      url: "/",
                      templateUrl: fullUrlBase + templateBase + '/list',
                      controller: 'main'
                    })
                    .state('showlotetwoway', {
                      url: "/showlotetwoway",
                      templateUrl: fullUrlBase + templateBase + '/showlotetwoway',
                      controller: 'showlotetwoway'
                    })
                    .state('create', {
                      url: "/speedsent",
                      templateUrl: fullUrlBase + templateBase + '/speedsent',
                      controller: 'speedsent'
                    })
                    .state('csvsent', {
                      url: "/createcsv",
                      templateUrl: fullUrlBase + templateBase + '/createcsv',
                      controller: 'csv'
                    })
                    .state('createdcontact', {
                      url: "/createdcontact",
                      templateUrl: fullUrlBase + templateBase + '/createdcontact',
                      controller: 'createdcontact'
                    })
                    .state('editCsv', {
                      url: "/editcsv/:idSmsTwoway",
                      templateUrl: fullUrlBase + templateBase + '/createcsv',
                      controller: 'csv'
                    })
                    .state('editsmstwowaycontact', {
                      url: "/editsmstwowaycontact/:idSmsTwoway",
                      templateUrl: fullUrlBase + templateBase + '/createdcontact',
                      controller: 'createdcontact'
                    })
                    .state('editspeedsent', {
                      url: "/editspeedsent/:idSmsTwoway",
                      templateUrl: fullUrlBase + templateBase + '/speedsent',
                      controller: 'speedsent'
                    });

            $urlRouterProvider.otherwise('/');
          }])
        .constant('contantSmstwoway', {
          urlPeticion: {
            getCategory: fullUrlBase + '/api/smscategory/getall',
            getTimezone: fullUrlBase + 'mail/timezone/',
            indexLoteTwoway: fullUrlBase + 'api/smstwoway/getallsmstwoway/',
            createLoteTwoway: fullUrlBase + 'api/smstwoway/createsmslotetwoway',
            editLoteTwoway: fullUrlBase + 'api/smstwoway/editsmslotetwowaysend',
            getsegments: fullUrlBase + 'api/sendmail/getsegment',
            getcontactlist: fullUrlBase + 'api/sendmail/getcontactlist',
            createCsvTwoway: fullUrlBase + 'api/smstwoway/createcsv',
            changeStatusTwoway: fullUrlBase + 'api/smstwoway/changestatus/',
            countContact: fullUrlBase + 'api/sms/countcontact',
            listSmsTemplate: fullUrlBase + 'api/smstemplate/listfull',
            saveSmstowwayContact: fullUrlBase + 'api/smstwoway/savesmstowwaycontact',
            indexSmsTwoway: fullUrlBase + 'smstwoway',
            changeDataEditAll: fullUrlBase + 'api/smstwoway/getalledit/',
            getInfoTwoway: fullUrlBase + 'api/smstwoway/getone',
            getInforEdit: fullUrlBase + 'api/smstwoway/getalleditcontact',
            calcelEdit: fullUrlBase + 'api/smstwoway/changestatusedit',
            editcsv: fullUrlBase + 'api/smstwoway/editcsv',
            getavalaiblecountry: fullUrlBase + 'api/smstwoway/getavalaiblecountry'

          },
          error: {
            messages: {
              msgNameSent: "Debes ingresar un nombre de envio de SMS doble via",
              msgCategory: "Por favor ingrese una categoria",
              msgTimezone: "Por favor ingrese una zona horaria",
              msgDateTime: "Debes ingresar una fecha y hora de envio",
              msgDestinataries: "Debes ingresar al menos un destinatario",
              msgIndicative: "Indicativo no valido",
              msgValidatePuntoYComa: "Recuerde que los datos de cada destinatario deben estar separados por punto y coma ';' y los destinatarios por un salto de línea (enter)",
              msgValidatePhone: "Numero de destinatario invalido.",
              msgValidateInvalidCharacters: "El mensaje no permite estos caracteres: ñ Ñ á é í ó ú Á É Í Ó Ú ´",
              msgMaxCharacters: "El mensaje para cada destinatario es de maximo 160 caracteres.",
              msgMaxDestinataries: "Solo se puede ingresar 500 destinatarios",
              msgEmailEmpty: "Por favor ingrese el email",
              msgEmailEmpty2: "No se encontro ningun correo electronico",
              msgMaxEmail: "Solo se puede ingresar un máximo de 8 correos electrónicos",
              msgIntervalos: "Por favor ingrese la cantidad de envio por intervalos",
              msgSentTime: "Por favor ingrese tiempo de envio",
              msgTimeFormat: "Por favor ingrese el formato de envio",
              msgTypeResponseNotClicked: "Debe agregar tipos de respuesta y homologaciones de las mismas para sus envios de SMS doble via.",
              msgTypeResponseEmpty: "Los campos de respuesta y homologacion no pueden estar vacios.",
              msgTypeResponseMinLength: "El minimo de respuestas con sus respectivas homologaciones es 2",
              msgTypeResponseHomologateMinLength: "El maximo de homologaciones es 10",
              msgBlankSpaces: "La respuesta y/o homologación no puede contener tildes, ni ñ ni espacios en blanco.",
              msgLoadCsv: "Debe adjuntar un archivo con extension .csv",
              msgEnvioExitoso: "El envío se realizó correctamente!",
              msgEnvioNoExitoso: "No se pudo realizar el envio, intente de nuevo",
              msgCsv: "Por favor seleccione un archivo CSV,intente de nuevo",
              msgInvalidMail1: "El correo ",
              msgInvalidMail2: " no es valido.",
            }
          },
          values: {
            messages: {
              msgMaxDestinatariesValue:500,
              msgMaxPhoneDigits: 10,
              msgMaxCharacters: 160,
              msgMinTypeResponseValue: 2,
              msgMaxHomologateContentValue: 10,
              msgMaxEmailValue: 8,
              initValueZero: 0,
              initValueOne: 1,
            }
          },
          misc: {
            idSubaccount: idSubaccount,
            arrBoolean: ["true", "false", "TRUE", "FALSE", 0, 1, "0", "1"],
            limitTypeResponse: 5,
            minTypeResponse: 2,
            typeResponseInit: [{response:"si",homologate:"confirmado,acepto,ok"},{response:"no",homologate:"cancelado,negativo"}],
            timeZn: "-0500",
            minValue: 'minute',
            minName: "Minuto(s)", 
            hourValue: 'hour',
            hourName: "Hora(s)",
            dayValue: 'day',
            dayName: "Día(s)",
            weekValue: 'week',
            weekName: "Semana(s)",
            monthValue:'month',
            monthName: "Mes(es)",
          },
          classToogle: {
            danger: "danger",
            error: "error",
            warning: "warning",
            success: "success",
            notice: "notice",
            info: "info",
          },
          milliSeconds: {
            threeThousand: 3000,
            fourThousand: 4000,

          },
          slideOnTop: {
            classSlide: "glyphicon glyphicon-info-sign",
            classSlideRemoveCircle: "glyphicon glyphicon-remove-circle",
            classSlideOkCircle: "glyphicon glyphicon-ok-circle",
            classSlideExclamationSign: "glyphicon glyphicon-exclamation-sign",
          },
          title:{
            editsmscontac:"Editar SMS doble vía por contacto o segmentos.",
            createdsmscontac:"Crear SMS doble vía por contacto o segmentos.",
          },
          patterns:{
            accentsMsgDestinataries: /[ñÑáéíóúÁÉÍÓÚ´]/,
            blankSpacesResponseAndHomologate:/\s/,
            accentsResponseAndHomologate:/[ñÑáéíóúÁÉÍÓÚ¿¡´]/,
            verifyCorrectEmail: /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
          },
          cases: {
            contact: 'contact',
            csv: 'csv',
            speedSent: 'lote',
            
            draft: 'draft',
            scheduled: 'scheduled',
            pending: 'pending',
            sending: 'sending',
            sent: 'sent',
            paused: 'paused',
            canceled: 'canceled',
            undelivered: 'undelivered'
          },
          statusType:{
            contact: "Contacto",
            csv: "Csv",
            sentSpeed: "Envío rapido"
          },
          restricts: {
            letterRestrict: 'A'
          },
          atSym: {
            symbolAt: '@'
          },
          classSt:{
            colorDraft: 'color-draft',
            colorScheduled: 'color-scheduled',
            colorPending: 'color-pending',
            colorSending: 'color-sending',
            colorSent:'color-sent',
            colorPaused:'color-paused',
            colorCanceled1:'color-canceled', 
            colorCanceled2:'color-canceled', 
          },
          smsTranslate:{
            borrador: "Borrador",
            programado: "Programado",
            pendiente: "Pendiente",
            enProcesoDeEnvio: "En proceso de envío",
            enviado: "Enviado",
            pausado: "Pausado",
            cancelado: "Cancelado",
            noEnviado: "No enviado",
          },
          sckts:{
            stopSmsTwoway: 'stop-sms-twoway',
            pausedSmsTwoway: 'paused-sms-twoway',
            resumeSmsTwoway: 'resume-sms-twoway',
            refreshViewSmsTwoWay:'refresh-view-sms-two-way',
            processSmsTwoWay: 'process-sms-twoway'
          },
          funcNode:{
            canc:"canceled",
            paus: "paused",
            sche: "scheduled"
          },
          casesNode:{
            cancela: 'cancel',
            pau: 'pause',
            contin: 'continue',
          },
          caseResponseNode:{
            caseOne: '1',
            caseTwo: '2',
            caseThree: '3'
          },
          toggleSmsTwoway:{
            toggOn: 'Si', 
            toggOff: 'No',
            toggOnStyle: 'success', 
            toggOffStyle: 'danger',
            toggSize: 'small'
          },
          dTPicker:{
            frmt: 'yyyy-MM-dd hh:mm:ss',
            lng: 'es',
          },
          routing:{
            smstwowayRoute: "smstwoway/",
            goState: 'indextwoway'
          },
          statusLoadingCsv:{
            preload: "preload",
            validations: "validations",
            load: "load",
            finish: "finish"
          },
          clType:{
            typeContactList: 'contactlist',
            typeSegment: 'segment'
          },
          equalFR:{
            equalFileRead: "="
          },
          csvProcess:{
            csvPorc: 20
          },
          valueSenitizeCsv:{
            valuSC: "1"
          }
        });

angular.element(document).ready(function () {
  angular.bootstrap(document, ['smstwoway']);
})
