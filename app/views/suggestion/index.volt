{% extends "templates/default.volt" %}
{% block header %}
  {# Notifications #}
  {{ partial("partials/slideontop_notification_partial") }}
  <script type="text/javascript">
    $(function () {
      $('#details').tooltip();
    });
  </script>
{% endblock %}
{% block content %}
  <div class="clearfix"></div>
  <div class="space"></div>     
  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
      <div class="title">
        Comentarios y/o Sugerencias
      </div>            
      <hr class="basic-line" />
      <p>
        A través del siguiente formulario usted puede hacer saber al equipo de Sigma Móvil sus comentarios
        y/o sugerencias sobre la plataforma.
      </p>            
    </div>
  </div>
  <div class="space"></div>
  <div class="clearfix"></div>
  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">
      <form action="{{url('suggestion/index')}}" method="post" class="form-horizontal">
        <div class="block block-info">          
          <div class="body">
            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label  class="col-sm-4 ">*Nombre</label>
                <span class="input hoshi input-default  col-sm-8">
                  <input type="text" name="name" class="undeline-input" autofocus="autofocus" required="required">
                </span>
              </div>
            </div>
            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label  class="col-sm-4 ">*Apellido</label>
                <span class="input hoshi input-default  col-sm-8">
                  <input type="text" name="lastname" class="undeline-input" required="required">
                </span>
              </div>
            </div>
            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label  class="col-sm-4 ">*Email</label>
                <span class="input hoshi input-default  col-sm-8">
                  <input type="email" name="email" class="undeline-input" required="required">
                </span>
              </div>
            </div>
            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label  class="col-sm-4 ">*Comentarios y/o Sugerencias</label>
                <span class="input hoshi input-default  col-sm-8">
                  <textarea name="suggestions" class="undeline-input" required="required"></textarea>
                </span>
              </div>
            </div>
          </div>
          <div class="footer" align="right">                        
            <button type="submit" class="button shining btn btn-md success-inverted">Enviar</button>
          </div>
        </div>
      </form>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">                            
      <div class="fill-block fill-block-primary" >
        <div class="header">
          Información
        </div>
        <div class="body">
          <p>
            Recuerde tener en cuenta estas recomendaciones:
          <ul>
            <li>Recuerde que los campos con asterisco(*) son oblogatorios</li>
          </ul>
          </p>
        </div>
        <div class="footer">
          Creación
        </div>
      </div>     
    </div> 
  </div>
{% endblock %}    
