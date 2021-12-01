<script>
    $(function() {
        $('#toggle-one').bootstrapToggle({
            on: 'On',
            off: 'Off',
            onstyle: 'success',
            offstyle: 'danger',
            size: 'small'
        });
        $(".select2").select2({
            theme: 'classic',
            placeholder: 'Seleccionar',
            minimumResultsForSearch: -1,

        });
        $('.search-select-multiple').select2({
            dropdownAutoWidth: true,
            multiple: true,
            width: '100%',
            height: '30px',

            placeholder: "Select",
            allowClear: true
        });
        $.extend($.fn.select2.defaults, {
            formatSelectionTooBig: function(limit) {

                // Callback

                return 'Too many selected items';
            }
        });

        $("#services").select2({
            maximumSelectionLength: 3
        });
        $('.select2-search__field').css('width', 'auto');
        $('#indicative').select2().on('select2:open', function(e) {
            $('.select2-search__field').attr('placeholder', 'Seleccione');
        })
    });
</script>

<script>
    $.fn.datetimepicker.defaults = {
        maskInput: false,
        pickDate: false,
        pickTime: true,
        startDate: new Date()
    };

    var idAccount = {{idAccount}};
    if(idAccount == 49 || idAccount == 1387){
        $("#OA").css({"display":"block"});
    } else{
        $("#OA").css({"display":"none"});
    }   

    function htmlPreview(idAutoresponder) {
        $.post("{{url('autoresponder/preview')}}/" + idAutoresponder, function(preview) {
            var e = preview.preview;
            $('<iframe id="frame" frameborder="0" />').appendTo('#modal-body-preview').contents().find('body').append(e);
        });
    }
</script>
<style>
    #modal-body-preview {
        width: 600px;
        height: 390px;
        padding: 0;
        overflow: hidden;
        display: inline-block;
    }
    
    #frame {
        width: 850px;
        height: 520px;
        /*border: 1px solid black;*/
    }
    
    #frame {
        zoom: 0.75;
        -moz-transform: scale(0.75);
        -moz-transform-origin: 0 0;
    }
</style>

<div class="row">
    <div class="col-xs-12 col-sm- 12 col-md-12 col-lg-12 wrap">
        <div class="title">
            Autorespuesta de Mail
        </div>
        <hr class="basic-line">
    </div>
</div>

<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
        <form data-ng-submit="saveAutoresponder()">
            <div class="block block-info">
                <div class="body row">
                    <div class="col-md-12 wrap">
                        <div class="body form-horizontal">
                            <div class="form-group">
                                <label for="name" class="col-sm-2 control-label">*Nombre de envío </label>
                                <div class="col-sm-10">
                                    {{autoresponderForm.render('name')}}
                                    <div class="text-right" data-ng-class="data.name.length > 40 ? 'negative':''">{{"{{data.name.length > 0 ? data.name.length+'/40':''}}"}}</div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="name" class="col-sm-2 control-label">*¿A quién envías?</label>
                                <div class="col-sm-10">
                                    <div class="background-color-gray">
                                        <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4 margin-top-15px margin-botton">
                                            <md-content class="none-scroll-horizontal" layout-xs="column" layout="row">
                                                <div flex-xs flex-gt-xs="50" layout="column" class="">
                                                    <md-card class="none-margin min-height-300">
                                                        <md-card-title class="text-center">
                                                            <md-card-title-text>
                                                                <span class="small-text">¿Quien debe recibir este correo?</span>
                                                                <hr style="margin: 0; border-top: 1px solid #AFA3A3">
                                                            </md-card-title-text>
                                                        </md-card-title>
                                                        <md-card-content layout="row" layout-align="space-between">
                                                            <div class="row list-addresses-selector">
                                                                <ul>
                                                                    <li>
                                                                        <md-button class="col-lg-12 text-align-left" ng-click="getContactlist()">
                                                                            <i class="fa fa-server list-addresses-avatar" aria-hidden="true"></i> Listas de contactos
                                                                        </md-button>
                                                                    </li>
                                                                    <li>
                                                                        <md-button class="col-lg-12 text-align-left" ng-click="getSegment()">
                                                                            <i class="fa fa-user list-addresses-avatar" aria-hidden="true"></i> Segmentos
                                                                        </md-button>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </md-card-content>
                                                    </md-card>
                                                </div>
                                            </md-content>
                                        </div>

                                        <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4 margin-top-15px" ng-hide="addressees.showstep1">
                                            <div class="sgm-left-arrow-border"></div>
                                            <div class="sgm-left-arrow"></div>
                                            <md-content class="margin-left--5" layout-xs="column" layout="row">
                                                <div flex-xs flex-gt-xs="50" layout="column" ng-hide="addressees.showContactlist">
                                                    <md-card class="none-margin min-height-300">
                                                        <md-card-title class="text-center">
                                                            <md-card-title-text>
                                                                <span class="small-text">Seleccione una o varias listas</span>
                                                                <hr style="margin: 0; border-top: 1px solid #AFA3A3">
                                                            </md-card-title-text>
                                                        </md-card-title>
                                                        <md-card-content layout="row" layout-align="space-between" id=''>
                                                            <div class=" row " style="margin-top: -5%">
                                                                {#
                                                                <div class="col-lg-6" style="margin-left: -5px">
                                                                    <a ng-click='allContactlist()' class="button btn btn-xs info-inverted" data-toggle="tooltip" data-placement="top" title="Limpiar">Seleccionar todas las listas</a>
                                                                </div>
                                                                <div class="col-lg-6 text-right" style="margin-left: 5px">
                                                                    <a ng-click='clearSelect()' class="button btn btn-xs warning-inverted" data-toggle="tooltip" data-placement="top" title="Limpiar">Limpiar</a>
                                                                </div>#}
                                                                <div class="inline-block" style="margin-right: 0; padding-right: 0">
                                                                    <a ng-click='allContactlist()' class="button btn btn-xs info-inverted" data-toggle="tooltip" data-placement="top" title="Limpiar">Todas las listas</a>
                                                                </div>
                                                                <div class="inline-block" style="margin-left: 0; padding-left: 0; margin-right: 0; padding-right: 0">
                                                                    <a ng-click='clearSelect()' class="button btn btn-xs warning-inverted" data-toggle="tooltip" data-placement="top" title="Limpiar">Limpiar</a>
                                                                </div>
                                                                <div class="inline-block" ng-if="addressees.selectdContactlis.length >= 1" style="margin-left: 0; padding-left: 0;">
                                                                    <a ng-click='addFilter()' class="button btn btn-xs success-inverted" data-toggle="tooltip" data-placement="top" title="Agregar filtro">Filtro</a>
                                                                </div>
                                                            </div>
                                                            <div class="row ">
                                                                <div class="col-lg-12">
                                                                    <ui-select ng-disabled="disabledContactlist" on-select='selectAction()' multiple ng-model="addressees.selectdContactlis" ng-required="true" ui-select-required class='min-width-100' theme="select2" title="" sortable="false" close-on-select="true">
                                                                        <ui-select-match ng-hide="true">{{"{{$item.name}}"}}</ui-select-match>
                                                                        <ui-select-choices repeat="key in contactlists | propsFilter: {name: $select.search}">
                                                                            <div ng-bind-html="key.name | highlight: $select.search"></div>
                                                                        </ui-select-choices>
                                                                    </ui-select>
                                                                </div>
                                                            </div>
                                                            <div class=" margin-top-15px row">
                                                                <div class="col-lg-12" style="margin-left: 1px">
                                                                    <span class="small-text">Listas seleccionadas</span>
                                                                </div>
                                                            </div>
                                                            <div class="list-addresses-selector border-div-target" id='step1Content'>
                                                                <ul>
                                                                    <li ng-repeat="value in addressees.selectdContactlis">&raquo; {{"{{value.name}}"}}</li>
                                                                </ul>
                                                            </div>
                                                            <md-radio-group ng-model="addressees.condition" ng-if="filters.length > 0" ng-change="selectAction()">
                                                                <md-radio-button value="all" class="md-primary" ng-style="{'display':'inline'}">Todas</md-radio-button>
                                                                <md-radio-button value="some" class="md-primary" ng-style="{'display':'inline'}">Algunas</md-radio-button>
                                                            </md-radio-group>
                                                        </md-card-content>
                                                    </md-card>
                                                </div>
                                                <div flex-xs flex-gt-xs="50" layout="column" ng-hide="addressees.showSegment">
                                                    <md-card class="none-margin min-height-300">
                                                        <md-card-title class="text-center">
                                                            <md-card-title-text>
                                                                <span class="small-text">Seleccionar segmentos</span>
                                                                <hr style="margin: 0; border-top: 1px solid #AFA3A3">
                                                            </md-card-title-text>
                                                        </md-card-title>
                                                        <md-card-content layout="row" layout-align="space-between">
                                                            <div class="row" style="margin-top: -5%">
                                                                <div class="inline-block" style="margin-right: 0; padding-right: 0">
                                                                    <a ng-click='allSegment()' class="button btn btn-xs info-inverted" data-toggle="tooltip" data-placement="top" title="Limpiar">Todos los segmentos</a>
                                                                </div>
                                                                <div class="inline-block" style="margin-left: 0; padding-left: 0; margin-right: 0; padding-right: 0">
                                                                    <a ng-click='clearSelect()' class="button btn btn-xs warning-inverted" data-toggle="tooltip" data-placement="top" title="Limpiar">Limpiar</a>
                                                                </div>
                                                                <div class="inline-block" style="margin-left: 0; padding-left: 0;" ng-if="addressees.selectdSegment.length >= 1">
                                                                    <a ng-click='addFilter()' class="button btn btn-xs success-inverted" data-toggle="tooltip" data-placement="top" title="Agregar filtro">Filtro</a>
                                                                </div>
                                                            </div>
                                                            <div class="row ">
                                                                <div class="col-lg-12">
                                                                    <ui-select ng-disabled="disabledSegment" on-select='selectActionSegment()' multiple ng-model="addressees.selectdSegment" ng-required="true" ui-select-required class='min-width-100' theme="select2" title="" sortable="false" close-on-select="true">
                                                                        <ui-select-match ng-hide="true">{{"{{$item.name}}"}}</ui-select-match>
                                                                        <ui-select-choices repeat="key in segments | propsFilter: {name: $select.search}">
                                                                            <div ng-bind-html="key.name | highlight: $select.search"></div>
                                                                        </ui-select-choices>
                                                                    </ui-select>
                                                                </div>
                                                            </div>
                                                            {#
                                                            <div class=" margin-top-15px row">
                                                                <div class="col-lg-6" style="margin-left: -5px">
                                                                    <a ng-click='allSegment()' class="button btn btn-xs info-inverted" data-toggle="tooltip" data-placement="top" title="Limpiar">Seleccionar todos los segmentos</a>
                                                                </div>
                                                                <div class="col-lg-6 text-right" style="margin-left: 5px">
                                                                    <a ng-click='clearSelect()' class="button btn btn-xs warning-inverted" data-toggle="tooltip" data-placement="top" title="Limpiar">Limpiar</a>
                                                                </div>
                                                            </div>#}
                                                            <div class=" margin-top-15px row">
                                                                <div class="col-lg-12" style="margin-left: 1px">
                                                                    <span class="small-text">Segmentos seleccionadas</span>
                                                                </div>
                                                            </div>
                                                            <div class="list-addresses-selector border-div-target wrap" style="" id='step1Content'>
                                                                <ul>
                                                                    <li ng-repeat="value in addressees.selectdSegment">&raquo; {{"{{value.name}}"}}</li>
                                                                </ul>
                                                            </div>
                                                            {#
                                                            <div class="list-addresses-selector div-scroll-100px margin-top-15px wrap" style="" id='step1Content'>
                                                                <ul>
                                                                    <li ng-repeat="value in addressees.selectdSegment">&raquo; {{"{{value.name}}"}}</li>
                                                                </ul>
                                                            </div>#}
                                                            <md-radio-group ng-model="addressees.condition" ng-if="filters.length > 0" ng-change="selectActionSegment()">
                                                                <md-radio-button value="all" class="md-primary" ng-style="{'display':'inline'}">Todas</md-radio-button>
                                                                <md-radio-button value="some" class="md-primary" ng-style="{'display':'inline'}">Algunas</md-radio-button>
                                                            </md-radio-group>
                                                        </md-card-content>
                                                    </md-card>
                                                </div>

                                            </md-content>
                                        </div>
                                        <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4 margin-top-15px margin-botton" ng-repeat="key in filters">
                                            <div class="sgm-left-arrow-border" ng-hide="$index == 1"></div>
                                            <div class="sgm-left-arrow" ng-hide="$index == 1"></div>
                                            <md-content class="margin-left--5" layout-xs="column" layout="row">
                                                <div flex-xs flex-gt-xs="50" layout="column">
                                                    <md-card class="none-margin min-height-300">
                                                        <md-card-title class="text-center">
                                                            <md-card-title-text>
                                                                <span class="small-text">Seleccione un filtro</span>
                                                                <a ng-click='removeFilters($index)' class="button btn btn-xs info-inverted" data-toggle="tooltip" data-placement="top" title="Limpiar">Eliminar</a>
                                                                <hr style="margin: 0; border-top: 1px solid #AFA3A3">
                                                            </md-card-title-text>
                                                        </md-card-title>
                                                        <md-card-content layout="row" layout-align="space-between">
                                                            Tipo
                                                            <ui-select ng-model="key.typeFilters" ng-required="true" ui-select-required theme="select2" sortable="false" close-on-select="true" ng-change="selectTypeFilter(key)">
                                                                <ui-select-match placeholder="Seleccione uno">{{ "{{$select.selected.name}}" }}</ui-select-match>
                                                                <ui-select-choices repeat="value.id as value in tipeFilters | propsFilter: {name: $select.search}">
                                                                    <div ng-bind-html="value.name | highlight: $select.search"></div>
                                                                </ui-select-choices>
                                                            </ui-select>
                                                            Mail
                                                            <ui-select ng-model="key.mailSelected" ng-required="true" ui-select-required theme="select2" sortable="false" close-on-select="true" ng-change="selectMailFilter(key)">
                                                                <ui-select-match placeholder="Seleccione uno">{{ "{{$select.selected.name}}" }}</ui-select-match>
                                                                <ui-select-choices repeat="value.idMail as value in key.mail | propsFilter: {name: $select.search}">
                                                                    <div ng-bind-html="value.name | highlight: $select.search"></div>
                                                                </ui-select-choices>
                                                            </ui-select>
                                                            <div ng-if="key.typeFilters == 3  ">
                                                                Links
                                                                <ui-select ng-model="key.linkSelected" ng-required="true" ui-select-required theme="select2" sortable="false" close-on-select="true" ng-change="selectLinkFilter(key)">
                                                                    <ui-select-match placeholder="Seleccione uno">{{ "{{$select.selected.link}}" }}</ui-select-match>
                                                                    <ui-select-choices repeat="value.idMail_link as value in key.links | propsFilter: {link: $select.search}">
                                                                        <div ng-bind-html="value.link | highlight: $select.search"></div>
                                                                    </ui-select-choices>
                                                                </ui-select>
                                                            </div>
                                                        </md-card-content>
                                                    </md-card>
                                                </div>
                                            </md-content>
                                        </div>
                                        <div class="row wrap ">
                                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 well ">
                                                <span>Contactos aproximados: </span> {{"{{addressees.count.count}}"}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">*Hora del envío</label>
                                <div class="col-sm-10">
                                    <div id="dateInitial" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 input-append date add-on input-group">
                                        <span class="input-append date add-on input-group">
                        <input id="valueDatepicker" data-format="hh:mm" type="text" class="undeline-input">
                        <span class="add-on input-group-addon ">
                          <i data-time-icon="glyphicon glyphicon-time" data-date-icon="glyphicon glyphicon-calendar"></i>
                        </span>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">*Asunto</label>
                                <div class="col-sm-10">
                                    {{autoresponderForm.render('subject')}}
                                    <div class="text-right" data-ng-class="data.subject.length > 100 ? 'negative':''">{{"{{data.subject.length > 0 ? data.subject.length+'/100':''}}"}}</div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">*Nombre del remitente</label>
                                <div class="col-sm-10">
                                    <div>
                                        <div>
                                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding">
                                                <div class="col-xs-10 col-sm-10 col-md-10 col-lg-11 none-padding">
                                                    <span class="input hoshi input-default">
                              <div data-ng-show="showInputName">
                                <input placeholder="*Nombre del remitente" data-ng-model="senderName"
                                       maxlength="200" class="undeline-input">
                              </div>
                              <div data-ng-show="showSelectName">
                                <ui-select ng-model="data.senderNameSelect" ng-required="true"
                                           ui-select-required theme="select2" sortable="false"
                                           close-on-select="true">
                                  <ui-select-match
                                    placeholder="Seleccione uno">{{ "{{$select.selected.name}}" }}</ui-select-match>
                                  <ui-select-choices
                                    repeat="key.idNameSender as key in emailname | propsFilter: {name: $select.search}">
                                    <div ng-bind-html="key.name | highlight: $select.search"></div>
                                  </ui-select-choices>
                                </ui-select>
                              </div>
                            </span>
                                                </div>
                                                <div class="col-xs-2 col-sm-2 col-md-2 col-lg-1 margin-top">
                                                    <div data-ng-show="showIconsName">
                                                        <a class="color-primary" data-ng-click="changeStatusInputName()" href=""><span
                                  class="fa fa-plus " title="Agregar otro nombre"></span></a>
                                                    </div>
                                                    <div data-ng-show="showIconsSaveName">
                                                        <a class="negative" data-ng-click="changeStatusInputName()" href=""><span
                                  class="glyphicon glyphicon-remove"
                                  title="Cancelar"></span></a>
                                                        <a class="positive" data-ng-click="saveName()" href=""><span
                                  class="glyphicon glyphicon-ok margin-left-10"
                                  title="Guardar"></span></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">*Correo del remitente</label>
                                <div class="col-sm-10">
                                    <div>
                                        <div>
                                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding">
                                                <div class="col-xs-10 col-sm-10 col-md-10 col-lg-11 none-padding">
                                                    <span class="input hoshi input-default">
                              <div data-ng-show="showInputEmail">
                                <input placeholder="*Correo del remitente" maxlength="200"
                                       class="undeline-input" ng-model="senderMail">
                              </div>
                              <div data-ng-show="showSelectEmail">
                                <ui-select ng-model="data.senderMailSelect" ng-required="true" theme="select2"
                                           sortable="false" close-on-select="true">
                                  <ui-select-match
                                    placeholder="Seleccione uno">{{ "{{$select.selected.email}}" }}</ui-select-match>
                                  <ui-select-choices
                                    repeat="key.idEmailsender as key in emailsend | propsFilter: {email: $select.search}">
                                    <div ng-bind-html="key.email | highlight: $select.search"></div>
                                  </ui-select-choices>
                                </ui-select>
                              </div>
                            </span>
                                                </div>
                                                <div class="col-xs-2 col-sm-2 col-md-2 col-lg-1 margin-top">
                                                    <div data-ng-show="showIconsEmail">
                                                        <a class="color-primary" data-ng-click="changeStatusInputEmail()" href=""><span
                                  class="fa fa-plus " title="Agregar otro email"></span></a>
                                                    </div>
                                                    <div data-ng-show="showIconsSaveEmail">
                                                        <a class="negative" data-ng-click="changeStatusInputEmail()" href=""><span
                                  class="glyphicon glyphicon-remove"
                                  title="Cancelar"></span></a>
                                                        <a class="positive" data-ng-click="saveEmail()" href=""><span
                                  class="glyphicon glyphicon-ok margin-left-10"
                                  title="Guardar"></span></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Responder a</label>
                                <div class="col-sm-10">
                                    <input class="undeline-input form-control" placeholder="Responder a" type="text" ng-model="data.replyTo">
                                    <div class="text-right" data-ng-class="data.replyTo.length > 40 ? 'negative':''">{{"{{data.replyTo.length > 0 ? data.replyTo.length+'/40':''}}"}}</div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">*Estado</label>

                                <div class="col-sm-8">
                                    <md-switch class="md-primary none-margin" ng-model="data.status" md-no-ink aria-label="Switch 1">
                                        <em>Si el estado está encedido, quiere decir que la autorespuesta se encuentra activa.</em>
                                    </md-switch>

                                </div>

                            </div>
                            <!--aqui inicio option advance -->
                            <div id="OA" class="form-group" ng-cloak>
                                <label class="col-sm-2 control-label">Opciones avanzadas</label>
                                <div class="col-sm-8">
                                    <div class="onoffswitch">
                                        <input type="checkbox" ng-model="advancedoptions" class="onoffswitch-checkbox" id="advancedoptions">
                                        <label class="onoffswitch-label" for="advancedoptions">
                                            <span class="onoffswitch-inner"></span>
                                            <span class="onoffswitch-switch"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group" ng-show="advancedoptions == true">
                                <label class="col-sm-2 control-label">Crear campo combinado</label>
                                <div class="col-sm-1">
                                    <div class="onoffswitch">
                                        <input type="checkbox" ng-click="setAlign(2)" name="insertoption" ng-model="insertoption" class="onoffswitch-checkbox advancecont insertoption" id="insertoption">
                                        <label class="onoffswitch-label" for="insertoption">
                                            <span class="onoffswitch-inner"></span>
                                            <span class="onoffswitch-switch"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="insertoption advancedoptions-container hiddecontent" ng-show="insertoption == true && advancedoptions == true">
                                <div class="container" style="margin-left: 46px;margin-top: 50px;">
                                    <div class="row">
                                        <div class="col-sm-12"> 
                                            <div class="col-sm-2">
                                                <label class="control-label" style="padding-top: 15px;">*Lista de campos</label>
                                            </div> 
                                            <div class="col-sm-3">
                                                <span class="input hoshi input-default spanLC">
                                                    <select class="undeline-input" multiple="multiple" name="services" id="services" ng-model="services" style="padding-bottom: 0px;" ng-change="limitfields(1)">
                                                        <option ng-repeat="ci in customfield"  value="{{ '{{ci.idCustomfield }}' }}">{{ '{{ci.name}}' }}</option>
                                                    </select>
                                                    <p style="font-size:12px; color:red">Debe elegir 3 campos para combinar</p>
                                                </span>
                                            </div>
                                            <div class="col-sm-7">
                                                <label class="control-label" style="text-align: unset;font-weight: unset;">
                                                    <strong>Nombre del campo personalizado:</strong><br/>
                                                    <span>{{ '{{mixedField}}' }}<br/></span>
                                                    <span style="font-size:12px ; color:red">*Debes poner este nombre de campo personalizado en tu plantilla.*</span>
                                                </label>
                                            </div>
                                        </div> 
                                    </div>
                                </div>
                                <!--NAVS DINAMICOS-->
                                <div ng-cloak class="wrap">
                                  <md-content>
                                    <md-tabs ng-show="tabs.length > 0" md-dynamic-height md-border-bottom class="md-tabs-autoresponder tabs">
                                        <md-tab ng-repeat="tab in tabs" label="{{'{{tab.nameService}}'}}" ng-disabled="tab.disabled">
                                          <div class ="container" style="margin-left: unset;margin-top: 50px;"> 
                                            <div class="row"> 
                                                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6"> 
                                                    <div class="col-xs-8 col-sm-8 col-md-4 col-lg-4"> 
                                                        <label>Tamaño de texto</label> 
                                                    </div> 
                                                    <div class="col-xs-8 col-sm-8 col-md-4 col-lg-4">
                                                        <select class="form-control ng-pristine ng-valid ng-empty ng-touched" name="letra" id="letra" ng-model="selectedOption" data-ng-change="changeSize($index,this)" ng-init="selectedOption = tab.fontSize"> 
                                                            <option value="8">8</option>
                                                            <option value="10">10</option>
                                                            <option value="12">12</option>
                                                            <option value="14">14</option>
                                                            <option value="16">16</option>
                                                            <option value="18">18</option>
                                                            <option value="20">20</option>
                                                            <option value="24">24</option>
                                                            <option value="36">36</option>
                                                        </select> 
                                                    </div> 
                                                </div> 
                                            </div> 
                                            <div class="row" style="margin-top: 2%;"> 
                                                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6"> 
                                                    <div class="col-xs-8 col-sm-8 col-md-4 col-lg-4"> 
                                                        <label>Color de texto</label> 
                                                    </div> 
                                                    <div class="col-xs-8 col-sm-8 col-md-4 col-lg-4">
                                                        <input class="form-control" type="color"  id="selectcolor" ng-model="selectColor" data-ng-change="changeColor($index,this)" ng-init="selectColor = tab.color">
                                                    </div> 
                                                </div> 
                                            </div> 
                                            <div class="row" style="margin-top: 2%;"> 
                                                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6"> 
                                                    <div class="col-xs-8 col-sm-8 col-md-4 col-lg-4"> 
                                                        <label>Estilo de texto</label> 
                                                    </div> 
                                                    <div class="col-xs-8 col-sm-8 col-md-4 col-lg-4"> 
                                                        <label id="negrita" style="padding: 6px 6px 1px; border-radius: 30px; " class="btn default-inverted glyphicon {{'{{tab.boldClass}}'}}" [ngClass]="{info-inverted: true, '': false}" (click)="infoInvert()" data-toggle="tooltip" data-placement="top" title="Negrita"> 
                                                            <input type="checkbox" id="bold" class="check" style="display:none" ng-model="bold" data-ng-click="changeStyle($index,this)"> 
                                                            <span><i class="glyphicon glyphicon-bold"></i></span> 
                                                        </label> 
                                                        <label id="cursiva" style="padding: 6px 6px 1px; border-radius: 30px; " class="btn default-inverted glyphicon {{'{{tab.italicsClass}}'}}" data-toggle="tooltip" data-placement="top" title="Cursiva"> 
                                                            <input type="checkbox" id="italics" class="check" style="display:none" ng-model="italics"  data-ng-click="changeStyle($index,this)"> 
                                                            <span><i class="glyphicon glyphicon-italic"></i></span> 
                                                        </label> 
                                                        <label id="subrayada" style="padding: 6px 6px 1px; border-radius: 30px; " class="btn default-inverted glyphicon {{'{{tab.underlinedClass}}'}}" data-toggle="tooltip" data-placement="top" title="Subrayado"> 
                                                            <input type="checkbox" id="underlined" class="check" style="display:none" ng-model="underlined"  data-ng-click="changeStyle($index,this)"> 
                                                            <span><i class="glyphicon glyphicon-text-color"></i></span> 
                                                        </label> 
                                                    </div> 
                                                </div> 
                                            </div> 
                                             <div class="row" style="margin-top: 2%;"> 
                                                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6"> 
                                                    <div class="col-xs-8 col-sm-8 col-md-4 col-lg-4"> 
                                                        <label>Fuente</label> 
                                                    </div> 
                                                    <div class="col-xs-8 col-sm-8 col-md-4 col-lg-4"> 
                                                        <select  class="form-control  ng-pristine ng-valid ng-empty ng-touched" name="letra" id="letra" ng-model="fontFamily" data-ng-change="changeFont($index,this)" ng-init="fontFamily = tab.fontFamily">
                                                            <option value="Arial">Arial</option>
                                                            <option value="Courier New">Courier New</option>
                                                            <option value="Verdana">Verdana</option>
                                                            <option value="Comic Sans MS">Comic Sans MS</option>
                                                            <option value="Georgia">Georgia</option>
                                                            <option value="Times New Roman">Times New Roman</option>
                                                        </select>
                                                    </div>  
                                                </div> 
                                            </div>
                                        </div>
                                        </md-tab>
                                    </md-tabs>
                                  </md-content>
                                </div>
                                <hr>
                                <div class ="container" ng-show="tabs.length > 1" style="margin-left: 46px;margin-top: 50px;">
                                    <div class="row" style="margin-top: 2%;"> 
                                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6"> 
                                            <div class="col-xs-8 col-sm-8 col-md-4 col-lg-4"> 
                                                <label>Alineación de texto</label> 
                                            </div> 
                                            <div class="col-xs-8 col-sm-8 col-md-4 col-lg-4"> 
                                                <label id="alignleft" style="padding: 6px 6px 1px; border-radius: 30px; " class="btn default-inverted glyphicon {{'{{infoLeft}}'}}" data-toggle="tooltip" data-placement="top" title="Alinear a la izquierda"> 
                                                    <input id="check2" type="checkbox" style="display:none" class="check" ng-model="left" data-ng-click="alignText(this)"> 
                                                    <span><i class="glyphicon glyphicon-align-left"></i></span> 
                                                </label> 
                                                <label id="aligncenter" style="padding: 6px 6px 1px; border-radius: 30px; " class="btn default-inverted glyphicon {{'{{infoCenter}}'}}" data-toggle="tooltip" data-placement="top" title="Centrar"> 
                                                    <input id="check3" type="checkbox" style="display:none" class="check" ng-model="center" data-ng-click="alignText(this)"> 
                                                    <span><i class="glyphicon glyphicon-align-center"></i></span> 
                                                </label> 
                                                <label id="alignright" style="padding: 6px 6px 1px; border-radius: 30px; " class="btn default-inverted glyphicon {{'{{infoRight}}'}}" data-toggle="tooltip" data-placement="top" title="Alinear a la derecha"> 
                                                    <input id="check4" type="checkbox" style="display:none" class="check" ng-model="right" data-ng-click="alignText(this)"> 
                                                    <span><i class="glyphicon glyphicon-align-right"></i></span> 
                                                </label> 
                                            </div> 
                                        </div> 
                                    </div>
                                    <div class="row" style="margin-top: 2%;"> 
                                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6"> 
                                            <div class="col-xs-8 col-sm-8 col-md-4 col-lg-4"> 
                                                <label>Vista previa</label> 
                                            </div> 
                                            <div class="col-xs-8 col-sm-8 col-md-4 col-lg-4">
                                                <div ng-show="tabs.length > 0" class="preview" style="width: 800px;border: 1px solid black;height: auto;padding: 5px 10px;text-align:{{'{{textAlign}}'}};">
                                                    <span ng-repeat="tab in tabs" style="color:{{'{{tab.color}}'}};font-size:{{'{{tab.fontSize}}'}}px;font-weight:{{'{{tab.fontWeight}}'}};font-style:{{'{{tab.fontStyle}}'}};text-decoration:{{'{{tab.textDecoration}}'}};font-family:{{'{{tab.fontFamily}}'}};">{{ '{{tab.nameService}}' }} </span>
                                                </div>
                                            </div> 
                                        </div> 
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="space"></div>
                            </div>
                            <!-- AQUI EMPIEZA EL CONTENIDO DEL CORREO-->
                            <div class="row">
                                <div class="clearfix"></div>
                                <div class="space"></div>
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">

                                    <div class="subtitle text-left margin-left-10">
                                        <em>Contenido del correo</em>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                <div ng-if="boolEditors">
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-2 col-sm-offset-3 col-md-offset-3 col-lg-offset-3">
                                            <ul class="ch-grid ">
                                                <li>
                                                    <div class="ch-item edit-avanz pointer-cursor margin-botton">
                                                        <a data-ng-click="openMailTemplate()">
                                                            <div class="ch-info">
                                                                <h3>Plantillas prediseñadas</h3>
                                                            </div>
                                                        </a>
                                                    </div>
                                                    <b>Plantillas prediseñadas</b>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-2">
                                            <ul class="ch-grid ">
                                                <li>
                                                    <div class="ch-item edit-avanz pointer-cursor margin-botton">
                                                        <a data-ng-click="openContentEditor()">
                                                            <div class="ch-info">
                                                                <h3>Creación de contenido</h3>
                                                            </div>
                                                        </a>
                                                    </div>
                                                    <b>Creación de contenido</b>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="col-xs-12 col-sm-6 col-md-4 col-lg-2">
                                            <ul class="ch-grid">
                                                <li>

                                                    <div class="ch-item html-icon pointer-cursor margin-botton">
                                                        <a data-ng-click="openEditorHtml()" class="text-center">
                                                            <div class="ch-info">
                                                                <h3>Editor de html</h3>
                                                            </div>
                                                        </a>
                                                    </div>
                                                    <b>Editor de html</b>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div ng-show="!boolEditors">
                                    <div class="container-fluid">
                                        <div class="body row">
                                            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 col-md-offset-3">
                                                <a href="" data-ng-click="editContentEditor()" class="text-center">
                                                    <h3>Click aquí para editar contenido</h3>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="body row">
                                            <div class="col-lg-6 col-lg-offset-3 text-center">
                                                <div id="modal-body-preview"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="space"></div>
                            <div class="clearfix"></div>
                            <div class="space"></div>
                            <div class="footer text-right">
                                <button type="submit" class="button shining btn btn-xs-round shining shining-round round-button success-inverted" data-toggle="tooltip" data-placement="top" title="Guardar">
                                    <span class="glyphicon glyphicon-ok"></span>
                                </button>
                                <a ui-sref="index" class="button shining btn btn-xs-round shining shining-round round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Cancelar">
                                    <span class="glyphicon glyphicon-remove"></span>
                                </a>

                            </div>
        </form>
        </div>
       
        </div>
        </div>
        </div>
    </div>
</div>
        </form>
        </div>
        </div>
        </div>
        </div>
    </div>
</div>

                       