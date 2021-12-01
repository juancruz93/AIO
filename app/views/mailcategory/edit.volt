<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="title">
      Editar la categoria {{'{{mailCategory.name}}'}}
    </div>            
    <hr class="basic-line" />
    <p class="text-justify">
      Las categorías de correo le ayudarán a organizar de manera práctica los registros de los correos.
    </p>
  </div>
</div>
<div class="clearfix"></div>
<div class="row">
  <form name="contactlistForm" class="form-horizontal" role="form" ng-submit="editCategory()" >
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">
      <div class="block block-info">
        <div class="body">
          <div class="form-group">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
              <label class="col-sm-2 text-right">{{form.label('name')}}</label>
              <span class="col-sm-10">
                {{form.render('name',{'class': 'undeline-input' })}}
              </span>
            </div>
          </div>
          <div class="form-group">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
              <label  class="col-sm-2 text-right">{{form.label('description')}}</label>
              <span class="col-sm-10">
                {{form.render('description',{'class': 'undeline-input' })}}
              </span>
            </div>
          </div>
          <div class="form-group">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
              <label for="status" class="col-sm-2 text-right">{{form.label("status")}}</label>
              <div class="col-sm-10">
                <md-switch class="md-warn none-margin" md-no-ink aria-label="Switch No Ink" ng-model="saveData.status">{{"{{saveData.status ? 'Activo':'Inactivo'}}"}}</md-switch>
              </div>
            </div>
          </div>
        </div>
        <div class="footer" align="right">
          <button type="submit" class="button shining btn btn-xs-round shining shining-round round-button success-inverted" data-toggle="tooltip" data-placement="top" title="Guardar">
            <span class="glyphicon glyphicon-ok"></span>
          </button>
          <a href="#/" class="button shining btn btn-xs-round shining shining-round round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Cancelar">
            <span class="glyphicon glyphicon-remove"></span>
          </a>

        </div>
      </div>
    </div>
  </form>

  <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">       
    <div class="fill-block fill-block-primary" >
      <div class="header">
        Información
      </div>
      <div class="body">
        <p>
          Recuerde tener en cuenta estas recomendaciones:
        <ul>                            
          <li>El nombre debe tener mínimo 2 y máximo 80 caracteres</li>
          <li>El nombre de la categoria debe ser un nombre único, es decir, no pueden existir dos en la bases de datos con el mismo nombre.</li>
          <li>La descripción debe tener máximo 400 caracteres</li>
          <li>Recuerde que los campos con asterisco(*) son oblogatorios</li>
        </ul> 
        </p>
      </div>
    </div> 
  </div>
