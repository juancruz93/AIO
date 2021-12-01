<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="title">
      Nuevo plan de pago
    </div>            
    <hr class="basic-line">
    <p class="text-justify">
      Formulario para crear una plan de pago
    </p>
  </div>
</div>

<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8 wrap">
    <form data-ng-submit="savePaymentPlan()">
      <div class="block block-info">
        <div class="body row">
          <div class="col-md-12">
            <div class="body form-horizontal">
              <div class="form-group">
                <label for="idCountry" class="col-sm-3 control-label">*País</label>
                <div class="col-sm-9">
                  <select class="chosen form-control" data-placeholder="Seleccione un país" data-ng-model="data.idCountry" style="width: 100%" required="true" data-ng-change="loadTax()">
                    <option value=""></option>
                    <option ng-repeat="x in countries" value="{{"{{x.idCountry}}"}}">{{"{{x.name}}"}}</option>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label for="tax" class="col-sm-3 control-label">Impuestos</label>
                <div class="col-sm-9">
                  <select class="chosen form-control taxes" ng-disabled="!data.idCountry" data-placeholder="Selecciones los impuestos" data-ng-model="tax.idTax" multiple style="width: 100%">
                    <option value=""></option>
                    <option ng-repeat="x in listtax" value="{{"{{x.idTax}}"}}">{{"{{x.name}}"}}</option>
                  </select>
                  <h5 class="color-warning">Sólo se mostrarán los impuestos del país previamente seleccionado</h5>
                </div>
              </div>
              <div class="form-group">
                <label for="type" class="col-sm-3 control-label">{{form.label('type')}}</label>
                <div class="col-sm-9">
                  {{form.render('type')}}
                </div>
              </div>
              <div class="form-group" ng-if="{{user.Usertype.idAllied}}" ng-show="data.type == 'public'">
                <label for="status" class="col-sm-3 control-label">*Plan de cortesia</label>
                <div class="col-sm-9">
                  <md-switch class="md-warn none-margin"  md-no-ink aria-label="Switch No Ink" data-ng-model="data.courtesy" ng-change ="validatecourtesyplan()">
                  </md-switch>
                </div>
              </div>
              <div class="form-group">
                <label for="name" class="col-sm-3 control-label">{{form.label('name')}}</label>
                <div class="col-sm-9">
                  {{form.render('name')}}
                  <div class="text-right" data-ng-class="data.name.length > 40 ? 'negative':''">{{"{{data.name.length > 0 ?  data.name.length+'/40':''}}"}}</div>
                </div>
              </div>
              <div class="form-group">
                <label for="type" class="col-sm-3 control-label">{{form.label('diskSpace')}}</label>
                <div class="col-sm-9">
                  {{form.render('diskSpace')}}
                </div>
              </div>
              <div class="form-group">
                <label for="description" class="col-sm-3 control-label">{{form.label('description')}}</label>
                <div class="col-sm-9">
                  {{form.render('description')}}
                  <div class="text-right" data-ng-class="data.description.length > 100 ? 'negative':''">{{"{{data.description.length > 0 ?  data.description.length+'/100':''}}"}}</div>
                </div>
              </div>

              <div class="form-group">
                <label for="services" class="col-sm-3 control-label">*Servicios</label>
                <div class="col-sm-9">
                  <select class="chosen form-control taxes" data-placeholder="Selecciones los servicios" data-ng-model="service.idServices" multiple style="width: 100%" required="true" data-ng-change="changeService()">
                    <option value=""></option>
                    <option ng-repeat="x in listServices" value="{{"{{x.idServices}}"}}">{{"{{x.name}}"}}</option>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label for="status" class="col-sm-3 control-label">*Estado</label>
                <div class="col-sm-9">
                  <md-switch class="md-warn none-margin" md-no-ink aria-label="Switch No Ink" data-ng-model="data.status">
                  </md-switch>
                </div>
              </div>
              <div class="form-group" ng-if="data.courtesy == true">
                <i class="input hoshi input-default col-sm-9 col-md-9 float-right">Ej: <b>ej1@aio.com, ej3@aio.com, ej3@aio.com </b></i>
                <label class="col-sm-3 col-md-3 text-right">*Notificar a</label>
                <span class="input hoshi input-default col-sm-9 col-md-9">
                  <textarea class="undeline-input ng-valid ng-valid-maxlength ng-dirty ng-valid-parse ng-not-empty ng-touched" maxlength="500" rows="2" data-ng-model="data.email" style="margin: 0px; width: 346px; height: 104px;"></textarea></span>
              </div>

              <div class="form-group">
                <div class="col-sm-12 col-md-offset-3 col-md-9">
                  <!-- Nav tabs -->
                  {#<ul class="nav nav-tabs" role="tablist" id="myTabs" data-ng-show="hr">
                    <li role="presentation" data-ng-class="tabsms == true ? 'active':''" data-ng-show="tabsms"><a href="#sms" aria-controls="sms" role="tab" data-toggle="tab">SMS</a></li>
                    <li role="presentation" data-ng-class="tabemail == true ? tabsms == true  ? '':'active':''" data-ng-show="tabemail"><a href="#email_marketing" aria-controls="email_marketing" role="tab" data-toggle="tab">Email Marketing</a></li>
                    <li role="presentation" data-ng-class="tabmailtester == true ? tabsms == true || tabemail == true ? '':'active':''" data-ng-show="tabmailtester"><a href="#mailtester" aria-controls="mailtester" role="tab" data-toggle="tab">Mail Tester</a></li>
                  </ul>#}

                  <div ng-cloak>
                    <md-content>
                      <md-tabs md-dynamic-height md-border-bottom>
                        <md-tab label="SMS" data-ng-if="tabsms">
                          <md-content class="md-padding">
                            <br>
                            <div class="form-group">
                              <label for="plantype" class="col-sm-3 control-label">*Tipo de plan</label>
                              <div class="col-sm-9">
                                {#<select class="chosen form-control tsms" data-placeholder="Seleccione el tipo de plan" data-ng-model="sms.idPlanType" style="width: 100%">
                                  <option value=""></option>
                                  <option ng-repeat="x in plantypes" value="{{"{{x.idPlanType}}"}}">{{"{{x.name}}"}}</option>
                                </select>#}
                                <ui-select data-ng-model="sms.idPlanType" theme="select2" style="text-align: left; width: 100%;z-index: 2 !important;" title="Seleccione el tipo de plan">
                                  <ui-select-match placeholder="Seleccione el tipo de plan">{{"{{$select.selected.name}}"}}</ui-select-match>
                                  <ui-select-choices repeat="item.idPlanType as item in plantypes | filter: $select.search" refresh="search(2,$select.search)">
                                    <div ng-bind-html="item.name | highlight: $select.search" style="text-align: left"></div>
                                  </ui-select-choices>
                                </ui-select>
                              </div>
                            </div>
                            <div class="form-group">
                              <label for="pricelist" class="col-sm-3 control-label">*Listas de precios</label>
                              <div class="col-sm-9">
                                <ui-select data-ng-model="sms.idPriceList" theme="select2" style="text-align: left; width: 100%" title="Seleccione una lista de precios">
                                  <ui-select-match placeholder="Debe seleccionar una lista de precios">{{"{{$select.selected.name}}"}}</ui-select-match>
                                  <ui-select-choices repeat="item.idPriceList as item in listpricelistsms | filter: $select.search" refresh="search(1,$select.search)">
                                    <div ng-bind-html="item.name | highlight: $select.search" style="text-align: left"></div>
                                  </ui-select-choices>
                                </ui-select>
                              </div>
                            </div>
                            <div class="form-group">
                              <label for="adapter" class="col-sm-3 control-label">*Adaptador</label>
                              <div class="col-sm-9">
                                <ui-select multiple data-ng-model="sms.idAdapter" theme="bootstrap" close-on-select="true" style="width: 100%">
                                  <ui-select-match placeholder="Seleccione un adaptador">{{"{{$item.name}}"}}</ui-select-match>
                                  <ui-select-choices repeat="key.idAdapter as key in listadapter | propsFilter: {name: $select.search}">
                                    <div ng-bind-html="key.name | highlight: $select.search"></div>
                                  </ui-select-choices>
                                </ui-select>
                              </div>
                            </div>
                            <div class="form-group">
                              <label for="amount" class="col-sm-3 control-label">*Cantidad</label>
                              <div class="col-sm-9">
                                <input type="number" class="undeline-input form-control tsms" min="1" placeholder="Cantidad" data-ng-model="sms.amount"/>
                              </div>
                            </div>
                            <div class="form-group">
                              <label for="velocity" class="col-sm-3 control-label">*Velocidad</label>
                              <div class="col-sm-9">
                                <input type="number" class="undeline-input form-control tsms" min="1" max="100" placeholder="Velocidad por minuto" data-ng-model="sms.speed"/>
                              </div>
                            </div>
                          </md-content>
                        </md-tab>

                        <md-tab label="SMS doble via" data-ng-if="tabsmstwoway">
                          <md-content class="md-padding">
                            <br>
                            <div class="form-group">
                              <label for="plantype" class="col-sm-3 control-label">*Tipo de plan</label>
                              <div class="col-sm-9">
                                {#<select class="chosen form-control tsms" data-placeholder="Seleccione el tipo de plan" data-ng-model="sms.idPlanType" style="width: 100%">
                                  <option value=""></option>
                                  <option ng-repeat="x in plantypes" value="{{"{{x.idPlanType}}"}}">{{"{{x.name}}"}}</option>
                                </select>#}
                                <ui-select data-ng-model="smstwoway.idPlanType" theme="select2" style="text-align: left; width: 100%;z-index: 2 !important;" title="Seleccione el tipo de plan">
                                  <ui-select-match placeholder="Seleccione el tipo de plan">{{"{{$select.selected.name}}"}}</ui-select-match>
                                  <ui-select-choices repeat="item.idPlanType as item in plantypes | filter: $select.search" refresh="search(2,$select.search)">
                                    <div ng-bind-html="item.name | highlight: $select.search" style="text-align: left"></div>
                                  </ui-select-choices>
                                </ui-select>
                              </div>
                            </div>
                            <div class="form-group">
                              <label for="pricelist" class="col-sm-3 control-label">*Listas de precios</label>
                              <div class="col-sm-9">
                                <ui-select data-ng-model="smstwoway.idPriceList" theme="select2" style="text-align: left; width: 100%" title="Seleccione una lista de precios">
                                  <ui-select-match placeholder="Debe seleccionar una lista de precios">{{"{{$select.selected.name}}"}}</ui-select-match>
                                  <ui-select-choices repeat="item.idPriceList as item in listpricelistsmstwoway | filter: $select.search" refresh="search(7,$select.search)">
                                    <div ng-bind-html="item.name | highlight: $select.search" style="text-align: left"></div>
                                  </ui-select-choices>
                                </ui-select>
                              </div>
                            </div>
                            {#<div class="form-group">
                              <label for="adapter" class="col-sm-3 control-label">*Adaptador</label>
                              <div class="col-sm-9">
                                <ui-select multiple data-ng-model="sms.idAdapter" theme="bootstrap" close-on-select="true" style="width: 100%">
                                  <ui-select-match placeholder="Seleccione un adaptador">{{"{{$item.name}}"}}</ui-select-match>
                                  <ui-select-choices repeat="key.idAdapter as key in listadapter | propsFilter: {name: $select.search}">
                                    <div ng-bind-html="key.name | highlight: $select.search"></div>
                                  </ui-select-choices>
                                </ui-select>
                              </div>
                            </div>#}
                            <div class="form-group">
                              <label for="amount" class="col-sm-3 control-label">*Cantidad</label>
                              <div class="col-sm-9">
                                <input type="number" class="undeline-input form-control tsms" min="1" placeholder="Cantidad" data-ng-model="smstwoway.amount"/>
                              </div>
                            </div>
                            {#<div class="form-group">
                              <label for="velocity" class="col-sm-3 control-label">*Velocidad</label>
                              <div class="col-sm-9">
                                <input type="number" class="undeline-input form-control tsms" min="1" max="100" placeholder="Velocidad por minuto" data-ng-model="sms.speed"/>
                              </div>
                            </div>#}
                          </md-content>
                        </md-tab>    
                          
                        <md-tab label="Landing Page" data-ng-if="tablandingpage">
                          <md-content class="md-padding">
                            <br>
                            <div class="form-group">
                              <label for="plantype" class="col-sm-3 control-label">*Tipo de plan</label>
                              <div class="col-sm-9">
                                {#<select class="chosen form-control tsms" data-placeholder="Seleccione el tipo de plan" data-ng-model="sms.idPlanType" style="width: 100%">
                                  <option value=""></option>
                                  <option ng-repeat="x in plantypes" value="{{"{{x.idPlanType}}"}}">{{"{{x.name}}"}}</option>
                                </select>#}
                                <ui-select data-ng-model="landingpage.idPlanType" theme="select2" style="text-align: left; width: 100%;z-index: 2 !important;" title="Seleccione el tipo de plan">
                                  <ui-select-match placeholder="Seleccione el tipo de plan">{{"{{$select.selected.name}}"}}</ui-select-match>
                                  <ui-select-choices repeat="item.idPlanType as item in plantypes | filter: $select.search" refresh="search(2,$select.search)">
                                    <div ng-bind-html="item.name | highlight: $select.search" style="text-align: left"></div>
                                  </ui-select-choices>
                                </ui-select>
                              </div>
                            </div>
                            <div class="form-group">
                              <label for="pricelist" class="col-sm-3 control-label">*Listas de precios</label>
                              <div class="col-sm-9">
                                <ui-select data-ng-model="landingpage.idPriceList" theme="select2" style="text-align: left; width: 100%" title="Seleccione una lista de precios">
                                  <ui-select-match placeholder="Debe seleccionar una lista de precios">{{"{{$select.selected.name}}"}}</ui-select-match>
                                  <ui-select-choices repeat="item.idPriceList as item in listpricelistlandingpage | filter: $select.search" refresh="search(8,$select.search)">
                                    <div ng-bind-html="item.name | highlight: $select.search" style="text-align: left"></div>
                                  </ui-select-choices>
                                </ui-select>
                              </div>
                            </div>
                            {#<div class="form-group">
                              <label for="adapter" class="col-sm-3 control-label">*Adaptador</label>
                              <div class="col-sm-9">
                                <ui-select multiple data-ng-model="sms.idAdapter" theme="bootstrap" close-on-select="true" style="width: 100%">
                                  <ui-select-match placeholder="Seleccione un adaptador">{{"{{$item.name}}"}}</ui-select-match>
                                  <ui-select-choices repeat="key.idAdapter as key in listadapter | propsFilter: {name: $select.search}">
                                    <div ng-bind-html="key.name | highlight: $select.search"></div>
                                  </ui-select-choices>
                                </ui-select>
                              </div>
                            </div>#}
                            <div class="form-group">
                              <label for="amount" class="col-sm-3 control-label">*Cantidad</label>
                              <div class="col-sm-9">
                                <input type="number" class="undeline-input form-control tsms" min="1" placeholder="Cantidad" data-ng-model="landingpage.amount"/>
                              </div>
                            </div>
                            {#<div class="form-group">
                              <label for="velocity" class="col-sm-3 control-label">*Velocidad</label>
                              <div class="col-sm-9">
                                <input type="number" class="undeline-input form-control tsms" min="1" max="100" placeholder="Velocidad por minuto" data-ng-model="sms.speed"/>
                              </div>
                            </div>#}
                          </md-content>
                        </md-tab>                          
                          
                        <md-tab label="Email Marketing" data-ng-if="tabemail">
                          <md-content class="md-padding">
                            <div class="form-group">
                              <label for="plantype" class="col-sm-3 control-label">*Tipo de plan</label>
                              <div class="col-sm-9">
                                {#<select class="chosen form-control" data-placeholder="Seleccione el tipo de plan" data-ng-model="email.idPlanType" style="width: 100%">
                                  <option value=""></option>
                                  <option ng-repeat="x in plantypes" value="{{"{{x.idPlanType}}"}}">{{"{{x.name}}"}}</option>
                                </select>#}
                                <ui-select data-ng-model="email.idPlanType" theme="select2" style="text-align: left; width: 100%;z-index: 2 !important;" title="Seleccione el tipo de plan">
                                  <ui-select-match placeholder="Seleccione el tipo de plan">{{"{{$select.selected.name}}"}}</ui-select-match>
                                  <ui-select-choices repeat="item.idPlanType as item in plantypes | filter: $select.search" refresh="search(2,$select.search)">
                                    <div ng-bind-html="item.name | highlight: $select.search" style="text-align: left"></div>
                                  </ui-select-choices>
                                </ui-select>
                              </div>
                            </div>
                            <div class="form-group">
                              <label for="pricelist" class="col-sm-3 control-label">*Listas de precios</label>
                              <div class="col-sm-9">
                                <ui-select data-ng-model="email.idPriceList" theme="select2" style="text-align: left; width: 100%;" title="Seleccione una lista de precios">
                                  <ui-select-match placeholder="Debe seleccione una lista de precios">{{"{{$select.selected.name}}"}}</ui-select-match>
                                  <ui-select-choices repeat="item.idPriceList as item in listpricelistemail | filter: $select.search" refresh="search(2,$select.search)">
                                    <div ng-bind-html="item.name | highlight: $select.search" style="text-align: left"></div>
                                  </ui-select-choices>
                                </ui-select>
                              </div>
                            </div>
                            {% if user.Usertype.name == "root" %}
                              <div class="form-group">
                                <label for="accountingMode" class="col-sm-3 control-label">*Modo</label>
                                <div class="col-sm-9">
                                  <ui-select data-ng-model="email.accountingMode" theme="select2" style="text-align: left; width: 100%;z-index: 1 !important;" title="Seleccione una lista de precios">
                                    <ui-select-match placeholder="Debe seleccione el modo">{{"{{$select.selected.name}}"}}</ui-select-match>
                                    <ui-select-choices repeat="item.value as item in listaccountingMode | filter: $select.search">
                                      <div ng-bind-html="item.name | highlight: $select.search" style="text-align: left"></div>
                                    </ui-select-choices>
                                  </ui-select>
                                </div>
                              </div>
                            {% endif %}
                            <div class="form-group">
                              <label for="mta" class="col-sm-3 control-label">*Mta</label>
                              <div class="col-sm-9">
                                <ui-select multiple data-ng-model="email.idMta" theme="bootstrap" close-on-select="true">
                                  <ui-select-match placeholder="Seleccione el MTA">{{"{{$item.name}}"}}</ui-select-match>
                                  <ui-select-choices repeat="key.idMta as key in listmta | propsFilter: {name: $select.search}">
                                    <div ng-bind-html="key.name | highlight: $select.search"></div>
                                  </ui-select-choices>
                                </ui-select>
                              </div>
                            </div>
                            <div class="form-group">
                              <label for="urldomain" class="col-sm-3 control-label">*Urldomain</label>
                              <div class="col-sm-9">
                                <ui-select multiple data-ng-model="email.idUrldomain" theme="bootstrap" close-on-select="true">
                                  <ui-select-match placeholder="Seleccione el Urldomain">{{"{{$item.name}}"}}</ui-select-match>
                                  <ui-select-choices repeat="key.idUrldomain as key in listurldomain | propsFilter: {name: $select.search}">
                                    <div ng-bind-html="key.name | highlight: $select.search"></div>
                                  </ui-select-choices>
                                </ui-select>
                              </div>
                            </div>
                            <div class="form-group">
                              <label for="mailclass" class="col-sm-3 control-label">*Mailclass</label>
                              <div class="col-sm-9">
                                <ui-select multiple data-ng-model="email.idMailClass" theme="bootstrap" close-on-select="true">
                                  <ui-select-match placeholder="Seleccione el MailClass">{{"{{$item.name}}"}}</ui-select-match>
                                  <ui-select-choices repeat="key.idMailClass as key in listmailclass | propsFilter: {name: $select.search}">
                                    <div ng-bind-html="key.name | highlight: $select.search"></div>
                                  </ui-select-choices>
                                </ui-select>
                              </div>
                            </div>
                            <div class="form-group">
                              <label for="accountingMode" class="col-sm-3 control-label">*Modalidad</label>
                              <div class="col-sm-9">
                                <ui-select data-ng-model="email.accountingMode" theme="select2" style="text-align: left; width: 100%;z-index: 2 !important;" title="Seleccione el tipo de plan">
                                  <ui-select-match placeholder="Seleccione el tipo de plan">{{"{{$select.selected.name}}"}}</ui-select-match>
                                  <ui-select-choices repeat="item.value as item in listaccountingMode | filter: $select.search" refresh="search(2,$select.search)">
                                    <div ng-bind-html="item.name | highlight: $select.search" style="text-align: left"></div>
                                  </ui-select-choices>
                                </ui-select>
                              </div>
                            </div>
                            <div class="form-group">
                              <label for="amount" class="col-sm-3 control-label">*Cantidad</label>
                              <div class="col-sm-9">
                                <input type="number" class="undeline-input form-control" min="1" placeholder="Cantidad" data-ng-model="email.amount"/>
                              </div>
                            </div>
                          </md-content>
                        </md-tab>
                        <md-tab label="Mail Tester" data-ng-if="tabmailtester">
                          <md-content class="md-padding">
                            <div class="form-group">
                              <label for="pricelist" class="col-sm-3 control-label">*Listas de precios</label>
                              <div class="col-sm-9">
                                <ui-select data-ng-model="mailtester.idPriceList" theme="select2" style="text-align: left; width: 100%" title="Seleccione una lista de precios">
                                  <ui-select-match placeholder="Debe seleccionar una lista de precios">{{"{{$select.selected.name}}"}}</ui-select-match>
                                  <ui-select-choices repeat="item.idPriceList as item in listpricelistmailterster | filter: $select.search" refresh="search(3,$select.search)">
                                    <div ng-bind-html="item.name | highlight: $select.search" style="text-align: left"></div>
                                  </ui-select-choices>
                                </ui-select>
                              </div>
                            </div>
                            <div class="form-group">
                              <label for="amount" class="col-sm-3 control-label">*Cantidad</label>
                              <div class="col-sm-9">
                                <input type="number" class="undeline-input form-control" min="1" placeholder="Cantidad" data-ng-model="mailtester.amount"/>
                              </div>
                            </div>
                          </md-content>
                        </md-tab>
                        <md-tab label="Adjuntar archivos" data-ng-if="tabattachment">
                          <md-content class="md-padding">
                            <div class="form-group">
                              <label for="pricelist" class="col-sm-3 control-label">*Listas de precios</label>
                              <div class="col-sm-9 ">
                                <ui-select data-ng-model="attachment.idPriceList" theme="select2" style="text-align: left; width: 100%; margin-bottom: 50px;" title="Seleccione una lista de precios">
                                  <ui-select-match placeholder="Debe seleccionar una lista de precios">{{"{{$select.selected.name}}"}}</ui-select-match>
                                  <ui-select-choices repeat="item.idPriceList as item in listpricelistattachment | filter: $select.search" refresh="search(6,$select.search)">
                                    <div ng-bind-html="item.name | highlight: $select.search" style="text-align: left"></div>
                                  </ui-select-choices>
                                </ui-select>
                              </div>
                            </div>
                          </md-content>
                        </md-tab>
                        <md-tab label="Encuestas" data-ng-if="tabsurvey">
                          <md-content class="md-padding">
                            <div class="form-group">
                              <label for="pricelist" class="col-sm-3 control-label">*Listas de precios</label>
                              <div class="col-sm-9">
                                <ui-select data-ng-model="survey.idPriceList" theme="select2" style="text-align: left; width: 100%;" title="Seleccione una lista de precios">
                                  <ui-select-match placeholder="Debe seleccione una lista de precios">{{"{{$select.selected.name}}"}}</ui-select-match>
                                  <ui-select-choices repeat="item.idPriceList as item in listpricelistsurvey | filter: $select.search" refresh="search(5,$select.search)">
                                    <div ng-bind-html="item.name | highlight: $select.search" style="text-align: left"></div>
                                  </ui-select-choices>
                                </ui-select>
                              </div>
                            </div>
                            <div class="form-group">
                              <label for="mta" class="col-sm-3 control-label">*Mta</label>
                              <div class="col-sm-9">
                                <ui-select multiple data-ng-model="survey.idMta" theme="bootstrap" close-on-select="true">
                                  <ui-select-match placeholder="Seleccione el MTA">{{"{{$item.name}}"}}</ui-select-match>
                                  <ui-select-choices repeat="key.idMta as key in listmta | propsFilter: {name: $select.search}">
                                    <div ng-bind-html="key.name | highlight: $select.search"></div>
                                  </ui-select-choices>
                                </ui-select>
                              </div>
                            </div>
                            <div class="form-group">
                              <label for="urldomain" class="col-sm-3 control-label">*Urldomain</label>
                              <div class="col-sm-9">
                                <ui-select multiple data-ng-model="survey.idUrldomain" theme="bootstrap" close-on-select="true">
                                  <ui-select-match placeholder="Seleccione el Urldomain">{{"{{$item.name}}"}}</ui-select-match>
                                  <ui-select-choices repeat="key.idUrldomain as key in listurldomain | propsFilter: {name: $select.search}">
                                    <div ng-bind-html="key.name | highlight: $select.search"></div>
                                  </ui-select-choices>
                                </ui-select>
                              </div>
                            </div>
                            <div class="form-group">
                              <label for="mailclass" class="col-sm-3 control-label">*Mailclass</label>
                              <div class="col-sm-9">
                                <ui-select multiple data-ng-model="survey.idMailClass" theme="bootstrap" close-on-select="true">
                                  <ui-select-match placeholder="Seleccione el MailClass">{{"{{$item.name}}"}}</ui-select-match>
                                  <ui-select-choices repeat="key.idMailClass as key in listmailclass | propsFilter: {name: $select.search}">
                                    <div ng-bind-html="key.name | highlight: $select.search"></div>
                                  </ui-select-choices>
                                </ui-select>
                              </div>
                            </div>
                            <div class="form-group">
                              <label for="amount" class="col-sm-3 control-label">*Cantidad de preguntas</label>
                              <div class="col-sm-9">
                                <input type="number" class="undeline-input form-control" min="1" placeholder="Cantidad de preguntas" data-ng-model="survey.amountQuestion"/>
                              </div>
                            </div>
                            <div class="form-group">
                              <label for="amount" class="col-sm-3 control-label">*Cantidad de respuestas</label>
                              <div class="col-sm-9">
                                <input type="number" class="undeline-input form-control" min="1" placeholder="Cantidad de respuestas" data-ng-model="survey.amountAnswer"/>
                              </div>
                            </div>
                          </md-content>
                        </md-tab>
                      </md-tabs>
                    </md-content>
                  </div>

                  <!-- Tab panes -->
                  {#<div class="tab-content">
                    <!-- PRIMER TAB -->
                    <div role="tabpanel" class="tab-pane fade" data-ng-class="tabsms == true ? 'in active':''" id="sms">
                      <br>
                      <div class="form-group">
                        <label for="plantype" class="col-sm-3 control-label">*Tipo de plan</label>
                        <div class="col-sm-9">
                          <select class="chosen form-control tsms" data-placeholder="Seleccione el tipo de plan" data-ng-model="sms.idPlanType" style="width: 100%">
                            <option value=""></option>
                            <option ng-repeat="x in plantypes" value="{{"{{x.idPlanType}}"}}">{{"{{x.name}}"}}</option>
                          </select>
                        </div>
                      </div>
                      <div class="form-group">
                        <label for="pricelist" class="col-sm-3 control-label">*Listas de precios</label>
                        <div class="col-sm-9">
                          <ui-select data-ng-model="sms.idPriceList" theme="select2" style="text-align: left; width: 100%" title="Seleccione una lista de precios">
                            <ui-select-match placeholder="Debe seleccionar una lista de precios">{{"{{$select.selected.name}}"}}</ui-select-match>
                            <ui-select-choices repeat="item.idPriceList as item in listpricelistsms | filter: $select.search" refresh="search(1,$select.search)">
                              <div ng-bind-html="item.name | highlight: $select.search" style="text-align: left"></div>
                            </ui-select-choices>
                          </ui-select>
                        </div>
                      </div>
                      <div class="form-group">
                        <label for="adapter" class="col-sm-3 control-label">*Adaptador</label>
                        <div class="col-sm-9">
                          <ui-select multiple data-ng-model="sms.idAdapter" theme="bootstrap" close-on-select="true" style="width: 100%">
                            <ui-select-match placeholder="Seleccione un adaptador">{{"{{$item.name}}"}}</ui-select-match>
                            <ui-select-choices repeat="key.idAdapter as key in listadapter | propsFilter: {name: $select.search}">
                              <div ng-bind-html="key.name | highlight: $select.search"></div>
                            </ui-select-choices>
                          </ui-select>
                        </div>
                      </div>
                      <div class="form-group">
                        <label for="amount" class="col-sm-3 control-label">*Cantidad</label>
                        <div class="col-sm-9">
                          <input type="number" class="undeline-input form-control tsms" min="1" placeholder="Cantidad" data-ng-model="sms.amount"/>
                        </div>
                      </div>
                      <div class="form-group">
                        <label for="velocity" class="col-sm-3 control-label">*Velocidad</label>
                        <div class="col-sm-9">
                          <input type="number" class="undeline-input form-control tsms" min="1" max="100" placeholder="Velocidad por minuto" data-ng-model="sms.speed"/>
                        </div>
                      </div>
                    </div>

                    <!-- SEGUNDO TAB -->
                    <div role="tabpanel" class="tab-pane fade" data-ng-class="tabemail == true ? tabsms == true  ? '':'in active':''" id="email_marketing">
                      <br>
                      <div class="form-group">
                        <label for="plantype" class="col-sm-3 control-label">*Tipo de plan</label>
                        <div class="col-sm-9">
                          <select class="chosen form-control" data-placeholder="Seleccione el tipo de plan" data-ng-model="email.idPlanType" style="width: 100%">
                            <option value=""></option>
                            <option ng-repeat="x in plantypes" value="{{"{{x.idPlanType}}"}}">{{"{{x.name}}"}}</option>
                          </select>
                        </div>
                      </div>
                      <div class="form-group">
                        <label for="pricelist" class="col-sm-3 control-label">*Listas de precios</label>
                        <div class="col-sm-9">
                          <ui-select data-ng-model="email.idPriceList" theme="select2" style="text-align: left; width: 100%;z-index: 2 !important;" title="Seleccione una lista de precios">
                            <ui-select-match placeholder="Debe seleccione una lista de precios">{{"{{$select.selected.name}}"}}</ui-select-match>
                            <ui-select-choices repeat="item.idPriceList as item in listpricelistemail | filter: $select.search" refresh="search(2,$select.search)">
                              <div ng-bind-html="item.name | highlight: $select.search" style="text-align: left"></div>
                            </ui-select-choices>
                          </ui-select>
                        </div>
                      </div>
                      {% if user.Usertype.name == "root" %}
                        <div class="form-group">
                          <label for="accountingMode" class="col-sm-3 control-label">*Modo</label>
                          <div class="col-sm-9">
                            <ui-select data-ng-model="email.accountingMode" theme="select2" style="text-align: left; width: 100%;z-index: 1 !important;" title="Seleccione una lista de precios">
                              <ui-select-match placeholder="Debe seleccione el modo">{{"{{$select.selected.name}}"}}</ui-select-match>
                              <ui-select-choices repeat="item.value as item in listaccountingMode | filter: $select.search">
                                <div ng-bind-html="item.name | highlight: $select.search" style="text-align: left"></div>
                              </ui-select-choices>
                            </ui-select>
                          </div>
                        </div>
                      {% endif %}
                      <div class="form-group">
                        <label for="mta" class="col-sm-3 control-label">*Mta</label>
                        <div class="col-sm-9">
                          <ui-select multiple data-ng-model="email.idMta" theme="bootstrap" close-on-select="true">
                            <ui-select-match placeholder="Seleccione el MTA">{{"{{$item.name}}"}}</ui-select-match>
                            <ui-select-choices repeat="key.idMta as key in listmta | propsFilter: {name: $select.search}">
                              <div ng-bind-html="key.name | highlight: $select.search"></div>
                            </ui-select-choices>
                          </ui-select>
                        </div>
                      </div>
                      <div class="form-group">
                        <label for="urldomain" class="col-sm-3 control-label">*Urldomain</label>
                        <div class="col-sm-9">
                          <ui-select multiple data-ng-model="email.idUrldomain" theme="bootstrap" close-on-select="true">
                            <ui-select-match placeholder="Seleccione el Urldomain">{{"{{$item.name}}"}}</ui-select-match>
                            <ui-select-choices repeat="key.idUrldomain as key in listurldomain | propsFilter: {name: $select.search}">
                              <div ng-bind-html="key.name | highlight: $select.search"></div>
                            </ui-select-choices>
                          </ui-select>
                        </div>
                      </div>
                      <div class="form-group">
                        <label for="mailclass" class="col-sm-3 control-label">*Mailclass</label>
                        <div class="col-sm-9">
                          <ui-select multiple data-ng-model="email.idMailClass" theme="bootstrap" close-on-select="true">
                            <ui-select-match placeholder="Seleccione el MailClass">{{"{{$item.name}}"}}</ui-select-match>
                            <ui-select-choices repeat="key.idMailClass as key in listmailclass | propsFilter: {name: $select.search}">
                              <div ng-bind-html="key.name | highlight: $select.search"></div>
                            </ui-select-choices>
                          </ui-select>
                        </div>
                      </div>
                      <div class="form-group">
                        <label for="amount" class="col-sm-3 control-label">*Cantidad</label>
                        <div class="col-sm-9">
                          <input type="number" class="undeline-input form-control" min="1" placeholder="Cantidad" data-ng-model="email.amount"/>
                        </div>
                      </div>
                    </div>
                    <!-- FIN SEGUNDO TAB -->
                    <!-- TERCER TAB -->
                    <div role="tabpanel" class="tab-pane fade" data-ng-class="tabmailtester == true ? tabsms == true || tabemail == true ? '':'in active':''" id="mailtester">
                      <br>
                      <div class="form-group">
                        <label for="pricelist" class="col-sm-3 control-label">*Listas de precios</label>
                        <div class="col-sm-9">
                          <ui-select data-ng-model="mailtester.idPriceList" theme="select2" style="text-align: left; width: 100%" title="Seleccione una lista de precios">
                            <ui-select-match placeholder="Debe seleccionar una lista de precios">{{"{{$select.selected.name}}"}}</ui-select-match>
                            <ui-select-choices repeat="item.idPriceList as item in listpricelistmailterster | filter: $select.search" refresh="search(3,$select.search)">
                              <div ng-bind-html="item.name | highlight: $select.search" style="text-align: left"></div>
                            </ui-select-choices>
                          </ui-select>
                        </div>
                      </div>
                      <div class="form-group">
                        <label for="amount" class="col-sm-3 control-label">*Cantidad</label>
                        <div class="col-sm-9">
                          <input type="number" class="undeline-input form-control" min="1" placeholder="Cantidad" data-ng-model="mailtester.amount"/>
                        </div>
                      </div>
                    </div>
                    <!-- FIN TERCER TAB -->
                  </div>#}
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="footer text-right">
          <button type="submit" class="button shining btn btn-xs-round shining shining-round round-button success-inverted" data-toggle="tooltip" data-placement="top" title="Guardar">
            <i class="fa fa-check"></i>
          </button>
          <a href="{{url('paymentplan#/')}}" class="button shining btn btn-xs-round shining shining-round round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Cancelar">
            <i class="fa fa-times"></i>
          </a>
        </div>
      </div>
    </form>
  </div>
  <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4 wrap">                            
    <div class="fill-block fill-block-info">
      <div class="header">
        Información
      </div>
      <div class="body">
        <p>Recuerde tener en cuenta estas recomendaciones:</p>
        <ul>                            
          <li><p>Debe seleccionar un país donde quiere mostrar el plan de pago.</p></li>
          <li><p>Debe seleccionar un tipo el cual será público o privado.</p></li>
          <li><p>El nombre debe tener al menos 2 y máximo 40 caracteres.</p></li>
          <li><p>El espacio en disco debe ser un número en entero el cual representa la cantidad en MegaBytes (MB).</p></li>
          <li><p>La descripción debe tener al menos 2 y máximo 100 caracteres</p></li>
          <li><p>El listado de impuestos para seleccionar, será de acuerdo al país que se haya seleccionado anteriormente.</p></li>
          <li><p>Debe seleccionar al menos un servicio el cual desplegará un formulario para hacer su respectiva configuración</p></li>
          <li><p>Debe seleccionar un estado el cual podrá se activo o inactivo, por defecto el sistema lo pondrá activo.</p></li>
        </ul> 
        <p></p>
      </div>
      <div class="footer">
        Creación
      </div>
    </div>     
  </div>
</div>

<script>
  $(function () {
    setTimeout(function () {
      $('[data-toggle="tooltip"]').tooltip();
    }, 1000);

    $(".chosen").select2({
      theme: 'classic'
    });

    $('#myTabs a').click(function (e) {
      e.preventDefault();
      $(this).tab('show');
    });
  });
  $('.taxes').select2({
    language: {
      noResults: function (params) {
        return "El país seleccionado no tiene impuestos asociados";
      }
    }
  });
  $(".norm").select2();
</script>
