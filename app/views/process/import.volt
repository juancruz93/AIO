{% extends "templates/default.volt" %}
{% block css %}
    {# Notifications #}
    {{ partial("partials/css_notifications_partial") }}
    {{ stylesheet_link('library/angular-xeditable-0.2.0/css/xeditable.css') }}
    <link rel="stylesheet"
          href="https://ajax.googleapis.com/ajax/libs/angular_material/1.1.0/angular-material.min.css">
{% endblock %}

{% block js %}
    {{ partial("partials/js_notifications_partial") }}
    {{ partial("partials/slideontop_notification_partial") }}

    {# Dialogs #}
    {{ stylesheet_link('library/dialog-effects/css/dialog.css') }}
    {{ stylesheet_link('library/dialog-effects/css/dialog-wilma.css') }}
    {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}

    {{ javascript_include('library/angular-1.5/js/angular-route.min.js') }}
    {{ javascript_include('js/angular/process/app.js') }}
    {{ javascript_include('js/angular/process/controllers.js') }}
    {{ javascript_include('js/angular/process/services.js') }}
    {{ javascript_include('library/angular-xeditable-0.2.0/js/xeditable.min.js') }}

    {# Select 2 #}
    {{ stylesheet_link('library/select2-4.0.0/css/select2.css') }}
    {{ javascript_include('library/select2-4.0.0/js/select2.min.js') }}
    <!-- Angular Material Dependencies -->
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-animate.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-aria.min.js"></script>
    <!-- Angular Material Javascript now available via Google CDN; version 1.0.7 used here -->
    <script src="https://ajax.googleapis.com/ajax/libs/angular_material/1.0.7/angular-material.min.js"></script>
    <script>
        $(function () {
            $(".select2").select2({
                theme: 'classic'
            });
        });
    </script>
{% endblock %}
{% block content %}
    <div data-ng-controller="importDetailController">
        <div class="clearfix"></div>
        <div class="space"></div>
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <div class="title">
                    Listado de importaciones de la lista: <strong>{{ contactlist.name }}</strong>
                </div>
                <hr class="basic-line"/>
            </div>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
            <div class="pull-right">
                <a href="{{ url('contactlist/show/') }}" class="button shining btn default-inverted">Regresar</a>
            </div>
        </div>

        {% if page.items|length != 0 %}
        <div class="row">
            <div class="row">
                {{ partial('partials/pagination_static_partial', ['pagination_url': 'process/import/'~contactlist.idContactlist]) }}
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <div class="body">
                    <table class="table table-bordered">
                        <thead class="theader">
                        <tr>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th>Detalles</th>
                            <th>Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        {% for item in page.items %}
                            <tr>
                                <td>Creado por <strong>{{ item.email }}</strong> el día
                                    <strong>{{ date('d/m/Y g:i a', item.created) }}</strong>
                                </td>
                                <td>
                                    {% if( item.status == "preprocessing" ) %}
                                        Preprocesado
                                    {% elseif(item.status == "processing") %}
                                        Procesando
                                    {% elseif(item.status == "saving") %}
                                        Guardando
                                    {% elseif(item.status == "canceled") %}
                                        Cancelado
                                    {% elseif(item.status == "pending") %}
                                        Pendiente
                                    {% elseif(item.status == "finished") %}
                                        Finalizado
                                    {% endif %}
                                </td>
                                <td>
                                    <dl>
                                        <dd><strong>Registros totales:</strong> {{ item.rows }}</dd>
                                        <dd><strong>Importados:</strong>{{ item.imported }} </dd>
                                        <dd><strong>No importados:</strong> {{ item.rows - item.imported }}</dd>
                                    </dl>
                                </td>
                                <td class="text-right">
                                    <a href="{{ url('process/importdetail/'~item.idImportcontactfile) }}"
                                       class="button shining btn btn-xs-round shining shining-round round-button primary-inverted"
                                       data-toggle="tooltip" data-placement="top" title=""
                                       data-original-title="Detalle de importación">
                                        <span class="glyphicon glyphicon-search"></span>
                                    </a>
                                </td>
                            </tr>
                        {% endfor %}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="row">
            {{ partial('partials/pagination_static_partial', ['pagination_url': 'process/import/'~contactlist.idContactlist]) }}
        </div>

        {% else %}

            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <div class="block block-success">
                        <div class="body success-no-hover text-center">
                            <h2>
                                No existen importaciones realizadas en esta lista de contactos, si desea realizar una haga <a href="{{url('contact/index/')~contactlist.idContactlist}}#/import">clic aquí</a>.
                            </h2>
                        </div>
                    </div>
                </div>
            </div>
        {% endif %}

    </div>

{% endblock %}
{% block footer %}
    {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}

    <script type="text/javascript">

    </script>

    {#{{ javascript_include('library/angular-1.5/js/angular.min.js') }}#}

{% endblock %}
