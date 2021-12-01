<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="title">
      Lista de categorías de correo
    </div>            
    <hr class="basic-line" />
  </div>
</div>


<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="col-sm-4 col-lg-4 col-md-4 col-xs-4 searchBoxFilter">
      <div class="input-group">
        <input class="form-control"  id="name" ng-change='functions.search()' placeholder="Buscar por nombre" ng-model="data.filter.name" />
        <span class=" input-group-addon" id="basic-addon1" >
          <i class="fa fa-search"></i>
        </span>
      </div>
    </div>  
    <div class="col-md-offset-1 col-md-3 col-lg-3 col-sm-3 col-xs-3 dateInitialBox">
      <!-- campo fecha inicial -->
      <div class="dropdown form-group dropdown-start-parent">
        <a class="dropdown-toggle" id="dropdownStart" role="button" data-toggle="dropdown" data-target=".dropdown-start-parent"
           href="#">
          <div class="input-group date">
            <input type="text" class="form-control" placeholder="Fecha Inicial" readonly="true" ng-model="data.filter.dateinitial" data-date-time-input="YYYY-MMM-DD" style="background-color: white">
            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
          </div>
        </a>
        <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
          <datetimepicker data-ng-model="data.filter.dateinitial"
                          data-datetimepicker-config="{ dropdownSelector: '#dropdownEnd', renderOn: 'end-date-changed', startView: 'month', minView: 'day', modelType: 'YYYY-MM-DD' }"
                          ></datetimepicker>
        </ul>
      </div>  
    </div>

    <div class="col-md-3 col-lg-3 col-sm-3 col-xs-3 dateFinalBox">
      <!-- campo fecha Final -->
      <div class="dropdown form-group dropdown-end-parent">
        <a class="dropdown-toggle" id="dropdownEnd" role="button" data-toggle="dropdown" data-target=".dropdown-end-parent"
           href="#">
          <div class="input-group date">
            <input type="text" class="form-control" placeholder="Fecha Final" readonly="true" ng-model="data.filter.dateend" data-date-time-input="YYYY-MMM-DD" style="background-color: white">
            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
          </div>
        </a>
        <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
          <datetimepicker data-ng-model="data.filter.dateend"
                          data-datetimepicker-config="{ dropdownSelector: '#dropdownEnd', renderOn: 'end-date-changed', startView: 'month', minView: 'day', modelType: 'YYYY-MM-DD' }"
                          ></datetimepicker>
        </ul>

      </div> 
    </div>

    <div class="col-md-1 col-lg-1 col-xs-1 col-sm-1 text-right refreshBoxFuera">
      <div class="dropdown form-group dropdown-end-parent refreshBoxDentro">
        <button type="button" title="Borrar Filtros" data-ng-click="functions.refresh()" class="btn btn-danger glyphicon glyphicon-erase warning-inverted" ></button>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class=" col-sm-2 col-lg-12 wrap text-right">
    <a href="{{url('mail')}}" class=" btn btn-md default-inverted">Regresar al listado de envíos </a>
    <a href="#/add"><button class="button shining btn btn-md success-inverted">Agregar una categoría</button></a>
  </div>
</div>

{{'{{data.mensaje}}'}}


<div ng-show="data.mailcategory.items.length > 0" class="">
  
  <div id="pagination" class="text-center">
    <ul class="pagination">
        <li ng-class="data.page == 1 ? 'disabled'  : ''">
          <a  href="#/" ng-click="data.page == 1 ? true  : false || functions.fastbackward()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
        </li>
        <li  ng-class="data.page == 1 ? 'disabled'  : ''">
          <a href="#/"  ng-click="data.page == 1 ? true  : false || functions.backward()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
        </li>
        <li>
          <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{data.mailcategory.total}}"}}
            </b> registros </span><span>Página <b>{{"{{ data.page }}"}}
            </b> de <b>
              {{ "{{ (data.mailcategory.total_pages ) }}"}}
            </b></span>
        </li> 
        <li   ng-class="data.page == (data.mailcategory.total_pages)  || data.mailcategory.total_pages == 0  ? 'disabled'  : ''">
          <a href="#/" ng-click="data.page == (data.mailcategory.total_pages)  || data.mail_category.total_pages == 0  ? true  : false || functions.forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
        </li>
        <li   ng-class="data.page == (data.mailcategory.total_pages)  || data.mailcategory.total_pages == 0  ? 'disabled'  : ''">
          <a ng-click="data.page == (data.mailcategory.total_pages)  || data.mailcategory.total_pages == 0  ? true  : false || functions.fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
        </li>
    </ul>
        
  </div>
         
            
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
      <table class="table table-bordered">
        <thead class="theader ">
          <tr>
            <th>Nombre</th>
            <th>Descripcion</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr ng-repeat="mailcategory in data.mailcategory.items" >
            <td>
              <div class="medium-text">
                {{ '{{mailcategory.name}}' }}
              </div>
        <dd class="small-text"> </dd>
        <dd> <em class="extra-small-text">Creado por <strong>{{"{{(mailcategory.createdBy)}}"}}</strong> , el <strong>{{"{{mailcategory.createdDate}}"}}</strong> a las <strong>{{"{{mailcategory.createdHour}}"}}</strong></em></dd>
        <dd> <em class="extra-small-text">Actualizado por <strong> {{"{{(mailcategory.updatedBy)}}"}}</strong>, el  <strong>{{"{{mailcategory.updatedDate}}"}}</strong> a las <strong>{{"{{(mailcategory.updatedHour)}}"}}</strong></em></dd>
        </td>
        <td>
          <div class="small-text">
            <em>
              {{ '{{mailcategory.description}}' }}
            </em>
          </div>
        </td>
        <td>
          <div class="pull-right">
            <a ui-sref="edit({idMail:mailcategory.idMailCategory})" class="button shining btn btn-xs-round shining-round round-button primary-inverted" data-toggle="tooltip" data-placement="left" title="Editar base de datos">
              <span class="glyphicon glyphicon-pencil"></span>
            </a>
            <a style="cursor:pointer;" ng-click="functions.openModal(mailcategory.idMailCategory)" class="button shining btn btn-xs-round shining-round round-button danger-inverted" data-toggle="tooltip" data-placement="left" title="Eliminar base de datos" >
              <span class="glyphicon glyphicon-trash"></span>
            </a>
          </div>
        </td>
        </tr>
        </tbody>
      </table>
    </div>          
            
            
  <div id="pagination" class="text-center">
    <ul class="pagination">
        <li ng-class="data.page == 1 ? 'disabled'  : ''">
          <a  href="#/" ng-click="data.page == 1 ? true  : false || functions.fastbackward()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
        </li>
        <li  ng-class="data.page == 1 ? 'disabled'  : ''">
          <a href="#/"  ng-click="data.page == 1 ? true  : false || functions.backward()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
        </li>
        <li>
          <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{data.mailcategory.total }}"}}
            </b> registros </span><span>Página <b>{{"{{ data.page }}"}}
            </b> de <b>
              {{ "{{ (data.mailcategory.total_pages ) }}"}}
            </b></span>
        </li> 
        <li   ng-class="data.page == (data.mailcategory.total_pages)  || data.mailcategory.total_pages == 0  ? 'disabled'  : ''">
          <a href="#/" ng-click="data.page == (data.mailcategory.total_pages)  || data.mail_category.total_pages == 0  ? true  : false || functions.forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
        </li>
        <li   ng-class="data.page == (data.mailcategory.total_pages)  || data.mailcategory.total_pages == 0  ? 'disabled'  : ''">
          <a ng-click="data.page == (data.mailcategory.total_pages)  || data.mailcategory.total_pages == 0  ? true  : false || functions.fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
        </li
      </ul>
    </ul>        
  </div>
            
</div>



<br>
<div ng-show="data.mailcategory.items.length == 0">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="block block-success">
      <div class="body success-no-hover text-center">
        <h2>
          No hay registros de categoria de correo, para crear una haga <a href="#/add">clic aquí</a>.
        </h2>    
        </h2>    
      </div>
    </div>
  </div>
</div>

<div id="deleteDialog" class="dialog ">
  <div class="dialog__overlay"></div>
  <div class="dialog__content">
    <div class="morph-shape ">
      <svg xmlns="https://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 560 280" preserveAspectRatio="none">
      <rect x="3" y="3" fill="none" width="556" height="276"/>
      </svg>
    </div>
    <div class="dialog-inner dialog-padding">
      <p ng-show="PlaneError.error == 1">{{'{{PlaneError.msg}}'}}</p>
      <div class="body row text-center">
        <h3>¿Desea eliminar la categoria?</h3>
      </div>
      <div>
        <h5>Si se elimina la categoría no la podrá volver a usar pero seguirá asociada a los envíos en los que la haya usado.</h5>
      </div>
      <div class="body row" style="padding-top: 1em;">                    
        <a ng-click="functions.closeModal()" class="button shining btn btn-md danger-inverted" data-dialog-close>Cancelar</a>
        
        <a ng-click="restServices.deleteCategory(data.mailcategory.idMailCategory)"  id="btn-ok" class="button shining btn btn-md success-inverted">Eliminar</a>
      </div>
    </div>
  </div>
</div> 