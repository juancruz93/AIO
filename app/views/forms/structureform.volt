<!DOCTYPE html>
<html ng-app="appFormSigma" ng-controller="controllerFormSigma">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="%%urlBase%%library/bootstrap-3.3.4/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="%%urlBase%%library/angular-form-builder-master/dist/angular-form-builder.css"/>
    <link rel="stylesheet" href="%%urlBase%%library/spectrum/css/spectrum.min.css"/>
    <link rel="stylesheet" href="%%urlBase%%library/sweetalert2/sweetalert2.min.css"/>
  </head>
  <body ng-init="getForm(%%IDFORM%%)">
    <div ng-if="complet">
      <form id="sigmaForm" class="form-horizontal container" ng-style="{'background-color':dataContent.background}" ng-submit="sendData(%%IDFORM%%)">
        <div class="row">
          <div class="col-12 col-sm-10 col-md-10 col-lg-10 col-xl-10">
            <div ng-model="input"  fb-form="sigmaForm" fb-default="defaultValue"></div>
          </div>
        </div>
      </form>      
    </div>
    <script src="%%urlBase%%library/jquery/jquery-1.11.2.min.js"></script>
    <script src="%%urlBase%%library/bootstrap-3.3.4/js/bootstrap.min.js"></script>
    <script src="https:////cdnjs.cloudflare.com/ajax/libs/moment.js/2.1.0/moment.min.js"></script> 
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.8/angular.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-sanitize.min.js"></script>
    <script src="https://angular-ui.github.io/bootstrap/ui-bootstrap-tpls-2.5.0.js"></script>
    <script src="%%urlBase%%library/angular-form-builder-master/dist/angular-form-builder-v1.js"></script>
    <script src="%%urlBase%%library/angular-form-builder-master/dist/angular-form-builder-components.js?v=1.0.0"></script>
    <script src="%%urlBase%%library/angular-spectrum-colorpicker/dist/angular-spectrum-colorpicker.js"></script>
    <script src="%%urlBase%%library/spectrum/js/spectrum.js"></script>
    <script type="text/javascript" src="https://kelp404.github.io/angular-validator/dist/angular-validator.min.js"></script>
    <script type="text/javascript" src="https://kelp404.github.io/angular-validator/dist/angular-validator-rules.min.js"></script>
    <script src="https://angular-ui.github.io/bootstrap/ui-bootstrap-tpls-2.4.0.js"></script>
    <script src="%%urlBase%%library/sweetalert2/sweetalert2.min.js"></script>
    <script src="%%urlBase%%js/angular/forms/appform.js?v=1.0.0"></script>
    <!--script src="%%urlBase%%js/angular/forms/appform.min.js"></script-->
    <script type="text/javascript">
      var fullUrlBaseSigmaDomain = "%%urlBase%%";
      var HB = "%%HB%%";
    </script>
  </body> 
</html>

