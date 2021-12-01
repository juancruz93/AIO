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
    {{ partial("partials/css_notifications_partial") }}
    {{ javascript_include('library/notification-styles/js/modernizr.custom.js') }}
    {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}
    {# Jquery#}
    {{ javascript_include('library/jquery/jquery-1.11.2.min.js') }}
    {# base de bootstrap #}
    {{ stylesheet_link('library/bootstrap-3.3.4/css/bootstrap.min.css') }}
    {# Para cambiar el tema modificar la ruta en el siguiente enlace#}
    {{ stylesheet_link('themes/' ~ theme.name ~ '/css/styles.css') }}
    {{ stylesheet_link('css/adjustments.css') }}
    {# sticky tables #}
    {{ stylesheet_link('library/sticky-table-headers/css/component.min.css') }}
    {# base de fontawesome #}
    {{ stylesheet_link('library/font-awesome-4.6.3/css/font-awesome.min.css') }}
    <script type="text/javascript">
      var myBaseURL = '{{url('')}}';
    </script>
    {% block css %}<!-- custom header code -->{% endblock %}
    {% block header %}<!-- custom header code -->{% endblock %}
  </head>
  <body style="overflow-x: hidden;">

    <!-- nav bar -->        


    <nav class="navbar navbar-default alert-success" role="navigation">
      <div class="container-fluid">
        <div class="navbar-header">
          <a class="navbar-brand" style="display: inline;"  href="{{url('')}}">
            {{theme.logo}}
          </a>
        </div>
      </div>
    </nav>

    <!-- Contenedor principal -->

    <div class="container-fluid" style="margin-bottom: 10%;">
      
      <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">


          <!-- Inicio de contenido -->
          {% block content %}
            <!-- Aqui va el contenido -->
            
            
          {% endblock %}
          <!-- Fin de contenido -->
        </div>
      </div>
      <div class="clearfix"></div>
      <div class="space"></div>         
      <div class="clearfix"></div>
      <div class="space"></div> 
      <div class="clearfix"></div>
      <div class="space"></div> 
      <div class="clearfix"></div>
      <div class="space-responsive"></div> 
      <div class="clearfix"></div>

        

    </div>
       
 



  

    {# Modernizr #}
    {{ javascript_include('library/notification-styles/js/modernizr.custom.js') }}
    {% block footer %} {% endblock %}   
    {# Base JS de bootstrap #}
    {{ javascript_include('library/bootstrap-3.3.4/js/bootstrap.min.js') }}
    {# Sticky Tables #}
    {{ javascript_include('library/sticky-table-headers/js/jquery.ba-throttle-debounce.min.js') }}
    {{ javascript_include('library/sticky-table-headers/js/jquery.stickyheader.min.js') }}
    {# Classie #}
    {{ javascript_include('library/text-input-effects/js/classie.min.js') }}
    {#    {{ javascript_include('library/angular-1.5/js/angular.min.js') }}#}
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.8/angular.min.js"></script>
    {#        <script src="http://ajax.googleapis.com/ajax/libs/angularjs/1.2.18/angular.js"></script>#}
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-sanitize.min.js"></script>
    {{ partial("partials/js_notifications_partial") }}
    {{ partial("partials/slideontop_notification_partial") }}
    {% block js %}<!-- custom js code -->{% endblock %}
  </body>
</html>
