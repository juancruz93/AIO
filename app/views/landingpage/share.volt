<style>
  .width-50{
    width: 50px;
  }
  .dialog-inner {
    max-height: calc(100vh - 210px);
    overflow-y: auto;
  }
</style>
<div class="block block-primary" ng-init="resServices.getLandingPage()">
  <div class="body">
    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
        <label class="small-text margin-top-15px"><em>Resumen de la Landing Page</em></label>
        <hr class="hr-classic">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 padding-top-15px border-right-black">
          <div class="form-group">
            <div class="row">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
                  <label >Nombre:</label>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-10 col-lg-10">
                  <span class="input hoshi col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding" ng-class="{positive: data.infoLandingPage.name, negative: !data.infoLandingPage.name}">
                    <em>{{ '{{ !data.infoLandingPage.name ? "El nombre no debe estar vacío." : "" }}' }}</em>
                    {{ '{{ data.infoLandingPage.name }}'}}  <span ng-class="{'fa fa-check-circle': data.infoLandingPage.name,  'fa fa-times-circle': !data.infoLandingPage.name}"></span>
                  </span>
                </div>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="row">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
                  <label >Descripción:</label>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-10 col-lg-10">
                  <span class="input hoshi col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding" ng-class="{positive: data.infoLandingPage.description, negative: !data.infoLandingPage.description}">
                    <em>{{ '{{ !data.infoLandingPage.description ? "La descripción no debe estar vacío." : "" }}' }}</em>
                    {{ '{{ data.infoLandingPage.description }}'}}  <span ng-class="{'fa fa-check-circle': data.infoLandingPage.description,  'fa fa-times-circle': !data.infoLandingPage.description}"></span>
                  </span>
                </div>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="row">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
                  <label >Categoría:</label>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-10 col-lg-10">
                  <span class="input hoshi col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding" ng-class="{positive: data.infoLandingPage.namecategory, negative: !data.infoLandingPage.namecategory}">
                    <em>{{ '{{ !data.infoLandingPage.namecategory ? "La categoria no debe estar vacío." : "" }}' }}</em>
                    {{ '{{ data.infoLandingPage.namecategory }}'}}  <span ng-class="{'fa fa-check-circle': data.infoLandingPage.namecategory,  'fa fa-times-circle': !data.infoLandingPage.namecategory}"></span>
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 padding-top-15px">
          <div class="form-group">
            <div class="row">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                  <label >Fecha de inicio:</label>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
                  <span class="input hoshi col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding" ng-class="{positive: data.infoLandingPage.startDate, negative: !data.infoLandingPage.startDate}">
                    <em>{{ '{{ !data.infoLandingPage.startDate ? "La fecha de inicio de la Landing Page no debe estar vacío." : "" }}' }}</em>
                    {{ '{{ data.infoLandingPage.startDate }}'}}  <span ng-class="{'fa fa-check-circle': data.infoLandingPage.startDate,  'fa fa-times-circle': !data.infoLandingPage.startDate}"></span>
                  </span>
                </div>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="row">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                  <label >Fecha de expiración:</label>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
                  <span class="input hoshi col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding" ng-class="{positive: data.infoLandingPage.endDate, negative: !data.infoLandingPage.endDate}">
                    <em>{{ '{{ !data.infoLandingPage.endDate ? "La fecha de final de la Landing Page no debe estar vacío." : "" }}' }}</em>
                    {{ '{{ data.infoLandingPage.endDate }}'}}  <span ng-class="{'fa fa-check-circle': data.infoLandingPage.endDate,  'fa fa-times-circle': !data.infoLandingPage.endDate}"></span>
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <p class="text-3em text-center">¿Cómo compartir tu Landing Page?</p>
    <div class="row-eq-height wrap ">
      {#      <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2"></div>#}
      <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4 fill-block fill-block-primary margin-10px" >
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding" >
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding">
            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 none-padding">
              <img class="width-50"ng-src="{{'{{misc.imgEmail}}'}}" />
            </div>
            <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9 none-padding">
              <a class="medium-text cursor-pointer" ng-click="functions.open(1)">Por email</a>
            </div>
          </div>
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding padding-top-15px">
            Envía por correo electrónico a tus contactos tu enlace Web.
          </div>
        </div>
      </div>
      <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4 fill-block fill-block-primary margin-10px" >
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding">
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding">
            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 none-padding">
              <img class="width-50"ng-src="{{'{{misc.imgFb}}'}}" />
            </div>
            <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9 none-padding">
              <a class="medium-text cursor-pointer" ng-click="functions.open(2)">Publicación en redes sociales</a>
            </div>
          </div>
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding padding-top-15px">
            Publica tu Landing Page en Facebook.
          </div>
        </div>
      </div>
      <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4 fill-block fill-block-primary margin-10px"  >
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding">
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding">
            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 none-padding">
              <img class="width-50"ng-src="{{'{{misc.imgLink}}'}}" />
            </div>
            <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9 none-padding">
              <a class="medium-text cursor-pointer" ng-click="functions.open(3)">Por un enlace web</a>
            </div>
          </div>
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding padding-top-15px">
            Comparte un enlace web por correo electrónico, publícalo en las redes sociales o en tu sitio web.
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="footer text-right">
    {#    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 text-right col-lg-offset-6 col-md-offset-6">#}
    <a href="{{ url('landingpage') }}"
       class="button btn btn-small danger-inverted"
       data-toggle="tooltip" data-placement="top" title="Cancelar">
      Salir
    </a>
    {#    </div>     #}
  </div>
</div>

<div class="modal fade" id="preview" role="dialog">
  <div class="modal-dialog">

    <div class="modal-content" style="top: 60px;">

      <div class="modal-body "  >
        <md-progress-linear md-mode="indeterminate" ng-hide="previewShow" class="md-warn"></md-progress-linear>
        <div ng-if="previewShow" style="overflow-y: scroll;max-height: 400px;" style="text-align: left !important;">
          <div class="container-fluid">
            <div class="form-group"  >
              <form  ng-submit="global.validateSurvey()"ng-style="{'background-color':data.infoLandingPage.content.backgroundForm}">
                <div ng-model="input"  fb-form="sigmaSurvey" fb-default="defaultValue"></div>
              </form>
            </div>
          </div>
        </div>

      </div>
      <div class="modal-footer">
        <div class="row">
          <div class="form-goup">
            <div class="col-md-3 col-md-offset-4">                
              <button type="button" style="margin-right:7px; width:72px;" class="button shining btn btn-md danger-inverted" data-dismiss="modal">Cerrar</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<fb app-id="{{idfb}}"></fb>




