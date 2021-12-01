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
    {# Dialogs #}
    {{ stylesheet_link('library/dialog-effects/css/dialog.css') }}
    {{ stylesheet_link('library/dialog-effects/css/dialog-wilma.css') }}
    {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}
{% endblock %}
{% block content %}
    <div class="clearfix"></div>
    <div class="space"></div>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
            <div class="title">
                Nuevo Servicio
            </div>
            <hr class="basic-line" />    
            <p>
                Recuerde que los campos con asterisco(*) son oblogatorios
            </p>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">  
            <form action="{{url('services/create')}}" class="form-horizontal" method="post" >      
                <div class="block block-info ">
                    <div class="body">
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">

                                <div class="form-group">
                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                                        <label  class="col-sm-4 text-right">*Nombre</label>
                                        <span class="input hoshi input-default  col-sm-8">     
                                            {{services_form.render('name')}}
                                        </span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                                        <label  class="col-sm-4 text-right">*Descripción</label>
                                        <span class="input hoshi input-default  col-sm-8">     
                                            {{services_form.render('description')}}
                                        </span>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="footer text-right">
                        <button class="button  btn btn-xs-round   round-button success-inverted" data-dialog="confirm" data-toggle="tooltip" data-placement="top" title="Guardar adaptador" type="submit"><span class="glyphicon glyphicon-ok"></span></button>
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
                    </p><ul>
                        <li>El campo nombre no debe contener espacios, caracteres especiales o estar vacio.</li>                            
                        <li>El nombre del Servicio debe ser un nombre único, es decir, no pueden existir dos Servicios con el mismo nombre.</li>                            
                        <li>En la descripción, explique de manera breve las funcionalidades del servicio.</li>s
                        <li>Recuerde que los campos con asterisco(*) son oblogatorios</li>                        
                    </ul>
                    <p></p>
                </div>
                <div class="footer">
                    
                </div>
            </div>     
        </div>
    </div>
{% endblock %}
