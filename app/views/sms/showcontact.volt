{% extends "templates/default.volt" %}
{% block css %}
    {# Notifications #}
    {{ partial("partials/css_notifications_partial") }}
    {# Dialogs #}
    {{ stylesheet_link('library/dialog-effects/css/dialog.css') }}
    {{ stylesheet_link('library/dialog-effects/css/dialog-wilma.css') }}
    {{ stylesheet_link('library/angular-material-1.1.0/css/angular-material.min.css') }}
{% endblock %}

{% block js %}
    {{ javascript_include('library/angular-keep-values/angular-keep-values.min.js') }}
    {{ javascript_include('library/ui-select-master/dist/select.min.js') }}
    {# {{ javascript_include('js/angular/sms/controller.js') }} #}
    {{ javascript_include('js/angular/sms/dist/sms.c74c5ac2d7c95ebe09dd.min.js') }}
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

        var relativeUrlBase = "{{ urlManager.get_base_uri() }}";
        var fullUrlBase = "{{ urlManager.get_base_uri(true) }}";
        var templateBase = "sms";
        var idSms = {{ sms.idSms }};
    </script>
    {{ javascript_include('js/search/search-account.js') }}
{% endblock %}
{% block content %}
<div ng-app="aio" ng-controller="showContact">
    <div class="clearfix"></div>
    <div class="space"></div>

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
            <div class="title">
                Lista de Sms <strong>{{ sms.name }}</strong>
            </div>
            <hr class="basic-line" />
            <p>
                En esta lista podra ver los  sms
            </p>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-lg-12 text-right wrap" >
            <a href="{{ url("sms")}}">
                <button class="button  btn btn-sm default-inverted">
                    <i class="fa fa-arrow-left"></i>
                    Regresar
                </button>
            </a>
        </div>
    </div>

    <div ng-show="listsms.detail[0].length > 0" ng-cloak>

        <div id="pagination" class="text-center">
            <ul class="pagination">
                <li ng-class="page == 1 ? 'disabled'  : ''">
                    <a  href="" ng-click="page == 1 ? true  : false || fastbackward()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
                </li>
                <li  ng-class="page == 1 ? 'disabled'  : ''">
                    <a href=""  ng-click="page == 1 ? true  : false || backward()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
                </li>
                <li>
                <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{listsms.detail[1].total }}"}}
                  </b> registros </span><span>Página <b>{{"{{ page }}"}}
                  </b> de <b>
                    {{ "{{ (listsms.detail[1].total_pages ) }}"}}
                  </b></span>
                </li>
                <li   ng-class="page == (listsms.detail[1].total_pages)  || listsms.detail[1].total_pages == 0  ? 'disabled'  : ''">
                    <a href="" ng-click="page == (listsms.detail[1].total_pages)  || listsms.detail[1].total_pages == 0  ? true  : false || forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
                </li>
                <li   ng-class="page == (listsms.detail[1].total_pages)  || listsms.detail[1].total_pages == 0  ? 'disabled'  : ''">
                    <a ng-click="page == (listsms.detail[1].total_pages)  || listsms.detail[1].total_pages == 0  ? true  : false || fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
                </li>
            </ul>
        </div>

        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <table class="table table-bordered table-responsive" id="resultTable">
                    <thead class="theader">
                    <tr>
                        <th>Información</th>
                        <th>Detalles</th>
                        <th>Mensaje</th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr ng-repeat="detail in listsms.detail[0]">
                            <td>
                                <dl>
                                    <dd class="small-text">Estado: {{ '{{traslateStatus(detail.status)}}' }} </dd>
                                </dl>
                            </td>
                            <td>
                                <di>
                                    <dd>Celular: ({{ '{{detail.code}}' }}) {{ '{{detail.phone}}' }} </dd>
                                </di>
                            </td>
                            <td>
                                <p>{{ '{{detail.message}}' }}</p>
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
                <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{listsms.detail[1].total }}"}}
                  </b> registros </span><span>Página <b>{{"{{ page }}"}}
                  </b> de <b>
                    {{ "{{ (listsms.detail[1].total_pages ) }}"}}
                  </b></span>
                </li>
                <li   ng-class="page == (listsms.detail[1].total_pages)  || listsms.detail[1].total_pages == 0  ? 'disabled'  : ''">
                    <a href="" ng-click="page == (listsms.detail[1].total_pages)  || listsms.detail[1].total_pages == 0  ? true  : false || forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
                </li>
                <li   ng-class="page == (listsms.detail[1].total_pages)  || listsms.detail[1].total_pages == 0  ? 'disabled'  : ''">
                    <a ng-click="page == (listsms.detail[1].total_pages)  || listsms.detail[1].total_pages == 0  ? true  : false || fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
                </li>
            </ul>
        </div>
    </div>

    <div ng-show="listsms.detail[0].length == 0">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <div class="block block-success">
                    <div class="body success-no-hover text-center">
                        <h2>
                            <a href="{{url('sms/showlote/')}}">clic aquí</a>.
                        </h2>
                    </div>
                </div>
            </div>
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
                <div style="z-index: 999999;">
                    <a onClick="closeModal();" class="button shining btn btn-md danger-inverted" data-dialog-close>Cancelar</a>
                    <a href="#" id="btn-ok" class="button shining btn btn-md success-inverted">Confirmar</a>
                </div>
            </div>
        </div>
    </div>
</div>
{% endblock %}
