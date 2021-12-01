<div class='col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap'>
  <div class="form-group">
    <label >Enlaces de la plantilla</label>
    <ui-select 
      multiple 
      ng-model="selected.linksTemplateSelected" 
      ng-required="true" 
      ui-select-required 
      class='min-width-100'
      theme="select2" 
      title="" 
      sortable="false" 
      close-on-select="true" 
      ng-change="countContacts()"
    >
      <ui-select-match >{{"{{$item.name}}"}}</ui-select-match>
      <ui-select-choices repeat="key as key in listLinksTemplate | propsFilter: {name: $select.search}">
        <div ng-bind-html="key.name | highlight: $select.search"></div>
      </ui-select-choices>
    </ui-select>
  </div>
</div>

<div class="clearfix"></div>
<div class="footer" align="right">                                                
  <a  ng-click="closePopover()"class="danger-no-hover" style="cursor:pointer;" data-toggle="tooltip" data-placement="top" title="Cancelar">
    <span class="glyphicon glyphicon-remove"></span>
  </a>
  <a  ng-click="refreshData()" class="primary-no-hover" style="cursor:pointer;" data-toggle="tooltip" data-placement="top" title="Actualizar">
      <i ng-class="classRefreshrotate ? 'fa fa-refresh fa-spin fa-fw': 'fa fa-refresh fa-fw'"></i>
    </a>
  <a ng-click="applyListSelectedClick()" class="success-no-hover"  style="cursor:pointer;" data-toggle="tooltip" data-placement="top" title="Guardar">
    <span class="glyphicon glyphicon-ok"></span>
  </a>
</div>