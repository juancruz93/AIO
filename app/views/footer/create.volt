{% extends "templates/default.volt" %}
{% block header %}
    {# Notifications #}
    {{ partial("partials/slideontop_notification_partial") }}

    {# Dialogs #}
    {{ stylesheet_link('library/dialog-effects/css/dialog.css') }}
    {{ stylesheet_link('library/dialog-effects/css/dialog-wilma.css') }}
    {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}
    {{ stylesheet_link('library/ui-select-master/dist/select.css') }}

{% endblock %}

{% block js %}
    {{ javascript_include('js/angular/footer/app.js') }}
    {{ javascript_include('js/angular/footer/controllers.js') }}
    {{ javascript_include('js/angular/footer/directives.js') }}
    {{ javascript_include('js/angular/footer/services.js') }}

    <script type="text/javascript">
        objMail = "Footer";
        var config = {imagesUrl: "{{url('images/editor')}}", templateUrl: "{{url('template/create')}}"};

        function iframeResize() {
            var iFrame = document.getElementById('iframeEditor');
            iFrame.height = "650px";
        };

        function verHTML() {
            var editor = document.getElementById('iframeEditor').contentWindow.catchEditorData();
            $.ajax({
                url: "{{url('footer/previeweditor')}}",
                type: "POST",
                data: { editor: editor},
                error: function(msg){
                    $.gritter.add({class_name: 'error', title: '<i class="icon-warning-sign"></i> Atención', text: msg, sticky: false, time: 10000});
                },
                success: function() {
                    $( "#modal-body-preview" ).empty();
                    $('#modal-body-preview').append($('<iframe frameborder="0" id="footer-preview" width="100%" height="100%" src="{{url('footer/previewdata')}}"/>'));
                }
            });
            document.getElementById('iframeEditor').contentWindow.RecreateEditor();
        };

    </script>

{% endblock %}

{% block content %}
    <div class="clearfix"></div>
    <div class="space"></div>
    <div ng-app="footer">

        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <div class="title">
                    Crear Footer
                </div>
                <hr class="basic-line"/>
                <p class="small-text text-justify">
                    Cree footers (Píes de página) que usarán las cuentas en la creación de contenido de correo. Puede configurar para que las cuentas no puedan cambiar o eliminar estos footers aprovechando para hacer publicidad de la marca.
                </p>
            </div>
        </div>

        <div class="row wrap" data-ng-controller="createController">
            <form name="contactlistForm" class="form-horizontal" role="form" ng-submit="saveFooter()">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 ">
                    <div class="block block-info">
                        <div class="body">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-right">
                                <label class="col-md-2">{{ footerForm.label('name') }}</label>
                                <span class="input hoshi input-default col-sm-10 padding-right-25px">
                                    {{ footerForm.render('name') }}
                                </span>
                            </div>

                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 margin-top-45px text-right">
                                <label class="col-md-2">{{ footerForm.label('description') }}</label>
                                <span class="input hoshi input-default col-md-10 padding-right-25px">
                                    {{ footerForm.render('description') }}
                                </span>
                            </div>
                            <div class="row">
                                <iframe id="iframeEditor" src="{{url('mail/editor_frame')}}" width="107%" onload="iframeResize();" frameborder="0" seamless></iframe>
                            </div>
                        </div>
                        <div class="footer row none-margin" >
                            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 text-left">
                                <a href="" class="button shining btn btn-xs-round shining shining-round round-button default-inverted"
                                   data-toggle="modal" data-target="#preview-modal" onClick="verHTML();" data-placement="top" title="Previsualizar">
                                    <span class="glyphicon glyphicon-eye-open"></span>
                                </a>
                            </div>
                            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 text-right">
                                <button type="submit"
                                        class="button shining btn btn-xs-round shining shining-round round-button success-inverted"
                                        data-toggle="tooltip" data-placement="top" title="Guardar">
                                    <span class="glyphicon glyphicon-ok"></span>
                                </button>
                                <a href="{{ url('footer') }}"
                                   class="button shining btn btn-xs-round shining shining-round round-button danger-inverted"
                                   data-toggle="tooltip" data-placement="top" title="Cancelar">
                                    <span class="glyphicon glyphicon-remove"></span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="modal fade " id="preview-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-prevew-width">
                <div class="modal-content modal-prevew-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h1 class="modal-title" id="myModalLabel">Previsualización</h1>
                    </div>
                    <div class="modal-body modal-prevew-body" id="modal-body-preview" style="height: 550px;"></div>
                    <div class="modal-footer">
                        <button type="button" data-dismiss="modal" class="button fill btn btn-sm danger-inverted">Cerrar</button>
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
        var templateBase = "footer";
    </script>

{% endblock %}
