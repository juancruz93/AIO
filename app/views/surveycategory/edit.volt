<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="title">
      Editar categoría <i>{{"{{data.name}}"}}</i>
    </div>            
    <hr class="basic-line">
    <p class="text-justify">
      Las categorías de encuestas le ayudarán a organizar de manera práctica los registros de las encuestas.
    </p>
  </div>
</div>

<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">
    <form data-ng-submit="edit()">
      <div class="block block-info">
        <div class="body form-horizontal">
          <div class="form-group">
            <label for="name" class="col-sm-2 control-label">{{form.label("name")}}</label>
            <div class="col-sm-10">
              {{form.render("name")}}
              <div class="text-right" data-ng-class="data.name.length > 40 ? 'negative':''">{{"{{data.name.length > 0 ?  data.name.length+'/40':''}}"}}</div>
            </div>
          </div>
          <div class="form-group">
            <label for="description" class="col-sm-2 control-label">{{form.label("description")}}</label>
            <div class="col-sm-10">
              {{form.render("description")}}
              <div class="text-right" data-ng-class="data.description.length > 200 ? 'negative':''">{{"{{data.description.length > 0 ?  data.description.length+'/200':''}}"}}</div>
            </div>
          </div>
          <div class="form-group">
            <label for="status" class="col-sm-2 control-label">{{form.label("status")}}</label>
            <div class="col-sm-10">
              <md-switch class="md-warn none-margin" md-no-ink aria-label="Switch No Ink" data-ng-model="data.status">{{"{{data.status ? 'Activo':'Inactivo'}}"}}</md-switch>
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

  <<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">
    <div class="fill-block fill-block-primary">
      <div class="header">
        Instrucciones
      </div>
      <div class="body">
        <p>Recuerde tener en cuenta estas recomendaciones</p>
        <ul>
          <li>Los campos con asterisco(*) son obligatorios.</li>
          <li><p>El nombre debe tener mínimo 2 y máximo 40 caracteres</p></li>
          <li>
            <p>La descripción debe tener mínimo 2 y máximo 200 caracteres</p>
          </li>
          <li>
            <p>El estado por defecto será activo</p>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>