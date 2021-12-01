<!DOCTYPE html>
<html ng-app="appSurveySigma" ng-controller="controllerSurveySigma">
  <head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="{{urlBase}}library/bootstrap-3.3.4/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="{{urlBase}}library/angular-form-builder-master/dist/angular-form-builder.css"/>
    <link rel="stylesheet" href="{{urlBase}}library/spectrum/css/spectrum.css"/>
    <link rel="stylesheet" href="{{urlBase}}library/sweetalert2/sweetalert2.min.css"/>
    <link rel="stylesheet" href="{{urlBase}}css/surveyStyles.css"/>
    <link rel="stylesheet" href="{{urlBase}}css/adjustments.css"/>
    <link rel="shortcut icon" type="image/x-icon" href="{{url('')}}themes/{{theme.name}}/images/favicons/favicon48x48.ico">
  </head>
  <body ng-init="getSurvey({{survey.idSurvey}})" data-ng-style="{'background-color':dataContent.background}">
    <div class="container-fluid">
      {% set avaliable = true %}
      {% set msg = "" %}
      {% set status = true %}

      {# {% if not status %}
         {#{% set avaliable = false %}#}
      {#        {% set msg = "La encuesta tiene como estado <b>Borrador</b> así que no podrá realizar respuestas a esta encuesta" %}#}
      {#{% else %}
        {% if not startDate %}
          {% set avaliable = false %}
          {% set msg = "La encuesta aún no está disponible, ya que la fecha inicial de la encuesta es superior a la actual" %}
        {% endif %}

        {% if not endDate %}
          {% set avaliable = false %}
          {% set msg = "La encuesta ya no está disponible, debido a que la fecha de expiración de la encuesta ya se cumplió" %}
        {% endif %}
      {% endif %}#}

      {% if avaliable %}
        <div class="content" data-ng-style="{'background-color':dataContent.background}" id="surveyBody">
          {% if not status %}
            <header>
              <div class="alert alert-info" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
                <span class="glyphicon glyphicon-info-sign"></span> {{msg}}
              </div>
            </header>
          {% else %}
            <section>
              <div data-ng-if="complet">
                <form {% if status %} data-ng-submit="sendData({{survey.idSurvey}},{{idContact}})" {% endif %}>
                  <div data-ng-model="input"  fb-form="sigmaSurvey" fb-default="defaultValue"></div>
                </form>
              </div>
            </section>
            <br>
    {#        <footer>
              <div class="footer">
                <img src="{{urlBase}}images/sigma_movil_power.png" class="img-responsive center-block" style="max-width: 40%" />
              </div>
            </footer>#}
          {% endif %}
        </div>
      {% else %}
        <div class="content">
          <div class="alert alert-danger" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
            <span class="glyphicon glyphicon-info-sign"></span>
            {{msg}}
            <br><br>
            <a href="http://www.sigmamovil.com" class="btn btn-default" ><span class="glyphicon glyphicon-remove-sign"></span> Salir</a>
          </div>
        </div>
      {% endif %}
    </div>
    <script src="{{urlBase}}library/jquery/jquery-1.11.2.min.js"></script>
    <script src="{{urlBase}}library/bootstrap-3.3.4/js/bootstrap.min.js"></script>
    <script src="https:////cdnjs.cloudflare.com/ajax/libs/moment.js/2.1.0/moment.min.js"></script> 
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.8/angular.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-sanitize.min.js"></script>
    <script src="{{urlBase}}library/angular-form-builder-master/dist/angular-form-builder-v1.js"></script>
    <script src="{{urlBase}}js/angular/survey/angular-form-builder-components-survey.js"></script>
    <script src="{{urlBase}}library/angular-spectrum-colorpicker/dist/angular-spectrum-colorpicker.js"></script>
    <script src="{{urlBase}}library/spectrum/js/spectrum.js"></script>
    <script src="{{urlBase}}library/sweetalert2/sweetalert2.min.js"></script>
    <script type="text/javascript" src="//kelp404.github.io/angular-validator/dist/angular-validator.min.js"></script>
    <script type="text/javascript" src="//kelp404.github.io/angular-validator/dist/angular-validator-rules.min.js"></script>
    <script src="//angular-ui.github.io/bootstrap/ui-bootstrap-tpls-2.4.0.js"></script>
    <script src="{{urlBase}}js/angular/survey/appsurvey.js"></script>
    <script type="text/javascript">
var fullUrlBaseSigmaDomain = "{{urlBase}}";
    </script>
  </body> 
</html>