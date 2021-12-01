{% extends "templates/default.volt" %}
{% block header %}
    {# Notifications #}

    {# Dialogs #}
    {{ stylesheet_link('library/dialog-effects/css/dialog.css') }}
    {{ stylesheet_link('library/dialog-effects/css/dialog-wilma.css') }}
    {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}

    <script type="text/javascript">
        {% if notification.notification() %}
            $(function () {
            {% for message in notification.getNotification()%}
                    slideOnTop('{{message.message}}', 6000, 'glyphicon glyphicon-info-sign', '{{message.type}}');
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
                Creación de una nueva URL
            </div>            
            <hr class="basic-line" />            
        </div>
    </div>                                                       

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">
            <form action="{{url('urldomain/create')}}" method="post" class="form-horizontal">
                <div class="block block-info">               
                    <div class="body">               

                        <div class="form-group">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                                <label  class="col-sm-4 text-right">*URL</label>
                                <span class="input hoshi input-default  col-sm-8">                                    
                                    {{url_form.render('name', {'class': 'undeline-input'} )}}
                                </span>
                            </div>       
                        </div>

                        <div class="form-group">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                                <label  class="col-sm-4 text-right">*Descripción</label>
                                <span class="input hoshi input-default  col-sm-8">                                       
                                    {{url_form.render('description', {'class': 'undeline-input'} )}}
                                </span>
                            </div>        
                        </div>
                    </div>                                            

                    <div class="footer text-right">                        
                        <button class="button btn btn-xs-round round-button success-inverted" data-toggle="tooltip" data-placement="top" title="Guardar">
                            <span class="glyphicon glyphicon-ok"></span>
                        </button>
                        <a href="{{url('urldomain/index')}}" class="button btn btn-xs-round round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Cancelar">
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
                        <li>El URL nombre no debe contener espacios, caracteres especiales o estar vacio.</li>
                        <li>La URL debe llevar el https://</li>
                        <li>La URL debe ser única, es decir, no pueden existir dos URL iguales registradas en la plataforma.</li>
                        <li>La descripción debe ser clara sobre el funcionamiento de la URL</li>
                        <li>Los campos con asterisco(*) son obligatorios.</li>                        
                    </ul>
                </div>
                <div class="footer">
                    
                </div>
            </div>     
        </div>
    </div>                                   
{% endblock %}
