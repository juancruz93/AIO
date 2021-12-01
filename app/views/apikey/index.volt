{% extends "templates/default.volt" %}
{% block css %}
    {# Notifications #}
    {{ partial("partials/css_notifications_partial") }}
    {{ stylesheet_link('library/angular-xeditable-0.2.0/css/xeditable.css') }}
    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/angular_material/1.1.0/angular-material.min.css">
{% endblock %}

{% block js %}
    {{ partial("partials/js_notifications_partial") }}
    {{ partial("partials/slideontop_notification_partial") }}

    {# Dialogs #}
    {{ stylesheet_link('library/dialog-effects/css/dialog.css') }}
    {{ stylesheet_link('library/dialog-effects/css/dialog-wilma.css') }}
    {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}

    {{ javascript_include('js/angular/apikey/app.js') }}
    {{ javascript_include('js/angular/apikey/controllers.js') }}
    {{ javascript_include('js/angular/apikey/services.js') }}
    {{ javascript_include('js/angular/apikey/directives.js') }}
    {{ javascript_include('library/angular-xeditable-0.2.0/js/xeditable.min.js') }}

    <!-- Angular Material Dependencies -->
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-animate.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-aria.min.js"></script>
    <!-- Angular Material Javascript now available via Google CDN; version 1.0.7 used here -->
    <script src="https://ajax.googleapis.com/ajax/libs/angular_material/1.0.7/angular-material.min.js"></script>
{% endblock %}
{% block content %}
    <div data-ng-app="apikey" data-ng-controller="indexController" ng-cloak>
        <div class="clearfix"></div>
        <div class="space"></div>
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <div class="title">
                    API Keys
                </div>
                <hr class="basic-line"/>
                <p>
                    La clave API es obligatoria para acceder a los servicios web Sigma.
                </p>
                <p>
                    La clave secreta debería guardarse muy bien, como la contraseña; no la compartas con nadie.
                </p>
            </div>
        </div>

        <div data-ng-show="apikey.items.length > 0">
        <div id="pagination" class="text-center">
            <ul class="pagination">
                <li ng-class="page == 1 ? 'disabled'  : ''">
                    <a  href="" ng-click="page == 1 ? true  : false || fastbackward()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
                </li>
                <li  ng-class="page == 1 ? 'disabled'  : ''">
                    <a href=""  ng-click="page == 1 ? true  : false || backward()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
                </li>
                <li>
                                <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{apikey.total }}"}}
                                  </b> registros </span><span>Página <b>{{"{{ page }}"}}
                                  </b> de <b>
                                    {{ "{{ (apikey.total_pages ) }}"}}
                                  </b></span>
                </li>
                <li   ng-class="page == (apikey.total_pages)  || apikey.total_pages == 0  ? 'disabled'  : ''">
                    <a href="" ng-click="page == (apikey.total_pages)  || apikey.total_pages == 0  ? true  : false || forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
                </li>
                <li   ng-class="page == (apikey.total_pages)  || apikey.total_pages == 0  ? 'disabled'  : ''">
                    <a ng-click="page == (apikey.total_pages)  || apikey.total_pages == 0  ? true  : false || fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
                </li>
            </ul>
        </div>

            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap sticky-wrap">
                        <table class="table table-bordered sticky-enabled">
                            <thead class="theader">
                            <tr>
                                <th></th>
                                <th>Tipo de usuario</th>
                                <th>Api Key</th>
                                <th>Secreto</th>
                                <th>Estado</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr data-ng-repeat="key in apikey.items track by $index">
                                    <td width="25%">
                                        <div class="small-text">{{ '{{ key.username }}' }} {{ '{{ key.userlastname }}' }}</div>
                                        <b>{{ '{{ key.useremail }}' }}</b> <br>
                                        <em>{{ '{{ key.subaccountname }}' }}</em>
                                    </td>
                                    <td>{{ '{{ key.rolename }}' }}</td>
                                    <td>
                                        <div data-ng-show="key.apikey">
                                            <label type="text" class="form-control" >{{ '{{ key.apikey }}' }}</label>
                                        </div>
                                        <div ng-hide="key.apikey">
                                            <a ng-click="addApikey(key.iduser)" class="button btn btn-sm success-inverted" data-toggle="tooltip" data-placement="top" title="Regenerar API Key">
                                                <span>Crear API Key</span>
                                            </a>
                                        </div>
                                    </td>
                                    <td data-ng-show="key.apikey">
                                        <label type="text" class="form-control">{{ '{{ key.apiSecret }}' }}</label>
                                    </td>
                                    <td data-ng-show="key.apikey">
                                        <md-switch ng-model="data.status" ng-init="(key.apiStatus == 1) ? data.status = true : data.status = false" data-ng-change="changeStatusApikey(key.iduser, data.status)" aria-label="Switch 2" class="md-warn">
                                        </md-switch>
                                    </td>
                                    <td class="text-right" data-ng-show="key.apikey">
                                        <a ng-click="openModalRegenerate(key.iduser)" class="button btn btn-xs-round success-inverted" data-toggle="tooltip" data-placement="top" title="Regenerar API Key">
                                            <span class="fa fa-repeat"></span>
                                        </a>
                                        <a ng-click="openModal(key.iduser)" class="button btn btn-xs-round danger-inverted" data-toggle="tooltip" data-placement="top" title="Eliminar API Key">
                                            <span class="fa fa-trash-o"></span>
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                </div>
            </div>

            <div id="pagination" class="text-center">
                <ul class="pagination">
                    <li ng-class="page == 1 ? 'disabled'  : ''">
                        <a  href="" ng-click="page == 1 ? true  : false || fastbackward()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
                    </li>
                    <li  ng-class="page == 1 ? 'disabled'  : ''">
                        <a href=""  ng-click="page == 1 ? true  : false || backward()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
                    </li>
                    <li>
                                <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{apikey.total }}"}}
                                  </b> registros </span><span>Página <b>{{"{{ page }}"}}
                                  </b> de <b>
                                    {{ "{{ (apikey.total_pages ) }}"}}
                                  </b></span>
                    </li>
                    <li   ng-class="page == (apikey.total_pages)  || apikey.total_pages == 0  ? 'disabled'  : ''">
                        <a href="" ng-click="page == (apikey.total_pages)  || apikey.total_pages == 0  ? true  : false || forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
                    </li>
                    <li   ng-class="page == (apikey.total_pages)  || apikey.total_pages == 0  ? 'disabled'  : ''">
                        <a ng-click="page == (apikey.total_pages)  || apikey.total_pages == 0  ? true  : false || fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
                    </li>
                </ul>
            </div>
        </div>

            <div data-ng-show="apikey.items.length == 0">
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                        <div class="block block-success">
                            <div class="body success-no-hover text-center">
                                <h2>
                                    En estos momentos no hay ninguna subcuenta perteneciente a esta cuenta.
                                </h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="deletedialog" class="dialog">
                <div class="dialog__overlay"></div>
                <div class="dialog__content">
                    <div class="morph-shape">
                        <svg xmlns="https://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 560 280" preserveAspectRatio="none">
                            <rect x="3" y="3" fill="none" width="556" height="276"/>
                        </svg>
                    </div>
                    <div class="dialog-inner">
                        <h2>¿Está seguro que desea eliminar esta API KEY?</h2>
                        <p>
                            Recuerde que si elimina esta API KEY las herramientas de configuración externas dejarán de funcionar.
                        </p>
                        <div style="z-index: 999999;">
                            <a data-ng-click="closeModal();" class="button shining btn btn-md danger-inverted" data-dialog-close>Cancelar</a>
                            <a data-ng-click="deleteApikey()" id="btn-ok" class="button shining btn btn-md success-inverted">Confirmar</a>
                        </div>
                    </div>
                </div>
            </div>

            <div id="regeneratedialog" class="dialog">
                <div class="dialog__overlay"></div>
                <div class="dialog__content">
                    <div class="morph-shape">
                        <svg xmlns="https://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 560 280" preserveAspectRatio="none">
                            <rect x="3" y="3" fill="none" width="556" height="276"/>
                        </svg>
                    </div>
                    <div class="dialog-inner">
                        <h2>¿Está seguro que desea regenerar esta API KEY?</h2>
                        <p>
                            Recuerde que si regenera esta API KEY las herramientas de configuración externas no funcionaran con la API Key anterior.
                        </p>
                        <div style="z-index: 999999;">
                            <a data-ng-click="closeModalRegenerate();" class="button shining btn btn-md danger-inverted" data-dialog-close>Cancelar</a>
                            <a data-ng-click="RegenerateApikey()" id="btn-ok" class="button shining btn btn-md success-inverted">Confirmar</a>
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
        var templateBase = "apikey";

    </script>

{% endblock %}
