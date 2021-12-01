{% extends "templates/default.volt" %}
{% block header %}
  {# Notifications #}
  {{ partial("partials/slideontop_notification_partial") }}

  {# Dialogs #}
  {{ stylesheet_link('library/dialog-effects/css/dialog.css') }}
  {{ stylesheet_link('library/dialog-effects/css/dialog-wilma.css') }}
  {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}
  {{ stylesheet_link('library/angular-material-0.11.2/css/angular-material.min.css') }}
{% endblock %}
{% block js %}
  {{ javascript_include('library/angular-ui-router/angular-ui-router.min.js') }}
  {{ javascript_include('js/angular/currency/app.js') }}
  {{ javascript_include('js/angular/currency/controller.js') }}
  {{ javascript_include('js/angular/currency/services.js') }}
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
    var templateBase = "currency";
  </script>

{% endblock %}

