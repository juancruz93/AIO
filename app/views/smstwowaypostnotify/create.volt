<div ng-cloak>
  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
      <div class="title">
       Envío de Notificaciones Post de Respuestas de SMS Doble-Vía
      </div>
      <p>
       Esta opción permite enviar las Respuestas de los mensajes del servicio SMS doble-via a través de peticiones POST a una URL con su respectivo codigo de autenticación.
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap ">
      <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 text-right pull-right">
        <a href="{{ url('tools') }}" class="button shining btn btn-sm default-inverted"><i class="fa fa-arrow-left"></i> Regresar</a>
      </div>
    </div>
  </div>
  
  <div class="row">
    
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
      <div class="fill-block fill-block-primary" >
        <div class="header">
          Información
        </div>
        <div class="body">
          <p>
            Recuerde tener en cuenta las siguientes recomendaciones: 
          <ul>
            <li>
              Todos los campos son obligatorios.
            </li>
            <li>
              La URL es la ruta del archivo alojado en su servidor, al cual serán enviadas las respuestas de SMS doble-vía realizadas por los usuarios
            </li>
            <li>
              La clave se genera una sola vez y será usada para la autenticación del archivo especificado en la URL.
            </li>                      
          </ul>
          </p>
        </div>
      </div>
    </div>
    
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
      <form name="smsxemailForm" class="form-horizontal" role="form" ng-submit="functions.validate()">
        <div class="block block-info">
          <div class="body">
            
            <div class="form-group">
              <label for="senderEmail" class="col-sm-3 control-label">*URL:</label>
              <div class="col-sm-6">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                  <div class="form-horizontal">
                    <input type="url" class="undeline-input" ng-model="data.smstwowaydata.url" id="senderEmail" placeholder="*URL para envío de respuestas" ng-disabled="misc.isDisabled" required="required">
                  </div>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-3 control-label">*Generar Clave:</label>
              <div class="col-sm-6">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                  <div class="form-horizontal input-group">
                    <input id="generateKey" class="undeline-input" ng-model="data.smstwowaydata.password" placeholder="*Generar Clave" disabled="" required="required">
                      <span class="add-on input-group-addon">
                        <a id="generateLink" href="" ng-click="functions.generateKey()" class="glyphicon glyphicon-refresh" title="Generar Clave" ng-style="styleLink"></a>
                      </span>
                      
{#                    <span class="input-append date add-on input-group none-padding">#}
                     {#<span class="add-on input-group-addon" data-ng-show="misc.copy">
                        <a href="" ng-click="functions.copyKey(data.idSmsxEmail)" class="fa fa-copy" title="Copiar Clave" id="btnCopy"></a>
                      </span>#}
{#                    </span>#}
                  </div>
                </div>
              </div>
            </div>
          
          <div class="footer" align="right" >
            <button type="submit" class="button shining btn btn-xs-round shining shining-round round-button success-inverted" data-toggle="tooltip" data-placement="top" title="Guardar Configuración" ng-disabled="misc.isDisabledButton">
              <span class="glyphicon glyphicon-ok"></span>
            </button>
            <button type="button" class="button shining btn btn-xs-round shining shining-round round-button success-inverted" data-toggle="tooltip" data-placement="top" title="Editar Configuración" ng-disabled="!misc.isDisabledButton" ng-click="functionsApi.edit();">
              <span class="glyphicon glyphicon-pencil"></span>
            </button>
            <a href="{{ url('tools') }}"  class="button shining btn btn-xs-round shining shining-round round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Cancelar Configuración">
              <span class="glyphicon glyphicon-remove"></span>
            </a>
          </div>           
        </div>
      </form>
    </div>
  </div>
</div>
<script>
  {#$(function () {
    setTimeout(function () {
      $('[data-toggle="tooltip"]').tooltip();
    }, 1000);
  });
  function openModal() {
    $('.dialog').addClass('dialog--open');
  }

  function closeModal() {
    $('.dialog').removeClass('dialog--open');
  }#}
</script>