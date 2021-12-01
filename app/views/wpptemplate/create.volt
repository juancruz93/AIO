<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
        <div class="title">
            Nueva plantilla HSM
        </div>
        <hr class="basic-line">
    </div>
</div>

<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">
        <form>
            <div class="block block-info">
                <div class="body row">
                    <div class="col-md-12">
                        <div class="body form-horizontal">
                            <div class="form-group">
                                <label for="nametempwpp" class="col-sm-2 control-label">* Nombre</label>
                                <div class="col-sm-10">
                                    <input type="text" class="undeline-input form-control" id="nametempwpp" maxlength="45" minlength="2" autofocus
                                        data-ng-model="nametempwpp">
                                    <div class="text-right" data-ng-class="nametempwpp.length > 45 ? 'negative':''">
                                        {{"{{nametempwpp.length > 0 ?  nametempwpp.length+'/45':''}}"}}</div>
                                    <h6 class="color-danger text-justify" ng-show='validateNameWpp'>
                                        Por favor ingrese el nombre de la plantilla HSM.
                                    </h6>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="wpptempcateg" class="col-sm-2 control-label">* Categoría</label>
                                <div class="col-sm-9">
                                    <select class="chosen form-control input-lg" style="width: 100%"
                                        data-ng-model="wpptempcateg" id="wpptemplatecategory">
                                        <option value=""></option>
                                        <option ng-repeat="x in listcateg" value="{{"{{x.code}}" }}">{{"{{x.name}}"}}</option>
                                    </select>
                                    <h6 class="color-danger text-justify" ng-show='validateTempWpp'>
                                        Por favor elija la categoria de la plantilla HSM.
                                    </h6>
                                </div>
                                <div class="col-sm-1">
                                    <span ng-click="opeModalMoreInfo()"><i class="fa fa-info-circle" style="font-size: 25px;color:#009fb2;cursor: pointer;margin: 1px 0 0 -6px;"></i></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="contenttempwpp" class="col-sm-2 control-label">* Contenido</label>
                                <div class="col-sm-10">
                                    <textarea class="undeline-input form-control" id="contenttempwpp"
                                        style="resize: none;"
                                        maxlength="300" minlength="1" rows="3"
                                        data-ng-model="contenttempwpp"></textarea>

                                    <div class="text-right" ng-hide='morecaracter'
                                        data-ng-class="contenttempwpp.length > 300 ? 'negative':''">
                                        {{"{{contenttempwpp.length > 0 ?  contenttempwpp.length+'/300 aproximadamente':''}}"}}
                                    </div>
                                    <h6 class="color-danger text-justify" ng-show='validateContentWpp'>
                                        Por favor ingrese el contenido de la plantilla HSM.
                                    </h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="footer text-right">
                    <button type="button"
                        class="button shining btn btn-xs-round shining shining-round round-button success-inverted"
                        data-toggle="tooltip" data-placement="top" title="Guardar" data-ng-click="saveWppTemplate()">
                        <span class="glyphicon glyphicon-ok"></span>
                    </button>
                    <a href="{{url('wpptemplate#/')}}"
                        class="button shining btn btn-xs-round shining shining-round round-button danger-inverted"
                        data-toggle="tooltip" data-placement="top" title="Cancelar">
                        <span class="glyphicon glyphicon-remove"></span>
                    </a>
                    <a ng-show="contenttempwpp" ng-click="openPreview()"
                        class="button shining btn btn-xs-round shining-round round-button success-inverted"
                        data-toggle="tooltip" data-placement="top" title="Visualizar">
                        <span class="fa fa-eye" aria-hidden="true"></span>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">
        <div class="fill-block fill-block-primary">
            <div class="header">
                <b>Instrucciones</b>
            </div>
            <div class="body">
                <p>Recuerde tener en cuenta estas recomendaciones</p>
                <ul>
                    <li>El nombre debe tener mínimo <b>2</b> y máximo <b>45</b> caracteres.</li>
                    <li>Debe seleccionar un item de la lista de categorías.</li>
                    <li>
                        Debe escribir el contenido que llevara la plantilla del <b>WhatsApp(HSM),</b> para enviar parámetros de entrada los cuales se reemplazaran al momento 
                        de realizar un envió, debe especificarlos con números iniciando desde el 1 y así sucesivamente dependiendo la cantidad que se envié (VALIDAR 
                        EL LIMITE) estos números deben estar contenidos entre 2 signos de porcentaje<b>(%)</b> a cada lado del mismo y espacio al inicio y al final, por ejemplo 
                        para enviar el parámetro 1 el cual será reemplazado por el nombre del contacto: Hola mi nombre es <b>%%1%%</b> de Cali.
                    </li>
                    <li>Los campos con asterisco<b>(*)</b> son obligatorios.</li>
                </ul>
            </div>
        </div>
    </div>

</div>
<div id="preview" class="dialog">
    <div class="dialog__overlay"></div>
    <div class="dialog__content">

        <div class="dialog-inner">
            <div class='smsContainer'>
                Tu plantilla tendrá el siguiente aspecto
                <div class="smsContent" ng-bind-html="taggedMessage">
                </div>
            </div>
            <div>
                <a ng-click="closePreview()" id="btn-ok"
                    class="button shining btn btn-md success-inverted float-right">Ok</a>
            </div>
        </div>
    </div>
</div>
<!-- MODAL PARA VER LA INFORMACION ADICIONAL SOBRE LAS CATEGORIAS DE WPP -->
<div id="moreInfo" class="dialog">
    <div class="dialog__overlay"></div>
    <div class="dialog__content" style="width: 70%;max-width: none;">
        <div class="morph-shape">
            <svg xmlns="https://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 560 280"
                preserveAspectRatio="none">
                <rect x="3" y="3" fill="none" width="556" height="276" />
            </svg>
        </div>
        <div class="dialog-inner">
            <div class="table-responsive" style="height: 40em;">
                <table class="table table-bordered sticky-enabled">
                    <thead class="theader">
                        <tr>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Ejemplo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr data-ng-repeat="data in dataMoreInformation">
                            <td>{{"{{data.name}}"}}</td>
                            <td>{{"{{data.description}}"}}</td>
                            <td>
                                <ul>
                                    <li data-ng-repeat="examples in data.examples">{{"{{examples}}"}}</li>
                                </ul>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div>
                <a onClick="closeModalForm()" class="button shining btn btn-md danger-inverted"
                    data-dialog-close>Cerrar</a>
            </div>
        </div>
    </div>
</div>
<script>
    $(".chosen").select2({
        placeholder: 'Seleccione una categoría'
    });

    function closeModalForm() {
        $('#moreInfo').removeClass('dialog dialog--open');
        $('#moreInfo').addClass('modal');
    }

</script>