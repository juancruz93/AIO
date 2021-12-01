{% extends "templates/default.volt" %}
{% block css %}
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
    {#<ul class="ch-grid">
      {% if user.usertype.idAllied is  defined %}
        <li onclick="location.href = '{{url('account')}}';">
          <div class="ch-item account pointer-cursor">
            <div class="ch-info">
              <h3>Cuentas</h3>
            </div>
          </div>
          Cuentas de usuario
        </li>
      {% endif %}
      {% if user.usertype.idAccount is  defined %}
        <li onclick="location.href = '{{url('subaccount/index/'~ user.usertype.idAccount)}}';">
          <div class="ch-item  pointer-cursor">
            <div class="ch-info">
              <h4>Subcuentas</h4>
            </div>
          </div>
          Subcuentas
        </li>
      {% endif %}
      <li onclick="location.href = '{{url('user')}}';">
        <div class="ch-item user pointer-cursor">
          <div class="ch-info">
            <h3>Usuarios</h3>
          </div>
        </div>
        Usuarios
      </li>
      <li onclick="location.href = '{{url('permissionsystem#/roles')}}';">
        <div class="ch-item security pointer-cursor">
          <div class="ch-info">
            <h3>Seguridad</h3>
          </div>
        </div>
        Permisos de usuario
      </li>
      <li onclick="location.href = '{{url('flashmessage')}}';">
        <div class="ch-item flash-message pointer-cursor">
          <div class="ch-info">
            <h4>Flash messages</h4>
          </div>
        </div>
        Mensajes Flash
      </li>
      <li onclick="location.href = '{{url('process')}}';">
        <div class="ch-item schedule pointer-cursor">
          <div class="ch-info">
            <h4>Programación global</h4>
          </div>
        </div>
        Programación global
      </li>
      <li onclick="location.href = '{{url('process')}}';">
        <div class="ch-item social-networks pointer-cursor">
          <div class="ch-info">
            <h4>Redes sociales</h4>
          </div>
        </div>
        Redes sociales
      </li>
      <li onclick="location.href = '{{url('apikey')}}';">
        <div class="ch-item api-keys pointer-cursor">
          <div class="ch-info">
            <h4>API Keys</h4>
          </div>
        </div>
        API Keys
      </li>
      <li onclick="location.href = '{{url('footer')}}';">
        <div class="ch-item footers pointer-cursor">
          <div class="ch-info">
            <h4>Footers</h4>
          </div>
        </div>
        Footers
      </li>
      <li onclick="location.href = '{{url('process')}}';">
        <div class="ch-item smart pointer-cursor">
          <div class="ch-info">
            <h4>Gestión inteligente</h4>
          </div>
        </div>
        Gestión inteligente
      </li><li onclick="location.href = '{{url('process')}}';">
        <div class="ch-item cloud-computing pointer-cursor">
          <div class="ch-info">
            <h4>Procesos</h4>
          </div>
        </div>
        Procesos
      </li>
      <li onclick="location.href = '{{url('accountclassification')}}';">
        <div class="ch-item classification pointer-cursor">
          <div class="ch-info">
            <h4>Clasificación de cuentas</h4>
          </div>
        </div>
        Clasificación de cuentas
      </li>
      <li onclick="location.href = '{{url('systemmail')}}';">
        <div class="ch-item system-mail pointer-cursor">
          <div class="ch-info">
            <h4>Correos del sistema</h4>
          </div>
        </div>
        Correos del sistema
      </li>
      <li onclick="location.href = '{{url('mailtemplate#/')}}';">
        <div class="ch-item mail-template pointer-cursor">
          <div class="ch-info">
            <h4>Plantillas de correo</h4>
          </div>
        </div>
        Plantillas de correo
      </li>
      <li onclick="location.href = '{{url('smstemplate#/')}}';">
        <div class="ch-item sms-template pointer-cursor">
          <div class="ch-info">
            <h4>Plantillas de SMS</h4>
          </div>
        </div>
        Plantillas de SMS
      </li>
      <li onclick="location.href = '{{url('gallery')}}';">
        <div class="ch-item gallery pointer-cursor">
          <div class="ch-info">
            <h4>Galería de archivos</h4>
          </div>
        </div>
        Galería de archivos
      </li>
      <li onclick="location.href = '{{url('mail_structure')}}';">
        <div class="ch-item  pointer-cursor">
          <div class="ch-info">
            <h4>Estructuras predeterminadas</h4>
          </div>
        </div>
        Estructuras predeterminadas
      </li>
    </ul>#}
  </section>
{% endblock %}    
