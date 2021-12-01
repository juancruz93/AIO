{% extends "templates/default.volt" %}
{% block header %}
    {# Notifications #}
    {{ partial("partials/slideontop_notification_partial") }}
    {# Dialogs #}
    {{ stylesheet_link('library/dialog-effects/css/dialog.css') }}
    {{ stylesheet_link('library/dialog-effects/css/dialog-wilma.css') }}
    {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}

     <script type="text/javascript">
        $(function () {
            $('#details').tooltip();
        });
     </script>
    {{ javascript_include('library/angular-1.5/js/angular-route.min.js') }}
    {{ javascript_include('library/angular-ui-router/angular-ui-router.min.js') }}
{% endblock %}

{% block content %}
    <div class="clearfix"></div>
    <div class="space"></div>     
    
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
            <div class="title">
                Lista de Subcuentas
            </div>            
            <hr class="basic-line" />
            <p>
                En esta lista podra ver, crear, editar y eliminar subcuentas de una cuenta.
            </p>            
        </div>
    </div>
    
    {% if page.items|length != 0 %}
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap text-right">            
                <a href="{{url('account/index')}}" class="button shining btn btn-sm default-inverted">Regresar a lista de Cuentas</a>
                <a href="{{url('account/subaccountcreate')}}/{{idAccount}}" class="button shining btn btn-sm success-inverted">Crear una nueva Subcuenta</a>
                {{ partial('partials/pagination_static_partial', ['pagination_url': 'account/userlist']) }}
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <table class="table table-bordered">                
                    <thead class="theader">
                        <tr>
                            <th>Nombre</th>
                            <th>Prefijo</th>
                            <th>Espacio (Mb)</th>
                            <th>Límite de Correos</th>
                            <th>Límite de Mensajes</th>
                            <th>Límite de Contactos</th>
                            <th>Creado</th>
                            <th>Actualizado</th>
                            <th></th>
                        </tr>
                    </thead>

                    <tbody>
                        {% for item in page.items %}                    
                        <tr {% if item.status == 0 %} class="account-disabled" {% endif %}>
                            <td>
                                <strong>
                                    {{(item.idSubaccount)}} - {{item.name}}
                                </strong>
                            </td>
                            <td>{{item.prefix}}</td>
                            <td>{{item.fileSpace}}</td>
                            <td>{{item.messagesLimit}}</td>
                            <td>{{item.smsLimit}}</td>
                            <td>{{item.contactLimit}}</td>
                            <td>{{date('d/m/Y g:i a', item.created)}}</td>
                            <td>{{date('d/m/Y g:i a', item.updated)}}</td>                        
                            <td class="user-actions text-right">
                                <a href="{{url('account/subaccountedit')}}/{{item.idSubaccount}}" class="button shining btn btn-xs-round shining shining-round round-button info-inverted" data-toggle="tooltip" data-placement="top" title="Editar esta subcuenta">
                                    <span class="glyphicon glyphicon-pencil"></span>
                                </a>
                                <button id="delete" onClick="openModal();" class="button shining btn btn-xs-round shining shining-round round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Borrar esta subcuenta" data-id="{{url('account/subaccountdelete')}}/{{item.idSubaccount}}">
                                    <span class="glyphicon glyphicon-trash"></span>
                                </button>
                            </td>
                        </tr>
                        {% endfor %}
                    </tbody>
                </table>
                {{ partial('partials/pagination_static_partial', ['pagination_url': 'account/userlist']) }}
            </div>
        </div>
    {% else %} 
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <div class="text-right">                
                    <a href="{{url('account/index')}}" class="button shining btn btn-sm default-inverted">Regresar a lista de Cuentas</a>
                </div>
                <div class="block block-success">
                    <div class="body success-no-hover text-center">
                        <h2>
                            No existen subcuentas creadas actualmente, si desea crear una haga <a href="{{url('account/subaccountcreate')}}/{{idAccount}}">clic aquí</a>.
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
                <div style="z-index: 999999;">           
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
