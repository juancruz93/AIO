{% extends "templates/default.volt" %}
{% block css %}
  {# Notifications #}
  {{ partial("partials/css_notifications_partial") }}
  <link rel="stylesheet"
        href="https://ajax.googleapis.com/ajax/libs/angular_material/0.11.2/angular-material.min.css">
  {{ stylesheet_link('library/select2-4.0.0/css/select2.css') }}
{% endblock %}

{% block js %}
  {{ partial("partials/js_notifications_partial") }}
  {{ partial("partials/slideontop_notification_partial") }}

  {# Dialogs #}
  {{ stylesheet_link('library/dialog-effects/css/dialog.css') }}
  {{ stylesheet_link('library/dialog-effects/css/dialog-wilma.css') }}
  {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}
  {# Select 2 #}
  {{ javascript_include('library/select2-4.0.0/js/select2.min.js') }}
  {{ javascript_include('library/angular-1.5/js/angular-route.min.js') }}
  {{ javascript_include('js/angular/language/app.js') }}
  {{ javascript_include('js/angular/language/controllers.js') }}
  {{ javascript_include('js/angular/language/directives.js') }}
  {{ javascript_include('js/angular/language/filters.js') }}
  {{ javascript_include('js/angular/language/services.js') }}

  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-aria.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-animate.min.js"></script>
  {{ javascript_include('library/angular-strap-master/dist/angular-strap.min.js') }}
  {{ javascript_include('library/angular-strap-master/dist/angular-strap.tpl.min.js') }}
  <script src="https://ajax.googleapis.com/ajax/libs/angular_material/1.0.7/angular-material.min.js"></script>
  
  {# Socket.IO#}
{#  {{ javascript_include('js/socket.io.js') }}
  {{ javascript_include('js/main.js') }}#}

{% endblock %}
{% block content %}

  <div class="clearfix"></div>
  <div class="space"></div>     
    <div ng-view></div>
  

{% endblock %}

{% block footer %}
  {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}

  <script type="text/javascript">
    var relativeUrlBase = "{{urlManager.get_base_uri()}}";
    var fullUrlBase = "{{urlManager.get_base_uri(true)}}";
    var templateBase = "language";
  </script>    

{% endblock %}
