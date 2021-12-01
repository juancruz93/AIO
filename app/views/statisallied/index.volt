{% extends "templates/default.volt" %} 
{% block css %}
  {{ stylesheet_link('library/dialog-effects/css/dialog.css') }}
  {{ stylesheet_link('library/dialog-effects/css/dialog-wilma.css') }}
  {{ stylesheet_link('library/angular-material-1.1.0/css/angular-material.min.css') }} 

{% endblock %}
{% block js %}

  {{ partial("partials/js_notifications_partial") }}
  {{ partial("partials/slideontop_notification_partial") }}

  {# Dialogs #}
  {{ stylesheet_link('library/dialog-effects/css/dialog.css') }}
  {{ stylesheet_link('library/dialog-effects/css/dialog-wilma.css') }}
  {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}

  {{ javascript_include('library/angular-1.5/js/angular-route.min.js') }}
  {{ javascript_include('library/select2-4.0.0/js/select2.min.js') }}
  {{ javascript_include('library/angular-xeditable-0.2.0/js/xeditable.min.js') }}
  {#  {{ javascript_include('library/ngProgress-master/build/ngProgress.js') }}#}
  {{ javascript_include('js/checklist-model.js') }}

  <!-- Angular Material Dependencies -->
{{ javascript_include('library/angular-material-1.1.0/js/angular-animate.min.js') }}
{{ javascript_include('library/angular-material-1.1.0/js/angular-aria.min.js') }}
  <!-- Angular Material Javascript now available via Google CDN; version 1.0.7 used here -->
  {{ javascript_include('library/angular-material-1.1.0/js/angular-material.min.js') }}
  
  {{ javascript_include('js/angular/statisallied/app.js') }}
  {{ javascript_include('js/angular/statisallied/services.js') }}
  {{ javascript_include('js/angular/statisallied/directives.js') }}
  {{ javascript_include('js/angular/statisallied/filters.js') }}
  {{ javascript_include('js/angular/statisallied/controllers.js') }}

{% endblock %}
{% block content %}
 
<div class="space"></div>    
  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12">    
      <div id="app-container" class="container-fluid">
        <div ng-view>
        </div>
      </div>
    </div>
  </div>
{% endblock %}
{% block footer %}


  <script type="text/javascript">
    var relativeUrlBase = "{{ urlManager.get_base_uri() }}";
    var fullUrlBase = "{{ urlManager.get_base_uri(true) }}";
    var templateBase = "statisallied";
  </script>




{% endblock %}