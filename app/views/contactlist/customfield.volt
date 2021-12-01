<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="title" >
      Listas de campos personalizados de la lista <b>{{ '{{customfield[0].nameContactlist}}' }}</b>
    </div>            
    <hr class="basic-line" />
    <p>
      Administre las los campos personalizados, donde podrá agregar campos personalizados nuevos que luego podrá usar para crear contactos nuevos
    </p>
  </div>
</div>

<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">

    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="pull-right">
          <a href="{{ url('contact/index/') }}{{"{{idContactlist}}"}}">
            <button class="button shining btn btn-md default-inverted">
              Regresar a la lista de contactos
            </button>
          </a>
          {#          <a href="#/addcustomfield/{{"{{idContactlist}}"}}">#}
          <button ng-click="permissionCustomfield()" class="button shining btn btn-md success-inverted">
            Agregar campo personalizado
          </button>
          {#          </a>#}
        </div>
      </div>
    </div>

    <div ng-show="customfield[1].items.length > 0">
      <div id="pagination" class="text-center">
        <ul class="pagination">
          <li ng-class="page == 1 ? 'disabled'  : ''">
            <a   ng-click="fastbackward()" class="new-element cursor"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
          </li>
          <li  ng-class="page == 1 ? 'disabled'  : ''">
            <a   ng-click="backward()" class="new-element cursor"><i class="glyphicon glyphicon-step-backward"></i></a>
          </li>
          <li>
            <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{customfield.total }}"}}
              </b> registros </span><span>Página <b>{{"{{ page }}"}}
              </b> de <b>
                {{ "{{ (customfield.total_pages ) }}"}}
              </b></span>
          </li>
          <li   ng-class="page == (customfield.total_pages) ? 'disabled'  : ''">
            <a  ng-click="forward()" class="new-element cursor"><i class="glyphicon glyphicon-step-forward"></i></a>
          </li>
          <li   ng-class="page == (customfield.total_pages) ? 'disabled'  : ''">
            <a ng-click="fastforward()" class="new-element cursor"><i class="glyphicon glyphicon-fast-forward"></i></a>
          </li>
        </ul>
      </div>
      <table class="table table-bordered">
        <thead class="theader">
          <tr>
            <th>Información</th>
            <th>Detalles</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr ng-repeat="contactlist in customfield[1].items">
            <td>
              <div class="medium-text" >
                {{ '{{contactlist.name}}' }}
              </div>
              <div class="small-text">
                <em>
                  {{ '{{contactlist.description}}' }}
                </em>
              </div>
              <div class="extra-small-text">
                Creada el <strong> {{ "{{contactlist.created}}" }}</strong>, <br>
                Actualizada el <strong> {{ "{{contactlist.updated}}" }}</strong> <br>
              </div>
            </td>
            <td>
        <di>
          <dd>
            Tipo :     {{ '{{traslateCustomfield(contactlist.type)}}' }}
          </dd>
          <dd>
            {{'{{contactlist.defaultvalue}}'}}
          </dd>
        </di>
        </td>

        <td>
          <div class="pull-right">
            {#          <a href="#/customfield/{{ '{{contactlist.idContactlist}}' }}" class="button shining btn btn-xs-round shining-round round-button info-inverted" data-toggle="tooltip" data-placement="left" title="Agregar campo personalizado">
                        <span class="fa fa-plus-circle"</span>
                      </a>#}
            <a href="#/editcustomfield/{{ '{{contactlist.idCustomfield}}' }}" class="button shining btn btn-xs-round shining-round round-button primary-inverted" data-toggle="tooltip" data-placement="left" title="Editar">
              <span class="glyphicon glyphicon-pencil"</span>
            </a>
            {#   <a href="#/deletecustomfield/{{ '{{contactlist.idCustomfield}}' }}" class="button shining btn btn-xs-round shining-round round-button danger-inverted" data-toggle="tooltip" data-placement="left" title="Eliminar" >
                 <span class="glyphicon glyphicon-trash"</span>
               </a>#}
            <a href="" ng-click="confirmDeleteCustomfield(contactlist.idCustomfield)" class="button shining btn btn-xs-round shining-round round-button danger-inverted" data-toggle="tooltip" data-placement="left" title="Eliminar" >
              <span class="glyphicon glyphicon-trash"</span>
            </a>
          </div>
        </td>
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
                Si elimina este campo personalizado no se podrán recuperar los datos
              </div>
              <br>
              <div>
                <a onClick="closeModal();" class="button shining btn btn-md danger-inverted" data-dialog-close>Cancelar</a>
                <a href="#" ng-click="deleteCustomfield(contactlist.idCustomfield)" id="btn-ok" class="button shining btn btn-md success-inverted">Aceptar</a>
              </div>
            </div>
          </div>
        </div>
        </tr>
        </tbody>
      </table>

      <div id="pagination" class="text-center">
        <ul class="pagination">
          <li ng-class="page == 1 ? 'disabled'  : ''">
            <a   ng-click="fastbackward()" class="new-element cursor"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
          </li>
          <li  ng-class="page == 1 ? 'disabled'  : ''">
            <a   ng-click="backward()" class="new-element cursor"><i class="glyphicon glyphicon-step-backward"></i></a>
          </li>
          <li>
            <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{customfield.total }}"}}
              </b> registros </span><span>Página <b>{{"{{ page }}"}}
              </b> de <b>
                {{ "{{ (customfield.total_pages ) }}"}}
              </b></span>
          </li>
          <li   ng-class="page == (customfield.total_pages) ? 'disabled'  : ''">
            <a  ng-click="forward()" class="new-element cursor"><i class="glyphicon glyphicon-step-forward"></i></a>
          </li>
          <li   ng-class="page == (customfield.total_pages) ? 'disabled'  : ''">
            <a ng-click="fastforward()" class="new-element cursor"><i class="glyphicon glyphicon-fast-forward"></i></a>
          </li>
        </ul>
      </div>
    </div>

    <div ng-show="customfield[1].items.length == 0">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
        <div class="block block-success">
          <div class="body success-no-hover text-center">
            <h2>
              No hay registros de campos personalizados, para crear uno haga <a href="#/addcustomfield/{{"{{idContactlist}}"}}">clic aquí</a>.
            </h2>
            </h2>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>
<div id="dialogcustom" class="dialog">
  <div class="dialog__overlay"></div>
  <div class="dialog__content">
    <div class="morph-shape">
      <svg xmlns="https://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 560 280" preserveAspectRatio="none">
      <rect x="3" y="3" fill="none" width="556" height="276"/>
      </svg>
    </div>
    <div class="dialog-inner">
      <h2>No es posible crear más campos personalizados, solo se permiten 10 campos por cada lista.</h2>
      <br>
      <div>        
        <a onClick="closeModal();"  id="btn-ok" class="button shining btn btn-md success-inverted">Confirmar</a>
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