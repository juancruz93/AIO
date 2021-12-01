<style>
  .widthFull{
    width:100%;
  }
</style>
<script type="text/javascript">
  $(document).ready(function () {
    $('[data-toggle="popover"]').popover();
  });
  $(function () {
    $('#details').tooltip();
  });

  $(document).on("click", "#delete", function () {
    var myURL = $(this).data('id');
    $("#btn-ok").attr('href', myURL);
  });

  $(document).on("click", "#editsms", function () {
    var myURL = $(this).data('id');
    $("#btn-ok-edit").attr('href', myURL);
  });
</script>
<div ng-cloak>
  <div class="row wrap">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
      <div class="title col-md-6 float-left text-left" style="padding-left: 0px">
        Listas de contactos
      </div>
    <div class="col-md-6 col-sm-6 col-xs-6 col-lg-6 float-right text-center" style="padding-right: 0px">
        <div class="col-md-2 col-sm-2 col-xs-2 col-lg-2 float-right" style="padding-right: 0px; padding-left: 0px">
            <div class="small-text danger" >
                {{'{{data.totals.bloqueados}}'}}
            </div>
            <div class="extra-small-text danger">
                Bloqueados
                                
            </div>
        </div>  
        <div class="col-md-2 col-sm-2 col-xs-2 col-lg-2 float-right" style="padding-right: 0px; padding-left: 0px">
            <div class="small-text warning" >
                {{'{{data.totals.rebotados}}'}}
            </div>
            <div class="extra-small-text warning">
                Rebotados
            </div>
        </div>  
        <div class="col-md-2 col-sm-2 col-xs-2 col-lg-2 float-right" style="padding-right: 0px; padding-left: 0px">
            <div class="small-text danger" >
                {{'{{data.totals.spam}}'}}
            </div>
            <div class=" extra-small-text danger">
               SPAM 
            </div>
        </div>  
        <div class="col-md-2 col-sm-2 col-xs-2 col-lg-2 float-right" style="padding-right: 0px; padding-left: 0px">
            <div class="small-text default" >
                {{'{{data.totals.desuscritos}}'}}
            </div>
            <div class="extra-small-text default" style="padding-right: 0px; padding-left: 0px">
                Desuscritos
            </div>
        </div> 
        <div class="col-md-2 col-sm-2 col-xs-2 col-lg-2 float-right" style="padding-right: 5px; padding-left: 5px">
            <div class="small-text success" >
                {{'{{data.totals.activos}}'}}
            </div>
            <div class="extra-small-text success">
               Activos
            </div>
        </div> 
        <div class="col-md-2 col-sm-2 col-xs-2 col-lg-2 float-right" style="padding-right: 5px; padding-left: 0px">
            <div class="small-text info" >
                {{'{{data.totals.totales}}'}}
            </div>
            <div class="extra-small-text info">
                Totales
            </div>
        </div>          
    </div>

    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="padding-left: 0px; padding-right: 0px">
        <hr class="basic-line" />
      <p>
        Administre las listas de contactos, donde podrá agregar contactos que luego podrá usar para los envíos de correo y de SMS
      </p>
    </div>      
    </div>
  </div>

  <div class="row wrap">
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
      <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
          <div class="input-group">
            <span class=" input-group-addon cursor" id="basic-addon1" placement="top" data-toggle="popover" title="" data-content="Puede buscar las listas de contacto por el nombre o alguno que se asemeja al nombre que desee buscar." data-original-title="Instrucciones">
              <i class="fa fa-question-circle" aria-hidden="true"></i>
            </span>
            <input class="form-control input-sm" id="name" placeholder="Buscar por nombre de lista de contacto" ng-keyup="functions.filter.name()" ng-model="data.filter.name" aria-invalid="false"/>
          </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
          <div class="input-group" ng-class="{'has-error':misc.filter.emailInvalid && data.filter.email.length > 0}">
            <span class=" input-group-addon cursor" id="basic-addon1" placement="top" data-toggle="popover" title="" data-content="Solo se puede validar correos validados por ejemplo: ejemplo@example.com " data-original-title="Instrucciones">
              <i class="fa fa-question-circle" aria-hidden="true"></i>
            </span>
            <input class="form-control input-sm" id="name" placeholder="Buscar por correo de contacto" ng-change="functions.filter.email()" ng-model="data.filter.email" aria-invalid="false"/>
          </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
          <div class="col-xs-12 col-sm-12 col-md-10 col-lg-10">
            <ui-select ng-model="data.filter.idContactlistCategory" theme="select2"  title="Seleccione una categoría" data-ng-change="functions.filter.name()" class="widthFull">
              <ui-select-match placeholder="Seleccione una categoría">{{"{{$select.selected.name}}"}}</ui-select-match>
              <ui-select-choices repeat="key.idContactlistCategory as key in data.listCategories | propsFilter: {name: $select.search}">
                <div ng-bind-html="key.name | highlight: $select.search"></div>
              </ui-select-choices>
            </ui-select>
          </div>
          <div class="col-xs-12 col-sm-12 col-md-1 col-lg-1 text-right refreshBoxFuera">
            <div class="dropdown form-group dropdown-end-parent refreshBoxDentro">
                <button type="button" title="Borrar Filtros" data-ng-click="functions.filter.refresh()" class="btn btn-danger glyphicon glyphicon-erase warning-inverted" ></button>
            </div>
          </div>


        </div>

      </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 text-right">
      <a href="#/add" class="button shining btn btn-md success-inverted">
        Agregar una lista de contactos
      </a>
      <a href="{{url("segment/index")}}" class="button shining btn btn-md primary-inverted">
        Segmentos
      </a>
      <a href="{{url("blockade/index")}}" class="button shining btn btn-md warning-inverted">
        Lista de bloqueos
      </a>
    </div>
  </div>

  <div class="row wrap" ng-class="{'hidden' : progressbar}" >
    <md-progress-linear md-mode="indeterminate" class="md-warn" ></md-progress-linear>
  </div>
  {#  <md-progress-linear md-mode="indeterminate" class="md-warn" ></md-progress-linear>#}
  <div ng-hide="data.contactlists[0].items.length == 0 || !progressbar">
    <div id="pagination" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center">
      <ul class="pagination">
        <li ng-class="data.page == 1 ? 'disabled'  : ''">
          <a  href="#/" ng-click="data.page == 1 ? true  : false || functions.fastbackward()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
        </li>
        <li  ng-class="data.page == 1 ? 'disabled'  : ''">
          <a href="#/"  ng-click="data.page == 1 ? true  : false || functions.backward()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
        </li>
        <li>
          <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{data.contactlists.total }}"}}
            </b> registros </span><span>Página <b>{{"{{ data.page }}"}}
            </b> de <b>
              {{ "{{ (data.contactlists.total_pages ) }}"}}
            </b></span>
        </li>
        <li   ng-class="data.page == (data.contactlists.total_pages)  || data.contactlists.total_pages == 0  ? 'disabled'  : ''">
          <a href="#/" ng-click="data.page == (data.contactlists.total_pages)  || data.contactlists.total_pages == 0  ? true  : false || functions.forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
        </li>
        <li   ng-class="data.page == (data.contactlists.total_pages)  || data.contactlists.total_pages == 0  ? 'disabled'  : ''">
          <a ng-click="data.page == (data.contactlists.total_pages)  || data.contactlists.total_pages == 0  ? true  : false || functions.fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
        </li>
      </ul>
    </div>
  </div>

  <div class="row wrap" ng-show="data.contactlists[0].items.length > 0">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
      {# <div ng-if="selected.length>0" >
         Ha selecccionado <b ng-if="selected.length!=items.length">{{"{{selected.length}}"}}</b><b ng-if="selected.length==items.length">todas</b> listas de contacto
         <br>
         
       </div>#}
      <table class="table table-bordered">
        <thead class="theader ">
          <tr>
            {#  <th>
          <md-checkbox aria-label="Select All"
                       ng-checked="isChecked()"
                       md-indeterminate="isIndeterminate()"
                       ng-click="toggleAll()">
            <span ng-if="isChecked()"></span>
          </md-checkbox>
          </th>#}
            <th style="width: 33%;">Información</th>
            <th>Detalles</th>
            <th style="width: 24%;">Acciones</th>
          </tr>
        </thead>
        <tbody id="tbody">
          <tr ng-repeat="contactlist in data.contactlists[0].items track by $index">
            {# <td>
         <md-checkbox ng-checked="exists(contactlist.idContactlist, selected)" ng-click="toggle(contactlist.idContactlist, selected)">
         </md-checkbox>
         </td>#}
            <td>
              <div class="medium-text" >
                <a href="{{ url('contact/index/' ~ "{{contactlist.idContactlist}}") }}">
                  {{ '{{contactlist.name}}' }}
                </a>
              </div>
              <div class="small-text">
                <em>
                  Categoria: {{ '{{contactlist.category}}' }}
                </em>
              </div>
              <div class="small-text">
                <em>
                  {{ '{{contactlist.description}}' }}
                </em>
              </div>
              <div class="extra-small-text">
                Creada el {{ '{{contactlist.createdDate}}' }} a las {{ '{{contactlist.createdHour}}' }} <br>
                Actualizada el {{ '{{contactlist.updatedDate}}' }} a las {{ '{{contactlist.updatedHour}}' }}
              </div>
            </td>
            <td>
              <div class="row wrap text-center" id="contentList">
                <div class="inline-block text-center info">
                  <div class="medium-text">{{ '{{contactlist.ctotal}}' }}</div>
                  Totales
                </div>

                <div class="inline-block text-center success">
                  <div class="medium-text">{{ '{{contactlist.cactive}}' }}</div>
                  Activos
                </div>

                <div class="inline-block text-center default">
                  <div class="medium-text">{{ '{{contactlist.cunsubscribed}}' }}</div>
                  Desuscritos
                </div>

                <div class="inline-block text-center danger">
                  <div class="medium-text">{{ '{{contactlist.cspam}}' }}</div>
                  SPAM
                </div>

                <div class="inline-block text-center warning">
                  <div class="medium-text">{{ '{{contactlist.cbounced}}' }}</div>
                  Rebotados
                </div>

                <div class="inline-block text-center danger">
                  <div class="medium-text">{{"{{ contactlist.cblocked}}"}}</div>
                  Bloqueados
                </div>

              </div>
            </td>

            <td>
              <div class="pull-right">

                <a href="{{ url('process/import/') }}{{ '{{contactlist.idContactlist}}' }}" class="button shining btn btn-xs-round shining-round round-button default-inverted" >
                  <span class="fa fa-list-alt"></span>
                  <md-tooltip md-direction="bottom">
                    Listado de importaciones
                  </md-tooltip>
                </a>
                <a href="#/customfield/{{ '{{contactlist.idContactlist}}' }}" class="button shining btn btn-xs-round shining-round round-button info-inverted" >
                  <span class="fa fa-bars"></span>
                  <md-tooltip md-direction="bottom">
                    Campos personalizados
                  </md-tooltip>
                </a>
                <a href="#/edit/{{ '{{contactlist.idContactlist}}' }}" class="button shining btn btn-xs-round shining-round round-button primary-inverted">
                  <span class="glyphicon glyphicon-pencil"></span>
                  <md-tooltip md-direction="bottom">
                    Editar lista de contactos
                  </md-tooltip>
                </a>
                <a href="" class="button shining btn btn-xs-round shining-round round-button danger-inverted" data-ng-click="functions.confirmDelete(contactlist.idContactlist)">
                  <span class="glyphicon glyphicon-trash"></span>
                  <md-tooltip md-direction="bottom">
                    Eliminar lista de contactos
                  </md-tooltip>
                </a>
                <button ng-click="validateTotalContacts(contactlist.idContactlist,typeExport)" class="button shining btn btn-xs-round shining-round round-button success-inverted" >
                  <span class="fa fa-download" aria-hidden="true"></span>
                  <md-tooltip md-direction="bottom">
                    Exportar contactos
                  </md-tooltip>
                </button>
                <a href="{{ url('contact/index/' ~ "{{contactlist.idContactlist}}") }}" class="button shining btn btn-xs-round shining-round round-button success-inverted" >
                  <span class="fa fa-eye" aria-hidden="true"></span>
                  <md-tooltip md-direction="bottom">
                    Ver contactos
                  </md-tooltip>
                </a>
                <a href="{{ url('contact/index/' ~ "{{contactlist.idContactlist}}" ~ '#/newcontact') }}" class="button shining btn btn-xs-round shining-round round-button warning-inverted" >
                  <span class="fa fa-user-plus" aria-hidden="true"></span>
                  <md-tooltip md-direction="bottom">
                    Agregar nuevo contacto
                  </md-tooltip>
                </a>
                {# <a ng-click="exportContacts(contactlist.idContactlist)" class="button shining btn btn-xs-round shining-round round-button export-inverted" >
                   <span class="glyphicon glyphicon-download-alt"></span>
                   <md-tooltip md-direction="bottom">
                     Exportar contactos 
                   </md-tooltip>
                 </a>#}
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>


  <div class="row wrap" ng-show="data.contactlists[0].items.length > 0">
    <div id="pagination" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center">
      <ul class="pagination">
        <li ng-class="data.page == 1 ? 'disabled'  : ''">
          <a  href="#/" ng-click="data.page == 1 ? true  : false || functions.fastbackward()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
        </li>
        <li  ng-class="data.page == 1 ? 'disabled'  : ''">
          <a href="#/"  ng-click="data.page == 1 ? true  : false || functions.backward()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
        </li>
        <li>
          <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{data.contactlists.total }}"}}
            </b> registros </span><span>Página <b>{{"{{ data.page }}"}}
            </b> de <b>
              {{ "{{ (data.contactlists.total_pages ) }}"}}
            </b></span>
        </li>
        <li   ng-class="data.page == (data.contactlists.total_pages)  || data.contactlists.total_pages == 0  ? 'disabled'  : ''">
          <a href="#/" ng-click="data.page == (data.contactlists.total_pages)  || data.contactlists.total_pages == 0  ? true  : false || functions.forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
        </li>
        <li   ng-class="data.page == (data.contactlists.total_pages)  || data.contactlists.total_pages == 0  ? 'disabled'  : ''">
          <a ng-click="data.page == (data.contactlists.total_pages)  || data.contactlists.total_pages == 0  ? true  : false || functions.fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
        </li>
      </ul>
    </div>
  </div>

  <div class="row wrap no-margin" ng-show="data.contactlists[0].items.length == 0">
    <div ng-show="data.contactlists[0].items.length == 0" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 dashboard-item text-center">
      <h4>No hemos encontrado listas de contactos, para empezar a utilizar tus servicios necesitas al menos una lista de contactos, haz <a href="#/add">clic aquí</a> para crear una.</h4>
      <img class="contact-infographic" src="{{url('')}}images/general/infografia-contactos.png" alt="Crear una lista de contactos" />
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
          ¿Esta seguro de que desea eliminar la lista de contactos?
        </div>
        <br>
        <div>
          <a onClick="closeModal();" class="button shining btn btn-md danger-inverted" data-dialog-close>Cancelar</a>
          <a href="#/" data-ng-click="restServices.deleteContactlist()" id="btn-ok" class="button shining btn btn-md success-inverted">Confirmar</a>
        </div>
      </div>
    </div>
  </div>

  <div id="moreExport" class="dialog">
    <div class="dialog__overlay"></div>
    <div class="dialog__content">
      <div class="morph-shape">
        <svg xmlns="https://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 560 280" preserveAspectRatio="none">
        <rect x="3" y="3" fill="none" width="556" height="276"/>
        </svg>
      </div>
      <div class="dialog-inner">
        <h2 style="padding-bottom: 1em;">IMPORTANTE</h2>
        <div class="row">
          <div class="col-md-12 input-group" style="padding-bottom: 1em;">
            <h4>La cantidad de contactos a exportar es mayor o igual a 15.000, le enviaremos un correo cuando el archivo este listo, por favor diligencie el correo al que desea ser notificado.</h4>
            <input class="form-control" type="text" ng-model="emailExport" placeholder="Ingresa tu correo">
            <br><br>
            <h5 class="color-warning" ng-show="flagEmail">El formato del correo no es valido.</h5>
          </div>
          <div class="col-md-12">
            <a data-ng-click="closeModalMoreExport()" class="button shining btn btn-md danger-inverted" data-dialog-close>Cancelar</a>
            <a data-ng-click="validateEmail()" class="button shining btn btn-md success-inverted">Exportar</a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div id="moreExportConfirmation" class="dialog">
    <div class="dialog__overlay"></div>
    <div class="dialog__content">
      <div class="morph-shape">
        <svg xmlns="https://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 560 280" preserveAspectRatio="none">
        <rect x="3" y="3" fill="none" width="556" height="276"/>
        </svg>
      </div>
      <div class="dialog-inner">
        <h2 style="padding-bottom: 1em;">IMPORTANTE</h2>
        <div class="row">
          <div class="col-md-12 input-group" style="padding-bottom: 1em;">
            <h4>Todo queda en nuestras manos, enviaremos un correo a <b>{{"{{emailExport}}"}}.</b> con el link de descarga del archivo.</h4>
          </div>
          <div class="col-md-12">
            <a data-ng-click="closeModalMoreExportConfirmation()" class="button shining btn btn-md danger-inverted" data-dialog-close>Cerrar</a>
          </div>
        </div>
      </div>
    </div>
  </div>
  
</div>

<script>
  function openModal(id) {
    $('#' + id).addClass('dialog--open');
  }

  function closeModal(id) {
    $('#' + id).removeClass('dialog--open');
  }
</script>
</div>  

