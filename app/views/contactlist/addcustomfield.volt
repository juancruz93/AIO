<style>
  .widthFull{
    width:100%;
  }
</style>
<div data-ng-init="functions.initComponents()">
  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
      <div class="title">
        Agregar un campo personalizado
      </div>
      <hr class="basic-line"/>
    </div>
  </div>
  <div class="row wrap">
    <form name="contactlistForm" class="form-horizontal" role="form" ng-submit="addcustomfield()" >
      <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
        <div class="block block-info">
          <div class="body">
            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label  class="col-sm-4 text-right">*Nombre</label>
                <span class="input hoshi input-default  col-sm-8">
                  <input type="text"  class="undeline-input" ng-model="vars.data.name"  id="name" name="name" ng-required="true">
                  <h6 class="color-danger">
                    Los siguientes caracteres serán removidos del mensaje: \º~|·[]^{}¨´€"
                  </h6>
                </span>
              </div>
            </div>
            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label  class="col-sm-4 text-right">Valor por defecto</label>
                <span class="input hoshi input-default  col-sm-8">
                  <input type="text"  class="undeline-input" ng-model="vars.data.defaultvalue"  id="description" name="defaultvalue">
                </span>
              </div>
            </div>
            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label  class="col-sm-4 text-right">*Tipo de formato del campo</label>
                <div class="input hoshi input-default col-sm-8">
                  <ui-select data-ng-model="vars.data.typefield" theme="select2" title="Seleccione una categoría" data-ng-change="selectType()" class="widthFull">
                    <ui-select-match placeholder="Seleccione un tipo de campo">{{"{{$select.selected.label}}"}}</ui-select-match>
                    <ui-select-choices repeat="key.value as key in vars.listTypeFields | propsFilter: {label: $select.search}">
                      <div ng-bind-html="key.label | highlight: $select.search"></div>
                    </ui-select-choices>
                  </ui-select>
                </div>
              </div>
            </div>
            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label  class="col-sm-4 text-right">Valor</label>
                <span class="input hoshi input-default  col-sm-8">
                  {#                                <input type="text" placeholder="Valor" ng-disabled="valueSelected" ng-required="valueSelected" class="undeline-input" ng-model="value"  id="value" name="value">#}
                  <md-chips placeholder="Agregue con ENTER" ng-model="vars.data.value" readonly="valueSelected" md-removable="true" md-enable-chip-edit="true" ng-disabled="" ng-required="valueSelected" class="undeline-input" id="value" name="value" style="padding: 0px;"></md-chips>
                </span>
              </div>
            </div>
          </div>
          <div class="footer" align="right">
            <div ng-class="{'hidden' : !btndisabled}" >
              <md-progress-linear md-mode="indeterminate" class="md-warn" ></md-progress-linear>
            </div>
            <button ng-disabled="btndisabled" type="submit" class="button shining btn btn-xs-round shining shining-round round-button success-inverted" data-toggle="tooltip" data-placement="top" title="Guardar">
              <span class="glyphicon glyphicon-ok"></span>
            </button>
            <a ng-disabled="btndisabled" href="#/customfield/{{"{{idContactlist}}"}}" class="button shining btn btn-xs-round shining shining-round round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Cancelar">
              <span class="glyphicon glyphicon-remove"></span>
            </a>
          </div>
        </div>
      </div>
    </form>

    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">       
      <div class="fill-block fill-block-primary" >
        <div class="header">
          Información
        </div>
        <div class="body">
          <p>
            Recuerde tener en cuenta estas recomendaciones:
          <ul>                            
            <li>El valor por defecto es el valor que se le dará si el campo se deja vacío.</li>
            <li>El campo valor es para el tipo de campo selección y selección múltiple y debe de estar separador por comas</li>
            <li>Recuerde que los campos con asterisco(*) son oblogatorios</li>
          </ul> 
          </p>
        </div>
      </div> 
    </div>
  </div>
</div>

<div id="dialogcustom" class="dialog">
  <div class="dialog__overlay"></div>
  <div class="dialog__content">
    <div class="morph-shape">
      <svg xmlns="https://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 560 280" preserveAspectRatio="none">
      <rect x="3" y="3" fill="none" width="556" height="276"/>
      </svg>
    </div>
    <div class="dialog-inner">
      <h2>No es posible crear más campos personalizados, solo se permiten 10 campos por cada lista.</h2>
      <br>
      <div>        
        <a onClick="closeModal();"  id="btn-ok" class="button shining btn btn-md success-inverted">Confirmar</a>
      </div>
    </div>
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