<div class='col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap'>

  <div class='form-group'>
    <label>*Selección de destinatarios</label>
    <ui-select ng-change="setListChange(selected.list)" ng-model="selected.list" ng-required="true"  ui-select-required  class='min-width-100' 
               theme="select2" title=""  sortable="false" close-on-select="true">
      <ui-select-match placeholder="Seleccione uno">{{ "{{$select.selected.name}}" }}</ui-select-match>
      <ui-select-choices repeat="key in listDestinary | propsFilter: {name: $select.search}">
        <div ng-bind-html="key.name | highlight: $select.search"></div>
      </ui-select-choices>
    </ui-select>
  </div>
  <div>
    <label>*Destinatarios</label>
    <ui-select multiple ng-model="selected.selected" ng-required="true"  ui-select-required  class='min-width-100' 
               theme="select2" title=""  sortable="false" close-on-select="true">
      <ui-select-match >{{"{{$item.name}}"}}</ui-select-match>
      <ui-select-choices repeat="key in list | propsFilter: {name: $select.search}">
        <div ng-bind-html="key.name | highlight: $select.search"></div>
      </ui-select-choices>
    </ui-select>
  </div>    
  <div class="form-group" >
    <p class="text-danger" ng-show="selected.error">Todos los campos son obligarotios.</p>
  </div>
  <div class="clearfix"></div>
  <div class="footer" align="right">                                                
    <a  ng-click="closePopover()" class="danger-no-hover" style="cursor:pointer;" data-toggle="tooltip" data-placement="top" title="Cancelar">
      <span class="glyphicon glyphicon-remove"></span>
    </a>
    <a ng-click="applyListSelected()" class="success-no-hover" style="cursor:pointer;" data-toggle="tooltip" data-placement="top" title="Guardar">
      <span class="glyphicon glyphicon-ok"></span>
    </a>
  </div>
</div>
