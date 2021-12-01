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
  </section>

  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
      <div class="title">
        Especifique su habeas Data
      </div>            
      <hr class="basic-line">
      <p>
        El habeas data es una extensión del derecho a la intimidad y le permite al ciudadano conocer, actualizar 
        y rectificar las informaciones que se hayan recogido sobre él en bancos de datos y en archivos de entidades 
        públicas y privadas, ya sea mediante medios automatizados o manuales.
      </p>            
    </div>
  </div>

<div class="col-xs-12 col-sm-12 col-md-8 col-lg-6 wrap">
  <div class="form-group">                            
    <div class="fill-block fill-block-info">
      <div class="header">
        Habeas Data
      </div>
      <div class="body">
        
{#        <form action="" method="post">#}
        <form action="#/" class="form-horizontal" method="post" >
          {{form.render('habeasData')}}
          <br>
          <div class="text-right">
            <input value="Guardar" type="submit" class="btn btn-success "/>
          </div>
        </form>
        
      </div>
      {#<div class="footer">
          Creación
      </div>#}
    </div>     
  </div>
</div>
    
<div class="col-xs-12 col-sm-12 col-md-8 col-lg-6 wrap">
  <div class="form-group">                            
    <div class="fill-block fill-block-info" >
      <div class="header">
        Información
      </div>
      <div class="body">
        <p>
          Recuerde tener en cuenta estas recomendaciones:
        <ul>
          <li>Este es el texto o parrafo de habeas data que aparecera cuando necesites especificar en un formulario.</li>
          <li>Esta especificacion de habeas data aparecera en la parate inferior del formulario al momento de hacer uso del servicio de formularios.</li>
          <li>Es permitido el ingreso de un texto de hasta 1000 catacteres para la especificacion que coloques</li>
        </ul>
        </p>
      </div>
      {#<div class="footer">
          Creación
      </div>#}
    </div>     
  </div>
</div>
</div>
{% endblock %}  
