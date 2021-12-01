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
                Lista de Servicios
            </div>
            <hr class="basic-line" />
            <p>
                Sigma Móvil ofrece una serie de servicios para hacer Marketing Digital de manera rápida y fácil,
                estos servicios por lo general son servicios que efectuan una tarea puntual, como por ejemplo 
                el servicio de Email Marketing o el servicio de SMS Marketing. Sigma Móvil All In One es una aplicación que integra todos estos servicios en una sola parte 
                siendo mucho más manejable y amigable, pero es probable que en un futuro se integren más servicios, por está razón es importante que este preparada para integrar otros servicios. Aqui encontrará el listado de todos los servicios que están integradas actualmente a Sigma Móvil All In One
            </p>
        </div>
    </div>
                    
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap text-right">
            <a href="{{url('system')}}" class="button shining btn default-inverted">Regresar</a>
            <a href="{{url('services/create')}}" class="button shining btn btn-md success-inverted">Crear nueva servicio</a>
        </div>
    </div>

    {% if page.items|length != 0 %}
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                {{ partial('partials/pagination_static_partial', ['pagination_url': 'services/index']) }}
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
                                <td class="text-right">
                                    {#
                                    <button id="delete" onClick="openModal();" class="button shining btn btn-xs-round shining-round round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Eliminar servicio" data-id="{{ url('services/delete') }}/{{item.idServices}}">
                                        <span class="glyphicon glyphicon-trash"</span>
                                    </button>
                                    #}
                                    <a href="{{ url('services/edit') }}/{{item.idServices}}">
                                        <button class="button shining btn btn-xs-round shining-round round-button primary-inverted" data-toggle="tooltip" data-placement="top" title="Editar servicio">
                                            <span class="glyphicon glyphicon-pencil"</span>
                                        </button>
                                    </a>
                                </td>
                            </tr>
                        {% endfor %}
                    </tbody>
                </table> 
            </div>
        </div>

        <div class="row">
            {{ partial('partials/pagination_static_partial', ['pagination_url': 'adapter/index']) }}
        </div>
    {% else %} 
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <div class="block block-success">
                    <div class="body success-no-hover text-center">
                        <h2>
                            No existen servicios creadas actualmente, si desea crear una haga <a href="{{url('services/create')}}">clic aquí</a>.
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
                <svg xmlns="https://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 460 180" preserveAspectRatio="none">
                <rect x="3" y="3" fill="none" width="456" height="176"/>
                </svg>
            </div>
            <div class="dialog-inner">
                <h2>¿Está seguro?</h2>
                <div>
                    <a onClick="closeModal()" class="button shining btn danger-inverted" data-dialog-close>Cancelar</a>
                    <a href="#" id="btn-ok" class="button shining btn success-inverted">Confirmar</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).on("click", "#delete", function () {
            $('.dialog').addClass('dialog--open');
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
