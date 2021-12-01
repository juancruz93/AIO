{% block js %}
  {{ javascript_include('library/angular-keep-values/angular-keep-values.min.js') }}
{% endblock %}  

<script>
  $(function () {
    $(".select2").select2({
      theme: 'classic',
      placeholder: {
        id: 'Text', // the value of the option
        text: 'Select an option'
      }
    });
  });
</script>
<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="title">
      Editar campo personalizado
    </div>
    <hr class="basic-line"/>
  </div>
</div>
<div class="row" ng-show="customfield">
  <form name="customfieldForm" class="form-horizontal" role="form" ng-submit="editCustomfield()" >
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
      <div class="block block-info">
        <div class="body">
          <div class="form-group">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
              <label  class="col-sm-4 text-right">*Nombre</label>
              <span class="input hoshi input-default  col-sm-8">
                <input type="text" placeholder="*Nombre" class="undeline-input" ng-model="customfield.name"  id="name" name="name" required>
              </span>
            </div>
          </div>
          <div class="form-group">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
              <label  class="col-sm-4 text-right">Valor por defecto</label>
              <span class="input hoshi input-default  col-sm-8">
                <input type="text" placeholder="Valor por defecto" class="undeline-input" ng-model="customfield.defaultvalue"  id="description" name="defaultvalue">
              </span>
            </div>
          </div>
          <div class="form-group">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
              <label  class="col-sm-4 text-right">*Tipo de formato del campo</label>
              <span class="input hoshi input-default  col-sm-8">
                <select  class="form-control" id="typefield" name="typefield" ng-model="customfield.type" required="" ng-change="selectType()">
                  <option value="">Seleccione un tipo de formato</option>
                  <option value="Text" >Texto</option>
                  <option value="Date"  >Fecha</option>
                  <option value="Numerical"  >Numerico</option>
                  <option value="TextArea"  >Area de texto</option>
                  <option value="Select"  >Selección</option>
                  <option value="Multiselect"  >Selección multiple</option>
                  {#                  <option value="Text" {{"{{ ((customfield.type == 'Text') ? 'selected' : '' )  }}"}}  >Texto</option>
                                    <option value="Date"  {{"{{ ((customfield.type == 'Date') ? 'selected' : '' )  }}"}} >Fecha</option>
                                    <option value="Numerical"  {{"{{ ((customfield.type == 'Numerical') ? 'selected' : '' )  }}"}}>Numerico</option>
                                    <option value="TextArea"  {{"{{ ((customfield.type == 'TextArea') ? 'selected' : '' )  }}"}}>Area de texto</option>
                                    <option value="Select"  {{"{{ ((customfield.type == 'Select') ? 'selected' : '' )  }}"}}>Selección</option>
                                    <option value="Multiselect"  {{"{{ ((customfield.type == 'Multiselect') ? 'selected' : '' )  }}"}}>Selección multiple</option>#}
                </select>
              </span>
            </div>
          </div>
          <div class="form-group">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
              <label  class="col-sm-4 text-right">Valor</label>
              <span class="input hoshi input-default  col-sm-8">
                {#                {{"{{customfield.value}}"}}#}
                {#                <md-chips placeholder="Agregue con ENTER" ng-model="asd" readonly="valueSelected" md-removable="true" md-enable-chip-edit="true" ng-disabled="" ng-required="valueSelected" class="undeline-input" id="value" name="value" style="padding: 0px;"></md-chips>#}
                <md-chips placeholder="Agregue con ENTER" ng-model="value" readonly="valueSelected" md-removable="true" md-enable-chip-edit="true" ng-disabled="" ng-required="valueSelected" class="undeline-input" id="value" name="value" style="padding: 0px;"></md-chips>
                  {#                <input type="text"  ng-disabled="valueSelected" class="undeline-input" ng-model="customfield.value"  id="value" name="value">#}
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
          <a ng-disabled="btndisabled" href="#/customfield/{{"{{ customfield.idContactlist }}"}}" class="button shining btn btn-xs-round shining shining-round round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Cancelar">
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
          <li>El campo valor es para el tipo de campo selección y selección múltiple</li>
          <li>Recuerde que los campos con asterisco(*) son oblogatorios</li>
        </ul> 
        </p>
      </div>
    </div> 
  </div>
</div>