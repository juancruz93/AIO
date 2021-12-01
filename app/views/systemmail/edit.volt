{% extends "templates/default.volt" %}
{% block header %}
  {# {{ partial("partials/slideontop_notification_partial") }} #}

  <script type="text/javascript">
    function iframeResize() {
      var iFrame = document.getElementById('iframeEditor');
      //iFrame.height = iFrame.contentWindow.document.body.scrollHeight + "px";
      iFrame.height = "700px";
    }
    ;

    var objMail = {{smail.content}};
            function sendData() {
              try {
                var editor = document.getElementById('iframeEditor').contentWindow.catchEditorData();
                var name = $('#name').val();
                var desc = $('#description').val();
                var category = $('#category').val();
                var subject = $('#subject').val();
                var fromEmail = $('#fromEmail').val();
                var fromName = $('#fromName').val();

                if (name === null || name === '' || name === undefined) {
                  throw "El campo nombre se encuentra vacío, por favor valide la información";
                }
                if (desc === null || desc === '' || desc === undefined) {
                  throw "El campo descripción se encuentra vacío, por favor valide la información";
                }
                if (category === null || category === '' || category === undefined) {
                  throw "El campo categoría se encuentra vacío, por favor valide la información";
                }
                if (subject === null || subject === '' || subject === undefined) {
                  throw "El campo asunto se encuentra vacío, por favor valide la información";
                }
                if (fromEmail === null || fromEmail === '' || fromEmail === undefined) {
                  throw "El campo correo de remitente se encuentra vacío, por favor valide la información";
                }
                if (fromName === null || fromName === '' || fromName === undefined) {
                  throw "El campo nombre de remitente se encuentra vacío, por favor valide la información";
                }

                $.ajax({
                  url: "{{url('systemmail/edit')}}/{{smail.idSystemmail}}",
                  type: "POST",
                  data: {
                    name: name,
                    desc: desc,
                    category: category,
                    editor: editor,
                    subject: subject,
                    fromEmail: fromEmail,
                    fromName: fromName,
                  },
                  error: function (msg) {
                    slideOnTop(msg, 3500, 'glyphicon glyphicon-remove', 'danger');
                  },
                  success: function () {
                    $(location).attr('href', "{{url('systemmail')}}");
                  }
                });
              }
              catch (err) {
                slideOnTop(err, 3500, 'glyphicon glyphicon-remove', 'danger');
                document.getElementById('iframeEditor').contentWindow.RecreateEditor();
              }
            }

    function save() {
      try {
        var editor = document.getElementById('iframeEditor').contentWindow.catchEditorData();
        var name = $('#name').val();
        var desc = $('#description').val();
        var category = $('#category').val();
        var subject = $('#subject').val();
        var fromEmail = $('#fromEmail').val();
        var fromName = $('#fromName').val();

        if (name === null || name === '' || name === undefined) {
          throw "El campo nombre se encuentra vacío, por favor valide la información";
        }
        if (desc === null || desc === '' || desc === undefined) {
          throw "El campo descripción se encuentra vacío, por favor valide la información";
        }
        if (category === null || category === '' || category === undefined) {
          throw "El campo categoría se encuentra vacío, por favor valide la información";
        }
        if (subject === null || subject === '' || subject === undefined) {
          throw "El campo asunto se encuentra vacío, por favor valide la información";
        }
        if (fromEmail === null || fromEmail === '' || fromEmail === undefined) {
          throw "El campo correo de remitente se encuentra vacío, por favor valide la información";
        }
        if (fromName === null || fromName === '' || fromName === undefined) {
          throw "El campo nombre de remitente se encuentra vacío, por favor valide la información";
        }

        $.ajax({
          url: "{{url('systemmail/edit')}}/{{smail.idSystemmail}}",
          type: "POST",
          data: {
            name: name,
            desc: desc,
            category: category,
            editor: editor,
            subject: subject,
            fromEmail: fromEmail,
            fromName: fromName,
          },
          error: function (msg) {
            slideOnTop(msg, 3500, 'glyphicon glyphicon-remove', 'danger');
          },
          success: function () {
            slideOnTop('Se han guardado los cambios exitosamente', 3500, 'glyphicon glyphicon-exclamation-sign', 'primary');
            document.getElementById('iframeEditor').contentWindow.RecreateEditor();
          }
        });
      }
      catch (err) {
        slideOnTop(err, 3500, 'glyphicon glyphicon-remove', 'danger');
        document.getElementById('iframeEditor').contentWindow.RecreateEditor();
      }
    }

    function htmlPreview() {
      var editor = document.getElementById('iframeEditor').contentWindow.catchEditorData();
        $.ajax({
        url: "{{url('mailpreview/preview')}}",
        type: "POST",
        data: {
          editor: editor
        },
        error: function (msg) {
          slideOnTop(msg, 3500, 'glyphicon glyphicon-remove', 'danger');
        },
        success: function () {
          $("#modal-body-preview").empty();
          $('#modal-body-preview').append($('<iframe frameborder="0" width="100%" height="100%" src="{{url('mailpreview/previewdata')}}"/>'));
        }
      });

      document.getElementById('iframeEditor').contentWindow.RecreateEditor();
    }
  </script>    
{% endblock %}

{% block content %}
  <div class="row wrap">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

    </div>
  </div>

  <div class="row wrap">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-offset-2 col-lg-8">
      <div class="form-horizontal">
        <div class="block block-info" style="border-bottom-color: transparent;border-bottom-left-radius: 0;border-bottom-right-radius: 0;">          
          <div class="body">
            <div class="form-group">
              <div class="col-md-12 ">
                <label class="col-sm-3 col-md-3 ">*Nombre:</label>
                <span class="input hoshi input-default col-sm-9 col-md-9">
                  <input type="text" value="{{smail.name}}" id="name" name="name" class="undeline-input" required autofocus>                                    
                </span>
              </div>       
            </div>

            <div class="form-group">
              <div class="col-md-12">
                <label class="col-sm-3 col-md-3 ">*Descripción:</label>
                <span class="input hoshi input-default col-sm-9 col-md-9">
                  <textarea class="undeline-input" rows="2" id="description" name="description" required="required">{{smail.description}}</textarea>
                </span>
              </div>       
            </div>

            <div class="form-group">
              <div class="col-md-12 ">
                <label class="col-sm-3 col-md-3 ">*Categoría:</label>
                <span class="input hoshi input-default col-sm-9 col-md-9">
                  <input type="text" value="{{smail.category}}"  id="category" name="category" class="undeline-input" required="required">               
                </span>
              </div>       
            </div> 

            <div class="form-group">
              <div class="col-md-12 ">
                <label class="col-sm-3 col-md-3 ">*Asunto:</label>
                <span class="input hoshi input-default col-sm-9 col-md-9">
                  <input type="text" id="subject" value="{{smail.subject}}" name="subject" class="undeline-input" maxlength="80" required="required">                 
                </span>
                
              </div>       
            </div> 

            <div class="form-group">
              <div class="col-md-12 ">
                <label class="col-sm-3 col-md-3 ">*Nombre del remitente:</label>
                <span class="input hoshi input-default col-sm-9 col-md-9">
                  <input type="text" value="{{smail.fromName}}" id="fromName" name="fromName" class="undeline-input" required="required">             
                </span>
              </div>       
            </div> 

            <div class="form-group">
              <div class="col-md-12 ">
                  <label class="col-sm-3 col-md-3 ">*Correo del remitente:</label>
                <span class="input hoshi input-default col-sm-9 col-md-9">
                  <input type="email" value="{{smail.fromEmail}}" id="fromEmail" name="fromEmail" class="undeline-input" required="required">                       
                </span>
              </div>       
            </div> 
          </div>
        </div>
      </div>
    </div>    
  </div>
  <div class="row" >
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center" >
      <h4><b>Recuerde las siguientes etiquetas para crear su contenido escrito personalizado</b></h4>
       <table  class="wrapped confluenceTable" style="margin-left: auto;margin-right: auto;" data-mce-style="margin-left: auto; margin-right: auto;"><colgroup><col><col></colgroup>
        <tbody>
          <tr>
            <th class="confluenceTh" style="text-align: left;" data-mce-style="text-align: left;">
              Etiqueta
            </th>
            <th class="confluenceTh" style="text-align: left;" data-mce-style="text-align: left;">
              Valor
            </th>
          </tr>
          <tr>
            <td class="confluenceTd" style="text-align: left;" data-mce-style="text-align: left;">
              %NAME_<span>SENT</span>%
            </td>
            <td class="confluenceTd" style="text-align: left;" data-mce-style="text-align: left;">
              Nombre del envío
            </td>
          </tr>
          <tr>
            <td class="confluenceTd" style="text-align: left;" colspan="1" data-mce-style="text-align: left;">
              %DATETIME_<span>SENT</span>%
            </td>
            <td class="confluenceTd" style="text-align: left;" colspan="1" data-mce-style="text-align: left;">
              Fecha y hora del envío
            </td>
          </tr>
          <tr>
            <td class="confluenceTd" style="text-align: left;" colspan="1" data-mce-style="text-align: left;">
              %LINK_COMPLETE_<span>SENT</span>%
            </td>
            <td class="confluenceTd" style="text-align: left;" colspan="1" data-mce-style="text-align: left;">
              Enlace de las estadísticas completas del correo electrónico
            </td>
          </tr>
          <tr>
            <td class="confluenceTd" style="text-align: left;" colspan="1" data-mce-style="text-align: left;">
              %LINK_SUMMARY_<span>SENT</span>%
            </td>
            <td class="confluenceTd" style="text-align: left;" colspan="1" data-mce-style="text-align: left;">
              <span>Enlace de las estadísticas parciales del correo electrónico</span>
            </td>
          </tr>
          <tr>
            <td class="confluenceTd" style="text-align: left;" colspan="1" data-mce-style="text-align: left;">
              %TOTAL_<span>SENT</span>%
            </td>
            <td class="confluenceTd" style="text-align: left;" colspan="1" data-mce-style="text-align: left;">
              <span>Número total de envíos</span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
      <iframe id="iframeEditor" src="{{url('mail/editor_frame')}}" frameborder="0" width="100%" onload="iframeResize();" seamless></iframe>
    </div>
  </div>

  <div class="row wrap">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-offset-2 col-lg-8">
      <div class="form-horizontal">
        <div class="block block-info" style="border-top-color: transparent;border-top-left-radius: 0;border-top-right-radius: 0;">          
          <div class="body">

          </div>
          <div class="footer" align="right">
            <button onClick="sendData();" class="button shining btn btn-xs-round shining shining-round round-button success-inverted" data-toggle="tooltip" data-placement="top" title="" data-original-title="Guardar y salir">
              <span class="glyphicon glyphicon-ok"></span>
            </button>
            <button onClick="save();" class="button shining btn btn-xs-round shining shining-round round-button primary-inverted" data-toggle="tooltip" data-placement="top" title="" data-original-title="Guardar">
              <span class="glyphicon glyphicon-floppy-disk"></span>
            </button>
            <button onClick="htmlPreview();" class="button shining btn btn-xs-round shining shining-round round-button info-inverted" data-toggle="modal" data-target="#preview-modal">
              <span class="glyphicon glyphicon-eye-open"></span>
            </button>
            <a href="{{url('systemmail')}}" class="button shining btn btn-xs-round shining shining-round round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="" data-original-title="Cancelar">
              <span class="glyphicon glyphicon-remove"></span>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="preview-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-prevew-width">
      <div class="modal-content modal-prevew-content modal-prevew-width"W>
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title" id="myModalLabel">Previsualización de correo</h4>
        </div>
        <div class="modal-body modal-prevew-body" id="modal-body-preview" style="height: 375px;"></div>
        <div class="modal-footer">
          <button type="button" data-dismiss="modal" class="button fill btn btn-sm danger">Cerrar</button>
        </div>
      </div>
    </div>
  </div>
{% endblock %}
