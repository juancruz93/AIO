{% extends "templates/default.volt" %}
{% block content %}
  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12">
      <section class="main">
        <ul class="ch-grid">
          <li onclick="location.href = '{{url('smscategory')}}'">
            <div class="ch-item smscategory pointer-cursor">
              <div class="ch-info"></div>
            </div>
            <b>Categorías</b>
          </li>
          <li onclick="location.href = '{{url('sms')}}'">
            <div class="ch-item sms pointer-cursor">
              <div class="ch-info">
                <h3>Servicio</h3>
                <p><a href="#">¿Qué es esto?</a></p>
              </div>
            </div>
            <b>Sms</b>
          </li>
        </ul>
      </section>
    </div>
  </div>
{% endblock %}