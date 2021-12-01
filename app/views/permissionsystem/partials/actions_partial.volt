<script type="text/x-handlebars" data-template-name="actions/add">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <h2>Registrar nueva acción para el recurso <strong><em>{{ '{{App.resource.name}}' }}</em></strong></h2>
        </div>
    </div>
    
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <div class="block block-info">
                <div class="body">
                    <div class="form-horizontal" role="form">
                        <div class="form-group">
                            <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2 text-right">
                                *Nombre:
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-10 col-lg-10">
                                {{' {{input name="name" type="text" valueBinding="name" placeholder="Nombre de la acción" id="name" required="required" autofocus="autofocus" class="ember-textfield"}} '}}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="footer text-right">
                    <button class="button shining btn btn-xs-round shining shining-round round-button danger-inverted" {{'{{action "cancel" this}}'}}><span class="glyphicon glyphicon-remove"></span></button>
                    <button class="button shining btn btn-xs-round shining shining-round round-button success-inverted" {{'{{action "save" this}}'}}><span class="glyphicon glyphicon-ok"></span></button>
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
                        Debe ingresar un nombre para la nueva acción, este no debe contener espacios, ni caracteres especiales, y debe tener entre 4 y 40 caracteres.
                    </p>
                    <p>
                        Recuerde que los campos con (*)asterisco son obligatorios.
                    </p>
                </div>
                <div class="footer">
                    Creación
                </div>
            </div>
        </div>
    </div>
</script>

<script type="text/x-handlebars" data-template-name="actions/update">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <h2>Editar la acción del recurso <em><strong></strong></em></h2>
        </div>
    </div>
    
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <div class="block block-info">
                <div class="body">
                    <div class="form-horizontal" role="form">
                        <div class="form-group">
                            <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2 text-right">
                                *Nombre:
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-10 col-lg-10">
                                {{'{{input name="name" type="text" valueBinding="name" placeholder="Nombre del recurso" required="required" autofocus="autofocus" class="ember-textfield"}}'}}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="footer text-right">
                    <button class="button shining btn btn-xs-round shining shining-round round-button danger-inverted" {{'{{action "cancel" this}}'}}><span class="glyphicon glyphicon-remove"></span></button>
                    <button class="button shining btn btn-xs-round shining shining-round round-button success-inverted" {{'{{action "update" this}}'}}><span class="glyphicon glyphicon-ok"></span></button>
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
                        Debe ingresar un nombre para la acción, este no debe contener espacios, ni caracteres especiales, y debe tener entre 4 y 40 caracteres.
                    </p>
                    <p>
                        Recuerde que los campos con (*)asterisco son obligatorios.
                    </p>
                </div>
                <div class="footer">
                    Creación
                </div>
            </div>
        </div>
    </div>
</script>

<script type="text/x-handlebars" data-template-name="actions/remove">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <h2>Eliminar la accion <em><strong>{{'{{name}}'}}</strong></em>, del recurso {{ '{{App.resource.content.name}}' }}</h2>
        </div>
    </div>
    
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <div class="block block-warning">
                <div class="body">
                    <div class="alert alert-warning" role="alert">
                        ¿Está seguro que desea eliminar esta acción?
                    </div>
                </div>
                <div class="footer text-right">
                    <button class="button shining btn btn-xs-round shining shining-round round-button info-inverted" {{'{{action "cancel" this}}'}}><span class="glyphicon glyphicon-remove"></span></button>
                    <button class="button shining btn btn-xs-round shining shining-round round-button danger-inverted" {{'{{action "remove" this}}'}}><span class="glyphicon glyphicon-ok"></span></button>
                </div>
            </div>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <div class="fill-block fill-block-warning">
                <div class="header">
                    Información
                </div>
                <div class="body">
                    <p>
                        Es posible que no pueda eliminar esta acción, si esta asociado a un role de usuario, de ser asi por favor contacte
                        al administrador.
                    </p>
                </div>
                <div class="footer">
                    Eliminar
                </div>
            </div>
        </div>
    </div>
</script>
