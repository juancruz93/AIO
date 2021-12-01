{% extends "templates/default.volt" %}
{% block header %}
    {# Notifications #}
    {{ partial("partials/notifications_partial") }}
    
    {# Dialogs #}
    {{ stylesheet_link('library/dialog-effects/css/dialog.css') }}
    {{ stylesheet_link('library/dialog-effects/css/dialog-wilma.css') }}
    {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}
    
    <script type="text/javascript">
        function saveChanges() {
            var configData = $('#configData').val();
            $.ajax({
                data:  {configData: configData},
                url:   "{{url('system/configure')}}",
                type:  "post",
                error: function(msg){
                    var response = JSON.parse(msg.responseText);
                    slideOnTop(response.msg, 6000, 'glyphicon glyphicon-warning-sign','danger');
                },
                success: function(){
                    $(location).attr('href', "{{url('system')}}"); 
                }
            });
        }
    </script>
{% endblock %}
{% block content %}
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
            <div class="clearfix"></div>
            <div class="space"></div>            
            
            <div class="title">
                Editar archivo de configuración del sistema
            </div>   
            
            <hr class="basic-line" />
            
            <p>
                El archivo de configuración del sistema, es un archivo con extensión .ini, llamado configuration.ini y se utiliza como base del funcionamiento de la plataforma.
                En este archivo se configura el acceso a la base de datos, configuración de MTA, paths, status de la plataforma, peso máximo de archivos subidos, etc.
            </p>
            <p>
                Este archivo es muy delicado y cualquier cambio afectará a toda la plataforma, por favor editelo solo si es necesario y si está seguro.
            </p>
        </div>
    </div>
	
     <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
            <textarea class="form-control" rows="17" id="configData">{{config}}</textarea>
        </div>    
    </div>    
            
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap text-right">
            <a href="{{url('system/index')}}" class="button shining shining-round btn btn-xs-round danger-inverted round-button" data-toggle="tooltip" data-placement="top" title="Cancelar">
                <span class="glyphicon glyphicon-remove"></span>
            </a>
            
            <button class="button shining shining-round btn btn-xs-round success-inverted round-button trigger" data-dialog="confirm" data-toggle="tooltip" data-placement="top" title="Guardar cambios">
                <span class="glyphicon glyphicon-ok"></span>
            </button>
        </div>    
    </div>        
           
    <div id="confirm" class="dialog">
        <div class="dialog__overlay"></div>
        <div class="dialog__content">
            <div class="morph-shape">
                <svg xmlns="https://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 560 280" preserveAspectRatio="none">
                    <rect x="3" y="3" fill="none" width="556" height="276"/>
                </svg>
            </div>
            <div class="dialog-inner">
                <h2>¿Esta seguro que quiere editar el archivo de configuración del sistema?</h2> 
                <div>
                    <button class="action button shining shining btn btn-sm danger-inverted" data-dialog-close>Cancelar</button>
                    <button class="action button shining shining btn btn-sm success-inverted" id="confirm-button" data-dialog-close>Editar</button>
                </div>
            </div>
        </div>
    </div>            
    	
    <script type="text/javascript">
        $(function() {
            $('#confirm-button').on('click', function() {
                saveChanges();
                $(".dialog").removeClass('dialog--open');
            });
        });
        
        (function() {

            var dlgtrigger = document.querySelector( '[data-dialog]' ),
            somedialog = document.getElementById( dlgtrigger.getAttribute( 'data-dialog' ) ),
            dlg = new DialogFx( somedialog );

            dlgtrigger.addEventListener( 'click', dlg.toggle.bind(dlg) );
            
        })();
    </script>
{% endblock %}
