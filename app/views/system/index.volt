{% extends "templates/default.volt" %}
{% block header %}
  {# Notifications #}
  {{ partial("partials/slideontop_notification_partial") }}
{% endblock %} 
{% block content %}
  <section class="main">
    <ul class="ch-grid">
      {{partial("partials/submenu_partial")}}
      {#<li onclick="location.href = '{{url('mta')}}';">
        <div class="ch-item mta pointer-cursor">
          <div class="ch-info">
            <h3>MTA's</h3>
          </div>
        </div>
        MTA's
      </li>
      <li onclick="location.href = '{{url('adapter')}}';">
        <div class="ch-item adapter pointer-cursor">
          <div class="ch-info">
            <h3>Adaptadores</h3>
          </div>
        </div>
        Adaptadores
      </li>
      <li onclick="location.href = '{{url('urldomain')}}';">
        <div class="ch-item url-domain pointer-cursor">
          <div class="ch-info">
            <h3>URL's</h3>
          </div>
        </div>
        URL's
      </li>
      <li onclick="location.href = '{{url('services')}}';">
        <div class="ch-item platform pointer-cursor">
          <div class="ch-info">
            <h3>Servicios</h3>
          </div>
        </div>
        Servicios
      </li>
      <li onclick="location.href = '{{url('mailclass')}}';">
        <div class="ch-item mail-class pointer-cursor">
          <div class="ch-info">
            <h3>Mail Classes</h3>
          </div>
        </div>
        Mail Classes
      </li>
      <li onclick="location.href = '{{url('plantillas')}}';">
        <div class="ch-item plantillas pointer-cursor">
          <div class="ch-info">
            <h3>Plantillas</h3>
          </div>
        </div>
        Plantillas
      </li>
      <li onclick="location.href = '{{url('process')}}';">
        <div class="ch-item process pointer-cursor">
          <div class="ch-info">
            <h3>Procesos</h3>
          </div>
        </div>
        Procesos en background
      </li>
      {% if user.idRole is defined AND  user.idRole == -1 %}
        <li onclick="location.href = '{{url('masteraccount')}}';">
          <div class="ch-item account pointer-cursor">
            <div class="ch-info">
              <h3>Cuentas maestras</h3>
            </div>
          </div>
          Cuentas maestras
        </li>
      {% endif %}
      <li onclick="location.href = '{{url('account/index')}}';">
        <div class="ch-item client pointer-cursor">
          <div class="ch-info">
            <h3>Mis clientes</h3>
          </div>
        </div>
        Mis clientes
      </li>
      <li onclick="location.href = '{{url('masteraccount/myconfigedit')}}';">
        <div class="ch-item config pointer-cursor">
          <div class="ch-info">
            <h3>Editar mi configuración</h3>
          </div>
        </div>
        Editar mi configuración
      </li>
      {% if user.usertype.idMasteraccount IS  defined AND user.idRole != -1%}
        <li onclick="location.href = '{{url('masteraccount/aliaslist/' ~ user.usertype.idMasteraccount)}}';">
          <div class="ch-item allied pointer-cursor">
            <div class="ch-info">
              <h3>Aliados</h3>
            </div>
          </div>
          Aliados
        </li>
      {% endif %}
    </ul>#}
  </section>
{% endblock %}    
