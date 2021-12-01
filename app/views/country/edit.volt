
  <div ng-app="aio">
    {#    <div class="clearfix"></div>#}
    <div class="space"></div>
    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
        <div class="title">
          Edición de un país
        </div>            
        <hr class="basic-line" />            
      </div>
    </div>       

    <div class="row" >
      <form  method="post" ng-submit="editCountry()" >
        <div class="col-xs-12 col-sm-12 col-md-8 col-lg-6 wrap">
          <div class="block block-info">          
            <div class="body " >
              <div class="row">

                <div class="form-group">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <label class="col-sm-3 text-right">*Nombre</label>
                    <span class="input hoshi input-default col-sm-9">
                      <input type="text" placeholder="*Nombre" class="undeline-input" ng-model="country.name"  id="name" name="name" required maxlength="60" >
                    </span>
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <label class="col-sm-3 text-right">*Dígitos mínimos</label>
                    <span class="input hoshi input-default col-sm-9">
                      <input type="text" placeholder="*Dígitos mínimos" class="undeline-input" ng-model="country.minDigits"  id="shortname" name="minDigits" >
                    </span>
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <label class="col-sm-3 text-right">*Dígitos máximos</label>
                    <span class="input hoshi input-default col-sm-9">
                      <input type="text" placeholder="*Dígitos máximos" class="undeline-input" ng-model="country.maxDigits"  id="shortname" name="maxDigits">
                    </span>
                  </div>
                </div>


              </div>
            </div>
            <div class="footer" align="right">          
              <button type="submit" class="button shining btn btn-xs-round shining shining-round round-button success-inverted" data-toggle="tooltip" data-placement="top" title="Guardar">
                <span class="glyphicon glyphicon-ok"></span>
              </button>
              <a href="#/" class="button shining btn btn-xs-round shining shining-round round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Cancelar">
                <span class="glyphicon glyphicon-remove"></span>
              </a>
            </div>    
          </div>
        </div>
      </form>
      <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">                            
        <div class="fill-block fill-block-info" >
          <div class="header">
            Instrucciones
          </div>
          <div class="body">
            <p>
              Recuerde tener en cuenta estas recomendaciones:
            <ul>                            
              <li>El campo 'nombre' no puede tener más de 60 caracteres ni menos de 2 caracteres</li>
              <li>Los campos 'Dígitos mínimos' y 'Dígitos máximos' corresponde a la cantidad de dígitos mínimos y máximos respectivamente que un número de teléfono puede tener en este país</li>
              <li>Recuerde que los campos con asterisco(*) son obligatorios.</li>
            </ul> 
            </p>
          </div>
          <div class="footer">
            Creación
          </div>
        </div>     
      </div>            
    </div>

  </div>