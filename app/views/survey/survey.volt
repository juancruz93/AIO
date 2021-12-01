<hr/>
<div  class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 none-paddign">
        <button class="button btn success-inverted pull-right" ng-click="AddSurveyContent()">Guardar y continuar</button>
    </div>
</div>

<div class="fill-block fill-block-primary">
    <div class="body">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                    <div class="block bg-color">
                        <div class="header" role="tab" id="headingOne">
                            <h4 class="panel-title">
                                <a class="link-collapse disp-block"  role="button" data-toggle="collapse" data-parent="#accordion" data-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                    <i class="fa fa-caret-right color-text"></i><span class="color-text"> Generador</span>
                                </a>
                            </h4>
                        </div>
                        <div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
                            <div class="body wrap" id="style-2" style="background-color:#dddddd; max-height: 300px; overflow-y: scroll;">
                                <div data-ng-repeat="item in listItemsCollapse track by $index" ng-hide="item.hide" data-ng-mouseenter="viewBtnAddEnter($index,0)" data-ng-mouseleave="viewBtnAddLeave($index,0)" >
                                    <div class="item-collap"  >
                                        <div fb-component="item.component">
                                            <div class="item-collap-icon cursor-move bg-color">
                                                <img  ondragstart="return false" data-ng-src="{{url('images/icons-surveys/{{item.icon}}')}}" class="center-block"/>
                                            </div>
                                            <div class="item-collap-title cursor-move">
                                                {{"{{item.title}}"}}
                                            </div>
                                        </div>
                                        <div class="item-collap-btn cursor-move">
                                            <button type="button" class="btn btn-xs default-inverted pull-right cursor-pointer" data-ng-mouseenter="item.addManual = true" data-ng-mouseleave="item.addManual = false" data-ng-show="item.viewAdd" ng-click="addComponent($index,$event)"><i class="fa fa-plus"></i> Agregar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="block bg-color">
                        <div class="header " role="tab" id="headingTwo">
                            <h4 class="panel-title">
                                <a class="link-collapse disp-block" role="button" data-toggle="collapse" data-parent="#accordion" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                    <i class="fa fa-caret-right color-text"></i> <span class="color-text"> Configuración visual</span>
                                </a>
                            </h4>
                        </div>
                        <div id="collapseTwo" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingTwo">
                            <div class="body wrap" id="style-2" style="background-color:#dddddd; max-height: 300px; overflow-y: scroll;">
                                <div class="item-collap" data-ng-repeat="item in listItemsConfigVisual track by $index" data-ng-mouseenter="viewBtnAddEnter($index,1)" data-ng-mouseleave="viewBtnAddLeave($index,1)">
                                    <div class="item-collap-icon bg-color">
                                        <img ondragstart="return false" data-ng-src="{{url('images/icons-surveys/{{item.icon}}')}}" class="center-block"/>
                                    </div>
                                    <div class="item-collap-title bg">
                                        {{"{{item.title}}"}}
                                        <button type="button" class="btn btn-xs default-inverted pull-right" ng-click="toggleRight('right',$index)" data-ng-show="item.viewAdd"><i class="fa fa-pencil"></i> Cambiar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="block bg-color">
                        <div class="header" role="tab" id="headingThree">
                            <h4 class="panel-title">
                                <a class="link-collapse disp-block color-text" role="button" data-toggle="collapse" data-parent="#accordion" data-target="#collapseThree" aria-expanded="false" aria-controls="headingThree">
                                    <i class="fa fa-caret-right color-text"></i><span class="color-text"> Encabezado</span>
                                </a>
                            </h4>
                        </div>
                        <div id="collapseThree" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingThree">
                            <div class="body wrap " id="style-2" style="background-color:#dddddd; max-height: 300px; overflow-y: scroll;">
                                <div class="item-collap" data-ng-repeat="item in listItemsConfigEncabezado track by $index" data-ng-mouseenter="viewBtnAddEnter($index,2)" data-ng-mouseleave="viewBtnAddLeave($index,2)">
                                    <div class="item-collap-icon bg-color">
                                        <img ondragstart="return false" data-ng-src="{{url('images/icons-surveys/{{item.icon}}')}}" class="center-block"/>
                                    </div>
                                    <div class="item-collap-title">
                                        {{"{{item.title}}"}}
                                        <button type="button" class="btn btn-xs default-inverted pull-right" ng-click="selectionTittle($index)" data-ng-show="item.viewAdd"><i class="fa fa-pencil"></i> Seleccionar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="block bg-color">
                        <div class="header" role="tab" id="headingFourth">
                            <h4 class="panel-title">
                                <a class="link-collapse disp-block " role="button" data-toggle="collapse" data-parent="#accordion" data-target="#collapseFourth" aria-expanded="false" aria-controls="headingFourth">
                                    <i class="fa fa-caret-right color-text"></i><span class="color-text">Pie de pagina</span> 
                                </a>
                            </h4>
                        </div>
                        <div id="collapseFourth" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="collapseFourth">
                            <div class="body wrap" id="style-2" style="background-color:#dddddd; max-height: 300px; overflow-y: scroll;">
                                <div class="item-collap " data-ng-repeat="item in listItemsConfigFooter track by $index" data-ng-mouseenter="viewBtnAddEnter($index,3)" data-ng-mouseleave="viewBtnAddLeave($index,3)">
                                    <div class="item-collap-icon bg-color">
                                        <img ondragstart="return false" data-ng-src="{{url('images/icons-surveys/{{item.icon}}')}}" class="center-block"/>
                                    </div>
                                    <div class="item-collap-title">
                                        {{"{{item.title}}"}}
                                        <button type="button" class="btn btn-xs default-inverted pull-right" ng-click="selectionFooter($index)" data-ng-show="item.viewAdd"><i class="fa fa-pencil"></i> Seleccionar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9" ng-style="{'background-color':backgroundForm}">
                <div ng-if="formReady">
                    <form fb-builder="default" fb-align-popover="'bottom'"></form>
                </div>
                <section layout="row" flex="">
                    <md-sidenav class="md-sidenav-right md-whiteframe-4dp" md-component-id="right">

                        <md-toolbar class="md-warn" {#style="background-color: #ff6e00"#}>
                            <h1 class="md-toolbar-tools"><span data-ng-bind="titleConf"></span></h1>
                        </md-toolbar>
                        <md-content  layout-padding="">
                            <div bind-html-compile="htmlConf"></div>
                            <md-button ng-click="close()" class="md-warn md-raised md-mini">
                                Cerrar
                            </md-button>
                        </md-content>
                    </md-sidenav>
                </section>
            </div>
        </div>
    </div>
</div>

<div class="footer" >
    <div  class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 none-paddign">
            <button class="button btn success-inverted pull-right" ng-click="AddSurveyContent()">Guardar y continuar</button>
        </div>
    </div>
</div>

<script>
    (function () {
      $('.collapse').collapse('show');
    })();
</script>