<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="title">
      Editar MTA por direcciones IP <i>{{"{{data.name}}"}}</i>
    </div>
    <hr class="basic-line"/>
    <p>
      MTA por IP
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
                <label class="col-sm-4 col-md-4 text-left">*Nombre MTA:</label>
                <span class="input hoshi input-default col-sm-8 col-md-8">
                  <input type="text" maxlength="45" class="form-control" id="name" ng-model="misc.data.name">
                </span>
              </div>
            </div>
            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label class="col-sm-3 col-md-3 text-left">*Dirección IP:</label>
                <span class="input hoshi input-default col-sm-8 col-md-8" style="margin-left: 48px;width: 395px;">
                  <ui-select style="max-width: 95%" multiple ng-model="misc.data.ipdta" ng-required="true"  ui-select-required 
                             theme="select2" title=""  sortable="false" close-on-select="true" >
                    <ui-select-match placeholder="Direcciones IP">{{"{{$item.name}}"}}</ui-select-match>
                    <ui-select-choices repeat="key.idIp as key in misc.ips | propsFilter: {name: $select.search}">
                      <div ng-bind-html="key.name | highlight: $select.search"></div>
                    </ui-select-choices>
                  </ui-select>
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
            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label class="col-sm-4 col-md-4 text-left">Observación:</label>
                <span class="input hoshi input-default col-sm-8 col-md-8">
                  <textarea class="form-control" maxlength="200" ng-model="misc.data.observation" rows="4"></textarea>
                </span>
              </div>
            </div>
          </div>
        </div>
        <div class="footer" align="right">   
          <button class="button shining btn btn-xs-round shining shining-round round-button success-inverted" data-toggle="tooltip" data-placement="top" title="Guardar">
            <span class="glyphicon glyphicon-ok"></span>
          </button>
          <a href="{{url('mtaxip#/')}}" class="button shining btn btn-xs-round shining shining-round round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Cancelar">
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
          <li>Debe seleccionar las direcciones IP correspondientes sin importar el número</li>
          <li>Recuerde que los campos con asterisco(*) son obligatorios</li>
        </ul>
        </ul>
        </p>
      </div>
    </div>     
  </div> 
</div>





