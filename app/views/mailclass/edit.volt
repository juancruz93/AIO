{% extends "templates/default.volt" %}
{% block css %}
    {{ stylesheet_link('library/bootstrap-toggle-master/css/bootstrap-toggle.min.css') }}
{% endblock %}
{% block js %}
    {{ javascript_include('library/bootstrap-toggle-master/js/bootstrap-toggle.min.js') }}
{% endblock %}
{% block header %}
  {# Notifications #}
  {{ partial("partials/slideontop_notification_partial") }}
    <script>
        $(function () {
            $('#toggle-one').bootstrapToggle({
                on: 'On',
                off: 'Off',
                onstyle: 'success',
                offstyle: 'danger',
                size: 'small'
            });
        });
    </script>
{% endblock %}
{% block content %}
  <div class="clearfix"></div>
  <div class="space"></div>
  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
      <div class="title">
        Editar Mail Class <strong>{{mailclass_value.name}}</strong>
      </div>
    </div>
  </div>
  <hr class="basic-line" />  
  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">  
      <form action="{{url('mailclass/edit/')}}{{mailclass_value.idMailClass}}" class="form-horizontal" method="post" >      
        <div class="block block-info ">
          <div class="body">
            <div class="row">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">

                <div class="form-group">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <label  class="col-sm-4 ">*Nombre</label>
                    <span class="input hoshi input-default  col-sm-8">   
                      {{formMailclass.render('name', {'class': 'undeline-input'} )}}
                    </span>
                  </div>
                </div>

                <div class="form-group">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <label  class="col-sm-4 ">*Descripción</label>
                    <span class="input hoshi input-default  col-sm-8">   
                      {{formMailclass.render('description', {'class': 'undeline-input'} )}}
                    </span>
                  </div>
                </div>

                <div class="form-group">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <label  class="col-sm-4 ">*Estado</label>
                    <span class="input hoshi input-default  col-sm-8">
                      {{formMailclass.render('status', {'id': 'toggle-one'} )}}
                    </span>
                  </div>
                </div>

              </div>
            </div> 
          </div>
          <div class="footer text-right">
            <button class="button  btn btn-xs-round   round-button success-inverted" data-dialog="confirm" data-toggle="tooltip" data-placement="top" title="Guardar adaptador" type="submit"><span class="glyphicon glyphicon-ok"></span></button>
            <a class="button  btn btn-xs-round   round-button danger-inverted" href="{{url('mailclass/index')}}" data-dialog="confirm" data-toggle="tooltip" data-placement="top" title="Cancelar">
              <span class="glyphicon glyphicon-remove"></span>
            </a>
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
          </p><ul>
            <li>El campo nombre no debe contener espacios, caracteres especiales o estar vacio.</li>                            
            <li>El nombre de la Mail Class debe ser un nombre único, es decir, no pueden existir dos Mail Classes con el mismo nombre.</li>
            <li>La descripción de la Mail Class debe describir para que sirve dicha Mail Class.</li>
            <li>Recuerde que los campos con asterisco(*) son oblogatorios</li>                        
          </ul>
          <p></p>
        </div>
        <div class="footer">
          Edición
        </div>
      </div>     
    </div>
  </div>
{% endblock %}
