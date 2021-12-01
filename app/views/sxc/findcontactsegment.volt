
<div class="row">
  <div class="col-xs-4 col-sm-4 col-lg-4 text-left">
    <div class="input-group">
      <span class=" input-group-addon" id="basic-addon1" data-toggle="tooltip" data-placement="top" title="Para buscar con varios parametros se deben de separar por comas">
        <i class="fa fa-search-plus" aria-hidden="true" ></i>
      </span>
      <input class="form-control"  id="name" placeholder="Buscar por correo, telefono o campos personalizados" ng-keyup="searchcontacts()" ng-model="search" />
    </div>
  </div> 
  <div class="col-xs-8 col-sm-8 col-lg-8 text-right pull-right ">
    <a href="{{ url("segment/index")}}">
      <button class="button  btn btn-md default-inverted">
        Regresar a la lista de segmentos
      </button>
    </a>
  </div> 
</div>
<div ng-class="{'hidden' : progressbar}" >
  <md-progress-linear md-mode="indeterminate" class="md-warn" ></md-progress-linear> 
</div>
<div ng-cloak>
  {#  {% if "{{contact.total}}" > 0 %}#}
  <div ng-hide="showList" >
    <div id="pagination" class="text-center">
      <ul class="pagination">
        <li ng-class="page == 1 ? 'disabled'  : ''">
          <a  href="#/" ng-click="page == 1 ? true  : false || fastbackward()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
        </li>
        <li  ng-class="page == 1 ? 'disabled'  : ''">
          <a href="#/"  ng-click="page == 1 ? true  : false || backward()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
        </li>
        <li>
          <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{contact.total }}"}}
            </b> registros </span><span>Página <b>{{"{{ page }}"}}
            </b> de <b>
              {{ "{{ (contact.total_pages ) }}"}}
            </b></span>
        </li>
        <li   ng-class="page == (contact.total_pages) || contact.total_pages == 0 ? 'disabled'  : ''">
          <a href="#/" ng-click="page == (contacts.total_pages)  || contact.total_pages == 0  ? true  : false || page == (contact.total_pages)  || contact.total_pages == 0  ? true  : false || forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
        </li>
        <li   ng-class="page == (contact.total_pages)  || contact.total_pages == 0 ? 'disabled'  : ''">
          <a ng-click="page == (contact.total_pages)  || contact .total_pages == 0  ? true  : false || fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
        </li>
      </ul>
    </div>
    <table class="table table-bordered table-responsive" id="resultTable">
      <thead class="theader">
        <tr style="border-left: solid 4px #474646;">
          <th>Correo</th>
          <th>Nombre(s) y apellido(s)</th>
          <th>Teléfono</th>
          <th>Fecha de nacimiento</th>
        </tr>
      </thead>
      <tbody ng-repeat="contactlist in contact[0].items">
        <tr>
          <td  class="cursor" data-toggle="collapse" data-target="#allinfo{{ '{{contactlist.idContact}}' }}" aria-expanded="false" 
               aria-controls="allinfo{{ "{{ contactlist.idContact }}"}}" >     <a href="#/"> <u>   {{ '{{contactlist.email}}' }} </u></a>   </td>
          <td data-toggle="collapse" data-target="#allinfo{{ '{{contactlist.idContact}}' }}" aria-expanded="false" 
              aria-controls="allinfo{{ "{{ contactlist.idContact }}"}}" class="cursor" >      {{ '{{contactlist.name}}' }} {{ '{{contactlist.lastname}}' }}</td>
          <td  class="cursor" data-toggle="collapse" data-target="#allinfo{{ '{{contactlist.idContact}}' }}" aria-expanded="false" 
               aria-controls="allinfo{{ "{{ contactlist.idContact }}"}}" > {{  "{{ ((contactlist.indicative) ? '(+' +  contactlist.indicative + ')' : '' ) }}"   }} {{ '{{contactlist.phone}}' }}</td>
          <td  class="cursor" data-toggle="collapse" data-target="#allinfo{{ '{{contactlist.idContact}}' }}" aria-expanded="false" 
               aria-controls="allinfo{{ "{{ contactlist.idContact }}"}}" >      {{ '{{contactlist.birthdate}}' }}</td>
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
                            <a href="#/" ng-click="changestatus(contactlist.idContact, key.idContactlist )" class="margin-left-10   {{"{{ ((key.unsubscribed == 0) ? ' text-desuscribed' : 'text-suscribe' ) }}"}} " 
                               data-toggle="tooltip" data-placement="left"  >
                              <u>
                                {{"{{ ((key.unsubscribed == 0) ? 'Des-suscribir' : ' Suscribir' ) }}"}}
                              </u>
                            </a>
                          </li>
                        </ul>
                      </div>
                    </div>
                    <div class="col-lg-7 col-md-7 col-sm-7 text-center">
                      <strong>  Información completa</strong>
                      <table class="table-condensed table" style="border: 2px">

                        <tr ng-repeat="(key, value) in contactlist"  ng-hide="key == 'contactlist' || key == 'createdBy' || key == 'updatedBy' || key == 'updated' || key == 'created' || key == 'idContact' || key == 'blocked' || key == 'unsubscribed' || key == 'blockedPhone' || key == 'blockedEmail'  || key == 'deleted'  || key == 'status' || key == 'idSubaccount' || key == 'idAccount' || key == 'ipAddress' || key == 'browser'  ">
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
    <div ng-class="{'hidden' : loading}" >
      <md-progress-linear md-mode="indeterminate" class="md-warn" ></md-progress-linear> 
    </div>
    <div id="pagination" class="text-center">
      <ul class="pagination">
        <li ng-class="page == 1 ? 'disabled'  : ''">
          <a  href="#/" ng-click="page == 1 ? true  : false || fastbackward()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
        </li>
        <li  ng-class="page == 1  ? 'disabled'  : ''">
          <a href="#/"  ng-click="page == 1 ? true  : false || backward()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
        </li>
        <li>
          <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{contact.total }}"}}
            </b> registros </span><span>Página <b>{{"{{ page }}"}}
            </b> de <b>
              {{ "{{ (contact.total_pages ) }}"}}
            </b></span>
        </li>
        <li   ng-class="page == (contact.total_pages) || contact.total_pages == 0 ? 'disabled'  : ''">
          <a href="#/" ng-click="page == (contacts.total_pages)  || contact.total_pages == 0  ? true  : false || page == (contact.total_pages)  || contact.total_pages == 0  ? true  : false || forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
        </li>
        <li   ng-class="page == (contact.total_pages)  || contact.total_pages == 0 ? 'disabled'  : ''">
          <a ng-click="page == (contact.total_pages)  || contact .total_pages == 0  ? true  : false || fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
        </li>
      </ul>
    </div>

  </div>
  {#  {% else %}#}
  <br>
  <div ng-hide="show"  class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 ">
      <div class="block block-success">
        <div class="body success-no-hover text-center">
          <h2>
            <p>No hay contactos que coincidan con el criterio de búsqueda del segmento</p>
          </h2>    
        </div>
      </div>
    </div>
  </div>
</div>