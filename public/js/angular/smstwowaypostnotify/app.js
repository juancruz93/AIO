angular.module("smstwowaypostnotify",["ui.router", "smstwowaypostnotify.services", "smstwowaypostnotify.controllers", "ui.select", "ngMaterial", "ngSanitize"])
        .config(['$stateProvider','$urlRouterProvider', function ($stateProvider, $urlRouterProvider) {
          $stateProvider
            .state("create", {
              url: "/",
              templateUrl: fullUrlBase + templateBase + "/create",
              controller: "createController"
            }); 
            $urlRouterProvider.otherwise("/");
        }]).constant('constantPageSmsxemail', {
          UrlPeticion: {
            create: fullUrlBase + 'api/' + templateBase + '/create',
            getSavedCredentials: fullUrlBase + 'api/' + templateBase + '/getsavedcredentials',
            copykey: fullUrlBase + 'api/' + templateBase + '/copykey/',
          },
          Filter:{
            minChar:3
          },
          Messages:{
            confirmation:"Se ha creado correctamente!",
            confirmationEdit:"Se ha editado correctamente!",
            error:"No se ha creado correctamente. Por favor valide la informacion.",
          },
        });
        ;
