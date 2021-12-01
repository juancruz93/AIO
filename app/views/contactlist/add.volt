<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="title" >
      Agregar una lista de contactos
    </div>            
    <hr class="basic-line" />
    <p>
      Agregue una lista de contactos, en donde podrá gestionar la base de datos de sus contactos.
    </p>
  </div>
</div>
<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <form name="contactlistForm" class="form-horizontal" role="form" ng-submit="addContactlist()" >
      <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 none-padding-left">
        <div class="block block-info">
          <div class="body">
            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label  class="col-sm-4 text-right">*Nombre</label>
                <span class="input hoshi input-default  col-sm-8">
                  <input type="text" maxlength="50" class="undeline-input" ng-model="contactlist.name"  id="name" name="name" required>
                </span>
              </div>
            </div>
            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label class="col-sm-4 text-right">*Categoría <span class="fa fa-info-circle color-gray drop_info" title="Información"></span></label>
                <span class="input hoshi input-default  col-sm-8">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding">
                    <div class="col-xs-10 col-sm-10 col-md-10 col-lg-10 none-padding">
                      <span class="input hoshi input-default">
                        <div data-ng-show="showInputCategory">
                          <input placeholder="*Nombre de la categoria" data-ng-model="categoryName"
                                 maxlength="200"
                                 class="undeline-input">
                        </div>
                        <div data-ng-show="showCategoryName">
                          <select ng-model="contactlist.idContactlistCategory" class="undeline-input" required="">
                            <option ng-selected="{{"{{categories.idContactlistCategory == contactlist.idContactlistCategory}}"}}"
                                    ng-repeat="category in categories"
                                    value="{{"{{category.idContactlistCategory}}"}}">
                              {{"{{category.name}}"}}
                            </option>
                          </select>
                        </div>
                      </span>
                    </div>
                    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 margin-top">
                      <div data-ng-show="showIconsCategory">
                        <a class="color-primary" data-ng-click="changeStatusNameCategory()" href=""><span
                            class="fa fa-plus " title="Agregar otra categoria"></span></a>
                      </div>
                      <div data-ng-show="showIconsSaveCategory">
                        <a class="negative" data-ng-click="changeStatusNameCategory()" href=""><span
                            class="glyphicon glyphicon-remove"
                            title="Cancelar"></span></a>
                        <a class="positive" data-ng-click="saveCategory()" href=""><span
                            class="glyphicon glyphicon-ok margin-left-10"
                            title="Guardar"></span></a>
                      </div>
                    </div>
                  </div>
                </span>

              </div>
            </div>
            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label  class="col-sm-4 text-right">Descripción</label>
                <span class="input hoshi input-default  col-sm-8">
                  <textarea maxlength="200" class="undeline-input" ng-model="contactlist.description" id="description" name="description"></textarea>
                </span>
              </div>
            </div>
            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label  class="col-sm-4 text-right">Importar campos personalizados de otra lista de contactos</label>
                <span class="input hoshi input-default  col-sm-8">
                  <md-switch ng-click="getContactList()" class="md-primary" aria-label="Disabled switch" ng-model="contactlist.disabledCustomField">
                  </md-switch>
                </span>
              </div>
            </div>

            <div class="form-group" ng-hide="!contactlist.disabledCustomField">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label  class="col-sm-4 text-right">Lista de contactos</label>
                <div class="input hoshi input-default  col-sm-8">
                  <ui-select ng-model="contactlist.idContactlist" theme="select2"   title="Choose a person">
                    <ui-select-match placeholder="Seleccione una lista de contacto">{{"{{$select.selected.name}}"}}</ui-select-match>
                    <ui-select-choices repeat="key in contactlists | propsFilter: {name: $select.search}">
                      <div ng-bind-html="key.name | highlight: $select.search"></div>
                    </ui-select-choices>
                  </ui-select>
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

    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
        <div class="fill-block fill-block-primary" >
          <div class="header">
            Información
          </div>
          <div class="body">
            <p>
              Recuerde tener en cuenta estas recomendaciones:
            <ul>
              <li>El nombre de la lista de contactos debe ser un nombre único, es decir, no pueden existir dos listas de contactos con el mismo nombre.</li>
              <li>Recuerde que los campos con asterisco(*) son obligatorios</li>
            </ul>
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
