<div class="clearfix"></div>
<div class="space"></div>
<div>
    <div>
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <div class="title">
                    Lista de envíos de WhatsApp
                </div>
                <hr class="basic-line" />
                <p>
                    Este listado contiene el registro de envíos de WhatsApp. Tiene las de opciones de editar, cancelar y
                    ver estadísticas detalladas de cada envío realizado.
                </p>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-3 col-sm-3 col-lg-3 wrap" style="padding-right: 5px!important;">
                <div class="input-group">
                    <input class="form-control" id="name" ng-change='search()' placeholder="Buscar por nombre"
                        ng-model="data.filter.name" />
                    <span class=" input-group-addon" id="basic-addon1">
                        <i class="fa fa-search"></i>
                    </span>
                </div>
            </div>
            <div class="col-xs-3 col-sm-3 col-lg-2">
                <ui-select ng-change='searchcategory()' multiple ng-model="data.filter.category" ng-required="true"
                    ui-select-required theme="bootstrap" title="" sortable="false" close-on-select="true">
                    <ui-select-match placeholder="Categorías">{{"{{$item.name}}"}}</ui-select-match>
                    <ui-select-choices
                        repeat="key.idWppCategory as key in wppCategory | propsFilter: {name: $select.search}">
                        <div ng-bind-html="key.name | highlight: $select.search"></div>
                    </ui-select-choices>
                </ui-select>
            </div>
            <div class="col-xs-3 col-sm-3 col-lg-2">
                <select class="form-control " name="wppStatus" ng-model="data.filter.wppStatus"
                    ng-change='statusFunc()'>
                    <option value="" selected disabled>Seleccione estado</option>
                    <option value="allStatuses">Todos los estados</option>
                    <option value="sent">Enviado</option>
                    <option value="draft">Borrador</option>
                    <option value="sending">En proceso de envío</option>
                    <option value="scheduled">Programado</option>
                    <option value="canceled">Cancelado</option>
                </select>
            </div>
            <div class="col-xs-3 col-sm-3 col-lg-5 text-right form-inline" style="padding: 0 3% 0 0!important;">
                <div class="input-group" moment-picker="data.filter.dateinitial" format="YYYY-MM-DD">

                    <input class="form-control" placeholder="Seleccionar fecha inicial"
                        ng-model="data.filter.dateinitial" ng-model-options="{ updateOn: 'blur' }">
                    <span class="input-group-addon">
                        <i class="glyphicon glyphicon-calendar"></i>
                    </span>
                </div>

                <div class="input-group" moment-picker="data.filter.dateend" format="YYYY-MM-DD">

                    <input class="form-control" placeholder="Seleccionar fecha final" ng-model="data.filter.dateend"
                        ng-model-options="{ updateOn: 'blur' }">
                    <span class="input-group-addon">
                        <i class="glyphicon glyphicon-calendar"></i>
                    </span>
                </div>
                {#<span style="display: inline-block">
          Enviados entre
        </span>#}
                {#<div style="display: inline-block">
          <div  moment-picker="data.filter.dateinitial"
                format="YYYY-MM-DD"  class="">
            <input class="" ng-model="data.filter.dateinitial">
            <i class="glyphicon glyphicon-calendar"></i>
          </div>
        </div>#}

                {#<div style="display: inline-block">
          <div moment-picker="data.filter.dateend"
               format="YYYY-MM-DD"  class="">
            <input class="" ng-model="data.filter.dateend">
            <i class="glyphicon glyphicon-calendar"></i>
          </div>
        </div>#}
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap text-right">
                <a class="button btn button default-inverted" href="{{ url('marketing') }}">Regresar</a>
                <a href="{{url('wpptemplate#/')}}" class="button btn button primary-inverted">Plantillas HSM</a>
                <a href="{{url('wppcategory#/')}}" class="button btn button warning-inverted">Categorías de Whatsapp</a>
                <a ui-sref="create" class="button btn button success-inverted">Crear envío de Whatsapp</a>
            </div>
        </div>

        <div id="pagination" class="text-center">
            <ul class="pagination">
                <li ng-class="misc.page == 1 ? 'disabled'  : ''">
                    <a href="#/" ng-click="misc.page == 1 ? true  : false || functions.fastbackward()"
                        class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
                </li>
                <li ng-class="misc.page == 1 ? 'disabled'  : ''">
                    <a href="#/" ng-click="misc.page == 1 ? true  : false || functions.backward()"
                        class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
                </li>
                <li>
                    <span><b>
                            <script id="metamorph-58-start" type="text/x-placeholder"></script>
                            {{ "{{misc.whatsapp.total }}"}}
                        </b> registros </span><span>Página <b>{{"{{ misc.page }}"}}
                        </b> de <b>
                            {{ "{{ (misc.whatsapp.total_pages ) }}"}}
                        </b></span>
                </li>
                <li
                    ng-class="misc.page == (misc.whatsapp.total_pages) || misc.whatsapp.total_pages == 0 ? 'disabled'  : ''">
                    <a href="#/"
                        ng-click="misc.page == (misc.whatsapp.total_pages)  || misc.whatsapp.total_pages == 0  ? true  : false || misc.page == (misc.whatsapp.total_pages)  || misc.whatsapp.total_pages == 0  ? true  : false || functions.forward()"
                        class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
                </li>
                <li
                    ng-class="misc.page == (misc.whatsapp.total_pages)  || misc.whatsapp.total_pages == 0 ? 'disabled'  : ''">
                    <a ng-click="misc.page == (misc.whatsapp.total_pages)  || misc.whatsapp.total_pages == 0  ? true  : false || functions.fastforward()"
                        class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
                </li>
            </ul>
        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <table class="table table-bordered sticky-enabled">
                    <thead class="theader">
                        <tr>
                            <th class="border-left-tablehead-5px">Información</th>
                            <th>Detalles</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr data-ng-repeat="item in misc.whatsapp.items track by $index">

                            <td class="border-left-{{'{{item.status}}'}}-5px">
                                <strong class=" ng-binding medium-text ">
                                    {{'{{item.name}}'}}
                                </strong>
                                <dl>
                                    <dd class="small-text" type-sms="{{'{{item.type}}'}}"><b></b></dd>
                                    <dd class="small-text" status-sms="{{'{{item.status}}'}}"><b></b> </dd>
                                    <dd> <em class="extra-small-text">Creado por
                                            <strong>{{'{{item.createdBy}}'}}</strong> , a las
                                            <strong>{{'{{item.created}}'}}</strong> </em></dd>
                                    <dd> <em class="extra-small-text">Actualizado por <strong>
                                                {{'{{item.updatedBy}}'}}</strong>, a las
                                            <strong>{{'{{item.updated}}'}}</strong></em></dd>
                                </dl>
                            </td>

                            <td>
                                <div ng-if="item.notification == '1'">
                                    <dd>Notificación<b>: si</b></dd>
                                    {#<pre>{{'{{ item.email }}'}}</pre>#}
                                    <ul ng-if="item.email">
                                        <li ng-repeat="key in item.email | split:','"> {{'{{ key }}'}} </li>
                                    </ul>
                                </div>
                                <div ng-if="item.notification ==0">
                                    <dd>Notificación<b>: no</b></dd>
                                </div>
                                <dd>Fecha de envio: {{'{{item.startdate}}'}}</dd>
                                <dd>Cantidad de mensajes: {{'{{item.target}}'}}</dd>
                                <dd ng-if="item.status == 'sent'">Cantidad de mensajes enviados: {{'{{item.sent}}'}}</dd>
                            </td>
                            <td>
                                <a ng-if="item.status == 'scheduled' || item.status == 'sending'"
                                    {#ng-click="sockets.cancelSent(item.idWhatsapp)"#}
                                    class="button btn btn-xs-round danger-inverted" data-toggle="tooltip"
                                    data-placement="top" ng-click="functions.openModal(item.idWhatsapp,'canceled')"
                                    title="Cancelar envio este si">
                                    <span class="fa fa-ban"></span>
                                </a>
                                <div ng-if="item.status == 'sent'">
                                    <a href="{{url('statistic#/whatsapp')}}/{{'{{item.idWhatsapp}}'}}"
                                        class="button btn btn-xs-round primary-inverted" data-toggle="tooltip"
                                        data-placement="top" title="Ver estadísticas">
                                        <span class="fa fa-bar-chart"></span>
                                    </a>
                                </div>
                                <div ng-if="item.status == 'sending' ">
                                    <button class="button btn btn-xs-round warning-inverted" data-toggle="tooltip"
                                        class="button btn btn-xs-round danger-inverted" data-toggle="tooltip"
                                        data-placement="top" ng-click="functions.openModal(item.idWhatsapp,'paused')"
                                        data-placement="top" title="Pausar envio">
                                        <span class="glyphicon glyphicon-pause"></span>
                                    </button>
                                    {#  <button {#ng-click="sockets.pausedSent(item.idWhatsapp)"  class="button btn btn-xs-round danger-inverted" {#data-toggle="tooltip" data-placement="top" title="Cancelar envio">
                                        <span class="fa fa-ban"></span>
                                    </button>#}
                                </div>
                                <a ng-if="item.status == 'paused'" class="button btn btn-xs-round success-inverted"
                                    data-toggle="tooltip" data-placement="top"
                                    ng-click="functions.openModal(item.idWhatsapp,'sending')" title="Reanudar envio">
                                    <span class="glyphicon glyphicon-play"></span>
                                </a>
                                {#  <button ng-if="item.status == 'draft'" id="delete" onClick="openModal();" class="button shining btn btn-xs-round shining shining-round round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Cancelar envio" {#data-id="{{url('sms/smscancel')}}/{{'{{item.idWhatsapp}}'}}">
              <span class="fa fa-ban"></span>
            </button>#}
                                {#<a ng-if="(item.type == 'lote'  && item.status == 'draft') && (item.status == 'draft' || item.status == 'scheduled')"  ui-sref="edit({idWhatsapp:item.idWhatsapp})" class="button btn btn-xs-round info-inverted" data-toggle="tooltip" data-placement="top" title="Editar envio de sms">
              <span class="glyphicon glyphicon-pencil"></span>
            </a>#}
                                <a ng-if="(item.type == 'csv'  && item.status == 'draft') && (item.status == 'draft' || item.status == 'scheduled')"
                                    ui-sref="editCsv({idWhatsapp:item.idWhatsapp})"
                                    class="button btn btn-xs-round info-inverted" data-toggle="tooltip"
                                    data-placement="top" title="Editar envio de sms">
                                    <span class="glyphicon glyphicon-pencil"></span>
                                </a>
                                <a ng-if="(item.type == 'contact'  && (item.status == 'draft' || item.status == 'scheduled')) && (item.status == 'draft' || item.status == 'scheduled')"
                                    ui-sref="editwhatsappcontact({idWhatsapp:item.idWhatsapp})"
                                    class="button btn btn-xs-round info-inverted" data-toggle="tooltip"
                                    data-placement="top" title="Editar envio de sms">
                                    <span class="glyphicon glyphicon-pencil"></span>
                                </a>
                                <a ng-if="item.type == 'lote' && (item.status == 'draft' || item.status == 'scheduled')"
                                    ui-sref="editspeedsent({idWhatsapp:item.idWhatsapp})"
                                    class="button btn btn-xs-round info-inverted" data-toggle="tooltip"
                                    data-placement="top" title="Editar envio de sms">
                                    <span class="glyphicon glyphicon-pencil"></span>
                                </a>

                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>{#ng-click="searchProcess(item)"#}
            <div ng-if="misc.whatsapp.items.length<=0">
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                        <div class="block block-success">
                            <div class="body success-no-hover text-center">
                                <h2>
                                    No existen envíos de WhatsApp actualmente. Si desea realizar un envío haga <a
                                        ui-sref="toolstwoway">click aquí</a>.
                                </h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="pagination" class="text-center">
            <ul class="pagination">
                <li ng-class="misc.page == 1 ? 'disabled'  : ''">
                    <a href="#/" ng-click="misc.page == 1 ? true  : false || functions.fastbackward()"
                        class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
                </li>
                <li ng-class="misc.page == 1 ? 'disabled'  : ''">
                    <a href="#/" ng-click="misc.page == 1 ? true  : false || functions.backward()"
                        class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
                </li>
                <li>
                    <span><b>
                            <script id="metamorph-58-start" type="text/x-placeholder"></script>
                            {{ "{{misc.whatsapp.total }}"}}
                        </b> registros </span><span>Página <b>{{"{{ misc.page }}"}}
                        </b> de <b>
                            {{ "{{ (misc.whatsapp.total_pages ) }}"}}
                        </b></span>
                </li>
                <li
                    ng-class="misc.page == (misc.whatsapp.total_pages) || misc.whatsapp.total_pages == 0 ? 'disabled'  : ''">
                    <a href="#/"
                        ng-click="misc.page == (misc.whatsapp.total_pages)  || misc.whatsapp.total_pages == 0  ? true  : false || misc.page == (misc.whatsapp.total_pages)  || misc.whatsapp.total_pages == 0  ? true  : false || functions.forward()"
                        class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
                </li>
                <li
                    ng-class="misc.page == (misc.whatsapp.total_pages)  || misc.whatsapp.total_pages == 0 ? 'disabled'  : ''">
                    <a ng-click="misc.page == (misc.whatsapp.total_pages)  || misc.whatsapp.total_pages == 0  ? true  : false || functions.fastforward()"
                        class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
                </li>
            </ul>
        </div>
    </div>
</div>
<div id="ProcessCsv" class="dialog">
    <div class="dialog__overlay"></div>
    <div class="dialog__content">
        <div class="morph-shape">
            <svg xmlns="https://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 560 280"
                preserveAspectRatio="none">
                <rect x="3" y="3" fill="none" width="556" height="276" />
            </svg>
        </div>
        <div class="dialog-inner">

        </div>
    </div>
</div>

<div id="cancelDialog" class="dialog">
    <div class="dialog__overlay"></div>
    <div class="dialog__content">
        <div class="morph-shape">
            <svg xmlns="https://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 560 280"
                preserveAspectRatio="none">
                <rect x="3" y="3" fill="none" width="556" height="276" />
            </svg>
        </div>
        <div class="dialog-inner">
            <h2>¿Esta seguro?</h2>
            <div style="z-index: 999999;">
                <a ng-click="functions.clseModal()" class="button shining btn btn-md danger-inverted"
                    data-dialog-close>Cancelar</a>
                <button ng-click="sockets.executeFunction()"
                    class="button shining btn btn-md success-inverted">Confirmar</button>
            </div>
        </div>
    </div>
</div>