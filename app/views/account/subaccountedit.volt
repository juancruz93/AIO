{% extends "templates/default.volt" %}
{% block header %}
    {# Notifications #}
    {{ partial("partials/notifications_partial") }}
    {{ partial("partials/slideontop_notification_partial") }}

    {{ stylesheet_link('library/bootstrap-toggle-master/css/bootstrap-toggle.min.css') }}
    {{ javascript_include('library/bootstrap-toggle-master/js/bootstrap-toggle.min.js') }}
    
    <script>
        $(function() {
            $('#toggle-one').bootstrapToggle({
                on: 'On',
                off: 'Off',
                onstyle: 'success',
                offstyle: 'danger',
                size: 'small'
            });
        });
    </script>
    {{ javascript_include('library/angular-1.5/js/angular-route.min.js') }}
    {{ javascript_include('library/angular-ui-router/angular-ui-router.min.js') }}

{% endblock %}
    
{% block content %}    
    <div class="clearfix"></div>
    <div class="space"></div>

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
            <div class="title">
                Edición de la información de la Subcuenta: <strong>{{subaccount.name}}</strong>
            </div>            
            <hr class="basic-line" />            
        </div>
    </div>       
    
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">
            <form action="{{url('account/subaccountedit')}}/{{(subaccount.idSubaccount)}}" method="post" class="form-horizontal">
                <div class="block block-info">          
                    <div class="body">
                        
                        <div class="form-group">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                                <span class="input hoshi input-default">                                    
                                    {{subaccountForm.render('name', {'class': 'undeline-input' , 'placeholder':'*Nombre'})}}                                    
                                </span>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                                <span class="input hoshi input-default">                                    
                                    {{subaccountForm.render('prefix', {'class': 'undeline-input' , 'placeholder':'Prefijo'})}}                                    
                                </span>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                                <span class="input hoshi input-default">                                    
                                    {{subaccountForm.render('fileSpace', {'class': 'undeline-input' , 'placeholder':'*Espacio disponible en disco (MB'})}}                                    
                                </span>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                                <span class="input hoshi input-default">                                    
                                    {{subaccountForm.render('contactLimit', {'class': 'undeline-input' , 'placeholder':'*Limite de Contactos:'})}}
                                </span>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                                <span class="input hoshi input-default">                                    
                                    {{subaccountForm.render('messagesLimit', {'class': 'undeline-input' , 'placeholder':'*Limite de Correos:'})}}
                                </span>
                            </div>
                        </div>
                        

                        <div class="form-group">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                                <span class="input hoshi input-default">                                    
                                    {{subaccountForm.render('smsLimit', {'class': 'undeline-input' , 'placeholder':'*Limite de Mensajes de Texto:'})}}
                                </span>
                            </div>
                        </div>
                                                                        
                        <div class="form-group">
                            <div class="col-md-12">
                                <span class="input hoshi input-default input-filled">
                                    <label>*Estado:</label>
                                    {{subaccountForm.render('status', {'id': 'toggle-one'})}}
                                </span>
                            </div>        
                        </div> 
                        
                    </div>
                    <div class="footer" align="right">                        
                        <a href="{{url('account/subaccountlist')}}/{{(subaccount.idAccount)}}" class="button shining btn btn-xs-round shining shining-round round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Cancelar">
                            <span class="glyphicon glyphicon-remove"></span>
                        </a>
                        <button class="button shining btn btn-xs-round shining shining-round round-button success-inverted" data-toggle="tooltip" data-placement="top" title="Guardar">
                            <span class="glyphicon glyphicon-ok"></span>
                        </button>
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
                            <li>El campo nombre no debe contener espacios, caracteres especiales o estar vacio.</li>                            
                            <li>El campo Prefijo en caso de quedar vacio, el sistema automaticamente tomará las primeras 4 letras del campo nombre y lo guardará.</li>
                            <li>El nombre de la subcuenta debe ser un nombre único, es decir, no pueden existir dos subcuenta con el mismo nombre.</li>
                            <li>El estado de la subcuenta por defecto esta desactivada (off) si desea activarla haga clic en el switch para que cambie a activada (on).</li>
                            <li>Recuerde que los campos con asterisco(*) son obligatorios.</li>
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
