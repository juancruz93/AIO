<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="title">
      Base del conocimiento
    </div>            
    <hr class="basic-line" />
    <p>
      Validación de correos desde CSV
    </p>
  </div>
</div>

<div class="row ">

  <form method="post" class="form-horizontal" enctype="multipart/form-data" ng-submit="importcsv()">
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">
      <div class="block block-info">
        <div class="header">
          <span class="small-text">Validar correos electrónicos desde archivo .csv</span>
        </div>
        <div class="body">
          <div>
            <input ng-disabled="dis" type="file" nv-file-select="" uploader="uploader" multiple /><br/>
          </div>
          <div  style="margin-bottom: 40px" ng-show="uploader.queue.length > 0">
{#            <p>Cantidad de archivos seleccionados: {{"{{ uploader.queue.length }}"}}</p>#}
            <table class="table table-bordered"  >
              <thead>
                <tr>
                  <th width="50%">Nombre</th>
                  <th ng-show="uploader.isHTML5">Tamaño</th>
                  <th ng-show="uploader.isHTML5">Progreso</th>
                  <th>Estado</th>
                    {#<th>Acciones</th>#}
                </tr>
              </thead>
              <tbody>
{#                <tr ng-repeat="item in uploader.queue">#}
                <tr>
                  <td><strong>{{"{{ uploader.queue[uploader.queue.length-1].file.name }}"}}</strong></td>
                  <td ng-show="uploader.isHTML5" nowrap>{{"{{ uploader.queue[uploader.queue.length-1].file.size/1024/1024|number:2 }}"}} MB</td>
                  <td ng-show="uploader.isHTML5">
                    <div class="progress" style="margin-bottom: 0;">
                      <div class="progress-bar" role="progressbar" ng-style="{ 'width': uploader.queue[uploader.queue.length-1].progress + '%' }"></div>
                    </div>
                  </td>
                  <td class="text-center">
                    <span ng-show="uploader.queue[uploader.queue.length-1].isSuccess"><i class="glyphicon glyphicon-ok"></i></span>
                    <span ng-show="uploader.queue[uploader.queue.length-1].isCancel"><i class="glyphicon glyphicon-ban-circle"></i></span>
                    <span ng-show="uploader.queue[uploader.queue.length-1].isError"><i class="glyphicon glyphicon-remove"></i></span>
                  </td>
                  {#<td nowrap>
                  <button type="button" class="btn success-inverted btn-xs" ng-click="item.upload()" ng-disabled="item.isReady || item.isUploading || item.isSuccess">
                  <span class="glyphicon glyphicon-upload"></span> Adjuntar
                  </button>
                  <button type="button" class="btn btn-warning btn-xs" ng-click="item.cancel()" ng-disabled="!item.isUploading">
                  <span class="glyphicon glyphicon-ban-circle"></span> Cancelar
                  </button>
                  <button type="button" class="btn btn-danger btn-xs" ng-click="item.remove()">
                  <span class="glyphicon glyphicon-trash"></span> Remover
                  </button>
                  </td>#}
                </tr>
              </tbody>
            </table>

            <div>
              <div>

                {#<div class="progress" style="">
                <div class="progress-bar" role="progressbar" ng-style="{ 'width': uploader.progress + '%' }"></div>
                </div>#}
              </div>
              {#<button type="button" class="btn btn-success btn-s" ng-click="uploader.uploadAll()" ng-disabled="!uploader.getNotUploadedItems().length">
               <span class="glyphicon glyphicon-upload"></span> Adjuntar todos
               </button>
               <button type="button" class="btn btn-warning btn-s" ng-click="uploader.cancelAll()" ng-disabled="!uploader.isUploading">
               <span class="glyphicon glyphicon-ban-circle"></span> Cancelar todos
               </button>
               <button type="button" class="btn btn-danger btn-s" ng-click="uploader.clearQueue()" ng-disabled="!uploader.queue.length">
               <span class="glyphicon glyphicon-trash"></span> Remover todos
               </button>#}
            </div>

          </div>
    {#      <div class="form-group">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
              <label class="col-xs-1 col-sm-1 col-md-1 small-text adjust-asterisk">*</label>
              <span class="input hoshi input-default col-xs-11 col-sm-11 col-md-11">
                <input name="filecsv" type="file" class="undeline-input" accept=".csv" required>
              </span>
            </div>
          </div>#}
        </div>
        <div class="footer" align="right">
          <button id="submit" ng-disabled="dis" type="submit" class="button shining btn btn-xs-round shining shining-round round-button success-inverted" data-toggle="tooltip" data-placement="top" title="Cargar">
            <span class="glyphicon glyphicon-ok"></span>
          </button>
          <a href="#/" ng-disabled="dis" class="button shining btn btn-xs-round shining shining-round round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Cancelar">
            <span class="glyphicon glyphicon-remove"></span>
          </a>
        </div>
      </div>
    </div>
  </form>

  <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">
    <div class="fill-block fill-block-info" >
      <div class="header">
        Información
      </div>
      <div class="body">
        <p>
          Recuerde tener en cuenta estas recomendaciones:
        <ul>
          <li>Los programas de hojas de cálculo como Microsoft Excel u OpenOffice.org Calc permiten crear y editar archivos CSV fácilmente.</li>
          <li>El formato del archivo CSV debe ser "correo;estado;" obligatoriamente por cada una de las filas del archivo</li>
          <li>Recuerde que los campos con asterisco(*) son oblogatorios</li>
        </ul>
        </p>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap text-right">
    <a href="{{url('knowledgebase#/')}}" ng-disabled="dis" class="button shining btn btn-sm default-inverted">Regresar</a>
  </div>
</div>

<script>
  function openModal() {
    $('.dialog').addClass('dialog--open');
  }

  function closeModal() {
    $('.dialog').removeClass('dialog--open');
  }


</script>

</div>
</div>

