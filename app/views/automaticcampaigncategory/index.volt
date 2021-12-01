{% extends "templates/default.volt" %}
{% block header %}
  {# Notifications #}
  {{ partial("partials/slideontop_notification_partial") }}

  {# Dialogs #}
  {{ stylesheet_link('library/dialog-effects/css/dialog.css') }}
  {{ stylesheet_link('library/dialog-effects/css/dialog-wilma.css') }}
  {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}
  <link rel="stylesheet"
        href="https://ajax.googleapis.com/ajax/libs/angular_material/0.11.2/angular-material.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/3.4.5/select2.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.8.5/css/selectize.default.min.css">
  <link rel="stylesheet" type="text/css" media="screen"
        href="https://tarruda.github.com/bootstrap-datetimepicker/assets/css/bootstrap-datetimepicker.min.css">
  {{ stylesheet_link('library/select2/css/select2.min.css') }}
  {{ stylesheet_link('library/select2/css/select2-bootstrap.min.css') }}
  {{ stylesheet_link('library/bootstrap-toggle-master/css/bootstrap-toggle.min.css') }}
{% endblock %}
{% block js %}
  {{ javascript_include('library/angular-ui-router/angular-ui-router.min.js') }}
  {{ javascript_include('js/angular/automaticcampaigncategory/app.js') }}
  {{ javascript_include('js/angular/automaticcampaigncategory/controller.js') }}
  {{ javascript_include('js/angular/automaticcampaigncategory/services.js') }}
  {{ javascript_include('library/select2/js/select2.min.js') }}
  {{ javascript_include('library/bootstrap-toggle-master/js/bootstrap-toggle.min.js') }}

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
    var templateBase = "automaticcampaigncategory";
  </script>

{% endblock %}


