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

     <script type="text/javascript">
        $(function () {
            $('#details').tooltip();
        });
     </script>
{% endblock %}

{% block content %}
    <div class="clearfix"></div>
    <div class="space"></div>     
    
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
            <div class="title">
                Lista de Clasificación de Cuentas
            </div>            
            <hr class="basic-line" />
            <p>
                En esta lista encontraran las clasificaciones de cuentas con las que cuenta nuestra plataforma.
            </p>            
        </div>
    </div>
    
    {% if page.items|length != 0 %}
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap text-right">            
            <a href="{{url('accountclassification/create')}}" class="button shining btn btn-sm success-inverted">Crear una nueva Clasificación</a>
            {{ partial('partials/pagination_static_partial', ['pagination_url': 'accountclassification/index']) }}
        </div>
    </div>
    
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
            <table class="table table-bordered">                
                <thead class="theader">
                    <tr>
                        <th>Nombre</th>                                                
                        <th>Espacio (Mb)</th>
                        <th>Mensajes</th>
                        <th>Contactos</th>
                        <th>SMS</th>
                        <th>Fecha de Expiración</th>                        
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    {% for item in page.items %}
                    <tr>
                        <td>{{item.name}}</td>                                                                                                
                        <td>{{item.fileSpace}}</td>
                        <td>{{item.mailLimit}}</td>
                        <td>{{item.contactLimit}}</td>
                        <td>{{item.smsLimit}}</td>                        
                        <td>{{item.expiryDate}}</td>                        
                        <td class="user-actions text-right">    
                            <a class="button shining btn btn-xs-round shining shining-round round-button primary-inverted" data-toggle="collapse" href="#collapseDetails{{item.idAccountclassification}}" aria-expanded="false" aria-controls="collapseDetails" id="details" data-placement="top" title="Ver detalles">
                                <span class="glyphicon glyphicon-collapse-down"></span>
                            </a>
                            <a href="{{url('accountclassification/edit')}}/{{item.idAccountclassification}}" class="button shining btn btn-xs-round shining shining-round round-button info-inverted" data-toggle="tooltip" data-placement="top" title="Editar esta Clasificación">
                                <span class="glyphicon glyphicon-pencil">
                            </a>
                            <button id="delete" onClick="openModal();" class="button shining btn btn-xs-round shining shining-round round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Borrar esta Clasificación" data-id="{{url('accountclassification/delete')}}/{{item.idAccountclassification}}">
                                <span class="glyphicon glyphicon-trash">
                            </button>
                        </td>
                    </tr>     
                    <tr class="collapse" id="collapseDetails{{item.idAccountclassification}}" >
                        <td colspan="7">
                            <table class="table table-bordered" style="width: 45%;" align="center">
                                <tbody>
                                    <tr>
                                        <td>
                                            <strong>Mta:</strong>
                                        </td>
                                        <td >
                                            {{item.mta.name}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <strong>Adaptador:</strong>
                                        </td>
                                        <td>
                                            {{item.adapter.fname}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <strong>Url Domain: </strong> 
                                        </td>
                                        <td>
                                            {{item.urldomain.name}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <strong>Mail Class: </strong>
                                        </td>
                                        <td>
                                            {{item.mailclass.name}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <strong>Agregar remitentes: </strong>
                                        </td>
                                        <td>
                                            {% if item.senderAllowed == 0 %}
                                                No
                                            {% else %}
                                                Si
                                            {% endif %}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <strong>Footer Editable: </strong>
                                        </td>
                                        <td>
                                            {% if item.footerEditable == 0 %}
                                                No
                                            {% else %}
                                                Si
                                            {% endif %}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <strong>Creado: </strong>
                                        </td>
                                        <td>
                                            {{date('d/m/Y g:i a', item.created)}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <strong>Actualizado: </strong>
                                        </td>
                                        <td>
                                            {{date('d/m/Y g:i a', item.updated)}}
                                        </td>
                                    </tr>
                                </tbody>                                                                                          
                            </table>
                        </td>
                    </tr>
                </tbody>
                    {% endfor %}                
            </table>            
        </div>    
    </div>
        
    <div class="row">
        {{ partial('partials/pagination_static_partial', ['pagination_url': 'accountclassification/index']) }}
    </div>
    
    {% else %}    
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
            <div class="block block-success">
                <div class="body success-no-hover text-center">
                    <h2>
                        No existen clasificaciones de cuentas creadas actualmente, si desea crear una haga <a href="{{url('accountclassification/create')}}">clic aquí</a>.
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
    
