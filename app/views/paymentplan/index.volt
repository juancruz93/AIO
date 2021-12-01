{% extends "templates/default.volt" %}
{% block header %}
  {# Notifications #}
  {{ partial("partials/slideontop_notification_partial") }}
  
  {# Dialogs #}
  {{ stylesheet_link('library/dialog-effects/css/dialog.css') }}
  {{ stylesheet_link('library/dialog-effects/css/dialog-wilma.css') }}
  {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}
  {{ stylesheet_link('library/select2/css/select2.min.css') }}
  {{ stylesheet_link('library/select2/css/select2-bootstrap.min.css') }}
  {{ stylesheet_link('library/select2/css/select2.css') }}
  {{ stylesheet_link('library/angular-material-0.11.2/css/angular-material.min.css') }}
  {{ stylesheet_link('library/ui-select-master/dist/select.css') }}
{% endblock %}
{% block js %}
  {{ javascript_include('library/angular-ui-router/angular-ui-router.min.js') }}
  
  {{ javascript_include('library/select2/js/select2.min.js') }}
  {{ javascript_include('library/angular-material-0.11.2/js/angular-animate.min.js') }}
  {{ javascript_include('library/angular-material-0.11.2/js/angular-aria.min.js') }}
  {{ javascript_include('library/angular-material-0.11.2/js/angular-material.min.js') }}
  {{ javascript_include('library/ui-select-master/dist/select.min.js') }}
  
{#  {{ javascript_include('js/angular/paymentplan/controller.js') }}#}
  {{ javascript_include('js/angular/paymentplan/controller.min.js') }}
  {{ javascript_include('js/angular/paymentplan/services.min.js') }}
{#  {{ javascript_include('js/angular/paymentplan/services.js') }}#}
{#    {{ javascript_include('js/angular/paymentplan/app.js') }}#}
    {{ javascript_include('js/angular/paymentplan/app.min.js') }}

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
    var templateBase = "paymentplan";
  </script>

{% endblock %}
