{% extends "templates/default.volt" %}
{% block css %}
  {# Notifications #}
  {{ partial("partials/css_notifications_partial") }}
  {{ stylesheet_link('library/angular-material-1.1.0/css/angular-material.min.css') }}
  {{ stylesheet_link('library/ui-select-master/dist/select.css') }}
  {#<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/3.4.5/select2.css">#}
  {#<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.8.5/css/selectize.default.min.css">#}
  {#{{ stylesheet_link('library/select2/css/select2.min.css') }}
  {{ stylesheet_link('library/select2/css/select2-bootstrap.min.css') }}#}
{% endblock %}

{% block js %}
  {{ partial("partials/js_notifications_partial") }}
  {{ partial("partials/slideontop_notification_partial") }}
  {{ javascript_include('library/angular-ui-router/angular-ui-router.min.js') }}
  {{ javascript_include('js/angular/accounting/app.js') }}
  {{ javascript_include('js/angular/accounting/controllers.js') }}
  {{ javascript_include('js/angular/accounting/services.js') }}
  {{ javascript_include("library/angular-1.5/js/angular-animate.min.js") }}
  {{ javascript_include("library/angular-1.5/js/angular-aria.min.js") }}
  {{ javascript_include('library/angular-material-1.1.0/js/angular-material.min.js') }}
  {{ javascript_include('library/ui-select-master/dist/select.min.js') }}
  {{ javascript_include('library/select2/js/select2.min.js') }}
{% endblock %}
{% block content %}
  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
      <div ui-view></div>
    </div>
  </div>
{% endblock %}
{% block footer %}
  <script type="text/javascript">
    var relativeUrlBase = "{{ urlManager.get_base_uri() }}";
    var fullUrlBase = "{{ urlManager.get_base_uri(true) }}";
    var templateBase = "accounting";
  </script>

{% endblock %}
