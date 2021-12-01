<script type="text/x-handlebars" data-template-name="roles/index">
   <div>
    <div style="float:right;">
      {{'{{#link-to "roles.add"}}'}}
          <button class="button shining btn btn-md success-inverted">Crear nuevo role</button>
      {{'{{/link-to}}'}}
    </div>
   <h2>Roles de usuario</h2>
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
                  {{'{{#each role in model}}'}}
                      <tr>
                          <td><strong>{{ '{{role.id}}' }} - {{'{{role.name}}'}}</strong></td>
                          <td>{{'{{role.created}}'}}</td>
                          <td>{{'{{role.updated}}'}}</td>
                          <td class="text-right">
                               <button class="button shining btn btn-xs-round shining shining-round round-button default-inverted tool-tip" data-placement="top" title="Agregar permisos a este role" {{'{{action "configure" role}}'}}>
                                    <span class="glyphicon glyphicon-flash"></span>
                               </button>
                         
                              {{'{{#link-to "roles.update" role.id class="button shining btn btn-xs-round shining shining-round round-button info-inverted tool-tip" data-placement="top" title="Editar este role"}}'}}
                                  <span class="glyphicon glyphicon-edit"></span>
                              {{'{{/link-to}}'}}
                         
                              {{'{{#link-to "roles.remove" role.id class="button shining btn btn-xs-round shining shining-round round-button danger-inverted tool-tip" data-placement="top" title="Eliminar este role"}}'}}
                                  <span class="glyphicon glyphicon-trash"></span>
                              {{'{{/link-to}}'}}
                          </td>
                      </tr>
                  {{'{{/each}}'}}
              </tbody>
          </table>
       </div>
    </div>
</script>

<script type="text/x-handlebars" data-template-name="roles/add">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <h2>Crear un nuevo <strong>role de usuario</strong></h2>
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
                                {{'{{input name="name" type="text" valueBinding="name" required="required" autofocus="autofocus" class="ember-textfield"}}'}}
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
                        Debe ingresar un nombre para el nuevo role, este no debe contener espacios, ni caracteres especiales, y debe tener entre 4 y 40 caracteres.
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

<script type="text/x-handlebars" data-template-name="roles/update">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <h2>Editar el role de usuario <strong><em>{{ '{{name}}' }}</em></strong></h2>
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
                                {{'{{input name="name" type="text" valueBinding="name" placeholder="Nombre del role" required="required" autofocus="autofocus" class="ember-textfield"}}'}}
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
                        Debe ingresar un nombre para el role de usuario, este no debe contener espacios, ni caracteres especiales, y debe tener entre 4 y 40 caracteres.
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

<script type="text/x-handlebars" data-template-name="roles/remove">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <h2>Eliminar role de usuario <em><strong>{{'{{name}}'}}</strong></em></h2>
        </div>
    </div>
    
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <div class="block block-warning">
                <div class="body">
                    <div class="alert alert-warning" role="alert">
                        ¿Está seguro que desea eliminar este role de usuario?
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
                        Es posible que no pueda eliminar el role, si esta asociado a recursos y acciones de la plataforma, de ser asi por favor contacte
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
