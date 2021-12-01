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

    {# Select 2 #}
    {{ stylesheet_link('library/select2-4.0.0/css/select2.css') }}
    {{ javascript_include('library/select2-4.0.0/js/select2.min.js') }}
    
    <script>
        $(function() {            
            $(".select2").select2();
        });
    </script>

{% endblock %}
    
{% block content %}    
    <div class="clearfix"></div>
    <div class="space"></div>

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
            <div class="title">
                Creación de una nueva Clasificación
            </div>            
            <hr class="basic-line" />            
        </div>
    </div>
    
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8 wrap">
            <form action="{{url('accountclassification/create')}}" method="post">
                <div class="block block-info">
                    <div class="body">
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <span class="input hoshi input-default">                                        
                                            {{account_form.render('name')}}
                                            <label class="input-label label-hoshi hoshi-default">
                                                <span class="input-label-content label-content-hoshi">*Nombre:</span>
                                            </label>
                                        </span>
                                     </div>       
                                </div>

                                <div class="form-group">
                                    <div class="col-md-12">
                                        <span class="input hoshi input-default input-filled">
                                            {{account_form.render('idMta')}}
                                            <label class="input-label label-hoshi hoshi-default" for="input-90">
                                                <span class="input-label-content label-content-hoshi">*Mta:</span>
                                            </label>
                                        </span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-12">
                                        <span class="input hoshi input-default input-filled">
                                            {{account_form.render('idAdapter')}}
                                            <label class="input-label label-hoshi hoshi-default" for="input-91">
                                                <span class="input-label-content label-content-hoshi">*Adaptador:</span>
                                            </label>
                                        </span>
                                    </div>
                                </div>
                                    
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <span class="input hoshi input-default input-filled">
                                            {{account_form.render('idUrldomain')}}
                                            <label class="input-label label-hoshi hoshi-default" for="input-92">
                                                <span class="input-label-content label-content-hoshi">*Urldomain:</span>
                                            </label>
                                        </span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-12">
                                        <span class="input hoshi input-default input-filled">                                        
                                            {{account_form.render('idMailClass')}}
                                            <label class="input-label label-hoshi hoshi-default" for="input-93">
                                                <span class="input-label-content label-content-hoshi">*Mail Class:</span>
                                            </label>
                                        </span>
                                    </div>        
                                </div>

                                <div class="form-group">
                                    <div class="col-md-12">
                                        <span class="input hoshi input-default">                                        
                                            {{account_form.render('fileSpace')}}
                                            <label class="input-label label-hoshi hoshi-default">
                                                <span class="input-label-content label-content-hoshi">*Espacio disponible en disco (MB):</span>
                                            </label>
                                        </span>
                                    </div>        
                                </div>

                                <div class="form-group">
                                    <div class="col-md-12">
                                        <span class="input hoshi input-default">                                        
                                            {{account_form.render('mailLimit')}}
                                            <label class="input-label label-hoshi hoshi-default">
                                                <span class="input-label-content label-content-hoshi">*Limite de correos:</span>
                                            </label>
                                        </span>
                                    </div>        
                                </div>
                            </div>                           

                            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">                                
                                <div class="form-group">
                                    <div class="col-md-12 ">
                                        <span class="input hoshi input-default">                                        
                                            {{account_form.render('contactLimit')}}
                                            <label class="input-label label-hoshi hoshi-default">
                                                <span class="input-label-content label-content-hoshi">*Limite de contactos:</span>
                                            </label>
                                        </span>
                                    </div>        
                                </div>

                                <div class="form-group">
                                    <div class="col-md-12 ">
                                        <span class="input hoshi input-default">                                        
                                            {{account_form.render('smsLimit')}}
                                            <label class="input-label label-hoshi hoshi-default">
                                                <span class="input-label-content label-content-hoshi">*Limite de SMS:</span>
                                            </label>
                                        </span>
                                    </div>        
                                </div>

                                <div class="form-group">
                                    <div class="col-md-12 ">
                                        <span class="input hoshi input-default">                                        
                                            {{account_form.render('smsSpeed')}}
                                            <label class="input-label label-hoshi hoshi-default">
                                                <span class="input-label-content label-content-hoshi">*Capacidad de envío por segundo:</span>
                                            </label>
                                        </span>
                                    </div>        
                                </div>

                                <div class="form-group">
                                    <div class="col-md-12 ">
                                        <span class="input hoshi input-default input-filled">                                        
                                            {{account_form.render('senderAllowed', {'id':'input-94', 'class': 'input-field input-hoshi select2', 'required': 'required'})}}
                                            <label class="input-label label-hoshi hoshi-default">
                                                <span class="input-label-content label-content-hoshi">*¿Permitir al usuario agregar mas remitentes?:</span>
                                            </label>
                                        </span>
                                    </div>        
                                </div>

                                <div class="form-group">
                                    <div class="col-md-12 ">
                                        <span class="input hoshi input-default input-filled">                                        
                                            {{account_form.render('footerEditable', {'id':'input-95', 'class': 'input-field input-hoshi select2', 'required': 'required'})}}
                                            <label class="input-label label-hoshi hoshi-default" for="input-95">
                                                <span class="input-label-content label-content-hoshi">*Footer editable:</span>
                                            </label>
                                        </span>
                                    </div>        
                                </div>

                                <div class="form-group">
                                    <div class="col-md-12 ">
                                        <div class="col-md-4 ">
                                            <span class="input-default">
                                                <label class="hoshi-default">
                                                    <span class="input-label-content label-content-hoshi">*Fecha de expiración:</span>
                                                </label>
                                            </span>
                                        </div>    
                                        <div class="col-md-4 ">
                                            <span class="input hoshi input-default">                                        
                                                {{account_form.render('cant', {'class': 'input-field input-hoshi input-filled', 'required': 'required'})}}
                                                <label class="input-label label-hoshi hoshi-default"></label>
                                            </span>
                                        </div>    
                                        <div class="col-md-4 ">
                                            <span class="input hoshi input-default">                                        
                                                {{account_form.render('date', {'class': 'input-field input-hoshi', 'required': 'required'})}}
                                                <label class="input-label label-hoshi hoshi-default"></label>
                                            </span>
                                        </div>    
                                    </div>        
                                </div>
                            </div>                                    
                        </div>
                    </div>                        
                    <div class="footer" align="right">                        
                        <a href="{{url('accountclassification/index')}}" class="button shining btn btn-xs-round shining shining-round round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Cancelar">
                            <span class="glyphicon glyphicon-remove"></span>
                        </a>
                        <button class="button shining btn btn-xs-round shining shining-round round-button success-inverted" data-toggle="tooltip" data-placement="top" title="Guardar">
                            <span class="glyphicon glyphicon-ok"></span>
                        </button>
                    </div>                        
                </div>
            </form>
        </div>
   
        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4 wrap">                            
            <div class="fill-block fill-block-primary" >
                <div class="header">
                    Información
                </div>
                <div class="body">
                    <p>
                        Recuerde tener en cuenta estas recomendaciones:
                        <ul>
                            <li>El campo nombre no debe contener espacios, caracteres especiales o estar vacio.</li>                            
                            <li>El nombre de la clasificación de la cuenta debe ser un nombre único, es decir, no pueden existir dos clasificaciones con el mismo nombre.</li>                            
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
