<div class="clearfix"></div>
<div class="space"></div>
<div>

  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
      <div class="title">
        Crear nuevo formulario
      </div>
      <hr class="basic-line"/>
    </div>
  </div>

  <div id="form-container" >
    <div class="text-center">
      <div id="status-buttons" class="" >
        <a ui-sref="create.describe({id:idForm})" ng-class="{'active': state.includes('create.describe')}" >
          <span>1</span> <b>Información básica</b></a>

        <a  ui-sref="create.forms({id:idForm})" ng-class="{'active': state.includes('create.forms')}">
          <span>2</span> <b>Formulario</b>
        </a>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
      <ui-view></ui-view>
    </div>
  </div>  
</div>
