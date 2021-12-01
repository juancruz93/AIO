{% block js %}
  <script>

    var objMail;

    function iframeResize() {
      var iFrame = document.getElementById('iframeEditor');
      iFrame.height = iFrame.contentWindow.document.body.scrollHeight + "px";
      //iFrame.height = "650px";
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
<div>
  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
      <div class="title">
        Nueva plantilla prediseñada
      </div>            
      <hr class="basic-line">
      <p class="text-justify">
        No es necesario que sepa desarrollar HTML, con el editor avanzado solo seleccione, arrastre, suelte
        elementos y organícelos de la manera en que los necesite en cuestión de segundos. El editor se encarga
        de transformar sus piezas en html que podrá ser visualizado en la mayoría de los gestores de correo
        como Gmail o Hotmail. Recuerde que al usar el editor avanzado tendrá contenido responsive que podrá ser
        visualizado correctamente en tamaños de pantalla mas reducidos (Ej. dispositivos móviles). (Estos servicios podrían tener un
        costo adicional).
      </p>
    </div>
  </div>


  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
      <div class="block block-info">
        <div class="body row">
          <div class="col-md-12">
            <div class="form-horizontal">
              <div class="form-group">
                <label for="namemailtempcat" class="col-sm-2 control-label">*Nombre de la plantilla</label>
                <div class="col-sm-9">
                  <input placeholder='Máximo 80 caracteres' required='' type="text" class="undeline-input form-control" id="namemailtempcat" name="namemailtempcat" maxlength="80" data-ng-model="namemailtempcat">
                  <div class="text-right" data-ng-class="namemailtempcat.length > 80 ? 'negative':''">{{"{{namemailtempcat.length > 0 ?  namemailtempcat.length+'/80':''}}"}}</div>
                </div>
              </div>
              <div class="form-group" data-ng-show="!newcategorytemplatemail">
                <label for="mailtempcat" class="col-sm-2 control-label">*Categoría</label>
                <div class="col-sm-9">
                  <select class="chosen form-control" data-ng-model="mailtempcat" style="width: 100%" required=''>
                    <option value=""></option>
                    <option ng-repeat="x in liscateg" value="{{"{{x.idMailTemplateCategory}}"}}">{{"{{x.name}}"}}</option>
                  </select>
                </div>
                <div class="col-sm-1 text-right">
                  <a class="positive tooltip-de" data-placement="top" title="Nueva categoría" href="" data-ng-click="newCateg()"><i class="fa fa-plus fa-2x" style="margin-right: 80%"></i></a>
                </div>
              </div>
              <div class="form-group" data-ng-show="newcategorytemplatemail">
                <label for="newmailtempcat" class="col-sm-2 control-label">Nueva categoría</label>
                <div class="col-sm-9">
                  <input type="text" class="undeline-input form-control" maxlength="80" data-ng-model="newmailtempcat">
                  <div class="text-right" data-ng-class="newmailtempcat.length > 80 ? 'negative':''">{{"{{newmailtempcat.length > 0 ?  newmailtempcat.length+'/80':''}}"}}</div>
                </div>
                <div class="col-sm-1 text-right">
                  <a class="negative tooltip-de" data-toggle="tooltip" data-placement="top" title="Cancelar" href="" data-ng-click="cancelCateg()"><i class="fa fa-times fa-2x"></i></a>
                  <a class="positive tooltip-de" data-toggle="tooltip" data-placement="top" title="Guardar" href="" data-ng-click="saveCateg()"><i class="fa fa-check fa-2x"></i></a>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="body row">
          <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
              <iframe id="iframeEditor" src="{{url('mail/editor_frame')}}" frameborder="0" width="100%" height="713px" onload="iframeResize();" seamless></iframe>
            </div>
          </div>
        </div>
        <div class="footer row none-margin">
          <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            <a href="{{ url('mailtemplate#/') }}"
               class="button btn btn-small danger-inverted">
              <i class="fa fa-times"></i> Salir sin guardar cambios
            </a>
          </div>
          <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 text-right">
            <a href="" onClick="htmlPreview();" class="button btn btn-small info-inverted tooltip-de"
               data-toggle="modal" data-target="#preview-modal">
              <i class="fa fa-eye"></i> Previsualizar
            </a>
            <a class="button btn btn-small primary-inverted" data-ng-click="saveMailTemplate(1)">
              <i class="fa fa-pencil-square-o" aria-hidden="true"></i> Guardar y seguir editando
            </a>
            <button type="submit" class="button btn btn-small success-inverted" data-ng-click="saveMailTemplate()">
              <i class="fa fa-save"></i> Guardar y salir
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="preview-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style=" z-index: 99999;">
  <div class="modal-dialog modal-prevew-width">
    <div class="modal-content modal-prevew-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h1 class="modal-title" id="myModalLabel">Previsualización</h1>
      </div>
      <div class="modal-body modal-prevew-body" id="modal-body-preview" style="height: 550px;"></div>
      <div class="modal-footer">
        <button type="button" data-dismiss="modal" class="button btn btn-sm danger-inverted">Cerrar</button>
      </div>
    </div>
  </div>
</div>
{% block footer %}
  {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}
  <script>
    $(function () {
      $('.tooltip-de').tooltip();
    $(".chosen").select2({
      placeholder: 'Seleccione una categoría'
    });
        //$(function () {
        $('#globalTemp').bootstrapToggle({
          on: 'Sí',
          off: 'No',
          onstyle: 'success',
          offstyle: 'danger',
          size: 'small'
        });
        //});
      });
  </script>
{% endblock %}
