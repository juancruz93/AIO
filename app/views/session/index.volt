{% extends "templates/clean.volt" %}
{% block css %}
  {# Notifications #}
  {{ partial("partials/css_notifications_partial") }}
  <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/angular_material/1.1.0/angular-material.min.css">
  {{ stylesheet_link('library/ui-select-master/dist/select.css') }}
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/3.4.5/select2.css">    
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.8.5/css/selectize.default.min.css">
  {{ stylesheet_link('library/select2-4.0.0/css/select2.min.css') }}
  {{ stylesheet_link('library/select2/css/select2-bootstrap.min.css') }}
  <style>
    .icons-auth{
      width: 16px;
      height: 16px;
    }
  </style>
{% endblock %}

{% block js %}
  {{ partial("partials/js_notifications_partial") }}
  {{ partial("partials/slideontop_notification_partial") }}
  {{ javascript_include('library/angular-ui-router/angular-ui-router.min.js') }}
  {{ javascript_include('js/angular/session/app.js') }}
  {{ javascript_include('js/angular/session/controllers.js') }}
  {{ javascript_include('js/angular/session/services.js') }}
  {{ javascript_include('library/angular-material-1.1.0/js/angular-animate.min.js') }}
  {{ javascript_include('library/angular-material-1.1.0/js/angular-aria.min.js') }}
  {{ javascript_include('library/angular-material-1.1.0/js/angular-messages.min.js') }}
  {#  {{ javascript_include('library/angular-material-1.1.0/js/angular-material.min.js') }}#}
  <script src="//ajax.googleapis.com/ajax/libs/angular_material/1.1.0/angular-material.min.js"></script>
  {{ javascript_include('library/ui-select-master/dist/select.min.js') }}
  {{ javascript_include('library/select2/js/select2.min.js') }}
{% endblock %}

{% block content %} 


  <div class="container-fluid screen-complete">

    <div class="row screen-complete">
      <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 complete-height astronaut-container">
        <div class="site-wrapper">

          {#
          <div class="site-wrapper-inner">
              <div class="center-container">
                  <div class="session-container">
                      <img src="{{url('')}}images/aio/aio-moon.png" class="astronaut-session">
                  </div>
              </div>
          </div>
          #}

          <img src="{{url('')}}images/aio/aio-half-moon-planets.png" class="astronaut-session" style="position: absolute;bottom: 0px; width: 950px; right: 0px;">

        </div>
      </div>
      <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 complete-height">
        <div class="site-wrapper">
          <div class="site-wrapper-inner">
            <div class="center-container" style="margin-top: 30px;">
              <div class="session-container">
                <div class="row">
                  <div class="col-md-12">
                    <img class="session-logo" src="{{url('')}}themes/{{theme.name}}/images/aio.png" />
                        
                    <div ui-view></div>
                  </div>
                </div>

             

                <div class="row">
                  <div class="col-md-12 text-center">
                    <br />
                    <img src="{{url('')}}images/img_footer_color.png" style="width: 70px;">
                    <br />
                    <span class="extra-small-text">
                      ©{{ date('Y', time()) }} All Rights Reserved. AIO is a registered trademark of 
                      <a href="https://sigmamovil.com/" target="_blank">Sigma Móvil</a>
                    </span>
                  </div>
                </div>
                <div class="row" id="no_chrome" style="display: none">
                  <div style="margin-bottom: 0px; margin-top: 5px;" class="alert alert-info" role="alert"> 
                    <img style="width: 20px;" src="{{ urlManager.get_base_uri(true) }}images/google_chrome.png">
                    Se recomienda el uso de Google Chrome para una mejor experiencia
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
{% endblock %}
{% block footer %}
  <script type="text/javascript">
var relativeUrlBase = "{{ urlManager.get_base_uri() }}";
var fullUrlBase = "{{ urlManager.get_base_uri(true) }}";
var templateBase = "session";
  </script>
  <script type="text/javascript">
    var es_chrome = navigator.userAgent.toLowerCase().indexOf('chrome') > -1;
    if (!es_chrome) {
    {#      alert("El navegador que se está utilizando es Chrome");#}
        document.getElementById("no_chrome").style.display = "block";
      }
  </script>

{% endblock %}
