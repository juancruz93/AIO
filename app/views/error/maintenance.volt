{% extends "templates/clean.volt" %}	
{% block header %}
<style>
img {
  max-width: 100%;
  height: auto;
}
</style>
{% endblock %}
{% block content %}
  <div class="container-fluid">
    <figure class="fig-img-bottom">
      <a href="{{url('')}}">
        <img src="{{url('images/general/pagina-en-construccion.jpg')}}" class="img-cohete" title="Regresar">
      </a>
    </figure>
  </div>   
{% endblock %}