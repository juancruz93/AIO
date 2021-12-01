<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-6 col-lg-7">
    <form name="contactlistForm" class="form-horizontal" role="form" ng-submit="functions.confirmEdit()" >
      <div class="block block-info ">
        <div class="body row padding-right-30px " >
          <div class="col-xs-12 col-sm-5 col-md-5 col-lg-5 margin-top-15px ">
            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label  class="col-sm-4 text-right">*Nombre</label>
                <span class="input hoshi input-default  col-sm-8">            
                  <input type="text" id="name" name="name" ng-model="segment.name" class="undeline-input" maxlength="40" ng-required="true"> 
                </span>
              </div>
            </div>
            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label  class="col-sm-4 text-right">Descripción</label>
                <span class="input hoshi input-default  col-sm-8">                                     
                  <textarea maxlength="250" id="description" name="description" ng-model="segment.description" class="undeline-input"></textarea>
                </span>
              </div>
            </div>
            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label  class="col-sm-4 text-right">*Lista de contactos</label>
                <span class="input hoshi input-default  col-sm-8">   
                  <ui-select ng-change='contactlistSelected()' multiple ng-model="segment.contactlist"  ng-required="true"  ui-select-required  class='min-width-100' theme="select2" title=""  sortable="false" close-on-select="true">
                    <ui-select-match >{{"{{$item.name}}"}}</ui-select-match>
                    <ui-select-choices repeat="key in contactlist | propsFilter: {name: $select.search}">
                      <div ng-bind-html="key.name | highlight: $select.search"></div>
                    </ui-select-choices>
                  </ui-select>
                </span>
              </div>
            </div>
            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label  class="col-sm-4 text-right">*Crear segmento con</label>
                <span class="input hoshi input-default  col-sm-8">          
                  <ui-select ng-model="segment.conditions" theme="select2"  class='min-width-100' ng-required="true">
                    <ui-select-match placeholder="---">{{"{{$select.selected}}"}}</ui-select-match>
                    <ui-select-choices repeat="key in [ 'Todas las condiciones',  'Algunas de las condiciones' ]">
                      <div ng-bind-html="key"></div>
                    </ui-select-choices>
                  </ui-select>
                </span>
              </div>
            </div>
          </div>

          <div class="col-xs-12 col-sm-7 col-md-7 col-lg-7  fill-block fill-block-info  wrap padding-top-15px margin-1-5" style="margin-top: 0">
            <div class="header">
              Condiciones
            </div>
            <div class="row">
              <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 wrap">
                <label>Campos</label>
              </div>
              <div class="col-xs-3 col-sm-3 col-md-3 col-lg-5 wrap">
                <label>Tipo de condición</label>
              </div>
              <div class="col-xs-4 col-sm-4 col-md-4 col-lg-3 wrap">
                <label>Valor</label>
              </div>
            </div>
            <div  ng-repeat="item in segment.filters" class="row" >
              <div class="form-group margin-botton-none wrap" ng-init="initItemsEdit(item)">
                <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4  wrap ">
                  <ui-select ng-model="item.idCustomfield" theme="select2"  ng-change="valueItemEdit(item)"  class='input-hoshi input-field min-width-100'>
                    <ui-select-match placeholder="---">{{"{{$select.selected.name}}"}}</ui-select-match>
                      {#                    <ui-select-choices repeat=" key in customfield | propsFilter: {name: $select.search}">#}
                    <ui-select-choices repeat="key.idCustomfield as key in customfield | propsFilter: {name: $select.search}">
                      <div ng-bind-html="key.name | highlight: $select.search"></div>
                    </ui-select-choices>
                  </ui-select>
                </div>

                <div class="col-xs-3 col-sm-3 col-md-3 col-lg-4 wrap" ng-if='item.idCustomfield'>
                  <ui-select ng-model="item.conditions" theme="select2"   class='input-hoshi input-field min-width-100 '>
                    <ui-select-match placeholder="---">{{"{{$select.selected}}"}}</ui-select-match>
                    <ui-select-choices repeat="key  in item.con  ">
                      {#                    <ui-select-choices repeat="key  in ((item.customfield.type == 'Numerical') ? conditionsNumber : conditions ) ">#}
                      <div ng-bind-html="key"></div>
                    </ui-select-choices>
                  </ui-select>
                </div>

                <div class="col-xs-4 col-sm-4 col-md-4 col-lg-3 wrap " ng-show="item.conditions">
                  <ui-select style="width: 80px;" ng-if="item.type == 'Select' || item.idCustomfield.type == 'Select'  " ng-model="item.value"  theme="select2"  
                             class='input-hoshi input-field min-width-100 '>
                    <ui-select-match placeholder="---">{{"{{$select.selected}}"}}</ui-select-match>
                    <ui-select-choices repeat="key  in item.value2.split(',')">
                      <div ng-bind-html="key"></div>
                    </ui-select-choices>
                  </ui-select>

                  <input style="width: 80px;" ng-if="item.type == 'Numerical' "  placeholder="Valor" ng-model="item.value" type="number">

                  <input size="6" ng-if="item.type == 'Text' || item.customfield.type == 'TextArea' " class="form-control" placeholder="Valor" ng-model="item.value" >

                  <ui-select ng-if="item.type == 'Multiselect' " multiple='multiple' ng-model="item.value"  theme="bootstrap"  
                             class='input-hoshi input-field min-width-100 '>
                    <ui-select-match placeholder="---">{{"{{$item}}"}}</ui-select-match>
                    <ui-select-choices repeat="key  in item.customfield.value.split(',')">
                      <div ng-bind-html="key"></div>
                    </ui-select-choices>
                  </ui-select>

                  <div moment-picker="item.value"
                       format="YYYY-MM-DD"  ng-if="item.type == 'Date' ">
                    <input size="6" class=""
                           placeholder="Fecha"
                           ng-model="item.value"
                           ng-model-options="{ updateOn: 'blur' }">
                  </div>

                </div>
                <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 " ng-if="segment.filters.length > 1">
                  <a ng-click="deleteFilter($index)"class="btn btn-danger btn-xs" ><i class="fa fa-trash" aria-hidden="true"></i></a>
                </div>
              </div>
            </div>
            <div class="row" ng-show='viewFilters' >
              <div class="form-group margin-botton-none wrap" >
                <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 wrap " style="margin-left: -5px" >
                  <a ng-click="addFilter()"class="button shining btn  success-inverted" ng-disabled="filters.length == 20">Añadir condición</a>
                </div>
              </div>
            </div>
          </div>

        </div>
        <div class="footer" align="right">       
          <button ng-disabled="!progressbar" type="submit" class="button btn btn-xs-round success-inverted" data-toggle="tooltip" data-placement="top" title="Guardar">
            <span class="glyphicon glyphicon-ok"></span>
          </button>
          <a href="#/" ng-disabled="!progressbar" class="button shining btn btn-xs-round danger-inverted" data-toggle="tooltip" data-placement="top" title="Cancelar">
            <span class="glyphicon glyphicon-remove"></span>
          </a>
        </div>
      </div>
    </form>
  </div>

  <div class="col-xs-12 col-sm-12 col-md-5 col-lg-5">
    <div class="fill-block fill-block-primary" >
      <div class="header">
        Instrucciones
      </div>
      <div class="body">
        <p>Recuerde tener en cuenta las siguientes recomendaciones</p>
        <ul>
          <li><p>Los campos con asterisco (*) son obligatorios.</p></li>
          <li><p>El nombre puede contener máximo 40 caracteres.</p></li>
          <li><p>La descripción puede contener máximo 250 caracteres.</p></li>
          <li>
            <p>
              Debe escoger por lo menos una lista de contactos donde se aplicarán las condiciones definidas
              por el usuario, con esto se conformará el segmento de contactos que cumplan con las condiciones.
            </p>
          </li>
          <li>
            <p>
              Debe seleccionar de que forma desea crear el segmento, más específicamente si desea que el segmento
              cumpla con todas las condiciones o sólo con algunas.
            </p>
          </li>
          <li>
            <p>
              Para añadir una condición primero debe de seleccionar un campo por el cual va condicionar, después
              debe seleccionar el tipo de condición que desea (Ej: Es igual a, contiene, etc) y por último debe
              colocar el valor con el cual se va a comparar el campo según la condición.
            </p>
          </li>
          <li><p>Puede añadir hasta 20 condiciones.</p></li>
          <li>
            <p>
              Si selecciona "Todas las condiciones" el segmento solo buscará los contactos que cumplan todas las
              condiciones.
            </p>
          </li>
          <li><p>Si una condición queda vacía no se tendrá en cuenta para el segmento.</p></li>
        </ul>
      </div>
    </div> 
  </div>
</div>

<div id="somedialog" class="dialog">
  <div class="dialog__overlay"></div>
  <div class="dialog__content">
    <div class="dialog-inner">
      <h2>¡Tenga en cuenta!</h2>
      <div ng-class="{'hidden' : progressbar}" >
        <md-progress-linear md-mode="indeterminate" class="md-warn" ></md-progress-linear>
      </div>
      <div>
        Este proceso puede tardar de acuerdo a la cantidad de listas de contactos seleccionadas y las condiciones establecidas.
        Una vez finalice el proceso se le notificará por correo electrónico.
      </div>
      <br>
      <div>
        <button onClick="closeModal();" class="button shining btn btn-md danger-inverted" data-dialog-close>Cancelar</button>
        <button type="button" data-ng-click="functions.editSegment() " id="btn-ok" class="button shining btn btn-md success-inverted">Confirmar</button>
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