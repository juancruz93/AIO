
<div class="row" ng-controller="createdcontact">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="title">
       {{'{{misc.title}}'}}
    </div>
    <hr class="basic-line"/>
  </div>
</div>

<div class="row" ng-app="aio" ng-controller="createdcontact">
  <form method="post" ng-submit="functions.validate()" class="form-horizontal" >
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">
      <div class="block block-info">          
        <div class="body " >
          <div class="row">
            
            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label class="col-sm-4 col-md-4 text-right">*Nombre de envío:</label>
                <span class="input hoshi input-default col-sm-8 col-md-8">
                  <input type="text" required="required" class="form-control ng-pristine ng-valid ng-empty ng-valid-minlength ng-valid-maxlength ng-touched" placeholder="*Nombre" minlength="5" maxlength="50" ng-model="data.name">
                </span>
              </div>
            </div>
            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label class="col-sm-4 col-md-4 text-right">*Categoría:</label>
                <span class="input-default col-sm-8 col-md-8">
                  <select class="form-control" required="required"  ng-options="category.idSmsCategory as category.name for category in misc.listCategory" ng-model="data.idSmsCategory">
                    <option></option>
                  </select>
                </span>
              </div>
            </div>
            <div class="form-group" >
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label class="col-sm-4 col-md-4 text-left">*¿Enviar ahora mismo?:</label>
                <span class="input hoshi input-default col-sm-1 " >
                  {#                  <input type="checkbox" class="toggle-sms-two-way" id="toggle-two" ng-model="data.sendnow" ng-click="functions.sentNow()"/>#}
                  <div class="onoffswitch">
                    <input type="checkbox" name="sentNow" ng-model="data.sentNow" class="onoffswitch-checkbox" id="sentNow">
                    <label class="onoffswitch-label" for="sentNow">
                      <span class="onoffswitch-inner"></span>
                      <span class="onoffswitch-switch"></span>
                    </label>
                  </div>
                </span>
              </div>
            </div>
            <div class="form-group" ng-show="!data.sentNow">     
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label class="col-sm-4 col-md-4 text-right">*Zona horaria:</label>
                <span class="input-default col-sm-8 col-md-8">
                  <select class="form-control" {#required="required"#} ng-model="data.timezone"  ng-options = "gmt.gmt as gmt.countries for gmt in misc.listTimezone">
                    <option></option>
                  </select>
                </span>
              </div>
            </div>
            <div class="form-group" ng-show="!data.sentNow">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label class="col-sm-4 col-md-4 text-left">*Fecha y hora de envio:</label>
                <span class="input-append date  col-sm-8 col-md-8 input-group datetimepicker"  style="padding-left: 15px; padding-right: 15px;">
                  <input type='text' {#required="required"#} ng-model="data.startdate"  class="undeline-input"  id="datesend"/>
                  <span class="add-on input-group-addon">
                    <i data-time-icon="glyphicon glyphicon-time" data-date-icon="glyphicon glyphicon-calendar"></i>
                  </span>
                </span>
              </div>
            </div>
            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap"> 
                <label class="col-sm-4 col-md-4 text-right">*Lista de destinatario(s):</label>
                <span class="input-default col-sm-8 col-md-8">
                  <select class="form-control" required="required"  ng-options="listAddressee.id as listAddressee.name for listAddressee in misc.listAddressee" ng-model="data.listSelected" ng-change="restServicesFunction.getDetinatary(data.listSelected)">
                    <option></option>
                  </select>
                </span>
              </div>
            </div>
            <div class="form-group" ng-show="data.listSelected">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap"> 
                <label class="col-sm-4 col-md-4 text-right">*Destinatario(s):</label>
                <span class="input hoshi input-default col-sm-8 col-md-8">
                  <ui-select multiple ng-model="data.arrAddressee"  ng-required="required" ng-required="true" ui-select-required class='min-width-100'
                             theme="select2" title="" sortable="false" close-on-select="true" ng-change="functions.countContacts()">
                    <ui-select-match >{{"{{$item.name}}"}}</ui-select-match>
                    <ui-select-choices repeat="key as key in misc.listAllAddressee | propsFilter: {name: $select.search}">
                      <div ng-bind-html="key.name | highlight: $select.search"></div>
                    </ui-select-choices>
                  </ui-select>
                </span>
              </div>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap"> 
                <label class="col-sm-4 col-md-4 text-right">Destinatarios aproximados:</label>
                <span class="input hoshi input-default col-sm-8 col-md-8">
                  {{"{{countContactsApproximate.counts }}"}}
                </span>
              </div>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap"> 
                <label class="col-sm-4 col-md-4 text-right">Etiquetas:</label>
                <span class="input hoshi input-default col-sm-8 col-md-8">
                  <table id="customers">
                    <tbody>
                      <tr  ng-show="countContactsApproximate.counts">
                        <th>Campo</th>
                        <th>Etiqueta</th>
                      </tr>
                      <tr>
                        <td colspan="2" style="text-align: center;" ng-show="!countContactsApproximate.counts">
                          No hay etiquetas disponibles. Seleccione una lista de contactos o segmento con al menos un contacto.
                        </td>
                      </tr>
                      <tr  class="alt" ng-repeat="(key, value) in countContactsApproximate.tags"  ng-show="countContactsApproximate.counts">
                        <td>{{"{{value.name}}"}}</td>
                        <td ng-click="functions.addTag(value.tag)" style="cursor: pointer;">{{"{{value.tag}}"}}</td>
                      </tr>
                    </tbody></table>
                </span>
              </div>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg wrap">
                <label class="col-sm-4 col-md-4 text-right">¿Usar plantillas?</label>
                <span class="input hoshi input-default col-sm-1 col-md-1" {#ng-click="functions.smstemplate(viewTemplate)"#}>
                  <span class="input hoshi input-default col-sm-8 col-md-8 " >
                    {#                    <input type="checkbox" class="toggle-sms-two-way" ng-model="data.smstemplate" {#ng-click="functions.smstemplate(viewTemplate)"/>#}
                    <div class="onoffswitch">
                      <input type="checkbox" name="sentNow" ng-model="data.smstemplate" ng-change="functions.smstemplate()" class="onoffswitch-checkbox" id="smstemplate">
                      <label class="onoffswitch-label" for="smstemplate">
                        <span class="onoffswitch-inner"></span>
                        <span class="onoffswitch-switch"></span>
                      </label>
                    </div>
                  </span>
                </span>
              </div> 
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg wrap" ng-show="data.smstemplate || misc.listfullsmstemplate">
                <label class="col-sm-4 text-right">Elegir plantilla</label>
                <span class="input hoshi input-default col-sm-8" data-ng-click="">
                  <select class="form-control select2" data-placeholder="Seleccione una de las plantillas" style="width: 100%" ng-model="data.idSmsTemplate" data-ng-change="functions.useTemplate()">
                    <option value=""></option>
                    <option ng-repeat="i in misc.listfullsmstemplate" value="{{"{{i.idSmsTemplate}}"}}">{{"{{i.name}}"}}</option>
                  </select>
                </span>
              </div>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap" ng-show="countContactsApproximate.counts>0"> 
                <label class="col-sm-4 col-md-4 text-right">*Mensaje:{{"{{tag}}"}}</label>
                <span class="input hoshi input-default col-sm-8 col-md-8">
                  <textarea ng-model="data.message" class="form-control" rows="5" style="resize: none;" ng-change="functions.validateInLine()"></textarea>
                  <div class="text-right" data-ng-class="misc.newMessage.length > 160 ? 'negative':''">{{"{{misc.newMessage.length > 0 ?  misc.newMessage.length+'/160':''}}"}}</div>
                  <h6 class="color-danger" ng-show='misc.invalidCharacters'>Recuerde que ninguno de estos caracteres es permitido: ñ Ñ ¡ ¿ á é í ó ú Á É Í Ó Ú ; ´</h6>
                  <h6 class="color-warning" ng-show='misc.existTags'>Si personaliza el mensaje SMS y éste excede los 160 caracteres permitidos será cortado en el momento del envío</h6>
                </span>
              </div>
            </div>
            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap"> 
                {#                                <i class="input hoshi input-default col-sm-8 col-md-8 float-right">Ej: <b>57; 315XXXXXXX; mensaje de ejemplo</b></i>
                #}                                <label class="col-sm-12 col-md-12">*Tipos de respuestas:</label>
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                  <div class="col-xs-12 col-sm-12 col-md-4 col-lg-3 text-center" >RESPUESTA</div>
                  {#                                    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-3 text-center" >ACCION</div>#}
                  <div class="col-xs-12 col-sm-12 col-md-4 col-lg-8 text-center" >HOMOLOGAR <i class="input hoshi input-default ">Ej: <b>si,confirmado,enterado</b></i></div>
                  <div class="col-xs-12 col-sm-12 col-md-4 col-lg-1 text-center" ><i  class="fa fa-plus text-right" style="color:blue" ng-click="functions.addResponse()" aria-hidden="true" ></i></div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 margin-botton" ng-repeat="response in data.typeResponse">
                  <div class="col-xs-12 col-sm-12 col-md-4 col-lg-3" >
                    <input type="text" ng-model="response.response" class="form-control"  />
                  </div>
                  <div class="col-xs-12 col-sm-12 col-md-4 col-lg-8">
                    <textarea type="text" ng-model="response.homologate" class="form-control" row="4" ></textarea>
                  </div>
                  <div class="col-xs-12 col-sm-12 col-md-4 col-lg-1">
                    <i class="fa fa-ban danger-no-hover "  ng-click="functions.deleteResponse($index)" aria-hidden="true" ></i>
                  </div>
                </div>
              </div>
            </div>
            <div class="form-group" >
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label class="col-sm-4 col-md-4 text-right">*Opciones avanzadas:</label>
                <span class="input hoshi input-default col-sm-1 col-md-1">
                  <span class="input hoshi input-default col-sm-8 col-md-8 " >
                    {#                    <input type="checkbox" class="toggle-sms-two-way" ng-model="data.advancedOptions"  ng-click="functions.evaluateAdvancedoptions()"/>#}
                    <div class="onoffswitch">
                      <input type="checkbox" name="sentNow" ng-model="data.advancedoptions" class="onoffswitch-checkbox" id="advancedoptions">
                      <label class="onoffswitch-label" for="advancedoptions">
                        <span class="onoffswitch-inner"></span>
                        <span class="onoffswitch-switch"></span>
                      </label>
                    </div>
                  </span>
                </span>
              </div>
            </div>
            <div ng-show="data.advancedoptions" class="advancedoptions-container">
              <div class="title-advancedoption">Opciones avanzadas</div>
              <div class="form-group" ng-cloak>
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                  <label class="col-sm-4 col-md-4 text-right">*¿Enviar notificacion? :</label>
                  <span class="input hoshi input-default col-sm-1 col-md-1">
                    {#                    <input type="checkbox" class="toggle-sms-two-way" ng-model="data.sendNotification" ng-click="functions.sendnotification()">#}
                    <div class="onoffswitch">
                      <input type="checkbox" name="sentNow" ng-model="data.notification" class="onoffswitch-checkbox" id="sendNotification">
                      <label class="onoffswitch-label" for="sendNotification">
                        <span class="onoffswitch-inner"></span>
                        <span class="onoffswitch-switch"></span>
                      </label>
                    </div>
                  </span>
                </div>
              </div>

              <div class="form-group" id="email-addresses" ng-show="data.notification">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                  <i class="input hoshi input-default col-sm-8 col-md-8 float-right">Ej: <b>ej1@aio.com, ej3@aio.com, ej3@aio.com </b></i>
                  <label class="col-sm-4 col-md-4 text-right">Direccione(s) de correo electronico:</label>
                  <span class="input hoshi input-default col-sm-8 col-md-8">
                    <textarea class="undeline-input ng-pristine ng-valid ng-empty ng-valid-maxlength ng-touched" maxlength="500" rows="2" ng-model="data.email" ng-disabled="!data.notification"></textarea>
                  </span>
                </div>
              </div>
              <div class="top-line-advanced"></div>
              <div class="form-group" ng-cloak>
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                  <label class="col-sm-4 col-md-4 text-right">*¿Particionar el envío?:</label>
                  <span class="input hoshi input-default col-sm-1 col-md-1"  {#ng-click="divideSending()"#}>
                    {#                    <input type="checkbox" class="toggle-sms-two-way" ng-model="data.partitionShipments" ng-click="functions.divideSending()">#}
                    <div class="onoffswitch">
                      <input type="checkbox" name="sentNow" ng-model="data.divide" class="onoffswitch-checkbox" id="divide">
                      <label class="onoffswitch-label" for="divide">
                        <span class="onoffswitch-inner"></span>
                        <span class="onoffswitch-switch"></span>
                      </label>
                    </div>
                  </span>
                </div>
              </div>
              <div id="divide-container" ng-show="data.divide">
                <div class="form-group">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <label class="col-sm-4 col-md-4 text-right">*Cantidad de envíos por intervalo:</label>
                    <span class="input hoshi input-default col-sm-8 col-md-8">
                      <input type="number"  class="undeline-input ng-pristine ng-valid ng-empty ng-touched" placeholder="" ng-model="data.quantity">
                    </span>
                  </div>
                </div>

                <div class="form-group" >
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <i class="input hoshi input-default col-sm-8 col-md-8 float-right"></i>
                    <label class="col-sm-4 col-md-4 text-right">*Tiempo de envío:</label>
                    <span class="input hoshi input-default col-sm-8 col-md-8">
                      <select  ng-model="data.sendingTime" class="form-control select2"  required="">
                        {% for item in 1..60 %}
                          <option value="{{ item }}">{{ item }}</option>
                        {% endfor %}
                      </select>
                    </span>
                  </div>
                </div>
                <div class="form-group" >
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <i class="input hoshi input-default col-sm-8 col-md-8 float-right"></i>
                    <label class="col-sm-4 col-md-4 text-right">*Formato de tiempo:</label>
                    <span class="input hoshi input-default col-sm-8 col-md-8">
                      <select  ng-model="data.timeFormat" class="form-control select2" required="">
                        <option ng-repeat="time in misc.timeFormats" value="{{"{{time.value}}"}}">{{"{{time.name}}"}}</option>
                      </select>
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="footer" align="right">   
          <button id="submitButton" class="button shining btn btn-xs-round shining shining-round round-button success-inverted" data-toggle="tooltip" data-placement="top" title="Guardar">
            <span class="glyphicon glyphicon-ok"></span>
          </button>
          <a href="{{url('smstwoway/')}}" class="button shining btn btn-xs-round shining shining-round round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Cancelar">
            <span class="glyphicon glyphicon-remove"></span>
          </a>
          <a ng-show="data.message" ng-click="functions.openPreview()" class="button shining btn btn-xs-round shining-round round-button success-inverted" data-toggle="tooltip" data-placement="top" title="Visualizar">
            <span class="fa fa-eye" aria-hidden="true"></span>
          </a>

        </div>  
      </div>
    </div> 
  </form>
  <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">                            
    <div class="fill-block fill-block-info" >
      <div class="header">
        Información
      </div>
      <div class="body">
        <p>
          Recuerde tener en cuenta estas recomendaciones:
        <ul>
          <li>El nombre no puede tener menos de 5 caracteres ni más de 50 caracteres.</li>
          <li>La fecha y hora de envío es para decidir cuándo se van a enviar los SMS y tiene que ser entre las 7:00h  y las 18:00h (hora de Colombia).</li>
          <li>El envío de notificaciones por defecto está en "No", para activarlo haga clic en el switch para que cambie a "Si"</li>
          <li>Los correos deben estar separados por coma "," máximo 8 correos, donde se enviará la notificación cuando finalice el envío.</li>
          <li>Puede seleccionar varias listas o segmentos como destinatarios del envío de SMS.</li>
          <li>Recuerde que los campos con asterisco(*) son obligatorios.</li>
        </ul>
        </p>
      </div>
      <div class="footer">
        Creación
      </div>
    </div>     
  </div>

  <div id="somedialog" class="dialog">
    <div class="dialog__overlay"></div>
    <div class="dialog__content">
      <div class="morph-shape">
        <svg xmlns="https://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 560 280" preserveAspectRatio="none">
        <rect x="3" y="3" fill="none" width="556" height="276"/>
        </svg>
      </div>
      <div class="dialog-inner">
        <p ng-show="error == 0">Se van a enviar {{"{{countContactsApproximate.counts}}"}} mensaje(s) de SMS*.</p>
        <div ng-show="error != 0" class="div-scroll-100px">
          <p ng-repeat="obj in arrError" ng-show="error != 0">
            <em ng-show="$index != 0">&raquo;  {{ "{{obj}}"}}</em>
          </p>
        </div>
        <h2>¿Esta seguro que desea continuar?</h2>
        <div>   

          <a ng-click="functions.closeModal()"  class="button shining btn btn-md danger-inverted" data-dialog-close>Cancelar</a>
          <a ng-click="functions.createcontact();{#addDisabled('btn-ok')#}"  id="btn-ok" class="button shining btn btn-md success-inverted">Confirmar</a>
        </div>
        <h6>*La cantidad total de envíos puede variar dependiendo de la actividad de los contactos (se eliminan, se bloquean, se desuscriben, etc)</h6>
      </div>
    </div>
  </div>
  {#modal pre view de mensaje            #}

  <div id="preview" class="dialog">
    <div class="dialog__overlay"></div>
    <div class="dialog__content">

      <div class="dialog-inner">
        <div class='smsContainer'>
          Tu mensaje tendrá el siguiente aspecto

          <div class="smsContent" ng-bind-html="misc.taggedMessage">
          </div>
        </div>

        <div class='smsFooter'>                    
          <a ng-click="functions.closePreview()"  id="btn-ok" class="button shining btn btn-md success-inverted float-right">Ok</a>
        </div>
      </div>
    </div>
  </div>
</div>


