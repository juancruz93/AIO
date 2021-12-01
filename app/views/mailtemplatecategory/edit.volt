<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="title">
      Editar categoría de plantilla <i>{{"{{data.name}}"}}</i>
    </div>
    <hr class="basic-line"/>
    <p>
      Las categorías de plantillas le ayudarán a organizar de manera práctica los registros.
    </p>   
  </div>
</div>
<div class="row">
  <form  class="form-horizontal" ng-submit="restServices.edit()">
    <div class="col-xs-12 col-sm-12 col-md-8 col-lg-6 wrap">
      <div class="block block-info">          
        <div class="body " >
          <div class="row">
            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label class="col-sm-4 col-md-4 text-left">*Nombre :</label>
                <span class="input hoshi input-default col-sm-8 col-md-8">
                  <input type="text" maxlength="100" class="form-control" id="name" ng-model="misc.data.name">
                </span>
              </div>
            </div>
            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label class="col-sm-4 col-md-4 text-left">Descripción:</label>
                <span class="input hoshi input-default col-sm-8 col-md-8">
                  <textarea class="form-control" maxlength="200" ng-model="misc.data.description" rows="4"></textarea>
                </span>
              </div>
            </div>
            <div class="form-group" >
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label class="col-sm-4 col-md-4 text-left">Estado:</label>
                <span class="input hoshi input-default col-sm-1 " >
                  <div class="onoffswitch">
                    <input type="checkbox" name="sentNow" ng-model="misc.data.status" class="onoffswitch-checkbox" id="status">
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
          <a href="{{url('mailtemplatecategory#/')}}" class="button shining btn btn-xs-round shining shining-round round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Cancelar">
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
          <li>El nombre debe tener mínimo 2 y máximo 80 caracteres</li>
          <li>El nombre de la categoría debe ser un nombre único, es decir, no pueden existir dos iguales en la base de datos con el mismo nombre.</li>
          <li>La descripción debe tener máximo 200 caracteres</li>
          <li>Recuerde que los campos con asterisco(*) son obligatorios</li>
        </ul>
        </p>
      </div>
    </div>     
  </div> 
</div>





