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

    <div data-ng-controller="importDetailController" ng-cloak>
        <div class="clearfix"></div>
        <div class="space"></div>
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <div class="title">
                    Detalle de importación a la lista:
                    <strong>{{ importcontactfile.Importfile.Contactlist.name }}</strong>
                </div>
                <hr class="basic-line"/>
            </div>
        </div>

        <div class="row wrap">
            <div class="div-border-thin">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-top">
                    Importación realizada por <strong>{{ importcontactfile.Importfile.createdBy }}</strong>
                    el día
                    <strong>{{ date('Y/m/d h:m a', importcontactfile.Importfile.created) }}</strong>
                </div>
                <div class="row wrap"></div>
            </div>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
            <div class="pull-right">
                <a href="{{ url('contact/index/')}}{{ importcontactfile.Importfile.Contactlist.idContactlist }}" class="button shining btn default-inverted">
                    Regresar a la lista de contactos:
                    <strong>{{ importcontactfile.Importfile.Contactlist.name }}</strong>
                </a>
                <a href="{{ url('contactlist/show')}}" class="button shining btn primary-inverted">
                    Regresar al listado de las listas de contactos
                </a>
            </div>
        </div>

        <div class="margin-top">
            <div class="col-md-3"></div>
            <div class="row wrap">
                <div class="div-border-thin col-md-6 row wrap">
                    <div class="col-lg-12 text-center padding-top">
                        <strong class="medium-text">{{ '{{ changeStatus(nameStaus) }}' }}</strong>
                        <div ng-class="{'hidden' : progressbar}">
                            <md-progress-linear md-mode="indeterminate" class="md-warn"></md-progress-linear>
                        </div>
                    </div>
                    <div class="row" data-ng-if="nameStaus == 'canceled'">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="fill-block fill-block-danger" >
                                <div class="body">
                                    <p>
                                        <b>Es posible que el archivo que cargaste este corrupto, por favor ten en cuenta estas instrucciones:</b>
                                    <ul>
                                        <li>Revisa que la codificación del archivo este en UTF-8.</li>
                                        <li>Revisa que el archivo no tenga caracteres extraños.</li>
                                        <li>Te recomendamos abrirlo con Bloc de notas o Notepad++ para una mejor visión.</li>
                                    </ul>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3"></div>
        </div>

        <div class="row" data-ng-if="nameStaus == 'finished'">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <div class="body">
                    <table class="table table-bordered">
                        <tbody>
                        <tr>
                            <td class="text-center" width="5%">
                                <span class="fa fa-folder-open fa-2x color-info"></span>
                            </td>
                            <td class="small-text" width="40%">
                                <p class="color-info">Registros totales en el archivo</p>
                            </td>
                            <td class="text-right small-text" width="45%">
                                <p class="color-info"><strong>{{ '{{ data.rows }}' }}</strong></p>
                            </td>
                        </tr>
                        {#<tr>
                            <td class="text-center" width="5%">
                                <span class="fa fa-refresh fa-2x color-primary"></span>
                            </td>
                            <td class="small-text" width="40%">
                                <p class="color-primary">Actualizados exitosamente</p>
                            </td>
                            <td class="text-right small-text" width="45%">
                                <p class="color-primary"><strong>0</strong></p>
                            </td>
                        </tr>#}
                        <tr>
                            <td class="text-center" width="5%">
                                <span class="fa fa-check-circle-o fa-2x positive"></span>
                            </td>
                            <td class="small-text" width="40%">
                                <p class="positive">Importados exitosamente <a class="positive" href="{{ url("process/downloadsuccess/") }}{{ importcontactfile.idImportcontactfile }}">
                                        <b>(Descargar reporte)</b></a></p>
                            </td>
                            <td class="text-right small-text" width="45%">
                                <p class="positive"><strong>{{ '{{ data.imported }}' }}</strong></p>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-center" width="5%">
                                <span class="fa fa-ban fa-2x error"></span>
                            </td>
                            <td class="small-text" width="40%">
                                <p class="error">No importados por datos inválidos</p>
                            </td>
                            <td class="text-right small-text" width="45%">
                                <p class="error"><strong>{{ '{{ data.invalids }}' }}</strong></p>
                            </td>
                        </tr>
                        {#<tr>
                            <td class="text-center" width="5%">
                                <span class="fa fa-ban fa-2x error"></span>
                            </td>
                            <td class="small-text" width="40%">
                                <p class="error">No importados por correo bloqueado</p>
                            </td>
                            <td class="text-right small-text" width="45%">
                                <p class="error"><strong>0</strong></p>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-center" width="5%">
                                <span class="fa fa-ban fa-2x error"></span>
                            </td>
                            <td class="small-text" width="40%">
                                <p class="error">No importados porque están duplicados en el archivo</p>
                            </td>
                            <td class="text-right small-text" width="45%">
                                <p class="error"><strong>{{ '{{ data.repeated }}' }}</strong></p>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-center" width="5%">
                                <span class="fa fa-ban fa-2x error"></span>
                            </td>
                            <td class="small-text" width="40%">
                                <p class="error">No importados por límite de contactos excedidos</p>
                            </td>
                            <td class="text-right small-text" width="45%">
                                <p class="error"><strong>0</strong></p>
                            </td>
                        </tr>#}
                        <tr>
                            <td class="text-center" width="5%">
                                <span class="fa fa-ban fa-2x error" ></span>
                            </td>
                            <td class="small-text" width="40%">
                                <p class="error">Contactos no importados <a class="error" href="{{ url("process/downloaderror/") }}{{ importcontactfile.idImportcontactfile }}"><b>(Descargar
                                        reporte)</b></a></p>
                            </td>
                            <td class="text-right small-text" width="45%">
                                <p class="error"><strong>{{ '{{ totals(data.invalids, data.repeated)}}' }}</strong></p>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

{% endblock %}
{% block footer %}
    {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}

    <script type="text/javascript">
        var relativeUrlBase = "{{ urlManager.get_base_uri() }}";
        var fullUrlBase = "{{ urlManager.get_base_uri(true) }}";
        var templateBase = "process";
        var idImportcontactfile = {{ importcontactfile.idImportcontactfile }};
        var firstStatus = "{{ importcontactfile.status }}";
        var rows = "{{ importcontactfile.rows }}";
        var imported = "{{ importcontactfile.imported }}";
        var repeated = "{{ importcontactfile.repeated }}";
        var invalids = "{{ importcontactfile.invalids }}";
    </script>

{% endblock %}
