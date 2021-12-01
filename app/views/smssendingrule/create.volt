<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="title">
      Nueva regla de envío de SMS
    </div>
    <hr class="basic-line">
    <p class="text-justify">
      Las reglas de envío para SMS servirán para envíar los SMS por un canal determinado dependiendo de su prefijo e indicativo.
    </p>
  </div>
</div>

<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">
    <form data-ng-submit="create()">
      <div class="block block-info">
        <div class="body row">
          <div class="col-md-12">
            <div class="body form-horizontal">
              <div class="form-group">
                <label for="name" class="col-sm-2 control-label">{{form.label("name")}}</label>
                <div class="col-sm-10">
                  {{form.render("name")}}
                  <div class="text-right" data-ng-class="data.name.length > 80 ? 'negative':''">{{"{{data.name.length > 0 ?  data.name.length+'/80':''}}"}}</div>
                </div>
              </div>
              <div class="form-group">
                <label for="description" class="col-sm-2 control-label">{{form.label("description")}}</label>
                <div class="col-sm-10">
                  {{form.render("description")}}
                  <div class="text-right" data-ng-class="data.description.length > 200 ? 'negative':''">{{"{{data.description.length > 0 ?  data.description.length+'/200':''}}"}}</div>
                </div>
              </div>
              <div class="form-group">
                <label for="indicative" class="col-sm-2 control-label">País</label>
                <div class="col-sm-10">
                  <ui-select data-ng-model="data.idCountry" theme="select2" style="width: 100%" title="Seleccione un país" ng-required ui-select-required>
                    <ui-select-match placeholder="Seleccione un país">{{"{{$select.selected.name}}"}}</ui-select-match>
                    <ui-select-choices repeat="item.idCountry as item in listindicative | filter: $select.search">
                      <div ng-bind-html="'(+'+item.phoneCode+') ' + item.name | highlight: $select.search"></div>
                    </ui-select-choices>
                  </ui-select>
                </div>
              </div>
              <div class="form-group">
                <label for="status" class="col-sm-2 control-label">{{form.label("status")}}</label>
                <div class="col-sm-10">
                  <md-switch class="md-warn none-margin" md-no-ink aria-label="Switch No Ink" data-ng-model="data.status">{{"{{data.status ? 'Activo':'Inactivo'}}"}}</md-switch>
                </div>
              </div>
              <div class="block block-warning"  data-ng-repeat="form in forms track by $index">
                <div class="body row">
                  <div class="col-md-12">
                    <div class="body form-horizontal">
                      <div class="form-group">
                        <label for="adapter" class="col-sm-2 control-label">Adaptador</label>
                        <div class="col-sm-10">
                          <ui-select data-ng-model="form.idAdapter" theme="select2" style="width: 100%" title="Seleccione un rol">
                            <ui-select-match placeholder="Seleccione un adaptador">{{"{{$select.selected.name}}"}}</ui-select-match>
                            <ui-select-choices repeat="item.idAdapter as item in listadapter | filter: $select.search">
                              <div ng-bind-html="item.name | highlight: $select.search"></div>
                            </ui-select-choices>
                          </ui-select>
                        </div>
                      </div>
                      <div class="form-group">
                        <label for="byDefault" class="col-sm-2 control-label">Por defecto</label>
                        <div class="col-sm-10">
                          <md-switch class="md-warn none-margin" md-no-ink aria-label="Switch No Ink" data-ng-model="form.byDefault" data-ng-change="switchDefault($index)">{{"{{form.byDefault ? 'Sí':'No'}}"}}</md-switch>
                        </div>
                      </div>
                      <div class="form-group">
                        <label for="prefix" class="col-sm-2 control-label">Prefijo</label>
                        <div class="col-sm-10">
                          <input type="text" class="form-control undeline-input" placeholder="Lista de prefijos" data-ng-model="form.prefix" data-ng-disabled="form.prefixDisabled" data-ng-list/>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-6 col-md-6">
                          <span class="numbers-view warning">
                            {{"{{$index+1}}"}}
                            <md-tooltip md-direction="top">
                              Configuración {{"{{$index+1}}"}}
                            </md-tooltip>
                          </span>
                        </div>
                        <div class="col-sm-6 col-md-6 text-right">
                          <span class="danger small-text xs-margin" data-ng-click="removeForm($index)">
                            <i class="fa fa-trash"></i>
                            <md-tooltip md-direction="top">
                              Eliminar
                            </md-tooltip>
                          </span>
                          <span class="success small-text xs-margin" data-ng-show="$last" data-ng-click="addForm()">
                            <i class="fa fa-plus"></i>
                            <md-tooltip md-direction="top">
                              Agregar configuración
                            </md-tooltip>
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="footer text-right">
          <button type="submit" class="button shining btn btn-xs-round shining shining-round round-button success-inverted" data-toggle="tooltip" data-placement="top" title="Guardar">
            <span class="glyphicon glyphicon-ok"></span>
          </button>
          <a ui-sref="index" class="button shining btn btn-xs-round shining shining-round round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Cancelar">
            <span class="glyphicon glyphicon-remove"></span>
          </a>

        </div>
      </div>
    </form>
  </div>

  <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">
    <div class="fill-block fill-block-primary">
      <div class="header">
        Instrucciones
      </div>
      <div class="body">
        <p>Recuerde tener en cuenta estas recomendaciones</p>
        <ul>
          <li><p>El nombre debe tener mínimo 2 y máximo 45 caracteres</p></li>
          <li>
            <p>
              Con las configuraciones de reglas de envío, podrá indicarle al sistema por donde debe enviar los mensajes SMS según su prefijo, es decir
              por ejemplo, podrá configurar que los números de teléfono celular que empiecen con 312 se vayan por el adaptador o canal claro.
            </p>
          </li>
          <li><p>Cada regla debe tener al menos una configuración. Puede agregar hasta 20.</p></li>
          <li>
            <p>
              Debe seleccionar una configuración por defecto. Esto le indicará a la plataforma por donde debe envíar un mensaje de SMS en caso de que
              un número de teléfono celular tenga un prefijo nuevo o no definido.
            </p>
          </li>
          <li>
            <p>
              Los prefijos deben ir separados por coma (,) y no deben tener letras ni caracteres especiales (Ejemplo: 312,320,304)
            </p>
          </li>
          <li><p>Los campos con asterisco(*) son obligatorios.</p></li>
        </ul>
      </div>
    </div>
  </div>
</div>
