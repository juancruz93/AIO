<script type="text/javascript">
  $(document).ready(function () {
    $('[data-toggle="popover"]').popover();
  });
</script>


<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div class="title">
      Lista de bloqueos
    </div>
    <hr class="basic-line" />
  </div>
  <div class=" col-xs-12 col-sm-12 col-md-12 col-lg-12" >
    <div class="text-center">
      <div class="inline-block text-center none-padding">
        <strong>Contactos bloqueados totales:</strong>
        <br>  
        <span class="info medium-text">{{ "{{blockade.total }}"}}</span>
      </div>    
    </div>  
  </div>
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 ">
    <div class="row">
      <div class="col-xs-4 col-sm-4 col-lg-4  text-left">
        <div class="input-group">
          <span  class=" input-group-addon cursor" id="basic-addon1" placement="top" class="" data-toggle="popover" title="Instrucciones" 
                 data-content="Buscar por correo o por movil">
            <i class="fa fa-question-circle" aria-hidden="true" ></i>
          </span>
          <input class="form-control"  id="name" placeholder="Buscar por correo o número móvil" ng-keyup="searchBlocked()" ng-model="search" />
        </div>
      </div>
      <div class="col-xs-8 col-sm-8 col-lg-8 text-right pull-right ">
        <a href="{{ url("contactlist/show")}}">
          <button class="button  btn btn-md default-inverted">
            Regresar a la lista de contactos
          </button>
        </a>
        <a href="#/new">
          <button class="button  btn btn-md info-inverted">
            Bloquear un contacto
          </button>
        </a>
      </div> 
    </div>
  </div>
</div>
<div ng-hide="showblockade">
  <div id="pagination" class="text-center">
    <ul class="pagination">
      <li ng-class="page == 1 ? 'disabled'  : ''">
        <a  href="#/" ng-click="page == 1 ? true  : false || fastbackward()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
      </li>
      <li  ng-class="page == 1 ? 'disabled'  : ''">
        <a href="#/"  ng-click="page == 1 ? true  : false || backward()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
      </li>
      <li>
        <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{blockade.total }}"}}
          </b> registros </span><span>Página <b>{{"{{ page }}"}}
          </b> de <b>
            {{ "{{ (blockade.total_pages ) }}"}}
          </b></span>
      </li>
      <li   ng-class="page == (blockade.total_pages) || blockade.total_pages == 0 ? 'disabled'  : ''">
        <a href="#/" ng-click="page == (blockade.total_pages)  || blockade.total_pages == 0  ? true  : false || page == (blockade.total_pages)  || blockade.total_pages == 0  ? true  : false || forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
      </li>
      <li   ng-class="page == (blockade.total_pages)  || blockade.total_pages == 0 ? 'disabled'  : ''">
        <a ng-click="page == (blockade.total_pages)  || blockade.total_pages == 0  ? true  : false || fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
      </li>
    </ul>
  </div>
  <div class="">
    <table class="table table-bordered table-responsive sticky-enabled" >
      <thead class="theader">
        <tr>
          <th>
            Correo/movil
          </th>
          <th>
            Fecha
          </th>
          <th>
            Razon del bloqueo
          </th>
          <th>
            Acciones
          </th>
        </tr>
      </thead>
      <tbody ng-repeat="key in blockade[0].items">
        <tr>
          <td class="cursor" data-toggle="collapse" data-target="#allinfo{{ '{{key.idBlocked}}' }}" aria-expanded="false" >
            {{"{{key.email }}"}}
            <div ng-show="key.indicative.length != 0 && key.phone.length != 0">
              (+{{"{{key.indicative }}"}}) {{"{{key.phone }}"}}
            </div>
          </td>
          <td class="cursor" data-toggle="collapse" data-target="#allinfo{{ '{{key.idBlocked}}' }}" aria-expanded="false" 
              aria-controls="allinfo{{ "{{ key.idBlocked }}"}}">
            Creada por <strong>{{"{{ key.createdBy }}"}}</strong> el dia <strong>{{"{{ key.blocked * 1000  | date : 'yyyy-MM-dd' }}"}}</strong>
          </td>
          <td class="cursor" data-toggle="collapse" data-target="#allinfo{{ '{{key.idBlocked}}' }}" aria-expanded="false" >
            {{"{{key.motive }}"}}
          </td>
          <td class="text-right">
            <a ng-click="deleteBlocked(key.idBlocked)" class="button shining btn btn-xs-round shining-round round-button success-inverted" data-toggle="tooltip" data-placement="top"   title="Desbloquear" >
              <span class="fa fa-unlock"></span>
            </a>
          </td>
        </tr>
        <tr id="allinfo{{ "{{ key.idBlocked }}"}}" class="collapse">
          <td colspan="7">
            <div class="row">
              <div class="col-lg-12">
                <div class="block block-info">
                  <div class="body row">
                    <div class="col-lg-6 col-md-6 col-sm-6 text-center col-lg-offset-3">
                      <strong>Lista(s) a la(s) que está suscrito este contacto</strong>
                      <div class="div-border">
                        <br>
                        <ul class="text-left">
                          <li ng-repeat="item in key.contactlist" ng-hide="item == ''"class="small-text">
                            <strong>{{"{{item.name }}"}}</strong>
                          </li>
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>   
              </div>
            </div>
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
        <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{blockade.total }}"}}
          </b> registros </span><span>Página <b>{{"{{ page }}"}}
          </b> de <b>
            {{ "{{ (blockade.total_pages ) }}"}}
          </b></span>
      </li>
      <li   ng-class="page == (blockade.total_pages) || blockade.total_pages == 0 ? 'disabled'  : ''">
        <a href="#/" ng-click="page == (blockade.total_pages)  || blockade.total_pages == 0  ? true  : false || page == (blockade.total_pages)  || blockade.total_pages == 0  ? true  : false || forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
      </li>
      <li   ng-class="page == (blockade.total_pages)  || blockade.total_pages == 0 ? 'disabled'  : ''">
        <a ng-click="page == (blockade.total_pages)  || blockade.total_pages == 0  ? true  : false || fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
      </li>
    </ul>
  </div>
</div>
<br>
<div ng-hide="shownewblockade" class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="block block-success">
      <div class="body success-no-hover text-center">
        <h2>
          La lista de bloqueos se encuentra vacía, para bloquear un correo electrónico haga <a href="#/new">clic aquí</a>.
        </h2>    
      </div>
    </div>
  </div>
</div>
