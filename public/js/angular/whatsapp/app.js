angular.module('whatsapp', ['ui.router', 'whatsapp.controllers', 'whatsapp.services', 'ui.select', "ngSanitize", 'moment-picker'])
        .config(['$stateProvider', '$urlRouterProvider', function ($stateProvider, $urlRouterProvider) {
            $stateProvider
                    .state('toolstwoway', {
                      url: "/tools",
                      templateUrl: fullUrlBase + templateBase + '/toolstwoway',
                      controller: 'tools'
                    })
                    .state('indexwhatsapp', {
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
                      url: "/create",
                      templateUrl: fullUrlBase + templateBase + '/create',
                      controller: 'create'
                    })
                    .state('editspeedsent', {
                      url: "/editspeedsent/:idSmsTwoway",
                      templateUrl: fullUrlBase + templateBase + '/speedsent',
                      controller: 'speedsent'
                    });

            $urlRouterProvider.otherwise('/');
          }])
        .constant('contantWhatsapp', {
          urlPeticion: {
            getCategory: fullUrlBase + '/api/whatsapp/getallcategory',
            getTimezone: fullUrlBase + 'mail/timezone/',
            listWpp: fullUrlBase + 'api/whatsapp/getallwhatsapp/',
            getcontactlist: fullUrlBase + 'api/whatsapp/getcontactlist',
            getHsmTemplates: fullUrlBase + 'api/whatsapp/listwpptemplate',
            countContacts: fullUrlBase + 'api/whatsapp/countContacts',
            listSmsTemplate: fullUrlBase + 'api/smstemplate/listfull',
            saveSmstowwayContact: fullUrlBase + 'api/smstwoway/savesmstowwaycontact',
            changeDataEditAll: fullUrlBase + 'api/smstwoway/getalledit/',
          },
          error: {
            messages: {
              msgNameSent: "Debes ingresar un nombre de envio de SMS doble via",
              msgCategory: "Por favor ingrese una categoria",
              msgTimezone: "Por favor ingrese una zona horaria",
              msgDateTime: "Debes ingresar una fecha y hora de envio",
              msgDestinataries: "Debes ingresar al menos un destinatario",
              msgIndicative: "Indicativo no valido",
              msgValidatePuntoYComa: "Recuerde que los datos de cada destinatario deben estar separados por punto y coma ';' y los destinatarios por un salto de l??nea (enter)",
              msgValidatePhone: "Numero de destinatario invalido.",
              msgValidateInvalidCharacters: "El mensaje no permite estos caracteres: ?? ?? ?? ?? ?? ?? ?? ?? ?? ?? ?? ?? ??",
              msgMaxCharacters: "El mensaje para cada destinatario es de maximo 160 caracteres.",
              msgMaxDestinataries: "Solo se puede ingresar 500 destinatarios",
              msgEmailEmpty: "Por favor ingrese el email",
              msgEmailEmpty2: "No se encontro ningun correo electronico",
              msgMaxEmail: "Solo se puede ingresar un m??ximo de 8 correos electr??nicos",
              msgIntervalos: "Por favor ingrese la cantidad de envio por intervalos",
              msgSentTime: "Por favor ingrese tiempo de envio",
              msgTimeFormat: "Por favor ingrese el formato de envio",
              msgTypeResponseNotClicked: "Debe agregar tipos de respuesta y homologaciones de las mismas para sus envios de SMS doble via.",
              msgTypeResponseEmpty: "Los campos de respuesta y homologacion no pueden estar vacios.",
              msgTypeResponseMinLength: "El minimo de respuestas con sus respectivas homologaciones es 2",
              msgTypeResponseHomologateMinLength: "El maximo de homologaciones es 10",
              msgBlankSpaces: "La respuesta y/o homologaci??n no puede contener tildes, ni ?? ni espacios en blanco.",
              msgLoadCsv: "Debe adjuntar un archivo con extension .csv",
              msgEnvioExitoso: "El env??o se realiz?? correctamente!",
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
            dayName: "D??a(s)",
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
            editsmscontac:"Editar SMS doble v??a por contacto o segmentos.",
            createdsmscontac:"Crear SMS doble v??a por contacto o segmentos.",
          },
          patterns:{
            accentsMsgDestinataries: /[??????????????????????????]/,
            blankSpacesResponseAndHomologate:/\s/,
            accentsResponseAndHomologate:/[??????????????????????????????]/,
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
            sentSpeed: "Env??o rapido"
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
            enProcesoDeEnvio: "En proceso de env??o",
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
            whatsappRoute: "whatsapp/",
            goState: 'indexwhatsapp'
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
  angular.bootstrap(document, ['whatsapp']);
})
