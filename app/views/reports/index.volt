{% extends "templates/default.volt" %}
{% block header %}
    {# Notifications #}
    {{ partial("partials/css_notifications_partial") }}
{% endblock %}

{% block js %}
    {{ partial("partials/js_notifications_partial") }}
    {{ partial("partials/slideontop_notification_partial") }}
    {# Dialogs #}
    {{ stylesheet_link('library/dialog-effects/css/dialog.css') }}
    {{ stylesheet_link('library/dialog-effects/css/dialog-wilma.css') }}
    {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}
{% endblock %}
{% block content %}
  <section class="main">
    {{partial("partials/submenu_partial")}}
    {%if ((user.idUser == 177 or user.idUser == 120) and (user.email == "desarrollo@sigmamovil.com"))%}
      <a href="{{url('report/index#/stadisticsgeneral')}}">Reportes Generales de las campañas</a>
    {%endif%}
  </section>
{% endblock %}
