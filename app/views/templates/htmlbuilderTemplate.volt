<!DOCTYPE html>
<html {% if app_name is defined %} ng-app="{{app_name}}" {% endif%}>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, maximum-scale=1, initial-scale=1, user-scalable=1">
    {{getTitle()}}
    <link href='https://fonts.googleapis.com/css?family=Questrial' rel='stylesheet' type='text/css'>
    <link rel="shortcut icon" type="image/x-icon" href="{{url('')}}themes/{{theme.name}}/images/favicons/favicon48x48.ico">
    <!-- Always force latest IE rendering engine or request Chrome Frame -->
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">

    {# Jquery#}
    {{ javascript_include('library/jquery/jquery-1.11.2.min.js') }}
    {# css #}
    {{ stylesheet_link('library/sweetalert2/sweetalert2.min.css') }}
    {{ stylesheet_link('library/notifIt/css/notifIt.min.css') }}
    {#{{ stylesheet_link('library/bootstrap-3.3.4/css/bootstrap.min.css') }}#}
    {# base de fontawesome #}
    {{ stylesheet_link('library/font-awesome-4.6.3/css/font-awesome.min.css') }}
    <style>
      .medium-text{
        font-size: 25px;
      }
      .fig-logo-aio{
        margin: -15px 15px 15px 7px;
        padding: 5px;
      }
    </style>
    <script type="text/javascript">
      var baseAio = window.location.protocol+'//'+window.location.host+'{{url()}}';
      {#var controllerName = '{{controllerName}}';#}
    </script>
    {% block header %}<!-- custom header code -->{% endblock %}
    {% block css %}<!-- custom header code -->{% endblock %}
  </head>
  <body style="display: none">
    <div class="container-fluid">
      {% block content %}
        <!-- Aqui va el contenido -->
      {% endblock %}
    </div>
  </body>
</html>
{{ javascript_include('library/notifIt/js/notifIt.min.js') }}
{{ javascript_include('library/sweetalert2/sweetalert2.min.js') }}
{{ javascript_include('library/angular-1.5/js/angular.min.js') }}
{{ javascript_include('library/angular-1.5/js/angular-sanitize.min.js') }}
{{ javascript_include('library/angular-ui-router/angular-ui-router.min.js') }}
{% block js %}
  <!-- Aquí van los archivos JS -->  
{% endblock %}
