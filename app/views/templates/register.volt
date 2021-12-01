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
    {#    {{ get_title() }}#}
    {{ partial("partials/css_notifications_partial") }}
    {# Jquery#}
    {{ javascript_include('library/jquery/jquery-1.11.2.min.js') }}
    {# base de bootstrap#}
    {{ stylesheet_link('library/bootstrap-3.3.4/css/bootstrap.min.css') }}
    {# Para cambiar el tema modificar la ruta en el siguiente enlace#}
    {{ stylesheet_link('themes/' ~ theme.name ~ '/css/styles.css') }}
    {#{{ stylesheet_link('css/adjustments.css') }}#}
    {{ stylesheet_link('css/RegisterStyles.css') }}

    {# base de fontawesome #}
    {{ stylesheet_link('library/font-awesome-4.6.3/css/font-awesome.min.css') }}
    <script type="text/javascript">
      var myBaseURL = '{{url('')}}';
    </script>
    <style>
      {#.row{
        margin-left: -15px;
        margin-right: 0px;
      }#}
    </style>
    {% block header %}<!-- custom header code -->{% endblock %}
    {% block css %}<!-- custom header code -->{% endblock %}
  </head>
  <body>
    <div id="header">
      <div class="contentHeader">
        <figure>
          <img src="{{url('images/aio.png')}}" class="img-responsive logo-head" />
        </figure>
      </div>
    </div>
    <div class="container">
      {% block content %}
        <!-- Aqui va el contenido -->
      {% endblock %}
    </div>
    <div id="footer">
      <div class="contentFooter">
        <figure class="logo-footer">
          <img src="{{url('images/sigma-logo.png')}}" class="img-responsive pull-right" style="width: 13%" />
        </figure>
      </div>
    </div>
    {% block footer %} {% endblock %}   
    {# Bootstrap #}    
    {{ javascript_include('library/bootstrap-3.3.4/js/bootstrap.min.js') }}
    {# Modernizr #}
    {{ javascript_include('library/notification-styles/js/modernizr.custom.js') }}
    {# Classie #}
    {{ javascript_include('library/text-input-effects/js/classie.min.js') }}
    {{ partial("partials/js_notifications_partial") }}
    {{ partial("partials/slideontop_notification_partial") }}

    {# Angular 1.6.4 #}
    {{ javascript_include("library/angular-1.5/js/angular.min.js") }}
    {{ javascript_include("library/angular-1.5/js/angular-sanitize.min.js") }}
    {% block js %}<!-- custom header code -->{% endblock %}
  </body>
</html>
