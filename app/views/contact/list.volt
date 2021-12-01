<script type="text/javascript">
  $(document).ready(function () {
    $('[data-toggle="popover"]').popover();
  });

</script>
<div ng-cloak>
  <div class="row">
     <div class="col-xs-12  col-sm-12 col-lg-12 text-right pull-right " >

      <a href="{{ url("contactlist/show#/customfield/" ~ "{{idContactlist}}")}}">
        <button class="button  btn btn-sm info-inverted">
          <i class="fa fa-eye"></i>
          Campos personalizados
        </button>
      </a>


      <a href="#/newcontact">
        <button class="button  btn btn-sm warning-inverted">
          <i class="fa fa-user-plus"></i>
          Agregar contacto
        </button>
      </a>
      <a href="#/newbatch">
        <button class="button  btn btn-sm primary-inverted">
          <i class="fa fa-users"></i>
          Agregar contacto rápido
        </button>
      </a>
      
    <button class="button  btn btn-sm success-inverted" style="margin-right: 0px;" ng-click="validateTotalContacts(idContactlist,typeExport)">
      <i class="fa fa-download"></i>
      Exportar contactos
    </button>
      
      <a href="#/import">
        <button class="button  btn btn-sm success-inverted" style="margin-right: 0px;">
          <i class="fa fa-upload"></i>
          Importar contactos
        </button>
      </a>
      <a href="{{ url("contactlist/show")}}">
        <button class="button  btn btn-sm default-inverted">
          <i class="fa fa-arrow-left"></i>
          Regresar
        </button>
      </a>

    </div>
    
    <div class="col-xs-5 col-sm-5 col-lg-5  text-left ">
      <br>
      <div class="input-group">
        <input class="form-control"  id="name" placeholder="Buscar por correo, telefono, nombre o apellido"  ng-model="search" />
        {#        <input class="form-control"  id="name" placeholder="Buscar por correo, telefono o campos personalizados"  ng-model="search" />#}
        <div class="input-group-btn">
          {#          <span  class=" input-group-addon cursor" id="basic-addon1" placement="top" class="" data-toggle="popover" title="Instrucciones"
                           data-content="Para filtrar por varios campos que pueden ser el correo, el número de móvil o cualquiera de los campos personalizados se deben de separar por comas sin importar el orden">
                      <i class="fa fa-question-circle" aria-hidden="true" ></i>
                    </span>#}
          <button type="button" class="btn btn-default" ng-click="searchcontacts()">
            <i class="fa fa-search"></i>
          </button>

          <button type="button" class="btn btn-default" id="basic-addon1" placement="top" class="" data-toggle="popover" title="Instrucciones"
                  data-content="Para filtrar por varios campos se deben de separar por comas sin importar el orden" >
            <i class="fa fa-question-circle" aria-hidden="true" ></i>
          </button>
          <button type="button" class="btn btn-default" ng-click="clear()">
            <i class="fa fa-eraser"></i>
          </button>
        </div>
      </div>
    </div>

    <div class="col-xs-2 col-sm-2 col-lg-2 text-left">
      <br>
      {#   <ui-select ng-model="state" theme="select2"  title="Estado de contacto" data-ng-change="searchstate()" class="widthFull">
           <ui-select-match placeholder="Estado de contacto">{{"{{$select.selected.name}}"}}</ui-select-match>
           <ui-select-choices repeat="key.key as key in liststate | propsFilter: {name: $select.search}">
             <div ng-bind-html="key.name | highlight: $select.search"></div>
           </ui-select-choices>
         </ui-select>#}
      <select ng-model="state" ng-change="searchstate()" class="form-control" required>
        <option ng-repeat="sta in status" value="{{"{{sta}}"}}">{{"{{sta}}"}}</option>
      </select>   
      {#      <ui-select ng-model="data.filter.state" theme="select2"  title="Estado de Contacto" data-ng-change="functions.filter.state()" class="widthFull">
              <ui-select-match placeholder="Estado de Contacto">{{"{{$select.selected.name}}"}}</ui-select-match>
              <ui-select-choices repeat="key.state as data"></ui-select-choices>
            </ui-select>#}
    </div>

   
  </div>
    <br>
    <br>
  <div ng-class="{'hidden' : progressbar}" >
    <md-progress-linear md-mode="indeterminate" class="md-warn" ></md-progress-linear>
  </div>
  {#  <md-progress-linear md-mode="indeterminate" class="md-warn" ></md-progress-linear>#}
  <div ng-hide="contacts[0].items.length == 0 || !progressbar">
    <div id="pagination" class="text-center">
      <ul class="pagination">
        <li ng-class="page == 1 ? 'disabled'  : ''">
          <a  href="#/" ng-click="page == 1 ? true  : false || fastbackward()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
        </li>
        <li  ng-class="page == 1 ? 'disabled'  : ''">
          <a href="#/"  ng-click="page == 1 ? true  : false || backward()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
        </li>
        <li>
          <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{contacts.total }}"}}
            </b> registros </span><span>Página <b>{{"{{ page }}"}}
            </b> de <b>
              {{ "{{ (contacts.total_pages ) }}"}}
            </b></span>
        </li>
        <li   ng-class="page == (contacts.total_pages) || contacts.total_pages == 0 ? 'disabled'  : ''">
          <a href="#/" ng-click="page == (contacts.total_pages)  || contactlists.total_pages == 0  ? true  : false || page == (contactlists.total_pages)  || contactlists.total_pages == 0  ? true  : false || forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
        </li>
        <li   ng-class="page == (contacts.total_pages)  || contacts.total_pages == 0 ? 'disabled'  : ''">
          <a ng-click="page == (contacts.total_pages)  || contactlists.total_pages == 0  ? true  : false || fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
        </li>
      </ul>
    </div>
    <div ng-if="selected.length>0" style="text-align: right;">
      Ha selecccionado  <b>{{"{{selected.length}}"}}</b> contacto<span ng-if="selected.length>1">s</span>
      <br>
      <a href="" class="button shining btn btn-xs-round shining-round round-button danger-inverted" data-ng-click="confirmDeleteSelected()">
        <span class="glyphicon glyphicon-trash"></span>
        <md-tooltip md-direction="bottom">
          Eliminar contacto(s) seleccionado(s)
        </md-tooltip>
      </a>
      <a href="" class="button shining btn btn-xs-round shining-round round-button warning-inverted" data-ng-click="confirmMoveSelected()">
        <span class="glyphicon glyphicon-move"></span>
        <md-tooltip md-direction="bottom">
          Mover contacto(s) seleccionado(s)
        </md-tooltip>
      </a>
      <a href="" class="button shining btn btn-xs-round shining-round round-button copy-inverted" data-ng-click="confirmCopySelected()">
        <span class="glyphicon glyphicon-copy"></span>
        <md-tooltip md-direction="bottom">
          Copiar contacto(s) seleccionado(s)
        </md-tooltip>
      </a>
      <span class="float-right">
        <span hide-sm >
          <md-switch ng-model="suscribe" class="md-primary" data-ng-change="setSuscribe(suscribe)"></md-switch>
        </span>
        <md-tooltip md-direction="bottom">
          Des-suscribir y suscribir contacto(s) seleccionado(s)
        </md-tooltip>
      </span>
    </div>
    <table class="table table-bordered sticky-enabled" id="resultTable">
      <thead class="theader">
        <tr style="border-left: solid 4px #474646;">
          <th>
      <md-checkbox aria-label="Select All"
                   ng-checked="isChecked()"
                   md-indeterminate="isIndeterminate()"
                   ng-click="toggleAll()"
                   class="md-primary">
        <span ng-if="isChecked()"></span>
      </md-checkbox>
      </th>
      <th>Correo</th>
      <th style="width: 20%;">Nombre(s) y apellido(s)</th>
      <th>Teléfono </th>
      <th>Fecha de nacimiento</th>
      <th>Acciones</th>
      </tr>
      </thead>
      <tbody ng-repeat="contactlist in contacts[0].items">
        <tr class="undeline" ng-class="{'border-left-unsubscribed': contactlist.status == 'unsubscribed','border-left-active': contactlist.status == 'active','border-left-spam': contactlist.status == 'spam','border-left-bounced': contactlist.status == 'bounced','border-left-blocked': contactlist.status == 'blocked'}">
          <td>
      <md-checkbox class="md-primary" aria-label="Seleccionar" ng-checked="exists(contactlist.idContact, selected)" ng-click="toggle(contactlist.idContact, selected)"></md-checkbox>
      </td>
      <td  class="cursor" data-toggle="collapse" data-target="#allinfo{{ '{{contactlist.idContact}}' }}" aria-expanded="false"
           aria-controls="allinfo{{ "{{ contactlist.idContact }}"}}" >     <a href="#/"><u>    {{ '{{contactlist.email}}' }} </u></a>
      <spam ng-show="contactlist.blockedEmail>0" class="btn-xs btn-blocked">Bloqueado
        <i class="fa fa-info-circle" aria-hidden="true" ></i>
        <md-tooltip class="switch-style" md-direction="top">
          Este correo electrónico se encuentra bloqueado
        </md-tooltip>
      </spam>
      <spam ng-show="contactlist.status == 'spam'" class="btn-xs btn-danger">Spam
        <i class="fa fa-info-circle" aria-hidden="true" ></i>
        <md-tooltip class="switch-style" md-direction="top">
          Este correo electrónico se encuentra en spam
        </md-tooltip>
      </spam>
      <spam ng-show="contactlist.status == 'bounced'" class="btn-xs btn-danger">Rebotado
        <i class="fa fa-info-circle" aria-hidden="true" ></i>
        <md-tooltip class="switch-style" md-direction="top">
          Este correo electrónico se encuentra rebotado
        </md-tooltip>
      </spam>
      </td>
      <td data-toggle="collapse" data-target="#allinfo{{ '{{contactlist.idContact}}' }}" aria-expanded="false"
          aria-controls="allinfo{{ "{{ contactlist.idContact }}"}}" class="cursor" >
        {{ '{{contactlist.name}}' }} {{ '{{contactlist.lastname}}' }}
      </td>
      <td  class="cursor" data-toggle="collapse" data-target="#allinfo{{ '{{contactlist.idContact}}' }}" aria-expanded="false"
           aria-controls="allinfo{{ "{{ contactlist.idContact }}"}}" >
        {{  "{{ ((contactlist.indicative) ? '(+' +  contactlist.indicative + ')' : '' ) }}"   }} {{ '{{contactlist.phone}}' }}
      <spam ng-show="contactlist.blockedPhone>0" class="btn-xs btn-blocked">Bloqueado
        <i class="fa fa-info-circle" aria-hidden="true" ></i>
        <md-tooltip class="switch-style" md-direction="top">
          Este numeró telefónico se encuentra bloqueado
        </md-tooltip>
      </spam>
      </td>
      <td  class="cursor" data-toggle="collapse" data-target="#allinfo{{ '{{contactlist.idContact}}' }}" aria-expanded="false"
           aria-controls="allinfo{{ "{{ contactlist.idContact }}"}}" >
        {{ '{{contactlist.birthdate}}' }}
      </td>
      <td class="text-right " ng-init="changestatus[$index] = ((contactlist.unsubscribed == 0)? true:false)">
        <span class=" float-right" style="margin-top: 9px;">
          <a href="" class="button shining btn btn-xs-round  round-button danger-inverted" data-toggle="tooltip" data-placement="left" title="Eliminar contacto" data-ng-click="confirmDelete(contactlist,idContactlist)">
            <span class="glyphicon glyphicon-trash"></span>
          </a>
          <md-tooltip md-direction="bottom">
            Eliminar contacto
          </md-tooltip>
        </span>

        <span class="float-right">
          <span hide-sm >
            <md-switch aria-label="Switch 2" class="text-left  md-primary" ng-model="changestatus[$index]" data-ng-click="changestatus(contactlist,idContactlist,changestatus[$index])" >
            </md-switch>
          </span>
          <md-tooltip md-direction="bottom">
            Des-suscribir y suscribir
          </md-tooltip>
        </span>
        <span class=" float-right" style="margin-top: 9px;">
          <a href="{{"{{fullUrlBase+templateBase}}"}}/history/{{"{{idContactlist}}"}}/{{"{{ contactlist.idContact }}"}}" class="button shining btn btn-xs-round  round-button warning-inverted" data-toggle="tooltip" data-placement="left" title="Ver historial" >
            <i class="fa fa-history" aria-hidden="true"></i>
          </a>
          <md-tooltip md-direction="bottom">
            Ver historial de envíos
          </md-tooltip>
        </span>
      </td>
      </tr>
      <tr id="allinfo{{ "{{ contactlist.idContact }}"}}" class="collapse">
        <td colspan="7">
          <div class="row">
            <div class="col-lg-12">
              <div class="block block-info">
                <div class="body row">
                  <div class="col-lg-5 col-md-5 col-sm-5 text-center">
                    <strong>Lista a las que está suscrito este contacto</strong>
                    <div class="div-border">
                      <br>
                      <ul class="text-left">
                        <li ng-repeat="key in contactlist.contactlist" class="small-text">
                          <strong>{{"{{key.name }}"}}</strong>
                        </li>
                      </ul>
                    </div>
                  </div>
                  <div class="col-lg-7 col-md-7 col-sm-7 text-center">
                    <strong>  Información completa</strong>
                    <table class="table-condensed table" style="border: 2px">

                      <tr ng-repeat="(key, value) in contactlist"  ng-hide="key == 'contactlist' || key == 'createdBy' || key == 'updatedBy' || key == 'updated' || key == 'created' || key == 'idContact' || key == 'unsubscribed' || key == 'blockedPhone' || key == 'blockedEmail'  || key == 'deleted'  || key == 'status' || key == 'idAccount' || key == 'ipAddress' || key == 'browser' || key == 'blocked' ">
                        <td>
                          <strong> {{"{{ stringfieldsprimary(key) }}"}}</strong>
                        </td>
                        <td>
                          {#                          {{"{{$index}}"}}#}
                          <div ng-if="$index <= 8">
                            {#                            <div ng-if="!value.type || key == 'indicative' || key == 'birthdate'  ">#}
                            <div ng-hide="value.type || key == 'indicative' || key == 'birthdate'  ">
                              <a href="#" editable-text="value" onbeforesave="updateUser($data, key, contactlist.idContact)">{{"{{ value || 'Campo vacio' }}"}}</a>
                            </div>
                            <div ng-if=" key == 'indicative'">
                              <a href="#" class="" editable-select="value" onbeforesave="updateUser($data, key, contactlist.idContact)"
                                 e-ng-options="i.phoneCode as '(+'+i.phoneCode +') '+ i.name for i in  indicative " >{{"{{ value || 'Campo vacio'  }}"}}</a>
                            </div>
                            <div ng-if=" key == 'birthdate'">
                              <a href="#" editable-date="value" onbeforesave="updateUser($data, key, contactlist.idContact)">{{"{{ (value | date: 'yyyy-MM-dd')  ||  'Campo vacio'  }}"}}</a>
                            </div>
                          </div>
                          <div ng-if="$index > 8">
                            {#                                                      <div ng-if="$index > 6 && value.type">#}
                            <div ng-if="value.type" >
                              <div ng-if="value.type == 'Numerical' ">
                                <a href="#" editable-number="value.value" onbeforesave="updateUser($data, key, contactlist.idContact, value.idCustomfield)">{{"{{ value.value || 'Campo vacio' }}"}}</a>
                              </div>
                              <div ng-if="value.type == 'Text' ">
                                <a href="#" editable-text="value.value" onbeforesave="updateUser($data, key, contactlist.idContact, value.idCustomfield)">{{"{{ value.value || 'Campo vacio' }}"}}</a>
                              </div>
                              <div ng-if="value.type == 'Date' ">
                                <a onbeforesave="updateUser($data, key, contactlist.idContact, value.idCustomfield)" href="#" editable-date="value.value">{{"{{ (value.value | date: 'yyyy-MM-dd')  || '1969-12-31' }}"}}</a>
                              </div>
                              <div ng-if="value.type == 'TextArea' ">
                                <a href="#" editable-textarea="value.value" e-rows="3" e-cols="40" onbeforesave="updateUser($data, key, contactlist.idContact, value.idCustomfield)">{{"{{ value.value || 'Campo vacio' }}"}}</a>
                              </div>
                              <div ng-if="value.type == 'Select' ">
                                <a href="#" class="" editable-select="value.value" onbeforesave="updateUser($data, key, contactlist.idContact, value.idCustomfield)"
                                   e-ng-options="g for g in  arr[value.idCustomfield].split(',')" >{{"{{ value.value || 'Campo vacio' }}"}}</a>
                              </div>
                              <div ng-if="value.type == 'Multiselect' ">
                                <a href="#" editable-checklist="value.value"  onbeforesave="updateUser($data, key, contactlist.idContact, value.idCustomfield)"
                                   e-ng-options="g for g in  arr[value.idCustomfield].split(',')" >{{" {{ showStatus(arr[value.idCustomfield].split(','), value.value ) }}"}}</a>
                              </div>
                            </div>
                          </div>
                        </td>
                      </tr>
                    </table>
                  </div>
                </div>
                <div class=" text-center">
                  <p>
                    Creada por <strong>{{"{{ contactlist.createdBy }}"}}</strong> el dia <strong>{{"{{ contactlist.created * 1000  | date : 'yyyy-MM-dd HH:mm' }}"}}</strong>
                    Actualizada por <strong>{{"{{ contactlist.updatedBy }}"}}</strong> el dia <strong>{{"{{ contactlist.updated * 1000 | date : 'yyyy-MM-dd HH:mm' }}"}}</strong>
                  </p>
                </div>
              </div>
            </div>
          </div>
        </td>
      </tr>
      </tbody>
    </table>

    <div id="pagination" class="text-center">
      <ul class="pagination">
        <li ng-class="page == 1 ? 'disabled'  : ''">
          <a  href="#/" ng-click="page == 1 ? true  : false || fastbackward()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
        </li>
        <li  ng-class="page == 1 ? 'disabled'  : ''">
          <a href="#/"  ng-click="page == 1 ? true  : false || backward()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
        </li>
        <li>
          <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{contacts.total }}"}}
            </b> registros </span><span>Página <b>{{"{{ page }}"}}
            </b> de <b>
              {{ "{{ (contacts.total_pages ) }}"}}
            </b></span>
        </li>
        <li   ng-class="page == (contacts.total_pages) || contacts.total_pages == 0 ? 'disabled'  : ''">
          <a href="#/" ng-click="page == (contacts.total_pages)  || contactlists.total_pages == 0  ? true  : false || page == (contactlists.total_pages)  || contactlists.total_pages == 0  ? true  : false || forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
        </li>
        <li   ng-class="page == (contacts.total_pages)  || contacts.total_pages == 0 ? 'disabled'  : ''">
          <a ng-click="page == (contacts.total_pages)  || contactlists.total_pages == 0  ? true  : false || fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
        </li>
      </ul>
    </div>
  </div>
  <br>
  <div ng-show="contacts[0].items.length == 0" class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
      <div class="block block-success">
        <div class="body success-no-hover text-center">
          <h2>
            {#        <h2 ng-hide="show">#}
            La lista de contactos se encuentra vacía, para importar contactos haga <a href="#/import">clic aquí</a>.
          </h2>
          <div ng-hide="contacts[0].items.length > 1" class="row">
          </div>
        </div>
      </div>
    </div>
  </div>

  <div id="confirmDelete" class="dialog">
    <div class="dialog__overlay"></div>
    <div class="dialog__content">
      <div class="morph-shape">
        <svg xmlns="https://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 560 280" preserveAspectRatio="none">
        <rect x="3" y="3" fill="none" width="556" height="276"/>
        </svg>
      </div>
      <div class="dialog-inner">

        <div>
          <h2>¿Estas seguro?</h2>
          Tenga en cuenta que si elimina el contacto no podrá volver a usarlo ni recuperarlo.
        </div>
        <br>
        <div class="row">

          <div class="col-md-6">
            <div class="col-md-1">
              <div class="checkboxFive">
                <input  type="checkbox" id="deletedOnly" ng-model="deletedOnly" name="deletedOnly" onClick="chekout(this.name)"/>
                <label for="deletedOnly"></label>
              </div>
            </div>
            <div class="col-md-10">
              <label class="radio-inline" style="margin-right: 46%;">Esta lista</label>
            </div>
          </div>


          <div class="col-md-6">
            <div class="col-md-1">
              <div class="checkboxFive">
                <input  type="checkbox" id="deletedAll" ng-model="deletedAll" name="deletedAll" onClick="chekout(this.name)"/>
                <label for="deletedAll"></label>
              </div>
            </div>
            <div class="col-md-10">
              <label class="radio-inline" style="margin-right: 20%;">Todas las listas</label>
            </div>
          </div>
          <br>
          <br>
          <div class="col-md-12" id="alertDeleted" ng-if="deletedAll">
            
            <div class="panel panel-danger"> 
              <div class="panel-heading"> 
                <h3 class="panel-title">Esta opción eliminará el usuario en todas las listas de contacto y no podrá ser recuperado<br><br>!!Esta acción puede tardar!!</h3> 
              </div> 
           
            </div>
          </div>
          <br>
          <br>
          <br>
          <div class="col-md-12"> 
            <a onClick="closeModal('confirmDelete');" class="button shining btn btn-md danger-inverted" data-dialog-close>Cancelar</a>
            <a href="#/" data-ng-click="deleteContact(data)" id="btn-ok" class="button shining btn btn-md success-inverted">Confirmar</a>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div id="confirmDeleteSelected" class="dialog">
    <div class="dialog__overlay"></div>
    <div class="dialog__content">
      <div class="morph-shape">
        <svg xmlns="https://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 560 280" preserveAspectRatio="none">
        <rect x="3" y="3" fill="none" width="556" height="276"/>
        </svg>
      </div>
      <div class="dialog-inner">
        <h2>¿Estas seguro?</h2>
        <div>
          Tenga en cuenta que si elimina <span ng-if="selected.length==1">el</span><span ng-if="selected.length>1">los</span> contacto<span ng-if="selected.length>1">s</span> seleccionado<span ng-if="selected.length>1">s</span> no podrá volver a usarlo<span ng-if="selected.length>1">s</span> ni recuperarlo<span ng-if="selected.length>1">s</span>.
        </div>
        <br>
        <div>
          <a onClick="closeModal('confirmDeleteSelected');" class="button shining btn btn-md danger-inverted" data-dialog-close>Cancelar</a>
          <a href="#/" data-ng-click="deleteContactSelected()" id="btn-ok" class="button shining btn btn-md success-inverted">Confirmar</a>
        </div>
      </div>
    </div>
  </div>
  <div id="confirmMoveSelected" class="dialog">
    <div class="dialog__overlay"></div>
    <div class="dialog__content">
      <div class="morph-shape">
        <svg xmlns="https://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 560 280" preserveAspectRatio="none">
        <rect x="3" y="3" fill="none" width="556" height="276"/>
        </svg>
      </div>
      <div class="dialog-inner">
        <h2>Seleccionar lista</h2>
        <div>
          Seleccione la lista a la que quiere mover los contactos. Recuerde que al aceptar se moverán todos los contactos de esta lista a la seleccionada y no se podrá retroceder el proceso.
        </div>
        <br>
        <div style="max-height: 200px; overflow-y: scroll;">
          <table id="customers">
            <tbody>
              <tr>
                <th></th>
                <th>Lista de contacto</th>
              </tr>

              <tr ng-repeat="contactlist in contactliststomove" ng-show="contactliststomove.length > 0" ng-click="select(contactlist.idContactlist)">
                <td >
            <md-checkbox class="md-primary" ng-checked="exists(contactlist.idContactlist, selectedOne)"
                         ></md-checkbox>
            </td>
            <td>{{"{{contactlist.name}}"}}</td>
            </tr>
            <tr ng-show="contactliststomove.length <= 0">
              <td colspan="2" colspan="2" style="text-align: center">No tiene más listas de contacto</td>
            </tr>
            </tbody>
          </table>
        </div>

        <br>
        <div>
          <a onClick="closeModal('confirmMoveSelected');
              setSelectedEmpty();" class="button shining btn btn-md danger-inverted" data-dialog-close>Cancelar</a>
          <a href="#/" data-ng-click="validateContactSelected('move')" id="btn-ok" class="button shining btn btn-md success-inverted" ng-if="contactliststomove.length > 0">Confirmar</a>
        </div>
      </div>
    </div>
  </div>
            
  <div id="confirmDeleteSelected" class="dialog">
    <div class="dialog__overlay"></div>
    <div class="dialog__content">
      <div class="morph-shape">
        <svg xmlns="https://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 560 280" preserveAspectRatio="none">
        <rect x="3" y="3" fill="none" width="556" height="276"/>
        </svg>
      </div>
      <div class="dialog-inner">
        <h2>¿Estas seguro?</h2>
        <div>
          Tenga en cuenta que si elimina <span ng-if="selected.length==1">el</span><span ng-if="selected.length>1">los</span> contacto<span ng-if="selected.length>1">s</span> seleccionado<span ng-if="selected.length>1">s</span> no podrá volver a usarlo<span ng-if="selected.length>1">s</span> ni recuperarlo<span ng-if="selected.length>1">s</span>.
        </div>
        <br>
        <div>
          <a onClick="closeModal('confirmDeleteSelected');" class="button shining btn btn-md danger-inverted" data-dialog-close>Cancelar</a>
          <a href="#/" data-ng-click="deleteContactSelected()" id="btn-ok" class="button shining btn btn-md success-inverted">Confirmar</a>
        </div>
      </div>
    </div>
  </div>
  <div id="confirmMoveSelected" class="dialog">
    <div class="dialog__overlay"></div>
    <div class="dialog__content">
      <div class="morph-shape">
        <svg xmlns="https://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 560 280" preserveAspectRatio="none">
        <rect x="3" y="3" fill="none" width="556" height="276"/>
        </svg>
      </div>
      <div class="dialog-inner">
        <h2>Seleccionar lista</h2>
        <div>
          Seleccione la lista a la que quiere mover los contactos. Recuerde que al aceptar se moverán todos los contactos de esta lista a la seleccionada y no se podrá retroceder el proceso.
        </div>
        <br>
        <div style="max-height: 200px; overflow-y: scroll;">
          <table id="customers">
            <tbody>
              <tr>
                <th></th>
                <th>Lista de contacto</th>
              </tr>

              <tr ng-repeat="contactlist in contactliststomove" ng-show="contactliststomove.length > 0" ng-click="select(contactlist.idContactlist)">
                <td >
            <md-checkbox class="md-primary" ng-checked="exists(contactlist.idContactlist, selectedOne)"
                         ></md-checkbox>
            </td>
            <td>{{"{{contactlist.name}}"}}</td>
            </tr>
            <tr ng-show="contactliststomove.length <= 0">
              <td colspan="2" colspan="2" style="text-align: center">No tiene más listas de contacto</td>
            </tr>
            </tbody>
          </table>
        </div>

        <br>
        <div>
          <a onClick="closeModal('confirmMoveSelected');
              setSelectedEmpty();" class="button shining btn btn-md danger-inverted" data-dialog-close>Cancelar</a>
          <a href="#/" data-ng-click="validateContactSelected('move')" id="btn-ok" class="button shining btn btn-md success-inverted" ng-if="contactliststomove.length > 0">Confirmar</a>
        </div>
      </div>
    </div>
  </div>
  <div id="confirmCopySelected" class="dialog">
    <div class="dialog__overlay"></div>
    <div class="dialog__content">
      <div class="morph-shape">
        <svg xmlns="https://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 560 280" preserveAspectRatio="none">
        <rect x="3" y="3" fill="none" width="556" height="276"/>
        </svg>
      </div>
      <div class="dialog-inner">
        <h2>Seleccionar lista</h2>
        <div>
          Seleccione la lista a la que quiere copiar los contactos seleccionados.
        </div>
        <br>
        <div style="max-height: 200px; overflow-y: scroll;">
          <table id="customers">
            <tbody>
              <tr>
                <th></th>
                <th>Lista de contacto</th>
              </tr>

              <tr ng-repeat="contactlist in contactliststomove" ng-show="contactliststomove.length > 0" ng-click="select(contactlist.idContactlist)">
                <td >
            <md-checkbox class="md-primary" ng-checked="exists(contactlist.idContactlist, selectedOne)"
                         ></md-checkbox>
            </td>
            <td>{{"{{contactlist.name}}"}}</td>
            </tr>
            <tr ng-show="contactliststomove.length <= 0">
              <td colspan="2" colspan="2" style="text-align: center">No tiene más listas de contacto</td>
            </tr>
            </tbody>
          </table>
        </div>

        <br>
        <div>
          <a onClick="closeModal('confirmCopySelected')" ng-click="setSelectedEmpty()" class="button shining btn btn-md danger-inverted" data-dialog-close>Cancelar</a>
          <a href="#/" data-ng-click="validateContactSelected('copy')" id="btn-ok" class="button shining btn btn-md success-inverted" ng-if="contactliststomove.length > 0">Confirmar</a>
        </div>
      </div>
    </div>
  </div>
  <div id="validateCopySelected" class="dialog">
    <div class="dialog__overlay"></div>
    <div class="dialog__content">
      <div class="morph-shape">
        <svg xmlns="https://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 560 280" preserveAspectRatio="none">
        <rect x="3" y="3" fill="none" width="556" height="276"/>
        </svg>
      </div>
      <div class="dialog-inner">
        <h2>Existen contactos repetidos</h2>
        <div>
          Los siguientes contactos ya se encuentran en la lista de contactos de destino, por lo tanto no se <span ng-if="type=='copy'">copiarán</span><span ng-if="type=='move'">moverán</span>.

          Si está de acuerdo puede confirmar, sino puede cancelar.
        </div>
        <br>
        <div style="max-height: 200px; overflow-y: scroll;">
          <table id="customers">
            <tbody>
              <tr>
                <th>Nombre y apellido</th>
                <th>Correo</th>
                <th>Teléfono</th>
              </tr>

              <tr ng-repeat="contact in arrayError">
                <td>{{"{{contact.name}}"}} {{"{{contact.lastname}}"}}</td>
                <td>{{"{{contact.email}}"}}</td>
                <td>{{"{{contact.phone}}"}}</td>
              </tr>
            </tbody>
          </table>
        </div>

        <br>
         <div>
          <a onClick="closeModal('validateCopySelected')" ng-click="setSelectedEmpty()" class="button shining btn btn-md danger-inverted" data-dialog-close>Cancelar</a>
          <a href="#/" data-ng-click="executeSelected()" id="btn-ok" class="button shining btn btn-md success-inverted">Confirmar</a>
        </div>
      </div>
    </div>
  </div>

<div id="deletedOption" class="dialog">
    <div class="dialog__overlay"></div>
    <div class="dialog__content">
      <div class="morph-shape">
        <svg xmlns="https://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 560 280" preserveAspectRatio="none">
        <rect x="3" y="3" fill="none" width="556" height="276"/>
        </svg>
      </div>
      <div class="dialog-inner">
        <h2>¿Como desea {{'{{estatusUnsuscribe != false ? "desuscribir" : "suscribir"}}'}}  el contacto?</h2>
        <div class="row">

          <div class="col-md-6">
            <div class="col-md-1">
              <div class="checkboxFive">
                <input  type="checkbox" id="unsubscribeOnly" ng-model="unsubscribeOnly" name="unsubscribeOnly" onClick="chekout(this.name)"/>
                <label for="unsubscribeOnly"></label>
              </div>
            </div>
            <div class="col-md-10">
              <label class="radio-inline" style="margin-right: 46%;">Esta lista</label>
            </div>
          </div>
          <div class="col-md-6">
            <div class="col-md-1">
              <div class="checkboxFive">
                <input  type="checkbox" id="unsubscribeAll" ng-model="unsubscribeAll" name="unsubscribeAll" onClick="chekout(this.name)"/>
                <label for="unsubscribeAll"></label>
              </div>
            </div>
            <div class="col-md-10">
              <label class="radio-inline" style="margin-right: 20%;">Todas las listas</label>
            </div>
          </div>
          <br>
          <br>
          <br>
          <br>
          <div class="col-md-12">
            <a data-ng-click="closeModalUnsub()" class="button shining btn btn-md danger-inverted" data-dialog-close>Cancelar</a>
            <a href="#/" data-ng-click="unsubscribe()" id="btn-ok" class="button shining btn btn-md success-inverted">Confirmar</a>
          </div>
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
  

  <div id="waiting" class="dialog">
    <div class="dialog__overlay"></div>
    <div class="dialog__content" ng-class="{'hidden' : progressbar3}">
      <div layout="row" layout-sm="column" layout-align="space-around">
        <md-progress-circular class="md-warn"md-mode="indeterminate" md-diameter="150"></md-progress-circular>
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

  var chekout = function (event) {
    var unsubscribeOnly = document.getElementById("unsubscribeOnly");
    var unsubscribeAll = document.getElementById("unsubscribeAll");

    var deletedOnly = document.getElementById("deletedOnly");
    var deletedAll = document.getElementById("deletedAll");

    if (event == "unsubscribeAll") {
      if (unsubscribeOnly.checked == true) {
        unsubscribeOnly.click();
      }
    } else if (event == "unsubscribeOnly") {
      if (unsubscribeAll.checked == true) {
        unsubscribeAll.click();
      }
    } else if (event == "deletedOnly") {
      if (deletedAll.checked == true) {
        deletedAll.click();
      }
    } else if (event == "deletedAll") {
      if (deletedOnly.checked == true) {
        deletedOnly.click();
      }
    }
  }
</script>
