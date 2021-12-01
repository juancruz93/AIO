<script>
  function htmlPreview(idMail) {
    $.post("{{url('mail/preview')}}/" + idMail, function (preview) {
      var e = preview.preview;
      $('<iframe id="frame" frameborder="0" />').appendTo('#modal-body-preview').contents().find('body').append(e);
    });
  }
</script>

<style>
  #modal-body-preview { width: 600px; height: 390px; padding: 0; overflow: hidden; display: inline-block;}
  #frame { width: 850px; height: 520px; /*border: 1px solid black;*/ }
  #frame { zoom: 0.75; -moz-transform: scale(0.75); -moz-transform-origin: 0 0; }
</style>

<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="subtitle">

      <em>Contenido del correo</em>
    </div>
    <br>
    <p class="small-text">
      Tienes varias opciones que puedes elegir para crear el contenido de tu correo, te recomendamos el editor
      avanzado, crearás contenido de manera rápida y fácil.
    </p>
  </div>
</div>

<div class="row" ng-cloak>
  <form name="contactlistForm" class="form-horizontal" role="form" ng-submit="addContactlist()">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 ">
      <div class="block block-info" ng-if="boolEditors">
        <div class="body row text-center">
          <div class="col-md-12 col-xs-12">
            <div class="col-xs-12 col-lg-3">
              <ul class="ch-grid ">
                <li>
                  <div class="ch-item edit-avanz pointer-cursor margin-botton">
                    <a href="{{ url('mail/contenteditor/') }}{{ '{{ idMail}}' }}">
                      <div class="ch-info">
                        <h3>Creación de contenido</h3>
                      </div>
                    </a>
                  </div>
                  <b>Creación de contenido</b>
                </li>
                <p class="text-justify margin-top">Crea el contenido desde cero de manera rápida y
                  {#              <p class="text-justify margin-top" style="text-align: center;">Crea el contenido desde cero de manera rápida y#}
                  fácil.</p>
              </ul>
            </div>
            <div class="col-xs-12 col-lg-3">
              <ul class="ch-grid">
                <li >
                  <div class="ch-item template pointer-cursor margin-botton">
                    <a href="{{url('mailtemplate#/select')}}/{{'{{ idMail}}'}}" class="text-center">
                      <div class="ch-info">
                        <h3>Plantillas prediseñadas</h3>
                      </div>
                    </a>
                  </div>
                  <b>Plantillas prediseñadas</b>
                </li>
                <p class="text-justify margin-top">Elige una plantilla como base para el contenido del
                  correo.</p>
              </ul>
            </div>
            <div class="col-xs-12 col-lg-3">
              <ul class="ch-grid">
                <li>

                  <div class="ch-item html-icon pointer-cursor margin-botton">
                    <a href="{{url('mail/htmlcontent')}}/{{'{{ idMail}}'}}" class="text-center" >
                      <div class="ch-info">
                        <h3>Editor de html</h3>
                      </div>
                    </a>
                  </div>
                  <b>Editor de html</b>

                </li>
                <p class="text-justify margin-top">Podrás crear contenido html desde código. Este contenido tiene algunas restricciones ya que se trata de un correo.</p>
              </ul>
            </div>
            <div class="col-xs-12 col-lg-3 text-center">
              <ul class="ch-grid">
                <li>
                  <div class="ch-item url-icon pointer-cursor margin-botton">
                    <a href="{{ url('mail/urleditor/') }}{{'{{ idMail}}'}}">
                      <div class="ch-info">
                        <h3>Importar desde un website</h3>
                      </div>
                    </a>
                  </div>
                  <b>Importar desde un website</b>
                </li>
                <p class="text-justify margin-top">Carga contenido html desde una página web y luego ajústalo desde el editor de html, algunas características del contenido podrían no estar disponibles.</p>
              </ul>
            </div>
          </div>
        </div>
        <div class="footer" align="right">
          <a href="{{ url('mail') }}"
             class="button btn btn-small danger-inverted"
             data-toggle="tooltip" data-placement="top" title="Salir">
            Salir
          </a>
          <a ui-sref="addressees({id:idMailGet})"
             class="button btn btn-small info-inverted"
             data-toggle="tooltip" data-placement="top" title="Atrás">
            Atrás
          </a>
          <button type="submit"
                  class="button btn btn-small primary-inverted"
                  data-toggle="tooltip" data-placement="top" title="Guardar y continuar">
            Guardar y continuar
          </button>
        </div>
      </div>
      <div class="block block-info " ng-show="!boolEditors">
        <div class="container-fluid">
          <div class="body row">
            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 col-md-offset-3">
              <a href="{{ url('{{ getContent.url }}') }}/{{ '{{ idMail}}' }}" class="text-center">
                <h3>Click aquí para editar contenido</h3>
              </a>
            </div>
          </div>
          <div class="body row">
            <div class="col-lg-6 col-lg-offset-3 text-center">
              {#<div id="modal-body-preview"></div>#}
              <img src="{{url('')}}{{"{{urlThumbnail}}"}}?{{"{{imagenTime}}"}}"/>
            </div>
          </div>
          <div class="body row">
            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 col-md-offset-3">
              <a style="cursor:pointer;" class="text-center" ng-click="openModal()">
                <h3>Ver texto plano</h3>
              </a>
            </div>
          </div>
        </div>
        <div class="footer" align="right">
          <a href="{{ url('mail') }}"
             class="button btn btn-small danger-inverted"
             data-toggle="tooltip" data-placement="top" title="Salir">
            Salir
          </a>
          <a ui-sref="addressees({id:idMailGet})"
             class="button btn btn-small info-inverted"
             data-toggle="tooltip" data-placement="top" title="Atrás">
            Atrás
          </a>
          <a ui-sref="advanceoptions({id:idMailGet})"
             class="button btn btn-small primary-inverted"
             data-toggle="tooltip" data-placement="top" title="Guardar y continuar">
            Guardar y continuar
          </a>
        </div>
      </div>
    </div>
  </form>
</div>
<div id="somedialog" class="dialog ">
  <div class="dialog__overlay"></div>
  <div class="dialog__content">
    <div class="morph-shape ">
      <svg xmlns="https://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 560 280"
           preserveAspectRatio="none">
      <rect x="3" y="3" fill="none" width="556" height="276"/>
      </svg>
    </div>
    <div class="dialog-inner dialog-padding">
      <p ng-show="PlaneError.error == 1">{{ '{{PlaneError.msg}}' }}</p>
      <div class="body row text-center">
        <h3>Editar Texto plano</h3>
      </div>
      <div class="body row">
        <textarea ng-model="setPlane.content" class="form-control" rows="8" cols="11"
                  style="resize: none;"></textarea>
      </div>
      <div class="body row pull-right" style="padding-top: 1em;">
        <a ng-click="closeModal()" class="button shining btn btn-md danger-inverted"
           data-dialog-close>Cancelar</a>
        <a ng-click="addplaintext()" id="btn-ok" class="button shining btn btn-md success-inverted">Guardar</a>
      </div>
    </div>
  </div>
</div>
