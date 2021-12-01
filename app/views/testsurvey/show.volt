{% extends "templates/clean.volt" %}
{% block header %}
  {# Notifications #}
  {{ partial("partials/css_notifications_partial") }}
{% endblock %}
{% block css %}
  {{ stylesheet_link('library/angular-material-1.1.0/css/angular-material.min.css') }}
  {{ stylesheet_link('library/ui-select-master/dist/select.css') }}
  {{ stylesheet_link('library/bootstrap-social-gh-pages/bootstrap-social.css') }}
  {{ stylesheet_link("partials/angular-toastr-master/dist/angular-toastr.css") }}
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/3.4.5/select2.css">    
{% endblock %}

{% block js %}
  <script>
    var fullUrlBase = "{{ urlManager.get_base_uri(true) }}";
  </script>
  {{ javascript_include('library/angular-1.5/js/angular.min.js') }}
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-sanitize.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-animate.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-aria.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angular_material/1.0.7/angular-material.min.js"></script>
  {{ partial("partials/js_notifications_partial") }}
  {{ partial("partials/slideontop_notification_partial") }}
  {{ javascript_include("library/angular-toastr-master/dist/angular-toastr.js") }}
  {{javascript_include('js/angular/testsurvey/app.js') }}
  

{% endblock %}

{% block content %}
  <div class="clearfix"></div>
  <div class="space"></div>
  <div class="clearfix"></div>
  {#  <div class="space"></div>#}
  <div class="site-wrapper" ng-controller="controllerTestSurvey" ng-cloak>
    <div class="site-wrapper-inner">
      <div class="center-container">
        <div class="session-container">
          <img class="session-logo" style="width: 100px" src="{{url('')}}themes/{{theme.name}}/images/aio.png" />
          <br>  
          <button ng-show="!login" class="btn  btn-social btn-facebook" ng-click="loginFunc()">
            <span class="fa fa-facebook"></span> Continuar con facebook
          </button>
          <div class="form-horizontal" ng-show="login">
            <div class="form-group">
              <label class="control-label col-sm-2">Descripción</label>
              <div class="col-sm-10">
                <textarea class="form-control" ng-model="description"></textarea>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-sm-2">Imagen</label>
              <div class="col-sm-10">
                <md-switch ng-model="imagen" aria-label="Switch 1">
                </md-switch>
              </div>
            </div>
            <div class="form-group" ng-show="!imagen">
              <label class="control-label col-sm-2">Url</label>
              <div class="col-sm-10">
                <input class="form-control" ng-model="url"/>
              </div>
            </div>
            <div class="form-group"  ng-show="imagen">
              <label class="control-label col-sm-2">Image</label>
              <div class="col-sm-10">
                <input class="form-control" ng-model="imageUrl"/>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-sm-2">Selección de pagina</label>
              <div class="col-sm-10">
                <div class="pull-left">
                  <button class="btn btn-social btn-facebook" ng-click="selectedPage()">
                    <span class="fa fa-facebook"></span> Selecciona fan page
                  </button>
                  <img ng-src="{{'{{fanPageSelected.picture}}'}}" /><a ng-href="https://www.facebook.com/{{"{{fanPageSelected.id}}"}}" >{{"{{fanPageSelected.name}}"}}</a>
                </div>
              </div>
            </div>
            <div class="form-group" ng-show="fanPageSelected">
              <button class="btn btn-social btn-facebook" ng-click="publish()">
                    <span class="fa fa-facebook"></span> Compartir
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <fb app-id="773095846180301"></fb>
  <div class="principal-menu per-footerColor per-bottomLine">
    <img class="session-logo float-right" style="width: 100px; margin-top: 10px; margin-right: 30px;" src="{{url('')}}themes/default/images/logo.png" />
  </div>
{% endblock %}  
