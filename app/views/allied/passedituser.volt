{% extends "templates/default.volt" %}
{% block css %}
  {# Notifications #}
  {{ partial("partials/css_notifications_partial") }}
{% endblock %}

{% block js %}
  {{ partial("partials/js_notifications_partial") }}
  {{ partial("partials/slideontop_notification_partial") }}
{% endblock %}
{% block header %}
  {# Notifications #}
  {{ partial("partials/slideontop_notification_partial") }}
{% endblock %}

{% block content %}    
  <div class="clearfix"></div>
  <div class="space"></div>

  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
      <div class="title">
        Edición de la contraseña del Usuario
      </div>            
      <hr class="basic-line" />            
    </div>
  </div>

  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap"> 
      <form action="{{url('allied/passedituser')}}/{{(userE.idUser)}}/{{(idAllied)}}" method="post" class="form-horizontal">
        <div class="block block-info">
          <div class="body">

            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label class="col-sm-4 text-right">*Contraseña:</label>
                <span class="input hoshi input-default col-sm-8">         
                  <input class="undeline-input form-control" type="password" min="8" autofocus name="pass1" />
                </span>
              </div>
            </div>
            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label class="col-sm-4 text-right">*Repita la contraseña:</label>
                <span class="input hoshi input-default col-sm-8">         
                  <input class="undeline-input form-control" type="password" min="8" name="pass2" />
                </span>
              </div>
            </div>

          </div>

          <div class="footer" align="right">
              <button class="button shining btn btn-xs-round shining shining-round round-button success-inverted" data-toggle="tooltip" data-placement="top" title="Guardar">
              <span class="glyphicon glyphicon-ok"></span>
            </button>
            <a href="{{url('allied/listuser/'~ idAllied)}}" class="button shining btn btn-xs-round shining shining-round round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Cancelar">
              <span class="glyphicon glyphicon-remove"></span>
            </a>
            
          </div>
        </div>
      </form>
    </div>

    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">                            
      <div class="fill-block fill-block-info" >
        <div class="header">
          Información
        </div>
        <div class="body">
          <p>
            Recuerde tener en cuenta estas recomendaciones:
          <ul>
            <li>La contraseña debe tener mínimo 8 caracteres</li>
            <li>Las contraseñas deben coincidir</li>
            <li>Los campos con asterisco(*) son obligatorios.</li>
          </ul> 
          </p>
        </div>
        <div class="footer">
          Edición
        </div>
      </div>     
    </div>            
  </div>   
{% endblock %}    
