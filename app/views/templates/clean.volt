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
    {{ stylesheet_link('css/cover.min.css') }}
    {{ stylesheet_link('css/adjustments.min.css') }}
    {# sticky tables #}
    {{ stylesheet_link('library/sticky-table-headers/css/component.min.css') }}
    {# base de fontawesome #}
    {{ stylesheet_link('library/font-awesome-4.6.3/css/font-awesome.min.css') }}
    <script type="text/javascript">
      var myBaseURL = '{{url('')}}';
    </script>
    {% block header %}<!-- custom header code -->{% endblock %}
    {% block css %}<!-- custom header code -->{% endblock %}
  </head>
  <body>

    {% block content %}
      <!-- Aqui va el contenido -->
    {% endblock %}

    <div class="clearfix"></div>
    <div class="space"></div>

    <div class="menu-footer">
     {# <div class="social-network">
        <a href="https://es-es.facebook.com/SigmaMovil" target="_blank" data-toggle="tooltip" data-placement="top" title="Síguenos en facebook">
          <img src="{{url('')}}themes/{{theme.name}}/images/social-networks/facebook-icon.png" />
        </a>
        <a href="https://twitter.com/SigmaMovil" target="_blank" data-toggle="tooltip" data-placement="top" title="Síguenos en twitter">
          <img src="{{url('')}}themes/{{theme.name}}/images/social-networks/twitter-icon.png" />
        </a>
        <a href="https://www.youtube.com/channel/UCC_-Dd4-718gwoCPux8AtwQ" target="_blank" data-toggle="tooltip" data-placement="top" title="Síguenos en youtube">
          <img src="{{url('')}}themes/{{theme.name}}/images/social-networks/youtube-icon.png" />
        </a>
        <a href="https://plus.google.com/+Sigmamovil/posts" target="_blank" >
          <img src="{{url('')}}themes/{{theme.name}}/images/social-networks/google-plus-icon.png" data-toggle="tooltip" data-placement="top" title="Síguenos en google plus"/>
        </a>
        <a href="https://www.linkedin.com/company/sigma-m-vil-s.a." target="_blank" data-toggle="tooltip" data-placement="top" title="Síguenos en linkedin">
          <img src="{{url('')}}themes/{{theme.name}}/images/social-networks/linkedin-icon.png" />
        </a>
      </div>#}
      {#<div class="copy">
        {{theme.footer}}
      </div>#}
    </div>
    {% block footer %} {% endblock %}   
    {# Bootstrap #}    
    {{ javascript_include('library/bootstrap-3.3.4/js/bootstrap.min.js') }}
    {# Modernizr #}
    {{ javascript_include('library/notification-styles/js/modernizr.custom.js') }}
    {# Classie #}
    {{ javascript_include('library/text-input-effects/js/classie.min.js') }}
    {# Sticky Tables #}
    {{ javascript_include('library/sticky-table-headers/js/jquery.ba-throttle-debounce.min.js') }}
    {{ javascript_include('library/sticky-table-headers/js/jquery.stickyheader.min.js') }}
    {{ partial("partials/js_notifications_partial") }}
    {{ partial("partials/slideontop_notification_partial") }}
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.8/angular.min.js"></script>
    {#        <script src="http://ajax.googleapis.com/ajax/libs/angularjs/1.2.18/angular.js"></script>#}
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-sanitize.min.js"></script>
    {% block js %}<!-- custom header code -->{% endblock %}
  </body>
</html>
