{% extends "templates/default.volt" %}
{% block css %}
  {# Notifications #}
  {{ partial("partials/css_notifications_partial") }}
  {{ stylesheet_link('library/angular-material-0.11.2/css/angular-material.min.css') }}
  {{ stylesheet_link('library/select2-4.0.0/css/select2.css') }}
  {{ stylesheet_link('library/bootstrap-fileinput-master/css/fileinput.min.css') }}
{% endblock %}

{% block js %}
  {{ partial("partials/js_notifications_partial") }}
  {{ partial("partials/slideontop_notification_partial") }}

  {# Select 2 #}
  {{ javascript_include('library/select2-4.0.0/js/select2.min.js') }}
  {{ javascript_include('library/angular-1.5/js/angular-route.min.js') }}
  {{ javascript_include('library/angular-file-upload-master/dist/angular-file-upload.js') }}
  {{ javascript_include('js/angular/knowledgebase/app.js') }}
  {{ javascript_include('js/angular/knowledgebase/controllers.js') }}
  {{ javascript_include('js/angular/knowledgebase/directives.js') }}
  {{ javascript_include('js/angular/knowledgebase/filters.js') }}
  {{ javascript_include('js/angular/knowledgebase/services.js') }}
  
  {{ javascript_include('library/angular-1.5/js/angular-aria.min.js') }}
  {{ javascript_include('library/angular-1.5/js/angular-animate.min.js') }}
  {{ javascript_include('library/angular-strap-master/dist/angular-strap.min.js') }}
  {{ javascript_include('library/angular-strap-master/dist/angular-strap.tpl.min.js') }}
  {{ javascript_include('library/angular-material-0.11.2/js/angular-material.min.js') }}

{% endblock %}
{% block content %}

  <div class="clearfix"></div>
  <div class="space"></div> 
  <div ng-view></div>


{% endblock %}

{% block footer %}
  <script type="text/javascript">
    var relativeUrlBase = "{{urlManager.get_base_uri()}}";
    var fullUrlBase = "{{urlManager.get_base_uri(true)}}";
    var templateBase = "knowledgebase";
    var userRole = "{{user.Role.name}}";
    var root = "{{nameRoles.root}}";
    var master = "{{nameRoles.master}}";
    var allied = "{{nameRoles.allied}}";
    var account = "{{nameRoles.account}}";
    var subaccount = "{{nameRoles.subaccount}}";
  </script>    

{% endblock %}
