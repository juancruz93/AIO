{% extends "templates/default.volt" %}
{% block css %}
  {{ stylesheet_link('library/ui-select-master/dist/select.css') }}
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/3.4.5/select2.css">
  {{ stylesheet_link('library/dialog-effects/css/dialog.css') }}
  {{ stylesheet_link('library/dialog-effects/css/dialog-wilma.css') }}
  {{ stylesheet_link('library/bootstrap-datetimepicker-0.0.11/build/css/bootstrap-datetimepicker.min.css') }}
  {{ stylesheet_link('library/bootstrap-toggle-master/css/bootstrap-toggle.min.css') }}
  {{ stylesheet_link('library/select2-4.0.0/css/select2.css') }}
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.8.5/css/selectize.default.min.css">
{% endblock %}    
{% block js %}
  {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}
  {{ javascript_include('library/bootstrap-toggle-master/js/bootstrap-toggle.min.js') }}
  {{ javascript_include('library/angular-ui-router/angular-ui-router.min.js') }}
  {{ javascript_include('js/socket.io.js') }}
  {{ javascript_include('js/main.js') }}
  {{ javascript_include('js/angular/smstwoway/services.js') }}
  {{ javascript_include('js/angular/smstwoway/controllers.js') }}
  <script src="https://ajax.googleapis.com/ajax/libs/angular_material/1.0.7/angular-material.min.js"></script>
  {{ javascript_include('library/ui-select-master/dist/select.min.js') }}
  {{ javascript_include('js/angular/smstwoway/app.js') }}
  {{ javascript_include('library/bootstrap-datetimepicker-0.0.11/src/js/bootstrap-datetimepicker.js') }}
  {{ javascript_include('library/bootstrap-datetimepicker-0.0.11/src/js/locales/bootstrap-datetimepicker.es.js') }}
  <script type="text/javascript">
    $(function () {
      $('#datetimepicker1').datetimepicker();
    });
  </script>
{% endblock %}

{% block content %}


  <div class="clearfix"></div>
  <div class="space"></div>     
  <div id="console-event"></div>
  <ui-view></ui-view>

    <script type="text/javascript">
        var relativeUrlBase = "{{ urlManager.get_base_uri() }}";
        var fullUrlBase = "{{ urlManager.get_base_uri(true) }}";
        var templateBase = "smstwoway";
        var idSubaccount = {{user.Usertype.idSubaccount}};

  </script>

{% endblock %}   
