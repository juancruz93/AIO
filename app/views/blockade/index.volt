{% extends "templates/default.volt" %}
{% block css %}
  {{ stylesheet_link('library/select2-4.0.0/css/select2.min.css') }}
  {{ stylesheet_link('library/angular-material-0.11.2/css/angular-material.min.css') }}
  {# Notifications #}
  {{ partial("partials/css_notifications_partial") }}
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

  {# Dialogs #}
  {{ stylesheet_link('library/dialog-effects/css/dialog.min.css') }}
  {{ stylesheet_link('library/dialog-effects/css/dialog-wilma.min.css') }}
  {{ javascript_include('library/dialog-effects/js/dialogFx.min.js') }}

  {{ javascript_include('library/angular-1.5/js/angular-route.min.js') }}
  {{ javascript_include('js/angular/blockade/app.min.js') }}
  {{ javascript_include('js/angular/blockade/controllers.min.js') }}
  {{ javascript_include('js/angular/blockade/services.min.js') }}
  {{ javascript_include('js/angular/blockade/directives.min.js') }}
  {{ javascript_include('library/ngProgress-master/build/ngProgress.min.js') }}
  {{ javascript_include('library/select2-4.0.0/js/select2.min.js') }}
  <!-- Angular Material Dependencies -->
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-animate.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-aria.min.js"></script>
  <!-- Angular Material Javascript now available via Google CDN; version 1.0.7 used here -->
  <script src="https://ajax.googleapis.com/ajax/libs/angular_material/1.0.7/angular-material.min.js"></script>


{% endblock %}
{% block content %}
  <div class="clearfix"></div>
  <div class="space"></div>

 
  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
      <div ng-view></div>
    </div>
  </div>

{% endblock %}
{% block footer %}
  {#  {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}#}

  <script type="text/javascript">
    var relativeUrlBase = "{{urlManager.get_base_uri()}}";
    var fullUrlBase = "{{urlManager.get_base_uri(true)}}";
    var templateBase = "blockade";
  </script>
  {#    {{ javascript_include('library/angular-1.5/js/angular.min.js') }}#}
{% endblock %}

