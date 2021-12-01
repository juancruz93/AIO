{% extends "templates/default.volt" %}
{% block header %}
  {# Notifications #}
  {{ partial("partials/slideontop_notification_partial") }}

  {# Dialogs #}
  {{ stylesheet_link('library/dialog-effects/css/dialog.css') }}
  {{ stylesheet_link('library/dialog-effects/css/dialog-wilma.css') }}
  {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}
  {{ stylesheet_link('library/ui-select-master/dist/select.css') }}
  {{ stylesheet_link('library/angular-material-1.1.0/css/angular-material.min.css') }}
  {{ stylesheet_link('library/select2-4.0.0/css/select2.css') }}  
  {{ stylesheet_link('library/select2/css/select2.css') }}  
  
  
  {{ stylesheet_link('library/bootstrap-datetimepicker-0.0.11/build/css/bootstrap-datetimepicker.min.css') }}
  {{ stylesheet_link('library/angular-moment-picker-master/src/angular-moment-picker.css') }}
{% endblock %}
{% block js %}
  {{ javascript_include('library/angular-file-upload-master/dist/angular-file-upload.js') }}
  {{ javascript_include('library/ui-select-master/dist/select.js') }}
  {{ javascript_include('library/angular-ui-router/angular-ui-router.js') }}
  {{ javascript_include('library/angular-xeditable-0.2.0/js/xeditable.min.js') }}

  <!-- Landing page -->

  {{ javascript_include('js/angular/landingpage/controllers.js') }}
  {{ javascript_include('js/angular/landingpage/services.js') }}
  {{ javascript_include('js/angular/landingpage/app.js') }}
  {{ javascript_include('js/checklist-model.js') }}

  <!-- datetimepicker -->
  {{ javascript_include('library/bootstrap-datetimepicker-0.0.11/src/js/bootstrap-datetimepicker.js') }}
  {{ javascript_include('library/bootstrap-datetimepicker-0.0.11/src/js/locales/bootstrap-datetimepicker.es.js') }}
  

  <!-- Angular Material Dependencies -->
  {{ javascript_include('library/angular-material-1.1.0/js/angular-animate.min.js') }}
  {{ javascript_include('library/angular-material-1.1.0/js/angular-aria.min.js') }}
  {{ javascript_include('library/angular-material-1.1.0/js/angular-messages.min.js') }}
  {{ javascript_include('library/angular-material-1.1.0/js/angular-material.min.js') }}
  {{ javascript_include('library/ui-bootstrap/ui-bootstrap-tpls-2.4.0.js') }}
  {{ javascript_include('library/angular-moment-picker-master/src/angular-moment-picker.js') }}
  {{ javascript_include('library/moment/src/prueba.js') }}
  {{ javascript_include('library/angular-moment/angular-moment.min.js') }}
  {{ javascript_include('library/select2/js/select2.min.js') }}



{% endblock %}
{% block content %}
  <div class="clearfix"></div>
  <div class="space"></div>

  <div ng-cloak>
    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
        <ui-view></ui-view>
      </div>
    </div>  
  </div>
  
{% endblock %}
{% block footer %}
  {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}

  <script type="text/javascript">
    var relativeUrlBase = "{{ urlManager.get_base_uri() }}";
    var fullUrlBase = "{{ urlManager.get_base_uri(true) }}";
    
  </script>

{% endblock %}
