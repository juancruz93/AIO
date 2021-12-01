{% extends "templates/clean.volt" %}
{% block header %}
  {# Modernizr #}
  {{ javascript_include('library/notification-styles/js/modernizr.custom.js') }}
  {# Notifications #}
  {{ partial("partials/notifications_partial") }}
  {{ partial("partials/slideontop_notification_partial") }}           

{% endblock %}
{% block content %}    
  <div class="clearfix"></div>
  <div class="space"></div>

  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
      <div class="title">
        Cambiar la contraseña del Usuario
      </div>            
      <hr class="basic-line" />            
    </div>
  </div>

  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap"> 
      <form action="{{url('session/setnewpass')}}" method="post" class="form-horizontal">
        <input type="hidden" name="uniq" value="{{uniq}}"/>
        <div class="block block-info">
          <div class="body">




            <div class="form-group">
              <div class="col-md-9 col-md-offset-1">
                <span class="input hoshi input-default input-filled col-md-5" style="text-align: right;">
                  <label class="input-label label-hoshi hoshi-default">
                    <span class="input-label-content label-content-hoshi">*Contraseña:</span>
                  </label>
                </span>
                <span class="input hoshi input-default input-filled col-md-7">
                  <input class="form-control" type="password" min="8" autofocus name="pass1" />
                </span>
              </div>       
            </div>

            <div class="form-group">
              <div class="col-md-9 col-md-offset-1">
                <span class="input hoshi input-default input-filled col-md-5" style="text-align: right;">                                        
                  <label class="input-label label-hoshi hoshi-default">
                    <span class="input-label-content label-content-hoshi">*Repita la contraseña:</span>
                  </label>
                </span>
                <span class="input hoshi input-default input-filled col-md-7">                                        
                  <input class="form-control" type="password" min="8" name="pass2" />
                </span>
              </div>       
            </div>
          </div>

          <div class="footer" align="right">
            <a href="{{url('session')}}" class="button shining btn btn-xs-round shining shining-round round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Cancelar">
              <span class="glyphicon glyphicon-remove"></span>
            </a>
            <button class="button shining btn btn-xs-round shining shining-round round-button success-inverted" data-toggle="tooltip" data-placement="top" title="Cambiar contraseña">
              <span class="glyphicon glyphicon-ok"></span>
            </button>
          </div>
        </div>
      </form>
    </div>

    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap text-left">                            
      <div class="fill-block fill-block-info" >
        <div class="header">
          Información
        </div>
        <div class="body">
          <p>
            Recuerde tener en cuenta estas recomendaciones:
          </p>
          <ul>
            <li>La contraseña debe tener mínimo 8 caracteres.</li>
            <li>Las contraseñas deben coincidir.</li>
            <li>Los campos con asterisco(*) son obligatorios.</li>
          </ul>
        </div>
        <div class="footer text-right">
          Recuperar
        </div>
      </div>     
    </div>            
  </div>

{% endblock %}   
