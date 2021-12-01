{% extends "templates/default.volt" %}
{% block css %}
  {{ stylesheet_link('library/ui-select-master/dist/select.css') }}
  {#  <link rel="stylesheet" href="http://netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.css">#}
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/3.4.5/select2.css">    
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.8.5/css/selectize.default.min.css">
  {{ stylesheet_link('library/angular-xeditable-0.2.0/css/xeditable.css') }}
  {{ stylesheet_link('library/angular-moment-picker-master/src/angular-moment-picker.css') }}
  {# Notifications #}
  {{ partial("partials/css_notifications_partial") }}
    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/angular_material/0.11.2/angular-material.min.css">

  <style type="text/css" >
    #menu {
      float:left;
      {#      left:50%;#}
      list-style-type:none;
      margin:0 auto;
      padding:0;
      position:relative;
    }

    #menu li {
      float:left;
      position:relative;
      right:50%;
    }
  </style>
{% endblock %}

{% block js %}
  {# {{ javascript_include('library/ui-select-master/dist/select.js')}} #}
  {# {{ javascript_include('library/ui-select-0.19/select.css')}} #}
  {{ javascript_include('library/ui-select-0.19.6/select.min.js') }}
  
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-sanitize.min.js"></script>


  {{ partial("partials/js_notifications_partial") }}
  {{ partial("partials/slideontop_notification_partial") }}

  {# Dialogs #}
  {{ stylesheet_link('library/dialog-effects/css/dialog.css') }}
  {{ stylesheet_link('library/dialog-effects/css/dialog-wilma.css') }}
  {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}

  {{ javascript_include('library/angular-1.5/js/angular-route.min.js') }}
  {{ javascript_include('js/angular/segment/app.js') }}
  {{ javascript_include('js/angular/segment/controllers.js') }}
  {{ javascript_include('js/angular/segment/services.js') }}
  {{ javascript_include('js/angular/segment/directives.js') }}
  {{ javascript_include('library/angular-moment-picker-master/src/angular-moment-picker.js') }}
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-animate.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-aria.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angular_material/1.0.7/angular-material.min.js"></script>
  {{ javascript_include('library/moment/src/prueba.js') }}

  {{ javascript_include('library/angular-xeditable-0.2.0/js/xeditable.min.js') }}
  {{ javascript_include('library/angular-strap-master/dist/angular-strap.min.js') }}
  {{ javascript_include('library/angular-strap-master/dist/angular-strap.tpl.min.js') }}
{% endblock %}
{% block content %}
  <div class="clearfix"></div>
  <div class="space"></div>

  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
      <div class="title">
        Lista de segmentos
      </div>
      <hr class="basic-line" />
    </div>
  </div>
  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
      <div ng-view></div>
    </div>
  </div>

{% endblock %}
{% block footer %}
  {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}

  <script type="text/javascript">
    var relativeUrlBase = "{{urlManager.get_base_uri()}}";
    var fullUrlBase = "{{urlManager.get_base_uri(true)}}";
    var templateBase = "segment";
  </script>

      {{ javascript_include('library/angular-1.5/js/angular.min.js') }}
{% endblock %}
