<hr/>

<div  class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 none-paddign">
    
    <button class="button btn success-inverted pull-right" ng-click="AddForm()">Guardar y finalizar</button>
    <a ui-sref="list()"
             class="button btn btn-small danger-inverted pull-right"
             data-toggle="tooltip" data-placement="top" title="Cancelar">
            Cancelar
          </a>
  </div>
</div>
<hr/>
<div  class="row">
{#  <pre>{{"{{form}}"}}</pre>#}
  <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8 none-paddign">
    <div class="block block-info">
      <div class="body row" >
        <md-progress-linear md-mode="query" ng-show="!complet" class="md-warn"></md-progress-linear>
        <div class="container-fluid" ng-if="complet">
          <div fb-builder="default" ng-style="{'background-color':backgroundForm}"></div>
        </div>
      </div>
    </div>
  </div>    

  <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4 none-paddign">
    <md-tabs md-dynamic-height md-border-bottom >
      <md-tab label="Primarios" >
        <md-content class="md-padding">
          <div class="col-md-12 no-margin text-center">
            <button ng-repeat="primaryField in arrFields | filter:'primary'"  class="button btn " ng-class="!primaryField.selected?'primary-inverted':'default-inverted'" style="width: 100%;" ng-click="addComponent(primaryField)">{{"{{primaryField.title}}"}}</button>
          </div>
        </md-content>
      </md-tab>
      <md-tab label="Personalizados">
        <md-content flex class="md-padding">
          <div class="col-md-12 no-margin text-center">
            <button ng-repeat="primaryField in arrFields | filter:'custom'" class="button btn " ng-class="!primaryField.selected?'primary-inverted':'default-inverted'" style="width: 100%;" ng-click="addComponent(primaryField)">{{"{{primaryField.title}}"}}</button>
          </div>  
        </md-content>
      </md-tab>
      <md-tab label="Configuracion General">
        <md-content class="md-padding">
          <div class="col-md-12 no-margin text-center">
            <button ng-repeat="primaryField in arrFields | filter:'encabezado'" class="button btn " ng-class="!primaryField.selected?'primary-inverted':'default-inverted'" style="width: 100%;" ng-click="addComponent(primaryField)">Encabezado</button>
            <hr class="basic-line"/>
            <div class="row">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 none-paddign ">
                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4  text-left"><label type="text" class="small-text">Fondo: </label></div>
                <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8 text-left">
                  <spectrum-colorpicker  ng-model="backgroundForm" format="'rgb'"
                                         options="{
                                         showInput: true,
                                         showAlpha: true,
                                         allowEmpty: true,
                                         showPalette: true,
                                         palette: [['black', 'white', 'blanchedalmond'],['rgb(255, 128, 0);', 'hsv 100 70 50', 'lightyellow']]
                                         }">
                  </spectrum-colorpicker>
                </div>
              </div> 
            </div>

            <hr class="basic-line"/>
            <div class="row">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 none-paddign ">
                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4  text-left"><label type="text" class="small-text">Fuente: </label></div>
                <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8 text-left">
                  <spectrum-colorpicker  ng-model="fontForm" format="'rgb'" ng-change="configForm('fontColor',fontForm)"
                                         options="{
                                         showInput: true,
                                         showAlpha: true,
                                         allowEmpty: true,
                                         showPalette: true,
                                         palette: [['black', 'white', 'blanchedalmond'],['rgb(255, 128, 0);', 'hsv 100 70 50', 'lightyellow']]
                                         }">
                  </spectrum-colorpicker>
                </div>
              </div> 
            </div> 
            <hr class="basic-line"/>
            <div class="row">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 none-paddign ">
                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4 text-left"><label type="text" class="small-text">Tamaño: </label></div>
                <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8 pull-left">
                  <select class="form-control" ng-model="sizeForm" ng-change="configForm('sizeStyle',sizeForm)">
                    <option value="8px">8</option>
                    <option value="10px">10</option>
                    <option value="12px">12</option>
                    <option value="14px">14</option>
                    <option value="18px">18</option>
                    <option value="24px">24</option>
                    <option value="36px">36</option>
                  </select>
                </div>
              </div> 
            </div> 
            <hr class="basic-line"/>
            <div class="row">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 none-paddign ">
                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4 text-left"><label type="text" class="small-text">Tipografia: </label></div>
                <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8 pull-left">
                  <select class="form-control" ng-model="fontStyle" ng-change="configForm('fontStyle',fontStyle)">
                    <option value="Arial">Arial</option>
                    <option value="Courier New">Courier New</option>
                    <option value="Verdana">Verdana</option>
                    <option value="Comic Sans MS">Comic Sans MS</option>
                    <option value="Georgia">Georgia</option>
                    <option value="Times New Roman">Times New Roman</option>
                  </select>
                </div>
              </div> 
            </div> 
          </div>  
        </md-content>
      </md-tab>
    </md-tabs>
  </div>
</div>

{#  <div class="row">
    <h2>Form</h2>
    <hr/>
    <form class="form-horizontal">
      <div ng-model="input" fb-form="default" fb-default="defaultValue"></div>
      <div class="form-group">
        <div class="col-md-8 col-md-offset-4">
          <input type="submit" ng-click="submit()" class="btn btn-default"/>
        </div>
      </div>
    </form>
    <div class="checkbox">
      <label><input type="checkbox" ng-model="isShowScope" ng-init="isShowScope=true" />
        Show scope
      </label>
    </div>
    <pre ng-if="isShowScope">{{"{{input}}"}}</pre>
  </div>#}

