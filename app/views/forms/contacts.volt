<div class="row">
            <div class="clearfix"></div>
            <div class="space"></div>
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="title">
{#      Contactos inscritos mediante el formulario: <b>{{ form.name }}</b>#}
      Contactos inscritos mediante el formulario: <b>{{'{{form.name}}'}}</b>
    </div>
    <hr class="basic-line">
    <p>
      En esta lista podrá visualizar los contactos que se inscribieron mediante el formulario.
    </p>
  </div>
</div>

<div class="row wrap">
  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 ">
      <div class="row">
        <div class="col-xs-2 col-sm-2 col-lg-5  text-left">
          <div class="input-group">
            <span  class=" input-group-addon cursor" id="basic-addon1" placement="top" class="" data-toggle="popover" title="Instrucciones"
                   data-content="Para filtrar por varios campos que pueden ser el correo, el número de móvil o cualquiera de los campos personalizados se deben de separar por comas sin importar el orden">
              <i class="fa fa-question-circle" aria-hidden="true" ></i>
            </span>
            <input class="form-control"  id="name" placeholder="Buscar por correo, telefono o campos personalizados" ng-keyup="searchcontacts()" ng-model="search" ng-disabled="contacts[0].items.length == 0"/>
          </div>
        </div>
        <div class="col-xs-10 col-sm-10 col-lg-7 text-right pull-right " >
{#                                      <a href="{{ url("forms#/")}}">#}
          <a ui-sref="list()">
            <button class="button  btn btn-md default-inverted">
              <i class="fa fa-arrow-left"></i>
              Regresar
            </button>
          </a>
        </div>
      </div>
    </div>
  </div>
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
    <table class="table table-bordered sticky-enabled" id="resultTable">
      <thead class="theader">
        <tr style="border-left: solid 4px #474646;">
          {#<th>
              <md-checkbox aria-label="Switch 2" ng-click="toggleAll()"  ng-checked="isChecked()" md-indeterminate="isIndeterminate()" class="md-warn "> </md-checkbox>
          </th>#}
          <th>Correo</th>
          <th style="width: 20%;">Nombre(s) y apellido(s)</th>
          <th>Teléfono </th>
          <th>Fecha de nacimiento</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody ng-repeat="contactlist in contacts[0].items">
        <tr ng-class="{'border-left-unsubscribed': contactlist.status == 'unsubscribed','border-left-active': contactlist.status == 'active','border-left-spam': contactlist.status == 'spam','border-left-bounced': contactlist.status == 'bounced','border-left-blocked': contactlist.status == 'blocked'}" undeline">

            {#<td>
                <md-checkbox aria-label="Switch 2" ng-checked="exists(contactlist, selected)" ng-click="toggle(contactlist, selected)" class="md-warn md-align-top-left">
                </md-checkbox>
            </td>#}
            <td  class="cursor" data-toggle="collapse" data-target="#allinfo{{ '{{contactlist.idContact}}' }}" aria-expanded="false"
             aria-controls="allinfo{{ "{{ contactlist.idContact }}"}}" >     <a href="#/"><u>    {{ '{{contactlist.email}}' }} </u></a>
      <spam ng-show="contactlist.blockedEmail>0" class="btn-xs btn-blocked">Bloqueado</spam>
      </td>
      <td data-toggle="collapse" data-target="#allinfo{{ '{{contactlist.idContact}}' }}" aria-expanded="false"
          aria-controls="allinfo{{ "{{ contactlist.idContact }}"}}" class="cursor" >      {{ '{{contactlist.name}}' }} {{ '{{contactlist.lastname}}' }}</td>
      <td  class="cursor" data-toggle="collapse" data-target="#allinfo{{ '{{contactlist.idContact}}' }}" aria-expanded="false"
           aria-controls="allinfo{{ "{{ contactlist.idContact }}"}}" > {{  "{{ ((contactlist.indicative) ? '(+' +  contactlist.indicative + ')' : '' ) }}"   }} {{ '{{contactlist.phone}}' }}
      <spam ng-show="contactlist.blockedPhone>0" class="btn-xs btn-blocked">Bloqueado</spam>
      </td>
      <td  class="cursor" data-toggle="collapse" data-target="#allinfo{{ '{{contactlist.idContact}}' }}" aria-expanded="false"
           aria-controls="allinfo{{ "{{ contactlist.idContact }}"}}" >      {{ '{{contactlist.birthdate}}' }}</td>
      <td class="text-right " ng-init="changestatus[$index] = ((contactlist.unsubscribed == 0)? true:false)">
        <span class=" float-right" style="margin-top: 9px;">
          <a href="" class="button shining btn btn-xs-round  round-button danger-inverted" data-toggle="tooltip" data-placement="left" title="Eliminar contacto" data-ng-click="confirmDelete(contactlist.idContact,idContactlist)">
            <span class="glyphicon glyphicon-trash"></span>
          </a>
          <md-tooltip class="switch-style" md-direction="top">
            Eliminar contacto
          </md-tooltip>
        </span>
        <span class="float-right">
          <span hide-sm >
            <md-switch aria-label="Switch 2" class="text-left  md-primary" ng-model="changestatus[$index]" data-ng-click="changestatus(contactlist.idContact)" >
            </md-switch>
          </span>
          <md-tooltip class="switch-style" md-direction="top">
            Des-suscribir y suscribir
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

                      <tr ng-repeat="(key, value) in contactlist"  ng-hide="key == 'contactlist' || key == 'createdBy' || key == 'updatedBy' || key == 'updated' || key == 'created' || key == 'idContact' || key == 'unsubscribed' || key == 'blockedPhone' || key == 'blockedEmail'  || key == 'deleted'  || key == 'status' || key == 'idAccount' || key == 'ipAddress' || key == 'browser' || key == 'blocked'">
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
                                 e-ng-options="i.phonecode as '(+'+i.phonecode +') '+ i.name for i in  indicative " >{{"{{ value || 'Campo vacio'  }}"}}</a>
                            </div>
                            <div ng-if=" key == 'birthdate'">
                              <a href="#" editable-date="value" onbeforesave="updateUser($data, key, contactlist.idContact)">{{"{{ (value | date: 'yyyy-MM-dd')  ||  'Campo vacio'  }}"}}</a>
                            </div>
                          </div>
                          <div ng-if="$index > 8">
                            {#                                                      <div ng-if="$index > 6 && value.type">#}
                            <div ng-if="value.type" >
                              <div ng-if="value.type == 'Numerical' ">
                                <a href="#" editable-number="value.value" onbeforesave="updateUser($data, key, contactlist.idContact)">{{"{{ value.value || 'Campo vacio' }}"}}</a>
                              </div>
                              <div ng-if="value.type == 'Text' ">
                                <a href="#" editable-text="value.value" onbeforesave="updateUser($data, key, contactlist.idContact)">{{"{{ value.value || 'Campo vacio' }}"}}</a>
                              </div>
                              <div ng-if="value.type == 'Date' ">
                                <a onbeforesave="updateUser($data, key, contactlist.idContact)" href="#" editable-date="value.value">{{"{{ (value.value | date: 'yyyy-MM-dd')  || '1969-12-31' }}"}}</a>
                              </div>
                              <div ng-if="value.type == 'TextArea' ">
                                <a href="#" editable-textarea="value.value" e-rows="3" e-cols="40" onbeforesave="updateUser($data, key, contactlist.idContact)">{{"{{ value.value || 'Campo vacio' }}"}}</a>
                              </div>
                              <div ng-if="value.type == 'Select' ">
                                <a href="#" class="" editable-select="value.value" onbeforesave="updateUser($data, key, contactlist.idContact)"
                                   e-ng-options="g for g in arr[value.idCustomfield].split(',')" >{{"{{ value.value || 'Campo vacio' }}"}}</a>
                              </div>
                              <div ng-if="value.type == 'Multiselect' ">
                                <a href="#" editable-checklist="value.value"  onbeforesave="updateUser($data, key, contactlist.idContact)"
                                   e-ng-options="g for g in arr[value.idCustomfield].split(',')" >{{"{{ showStatus(arr[value.idCustomfield].split(','), value.value ) }}"}}</a>
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
                    Creada por <strong>{{"{{ contactlist.createdBy }}"}}</strong> el dia <strong>{{"{{ contactlist.created * 1000  | date : 'yyyy-MM-dd' }}"}}</strong>
                    Actualizada por <strong>{{"{{ contactlist.updatedBy }}"}}</strong> el dia <strong>{{"{{ contactlist.updated * 1000 | date : 'yyyy-MM-dd' }}"}}</strong>
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
            Aún no se han registrado contactos mediante el formulario
          </h2>
          <div ng-hide="contacts[0].items.length > 1" class="row">
          </div>
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
        <h2>¿Estas seguro?</h2>
        <div>
          Tenga en cuenta que si elimina el contacto no podrá volver a usarlo ni recuperarlo.
        </div>
        <br>
        <div>
          <a onClick="closeModal();" class="button shining btn btn-md danger-inverted" data-dialog-close>Cancelar</a>
          <a href="#/" data-ng-click="deleteContact()" id="btn-ok" class="button shining btn btn-md success-inverted">Confirmar</a>
        </div>
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
{#{% block footer %}

    <script type="text/javascript">
        var relativeUrlBase = "{{urlManager.get_base_uri()}}";
        var fullUrlBase = "{{urlManager.get_base_uri(true)}}";
        var templateBase = "forms";
        var idContactlist = "{{ form.idContactlist }}";
        var idForm = "{{ form.idForm }}";

        $(document).ready(function () {
            $('[data-toggle="popover"]').popover();
        });
    </script>

{% endblock %}#}

