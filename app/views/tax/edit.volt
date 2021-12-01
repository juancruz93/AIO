<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
        <div class="title">
            Nuevo impuesto
        </div>            
        <hr class="basic-line">
        <p class="text-justify">
            Formulario para crear un impuesto
        </p>
    </div>
</div>

<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">
        <form data-ng-submit="editTax()">
            <div class="block block-info">
                <div class="body row">
                    <div class="col-md-12">
                        <div class="body form-horizontal">
                            <div class="form-group">
                                <label for="idCountry" class="col-sm-3 control-label">*País</label>
                                <div class="col-sm-9">
                                    {#
                                    <select class="chosen form-control" data-ng-model="data.idCountry" style="width: 100%">
                                        <option ng-repeat="x in countries" value="{{"{{x.idCountry}}"}}" data-ng-selected="x.idCountry === data.idCountry">{{"{{x.name}}"}}</option>
                                    </select>
                                    #}
                                    <ui-select ng-model="data.country" autofocus="autofocus" ng-required="true" required="required" ui-select-required theme="select2" sortable="false" close-on-select="true">
                                        <ui-select-match placeholder="Seleccione uno">{{ "{{$select.selected.name}}" }}</ui-select-match>
                                        <ui-select-choices repeat="country in countries | propsFilter: {name: $select.search}">
                                            <div ng-bind-html="country.name | highlight: $select.search"></div>
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
                            <div class="form-group">
                                <label for="type" class="col-sm-3 control-label">{{form.label('type')}}</label>
                                <div class="col-sm-9">
                                    {#
                                    <select class="chosen form-control" data-ng-model="data.type" style="width: 100%">
                                        <option ng-repeat="x in types" value="{{"{{x.value}}"}}" data-ng-class="x.value == data.type">{{"{{x.name}}"}}</option>
                                    </select>
                                    #}
                                    <ui-select ng-model="data.tp" autofocus="autofocus" ng-required="true" required="required" ui-select-required theme="select2" sortable="false" close-on-select="true">
                                        <ui-select-match placeholder="Seleccione uno">{{ "{{$select.selected.name}}" }}</ui-select-match>
                                        <ui-select-choices repeat="type in types | propsFilter: {name: $select.search}">
                                            <div ng-bind-html="type.name | highlight: $select.search"></div>
                                        </ui-select-choices>
                                    </ui-select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="amount" class="col-sm-3 control-label">{{form.label('amount')}}</label>
                                <div class="col-sm-9">
                                    {{form.render('amount')}}
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
                    <a href="{{url('tax#/')}}" class="button shining btn btn-xs-round shining shining-round round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Cancelar">
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
                    <li><p>Debe seleccionar el país al cual pertenece el impuesto.</p></li>
                    <li><p>El nombre del impuesto debe tener al menos 2 y máximo 70 caracteres.</p></li>
                    <li><p>Debe seleccionar el tipo de valor del impuesto. Si selecciona porcentaje, en el momento de calcular un pago se calculará el valor del impuesto por porcentaje,
                        si selecciona neto, será ese valor fijo el que se le suma al pago total.</p></li>
                    <li><p>El valor del impuesto puede ser la cantidad del porcentaje (no es necesario escribir el comodín '%') o el valor neto.</p></li>
                    <li><p>La descripción debe tener al menos 2 y máximo 100 caracteres.</p></li>
                    <li><p>Debe seleccionar el estado, el cual será activo o inactivo.</p></li>
                </ul> 
                <p></p>
            </div>
            <div class="footer">
                Edición 
            </div>
        </div>     
    </div>
</div>
<script>
    $(function () {
        setTimeout(function () {
            $('[data-toggle="tooltip"]').tooltip();
        }, 1000);

        $(".chosen").select2({
            placeholder: 'Seleccione un elemento'
        });
    });
</script>