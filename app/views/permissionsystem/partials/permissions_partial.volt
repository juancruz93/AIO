<script type="text/x-handlebars" data-template-name="permissions/index">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <h2>Agregar permisos a <strong><em>{{ '{{App.role.name}}' }}</em></strong></h2>
        </div>
    </div>
    
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <div class="block block-info">
                <div class="body">
                    <table class="table table-bordered">
                        <thead class="theader"></thead>
                        <tbody>
                            {{ '{{#each App.permissions}}' }}
                                <tr data-toggle="collapse" data-target="#detail{{'{{unbound id}}'}}">
                                    <td><h4><strong>{{ '{{name}}' }}</strong></h4></td>
                                </tr>
                                <tr id="detail{{'{{unbound id}}'}}" class="collapse">
                                    <td>
                                        <table class="table table-bordered" style="width: 80%;" align="center">
                                            <thead class="theader"></thead>
                                            <tbody>
                                                {{ '{{#each actions}}' }}
                                                    <tr>
                                                        <td style="width: 50%; border: 1px solid black;">{{ '{{name}}' }}</td>
                                                        <td class="text-right" style="width: 50%; border: 1px solid black;">
                                                            {{ '{{#if allowed}}' }}
                                                                {{ ' {{view App.RadioButton name="schedule" id=id class="cursor-pointer toggle-one" selectionBinding="scheduleRadio" checked="checked"}}' }}
                                                            {{ '{{else}}' }}
                                                                {{ ' {{view App.RadioButton name="schedule" id=id class="cursor-pointer toggle-one" selectionBinding="scheduleRadio"}}' }}
                                                            {{ '{{/if}}' }}    
                                                        </td>
                                                    </tr>    
                                                {{ '{{/each}}' }}
                                            </tbody>
                                        </table>    
                                    </td>
                                </tr>
                            {{ '{{/each}}' }}
                        </tbody>
                    </table>
                </div>
                <div class="footer text-right">
                    <button {{ '{{action "cancel" this}}' }} class="button btn btn-xs-round shining shining-round round-button danger-inverted">
                        <span class="glyphicon glyphicon-remove"></span>
                    </button>
                    <button {{ '{{action "save" this}}' }} class="button btn btn-xs-round shining shining-round round-button success-inverted">
                        <span class="glyphicon glyphicon-ok"></span>
                    </button>
                </div>
            </div>
        </div>  
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <div class="fill-block fill-block-primary">
                <div class="header">
                    Información
                </div>
                <div class="body">
                    <p>
                        Haga click sobre el recurso y se desplegarán las acciones. desde aqui podrá activar o desactivar acciones, creando un mapa de recursos.
                    </p>
                </div>
                <div class="footer">
                    Creación
                </div>
            </div>
        </div>
    </div>
</script>    
