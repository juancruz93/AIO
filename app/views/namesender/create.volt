<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="title">
      Nuevo nombre remitente
    </div>
    <hr class="basic-line"/>
    <p>
      Los nombres de remitentes los visualiza al momento de crear un envio de correo.
    </p>   
  </div>
</div>
<div class="row">
  <form  class="form-horizontal" ng-submit="restServices.save()">
    <div class="col-xs-12 col-sm-12 col-md-8 col-lg-6 wrap">
      <div class="block block-info">          
        <div class="body " >
          <div class="row">
            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label class="col-sm-4 col-md-4 text-left">*Nombre de remitente:</label>
                <span class="input hoshi input-default col-sm-8 col-md-8">
                  <input type="text" maxlength="45" class="form-control" id="name" ng-model="data.name">
                </span>
              </div>
            </div>
            <div class="form-group" >
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label class="col-sm-4 col-md-4 text-left">Estado:</label>
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
          <a href="{{url('namesender#/')}}" class="button shining btn btn-xs-round shining shining-round round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Cancelar">
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
          <li>El nombre debe tener mínimo 2 y máximo 45 caracteres</li>
          <li>Recuerde que los campos con asterisco(*) son obligatorios</li>
        </ul>
        </p>
      </div>
    </div>     
  </div> 
</div>





