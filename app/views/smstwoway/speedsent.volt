
<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="title">
      Creación de un envío rápido de SMS doble vía
    </div>
    <hr class="basic-line"/>
  </div>
</div>
<div class="row">
  <form  class="form-horizontal" ng-submit="functions.validate()">
    <div class="col-xs-12 col-sm-12 col-md-8 col-lg-6 wrap">
      <div class="block block-info">          
        <div class="body " >
          <div class="row">
            <br>
            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label class="col-sm-4 col-md-4 text-left">*Nombre del envío rápido:</label>
                <span class="input hoshi input-default col-sm-8 col-md-8">
                  <input type="text" class="form-control" id="name" minlength="2" maxlength="50"  ng-model="data.name" required>
                </span>
              </div>
            </div>

            <!-- <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label class="col-sm-4 col-md-4 text-left">*Categoria:</label>
                <span class="input-default col-sm-8 col-md-8">
                  <select class="select2 form-control" ng-options="category.idSmsCategory as category.name for category in misc.listCategory" ng-model="data.category" required>
                    <option value=""></option>
                  </select>
                </span>
              </div>
            </div> -->
            
            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label class="col-sm-4 col-md-4 text-left">*Categoria:</label>
                <span class="input-default col-sm-8 col-md-8">
                  <ui-select ng-model="data.category" ng-required="true" ui-select-required theme="bootstrap" title="" 
                    sortable="false" close-on-select="true">
                    <ui-select-match>{{"{{$select.selected.name}}"}}</ui-select-match>
                    <ui-select-choices repeat="key in misc.listCategory | propsFilter: {name: $select.search}">
                      <div ng-bind-html="key.name | highlight: $select.search"></div>
                    </ui-select-choices>
                  </ui-select>
                </span>
              </div>
            </div>

            <div class="form-group" >
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label class="col-sm-4 col-md-4 text-left">*¿Enviar ahora mismo?:</label>
                <span class="input hoshi input-default col-sm-1 " >
                  <div class="onoffswitch">
                    <input type="checkbox" name="sentNow" ng-model="data.sentNow" class="onoffswitch-checkbox" id="sentNow">
                    <label class="onoffswitch-label" for="sentNow">
                      <span class="onoffswitch-inner"></span>
                      <span class="onoffswitch-switch"></span>
                    </label>
                  </div>
                  {# <input type="checkbox" class="toggle-sms-two-way" ng-click="functions.sentNow()"/>#}
                </span>
              </div>
            </div>
            {#{{'{{newCheck}}'}}
             <div class="onoffswitch">
                <input type="checkbox" name="onoffswitch" ng-model="newCheck" ng-click="prueba()" class="onoffswitch-checkbox" id="myonoffswitch">
                <label class="onoffswitch-label" for="myonoffswitch">
                 <span class="onoffswitch-inner"></span>
                  <span class="onoffswitch-switch"></span>
                </label>
             </div>#}
            <div ng-show="!data.sentNow">
              <!-- <div class="form-group" >
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                  <label class="col-sm-4 col-md-4 text-left">*Zona horaria:</label>
                  <span class="input-default col-sm-8 col-md-8">
                    <select class="select2 form-control" id="sel1" name="sel1" ng-model="data.gmt" ng-options="gmt.gmt as gmt.countries for gmt in misc.listTimezone">
                      <option value=""></option>
                    </select>
                  </span>
                </div>
              </div> -->
              <div class="form-group">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                  <label class="col-sm-4 col-md-4 text-left">*Zona horaria:</label>
                  <span class="input-default col-sm-8 col-md-8">
                    <ui-select ng-model="data.gmt" ng-required="true" ui-select-required theme="bootstrap" title="" 
                      sortable="false" close-on-select="true">
                      <ui-select-match style="overflow: hidden;">{{"{{$select.selected.countries}}"}}</ui-select-match>
                      <ui-select-choices repeat="key in misc.listTimezone | propsFilter: {countries: $select.search}">
                        <div ng-bind-html="key.countries | highlight: $select.search"></div>
                      </ui-select-choices>
                    </ui-select>
                  </span>
                </div>
              </div>

              <div class="form-group" >
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                  <label class="col-sm-4 col-md-4 text-left">*Fecha y hora de envio:</label>
                  <span class="input-append date  col-sm-8 col-md-8 input-group datetimepicker"  style="padding-left: 15px; padding-right: 15px;">
                    <input type='text' id="dtpicker" ng-model="data.startdate" class="undeline-input" />
                    <span class="add-on input-group-addon">
                      <i data-time-icon="glyphicon glyphicon-time" data-date-icon="glyphicon glyphicon-calendar"></i>
                    </span>
                  </span>
                </div>
              </div>
            </div>
            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap"> 
                <i class="input hoshi input-default col-sm-8 col-md-8 float-right">Ej: <b>57;315XXXXXXX;mensaje de ejemplo</b></i>
                <label class="col-sm-4 col-md-4 text-left">*Destinatario(s):</label>
                <span class="input hoshi input-default col-sm-8 col-md-8">
                  <textarea class="form-control" rows="2" id="destinatarios" ng-model="data.receiver" style="resize: none;"></textarea>
                  <h6 class="color-danger text-justify" >Recuerde que los datos de cada destinatario deben estar separados por punto y coma ";" y los destinatarios por un salto de línea (enter)</h6>
                  <h6 class="color-danger" ng-show='wrongRow'>El formato debe ser el siguiente: <br><b>57; 315XXXXXXX; mensaje de ejemplo</b></h6>
                  <h6 class="color-danger">
                    Los siguientes caracteres serán removidos del mensaje: \º~|·[]^{}¨´€"
                  </h6>
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
                  <div class="col-xs-12 col-sm-12 col-md-4 col-lg-1 text-center" ><i  class="fa fa-plus text-right" style="color:#00bede" ng-click="functions.addResponse()" ng-show="data.typeResponse.length<misc.limitTypeResponse" aria-hidden="true" ></i></div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 margin-botton" ng-repeat="response in data.typeResponse">
                  <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4" >
                    <input type="text" ng-model="response.response" class="form-control"  />
                  </div>
                  <div  class="col-xs-12 col-sm-12 col-sm-8 col-md-8" style="padding: 0;">
                    <textarea type="text" ng-model="response.homologate" class="form-control" row="4" style="resize: none;"></textarea>
                  </div>
                  <div class="col-xs-12 col-sm-12 col-md-4 col-lg-1">
                    <i class="fa fa-ban danger-no-hover " ng-show="data.typeResponse.length>misc.minTypeResponse" ng-click="functions.deleteResponse($index)" aria-hidden="true" ></i>
                  </div>
                </div>
              </div>
            </div>
            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label class="col-sm-4 col-md-4 text-left">*Opciones avanzadas:</label>
                {#<input type="checkbox" class="toggle-sms-two-way" ng-click="functions.optionsAvanced()"/>#}
                <span class="input hoshi input-default col-sm-1 " >
                  <div class="onoffswitch">
                    <input type="checkbox" name="optionsAvanced" ng-model="data.optionsAvanced" class="onoffswitch-checkbox" id="optionsAvanced">
                    <label class="onoffswitch-label" for="optionsAvanced">
                      <span class="onoffswitch-inner"></span>
                      <span class="onoffswitch-switch"></span>
                    </label>
                  </div>
                </span>
              </div>
            </div>

            <div ng-show="data.optionsAvanced" class="advancedoptions-container">
              <div class="title-advancedoption">Opciones avanzadas</div>
              <div class="form-group" ng-cloak>
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                  <label class="col-sm-4 col-md-4 text-left">*¿Enviar notificacion?:</label>
                  <span class="input hoshi input-default col-sm-1 col-md-1">
                    {#<input type="checkbox" class="toggle-sms-two-way" ng-click="functions.sendNotification()"/>#}  
                    <div class="onoffswitch">
                      <input type="checkbox" name="sendNotification" ng-model="data.sendNotification" class="onoffswitch-checkbox" id="sendNotification">
                      <label class="onoffswitch-label" for="sendNotification">
                        <span class="onoffswitch-inner"></span>
                        <span class="onoffswitch-switch"></span>
                      </label>
                    </div>
                  </span>
                </div>
              </div>

              <div class="form-group" id="email-addresses" ng-show="data.sendNotification" >
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                  <i class="input hoshi input-default col-sm-8 col-md-8 float-right">Ej: <b>ej1@aio.com, ej3@aio.com, ej3@aio.com </b></i>
                  <label class="col-sm-4 col-md-4 text-left">Direccione(s) de correo electronico:</label>
                  <span class="input hoshi input-default col-sm-8 col-md-8">
                    <textarea class="form-control" rows="2" id="destinatarios" ng-model="data.emailNotification" style="resize: none;"></textarea>
                  </span>
                </div>
              </div>

              <div class="top-line-advanced"></div>
              <div class="form-group" ng-cloak>
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                  <label class="col-sm-4 col-md-4 text-left">*¿Particionar el envío?:</label>
                  <span class="input hoshi input-default col-sm-1 col-md-1" >
                    {#<input type="checkbox" class="toggle-sms-two-way" ng-click="functions.divideSending()"/#}
                    <div class="onoffswitch">
                      <input type="checkbox" name="divideSending" ng-model="data.divideSending" class="onoffswitch-checkbox" id="divideSending">
                      <label class="onoffswitch-label" for="divideSending">
                        <span class="onoffswitch-inner"></span>
                        <span class="onoffswitch-switch"></span>
                      </label>
                    </div>

                  </span>
                </div>
              </div>

              <div id="divide-container" ng-show="data.divideSending">
                <div class="form-group">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <label class="col-sm-4 col-md-4 text-left">*Cantidad de envíos por intervalo:</label>
                    <span class="input hoshi input-default col-sm-8 col-md-8">
                      <input  type="number" class="form-control" ng-model="data.quantity" {#pattern="^[0-9]" title='Only Number' min="1" step="1" #}>
                    </span>
                  </div>
                </div>
                <div class="form-group" >
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <i class="input hoshi input-default col-sm-8 col-md-8 float-right"></i>
                    <label class="col-sm-4 col-md-4 text-left">*Tiempo de envío:</label>
                    <span class="input hoshi input-default col-sm-8 col-md-8">
                      <select id="sendingTime" name="sendingTime" ng-model="data.sendingTime" class="form-control" >
                        <option ng-repeat="item in misc.numberSending track by $index" ng-value="$index + 1">{{'{{$index + 1}}'}}</option>
                      </select>
                    </span>
                  </div>
                </div>
                <div class="form-group" >
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <i class="input hoshi input-default col-sm-8 col-md-8 float-right"></i>
                    <label class="col-sm-4 col-md-4 text-left">*Formato de tiempo:</label>
                    <span class="input hoshi input-default col-sm-8 col-md-8">
                      <select id="timeFormat" name="timeFormat" ng-model="data.timeFormat" class="form-control" >
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
          <button class="button shining btn btn-xs-round shining shining-round round-button success-inverted" data-toggle="tooltip" data-placement="top" title="Guardar">
            <span class="glyphicon glyphicon-ok"></span>
          </button>
          <a href="{{url('smstwoway#/')}}" class="button shining btn btn-xs-round shining shining-round round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Cancelar">
            <span class="glyphicon glyphicon-remove"></span>
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
          <li>El nombre del envio rapido no puede tener menos de 5 caracteres ni más de 50 caracteres.</li>
          <li>La fecha y hora de envío es para decidir cuando se van a enviar los SMS y tiene que ser entre las 7:00h  y las 18:00h (hora de Colombia).</li>
          <li>El envío de notificaciones por defecto está en "No", para activarlo haga clic en el switch para que cambie a "Si"</li>
          <li>Los destinatarios es a quien va a enviarse los SMS y tienen que ir de la siguiente forma: codigo de pais(sin el simbolo "+"), número de móvil, mensaje. Separados por punto y coma ";". Los destinatarios son cada línea separados por un salto de línea (enter).</li>
          <li>Los correos se deben de separar por coma "," maximo 8 correos, donde se enviarán las notificaciones</li>
          <li>Los tipos de respuesta hacen referencia a las respuestas que usted quiere recibir por parte de sus clientes a partir del mensaje que les envió. Por ejemplo, si usted envia el mensaje "Te gusta la cerveza? responde si o no a este mensaje", sus respuestas serian "si" y "no".  </li>
          <li>Homologar las respuestas hace referencia a las respuestas por parte de sus clientes que no son exactamente las que usted definio. Es decir, si usted definio las respuesta "si", su respectiva homologacion(es) seria "ok,vale,enterado". Si tambien definio la respuesta "no", su respectiva homologacion(es) seria "negativo,cancelado"  </li>
          <li>Recuerde que los campos con asterisco(*) son obligatorios.</li>
        </ul>
        </p>
      </div>
      {#<div class="footer">
          Creación
      </div>#}
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
      <p >Se va a realizar un envío de {{"{{misc.smsCount}}"}} SMS doble-via</p>
      <h2>¿Esta seguro que desea continuar?</h2>
      <div>                    
        <a onClick="closeModal();" class="button shining btn btn-md danger-inverted" data-dialog-close>Cancelar</a>
        <a ng-click="restServicesFunction.createLoteTwoway();{#addDisabled('btn-ok')#}"  id="btn-ok" class="button shining btn btn-md success-inverted">Confirmar</a>
      </div>
    </div>
  </div>
</div>

<script>
  function openModal() {
    $('.dialog').addClass('dialog--open');
  }

  function closeModal() {
    $('.dialog').removeClass('dialog--open');
  }

  function lol(opcion){
    console.log("Actualizando datos",opcion);
  }
</script>


