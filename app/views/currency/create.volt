<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="title">
      Nuevo tipo de moneda
    </div>            
    <hr class="basic-line">
    <p class="text-justify">
      Formulario para cre un tipo de moneda
    </p>
  </div>
</div>

<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">
    <form data-ng-submit="saveCurrency()">
      <div class="block block-info">
        <div class="body row">
          <div class="col-md-12">
            <div class="body form-horizontal">
              <div class="form-group">
                <label for="name" class="col-sm-3 control-label">{{form.label('name')}}</label>
                <div class="col-sm-9">
                  {{form.render('name')}}
                  <div class="text-right" data-ng-class="data.name.length > 35 ? 'negative':''">{{"{{data.name.length > 0 ?  data.name.length+'/35':''}}"}}</div>
                </div>
              </div>
              <div class="form-group">
                <label for="shortName" class="col-sm-3 control-label">{{form.label('shortName')}}</label>
                <div class="col-sm-9">
                  {{form.render('shortName')}}
                  <div class="text-right" data-ng-class="data.description.length > 3 ? 'negative':''">{{"{{data.shortName.length > 0 ?  data.shortName.length+'/3':''}}"}}</div>
                </div>
              </div>
              <div class="form-group">
                <label for="symbol" class="col-sm-3 control-label">{{form.label('symbol')}}</label>
                <div class="col-sm-9">
                  {{form.render('symbol')}}
                  <div class="text-right" data-ng-class="data.symbol.length > 1 ? 'negative':''">{{"{{data.symbol.length > 0 ?  data.symbol.length+'/1':''}}"}}</div>
                </div>
              </div>
              <div class="form-group">
                <label for="status" class="col-sm-3 control-label">*Estado</label>
                <div class="col-sm-9">
                  <md-switch class="md-warn none-margin" md-no-ink aria-label="Switch No Ink" ng-model="data.status">
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
          <a href="{{url('currency#/')}}" class="button shining btn btn-xs-round shining shining-round round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Cancelar">
            <i class="fa fa-times"></i>
          </a>
        </div>
      </div>
    </form>
  </div>
  <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">                            
    <div class="fill-block fill-block-info">
      <div class="header">
        Informaci??n
      </div>
      <div class="body">
        <p>Recuerde tener en cuenta estas recomendaciones:</p>
        <ul>                            
          <li><p>El nombre del tipo de la moneda y la abreviatura deben ser ??nicos y deben corresponder seg??n el est??ndar internacional ISO 4217.</p></li>
          <li><p>El nombre debe tener al menos 2 y m??ximo 35 caracteres.</p></li>
          <li><p>La abreviatura debe tener exactamente 3 caracteres seg??n el est??ndar (ISO 4217), estos deben estar en may??scula, para ello el sistema lo hace autom??ticamente.</p></li>
        </ul> 
        <p></p>
      </div>
      <div class="footer">
        Creaci??n
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
