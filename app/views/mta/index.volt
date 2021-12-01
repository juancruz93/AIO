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
                Lista de MTA'S
            </div>            
            <hr class="basic-line" />
            <p>
                Los Mail Transport Agent o MTA virtuales son las rutas que se utilizan para el envío de email
                entre dos servidores de correo, estos tienen asignada una o varias direcciones IP para realizar 
                dicho proceso. Adicionalmente estos MTA cuentan con caracteristicas como: capacidad de envío/hora,
                reputación y reglas de envío.
            </p>
            <p>
                En esta lista encontraran los MTA virtuales con los que cuenta nuestra plataforma.
            </p>            
        </div>
    </div>
    
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap text-right">            
            <a href="{{url('system')}}" class="button shining btn default-inverted">Regresar</a>
            <a href="{{url('mta/create')}}" class="button shining btn success-inverted">Crear un nuevo MTA</a>
        </div>
    </div>       
    
    {% if page.items|length != 0 %}
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap text-right">
            {{ partial('partials/pagination_static_partial', ['pagination_url': 'mta/index']) }}
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
                            <a href="{{url('mta/edit')}}/{{item.idMta}}" class="button shining btn btn-xs-round shining shining-round round-button info-inverted" data-toggle="tooltip" data-placement="top" title="Editar este MTA">
                                <span class="glyphicon glyphicon-pencil">
                            </a>
         {#                   <button id="delete" onClick="openModal();" class="button shining btn btn-xs-round shining shining-round round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Borrar este MTA" data-id="{{url('mta/delete')}}/{{item.idMta}}">
                                <span class="glyphicon glyphicon-trash">
                            </button>#}
                        </td>
                    </tr>                    
                </tbody>
                {% endfor %}
            </table>            
        </div>    
    </div>
    
    <div class="row">
        {{ partial('partials/pagination_static_partial', ['pagination_url': 'mta/index']) }}
    </div>
    
    {% else %}
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
            <div class="block block-success">
                <div class="body success-no-hover text-center">
                    <h2>
                        No existen MTA creados actualmente, si desea crear una haga <a href="{{url('mta/create')}}">clic aquí</a>.
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
