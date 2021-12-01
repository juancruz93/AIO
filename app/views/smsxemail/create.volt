<div ng-cloak>
  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
      <div class="title">
        Configuración de Email para envíos de SMS.
      </div>
      <p>
        Se debe registrar el correo del remitente, que enviará el Email, el cual será reconocido, para realizar los envíos de SMS.
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap ">
      <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 text-right pull-right">
        <a href="{{ url('tools') }}" class="button shining btn btn-sm default-inverted">Regresar</a>
      </div>
    </div>
  </div>
  
  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-8 wrap">
      <form name="smsxemailForm" class="form-horizontal" role="form" ng-submit="functions.validate()">
        <div class="block block-info">
          <div class="body">
            
            <div class="form-group">
              <label for="senderEmail" class="col-sm-3 control-label">*Correo del remitente:</label>
              <div class="col-sm-9">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                  <div class="form-horizontal">
                    <input class="undeline-input" ng-model="data.senderEmail" id="senderEmail" placeholder="*Correo del remitente" ng-disabled="misc.isDisabled">
                  </div>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-3 control-label">*Generar Clave:</label>
              <div class="col-sm-6">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                  <div class="form-horizontal">
                    <span class="input-append date add-on input-group none-padding">
                      <input id="generateKey" class="undeline-input js-copytextarea" ng-model="data.generateKey" placeholder="*Generar Clave" disabled="">
                      <span class="add-on input-group-addon" data-ng-show="misc.generate">
                        <a href="" ng-click="functions.generateKey()" class="glyphicon glyphicon-refresh" title="Generar Clave"></a>
                      </span>
                      <span class="add-on input-group-addon" data-ng-show="misc.copy">
                        <a href="" ng-click="functions.copyKey(data.idSmsxEmail)" class="fa fa-copy" title="Copiar Clave" id="btnCopy"></a>
                      </span>
                    </span>
                  </div>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-3 control-label">*Categoria SMS:</label>
              <div class="col-sm-6">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                  <select ng-model="data.idSmsCategory" class="undeline-input" ng-disabled="misc.isDisabled" required>
                    <option ng-repeat="SmsCategory in misc.smsCategory"
                            value="{{"{{SmsCategory.idSmsCategory}}"}}">
                      {{"{{SmsCategory.name}}"}}
                    </option>
                  </select> 
                </div>
              </div>
            </div>
            <div class="form-group">
              <label for="notificationEmail" class="col-sm-3 control-label">*Correo de notificación:</label>
              <div class="col-sm-9">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                  <div class="form-horizontal">
                    <input class="undeline-input" ng-model="data.notificationEmail" id="notificationEmail" placeholder="*Correo de notificación" ng-disabled="misc.isDisabled">
                  </div>
                </div>
              </div>
            </div>      
          </div>
          
          <div class="footer" align="right" data-ng-show="misc.showSave">
            <button type="submit" class="button shining btn btn-xs-round shining shining-round round-button success-inverted" data-toggle="tooltip" data-placement="top" title="Guardar Configuración">
              <span class="glyphicon glyphicon-ok"></span>
            </button>
            <a href="{{ url('tools') }}"  class="button shining btn btn-xs-round shining shining-round round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Cancelar Configuración">
              <span class="glyphicon glyphicon-remove"></span>
            </a> 
          </div>           
        </div>
      </form>
    </div>

    <div class="modal fade linkgen" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content bg-success">
          <div class="modal-body">
            <div class="row">
              <div class="col-sm-12">
                <p class="small-text">
                  Esta es la clave única, que debe colocar, en la estructura del envió de Email.
                <div class="form-group">
                  <div class="col-sm-10">
                    <input type="text" id="link" class="form-control" readonly="true" data-ng-model="linksurv" />
                  </div>
                  <div class="col-sm-2">
                    <button type="button" class="btn btn-info" id="buttonCopy">
                      <i class="fa fa-copy"></i> Copiar
                    </button>
                  </div>
                </div>
                </p>
              </div>
            </div>
            <br>
            <div class="row">
              <div class="col-sm-12 text-center">
                <button type="button" class="btn danger-inverted" data-dismiss="modal">Cerrar</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>    
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-4 wrap">
      <div class="fill-block fill-block-primary" >
        <div class="header">
          Información
        </div>
        <div class="body">
          <p>
            Recuerde tener en cuenta las siguientes recomendaciones: 
          <ul>
            <li>
              Todos los campos son requeridos.
            </li>
            <li>
              Correo del remitente, es por medio del cual, se realizarán los envíos de SMS.
            </li>
            <li>
              La clave se genera una sola vez y será usada en el contenido del email, para realizar los envíos de SMS.
            </li>            
            <li>
              Seleccione una categoría de SMS, que será usada internamente para ejecutar él envió del SMS.
            </li>            
            <li>
              Correo de notificación, que será usado para notificar una vez termine el envío de la campaña de SMS.
            </li>            
          </ul>
          </p>
        </div>
      </div>
    </div>
  </div>            
</div>
<script>
  $(function () {
    setTimeout(function () {
      $('[data-toggle="tooltip"]').tooltip();
    }, 1000);
  });
  function openModal() {
    $('.dialog').addClass('dialog--open');
  }

  function closeModal() {
    $('.dialog').removeClass('dialog--open');
  }
</script>