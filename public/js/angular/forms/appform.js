'use strict';
(function () {
  var app = angular.module('appFormSigma', ['ngSanitize', 'builder', 'builder.components', 'validator.rules', 'ui.bootstrap', 'angularSpectrumColorpicker']);
  app.constant("contantForm", {
    message: {
      Age: "Tu edad no es apta para acceder a este servicio.",
      Term: "Por favor aceptar los terminos y condiciones para poder continuar con el registro.",
      Acept: "El registro se guardo de forma exitosa.",
      Valid: "Se estan validando el registro, esto podria demorar un segundo.",
      InvalidDate: "No puedes digitar fechas futuras al diligenciar el formulario.",
      InvalidNumber: "Recuerda digitar solamente 10 digitos para el Telefono"
    },
    title:{
      Term: "Terminos y condiciones",
      Acept: "Correcto",
      Valid: "Se esta realizando validaciones",
    }
  });
  app.config(['$validatorProvider', function ($validatorProvider) {
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
        validator: /^[0-9]{4}-(0[0-9]|1[0-2])-(0[0-9]|[0-2][0-9]|3[0-1])$/,
//        validator: /.+/,
        error: 'El formato de la fecha no es valido.'
      });
      return $validatorProvider.register('url', {
        invoke: 'blur',
        validator: /((([A-Za-z]{3,9}:(?:\/\/)?)(?:[-;:&=\+\$,\w]+@)?[A-Za-z0-9.-]+|(?:www.|[-;:&=\+\$,\w]+@)[A-Za-z0-9.-]+)((?:\/[\+~%\/.\w-_]*)?\??(?:[-\+=&;%@.\w_]*)#?(?:[\w]*))?)/,
        error: 'El formato de la url no es valido.'
      });
    }]);
  app.service('serviceFormSigma', ['$http', '$q', function ($http, $q) {
      this.getForm = function (idForm) {
        var defer = $q.defer();
        var url = fullUrlBaseSigmaDomain + 'api/forms/getcontentform/' + idForm;
        $http.get(url)
                .success(function (data) {
                  defer.resolve(data);
                }) 
                .error(function (data) {
                  defer.reject(data);
                });
        return defer.promise;
      }
      this.sendForm = function (idForm, data) {
        var defer = $q.defer();
        var url = fullUrlBaseSigmaDomain + "api/contact/addcontactform/" + idForm;
        $http.post(url, data)
                .success(function (data) {
                  defer.resolve(data);
                })
                .error(function (data) {
                  defer.resolve(data);
                });
        return defer.promise;
      }
      this.converArrayToObj = function (data) {
        var defer = $q.defer();
        var objReturn = {};
        for (var i in data) {
          var value = data[i].value;
          var name = data[i].id;
          var component = data[i].component;
          if (component == "encabezado" || component == "button") {
            continue;
          }
          if (component == "dateInput") {
            var date = new Date(value);
            var month, day;
            if (date.getMonth() + 1 < 10) {
              month = date.getMonth() + 1;
              month = "0" + month.toString();
            } else {
              month = date.getMonth() + 1;
            }
            if (date.getDate() < 10) {
              day = "0" + date.getDate().toString();
            } else {
              day = date.getDate();
            }
            value = date.getFullYear() + "-" + month + "-" + day;
          }
          if (component == "checkbox") {
            value = value.split(",");
//            var array = value.split(",");
//            value = [];
//            for(var i in array){
//              value.push(array[i].trim());
//            }
          }
          if (name == "indicative") {
            var startOfSection = value.indexOf("+");
            var startOfValue = value.indexOf('+', startOfSection) + 1;
            var endOffValue = value.indexOf(')', startOfValue); //one char longer, as needed for substring

            value = value.substring(startOfValue, endOffValue);
          }
          objReturn[name] = value;
        }
        defer.resolve(objReturn);
        return defer.promise;
      }

    }]);
  app.controller('controllerFormSigma', ['$scope', 'serviceFormSigma', '$builder', '$validator', '$q', '$window', 'contantForm', function ($scope, serviceFormSigma, $builder, $validator, $q, $window, contantForm) {
      $scope.complet = false;
      $scope.input = [];
      $scope.deleted_cl = false;
      $scope.deleted_form = false;
      $scope.getForm = function (idForm) {
        serviceFormSigma.getForm(idForm).then(function (data) {
          $scope.deleted_cl = data.deleted_cl;
          $scope.deleted_form = data.deleted_form;

          var validateLink = HB.indexOf("https");
          if(validateLink >= 0){
            var newElementConfirmation2 = {};
              newElementConfirmation2.component = "link";
              newElementConfirmation2.label = "label";
              newElementConfirmation2.description = "";
              newElementConfirmation2.editable = true;
              newElementConfirmation2.id = "confirmation";
              newElementConfirmation2.index = 9998;
              newElementConfirmation2.options = ['Si, Acepto.'];
              newElementConfirmation2.url = HB;
              newElementConfirmation2.placeholder = "Terminos y condiciones";
          }else{
            var newElementConfirmation = {};
              newElementConfirmation.component = "confirmation";
              newElementConfirmation.label = HB;
              newElementConfirmation.description = "";
              newElementConfirmation.editable = true;
              newElementConfirmation.id = "confirmation";
              newElementConfirmation.index = 9999;
              newElementConfirmation.options = ['Si, Acepto.'];
          }

          $scope.dataContent = JSON.parse(data.content);
          if(validateLink >= 0){
            newElementConfirmation2.objExt = $scope.dataContent.form[1].objExt;
          }else{
            newElementConfirmation.objExt = $scope.dataContent.form[1].objExt;
          }
          
          var itemsNumber = $scope.dataContent.form.length;
          for (var i = 0; i < itemsNumber ; i++) {
            var idJson = $scope.dataContent.form[i].id;
            //var itemRequired = $scope.dataContent.form[i].required;
            //if ( idJson == "name")      { $scope.dataContent.form[i].validation = ""; }
            //if ( idJson == "lastname")  { $scope.dataContent.form[i].validation = ""; } 
            if ( idJson == "birthdate") { $scope.dataContent.form[i].validation = ""; }
            //if ( idJson == "indicative"){ $scope.dataContent.form[i].validation = ""; }
            //if ( idJson == "phone")     { $scope.dataContent.form[i].validation = ""; }
          }
          if(validateLink >= 0){
            $scope.dataContent.form.splice($scope.dataContent.form.length - 1, 0, newElementConfirmation2);
          }else{
            $scope.dataContent.form.splice($scope.dataContent.form.length - 1, 0, newElementConfirmation);
          }
          $builder.setForm('sigmaForm', $scope.dataContent.form);
          $scope.complet = true;
          if($scope.deleted_form == true){            
             swal("Ha ocurrido un error", "FORMULARIO ELIMINADO", "error");
             $("body").prepend("<div class=\"overlay\"></div>");

            $(".overlay").css({
                "position": "absolute", 
                "width": $(document).width(), 
                "height": $(document).height(),
                "z-index": 99999, 
            }).fadeTo(0, 0.8);
          }else if($scope.deleted_cl == true){
                         swal("Ha ocurrido un error", "FORMULARIO ELIMINADO", "error");
             $("body").prepend("<div class=\"overlay\"></div>");

            $(".overlay").css({
                "position": "absolute", 
                "width": $(document).width(), 
                "height": $(document).height(),
                "z-index": 99999, 
            }).fadeTo(0, 0.8);
          }
          
        });
      }      
      $scope.sendData = function (idForm) {
        $("#submitButton").prop('disabled', false);
        $scope.coverDateToString().then(function (data) {
          $validator.validate($scope, 'sigmaForm')
                  .success(function () {
                    serviceFormSigma.converArrayToObj($scope.input).then(function (data) {
                    
                    /* validando que el campo birthdate existan en el JSON si no es asi
                     * entonces no valide esta informacion y salte al checkbox */
                    if(angular.isDefined(data.birthdate)){
                        /*validando que al digitar cualquier cosa valide fechas... 
                         *en caso de que no haya nada no valide nada*/
                        if(data.birthdate != "NaN-NaN-NaN"){
                            //validando la edad del usuario y que ingrese fechas permitidas...
                            if ($scope.validateDate(data)){
                              if($scope.validateUnderAge(data)){
                                  swal("", contantForm.message.Age, "");
                                  $("#submitButton").prop('disabled', false);
                                  return false;
                              }
                              $("#submitButton").prop('disabled', false);
                            }else{                    
                              swal("", contantForm.message.InvalidDate, "");
                              $("#submitButton").prop('disabled', false);
                              return false;
                            }
                        }
                    }
                    
                    /* validando que el campo phone existan en el JSON si no es asi
                     * entonces no valide esta informacion y salte al checkbox*/ 
                    if(angular.isDefined(data.phone)){
                        /* validando que al digitar cualquier cosa valide fechas... 
                         * en caso de que no haya nada no valide nada */
                        if(data.phone != "NaN-NaN-NaN"){
                            //validando la edad del usuario y que ingrese fechas permitidas... */
                            if ($scope.validateTelephoneNumber(data)){
                              swal("", contantForm.message.InvalidNumber, "");
                              $("#submitButton").prop('disabled', false);
                              return false;
                            }
                        }
                    }

                    //validando la confirmacion antes de aceptar el form
                    if (data.confirmation == "" ) {
                      swal(contantForm.title.Term, contantForm.message.Term, "error");
                      $("#submitButton").prop('disabled', false);
                      return false;
                    }else 
                    // se utilizo una variante del componente sweetalert para 
                    // que al aceptar el usuario viera un componente de carga.
                    swal.queue([{
                        title: contantForm.title.Valid, confirmButtonText: 'Ok',
                        text: contantForm.message.Valid, showLoaderOnConfirm: true,
                        preConfirm: function (email) {
                          return new Promise(function (resolve, reject) {
                            setTimeout(function() {resolve()}, 6000)
                          })
                        }
                    }])
                    serviceFormSigma.sendForm(idForm, data).then(function (data) {
                      $("#sigmaForm")[0].reset();
                      swal(contantForm.title.Acept, contantForm.message.Acept, "success");
                      $("#submitButton").prop('disabled', false);
                      
                      if (data.url != "" && data.url != null) {
                        setTimeout(function() {$window.location.href = data.url;}, 3000);
                      } else {
                        $route.reload();
                        $window.location.reload();
                      }
                      //swal(contantForm.title.Acept, contantForm.message.Acept, "success");
                    });
                    });
                  })
                  .error(function () {
                    $("#submitButton").prop('disabled', false);
                  });
        });
      }
      $scope.validateTelephoneNumber = function(data){
        var telefonoDigitado = 0;
        telefonoDigitado = data.phone
          
        if(telefonoDigitado.toString().length != 10){
           return true;  //si es diferente de 10.
        } 
        return false; //ya que no es igual a 10.
      },
      $scope.coverDateToString = function () {
        var defer = $q.defer();
        var objReturn = {};
        for (var i in $scope.input) {
          var value = $scope.input[i].value;
          var name = $scope.input[i].id;
          var component = $scope.input[i].component;
          if (component == "encabezado" && component == "button") {
            continue;
          }
          if (component == "dateInput") {
            var date = new Date(value);
            var month, day;
            if (date.getMonth() + 1 < 10) {
              month = date.getMonth() + 1;
              month = "0" + month.toString();
            } else {
              month = date.getMonth() + 1;
            }
            if (date.getDate() < 10) {
              day = "0" + date.getDate().toString();
            } else {
              day = date.getDate() + 1; //con esta adicion la fecha ya no retrocede...
            }
            $scope.input[i].value = date.getFullYear() + "-" + month + "-" + day;
          }
        }
        defer.resolve($scope.input);
        return defer.promise;
      },
      $scope.validateUnderAge = function(data){        
        
        //esto es para la fecha actual
        var date = new Date();
        var year1 = date.getFullYear();
        
        //esto es para la fecha digitada
        var date = new Date(data.birthdate);
        var year2 = date.getFullYear();
        
        //fecha actual menos 18 para saber año de comparacion...
        var comparativeYear = (year1 - 18);
        
        if (year2 > comparativeYear){
          return true;
        }else{
          return false;
        }
        
      }
      $scope.validateDate = function (data){
        
        //esto es para la fecha actual
        var date = new Date();
        var month, day;
        if (date.getMonth() + 1 < 10) {
          month = date.getMonth() + 1;
          month = "0" + month.toString();
        } else {
          month = date.getMonth() + 1;
        }
        if (date.getDate() < 10) {
          day = "0" + date.getDate().toString();
        } else {
          day = date.getDate(); //con esta adicion la fecha ya no retrocede...
        }
        var value1 = date.getFullYear() + "-" + month + "-" + day;
        var dateFormated1 = new Date(value1);
        
        //este date es para la fecha digitada
        var dateSelected = data.birthdate;
        var date = new Date(dateSelected);
        var month, day;
        if (date.getMonth() + 1 < 10) {
          month = date.getMonth() + 1;
          month = "0" + month.toString();
        } else {
          month = date.getMonth() + 1;
        }
        day = (date.getDate()+1)
        if (day < 10) {
          day = "0" + day.toString();
        } else {  
          day = date.getDate()+1; //con esta adicion la fecha ya no retrocede...
        }
        var value2 = date.getFullYear() + "-" + month + "-" + day;
        var dateFormated2 = new Date(value2);
        
        if (dateFormated1 >= dateFormated2){
          return true;
        } else {
          return false;
        }
      }
      
    }])
}).call(this);