<style>
  /*Alineación del dateTimePicker a la izquierda*/
  .alig-left-dtp{
    left: -46%
  }
</style>
<script type="text/javascript">
  $(function () {
    $(".select2").select2({
      theme: 'classic'
    });
  });
</script>
<div class="clearfix"></div>
<div class="space"></div>
<div class="row" data-ng-init="initComponents()">
  <span style="margin: 20px; float: right; font-weight: bolder  ">Va a agregar {{"{{batchs.length}}"}} de {{"{{limit}}"}} contactos</span>
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 ">
    <div class="block block-info">
      <div class="body">
        <div class="row">
          <form name="contactlistForm" role="form">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
              <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
                  <div class="form-group">
                    <label for="email">Correo Electrónico</label>
                    <input type="email" id="email" maxlength="50" class="form-control" ng-model="batch.email"  name="" aria-invalid="true" autofocus id="batchemail">
                  </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
                  <div class="form-group">
                    <label for="indicative">Indicativo</label>
                    <select class="form-control select2" id="indicative" name="indicative" data-ng-model="batch.indicative" data-placeholder="Seleccione">
                      <option value=""></option>
                      <option data-ng-repeat="item in listIndicatives track by $index" value="{{"{{item.idCountry}}"}}">(+{{"{{item.phoneCode}}"}}) {{"{{item.name}}"}}</option>
                    </select>
                  </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
                  <div class="form-group">
                    <label for="phone">Celular</label>
                    <input type="number" maxlength="50" class="form-control" ng-model="batch.phone" id="" name="" aria-invalid="true">
                  </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
                  <div class="form-group">
                    <label for="name">Nombres</label>
                    <input type="text" maxlength="50" class="form-control" ng-model="batch.name" id="" name="" aria-invalid="true">
                  </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
                  <div class="form-group">
                    <label for="lastname">Apellidos</label>
                    <input type="text" maxlength="50" class="form-control" ng-model="batch.lastname" id="" name="" aria-invalid="true">
                  </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
                  <div class="form-group" >
                    <label>Fecha de nacimiento</label>
                    <div class="dropdown dropdown-start-parent">
                      <a class="dropdown-toggle" id="dropdown2" role="button" data-toggle="dropdown" data-target=".dropdown-start-parent" href="">
                        <div class="input-group">
                          <input type="text" class="form-control" readonly="true" data-ng-model="batch.birthdate">
                          <span class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                          </span>
                        </div>
                      </a>
                      <ul class="dropdown-menu alig-left-dtp" role="menu" aria-labelledby="dLabel">
                        <datetimepicker data-ng-model="batch.birthdate"
                                        data-datetimepicker-config="{ dropdownSelector: '#dropdown2', startView: 'year', minView: 'day', modelType: 'YYYY-MM-DD' }"
                                        data-before-render="functions.dateBeforeRender($dates)"/>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </form>
        </div>
        <div class="row">
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-right">
            <a ng-click="validateEmptyBatch()" class="btn success-inverted" data-toggle="tooltip" data-placement="top" title="Agregar">
              <i class="fa fa-plus-circle"></i>
              Agregar contacto
            </a>
          </div>
        </div>

        <div class="row margin-top-25px">
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <md-progress-linear md-mode="query" data-ng-show="misc.loaderSaveShow" class="md-warn"></md-progress-linear>
          </div>
        </div>

        <div class="row" ng-if="batchs.length > 0">
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="table-responsive">
              <table class="table table-bordered sticky-enabled">
                <thead class="theader">
                  <tr>
                    <th>Correo Electrónico</th>
                    <th>Indicativo</th>
                    <th>Número de teléfono</th>
                    <th>Nombre</th>
                    <th>Apellidos</th>
                    <th>Feha de nacimiento</th>
                    <th>Acción</th>
                  </tr>
                </thead>
                <tbody>
                  <tr ng-repeat="batch in batchs track by $index">
                    <td><a href="#" editable-email="batchs[$index].email">{{"{{batch.email || 'Campo vacío'}}"}}</a></td>
                    <td style="width:7%"><a href="#" editable-select="batchs[$index].indicative" e-ng-options="s.idCountry as s.name for s in listIndicatives" onaftersave="updateNameIndicative($index)">{{"{{batch.nameIndicative || 'Campo vacío'}}"}}</a></td>
                    <td style="width:12%"><a href="#" editable-number="batchs[$index].phone">{{"{{batch.phone || 'Campo vacío'}}"}}</a></td>
                    <td><a href="#" editable-text="batchs[$index].name">{{"{{batch.name || 'Campo vacío'}}"}}</a></td>
                    <td><a href="#" editable-text="batchs[$index].lastname">{{"{{batch.lastname || 'Campo vacío'}}"}}</a></td>
                    <td style="width:13%"><a href="#" editable-text="batchs[$index].birthdate">{{"{{batch.birthdate | date: 'yyyy-MM-dd' || 'Campo vacío'}}"}}</a></td>
                    <td>
                      <a ng-click="removeBatchtoBatchs($index)" class="cursor-pointer" data-toggle="tooltip" data-placement="top" title="Eliminar" style="float: none; font-size: 25px; color: #ff2400">
                        <span class="glyphicon glyphicon-remove-sign"></span>
                      </a>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div class="footer" align="right">
        <span style="margin: 20px;">Va a agregar {{"{{batchs.length}}"}} de {{"{{limit}}"}} contactos</span>
        <button type="submit" class="button btn btn-xs-round round-button success-inverted" data-ng-click="validateContactBatch()" data-ng-disabled="misc.disabledSaveButton">
          <span class="glyphicon glyphicon-ok"></span>
          <md-tooltip md-direction="bottom">Guardar</md-tooltip>
        </button>
        <a href="#/" class="button btn btn-xs-round  round-button danger-inverted">
          <span class="glyphicon glyphicon-remove"></span>
          <md-tooltip md-direction="bottom">Cancelar</md-tooltip>
        </a>

      </div>
    </div>
  </div>

  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 ">
    <div class="fill-block fill-block-primary" >
      <div class="header">
        Instrucciones
      </div>
      <div class="body">
        <div class="row">
          <div class="col-lg-12">
            Recuerde tener en cuenta estas recomendaciones:
            <ul>
              <li>Cada línea es un contacto diferente</li>
              <li>Los datos del contacto deben estar separados por coma</li>
              <li>Los datos deben de tener el siguiente orden (Nombre, apellido, fecha de nacimiento, correo electrónico, indicativo del país y números de telefono)</li>
              <li>El indicativo del pais en el movil no debe de tener el signo (+)</li>
              <li>La fecha de nacimiento debe contener el siguiente formato dd/mm/aaaa</li>
              <li>Recuerde que el contacto debe contener al menos el correo electronico o el numero del móvil</li>
              <li>Si no va a ingreasr un dato dejar el espacio vacio </li>
              <li>Recuerde que solo puede agregar {{"{{limit}}"}} contactos rápidamente</li>
              <li>Recuerde que los campos con asterisco(*) son oblogatorios</li>
            </ul>
          </div>
          {#<div class="col-lg-6 text-right">
                        <img src="{{ url("")}}images/contact-batch-example.png" width="500px">
          </div>#}
        </div>
      </div>
    </div>
  </div>

</div>
<div id="somedialog" class="dialog">
  <div class="dialog__overlay"></div>
  <div class="dialog__content" style="max-width:100% !important; width: 80%;">
    <div class="morph-shape">
      <svg xmlns="https://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 560 280" preserveAspectRatio="none">
      <rect x="3" y="3" fill="none" width="556" height="276"/>
      </svg>
    </div>
    <div class="dialog-inner">
      <p ng-if="contacterror.length==1">El siguiente registro tiene algunos errores, si continua
        es posible que no sea creado</p>
      <p ng-if="contacterror.length>1">Los siguientes {{"{{contacterror.length }}"}} registros tienen algunos errores, si continua
        es posible que no sean creados</p>
        {#      {{"{{contacterror}}"}}#}
      <div class="div-scroll-100px" style="height: 200px;">

        <table class="table table-bordered">
          <thead>
            <tr>
              <th>Correo</th>
              <th>Indicativo</th>
              <th>Móvil</th>
              <th>Nombre</th>
              <th>Apellido</th>
              <th style="width: 15%;">Fecha nacimiento</th>
              <th style="width: 30%;">Error</th>
            </tr>
          </thead>
          <tbody>
            <tr class="danger" ng-repeat="obj in contacterror track by $index">
              <td>{{ "{{obj.email}}"}}</td>
              <td>{{ "{{obj.indicative}}"}}</td>
              <td>{{ "{{obj.phone}}"}}</td>
              <td>{{ "{{obj.name}}"}}</td>
              <td>{{ "{{obj.lastname}}"}}</td>
              <td>{{ "{{obj.birthdate}}"}}</td>
              <td>{{ "{{obj.error}}"}}</td>
            </tr>

          </tbody>
        </table>


      </div>
      <br>
      <h2>¿Esta seguro que desea continuar?</h2>
      <div>
        <a onClick="closeModal();" class="button shining btn btn-md danger-inverted" data-dialog-close>Cancelar</a>
        <a ng-click="addcontactbatch()"  id="btn-ok" class="button shining btn btn-md success-inverted">Confirmar</a>
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
