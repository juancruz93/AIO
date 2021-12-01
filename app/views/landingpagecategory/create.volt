<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="title">
      Edicion de envío rápido de SMS doble vía
    </div>
    <hr class="basic-line"/>
  </div>
</div>
<div class="row">
  <form  class="form-horizontal" ng-submit="functions.validate()">
    <div class="col-xs-12 col-sm-12 col-md-8 col-lg-6 wrap">
      <div class="block block-info">          
        <div class="body " >
          <div class="row">
            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label class="col-sm-4 col-md-4 text-left">*Nombre :</label>
                <span class="input hoshi input-default col-sm-8 col-md-8">
                  <input type="text" class="form-control" id="name" ng-model="data.name">
                </span>
              </div>
            </div>
            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label class="col-sm-4 col-md-4 text-left">Descripcion:</label>
                <span class="input hoshi input-default col-sm-8 col-md-8">
                  <textarea class="form-control" ng-model="data.description" rows="4"></textarea>
                </span>
              </div>
            </div>
            <div class="form-group" >
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label class="col-sm-4 col-md-4 text-left">Estato:</label>
                <span class="input hoshi input-default col-sm-1 " >
                  <div class="onoffswitch">
                    <input type="checkbox" name="sentNow" ng-model="data.status" class="onoffswitch-checkbox" id="status">
                    <label class="onoffswitch-label" for="status">
                      <span class="onoffswitch-inner"></span>
                      <span class="onoffswitch-switch"></span>
                    </label>
                  </div>
                  {# <input type="checkbox" class="toggle-sms-two-way" ng-click="functions.sentNow()"/>#}
                </span>
              </div>
            </div>
          </div>
        </div>
        <div class="footer" align="right">   
          <button class="button shining btn btn-xs-round shining shining-round round-button success-inverted" data-toggle="tooltip" data-placement="top" title="Guardar">
            <span class="glyphicon glyphicon-ok"></span>
          </button>
          <a ui-sref="list" class="button shining btn btn-xs-round shining shining-round round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Cancelar">
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
          <li>El nombre del envio rapido no puede tener menos de 5 caracteres ni más de 50 caracteres.</li>
          <li>La fecha y hora de envío es para decidir cuando se van a enviar los SMS y tiene que ser entre las 7:00h  y las 18:00h (hora de Colombia).</li>
          <li>El envío de notificaciones por defecto está en "No", para activarlo haga clic en el switch para que cambie a "Si"</li>
          <li>Los correos se deben de separar por coma "," maximo 8 correos, donde se enviarán las notificaciones</li>
          <li>Los destinatarios es a quien va a enviarse los SMS y tienen que ir de la siguiente forma: codigo de pais(sin el simbolo "+"), número de móvil, mensaje. Separados por punto y coma ";". Los destinatarios son cada línea separados por un salto de línea (enter).</li>
          <li>Recuerde que los campos con asterisco(*) son obligatorios.</li>
        </ul>
        </p>
      </div>
      {#<div class="footer">
          Creación
      </div>#}
    </div>     
  </div> 
</div>





