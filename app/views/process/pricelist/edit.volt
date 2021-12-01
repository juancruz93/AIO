<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
        <div class="title">
            Editar una lista de precios
        </div>            
        <hr class="basic-line">
        <p class="text-justify">
            Formulario para editar una lista de precios
        </p>
    </div>
</div>

<div class="row" ng-cloak>
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">
        <form data-ng-submit="editPriceList()">
            <div class="block block-info">
                <div class="body row">
                    <div class="col-md-12">
                        <div class="body form-horizontal">
                            <div class="form-group">
                                <label for="idCountry" class="col-sm-3 control-label">*País</label>
                                <div class="col-sm-9">
                                    {#
                                    <select class="chosen form-control" data-ng-model="data.idCountry" style="width: 100%" required="true">
                                        <option ng-repeat="x in countries" value="{{"{{x.idCountry}}"}}" ng-selected="x.idCountry == data.idCountry">{{"{{x.name}}"}}</option>
                                    </select>
                                    #}
                                    <ui-select ng-model="data.country" autofocus="autofocus" ng-required="true" ui-select-required theme="select2" sortable="false" close-on-select="true">
                                        <ui-select-match placeholder="Seleccione uno">{{ "{{$select.selected.name}}" }}</ui-select-match>
                                        <ui-select-choices repeat="country in countries | propsFilter: {name: $select.search}">
                                            <div ng-bind-html="country.name | highlight: $select.search"></div>
                                        </ui-select-choices>
                                    </ui-select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="idServices" class="col-sm-3 control-label">{{form.label('idServices')}}</label>
                                <div class="col-sm-9">
                                    {#
                                    <select class="chosen form-control" id="idServices" data-ng-model="data.idServices" style="width: 100%" required="true" data-ng-change="changeService()">
                                        <option ng-repeat="x in listServices" value="{{"{{x.idServices}}"}}" ng-selected="x.idServices == data.idServices">{{"{{x.name}}"}}</option>
                                    </select>
                                    #}
                                    <ui-select ng-model="data.service" on-select="changeService()" ng-required="true" ui-select-required theme="select2" sortable="false" close-on-select="true">
                                        <ui-select-match placeholder="Seleccione uno">{{ "{{$select.selected.name}}" }}</ui-select-match>
                                        <ui-select-choices repeat="service in listServices | propsFilter: {name: $select.search}">
                                            <div ng-bind-html="service.name | highlight: $select.search"></div>
                                        </ui-select-choices>
                                    </ui-select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="name" class="col-sm-3 control-label">{{form.label('name')}}</label>
                                <div class="col-sm-9">
                                    {{form.render('name')}}
                                    <div class="text-right" data-ng-class="data.name.length > 70 ? 'negative':''">{{"{{data.name.length > 0 ?  data.name.length+'/70':''}}"}}</div>
                                </div>
                            </div>
                            <div class="form-group" data-ng-show="viewMode">
                                <label for="accountingMode" class="col-sm-3 control-label">{{form.label('accountingMode')}}</label>
                                <div class="col-sm-9">
                                    {#
                                    <select class="chosen form-control" id="accountingMode" data-ng-model="data.accountingMode" style="width: 100%" required="true" data-ng-change="changeService()">
                                        <option ng-repeat="x in listaccountingMode" value="{{"{{x.value}}"}}" ng-selected="x.value == data.accountingMode">{{"{{x.name}}"}}</option>
                                    </select>
                                    #}
                                    <ui-select ng-model="data.accountingMode" ng-required="true" ui-select-required theme="select2" sortable="false" close-on-select="true">
                                        <ui-select-match placeholder="Seleccione uno">{{ "{{$select.selected.name}}" }}</ui-select-match>
                                        <ui-select-choices repeat="accountingMode in accountingModes | propsFilter: {name: $select.search}">
                                            <div ng-bind-html="accountingMode.name | highlight: $select.search"></div>
                                        </ui-select-choices>
                                    </ui-select>
                                </div>
                            </div>
                            <div class="form-group" data-ng-show="viewMailTester">
                                <label for="amount" class="col-sm-3 control-label">{{form.label('minValue')}}</label>
                                <div class="col-sm-9">
                                    {{form.render('minValue')}}
                                </div>
                            </div>
                            <div class="form-group" data-ng-show="viewMailTester">
                                <label for="amount" class="col-sm-3 control-label">{{form.label('maxValue')}}</label>
                                <div class="col-sm-9">
                                    {{form.render('maxValue')}}
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="amount" class="col-sm-3 control-label">{{form.label('price')}}</label>
                                <div class="col-sm-9">
                                    {{form.render('price')}}
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="description" class="col-sm-3 control-label">{{form.label('description')}}</label>
                                <div class="col-sm-9">
                                    {{form.render('description')}}
                                    <div class="text-right" data-ng-class="data.description.length > 100 ? 'negative':''">{{"{{data.description.length > 0 ?  data.description.length+'/100':''}}"}}</div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="status" class="col-sm-3 control-label">*Estado</label>
                                <div class="col-sm-9">
                                    <md-switch class="md-warn none-margin" md-no-ink aria-label="Switch No Ink" data-ng-model="data.status">
                                    </md-switch>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="footer text-right">
                    <button type="submit" class="button shining btn btn-xs-round shining shining-round round-button success-inverted" data-toggle="tooltip" data-placement="top" title="Guardar">
                        <i class="fa fa-check"></i>
                    </button>
                    <a href="{{url('pricelist#/')}}" class="button shining btn btn-xs-round shining shining-round round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Cancelar">
                        <i class="fa fa-times"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">                            
        <div class="fill-block fill-block-info">
            <div class="header">
                Información
            </div>
            <div class="body">
                <p>Recuerde tener en cuenta estas recomendaciones:</p>
                <ul>                            
                    <li><p>Debe seleccionar el país donde se quiere ofrecer este precio.</p></li>
                    <li><p>Debe seleccionar el servicio al que pondrá precio</p></li>
                    <li><p>El nombre debe tener al menos 2 y máximo 70 caracteres</p></li>
                    <li><p>El modo de contabilidad solo será tomado en cuento en caso de que seleccione el servicio de Email Marketing</p></li>
                    <li><p>Debe ingresar un valor mínimo y un valor máximo</p></li>
                    <li><p>Debe ingresar un precio el cual debe escribir sin comas ni puntos, a menos de que necesite escribir decimas para ellos use la coma (50000,99)</p></li>
                    <li><p>La descripción debe tener al menos 2 y máximo 100 caracteres</p></li>
                    <li><p>Debe seleccionar un estado el cual podrá se activo o inactivo, por defecto el sistema lo pondrá activo.</p></li>
                </ul> 
                <p></p>
            </div>
            <div class="footer">
                Creación
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
</script>