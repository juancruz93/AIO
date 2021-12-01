<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
      <div class="title">
        {{"{{ title }}"}}      
      </div>            
      <hr class="basic-line" />
    </div>
  </div>
  
  <div class="clearfix"></div>
  
  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
      <em class="text-2em"><strong>{{'{{data.sms.name}}'}}</strong></em><br>
      <em>enviado el <strong>{{'{{data.sms.startdate}}'}}</strong></em>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
      <table class="border-table-block-not-padding" style="width: 100%">
        <tr>
          <td class="text-right">
            <em ><strong>Categoria</strong></em>
          </td>
          <td class="text-left">
            {{'{{data.sms.category}}'}}
          </td>
          <td class="text-right">
            <em ><strong>Destinatarios</strong></em>
          </td>
          <td class="text-left">
            {{"{{data.sms.target}}"}} 
          </td>
        </tr>
      </table>
    </div>
  
  </div>
  <div class="clearfix"></div>
  <div class="space"></div>
  <div class="row">
    <div class="row" ng-show='data.detail'>
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
        <highchart  config="misc.chartConfig"  style="min-width: 100%;  margin: 0 auto" ></highchart>
        <div id="highchart"></div>
      </div>
    </div>
    <div class="clearfix"></div>
    <div class="space"></div>
    <div class="clearfix"></div>
    <div class="space"></div>
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
  
      <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
        <label class="small-text">Detalle del envio</label>
      </div>
  
      <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3 searchBoxFilter">
        <div class="input-group">
          <input class="form-control"  id="name" ng-change='functions.search()' placeholder="Buscar por numero" ng-model="data.filter" />
          <span class=" input-group-addon" id="basic-addon1" >
            <i class="fa fa-search"></i>
          </span>
        </div>
      </div>
  
      <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 text-right pull-right">
        <a href="{{ url("sms")}}" class="btn btn-md default-inverted ">
          <i class="fa fa-arrow-left"></i>
          Regresar
        </a>
        <button class="btn btn-md info-inverted " ng-click="funRestServices.reportSms()" >
          <i class="fa fa-download"></i>
          Descargar Detalle
        </button>
      </div>
    </div>
  
    <div class="clearfix"></div>
    <div class="space"></div>
    <div id="pagination" class="text-center">
         <ul class="pagination">
        <li ng-class="data.page == 1 ? 'disabled'  : ''">
          <a  ng-click="data.page == 1 ? true  : false || functions.fastbackward()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
        </li>
        <li  ng-class="data.page == 1 ? 'disabled'  : ''">
          <a  ng-click="data.page == 1 ? true  : false || functions.backward()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
        </li>
        <li>
          <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{data.list.data.length}}"}}
            </b> registros </span><span>Página <b>{{"{{ data.page }}"}}
            </b> de <b>
              {{ "{{ (data.list.detail.total_pages ) }}"}}
            </b></span>
        </li>
        <li   ng-class="data.page == (data.list.detail.total_pages)  || data.list.detail.total_pages == 0  ? 'disabled'  : ''">
          <a ng-click="data.page == (data.list.detail.total_pages)  || data.list.detail.total_pages == 0  ? true  : false || functions.forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
        </li>
          <li ng-class="data.page == (data.list.detail.total_pages)  || data.list.detail.total_pages == 0  ? 'disabled'  : ''">
          <a ng-click="data.page == (data.list.detail.total_pages)  || data.list.detail.total_pages == 0  ? true  : false || functions.fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
        </li>
      </ul>
    </div>  
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
      <table class="table table-bordered" ng-if="data.list.data != 0">
        <thead class="theader ">
          <tr>
            <th>
              Codigo del pais
            </th>
            <th>
              Movil
            </th>
            <th>
              Mensaje
            </th>
            <th>
              Estado
            </th>
            <th>
              Fecha
            </th>
            <th>
              Respuesta
            </th>
            <th>
              Grupo respuesta
            </th>
          </tr>
        </thead>
  
        
        <tbody ng-repeat="detail in data.list.data track by $index" >
          <div class="small-text">
          </div>
          <tr class="undeline ">
            
            <td class="cursor" data-toggle="collapse" data-target="#allinfo{{'{{ detail.idSmsLoteTwoway }}'}}" aria-expanded="false" aria-controls="allinfo{{ '{{ detail.idSmsLoteTwoway }}' }}">
              <div class="small-text">
                <em> +{{ '{{detail.indicative}}' }} </em>
              </div>
            </td>
  
            <td  class="cursor" data-toggle="collapse" data-target="#allinfo{{'{{ detail.idSmsLoteTwoway }}'}}" aria-expanded="false" aria-controls="allinfo{{ '{{ detail.idSmsLoteTwoway }}' }}" >
             <div class="small-text">
               <em> {{ '{{detail.phone}}' }} </em>
             </div>
            </td>
  
  
            <td data-toggle="collapse" data-target="#allinfo{{ '{{ detail.idSmsLoteTwoway }}' }}" aria-expanded="false" aria-controls="allinfo{{ '{{ detail.idSmsLoteTwoway }}' }}">
                <div class="small-text">
                  <em> {{ '{{detail.message}}' }} </em>
                </div>
            </td>
  
  
            <td data-toggle="collapse" data-target="#allinfo{{ '{{ detail.idSmsLoteTwoway }}' }}" aria-expanded="false" aria-controls="allinfo{{ '{{ detail.idSmsLoteTwoway }}' }}" >
               <div class="small-text">
                 <em> {{ '{{functions.traslateStatus(detail.status)}}' }} </em>
               </div>
            </td>
          
          
            <td class="cursor" data-toggle="collapse" data-target="#allinfo{{'{{ detail.idSmsLoteTwoway }}'}}" aria-expanded="false" aria-controls="allinfo{{ '{{ detail.idSmsLoteTwoway }}' }}">
              <div class="small-text">
                <em> {{ '{{ detail.date }}' }} </em>
              </div>
            </td>
  
            <td  class="cursor" data-toggle="collapse" data-target="#allinfo{{'{{ detail.idSmsLoteTwoway }}'}}" aria-expanded="false" aria-controls="allinfo{{ '{{ detail.idSmsLoteTwoway }}' }}" >
              <div class="small-text">
                <em> {{ '{{detail.userResponse}}' }} </em>
              </div>
            </td>
            
            <td class="cursor" data-toggle="collapse" data-target="#allinfo{{'{{ detail.idSmsLoteTwoway }}'}}" aria-expanded="false" aria-controls="allinfo{{ '{{ detail.idSmsLoteTwoway }}' }}">
              <div class="small-text">
                <em > {{ '{{detail.group}}' }} </em>
              </div>
            </td>
            
        </tr>
        
        <tr id="allinfo{{ '{{ detail.idSmsLoteTwoway }}' }}" class="collapse" ng-model="detail.idSmsLoteTwoway">
          <td colspan="7">
            <div class="row">
              <div class="col-lg-12">
                <div class="block block-info">
                  <div class="body row">
                    <div class="col-lg-5 col-md-5 col-sm-5 text-center">
  
  
                    </div>
                    <div class="col-lg-7 col-md-7 col-sm-7 text-center">
                      <table class="table-condensed table" style="border: 2px">
  
                        {#                      <tr ng-repeat="(key, value) in contactlist"  ng-hide="key == 'contactlist' || key == 'createdBy' || key == 'updatedBy' || key == 'updated' || key == 'created' || key == 'idContact' || key == 'unsubscribed' || key == 'blockedPhone' || key == 'blockedEmail'  || key == 'deleted'  || key == 'status' || key == 'idAccount' || key == 'ipAddress' || key == 'browser' || key == 'blocked' ">#}
                        
                        <thead ng-if=" detail.response.length > 0 ">
                        <th>
                          Fecha de Envio
                        </th>
                        <th>
                          Respuesta
                        </th>
                        <th>
                          Grupo
                        </th>
                        </thead>
                        <tbody ng-if=" detail.response.length > 0 ">
                          <tr tr ng-repeat="response in detail.response" >
                             <td>
                               <strong> {{ '{{ response.dateRegister }}' }} </strong>
                             </td>
                             <td>
                               <strong> {{ '{{ response.receiver }}' }} </strong>
                             </td>
                             <td>
                               <strong>  {{ '{{ response.group }}' }}  </strong>
                             </td>
                           </tr>
                        <tbody>
                        <div ng-if=" detail.response == null ">
                            <h1> 
                              Sin respuestas previas 
                            </h1>
                        </div>
  
                      </table>
                    </div>
                  </div>
                  
                </div>
              </div>
            </div>
          </td>
        </tr>
  
        </tbody>
      </table>
                             
      <div ng-if="data.list.data == 0">
          <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
              <div class="block block-success">
                <div class="body success-no-hover text-center">
                  <h2>
                    No existen envíos de SMS doble via con ese numero, si desea crear uno por lote haga <a href="{{url('sms/createlote')}}">click aquí</a>,
                    si desea crear desde un archivo CSV haga <a href="{{url('sms/createcsv')}}">click aquí</a>.
                  </h2>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>                       
  
    </div>
      <div id="pagination" class="text-center">
        <ul class="pagination">
          <li ng-class="data.page == 1 ? 'disabled'  : ''">
            <a  ng-click="data.page == 1 ? true  : false || functions.fastbackward()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
          </li>
          <li  ng-class="data.page == 1 ? 'disabled'  : ''">
            <a  ng-click="data.page == 1 ? true  : false || functions.backward()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
          </li>
          <li>
            <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{data.list.data.length}}"}}
              </b> registros </span><span>Página <b>{{"{{ data.page }}"}}
              </b> de <b>
                {{ "{{ (data.list.detail.total_pages ) }}"}}
              </b></span>
          </li>
          <li   ng-class="data.page == (data.list.detail.total_pages)  || data.list.detail.total_pages == 0  ? 'disabled'  : ''">
            <a ng-click="data.page == (data.list.detail.total_pages)  || data.list.detail.total_pages == 0  ? true  : false || functions.forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
          </li>
          <li   ng-class="data.page == (data.list.detail.total_pages)  || data.list.detail.total_pages == 0  ? 'disabled'  : ''">
            <a ng-click="data.page == (data.list.detail.total_pages)  || data.list.detail.total_pages == 0  ? true  : false || functions.fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
          </li>
        </ul>
      </div>  
  
      <div id="somedialog" class="dialog">
        <div class="dialog__overlay"></div>
        <div class="dialog__content">
          <div class="morph-shape">
            <svg xmlns="https://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 560 280" preserveAspectRatio="none">
            <rect x="3" y="3" fill="none" width="556" height="276"/>
            </svg>
          </div>
          <div class="dialog-inner" style="padding: 2em;">
            <div class="modal-header">
              <a type="button" class="close"aria-hidden="true" onClick="closeModal();">×</a>
              <h4 class="modal-title htitle">Compartir estadísticas</h4>
            </div>
            <div class="modal-body">
              <p style="text-align: left;">
                Copie este enlace y compartalo con quien quiera, y así las personas que lo abran
                en el navegador podrán ver las estadisticas del envío de SMS.
              </p>
  
              <h4 class="htitle">Compartir estadisticas del envío de SMS</h4>
              <p id="summary"><input type="text" class="col-sm-12" readonly="readonly" id="inputsummary" value="{{'{{sms.sms.shortStaticsUrl}}'}}" onclick='highlight(this);'></p>
            </div>
            <div class="modal-footer">
              <a onClick="closeModal();" class="button shining btn btn-md danger-inverted" data-dialog-close>Cerrar</a>
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
      <script type='text/javascript'>
        function highlight(field)
        {
          field.focus();
          field.select();
        }
  
      </script>
  