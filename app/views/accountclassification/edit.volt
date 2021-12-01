{% extends "templates/default.volt" %}
{% block css %}
    {# Notifications #}
    {{ partial("partials/css_notifications_partial") }}
    {# Dialogs #}
    {{ stylesheet_link('library/dialog-effects/css/dialog.css') }}
    {{ stylesheet_link('library/dialog-effects/css/dialog-wilma.css') }}
{% endblock %}
{% block js %}
    {# Notifications #}
    {{ partial("partials/js_notifications_partial") }}
    {{ partial("partials/slideontop_notification_partial") }}
    {# Bootstrap Toggle #}
    {{ javascript_include('library/bootstrap-toggle-master/js/bootstrap-toggle.min.js') }}
    {# Select 2 #}
    {{ javascript_include('library/select2-4.0.0/js/select2.min.js') }}
    {# Dialogs #}
    {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}
    <script type="text/javascript">
        $(function () {
            $('#details').tooltip();
        });
        var urlsearch = '{{url('account/search')}}';
        var urluserlist = '{{url('account/userlist')}}';
        var urlaccountedit = '{{url('account/edit')}}';

        $(document).on("click", "#delete", function () {
            var myURL = $(this).data('id');
            $("#btn-ok").attr('href', myURL );
        });

        function openModal() {
            $('.dialog').addClass('dialog--open');
        }

        function closeModal() {
            $('.dialog').removeClass('dialog--open');
        }
    </script>
    {{ javascript_include('js/search/search-account.js') }}
{% endblock %}
{% block header %}
    {# Notifications #}


    {# Select 2 #}
    {{ stylesheet_link('library/select2-4.0.0/css/select2.css') }}
    {{ javascript_include('library/select2-4.0.0/js/select2.min.js') }}
    
    <script>
        $(function() {            
            $(".select2").select2();
        });
    </script>
    
    <script type="text/javascript">
        {% if notification.notification() %}
            $(function () {
                {% for message in notification.getNotification()%}
                    slideOnTop('{{message.message}}', 3500, 'glyphicon glyphicon-info-sign', '{{message.type}}');
                {% endfor %}
            });
        {% endif %}
    </script>
    
{% endblock %}
    
{% block content %}    
    <div class="clearfix"></div>
    <div class="space"></div>            
            
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
            <div class="title">
                Edición de la Clasificación de la Cuenta <strong>{{account.name}}</strong>
            </div>            
            <hr class="basic-line" />
        </div>
    </div>
    
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8 wrap">
            <form action="{{url('accountclassification/edit')}}/{{(account.idAccountclassification)}}" method="post">
                <div class="block block-info">
                    <div class="body">
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">                                
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <span class="input hoshi input-default">                                        
                                            {{form.render('name')}}
                                            <label class="input-label label-hoshi hoshi-default">
                                                <span class="input-label-content label-content-hoshi">*Nombre:</span>
                                            </label>
                                        </span>
                                     </div>       
                                </div>

                                <div class="form-group">
                                    <div class="col-md-12 ">
                                        <span class="input hoshi input-default input-filled">                                        
                                            {{form.render('idMta')}}
                                            <label class="input-label label-hoshi hoshi-default" for="input-90">
                                                <span class="input-label-content label-content-hoshi">*MTA:</span>
                                            </label>
                                        </span>
                                    </div>        
                                </div>

                                <div class="form-group">
                                    <div class="col-md-12 ">
                                        <span class="input hoshi input-default input-filled">                                        
                                            {{form.render('idAdapter')}}
                                            <label class="input-label label-hoshi hoshi-default" for="input-91">
                                                <span class="input-label-content label-content-hoshi">*Adaptador:</span>
                                            </label>
                                        </span>
                                    </div>        
                                </div>

                                <div class="form-group">
                                    <div class="col-md-12 ">
                                        <span class="input hoshi input-default input-filled">                                        
                                            {{form.render('idUrldomain')}}
                                            <label class="input-label label-hoshi hoshi-default" for="input-92">
                                                <span class="input-label-content label-content-hoshi">*Urldomain:</span>
                                            </label>
                                        </span>
                                    </div>        
                                </div>

                                <div class="form-group">
                                    <div class="col-md-12 ">
                                        <span class="input hoshi input-default input-filled">                                        
                                            {{form.render('idMailClass')}}
                                            <label class="input-label label-hoshi hoshi-default" for="input-93">
                                                <span class="input-label-content label-content-hoshi">*Mail Class:</span>
                                            </label>
                                        </span>
                                    </div>        
                                </div>

                                <div class="form-group">
                                    <div class="col-md-12 ">
                                        <span class="input hoshi input-default">                                        
                                            {{form.render('fileSpace')}}
                                            <label class="input-label label-hoshi hoshi-default">
                                                <span class="input-label-content label-content-hoshi">*Espacio disponible en disco (MB):</span>
                                            </label>
                                        </span>
                                    </div>        
                                </div>

                                <div class="form-group">
                                    <div class="col-md-12 ">
                                        <span class="input hoshi input-default">                                        
                                            {{form.render('mailLimit')}}
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
                                            {{form.render('contactLimit')}}
                                            <label class="input-label label-hoshi hoshi-default">
                                                <span class="input-label-content label-content-hoshi">*Limite de contactos:</span>
                                            </label>
                                        </span>
                                    </div>        
                                </div>

                                <div class="form-group">
                                    <div class="col-md-12 ">
                                        <span class="input hoshi input-default">                                        
                                            {{form.render('smsLimit')}}
                                            <label class="input-label label-hoshi hoshi-default">
                                                <span class="input-label-content label-content-hoshi">*Limite de SMS:</span>
                                            </label>
                                        </span>
                                    </div>        
                                </div>

                                <div class="form-group">
                                    <div class="col-md-12 ">
                                        <span class="input hoshi input-default">                                        
                                            {{form.render('smsVelocity')}}
                                            <label class="input-label label-hoshi hoshi-default">
                                                <span class="input-label-content label-content-hoshi">*Capacidad de envío por segundo:</span>
                                            </label>
                                        </span>
                                    </div>        
                                </div>

                                <div class="form-group">
                                    <div class="col-md-12 ">
                                        <span class="input hoshi input-default input-filled">                                        
                                            {{form.render('senderAllowed', {'class': 'input-field input-hoshi', 'required': 'required'})}}
                                            <label class="input-label label-hoshi hoshi-default">
                                                <span class="input-label-content label-content-hoshi">*¿Permitir al usuario agregar mas remitentes?:</span>
                                            </label>
                                        </span>
                                    </div>        
                                </div>

                                <div class="form-group">
                                    <div class="col-md-12 ">
                                        <span class="input hoshi input-default input-filled">                                        
                                            {{form.render('footerEditable', {'class': 'input-field input-hoshi', 'required': 'required'})}}
                                            <label class="input-label label-hoshi hoshi-default">
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
                                                {{form.render('expiryDate', {'class': 'input-field input-hoshi'})}}
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
                    Edición
                </div>
            </div>     
        </div>
    </div>
{% endblock %}
