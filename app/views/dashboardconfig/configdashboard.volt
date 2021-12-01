<div class="row" ng-init="universalAction.getConfigDefaultDashboard();universalAction.getImagen()">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="title">
      Configuración Dashboard
    </div>
    <hr class="basic-line">
    <p>
      En esta lista podra ver, crear, editar y eliminar los formularios.
    </p>
  </div>
</div>
<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap ">
    <div class="pull-right">
      <a ng-href="{{"{{urlAccountList}}"}}" class="btn btn-danger">Regresar</a>
      <button class="btn btn-info" ng-click="universalAction.openModalPreview()">Previsualizar</button>
      <button class="btn btn-primary" ng-click="universalAction.saveConfig()" >Guardar</button>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap ">
    <!-- SIDENAV -->
    <section layout="row" flex="">
      <md-sidenav class="md-sidenav-right md-whiteframe-4dp" md-disable-backdrop md-component-id="right">
        <md-toolbar class="md-warn">
          <h1 class="md-toolbar-tools"><span >{{"{{objItemSelected.config.title}}"}}</span></h1>
        </md-toolbar>
        <md-content  layout-padding="">
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="form-group">
              <label>Texto enlace</label>
              <input class="form-control" type="text" ng-model="objItemSelected.config.textEnlace" maxlength="20" minlength="2" />
            </div>
            <div class="form-group">
              <label>Enlace sin servicio</label>
              <input class="form-control" type="url" ng-model="objItemSelected.config.hrefEnlace" />
            </div>
            <div class="form-group">
              <label>Enlace nuevo servicio</label>
              <input class="form-control" type="url" ng-model="objItemSelected.config.hrefEnlaceNewServices" />
            </div>
            <div class="form-group">
              {#<input class="form-control" type="url" ng-model="objItemSelected.config.hrefEnlace" />#}
              <button class="btn btn-primary" ng-click="universalAction.openModalImage(objItemSelected.index,$event)">Cambiar icono {#<i class="fa fa-file-image-o" aria-hidden="true"></i>#}</button>
            </div>
            <div class="form-group pull-right">
              <button ng-click="sideNavOptions.forDefault()" class="btn btn-info btn-sm">
                Descartar Cambios
              </button>
              <button ng-click="sideNavOptions.saveConfig()" class="btn btn-primary btn-sm">
                Guardar
              </button>
              <button ng-click="sideNavOptions.close()" class="btn btn-danger btn-sm">
                Cerrar
              </button>
            </div>
          </div>
        </md-content>
      </md-sidenav>
    </section>
    <!-- DRAGGABLE -->
    <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 heigth-droppable text-center" data-drop="true" ng-model='arrServices' data-jqyoui-options="{}"  jqyoui-droppable="{}">
      {#          <button class="btn btn-primary" ng-click="check()">Remove</button>#}
      <!-- SERVICIOS -->
      <div class="panel panel-sigma">
        <div class="panel-heading">Servicios</div>
        <div class="panel-body">
          <div class="item-collap cursor-move" data-ng-repeat="item in arrServices track by $index" data-drag="{{'{{item.drag}}'}}" data-jqyoui-options="{revert: 'invalid'}" ng-model="arrServices" jqyoui-draggable="{index: {{'{{$index}}'}},placeholder:true,animate:true}" ng-hide="!item.title">
            <div class="item-collap-icon">
              <img  ng-src="{{url('images/icons-dashboard/white/{{item.icon}}')}}" class="img-responsive center-block"/>
            </div>
            <div class="item-collap-title">
              {{"{{item.title}}"}}
            </div>
          </div>
        </div>
      </div>
      <!-- Configuracion -->      
      <div class="panel panel-sigma">
        <div class="panel-heading">Configuración</div>
        <div class="panel-body">
          <div class="item-collap cursor-pointer" ng-click="universalAction.openModalAdj($event)">
            <div class="item-collap-icon ">
              <img  ng-src="{{url('images/icons-dashboard/white/automatization.png')}}" class="img-responsive center-block"/>
            </div>
            <div class="item-collap-title">
              Subir imagen
            </div>
          </div>
          <div class="item-collap cursor-pointer" ng-click="universalAction.openModalImage('top',$event)">
            <div class="item-collap-icon ">
              <img  ng-src="{{url('images/icons-dashboard/white/automatization.png')}}" class="img-responsive center-block"/>
            </div>
            <div class="item-collap-title">
              Imagen AIO
            </div>
          </div>
          <div class="item-collap cursor-pointer" ng-click="universalAction.openModalImage('bottom',$event)">
            <div class="item-collap-icon ">
              <img  ng-src="{{url('images/icons-dashboard/white/automatization.png')}}" class="img-responsive center-block"/>
            </div>
            <div class="item-collap-title">
              imagen SIGMA
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- DROPPABLE -->
    <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9 boder-droppable heigth-droppable " id="droppable" data-drop="true" ng-model="arrConfigDashboard" data-jqyoui-options="optionDroppable" jqyoui-droppable="{multiple:true,onOver:'actionDroppable.onOver',onDrop:'actionDroppable.onDrop',onOut:'actionDroppable.onDrop'}">
      <div class="padding-top-15px equal" html-sortable ng-model="arrConfigDashboard">
        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 item-sortable"  ng-repeat="item in arrConfigDashboard track by $index">
          <div class="row padding-right-10px ">
            <div class="pull-right "><i class="fa fa-cog pointer-cursor" ng-click="sideNavOptions.open($index)"  aria-hidden="true"></i> <i class="fa fa-times pointer-cursor " ng-click="actionDroppable.remove($index)" aria-hidden="true"></i></div>
          </div>
          <div class="dashboard-item-image-center center-block " >
            <img class="img-static " ng-src="{{'{{item.imageDashboard}}'}}" alt="Crear lista de contactos" >
          </div>
          <div class="dashboard-item-title-center ">
            <a href="" class="extra-small-text">{{"{{item.textEnlace}}"}}</a>
          </div>
        </div>
      </div>
      <!-- TEXT DROPPABLE -->    
      {#      <p class="text-droppable text-3em">¡ARRASTRE AQUI!</p>#}
    </div>
  </div>
</div>
