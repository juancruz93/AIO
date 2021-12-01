<script type="text/x-handlebars" data-template-name="resources/index">
  <div>
    <div style="float:right;">
      {{'{{#link-to "resources.add"}}'}}
          <button class="button shining btn btn-md success-inverted">Crear nuevo recurso</button>
      {{'{{/link-to}}'}}
    </div>
   <h2>Recursos del sistema</h2>
   </div>
  <div class="row">
     <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <table class="table table-bordered">
             <thead class="theader">
                <tr>
                    <th>Nombre</th>
                    <th>Creado</th>
                    <th>Actualizado</th>
                    <th></th>
                </tr>
              </thead>
            <tbody>
                {{'{{#each resource in model}}'}}
                    <tr>
                        <td><strong>{{'{{resource.id}}'}} - {{'{{resource.name}}'}}</strong></td>
                        <td>{{'{{resource.created}}'}}</td>
                        <td>{{'{{resource.updated}}'}}</td>
                        <td class="text-right">
                            <button type="button" class="button shining btn btn-xs-round shining shining-round round-button warning-inverted tool-tip" data-toggle="collapse" data-target="#detail{{'{{unbound resource.id}}'}}" data-placement="top" title="Ver acciones de este recurso" aria-expanded="false" aria-controls="detail{{'{{unbound resource.id}}'}}">
                              <span class="glyphicon glyphicon-list"></span>
                            </button>
                        
                            <button {{ '{{action "addaction" resource }}' }} class="button shining btn btn-xs-round shining shining-round round-button primary-inverted tool-tip" data-placement="top" title="Añadir acción a este recurso">
                                <span class="glyphicon glyphicon-transfer"></span>
                            </button>
                        
                            {{'{{#link-to "resources.update" resource.id class="button shining btn btn-xs-round shining shining-round round-button info-inverted tool-tip" data-placement="top" title="Editar este recurso"}}'}}
                                <span class="glyphicon glyphicon-edit"></span>
                            {{'{{/link-to}}'}}
                       
                            {{'{{#link-to "resources.remove" resource.id class="button shining btn btn-xs-round shining shining-round round-button danger-inverted tool-tip" data-placement="top" title="Borrar este recurso"}}'}}
                                <span class="glyphicon glyphicon-trash"></span>
                            {{'{{/link-to}}'}}
                        </td>
                    </tr>
                    <tr id="detail{{'{{unbound resource.id}}'}}" class="collapse">
                        <td colspan="7">
                            <div class="row">
                                <div class="col-xs-12 col-sm-10 col-sm-offset-1 col-md-6 col-md-offset-3 col-lg-6 col-lg-offset-3">
                                    <div class="block block-info">
                                        <div class="body">
                                            <h4>Acciones de <strong><em>{{'{{resource.name}}'}}</em></strong></h4>
                                            <table class="table table-bordered">
                                                <thead></thead>
                                                <tbody>
                                                    {{'{{#each action in resource.actions}}'}} 
                                                        <tr>
                                                            <td>
                                                                {{'{{action.id}}'}}
                                                            </td>
                                                            <td>
                                                                <strong>{{ '{{action.name}}' }}</strong>
                                                            </td>
                                                            <td class="text-right">
                                                                {{ '{{#link-to "actions.update" action.id class="button shining btn btn-xs-round shining shining-round round-button info-inverted tool-tip"}}' }}
                                                                    <span class="glyphicon glyphicon glyphicon-pencil"></span>
                                                                {{'{{/link-to}}'}}
                                                           
                                                                {{ '{{#link-to "actions.remove" action.id class="button shining btn btn-xs-round shining shining-round round-button danger-inverted tool-tip"}}' }}
                                                                    <span class="glyphicon glyphicon-trash"></span>
                                                                {{'{{/link-to}}'}}
                                                            </td>
                                                        </tr>
                                                  {{ '{{else}}' }}
                                                        <tr>     
                                                            <td colspan="4">
                                                                 No hay acciones creadas en este recurso, para crear una haga <span {{ '{{action "addaction" resource}}' }} style="cursor:pointer;">clic aqui</span>
                                                            </td>
                                                        </tr>
                                                  {{ '{{/each}}' }}
                                                </tbody>
                                            </table>	
                                        </div>
                                    </div>   
                                </div>
                            </div>
                        </td>
                    </tr>
                {{'{{/each}}'}}
            </tbody>
        </table>
     </div>
  </div>
</script>

<script type="text/x-handlebars" data-template-name="resources/add">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <h2>Registrar un nuevo <strong>Recurso del sistema</strong></h2>
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
                                {{'{{input name="name" type="text" valueBinding="name" placeholder="Nombre del recurso" required="required" autofocus="autofocus" class="undeline-input form-control"}}'}}
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
                        Debe ingresar un nombre para el nuevo recurso, este no debe contener espacios, ni caracteres especiales, y debe tener entre 2 y 40 caracteres.
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

<script type="text/x-handlebars" data-template-name="resources/update">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <h2>Editar recurso del sistema <em><strong>{{ '{{name}}' }}</strong></em></h2>
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
                        Debe ingresar un nombre para el nuevo recurso, este no debe contener espacios, ni caracteres especiales, y debe tener entre 2 y 40 caracteres.
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

<script type="text/x-handlebars" data-template-name="resources/remove">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <h2>Eliminar recurso del sistema <em><strong>{{'{{name}}'}}</strong></em></h2>
        </div>
    </div>
    
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <div class="block block-warning">
                <div class="body">
                    <div class="alert alert-warning" role="alert">
                        ¿Está seguro que desea eliminar este recurso del sistema?
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
                        Es posible que no pueda eliminar el recurso del sistema, porque podría estar asociado a acciones. Si es así por favor contacte al administrador
                    </p>
                </div>
                <div class="footer">
                    Eliminar
                </div>
            </div>
        </div>
    </div>
</script>
