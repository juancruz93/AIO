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
                Editar Servicio <strong>{{services_value.name}}</strong>
            </div>
        </div>
    </div>
    <hr class="basic-line" />  
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">  
            <form action="{{url('services/edit/')}}{{services_value.idServices}}" class="form-horizontal" method="post" >
                <div class="block block-info ">
                    <div class="body">
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">

                                <div class="form-group">
                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                                        <label  class="col-sm-4 ">*Nombre</label>
                                        <span class="input hoshi input-default  col-sm-8">    
                                            <input name="name" class="undeline-input" value="{{services_value.name}}" required>
                                        </span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                                        <label  class="col-sm-4 ">*Descripción</label>
                                        <span class="input hoshi input-default  col-sm-8">    
                                            <input name="description" class="undeline-input" value="{{services_value.description}}" required>
                                        </span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                                        <label  class="col-sm-4 text-right">Estado</label>
                                        <span class="input hoshi input-default  col-sm-8">    
                                            <input type="checkbox" id="toggle-one" name="status" {% if services_value.status %} checked {% endif %}/>
                                        </span>
                                    </div>
                                </div>

                            </div>
                        </div> 
                    </div>
                    <div class="footer text-right">
                        <button class="button  btn btn-xs-round   round-button success-inverted" data-dialog="confirm" data-toggle="tooltip" data-placement="top" title="Guardar servicio" type="submit"><span class="glyphicon glyphicon-ok"></span></button>
                        <a class="button  btn btn-xs-round   round-button danger-inverted" href="{{url('services/index')}}" data-dialog="confirm" data-toggle="tooltip" data-placement="top" title="Cancelar">
                            <span class="glyphicon glyphicon-remove"></span>
                        </a>
                    </div>
                </div> 
            </form>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">                            
            <div class="fill-block fill-block-primary" >
                <div class="header">
                    Instrucciones
                </div>
                <div class="body">
                    <p>
                        Recuerde tener en cuenta estas recomendaciones:
                    </p>
                    <ul>
                        <li>El campo nombre no debe contener espacios, caracteres especiales o estar vacio.</li>                            
                        <li>El nombre del Servicio debe ser un nombre único, es decir, no pueden existir dos Servicios con el mismo nombre.</li>
                        <li>En la descripción, explique de manera breve las funcionalidades del servicio.</li>
                        <li>Recuerde que los campos con asterisco(*) son oblogatorios</li>                        
                    </ul>
                </div>
                <div class="footer">
                    
                </div>
            </div>     
        </div>
    </div>
{% endblock %}
