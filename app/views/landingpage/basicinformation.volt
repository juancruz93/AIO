<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="subtitle">
      <em>Información básica de la Landing Page</em>
    </div>
    <br>
    <p class="small-text">
      Configura la información básica acerca de la Landing Page, como un nombre del sitio para identificar la Landing. 
    </p>
  </div>
</div>
<div class="row">  
  <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 wrap">
    <form class="form-horizontal" >
      <div class="block block-primary">
        <div class="body row">
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap" >
            <div class="body form-horizontal">
              <div class="form-group">
                <div class="col-sm-12">
                  <label>*Nombre de la Landing</label>                
                  <input ng-model="data.name" type="text" placeholder="Nombre de la landing" class="form-control" minlength="2" maxlength="45">
                </div>
              </div>
              <div class="form-group">
                <div class="col-sm-12">
                  <label>Nombre del autor</label>  
                  <input ng-model="data.nameauthor" type="text" placeholder="Nombre del autor" class="form-control" minlength="2" maxlength="45">              
                </div>
              </div>
              <div class="form-group">
                <div class="col-sm-12">
                  <label>*Correo electrónico</label>
                  <input ng-model="data.email" type="text" placeholder="Correo electrónico" class="form-control" minlength="2" maxlength="45">
                </div>
              </div>
              <div class="col-md-12">
                <div class="form-group" data-ng-class="{'has-success':form.country.$valid}">
                  <label for="country"><strong>*País</strong></label>
                  <ui-select ng-model="data.idCountry" ng-required="true" ui-select-required theme="select2"
                             sortable="false" style="width: 100% !important" close-on-select="true" data-ng-change="resServices.states(data.idCountry)">
                    <ui-select-match placeholder="Selecciona un País">{{ "{{$select.selected.name}}" }}</ui-select-match>
                    <ui-select-choices repeat="key.idCountry as key in data.listcountry | propsFilter: {name: $select.search}">
                      <div ng-bind-html="key.name | highlight: $select.search"></div>
                    </ui-select-choices>
                  </ui-select>
                  <div class="validation" data-ng-show="form.$submitted || form.country.$touched">
                    <span class="help-block" data-ng-show="form.country.$error.required">Debe seleccionar un país</span>
                  </div>
                </div>
              </div>

              <div class="col-md-12">
                <div class="form-group">
                     <label for="state"><strong>*Departamento/Estado</strong></label>
                  <ui-select ng-model="data.idState" ng-required="true" ui-select-required theme="select2"
                             sortable="false" style="width: 100% !important" close-on-select="true" data-ng-change="resServices.cities(data.idState)">
                    <ui-select-match placeholder="Selecciona un Departamento / Estado / Provincia">{{ "{{$select.selected.name}}" }}</ui-select-match>
                    <ui-select-choices repeat="key.idState as key in data.liststates | propsFilter: {name: $select.search}">
                      <div ng-bind-html="key.name | highlight: $select.search"></div>
                    </ui-select-choices>
                  </ui-select>
                  <div class="validation" data-ng-show="form.$submitted || form.country.$touched">
                    <span class="help-block" data-ng-show="form.state.$error.required">Debe seleccionar un Departamento / Estado / Provincia </span>
                  </div>
                </div>
              </div>

              <div class="col-md-12">
                <div class="form-group"
                     <label for="city"><strong>*Ciudad</strong></label>
                  <ui-select ng-model="data.idCity" ng-required="true" ui-select-required theme="select2"
                             sortable="false" style="width: 100% !important" close-on-select="true">
                    <ui-select-match placeholder="Seleccione una ciudad">{{ "{{$select.selected.name}}" }}</ui-select-match>
                    <ui-select-choices repeat="key.idCity as key in data.listcities | propsFilter: {name: $select.search}">
                      <div ng-bind-html="key.name | highlight: $select.search"></div>
                    </ui-select-choices>
                  </ui-select>
                  <div class="validation" data-ng-show="form.$submitted || form.city.$touched">
                    <span class="help-block" data-ng-show="form.city.$error.required">Debe seleccionar un pais </span>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <div class="col-md-12">
                  <label>Dirección</label>  
                  <input ng-model="data.address" type="text" placeholder="Dirección" class="form-control" minlength="2" maxlength="50">              
                </div>
              </div>
              <div class="form-group">
                <div class="col-md-12">
                  <label>*Nombre del sitio Web</label>   
                  <input ng-model="data.website" type="text" placeholder="Nombre del sitio Web" class="form-control" minlength="2" maxlength="45">  
                </div>
              </div>
              <div class="form-group">
                <div class="col-md-12">
                  <label>NIT</label>              
                  <input ng-model="data.nit" type="number" placeholder="NIT" class="form-control" minlength="2" maxlength="45">  
                </div>
              </div>        
              <div class="form-group">
                <div class="col-md-12">
                  <label>Descripción</label>              
                  <textarea ng-model="data.description" type="text" placeholder="Descripción" class="form-control" minlength="2" maxlength="200">  
                  </textarea>
                </div>
              </div>
              <div class="col-md-12">
                <div class="form-group" data-ng-show="!misc.showNewCateg">
                  <label>*Categoría:</label>
                  <div class="row">
                    <div class="col-xs-11 col-sm-11 col-md-11 col-lg-11">
                      <ui-select ng-model="data.idCategoryLanding" ng-required="true" ui-select-required theme="select2"
                                 sortable="false" style="width: 100% !important" close-on-select="true" >
                        <ui-select-match placeholder="Selecciona una categoría">{{ "{{$select.selected.name}}" }}</ui-select-match>
                        <ui-select-choices repeat="key.idLandingPageCategory as key in data.landingCategory | propsFilter: {name: $select.search}">
                          <div ng-bind-html="key.name | highlight: $select.search"></div>
                        </ui-select-choices>
                      </ui-select>
                    </div>
                    <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
                      <a class="positive tooltip-de" data-placement="top" title="" href="" data-ng-click="functions.showNewCateg()" data-original-title="Nueva categoría">
                        <i class="fa fa-plus fa-2x" style="margin-right: 80%"></i>
                      </a>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-12">
                <div class="form-group" data-ng-show="misc.showNewCateg">
                  <label>Nueva categoría</label>
                  <div class="row">
                    <div class="col-xs-10 col-sm-10 col-md-10 col-lg-10">
                      <input type="text" class="undeline-input form-control" maxlength="80" data-ng-model="misc.newcateg">
                    </div>
                    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 text-right">
                      <a class="negative tooltip-de" data-toggle="tooltip" data-placement="top" title="Cancelar" href="" data-ng-click="functions.hideNewCateg()"><i class="fa fa-times fa-2x"></i></a>
                      <a class="positive tooltip-de" data-toggle="tooltip" data-placement="top" title="Guardar" href="" data-ng-click="functions.saveNewCateg()"><i class="fa fa-check fa-2x"></i></a>
                    </div>
                  </div>
                </div>
              </div>   
            </div>
          </div>
        </div>
        <div class="footer text-right">          
          <a ui-sref="index"
             class="button btn btn-small danger-inverted"
             data-toggle="tooltip" data-placement="top" title="Cancelar">
            Cancelar
          </a>
          <button class="button btn btn-small success-inverted" title="Guardar y continuar" ng-click="functions.saveBasicInformation()">
            Guardar y continuar
          </button>          
        </div>
      </div>
    </form>
  </div>

  <div class="col-xs-4 col-sm-4 col-md-6 col-lg-6 wrap none-paddign">
    <div class="fill-block fill-block-primary">
      <div class="header">
        Instrucciones
      </div>
      <div class="body">
        <p>
          Antes de comenzar, por favor lea atentamente la siguiente información:
        <ul>
          <li>El nombre de la Landing, solo se usa para que identificarla en su lista de Landing.</li>          
          <li>El nombre de la Landing debe tener un máximo de 45 caracteres.</li>
          <li>El nombre del autor debe contener un máximo de 45 caracteres.</li>
          <li>El correo debe contener la siguiente estructura prueba@gmail.com y un máximo de 45 caracteres.</li>                   
          <li>El campo (Nombre del sitio Web) hace referencia al nombre que aparece en la URL de su Landing Page</li>
          <li>El nombre del sitio Web debe contener un máximo de 45 caracteres</li>
          <li>El NIT debe contener un máximo de 45 caracteres.</li>
          <li>La descripción debe contener un máximo de 200 caracteres.</li>                  
          <li>Los campos con asterisco (*), son obligatorios.</li>                 
        </ul>
        </p>
      </div>
    </div>
  </div>      
</div>

