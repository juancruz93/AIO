<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="title">
      {{"{{data.name}}"}}
    </div>            
    <hr class="basic-line">
    <div class="small-text">
      {{"{{data.country + ' (+' + data.indicative + ')'}}"}}
    </div>
    <p>
      {{"{{data.description}}"}}
    </p>            
  </div>
</div>

<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap text-right">
    <a ui-sref="index" class="button btn btn-md default-inverted"><i class="fa fa-arrow-left" aria-hidden="true"></i> Regresar al listado de reglas</a>
  </div>
</div>

<div class="row">
  <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 wrap" data-ng-repeat="i in data.config">
    <div class="fill-block fill-block-primary">
      <div class="header">
        <span style="font-size: 1.7em">Configuración {{"{{$index+1}}"}}</span>
      </div>
      <div class="body">
        <table class="table table-striped">
          <tr>
            <th>Adaptador</th>
            <td>{{"{{i.adapter | capitalize}}"}}</td>
          </tr>
          <tr>
            <th>Canal por defecto</th>
            <td>{{"{{i.byDefault == 1 ? 'Sí':'No'}}"}}</td>
          </tr>
          <tr>
            <th>Prefijos:</th>
            <td>
              {{"{{i.prefix | implode : ','}}"}}
            </td>
          </tr>
        </table>
      </div>
    </div>
  </div>
</div>