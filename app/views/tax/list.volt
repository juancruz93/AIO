<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
        <div class="title">
            Lista de impuestos
        </div>            
        <hr class="basic-line">
        <p>
            Aquí encontrará el listado de impuestos con su respectivo correspondiente a cada país donde se ofrece el servico de All in One,
            se utiliza para realizar gestión de cobro automático.
        </p>            
    </div>
</div>

<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap text-left">
        <div class="form-inline">
            <div class="form-group">
                <div class="input-group">
                    <input type="text" class="undeline-input form-control" id="exampleInputAmount" placeholder="Buscar por nombre" autofocus="true" data-ng-model="filterName" data-ng-change="searchForName()">
                    <div class="input-group-addon"><i class="fa fa-search"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap text-right">
        <a href="{{url('tools')}}" class="button shining btn default-inverted">Regresar</a>
        <a href="{{url("tax#/create")}}" class="shining btn success-inverted">Crear nuevo impuesto</a>
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

<div class="row" ng-show="list.items.length > 0">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap sticky-wrap">
        <div class="table-responsive">
            <table class="table table-bordered sticky-enabled">
                <thead class="theader">
                    <tr>
                        <th>Nombre</th>
                        <th>Valor</th>
                        <th>Detalle</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr data-ng-repeat="i in list.items" data-ng-class="i.status == 0 ? 'danger letter-no-hover':''">
                        <td>
                            <b class="medium-text">{{"{{i.name}}"}}</b>
                            <br>
                            Impuesto valído solo para <b>{{"{{i.country}}"}}</b>
                            <br>
                            <em class="extra-small-text">Creado por <b>{{"{{i.createdBy}}"}}</b> el día <b>{{"{{i.created}}"}}</b> <br>
                                Actualizado por <b>{{"{{i.updatedBy}}"}}</b> el día <b>{{"{{i.updated}}"}}</b></em>
                        </td>
                        <td style="font-size: 2em;">{{"{{i.type == 'net' ? '$':''}}"}}{{"{{i.amount}}"}}{{"{{i.type == 'percentage' ? '%':''}}"}}</td>
                        <td>
                            {{"{{i.type == 'net' ? 'Neto':'Porcentaje'}}"}}<br>
                            <em>{{"{{i.description}}"}}</em>
                        </td>
                        <td class="text-right">
                            <a href="" class="button shining btn btn-xs-round shining-round round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Eliminar impuesto" data-ng-click="openMod(i.idTax);">
                                <i class="fa fa-trash"></i>
                            </a>
                            <a href="{{url('tax#/edit')}}/{{"{{i.idTax}}"}}" class="button shining btn btn-xs-round shining-round round-button info-inverted" data-toggle="tooltip" data-placement="top" title="Editar impuesto">
                                <i class="fa fa-pencil"></i>
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div ng-show="list.items.length == 0">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
        <div class="block block-success">
            <div class="body success-no-hover text-center">
                <h2>
                    No hay registros de impuestos que coincidan con los filtros, para crear una haga <a href="{{url("tax#/create")}}"><u>Clic aquí</u></a>.
                </h2>    
                </h2>    
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
                Si elimina el registro del impuesto, no podrá recuperar la información y <b>es posible que afecte a otros registros. </b>
            </div>
            <br>
            <div>
                <a onClick="closeModal();" class="button shining btn btn-md danger-inverted" data-dialog-close>Cancelar</a>
                <a href="#/" data-ng-click="deleteTax()" id="btn-ok" class="button shining btn btn-md success-inverted">Confirmar</a>
            </div>
        </div>
    </div>
</div>
<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
    function openModal() {
        $('.dialog').addClass('dialog--open');
    }

    function closeModal() {
        $('.dialog').removeClass('dialog--open');
    }
</script>
