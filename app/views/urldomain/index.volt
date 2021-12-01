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
                Lista de URL'S
            </div>            
            <hr class="basic-line" />
            <p>
                Es posible que en algunos casos haya que tener más servidores disponibles que se usen solo para
                cargar las imágenes de los correos para evitar la saturación. Por ello cada cuenta se debe configurar
                con la dirección URL que la plataforma usará para transformar las URL relativas de las imégenes en URL
                absolutas.
            </p>
            <p>
                En esta lista encontraran las URL'S con las que cuenta nuestra plataforma.
            </p>            
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap text-right">
            <a href="{{url('system')}}" class="button shining btn default-inverted">Regresar</a>
            <a href="{{url('urldomain/create')}}" class="button shining btn success-inverted">Crear una nueva URL</a>
        </div>
    </div>      


    {% if page.items|length != 0 %}
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap text-right">
                {{ partial('partials/pagination_static_partial', ['pagination_url': 'urldomain/index']) }}
            </div>
        </div>       

        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <table class="table table-bordered">                
                    <thead class="theader">
                        <tr>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        {% for item in page.items %}
                            <tr class="{% if item.status == 0 %} danger letter-no-hover {% endif %}">
                                <td>
                                    <div class="medium-text"><b>{{item.name}}</b></div>
                                    <em class="extra-small-text">
                                        Creado el <b>{{date('d/m/Y g:i a', item.created)}}</b> por <b>{{item.updatedBy}}</b>
                                    </em>
                                    <br />
                                    <em class="extra-small-text">
                                        Editado el <b>{{date('d/m/Y g:i a', item.updated)}}</b> por <b>{{item.updatedBy}}</b>
                                    </em>
                                </td>
                                <td>{{item.description}}</td>
                                <td class="user-actions text-right">
                                    <a href="{{url('urldomain/edit')}}/{{item.idUrldomain}}" class="button shining btn btn-xs-round shining shining-round round-button info-inverted" data-toggle="tooltip" data-placement="top" title="Editar esta URL">
                                        <span class="glyphicon glyphicon-pencil">
                                    </a>
                                    {#                
                                    <button id="delete" onClick="openModal();" class="button shining btn btn-xs-round shining shining-round round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Borrar esta URL" data-id="{{url('urldomain/delete')}}/{{item.idUrldomain}}">
                                        <span class="glyphicon glyphicon-trash">
                                    </button>
                                    #}
                                </td>
                            </tr>                    
                        </tbody>
                    {% endfor %}
                </table>
                {{ partial('partials/pagination_static_partial', ['pagination_url': 'urldomain/index']) }}
            </div>    
        </div>
    {% else %}
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <div class="block block-success">
                    <div class="body success-no-hover text-center">
                        <h2>
                            No existen URL creadas actualmente, si desea crear una haga <a href="{{url('urldomain/create')}}">clic aquí</a>.
                        </h2>    
                    </div>
                </div>
            </div>
        </div>
    {% endif %}

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
            $("#btn-ok").attr('href', myURL);
        });

        function openModal() {
            $('.dialog').addClass('dialog--open');
        }

        function closeModal() {
            $('.dialog').removeClass('dialog--open');
        }
    </script>

{% endblock %}
