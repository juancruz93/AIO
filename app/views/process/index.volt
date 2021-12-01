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

    {# Socket.IO#}
    {{ javascript_include('js/socket.io.js') }}
{% endblock %}
{% block content %}
    <div data-ng-app="process" data-ng-controller="indexController" ng-cloak>
        <div class="clearfix"></div>
        <div class="space"></div>
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <div class="title">
                    Procesos de envío
                </div>
                <hr class="basic-line"/>
                <p>
                    Monitoree los envíos de correos electrónicos, la programación de los mismos, sus estados y envíos en curso.
                </p>
            </div>
        </div>

        <div data-ng-show="socket.connected == false">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <div class="block block-danger">
                        <div class="body danger-no-hover text-center">
                            <h2>
                                El servidor nodeJS no se encuentra operando, para iniciarlo haga clic <a href="{{ url('process/startservernode') }}" class="error underline">aquí</a>
                            </h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div data-ng-show="socket.connected == true">

            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-right wrap">
                    <a href="" data-ng-click="restartServerNode()" class="button shining btn btn-sm warning-inverted">Reiniciar servidor</a>
                    <a href="" data-ng-click="stopServerNode()" class="button shining btn btn-sm danger-inverted">Detener servidor</a>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <div class="small-text">
                        <p>Procesos de envío de <b>correo</b>.</p>
                    </div>
                </div>
            </div>
            <div class="row" ng-show="processMail.length > 0">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <div class="body">
                        <table class="table table-bordered">
                            <thead class="theader">
                            <tr>
                                <th>PID</th>
                                <th>IdMail</th>
                                <th>Contactos totales</th>
                                <th>Correos enviados</th>
                                <th>Acciones</th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr data-ng-repeat="key in processMail">
                                    <td>{{ '{{ key.pid }}' }}</td>
                                    <td>{{ '{{ key.idMail }}' }}</td>
                                    <td>{{ '{{ key.quantitytarget }}' }}</td>
                                    <td>{{ '{{ key.messagesSent }}' }}</td>
                                    <td class="text-right">
                                        <a ng-click="pauseMailAn(key.idMail)" ng-show="key.status == 'scheduled'" class="button btn btn-xs-round warning-inverted" data-toggle="tooltip" data-placement="top" title="Pausar envio">
                                            <span class="glyphicon glyphicon-pause"></span>
                                        </a>
                                        <a ng-click="cancelMailAn(key.idMail)" ng-show="key.status == 'scheduled'" class="button btn btn-xs-round danger-inverted" data-toggle="tooltip" data-placement="top" title="Cancelar envio">
                                            <span class="fa fa-ban"></span>
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div ng-show="processMail.length == 0">
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                        <div class="block block-success">
                            <div class="body success-no-hover text-center">
                                <h2>
                                    En estos momentos no hay ningún proceso de envío de correo activo.
                                </h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row margin-top-45px">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <div class="small-text">
                        <p>Procesos de envío de <b>sms</b>.</p>
                    </div>
                </div>
            </div>
            <div class="row" ng-show="processSms.length > 0" >
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <div class="body">
                        <table class="table table-bordered">
                            <thead class="theader">
                            <tr>
                                <th>PID</th>
                                <th>IdSms</th>
                                <th>Contactos totales</th>
                                <th>Sms enviados</th>
                                <th>Acciones</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr data-ng-repeat="key in processSms">
                                <td>{{ '{{ key.pid }}' }}</td>
                                <td>{{ '{{ key.idSms }}' }}</td>
                                <td>falta {#{{ '{{ key.quantitytarget }}' }}#}</td>
                                <td>falta {#{{ '{{ key.messagesSent }}' }}#}</td>
                                <td class="text-right">
                                    <a ng-click="pauseSmsAn(key.idSms)" {#ng-show="key.status == 'scheduled'"#} class="button btn btn-xs-round warning-inverted" data-toggle="tooltip" data-placement="top" title="Pausar envio">
                                        <span class="glyphicon glyphicon-pause"></span>
                                    </a>
                                    <a ng-click="cancelSmsAn(key.idSms)" {#ng-show="key.status == 'scheduled'"#} class="button btn btn-xs-round danger-inverted" data-toggle="tooltip" data-placement="top" title="Pausar envio">
                                        <span class="fa fa-ban"></span>
                                    </a>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div ng-show="processSms.length == 0">
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                        <div class="block block-success">
                            <div class="body success-no-hover text-center">
                                <h2>
                                    En estos momentos no hay ningún proceso de envío de sms activo.
                                </h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row margin-top-45px">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <div class="small-text">
                        <p>Procesos de <b>Importación de contactos</b>.</p>
                    </div>
                </div>
            </div>
            <div class="row" ng-show="processImport.length > 0">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <div class="body">
                        <table class="table table-bordered">
                            <thead class="theader">
                            <tr>
                                <th>PID</th>
                                <th>idImportcontactfile</th>
                                <th>Contactos totales</th>
                                <th>Contactos procesados</th>
                                <th>Acciones</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr data-ng-repeat="key in processImport">
                                <td>{{ '{{ key.pid }}' }}</td>
                                <td>{{ '{{ key.idImportcontactfile }}' }}</td>
                                <td>{{ '{{ key.rows }}' }}</td>
                                <td>{{ '{{ key.processed }}' }}</td>
                                <td class="text-right">
                                    {#<a ng-click="pauseMailAn(key.idSms)" #}{#ng-show="key.status == 'scheduled'"#}{# class="button btn btn-xs-round warning-inverted" data-toggle="tooltip" data-placement="top" title="Pausar envio">
                                        <span class="glyphicon glyphicon-pause"></span>
                                    </a>
                                    <a ng-click="resumeMailAn(key.idSms)" #}{#ng-show="key.status == 'paused'"#}{# class="button btn btn-xs-round success-inverted" data-toggle="tooltip" data-placement="top" title="Reanudar envio">
                                        <span class="glyphicon glyphicon-play"></span>
                                    </a>#}
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div ng-show="processImport.length == 0">
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                        <div class="block block-success">
                            <div class="body success-no-hover text-center">
                                <h2>
                                    En estos momentos no hay ningún proceso de importación de contactos activo.
                                </h2>
                            </div>
                        </div>
                    </div>
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

    </script>

{% endblock %}
