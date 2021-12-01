<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
        <div class="title">
            Idiomas
        </div>            
        <hr class="basic-line" />
        <p>
            La plataforma podrá cambiar el idioma de presentación. Estos son los idiomas disponibles del sistema.
        </p>
    </div>
</div>
<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap text-right">     
        <a href="{{url('system')}}" class="button shining btn default-inverted">Regresar</a>
        <a href="{{url('language/create')}}" class="button shining btn success-inverted">Crear un nuevo idioma</a>
    </div>
</div>

<div id="pagination" class="text-center" ng-show="language.items.length>0">
    <ul class="pagination">
        <li ng-class="page == 1 ? 'disabled'  : ''">
            <a  href="#/" ng-click="page == 1 ? true  : false || fastbackward()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
        </li>
        <li  ng-class="page == 1 ? 'disabled'  : ''">
            <a href="#/"  ng-click="page == 1 ? true  : false || backward()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
        </li>
        <li>
            <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{language.total }}"}}
                </b> registros </span><span>Página <b>{{"{{ page }}"}}
                </b> de <b>
                    {{ "{{ (language.total_pages ) }}"}}
                </b></span>
        </li>
        <li   ng-class="page == (language.total_pages) || language.total_pages == 0 ? 'disabled'  : ''">
            <a href="#/" ng-click="page == (language.total_pages)  || language.total_pages == 0  ? true  : false || page == (language.total_pages)  || language.total_pages == 0  ? true  : false || forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
        </li>
        <li   ng-class="page == (language.total_pages)  || language.total_pages == 0 ? 'disabled'  : ''">
            <a ng-click="page == (language.total_pages)  || language.total_pages == 0  ? true  : false || fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
        </li>
    </ul>
</div>

<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">

        <table class="table table-bordered" ng-show="language.items.length>0">
            <thead class="theader ">
                <tr>
                    <th>Nombre completo</th>
                    <th>Nombre corto</th>
                    <th>Creación</th>
                    <th>Actualización</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="tbody">
                <tr ng-repeat="language in language.items">
                    <td><div class='medium-text'>{{ '{{language.name}}' }}</div></td>
                    <td><div class='medium-text'>{{ '{{language.shortName}}' }}</div></td>
                    <td>
                        <div class=''><b>{{ '{{language.created}}' }}</b></div>
                        <div class=''>Por: <b>{{ '{{language.createdBy}}' }}</b></div>
                    </td>
                    <td>
                        <div class=''><b>{{ '{{language.updated}}' }}</b></div>
                        <div class=''>Por: <b>{{ '{{language.updatedBy}}' }}</b></div>
                    </td>
                    <td>
                        <a href="" class="button shining btn btn-xs-round shining-round round-button danger-inverted" data-toggle="tooltip" data-placement="left" title="Eliminar" data-ng-click="confirmDelete(language.idLanguage)">
                            <md-tooltip>
                                Eliminar
                            </md-tooltip>
                            <span class="glyphicon glyphicon-trash"></span>
                        </a>
                        <a href="#/edit/{{ '{{language.idLanguage}}' }}" class="button shining btn btn-xs-round shining-round round-button primary-inverted" data-toggle="tooltip" data-placement="left" title="Editar">
                            <md-tooltip>
                                Editar
                            </md-tooltip>
                            <span class="glyphicon glyphicon-pencil"></span>
                        </a>



                    </td>
                </tr>

            </tbody>
        </table>
        <div id="pagination" class="text-center" ng-show="language.items.length==0">

        </div>
        <div  class="" style="" ng-show="!language.items.length">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <div class="block block-success">
                    <div class="body success-no-hover text-center">
                        <h2>
                            No hay registros de idiomas, para crear uno haga <a href="{{url('language/create')}}">clic aquí</a>.
                        </h2>

                    </div>
                </div>
            </div>
        </div>


    </div>
</div>

<div id="pagination" class="text-center" ng-show="language.items.length>0">
    <ul class="pagination">
        <li ng-class="page == 1 ? 'disabled'  : ''">
            <a  href="#/" ng-click="page == 1 ? true  : false || fastbackward()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
        </li>
        <li  ng-class="page == 1 ? 'disabled'  : ''">
            <a href="#/"  ng-click="page == 1 ? true  : false || backward()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
        </li>
        <li>
            <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{language.total }}"}}
                </b> registros </span><span>Página <b>{{"{{ page }}"}}
                </b> de <b>
                    {{ "{{ (language.total_pages ) }}"}}
                </b></span>
        </li>
        <li   ng-class="page == (language.total_pages) || language.total_pages == 0 ? 'disabled'  : ''">
            <a href="#/" ng-click="page == (language.total_pages)  || language.total_pages == 0  ? true  : false || page == (language.total_pages)  || language.total_pages == 0  ? true  : false || forward()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
        </li>
        <li   ng-class="page == (language.total_pages)  || language.total_pages == 0 ? 'disabled'  : ''">
            <a ng-click="page == (language.total_pages)  || language.total_pages == 0  ? true  : false || fastforward()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
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
                ¿Esta seguro de que desea eliminar el idioma?
            </div>
            <br>
            <div>
                <a onClick="closeModal();" class="button shining btn btn-md danger-inverted" data-dialog-close>Cancelar</a>
                <a href="#/" data-ng-click="deleteLanguage()" id="btn-ok" class="button shining btn btn-md success-inverted">Confirmar</a>
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
</div>
</div>

