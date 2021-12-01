<script>
  $.fn.datetimepicker.defaults = {
    maskInput: false,
    pickDate: false,
    pickTime: true,
    startDate: new Date()
  };

  function htmlPreview(idAutoresponder) {
    $.post("{{url('autoresponder/preview')}}/" + idAutoresponder, function (preview) {
      var e = preview.preview;
      $('<iframe id="frame" frameborder="0" />').appendTo('#modal-body-preview').contents().find('body').append(e);
    });
  }
</script>
<style>
  #modal-body-preview { width: 600px; height: 390px; padding: 0; overflow: hidden; display: inline-block;}
  #frame { width: 850px; height: 520px; /*border: 1px solid black;*/ }
  #frame { zoom: 0.75; -moz-transform: scale(0.75); -moz-transform-origin: 0 0; }
</style>
<div ng-cloak>
  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
      <div class="title">
        Autorespuesta de SMS 
      </div>
      <hr class="basic-line">
    </div>
  </div>

  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8 wrap">
      <form data-ng-submit="saveAutorespdesms()">
        <br/> 
        <div class="block block-info">
          <div class="body row">
            <div class="col-md-12 wrap">
              <div class="body form-horizontal">
                <div class="form-group">
                  <label for="name" class="col-sm-2 control-label">*Nombre de envío </label>
                  <div class="col-sm-10">
                    {{autoresponderForm.render('name')}}
                    <div class="text-right" data-ng-class="data.name.length > 40 ? 'negative':''">{{"{{data.name.length > 0 ?  data.name.length+'/40':''}}"}}</div>
                  </div>
                </div>

                {#<button type="button" ng-click="filtersmscategory()" class="btn btn-default btn-md">
                  <span class="glyphicon glyphicon-user" aria-hidden="true"></span> prueba
                </button>#}

                <div class="form-group" ng-if="misc.category">
                  <div class="form-group">
                    <label for="name" class="col-sm-2 control-label">*Categoria</label>
                    <div class="col-sm-10">
                      <ui-select ng-model="data.idSmsCategory" theme="select2" sortable="false" close-on-select="true">
                        <ui-select-match placeholder="Seleccione una categoría">{{ "{{$select.selected.name}}" }}</ui-select-match>
                        <ui-select-choices repeat="key.idSmsCategory as key in misc.category | propsFilter: {name: $select.search}">
                          <div ng-bind-html="key.name | highlight: $select.search"></div>
                        </ui-select-choices>
                      </ui-select> 
                    </div>
                  </div>
                </div>


                {#<div class="form-group">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                  <label class="col-sm-4 col-md-4 text-right">*Categoría:</label>
                  <span class="input-default col-sm-8 col-md-8">
                    <select id="idSmsCategory" name="idSmsCategory" ng-model="idSmsCategory" class="select2" required="">
                      <option value=""></option>
                      {% for item in category %}
                        <option value="{{ item.idSmsCategory }}">{{ item.name }}</option>
                      {% endfor %}
                    </select>
                  </span>
                </div>
              </div>#}


                <div class="form-group">
                  <label for="name" class="col-sm-2 control-label">*¿A quién envías?</label>
                  <div class="col-sm-10">
                    <div class="background-color-gray">
                      <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 margin-top-15px margin-botton" >
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
                                <div class="row list-addresses-selector" >
                                  <ul>
                                    <li>
                                    <md-button class="col-lg-12 text-align-left" ng-click="getContactlist()" >
                                      <i class="fa fa-server list-addresses-avatar" aria-hidden="true"></i>
                                      Listas de contactos
                                    </md-button>
                                    </li>
                                    <li>
                                      {#<md-button class="col-lg-12 text-align-left" ng-click="getSegment()">
                                        <i class="fa fa-user list-addresses-avatar" aria-hidden="true"></i>
                                        Segmentos
                                      </md-button>#}
                                    </li>
                                  </ul>
                                </div>
                              </md-card-content>
                            </md-card>
                          </div>
                        </md-content>
                      </div>

                      <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 margin-top-15px" ng-hide="addressees.showstep1" >
                        <div class="sgm-left-arrow-border"></div>
                        <div class="sgm-left-arrow"></div>
                        <md-content class="margin-left--5" layout-xs="column" layout="row" >
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
                                  {#                <div class="col-lg-6" style="margin-left: -5px">
                                                    <a ng-click='allContactlist()'  class="button btn btn-xs info-inverted"
                                                       data-toggle="tooltip" data-placement="top" title="Limpiar">Seleccionar todas las listas</a>
                                                  </div>
                                                  <div class="col-lg-6 text-right" style="margin-left: 5px">
                                                    <a ng-click='clearSelect()'  class="button btn btn-xs warning-inverted"
                                                       data-toggle="tooltip" data-placement="top" title="Limpiar">Limpiar</a>
                                                  </div>#}
                                  {#<div class="inline-block" style="margin-right: 0; padding-right: 0">
                                    <a ng-click='allContactlist()'  class="button btn btn-xs info-inverted"
                                       data-toggle="tooltip" data-placement="top" title="Limpiar">Todas las listas</a>   
                                  </div>#}
                                  <div class="inline-block" style="margin-left: 0; padding-left: 0; margin-right: 0; padding-right: 0">
                                    <a ng-click='clearSelect()'  class="button btn btn-xs warning-inverted"
                                       data-toggle="tooltip" data-placement="top" title="Limpiar">Limpiar</a>
                                  </div>
                                  {#<div class="inline-block" ng-if="addressees.selectdContactlis.length >= 1" style="margin-left: 0; padding-left: 0;">
                                    <a ng-click='addFilter()'  class="button btn btn-xs success-inverted"
                                       data-toggle="tooltip" data-placement="top" title="Agregar filtro">Filtro</a>
                                  </div>#}
                                </div>
                                <div class="row ">
                                  <div class="col-lg-12" >
                                    <ui-select ng-disabled="disabledContactlist" on-select='selectAction()'  multiple ng-model="addressees.selectdContactlis" ng-required="true"  ui-select-required  class='min-width-100'
                                               theme="select2" title=""  sortable="false" close-on-select="true">
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
                          <div flex-xs flex-gt-xs="50" layout="column" ng-hide="addressees.showSegment" >
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
                                    <a ng-click='allSegment()'  class="button btn btn-xs info-inverted"
                                       data-toggle="tooltip" data-placement="top" title="Limpiar">Todos los segmentos</a>   
                                  </div>
                                  <div class="inline-block" style="margin-left: 0; padding-left: 0; margin-right: 0; padding-right: 0">
                                    <a ng-click='clearSelect()'  class="button btn btn-xs warning-inverted"
                                       data-toggle="tooltip" data-placement="top" title="Limpiar">Limpiar</a>
                                  </div>
                                  <div class="inline-block" style="margin-left: 0; padding-left: 0;" ng-if="addressees.selectdSegment.length >= 1">
                                    <a  ng-click='addFilter()'  class="button btn btn-xs success-inverted"
                                        data-toggle="tooltip" data-placement="top" title="Agregar filtro">Filtro</a>
                                  </div>
                                </div>
                                <div class="row ">
                                  <div class="col-lg-12" >
                                    <ui-select ng-disabled="disabledSegment" on-select='selectActionSegment()' multiple ng-model="addressees.selectdSegment" ng-required="true"  ui-select-required  class='min-width-100'
                                               theme="select2" title=""  sortable="false" close-on-select="true">
                                      <ui-select-match ng-hide="true">{{"{{$item.name}}"}}</ui-select-match>
                                      <ui-select-choices repeat="key in segments | propsFilter: {name: $select.search}">
                                        <div ng-bind-html="key.name | highlight: $select.search"></div>
                                      </ui-select-choices>
                                    </ui-select>
                                  </div>
                                </div>
                                {#<div class=" margin-top-15px row">
                                  <div class="col-lg-6" style="margin-left: -5px">
                                    <a ng-click='allSegment()'  class="button btn btn-xs info-inverted"
                                       data-toggle="tooltip" data-placement="top" title="Limpiar">Seleccionar todos los segmentos</a>
                                  </div>
                                  <div class="col-lg-6 text-right" style="margin-left: 5px">
                                    <a ng-click='clearSelect()'  class="button btn btn-xs warning-inverted"
                                       data-toggle="tooltip" data-placement="top" title="Limpiar">Limpiar</a>
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
                                {#                       <div class="list-addresses-selector div-scroll-100px margin-top-15px wrap" style="" id='step1Content'>
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
                        <md-content class="margin-left--5" layout-xs="column" layout="row" >
                          <div flex-xs flex-gt-xs="50" layout="column">
                            <md-card class="none-margin min-height-300">
                              <md-card-title class="text-center">
                                <md-card-title-text>
                                  <span class="small-text">Seleccione un filtro</span>
                                  <a ng-click='removeFilters($index)'  class="button btn btn-xs info-inverted"
                                     data-toggle="tooltip" data-placement="top" title="Limpiar">Eliminar</a>     
                                  <hr style="margin: 0; border-top: 1px solid #AFA3A3">
                                </md-card-title-text>
                              </md-card-title>
                              <md-card-content layout="row" layout-align="space-between">
                                Tipo
                                <ui-select ng-model="key.typeFilters" ng-required="true"
                                           ui-select-required theme="select2" sortable="false"
                                           close-on-select="true" ng-change="selectTypeFilter(key)">
                                  <ui-select-match
                                    placeholder="Seleccione uno">{{ "{{$select.selected.name}}" }}</ui-select-match>
                                  <ui-select-choices
                                    repeat="value.id as value in tipeFilters | propsFilter: {name: $select.search}">
                                    <div ng-bind-html="value.name | highlight: $select.search"></div>
                                  </ui-select-choices>
                                </ui-select>
                                Mail
                                <ui-select ng-model="key.mailSelected" ng-required="true"
                                           ui-select-required theme="select2" sortable="false"
                                           close-on-select="true" ng-change="selectMailFilter(key)">
                                  <ui-select-match
                                    placeholder="Seleccione uno">{{ "{{$select.selected.name}}" }}</ui-select-match>
                                  <ui-select-choices
                                    repeat="value.idMail as value in key.mail | propsFilter: {name: $select.search}">
                                    <div ng-bind-html="value.name | highlight: $select.search"></div>
                                  </ui-select-choices>
                                </ui-select>
                                <div ng-if="key.typeFilters == 3  ">
                                  Links
                                  <ui-select ng-model="key.linkSelected" ng-required="true"
                                             ui-select-required theme="select2" sortable="false"
                                             close-on-select="true" ng-change="selectLinkFilter(key)">
                                    <ui-select-match
                                      placeholder="Seleccione uno">{{ "{{$select.selected.link}}" }}</ui-select-match>
                                    <ui-select-choices
                                      repeat="value.idMail_link as value in key.links | propsFilter: {link: $select.search}">
                                      <div ng-bind-html="value.link | highlight: $select.search"></div>
                                    </ui-select-choices>
                                  </ui-select>
                                </div>
                              </md-card-content>
                            </md-card>
                          </div>
                        </md-content>
                      </div>
                      <div class="row wrap " >
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 well ">
                          <span>Contactos aproximados: </span>     {{"{{addressees.results.counts}}"}}
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
                  <label class="col-sm-2 control-label">Comienzo de Programación</label>
                  <div class="col-xs-10 col-sm-10 col-lg-10 text-right wrap ">        
                    <div class="input-group"
                         moment-picker="data.scheduledate"
                         format="YYYY-MM-DD">

                      <input id="dateinitial" class="form-control"                   
                             placeholder="Seleccionar fecha inicial"
                             ng-model="data.scheduledate"              
                             ng-model-options="{ updateOn: 'blur' }"
                             >
                      <span class="input-group-addon">
                        <i class="glyphicon glyphicon-calendar"></i>
                      </span>
                    </div> 
                  </div>
                </div>

                <div class="form-group" >
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <label class="col-sm-2 col-md-2 text-left">Es cumpleaños?</label>
                    <span class="input hoshi input-default col-sm-1 " >
                      <div class="onoffswitch">
                        <input type="checkbox" name="sentNow" ng-click="birthdatefunction()" class="onoffswitch-checkbox" id="sentNow">
                        <label class="onoffswitch-label" for="sentNow">
                          <span class="onoffswitch-inner"></span>
                          <span class="onoffswitch-switch"></span>
                        </label>
                      </div>
                      {#                               <input type="checkbox" class="toggle-sms-two-way" ng-click="functions.sentNow()"/>#}
                    </span>
                  </div>
                </div>

                <div class="form-group">
                  <label class="col-sm-2 control-label">Días de la semana</label>
                  <div class="col-sm-10">
                    <md-checkbox 
                      md-indeterminate="isIndeterminate()" class="md-warn float-left" ng-model="data.days.Monday" > 
                      <span class="extra-small-text">Lunes</span>
                    </md-checkbox>
                    <md-checkbox 
                      md-indeterminate="isIndeterminate()" class="md-warn float-left" ng-model="data.days.Tuesday" > 
                      <span class="extra-small-text">Martes</span>
                    </md-checkbox>
                    <md-checkbox 
                      md-indeterminate="isIndeterminate()" class="md-warn float-left" ng-model="data.days.Wednesday" > 
                      <span class="extra-small-text">Miercoles</span>
                    </md-checkbox>
                    <md-checkbox 
                      md-indeterminate="isIndeterminate()" class="md-warn float-left" ng-model="data.days.Thursday" > 
                      <span class="extra-small-text">Jueves</span>
                    </md-checkbox>
                    <md-checkbox 
                      md-indeterminate="isIndeterminate()" class="md-warn float-left" ng-model="data.days.Friday" > 
                      <span class="extra-small-text">Viernes</span>
                    </md-checkbox>
                    <md-checkbox 
                      md-indeterminate="isIndeterminate()" class="md-warn float-left" ng-model="data.days.Saturday" {#ng-click="checkDays()"#} > 
                      <span class="extra-small-text">Sábado</span>
                    </md-checkbox>
                    <md-checkbox 
                      md-indeterminate="isIndeterminate()" aria-cheked="true" class="md-warn float-left" ng-model="data.days.Sunday" > 
                      <span class="extra-small-text">Domingo</span>
                    </md-checkbox>
                  </div>
                </div>

                <div class="form-group">
                  <label class="col-sm-2 control-label">Asunto (Descripcion)</label>
                  <div class="col-sm-10">
                    {{autoresponderForm.render('subject')}}
                    <div class="text-right" data-ng-class="data.subject.length > 100 ? 'negative':''">{{"{{data.subject.length > 0 ?  data.subject.length+'/100':''}}"}}</div>
                  </div>
                </div>



                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap"> 
                  <label class="col-sm-2 control-label text-align-left">Etiquetas:</label>
                  <span class="input hoshi input-default col-sm-8 col-md-8">
                    <table id="customers">
                      <tbody>
                        {#                                      <tr ng-show="countContactsApproximate.counts">#}
                        <tr ng-show="addressees.results.counts">
                          <th>Campo</th>
                          <th>Etiqueta</th>
                        </tr>
                        <tr>
                          {#                                          <td colspan="2" style="text-align: center;" ng-show="!countContactsApproximate.counts">#}
                          <td colspan="2" style="text-align: center;" ng-show="!addressees.results.counts">
                            No hay etiquetas disponibles. Seleccione una lista de contactos o segmento con al menos un contacto.
                          </td>
                        </tr>
                        <tr  class="alt" ng-repeat="(key, value) in addressees.results.tags"  ng-show="addressees.results.counts">
                          <td>{{"{{value.name}}"}}</td>
                          <td ng-click="addTag(value.tag)" style="cursor: pointer;">{{"{{value.tag}}"}}
                          </td>
                        </tr>

                      </tbody></table>
                  </span>
                </div>
             <div class="form-group" ng-cloak>
                  <label class="col-sm-2 control-label text-justify">Usar más de&nbsp 160 caracteres:</label>
                  <div class="col-sm-1">
                    <div class="onoffswitch">
                      <input type="checkbox" name="morecaracter" data-ng-model="data.morecaracter" class="onoffswitch-checkbox" id="morecaracter" ng-click="opeModalMoreCa()">
                      <label class="onoffswitch-label" for="morecaracter">
                        <span class="onoffswitch-inner"></span>
                        <span class="onoffswitch-switch"></span>
                      </label>
                    </div>
                  </div>               
              </div>
                <div class="form-group">
                  <label  class="col-sm-2 text-right">*Mensajes:</label>
                  <span class="input hoshi input-default  col-sm-10">
                    <textarea placeholder="Descripción" rows="5" class="undeline-input" ng-model="data.message" id="description" name="description" maxlength="{{'{{data.morecaracter == true ? 300:160 }}'}}" style="resize: none;" ng-change="validateInLine()"></textarea>
                    <div class="text-right" ng-hide='data.morecaracter' data-ng-class="data.message.length > 160 ? 'negative':''">{{"{{data.message .length > 0 ?  data.message.length+'/160':''}}"}}</div>
                    <div class="text-right" ng-show='data.morecaracter' data-ng-class="data.message.length > 300 ? 'negative':''">{{"{{data.message .length > 0 ?  data.message.length+'/300':''}}"}}</div>
                    <h6 class="color-danger">
                      Los siguientes caracteres serán removidos del mensaje: \º~|·[]^{}¨´€"
                    </h6>
                    <h6 class="color-warning" ng-show='misc.existTags && data.morecaracter == false' >Si personaliza el mensaje SMS y éste excede los 160 caracteres permitidos será cortado en el momento del envío</h6>
                    <h6 class="color-warning" ng-show='misc.existTags && data.morecaracter == true' >Si personaliza el mensaje SMS y éste excede los 300 caracteres permitidos será cortado en el momento del envío</h6>
                  </span>
                </div>

              </div>
            </div>
          </div>
          <div class="footer text-right">
            <button type="submit" class="button shining btn btn-xs-round shining shining-round round-button success-inverted" data-toggle="tooltip" data-placement="top" title="Guardar">
              <span class="glyphicon glyphicon-ok"></span>
            </button>
            <a ui-sref="index" class="button shining btn btn-xs-round shining shining-round round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Cancelar">
              <span class="glyphicon glyphicon-remove"></span>
            </a>
          </div>
        </div>
      </form>
    </div>


    <div class="col-xs-12 col-sm-12 col-md-8 col-lg-4 wrap">  
      <div class="form-group">                            
        <br>
        <div class="fill-block fill-block-info" >
          <div class="header">
            Instrucciones
          </div>
          <div class="body">
            <p>Recuerde tener en cuenta estas recomendaciones</p>
            <ul>
              <div class="form-group">
                <li>El nombre debe tener mínimo 2 y máximo 45 caracteres</li>
                <li>Recuerde que si usa la opción de más de 160 carácteres, su mensaje no podrá superar los <b>300</b> carácteres</li>
                <li>La cantidad de caracteres puede variar si se utilizan campos personalizados. 
                  Por ejemplo, al usar la etiqueta <i>%%NOMBRE%%</i>, en un SMS puede aparecer <i>Juan</i> con 4 caracteres y en otro <i>Fernando</i> con 8 caracteres</li>
                <li>Los campos con asterisco(*) son obligatorios.</li>
              </div>
            </ul>
          </div>
          {#<div class="footer">
              Creación
          </div>#}
        </div>     
      </div>
    </div>


  </div>


</div>
<div id="alertMoreCaracter" class="modal" >
  <div class="dialog__overlay"></div>
  <div class="dialog__content">
    <div class="morph-shape">
      <svg xmlns="https://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 560 280" preserveAspectRatio="none">
      <rect x="3" y="3" fill="none" width="556" height="276"/>
      </svg>
    </div>
    <div class="dialog-inner" >
      <div class="form-group row">
        <label for="name" class="col-xs-12" style="padding-top: 10px; font-size: 18px">Tenga en cuenta que cada mensaje que contenga entre <b>160 </b> y <b>300</b> caracteres, será cobrado por 2 sms</label>      
      </div>
      <div>
        <a onClick="closeModalForm()" class="button shining btn btn-md danger-inverted" data-dialog-close>Cerrar</a>      
      </div>
    </div>
  </div>
</div>
<script>       
    function closeModalForm() {
        $('#alertMoreCaracter').removeClass('dialog dialog--open');
        $('#alertMoreCaracter').addClass('modal'); 
    } 
</script>