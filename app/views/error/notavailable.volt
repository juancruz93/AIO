{% extends "templates/errors.volt" %}
{% block content %}
  <div class="container-fluid">
    <h1 class="title">503</h1>
    <p class="desc">No disponible</p>
    <figure class="fig-img-bottom">
      <a href="{{url('')}}">
        <img src="{{url('images/aio/cohete.png')}}" class="img-cohete" title="Regresar">
      </a>
    </figure>
  </div>   
{% endblock %}