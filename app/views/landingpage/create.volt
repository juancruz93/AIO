<div class="clearfix"></div>
<div>
  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
      <div class="title">
        Crear nueva Landing Page
      </div>
      <hr class="basic-line"/>
    </div>
  </div>

  <div id="form-container">
    <div class="text-center">
      <div id="status-buttons" class="">
        <a ui-sref="create.describe({idLandingPage:idLandingPageGet})" ng-class="{'active': route == 'create.describe'}" >
          <span>1</span> <b>Información básica</b></a>
        <a ui-sref="create.content({idLandingPage:idLandingPageGet})" ng-class="{'active': route == 'create.content'}">
          <span>2</span> <b>Diseñar Landing Page</b>
        </a>
        <a ui-sref="create.confirmation({idLandingPage:idLandingPageGet})"  ng-class="{'active': route == 'create.confirmation'}" >
          <span>3</span> <b>Fecha de publicación y visualización</b>
        </a>
        <a ui-sref="create.share({idLandingPage:idLandingPageGet})"  ng-class="{'active': route == 'create.share'} " >
          <span>4</span> <b>Compartir Landing Page</b>
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

