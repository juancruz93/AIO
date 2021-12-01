{% extends "templates/default.volt" %}
{% block header %}
    {# Notifications #}
    {{ partial("partials/notifications_partial") }}
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
                Lista de Categorias de Envíos
            </div>            
            <hr class="basic-line" />
            <p>
                Aqui podran ver, crear, editar y eliminar las diferentes categorias de envíos.
            </p>            
        </div>
    </div>
    
    {% if page.items|length != 0 %}
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap text-right">            
            <a href="{{url('sendingcategory/create')}}" class="button shining btn btn-sm success-inverted">Crear una nueva Categoria</a>
            {{ partial('partials/pagination_static_partial', ['pagination_url': 'sendingcategory/index']) }}
        </div>
    </div>
    
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
            <table class="table table-bordered">                
                <thead class="theader">
                    <tr>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Creado</th>
                        <th>Actualizado</th>
                        <th></th>
                    </tr>
                </thead>                
                <tbody>    
                    {% for item in page.items %}
                        <tr>
                            <td>{{item.name}}</td>
                            <td>{{item.description}}</td>                        
                            <td>{{date('d/m/Y g:i a',item.created)}}</td>
                            <td>{{date('d/m/Y g:i a',item.updated)}}</td>

                            <td class="user-actions text-right">
                                <a href="{{url('sendingcategory/edit')}}/{{(item.idSendingcategory)}}" class="button shining btn btn-xs-round shining shining-round round-button info-inverted" data-toggle="tooltip" data-placement="top" title="Editar esta categoria">
                                    <span class="glyphicon glyphicon-pencil"></span>
                                </a>
                                <button id="delete" onClick="openModal();" class="button shining btn btn-xs-round shining shining-round round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Borrar esta Categoria" data-id="{{url('sendingcategory/delete')}}/{{item.idSendingcategory}}">
                                    <span class="glyphicon glyphicon-trash"></span>
                                </button>
                            </td>
                        </tr>                    
                    {% endfor %}
                </tbody>                
            </table>            
        </div>
    </div>
    
    <div class="row">
        {{ partial('partials/pagination_static_partial', ['pagination_url': 'sendingcategory/index']) }}
    </div>
    
    {% else %}
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
            <div class="block block-success">
                <div class="body success-no-hover text-center">
                    <h2>
                        No existen Categorias de Envíos creados actualmente, si desea crear una haga <a href="{{url('sendingcategory/create')}}">clic aquí</a>.
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
