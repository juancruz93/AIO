<hr/>

<style>
    .width-50{
        width: 50px;
    }
    .dialog-inner {
        max-height: calc(100vh - 210px);
        overflow-y: auto;
    }

</style>
<div class="block block-primary" ng-init="global.getSurvey()">
    <div class="body">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label class="small-text margin-top-15px"><em>Resumen de la encuesta</em></label>
                <hr class="hr-classic">
                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 padding-top-15px border-right-black">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                                <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
                                    <label >Nombre:</label>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-10 col-lg-10">
                                    <span class="input hoshi col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding" ng-class="{positive: global.infoSurvey.name, negative: !global.infoSurvey.name}">
                                        <em>{{ '{{ !global.infoSurvey.name ? "El nombre no debe estar vacío." : "" }}' }}</em>
                                        {{ '{{ global.infoSurvey.name }}'}}  <span ng-class="{'fa fa-check-circle': global.infoSurvey.name,  'fa fa-times-circle': !global.infoSurvey.name}"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                                <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
                                    <label >Mensaje final:</label>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-10 col-lg-10">
                                    <span class="input hoshi col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding" ng-class="{positive: global.infoSurvey.messageFinal, negative: !global.infoSurvey.messageFinal}">
                                        <em>{{ '{{ !global.infoSurvey.messageFinal ? "El nombre no debe estar vacío." : "" }}' }}</em>
                                        {{ '{{ global.infoSurvey.messageFinal }}'}}  <span ng-class="{'fa fa-check-circle': global.infoSurvey.messageFinal,  'fa fa-times-circle': !global.infoSurvey.messageFinal}"></span>
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
                                    <span class="input hoshi col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding" ng-class="{positive: global.infoSurvey.description, negative: !global.infoSurvey.description}">
                                        <em>{{ '{{ !global.infoSurvey.description ? "La descripción no debe estar vacío." : "" }}' }}</em>
                                        {{ '{{ global.infoSurvey.description }}'}}  <span ng-class="{'fa fa-check-circle': global.infoSurvey.description,  'fa fa-times-circle': !global.infoSurvey.description}"></span>
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
                                    <span class="input hoshi col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding" ng-class="{positive: global.infoSurvey.category.name, negative: !global.infoSurvey.category.name}">
                                        <em>{{ '{{ !global.infoSurvey.category.name ? "La categoria no debe estar vacío." : "" }}' }}</em>
                                        {{ '{{ global.infoSurvey.category.name }}'}}  <span ng-class="{'fa fa-check-circle': global.infoSurvey.category.name,  'fa fa-times-circle': !global.infoSurvey.category.name}"></span>
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
                                    <label >Fecha inicio de encuesta:</label>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
                                    <span class="input hoshi col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding" ng-class="{positive: global.infoSurvey.startDate, negative: !global.infoSurvey.startDate}">
                                        <em>{{ '{{ !global.infoSurvey.startDate ? "La fecha de inicio de encuesta no debe estar vacío." : "" }}' }}</em>
                                        {{ '{{ global.infoSurvey.startDate }}'}}  <span ng-class="{'fa fa-check-circle': global.infoSurvey.startDate,  'fa fa-times-circle': !global.infoSurvey.startDate}"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                                    <label >Fecha final de la encuesta:</label>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9">
                                    <span class="input hoshi col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding" ng-class="{positive: global.infoSurvey.endDate, negative: !global.infoSurvey.endDate}">
                                        <em>{{ '{{ !global.infoSurvey.endDate ? "La fecha de final de encuesta no debe estar vacío." : "" }}' }}</em>
                                        {{ '{{ global.infoSurvey.endDate }}'}}  <span ng-class="{'fa fa-check-circle': global.infoSurvey.endDate,  'fa fa-times-circle': !global.infoSurvey.endDate}"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                                <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
                                    <button class="btn btn-primary" ng-click="global.previsualizar()" ng-if="global.infoSurvey.content">Previsualizar encuesta</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <p class="text-3em text-center">¿Cómo compartir tu encuesta?</p>
        <div class="row-eq-height wrap ">
            {#      <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2"></div>#}
            <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4 fill-block fill-block-primary margin-10px" >
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding" >
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding">
                        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 none-padding">
                            <img class="width-50"ng-src="{{'{{global.imgEmail}}'}}" />
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9 none-padding">
                            <a class="medium-text cursor-pointer" ng-click="modal.open(1)">Por email</a>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding padding-top-15px">
                        Crea invitaciones personalizadas de correo electrónico y mantén un registro de quiénes responden.
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4 fill-block fill-block-primary margin-10px" >
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding">
                        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 none-padding">
                            <img class="width-50"ng-src="{{'{{global.imgFb}}'}}" />
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9 none-padding">
                            <a class="medium-text cursor-pointer" ng-click="modal.open(2)">Publicación en redes sociales</a>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding padding-top-15px">
                        Publica tu encuesta en Facebook.
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4 fill-block fill-block-primary margin-10px"  >
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding">
                        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 none-padding">
                            <img class="width-50"ng-src="{{'{{global.imgLink}}'}}" />
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9 none-padding">
                            <a class="medium-text cursor-pointer" ng-click="modal.open(3)">Por un enlace web</a>
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
        <a href="{{ url('survey') }}"
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
                            <form  ng-submit="global.validateSurvey()"ng-style="{'background-color':global.infoSurvey.content.backgroundForm}">
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

