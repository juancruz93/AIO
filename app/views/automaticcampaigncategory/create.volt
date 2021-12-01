<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="title">
      Nueva categoría de campaña de automatización
    </div>            
    <hr class="basic-line">
    <p class="text-justify">
      Las categorías de campañas automáticas le ayudarán a organizar de manera práctica los registros de las campañas automáticas
    </p>
  </div>
</div>

<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">
    <form name="campcampcateg" data-ng-submit="save()">
      <div class="block block-info">
        <div class="body row">
          <div class="col-md-12">
            <div class="body form-horizontal">
              <div class="form-group">
                <label for="name" class="col-sm-2 control-label">*Nombre</label>
                <div class="col-sm-10">
                  {{form.render('name')}}
                  <div class="text-right" data-ng-class="data.name.length > 45 ? 'negative':''">{{"{{data.name.length > 0 ?  data.name.length+'/45':''}}"}}</div>
                </div>
              </div>
              <div class="form-group">
                <label for="description" class="col-sm-2 control-label">Descripción</label>
                <div class="col-sm-10">
                  {{form.render('description')}}
                  <div class="text-right" data-ng-class="data.description.length > 200 ? 'negative':''">{{"{{data.description.length > 0 ?  data.description.length+'/200':''}}"}}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="footer text-right">
          <button type="submit" class="button shining btn btn-xs-round shining shining-round round-button success-inverted" data-toggle="tooltip" data-placement="top" title="Guardar">
            <span class="glyphicon glyphicon-ok"></span>
          </button>
          <a href="{{url('automaticcampaigncategory#/')}}" class="button shining btn btn-xs-round shining shining-round round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Cancelar">
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
          <li>El nombre debe tener mínimo 2 y máximo 80 caracteres</li>
          <li>El nombre de la categoría debe ser un nombre único, es decir, no pueden existir dos iguales en la base de datos con el mismo nombre.</li>
          <li>La descripción debe tener máximo 200 caracteres</li>
          <li>Recuerde que los campos con asterisco(*) son obligatorios</li>
        </ul> 
        </p>
      </div>
    </div> 
  </div> 
</div>