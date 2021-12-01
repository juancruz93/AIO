<style>
  .width-250{
    width: 100%;
  }

  .border-left-tr-processing{
    border-left: solid 7px #ff6e00;
  }

  .border-left-tr-processed{
    border-left: solid 7px #00c1a5;
  }
</style>
<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 ">
    <div class="row">
      <div class="col-xs-4 col-sm-4 col-lg-4  text-left">
        <div class="input-group">
          <span class=" input-group-addon" id="basic-addon1" data-toggle="tooltip" data-placement="top" title="Buscar">
            <i class="fa fa-search-plus" aria-hidden="true" ></i>
          </span>
          <input class="form-control"  id="name" placeholder="Buscar" ng-keyup="searchcontacts()" ng-model="search" />
        </div>
      </div> 
      <div class="col-xs-8 col-sm-8 col-lg-8 text-right pull-right ">
        <a href="{{ url("contactlist/show")}}">
          <button class="button  btn btn-md default-inverted">
            Regresar a las listas de contactos
          </button>
        </a>
        <a href="#/newsegment">
          <button class="button  btn btn-md info-inverted">
            Crear un segmento
          </button>
        </a>
      </div> 
    </div>
  </div>
</div>
<div ng-show="segment[0].items.length > 0">
  <div id="pagination" class="text-center">
    <ul class="pagination">
      <li ng-class="page == 1 ? 'disabled'  : ''">
        <a  href="#/" ng-click="page == 1 ? true  : false || fastbackward()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
      </li>
      <li  ng-class="page == 1 ? 'disabled'  : ''">
        <a href="#/"  ng-click="page == 1 ? true  : false || backward()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
      </li>
      <li>
        <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>
            {{ "{{segment.total }}"}}
          </b> registros </span><span>Página <b>
          {{"{{ page }}"}}
          </b> de <b>
            {{ "{{ (segment.total_pages ) }}"}}
          </b></span>
      </li>
      <li   ng-class="page == (segment.total_pages) || segment.total_pages == 0 ? 'disabled'  : ''">
        <a href="#/" ng-click="page == (segment.total_pages)  || segment.total_pages == 0  ? true  : false || page == (segment.total_pages)  || segment.total_pages == 0  ? true  : false || forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
      </li>
      <li   ng-class="page == (segment.total_pages)  || segment.total_pages == 0 ? 'disabled'  : ''">
        <a ng-click="page == (segment.total_pages)  || segment.total_pages == 0  ? true  : false || fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
      </li>
    </ul>
  </div>
  <div data-ng-show="loaderList" >
    <md-progress-linear md-mode="indeterminate" class="md-warn" ></md-progress-linear>
  </div>
  <div class="table-responsive">
    <table class="table table-bordered table-responsive" id="resultTable">
      <thead class="theader">
        <tr style="border-left: solid 4px #474646;">
          <th>Nombre</th>
          <th>Detalle</th>
          <th>Total Contactos</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody ng-repeat="key in segment[0].items track by $index">
        <tr data-ng-class="{'border-left-tr-processing':key.status == 'processing', 'border-left-tr-processed': key.status == 'processed'}">
          <td width="45%">
            <div class="medium-text">
              <a href="{{ url('sxc/contactsegment/' ~ "{{key.idSegment}}") }}">
                {{"{{key.name}}"}}
              </a>
              <br/>
              <em class="extra-small-text">
                {{"{{key.status == 'processed' ? 'Contactos procesados' : 'Procesando contactos'}}"}}
              </em>
            </div>
            <div class="small-text">
              <em>
                {{ '{{key.description}}' }}
              </em>
            </div>
            <div class="extra-small-text">
              Creada por <strong>{{"{{ key.createdBy }}"}}</strong> el dia <strong>{{"{{ key.created * 1000  | date : 'yyyy-MM-dd' }}"}}</strong><br>
              Actualizada por <strong>{{"{{ key.updatedBy }}"}}</strong> el dia <strong>{{"{{ key.updated * 1000 | date : 'yyyy-MM-dd' }}"}}</strong>
            </div>
          </td>
          <td>
            <div class="row wrap ">
              <div class="inline-block none-padding width-250">
                <div class="info medium-text">Listas de contactos</div>
                <span><i>{{"{{key.contactlist | implode : ','}}"}}</i></span>
              </div>   
            </div> 
          </td>
          <td width="12%" class="text-center">
            <span class="info medium-text">{{"{{ key.totalSxc }}"}}</span>
          </td>
          <td width="12%" class="text-right">
            <a href="" class="button shining btn btn-xs-round round-button danger-inverted" title="Eliminar segmento" data-ng-click="confirmDelete(key.idSegment)" data-ng-disabled="key.status == 'processing'">
              <span class="glyphicon glyphicon-trash"></span>
            </a>
            <a href="#/editsegment/{{'{{key.idSegment}}'}}" class="button btn btn-xs-round info-inverted" title="Editar este Usuario" data-ng-disabled="key.status == 'processing'">
              <span class="glyphicon glyphicon-pencil"></span>
            </a>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
  <div id="pagination" class="text-center">
    <ul class="pagination">
      <li ng-class="page == 1 ? 'disabled'  : ''">
        <a  href="#/" ng-click="page == 1 ? true  : false || fastbackward()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
      </li>
      <li  ng-class="page == 1 ? 'disabled'  : ''">
        <a href="#/"  ng-click="page == 1 ? true  : false || backward()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
      </li>
      <li>
        <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>
            {{ "{{segment.total }}"}}
          </b> registros </span><span>Página <b>
          {{"{{ page }}"}}
          </b> de <b>
            {{ "{{ (segment.total_pages ) }}"}}
          </b></span>
      </li>
      <li   ng-class="page == (segment.total_pages) || segment.total_pages == 0 ? 'disabled'  : ''">
        <a href="#/" ng-click="page == (segment.total_pages)  || segment.total_pages == 0  ? true  : false || page == (segment.total_pages)  || segment.total_pages == 0  ? true  : false || forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
      </li>
      <li   ng-class="page == (segment.total_pages)  || segment.total_pages == 0 ? 'disabled'  : ''">
        <a ng-click="page == (segment.total_pages)  || segment.total_pages == 0  ? true  : false || fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
      </li>
    </ul>
  </div>
</div>
<br>
<div ng-hide="segment[0].items.length > 0" class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div class="block block-success">
      <div class="body success-no-hover text-center">
        <h2 ng-hide="stringsearch != -1">
          La lista de segmentos se encuentra vacía, para crear un segmento haga <a href="#/newsegment">clic aquí</a>.
        </h2>    
        <h2 ng-hide="stringsearch == -1">
          No hay coincidencias con la búsqueda
        </h2>    
      </div>
    </div>
  </div>
</div>
<div id="somedialog" class="dialog">
  <div class="dialog__overlay"></div>
  <div class="dialog__content">
    <div class="morph-shape">
      <svg xmlns="https://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 560 280" preserveAspectRatio="none">
      <rect x="3" y="3" fill="none" width="556" height="276"/>
      </svg>
    </div>
    <div class="dialog-inner">
      <h2>¿Esta seguro?</h2>
      <div>
        Tenga en cuenta que si elimina el segmento no podrá volver a usarlo ni recuperarlo.
      </div>
      <br>
      <div>
        <a onClick="closeModal();" class="button shining btn btn-md danger-inverted" data-dialog-close>Cancelar</a>
        <a href="#/" data-ng-click="deleteSegment() " id="btn-ok" class="button shining btn btn-md success-inverted">Confirmar</a>
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
