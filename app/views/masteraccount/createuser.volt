{% extends "templates/default.volt" %}
{% block css %}
  {{ partial("partials/css_notifications_partial") }}
{% endblock %}
{% block js %}
  {{ partial("partials/js_notifications_partial") }}
  {{ partial("partials/slideontop_notification_partial") }}
  {{ javascript_include('js/angular/user/controller.js') }}
{% endblock %}
{% block header %}
  {# Notifications #}
  {#    {{ partial("partials/notifications_partial") }}#}
  {{ partial("partials/slideontop_notification_partial") }}

  {{ stylesheet_link('library/bootstrap-toggle-master/css/bootstrap-toggle.min.css') }}
  {{ javascript_include('library/bootstrap-toggle-master/js/bootstrap-toggle.min.js') }}

  {# Select 2 #}
  {{ stylesheet_link('library/select2-4.0.0/css/select2.css') }}
  {{ javascript_include('library/select2-4.0.0/js/select2.min.js') }}

  <script>
    $(function () {
      $('#toggle-one').bootstrapToggle({
        on: 'On',
        off: 'Off',
        onstyle: 'success',
        offstyle: 'danger',
        size: 'small'
      });
      $(".select2").select2({
        theme: "classic"
      });
    });
  </script>
{% endblock %}
{% block content %}    
  <div class="clearfix"></div>
  <div class="space"></div>
  {{ partial('partials/new_user', ['tittle': 'Creación de un nuevo Usuario en la cuenta  <strong>'~masteraccount.name~ '</strong>', 'url': 'masteraccount/createuser/'~idMasteraccount, 'idMasteraccount': idMasteraccount]) }}
{% endblock %}    
