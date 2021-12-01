<style>
u:hover {
    background-color: #E5E8E8;
}
</style>
<div ng-cloak >
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
            <div class="title">
                Lista de autorespuestas
            </div>
            <hr class="basic-line" />
            <p>
                Este es el listado principal de Autorrespuestas de SMS. Podrá encontrar la información perteneciente a cada autorrespuesta, editarla o eliminarla. Tambien podrá filtrar por el nombre de la autorrespuesta.
            </p>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap ">
            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 none-padding-left">
                <div class="form-inline">
                    <div class="form-group">
                        <div class="input-group">
                            <input type="text" class="form-control" id="exampleInputAmount" placeholder="Buscar por nombre" autofocus="true" data-ng-model="filter.name" data-ng-change="filtername()">
                            <div class="input-group-addon"><i class="fa fa-search"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 text-right none-padding">
                <a href="{{ url('marketing') }}" class="button btn button default-inverted"><i class="fa fa-arrow-left" aria-hidden="true"> </i> Regresar</a>
{#              <a ui-sref="birthday" class="button btn button success-inverted">Crear una nueva autorespuesta</a>#}
                <a ui-sref="tools" class="button btn button success-inverted">Crear Nueva Autorrespuesta</a>
            </div>
        </div>
    </div>

    <div id="pagination" class="text-center" ng-show="list.items.length > 0">
        <ul class="pagination"> 
            <li ng-class="page == 1 ? 'disabled'  : ''">
                <a  href="#/" ng-click="page == 1 ? true  : false || fastbackward()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
            </li>
            <li  ng-class="page == 1 ? 'disabled'  : ''">
                <a href="#/"  ng-click="page == 1 ? true  : false || backward()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
            </li>
            <li>
          <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{list.total }}"}}
            </b> registros </span><span>Página <b>{{"{{ page }}"}}
            </b> de <b>
              {{ "{{ (list.total_pages ) }}"}}
            </b></span>
            </li>
            <li   ng-class="page == (list.total_pages)  || list.total_pages == 0  ? 'disabled'  : ''">
                <a href="#/" ng-click="page == (list.total_pages)  || list.total_pages == 0  ? true  : false || forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
            </li>
            <li   ng-class="page == (list.total_pages)  || list.total_pages == 0  ? 'disabled'  : ''">
                <a ng-click="page == (list.total_pages)  || list.total_pages == 0  ? true  : false || fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
            </li>
        </ul>
    </div>

    <div class="row" >
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap" ng-show="list.items.length > 0">
            <div class="row" data-ng-repeat="key in list.items">
                <div class="" style="float: left; width: 99%">
                    <div ng-class="{'border-left-success-10px': key.status == 1,'border-left-danger-10px': key.status == 0}" class="row fill-block fill-block-default" >
                        <div class="row ">
                            <div class="col-xs-4 col-sm-4 col-lg-4 cursor" data-toggle="collapse" data-target="#allinfo{{ '{{key.idAutoresponder}}' }}" aria-expanded="false"
                                 aria-controls="allinfo{{ "{{ key.idAutoresponder }}"}}">
                                <div>
                                    <dd><u class="small-text">{{"{{key.name}}"}}</u></dd>
                                </div>
                            </div>
                            <div class="col-xs-4 col-sm-4 col-lg-4 row" data-toggle="collapse" data-target="#allinfo{{ '{{key.idAutoresponder}}' }}" aria-expanded="false"
                                 aria-controls="allinfo{{ "{{ key.idAutoresponder }}"}}">
                                <div class="col-lg-12 col-md-12 col-sm-12 text-center">
                                    <di>
                                        <dd>Programada para la(s)
                                            <p class="medium-text"><b>{{"{{key.time}}"}}</b></p>
                                        </dd>
                                        <dd>
                                            los dias <b data-ng-repeat="day in key.days">
                                                {{ '{{ translationDays(day) }}' }},
                                            </b> recurrente.
                                        </dd>
                                        <dd>Es de Tipo:<p class="small-text"><b>{{'{{ translateClass(key.type) }}'}}</b></p></dd>
                                    </di>
                                </div>
                            </div>
                            <div class="col-xs-4 col-sm-4 col-lg-4 text-right">
                                <button id="delete" ng-click="confirmDelete(key.idAutoresponder)" class="button shining btn btn-xs-round shining shining-round round-button danger-inverted" >
                                    {#                    <button id="delete" onClick="openModal();" data-id="{{"{{key.idAutoresponder}}"}}" class="button shining btn btn-xs-round shining shining-round round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Borrar este correo" >#}
                                    <md-tooltip class="background-tooltip">
                                        Borrar Autorespuesta
                                    </md-tooltip>
                                    <span class="glyphicon glyphicon-trash"></span>
                                </button>
                                <button  ng-if="key.type == 'mail'" type="button" class="button btn btn-xs-round success-inverted" data-ng-click="previewAutoresponder(key.idAutoresponder);" data-toggle="modal" data-target="#myModal">
                                    <md-tooltip class="background-tooltip">
                                        Previsualizar
                                    </md-tooltip>
                                    <i class="fa fa-eye" aria-hidden="true"></i>
                                </button>
                                <a ng-if="key.type == 'sms'" ui-sref="birthdaysms({id:key.idAutoresponder})" class="button shining btn btn-xs-round shining shining-round round-button info-inverted" >
                                    <md-tooltip class="background-tooltip">
                                        Editar Autorespuesta Sms
                                    </md-tooltip>
                                    <span class="glyphicon glyphicon-pencil"></span>
                                </a>
                                    
                                <a ng-if="key.type == 'mail'" ui-sref="birthday({id:key.idAutoresponder})" class="button shining btn btn-xs-round shining shining-round round-button info-inverted" >
                                    <md-tooltip class="background-tooltip">
                                        Editar Autorespuesta Email
                                    </md-tooltip>
                                    <span class="glyphicon glyphicon-pencil"></span>
                                </a>
                                    
                                {#<a href="{{url("statistic#/mail")}}/{{"{{key.idAutoresponder}}"}}"  ng-show="key.status == 'sent'"  class="button shining btn btn-xs-round shining shining-round round-button success-inverted" >
                                    <md-tooltip md-direction="bottom">
                                        Ver estadísticas
                                    </md-tooltip>
                                    <span class="fa fa-bar-chart"></span>
                                </a>#}
                            </div>
                        </div>
                        <div id="allinfo{{ "{{ key.idAutoresponder }}"}}" class="collapse row" >
                            <div class="col-lg-11 col-md-11 col-sm-11  fill-block-default margin-block padding-block table-responsive">
                                <table ng-if="key.type == 'sms'" class="border-table-block" align="center">
                                    <tr>
                                        <td>
                                          <b>Asunto (<i>Descripción</i>):</b>
                                        </td>
                                        <td ng-if="key.subject">
                                            {{"{{ key.subject}}"}}
                                        </td>
                                        <td ng-if="!key.subject">
                                            <i>No Asignado</i>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <b>Enviar a:</b>
                                        </td>
                                        <td>
                                            {{"{{ key.target}}"}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <b>Estado:</b>
                                        </td>
                                        <td>
                                            {{"{{ key.status == 1 ? 'Activo' : 'Inactivo' }}"}}
                                        </td>
                                    </tr>
                                </table>
                                <table ng-if="key.type == 'mail'" class="border-table-block" align="center">
                                    <tr>
                                        <td>
                                            <b>Asunto:</b>
                                        </td>
                                        <td>
                                            {{"{{ key.subject  }}"}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <b>Remitente:</b>
                                        </td>
                                        <td>
                                            {{"{{ key.nameSender  }}"}}
                                            <{{"{{ key.emailsender  }}"}}>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <b>Responder a:</b>
                                        </td>
                                        <td>
                                            {{"{{ key.replyTo  }}"}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <b>Enviar a:</b>
                                        </td>
                                        <td>
                                            {{"{{ key.target}}"}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <b>Estado:</b>
                                        </td>
                                        <td>
                                            {{"{{ key.status == 1 ? 'Activo' : 'Inactivo' }}"}}
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div ng-show="list.items.length == 0">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <div class="block block-success">
                    <div class="body success-no-hover text-center">
                        <h2>
                            No hay registros de autorespuestas que coincidan con los filtros, para crear una haga <a ui-sref="birthday"><u>Clic aquí</u></a>.
                        </h2>
                        </h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="pagination" class="text-center" ng-show="list.items.length > 0">
        <ul class="pagination">
            <li ng-class="page == 1 ? 'disabled'  : ''">
                <a  href="#/" ng-click="page == 1 ? true  : false || fastbackward()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
            </li>
            <li  ng-class="page == 1 ? 'disabled'  : ''">
                <a href="#/"  ng-click="page == 1 ? true  : false || backward()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
            </li>
            <li>
          <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{list.total }}"}}
            </b> registros </span><span>Página <b>{{"{{ page }}"}}
            </b> de <b>
              {{ "{{ (list.total_pages ) }}"}}
            </b></span>
            </li>
            <li   ng-class="page == (list.total_pages)  || list.total_pages == 0  ? 'disabled'  : ''">
                <a href="#/" ng-click="page == (list.total_pages)  || list.total_pages == 0  ? true  : false || forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
            </li>
            <li   ng-class="page == (list.total_pages)  || list.total_pages == 0  ? 'disabled'  : ''">
                <a ng-click="page == (list.total_pages)  || list.total_pages == 0  ? true  : false || fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
            </li>
        </ul>
    </div>

    <div id="somedialog" class="dialog ng-scope">
        <div class="dialog__overlay"></div>
        <div class="dialog__content">
            <div class="morph-shape">
                <svg xmlns="https://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 560 280" preserveAspectRatio="none">
                    <rect x="3" y="3" fill="none" width="556" height="276"></rect>
                </svg>
            </div>
            <div class="dialog-inner">
                <h2>¿Esta seguro?</h2>
                <div>
                    Debe tener en cuenta que si elimina la autorespuesta ya no la podrá volver a visualizarla.
                </div>
                <br>
                <div>
                    <a onclick="closeModal();" class="button shining btn btn-md danger-inverted" data-dialog-close="">Cancelar</a>
                    <a href="#/" data-ng-click="deleteAutoresponder()" id="btn-ok" class="button shining btn btn-md success-inverted">Confirmar</a>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-prevew-width">
            <div class="modal-content modal-prevew-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h1 class="modal-title" id="myModalLabel">Contenido del correo</h1>
                </div>
                <div class="modal-body modal-prevew-body" id="modal-body-preview" style="height: 550px;">

                </div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="button fill btn btn-sm danger-inverted">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(function () {
        setTimeout(function () {
            $('[data-toggle="tooltip"]').tooltip();
        }, 1000);
    });

    function openModal() {
        $('.dialog').addClass('dialog--open');
    }

    function closeModal() {
        $('.dialog').removeClass('dialog--open');
    }

    function htmlPreviewAutores(idAutoresponder) {
        $.post("{{url('autoresponder/preview')}}/" + idAutoresponder, function (preview) {
            var e = preview.preview;
            $('<iframe id="frame" frameborder="0" width="100%" height="100%" />').appendTo('#modal-body-preview').contents().find('body').append(e);
        });
    }
</script>