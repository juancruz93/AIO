{% extends "templates/default.volt" %}
{% block header %}
  {# Notifications #}
  {{ partial("partials/slideontop_notification_partial") }}

  {# Dialogs #}
  {{ stylesheet_link('library/dialog-effects/css/dialog.min.css') }}
  {{ stylesheet_link('library/dialog-effects/css/dialog-wilma.min.css') }}
  {{ javascript_include('library/dialog-effects/js/dialogFx.min.js') }}
  {{ stylesheet_link('library/ui-select-master/dist/select.min.css') }}
  {{ stylesheet_link('library/angular-material-0.11.2/css/angular-material.min.css') }}
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/3.4.5/select2.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.8.5/css/selectize.default.min.css">
  {{ stylesheet_link('library/select2-4.0.0/css/select2.css') }}
{% endblock %}
{% block js %}
  {{ javascript_include('library/angular-ui-router/angular-ui-router.min.js') }}
  {{ javascript_include('library/ui-select-master/dist/select.min.js') }}
  {{ javascript_include('library/select2-4.0.0/js/select2.min.js') }}
  {#{{ javascript_include('js/angular/tax/app.js') }}
  {{ javascript_include('js/angular/tax/controller.js') }}
  {{ javascript_include('js/angular/tax/services.js') }}#}
  
  {{ javascript_include('js/angular/tax/app.min.js') }}
  {{ javascript_include('js/angular/tax/controller.min.js') }}
  {{ javascript_include('js/angular/tax/services.min.js') }}
  
  {{ javascript_include('library/select2/js/select2.min.js') }}
  {{ javascript_include('library/angular-material-0.11.2/js/angular-animate.min.js') }}
  {{ javascript_include('library/angular-material-0.11.2/js/angular-aria.min.js') }}
  {{ javascript_include('library/angular-material-0.11.2/js/angular-material.min.js') }}
{% endblock %}
{% block content %}
  <div class="clearfix"></div>
  <div class="space"></div>     

  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
      <div ui-view></div>
    </div>
  </div>
{% endblock %}  
{% block footer %}
  {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}

  <script type="text/javascript">
    var relativeUrlBase = "{{ urlManager.get_base_uri() }}";
    var fullUrlBase = "{{ urlManager.get_base_uri(true) }}";
    var templateBase = "tax";
  </script>

{% endblock %}