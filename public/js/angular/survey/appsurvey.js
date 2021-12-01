(function () {
  var app = angular.module('appSurveySigma', ['ngSanitize', 'builder', 'builder.components', 'validator.rules', 'ui.bootstrap', 'angularSpectrumColorpicker']);
  app.config([
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
//        validator: /^[0-9]{4}-(0[0-9]|1[0-2])-(0[0-9]|[0-2][0-9]|3[0-1])$/,
        validator: /.+/,
        error: 'El formato de la fecha no es valido.'
      });
      return $validatorProvider.register('url', {
        invoke: 'blur',
        validator: /((([A-Za-z]{3,9}:(?:\/\/)?)(?:[-;:&=\+\$,\w]+@)?[A-Za-z0-9.-]+|(?:www.|[-;:&=\+\$,\w]+@)[A-Za-z0-9.-]+)((?:\/[\+~%\/.\w-_]*)?\??(?:[-\+=&;%@.\w_]*)#?(?:[\w]*))?)/,
        error: 'El formato de la url no es valido.'
      });
    }
  ]);
  app.service('serviceSurveySigma', ['$http', '$q', function ($http, $q) {
      this.getSurvey = function (idSurvey) {
        var defer = $q.defer();
        var url = fullUrlBaseSigmaDomain + 'api/survey/getcontent/' + idSurvey;
        $http.get(url)
                .success(function (data) {
                  defer.resolve(data);
                })
                .error(function (data) {
                  defer.reject(data);
                });
        return defer.promise;
      };

      this.sendSurvey = function (idSurvey, idContact, data) {
        var defer = $q.defer();
        var url = fullUrlBaseSigmaDomain + "api/survey/saveanswer/" + idSurvey + "/" + idContact;
        $http.post(url, data)
                .success(function (data) {
                  defer.resolve(data);
                })
                .error(function (data) {
                  defer.reject(data);
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
          var objExt = data[i].objExt;
          if(value == ""){
            value = "Sin Respuesta";
          }
    
          if (objExt.notDb || component == "paragraph") {
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
            if (typeof objExt.anotherAnswer != "undefined" && objExt.anotherAnswer == true) {
              if (typeof objExt.inputTextOther != "undefined") {
                inputOther = objExt.inputTextOther.split(",");
                value = value.concat(inputOther);
              }
            }
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
          if (value == "other" && (typeof objExt.anotherAnswer != "undefined" && objExt.anotherAnswer == true)) {
            value = objExt.inputTextOther;
          }
          objReturn[name] = value;
        }
        defer.resolve(objReturn);
        return defer.promise;
      }

    }]);

  app.controller('controllerSurveySigma', ['$scope', 'serviceSurveySigma', '$builder', '$validator', '$q', '$window', function ($scope, serviceSurveySigma, $builder, $validator, $q, $window) {
      $scope.complet = false;
      $scope.input = [];
      $scope.isDisabled = false;
      $scope.getSurvey = function (idSurvey) {
        serviceSurveySigma.getSurvey(idSurvey).then(function (data) {
          $scope.dataContent = JSON.parse(data.content);
          $scope.deleted = data.deleted;
          $builder.setForm('sigmaSurvey', $scope.dataContent.content);
          // se realiza comentario por motivos de funcionamiento y error cuando abre el formulario de encuesta publico
          //$builder.setLogicDefault(false);
          $scope.complet = true;          
          if($scope.deleted>0){
             swal("Ha ocurrido un error", "ENCUESTA ELIMINADA", "error");
             $("#surveyBody").css({"pointer-events": "none","color" : "darkGray"});           
          }
        }).catch(function (data) {
          swal("Ha ocurrido un error", data.message, "error");
        });
      };

      $scope.$watch('input', function (data) {
        
      }, true);
      

      $scope.sendData = function (idSurvey, idContact) {
        var btn = document.getElementById("submitButton");
        btn.disabled = true;
        $validator.validate($scope, 'sigmaSurvey')
                .success(function () {
                  serviceSurveySigma.converArrayToObj($scope.input).then(function (data) {
                    serviceSurveySigma.sendSurvey(idSurvey, idContact, data).then(function (data) {
                      if (data.message == "success") {
                        $window.location.href = data.url;
                      }
                      //btn.disabled = false;
                    }).catch(function (data) {
                      btn.disabled = false;
                      swal("Ha ocurrido un error", data.message, "error");
                    });
                  });
                })
                .error(function () {
                  btn.disabled = false;
                });
      };

    }])

}).call(this);
