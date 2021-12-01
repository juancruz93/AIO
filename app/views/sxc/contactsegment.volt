{% extends "templates/default.volt" %}
{% block css %}
    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/angular_material/0.11.2/angular-material.min.css">
  {{ stylesheet_link('library/angular-xeditable-0.2.0/css/xeditable.css') }}
  {# Notifications #}
  {{ partial("partials/css_notifications_partial") }}
  <link rel="stylesheet" type="text/css" media="screen"
        href="https://tarruda.github.com/bootstrap-datetimepicker/assets/css/bootstrap-datetimepicker.min.css">
  {{ stylesheet_link('library/select2-4.0.0/css/select2.css') }}
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
  {{ partial("partials/js_notifications_partial") }}
  {{ partial("partials/slideontop_notification_partial") }}

  {# Dialogs 
  {{ stylesheet_link('library/dialog-effects/css/dialog.css') }}
  {{ stylesheet_link('library/dialog-effects/css/dialog-wilma.css') }}
  {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}#}

  {{ javascript_include('library/angular-1.5/js/angular-route.min.js') }}
  {{ javascript_include('js/angular/sxc/app.js') }}
  {{ javascript_include('js/angular/sxc/controllers.js') }}
  {{ javascript_include('js/angular/sxc/services.js') }}
  {{ javascript_include('js/angular/sxc/directives.js') }}
  {{ javascript_include('library/bootstrap-datetimepicker-0.0.11/src/js/bootstrap-datetimepicker.js') }}
  {{ javascript_include('library/bootstrap-datetimepicker-0.0.11/src/js/locales/bootstrap-datetimepicker.es.js') }}
  {{ javascript_include('library/angular-xeditable-0.2.0/js/xeditable.min.js') }}
  {{ javascript_include('js/checklist-model.js') }}

  <!-- Angular Material Dependencies -->
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-animate.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-aria.min.js"></script>
  <!-- Angular Material Javascript now available via Google CDN; version 1.0.7 used here -->
  <script src="https://ajax.googleapis.com/ajax/libs/angular_material/1.0.7/angular-material.min.js"></script>
{% endblock %}
{% block content %}
  <div class="clearfix"></div>
  <div class="space"></div>

  <div class="row" >
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
      <div class="title">
        Contactos del segmento <strong>{{segment.name}}</strong>
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

  <script type="text/javascript">
    var relativeUrlBase = "{{urlManager.get_base_uri()}}";
    var fullUrlBase = "{{urlManager.get_base_uri(true)}}";
    var templateBase = "sxc";
    var idSegment = "{{segment.idSegment}}";
  </script>
{% endblock %}

