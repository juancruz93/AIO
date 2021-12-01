{% block header %}
  {{ stylesheet_link('css/checkboxStyle.css') }}
{% endblock %}
<style>
  .md-content-select{
    display: table !important;
  }
</style>
<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="subtitle">
      <em>Destinatarios</em>
    </div>
    <br>
    <p class="small-text">
      Escoge una lista o segmento con los contactos que deseas que reciban el correo.
    </p>
  </div>
</div>

<div class="row">
  <form name="contactlistForm" class="form-horizontal" role="form" ng-submit="addAddressees()">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap" >
      <div class="block block-info">
        <div class="body background-color-gray" >
          <div class="row" >
            <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4 margin-botton" >
              <md-content class="none-scroll-horizontal " layout-xs="column" layout="row">
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
                          <md-button class="col-lg-12 text-align-left" ng-click="getSegment()">
                            <i class="fa fa-user list-addresses-avatar" aria-hidden="true"></i>
                            Segmentos
                          </md-button>
                          </li>
                        </ul> 
                      </div>
                    </md-card-content>
                    <md-card-content layout="row" layout-align="space-between">
                      <div class="row list-addresses-selector" >
                        <ul>
                          <li>
                          <md-switch  class="md-primary " ng-change="only()" ng-model="data.singleMail" aria-label="Switch 1" ng-mouseleave="enabled = false" ng-mouseover="enabled = true">
                            <span>Enviar correo único.<i class="fa fa-info" aria-hidden="true"  >
                              </i></span>              
                          </md-switch>
                          <div class="toolbardemoBasicUsage">
                            <md-content flex layout-padding ng-show="enabled" style="text-align: justify; background: papayawhip;">
                              <p>
                                Esta opción permite enviar un único email a correos que se encuentren repetidos entre las listas de contacto o segmentos.
                              </p>
                            </md-content>
                          </div>
                          </li>
                          <li>
                          <md-switch class="md-primary " ng-model="data.alldb" aria-label="Switch 1" ng-mouseleave="enableddb = false" ng-mouseover="enableddb = true" ng-show="data.idAccount == '49' || data.idAccount == '1094' || data.idAccount == '912' || data.idAccount == '541' ">
                            <span>Enviar a toda la base.<i class="fa fa-info" aria-hidden="true"  >
                              </i></span>              
                          </md-switch>
                          <div class="toolbardemoBasicUsage">
                            <md-content flex layout-padding ng-show="enableddb" style="text-align: justify; background: papayawhip;">
                              <p>
                                Esta opción envía la campaña a los contactos que se encuentren Bloqueados o Rebotados en su B.D. y siempre y cuando la lista haya sido importada como contactos suscritos
                              </p>
                            </md-content>
                          </div>
                          <md-switch class="md-primary " id="typeUnsuscribed" ng-model="data.typeUnsuscribed" ng-selected="data.typeUnsuscribed" aria-label='Switch 1' ng-mouseleave = 'enabledcat = false' ng-show="data.idAccount == '49' || data.idAccount == '641' || data.typeAccount=='online'" ng-mouseover='enabledcat = true' ">
                            <span>Desuscripción personalizada por categoría<i class="fa fa-info" aria-hidden="true"  ></i></span>              
                          </md-switch>
                          <div class="toolbardemoBasicUsage">
                            <md-content flex layout-padding ng-show="enabledcat" style="text-align: justify; background: papayawhip;">
                              <p>
                                Esta opción permite que se realice una desuscripción por categoría de las listas de contacto, tenga en cuenta que, en las listas de contactos que aparece el usuario, debe estar ordenado con sus respectivas categorías.
                              </p>
                            </md-content>
                          </div>
                          </li>
                        </ul> 
                      </div>
                    </md-card-content>
                  </md-card>
                </div>
              </md-content>
            </div>
            <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4" ng-hide="addressees.showstep1" >
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
                      <div class="row" style="margin-top: -1%">
                        <div class="inline-block" style="margin-right: 0; padding-right: 0">
                          <a ng-click='allContactlist()'  class="button btn btn-xs info-inverted"
                             data-toggle="tooltip" data-placement="top" title="Limpiar">Seleccionar todas las listas</a>   
                        </div>
                        <div class="inline-block" style="margin-left: 0; padding-left: 0; margin-right: 0; padding-right: 0">
                          <a ng-click='clearSelect()'  class="button btn btn-xs warning-inverted"
                             data-toggle="tooltip" data-placement="top" title="Limpiar">Limpiar</a>
                        </div>
                       <div class="inline-block" ng-if="addressees.selectdContactlis.length >= 1" style="margin-left: 0; padding-left: 0;">
                          <a ng-click='addFilter()'  class="button btn btn-xs success-inverted"
                             data-toggle="tooltip" data-placement="top" title="Agregar filtro" id="filterContact" ng-class="{disabled: filterContact > 0}">Filtro</a>
                        </div>
                      </div>
                      <div class="row " style="margin-top: -2%">
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
                        <div class="col-lg-12" style="margin-left: -1%">
                          <span class="small-text">Listas seleccionadas</span> 
                        </div>
                      </div>
                      <div class="list-addresses-selector border-div-target" id='step1Content' >
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
                        <span class="small-text">Seleccionar una o varios segmentos</span>
                        <hr style="margin: 0; border-top: 1px solid #AFA3A3">
                      </md-card-title-text>
                    </md-card-title>
                    <md-card-content layout="row" layout-align="space-between">
                      <div class="row" style="margin-top: -1%">
                        <div class="inline-block" style="margin-right: 0; padding-right: 0">
                          <a ng-click='allSegment()'  class="button btn btn-xs info-inverted"
                             data-toggle="tooltip" data-placement="top" title="Limpiar">Seleccionar todos los segmentos</a>   
                        </div>
                        <div class="inline-block" style="margin-left: 0; padding-left: 0; margin-right: 0; padding-right: 0">
                          <a ng-click='clearSelect()'  class="button btn btn-xs warning-inverted"
                             data-toggle="tooltip" data-placement="top" title="Limpiar">Limpiar</a>
                        </div>
                      {#  <div class="inline-block" style="margin-left: 0; padding-left: 0;" ng-if="addressees.selectdSegment.length >= 1">
                          <a  ng-click='addFilter()'  class="button btn btn-xs success-inverted"
                              data-toggle="tooltip" data-placement="top" title="Agregar filtro">Filtro</a>
                        </div>#}
                      </div>

                      <div class="row " style="margin-top: -2%">
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
                      <md-radio-group ng-model="addressees.condition" ng-if="filters.length > 0" ng-change="selectActionSegment()">
                        <md-radio-button value="all" class="md-primary" ng-style="{'display':'inline'}">Todas</md-radio-button>
                        <md-radio-button value="some" class="md-primary" ng-style="{'display':'inline'}">Algunas</md-radio-button>
                      </md-radio-group>
                    </md-card-content>
                  </md-card>
                </div>
              </md-content>
            </div>

            <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4 margin-botton" ng-repeat="key in filters">
              <div class="sgm-left-arrow-border" ng-hide="$index == 1"></div>
              <div class="sgm-left-arrow" ng-hide="$index == 1"></div>
              <md-content class="none-scroll-horizontal md-content-select" layout-xs="column" layout="row" style="width: 100%">
                <div flex-xs flex-gt-xs="50" layout="column" class="">
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
                          repeat="value.idMail as value in addressees.filerMail | propsFilter: {name: $select.search}">
                          <div ng-bind-html="value.name | highlight: $select.search"></div>
                        </ui-select-choices>
                      </ui-select>
                      <div ng-if="key.typeFilters == 3">
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
                      <div>
                        <md-checkbox md-no-ink aria-label="Checkbox No Ink" ng-model="key.inverted" ng-change="selectinverted(key)" class="md-primary">
                          Invertir filtro
                        </md-checkbox>
                      </div>
                    </md-card-content>
                  </md-card>
                </div>
              </md-content>
            </div>
          </div>
          <div class="row margin-0-1" >
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 well margin-well">
              <span>Contactos aproximados: </span>     {{"{{addressees.count}}"}}
            </div>
          </div>
          <div class="row margin-0-1" ng-show="data.typeUnsuscribed == 1 ">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 well margin-well">
              <span>Categorías relacionadas: </span>     {{"{{categories}}"}}
            </div>
          </div>
        </div>

        <div class="footer" align="right">
          <a href="{{ url('mail') }}"
             class="button btn btn-small danger-inverted"
             data-toggle="tooltip" data-placement="top" title="Salir">
            Salir
          </a>
          <a ui-sref="describe({id:idMailGet})"
             class="button btn btn-small info-inverted"
             data-toggle="tooltip" data-placement="top" title="Atrás">
            Atrás
          </a>
          <button class="button btn btn-small primary-inverted"
                  data-toggle="tooltip" data-placement="top" title="Guardar y continuar">
            Guardar y continuar
          </button>
        </div>
      </div>
    </div>
  </form>
</div>
<script>

</script>
