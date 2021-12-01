{% extends "templates/default.volt" %}
{% block header %}
    {# Notifications #}
    {{ partial("partials/slideontop_notification_partial") }}
    
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
                Lista de correos internos
            </div>            
            <hr class="basic-line" />
            <p>
                Aqui encontrará el listado de correos que la plataforma utiliza para notificar información a los clientes, como por ejemplo el correo con instrucciones,
                para recuperar contraseña
            </p>          
        </div>
    </div>
    
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap text-right">            
            <a href="{{url('systemmail/create')}}" class="button shining btn btn-sm success-inverted">Crear un nuevo correo</a>
            {{ partial('partials/pagination_static_partial', ['pagination_url': 'systemmail/index']) }}
        </div>
    </div>       
    
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
            <table class="table table-bordered">                
                <thead class="theader">
                    <tr>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Categoría</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    {% for item in page.items %}
                    <tr>
                        <td><strong>{{item.idSystemmail}} - {{item.name}}</strong></td>
                        <td>{{item.description}}</td>
                        <td>{{item.category}}</td>
                        <td class="user-actions text-right">
                            <a class="button shining btn btn-xs-round shining shining-round round-button primary-inverted" data-toggle="collapse" href="#collapseDetails{{item.idSystemmail}}" aria-expanded="false" aria-controls="collapseDetails" id="details" data-placement="top" title="Ver detalles">
                                <span class="glyphicon glyphicon-collapse-down"></span>
                            </a>
                            <a href="{{url('systemmail/edit')}}/{{item.idSystemmail}}" class="button shining btn btn-xs-round shining shining-round round-button info-inverted" data-toggle="tooltip" data-placement="top" title="Editar este correo">
                                <span class="glyphicon glyphicon-pencil">
                            </a>
                            <button id="delete" onClick="openModal();" class="button shining btn btn-xs-round shining shining-round round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Borrar este correo" data-id="{{url('systemmail/delete')}}/{{item.idSystemmail}}">
                                <span class="glyphicon glyphicon-trash">
                            </button>
                        </td>
                    </tr>
                    <tr class="collapse" id="collapseDetails{{item.idSystemmail}}" >
                        <td colspan="5" class="text-center default-table">
                            {# <div class="col-xs-12 col-sm-12 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3"> #}
                            <div class="col-md-12">
                                <h3>Detalles</h3>
                                <hr />
                                <div class="clearfix"></div>
                                <div class="preview-container">
                                    <div class="preview">
                                        {% if item.previewData == null%}
                                            <div class="no-preview"></div>
                                        {% else %} 
                                            <img src="data: image/png;base64, {{item.previewData}}" />
                                        {% endif %}
                                    </div>
                                    <div class="data">
                                        <table class="table table-bordered" style="margin-bottom: {% if item.previewData == null%}100px{% else %}-20px{% endif %};">
                                            <thead></thead>
                                            <tbody> 
                                                <tr>
                                                    <td><strong>Asunto: </strong></td>
                                                    <td>{{item.subject}}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Remitente: </strong></td>
                                                    <td>{{item.fromName}} < {{item.fromEmail}} ></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Creado: </strong></td>
                                                    <td>{{date('d/m/Y g:i a', item.created)}}</td>
                                                </tr>  
                                                <tr>
                                                    <td><strong>Actualizado: </strong></td>
                                                    <td>{{date('d/m/Y g:i a', item.updated)}}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>    
                </tbody>
                {% endfor %}
            </table>
        </div>    
    </div>
        
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap text-right">       
            {{ partial('partials/pagination_static_partial', ['pagination_url': 'systemmail/index']) }}
        </div>    
    </div>    
        
    <div id="somedialog" class="dialog">
        <div class="dialog__overlay"></div>
        <div class="dialog__content">
            <div class="morph-shape">
                <svg xmlns="https://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 560 280" preserveAspectRatio="none">
                    <rect x="3" y="3" fill="none" width="556" height="276"/>
                </svg>
            </div>
            <div class="dialog-inner">
                <h2>¿Esta seguro?</h2>
                <div>                    
                    <a onClick="closeModal();" class="button shining btn btn-md danger-inverted" data-dialog-close>Cancelar</a>
                    <a href="#" id="btn-ok" class="button shining btn btn-md success-inverted">Confirmar</a>
                </div>
            </div>
        </div>
    </div>

    <script>
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

{% endblock %}
