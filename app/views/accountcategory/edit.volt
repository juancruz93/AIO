<div ng-cloak>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
            <div class="title">
                Editar categoría de Cuenta <b>{{"{{data.name}}"}}</b>
            </div>
            <hr class="basic-line">
            <p class="text-justify">
                Las categorías de cuenta le ayudarán a organizar de manera práctica los registros de las cuentas
            </p>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">
            <form name="campcampcateg" data-ng-submit="editAccountCategory()">
                <div class="block block-info">
                    <div class="body row">
                        <div class="col-md-12">
                            <div class="body form-horizontal">
                                <div class="form-group">
                                    <label for="name" class="col-sm-2 control-label">*Nombre</label>
                                    <div class="col-sm-10">
                                        {{accountCategoryForm.render('name')}}
                                        <div class="text-right" data-ng-class="data.name.length > 70 ? 'negative':''">{{"{{data.name.length > 0 ?  data.name.length+'/70':''}}"}}</div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="description" class="col-sm-2 control-label">Descripción</label>
                                    <div class="col-sm-10">
                                        {{accountCategoryForm.render('description')}}
                                        <div class="text-right" data-ng-class="data.description.length > 200 ? 'negative':''">{{"{{data.description.length > 0 ?  data.description.length+'/200':''}}"}}</div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">*Estado</label>
                                    <div class="col-sm-10">
                                        <md-switch class="md-primary none-margin" ng-model="data.status" md-no-ink aria-label="Switch 1">
                                        </md-switch>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">*Fecha de expiración</label>
                                    <div class="col-sm-10">
                                        <md-switch class="md-primary none-margin" ng-model="data.expirationDate" md-no-ink aria-label="Switch 1">
                                        </md-switch>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="footer text-right">
                        <button type="submit" class="button shining btn btn-xs-round shining shining-round round-button success-inverted" data-toggle="tooltip" data-placement="top" title="Guardar">
                            <span class="glyphicon glyphicon-ok"></span>
                        </button>
                        <a ui-sref="index" class="button shining btn btn-xs-round shining shining-round round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Cancelar">
                            <span class="glyphicon glyphicon-remove"></span>
                        </a>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">
            <div class="fill-block fill-block-primary" >
                <div class="header">
                    Información
                </div>
                <div class="body">
                    <p>
                        Recuerde tener en cuenta estas recomendaciones:
                    <ul>
                        <li>El nombre debe tener mínimo 2 y máximo 70 caracteres</li>
                        <li>El nombre de la categoría debe ser un nombre único, es decir, no pueden existir dos categorías con el mismo nombre.</li>
                        <li>La descripción debe tener máximo 200 caracteres</li>
                        <li>Recuerde que los campos con asterisco(*) son oblogatorios</li>
                    </ul>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>