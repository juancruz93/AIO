{% extends "templates/register.volt" %}
{% block css %}
  {# Notifications #}
  {{ partial("partials/css_notifications_partial") }}
{% endblock %}

{% block js %}
  {{ partial("partials/js_notifications_partial") }}
  {{ partial("partials/slideontop_notification_partial") }}
{% endblock %}
{% block content %}
  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
      <img src="{{url('themes/default/images/aio.png')}}" class="img-responsive center-block"  style="width: 256px; height: 256px;"/>
    </div>
  </div>
  <br>
  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3"></div>
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 text-center">
      <p class="text-2em">
        Gracias por registrarte, hemos enviado un correo de verificación a la siguiente dirección <b>{{email}}</b>
      </p>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3"></div>
  </div>
{% endblock %}
