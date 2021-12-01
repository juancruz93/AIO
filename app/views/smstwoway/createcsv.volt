<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="title">
      Creación de un envio de SMS doble vía por archivo CSV
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
                  <input type="text" class="form-control" id="name" minlength=5 maxlength=50  ng-model="data.name">
                </span>
              </div>
            </div>

            <!-- <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label class="col-sm-4 col-md-4 text-left">*Categoria:</label>
                <span class="input-default col-sm-8 col-md-8">
                  <select class="form-control"  ng-options="category.idSmsCategory as category.name for category in misc.listCategory" ng-model="data.idSmsCategory">
                    <option></option>
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
                    <input type="checkbox" name="sentNow" ng-model="data.dateNow" class="onoffswitch-checkbox" id="sentNow">
                    <label class="onoffswitch-label" for="sentNow">
                      <span class="onoffswitch-inner"></span>
                      <span class="onoffswitch-switch"></span>
                    </label>
                  </div>
                </span>
              </div>
            </div>
            <div ng-show="!data.dateNow">
              <!-- <div class="form-group" >
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                  <label class="col-sm-4 col-md-4 text-left">*Zona horaria:</label>
                  <span class="input-default col-sm-8 col-md-8">
                    <select class="form-control" id="sel1" ng-model="data.gmt"  ng-options = "gmt.gmt as gmt.countries for gmt in misc.listTimezone">
                      <option></option>
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
                    <input type='text' id="dtpicker"  class="undeline-input" />
                    <span class="add-on input-group-addon">
                      <i data-time-icon="glyphicon glyphicon-time" data-date-icon="glyphicon glyphicon-calendar"></i>
                    </span>
                  </span>
                </div>
              </div>
            </div>
            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap"> 
                <label class="col-sm-12 col-md-12">*Tipos de respuestas:</label>
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                  <div class="col-xs-12 col-sm-12 col-md-4 col-lg-3 text-center" >RESPUESTA</div>
                  {#                                    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-3 text-center" >ACCION</div>#}
                  <div class="col-xs-12 col-sm-12 col-md-4 col-lg-8 text-center" >HOMOLOGAR <i class="input hoshi input-default ">Ej: <b>si,confirmado,enterado</b></i></div>
                  <div class="col-xs-12 col-sm-12 col-md-4 col-lg-1 text-center" ><i  class="fa fa-plus text-right" style="color:blue" ng-click="functions.addResponse()" ng-show="data.typeResponse.length<misc.limitTypeResponse" aria-hidden="true" ></i></div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 margin-botton" ng-repeat="response in data.typeResponse">
                  <div class="col-sm-4 col-md-4">
                    <input type="text" ng-model="response.response" class="form-control"  />
                  </div>
                  {#<div class="col-xs-12 col-sm-12 col-md-4 col-lg-3">
                      <select type="text" ng-model="response.action" class="form-control"  >
                          <option selected>Sin Accion</option>
                          <option>Agregar</option>
                          <option>Eliminar</option>
                      </select>
                  </div>#}
                  <div class="col-xs-12 col-sm-12 col-sm-8 col-md-8" style="padding: 0;">
                    <textarea type="text" ng-model="response.homologate" class="form-control" row="4" style="resize: none;"></textarea>
                  </div>
                  <!-- <div class="col-xs-12 col-sm-12 col-md-4 col-lg-1">
                    <i class="fa fa-ban danger-no-hover " ng-show="data.typeResponse.length>misc.minTypeResponse" ng-click="functions.deleteResponse($index)" aria-hidden="true" ></i>
                  </div> -->
                </div>
              </div>
            </div>
            <div class="form-group" ng-if="!misc.edit">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label class="col-sm-4 col-md-4 text-left">*¿Envio Internacional?:</label>
                <span class="input hoshi input-default col-sm-1 " >
                  <div class="onoffswitch">
                    <input type="checkbox" name="international" ng-model="data.international" class="onoffswitch-checkbox" id="international" ng-click="ValidateCheckInter()">
                    <label class="onoffswitch-label" for="international">
                      <span class="onoffswitch-inner"></span>
                      <span class="onoffswitch-switch"></span>
                    </label>
                  </div>
                </span>
              </div>              
            </div>
            <div class="form-group" ng-show="data.international">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                  <label class="col-sm-4 col-md-4 text-left">*Pais:</label>
                  <span class="input-default col-sm-8 col-md-8">
                    <ui-select ng-model="data.idcountry" ng-required="true" ui-select-required theme="bootstrap" title="" 
                      sortable="false" close-on-select="true">
                      <ui-select-match style="overflow: hidden;">{{"{{$select.selected.country}}"}}</ui-select-match>
                      <ui-select-choices repeat="key in internationalcountries | propsFilter: {country: $select.search}">
                        <div ng-bind-html="key.country | highlight: $select.search"></div>
                      </ui-select-choices>
                    </ui-select>
                  </span>
                </div>
              </div>
            <div class="form-group" ng-if="!misc.edit" >
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap"> 
                <label class="col-sm-4 col-md-4 ">*Archivo CSV:</label>
                <span class="input hoshi input-default col-sm-8 col-md-8">
                  {#                  <input type="file" class="form-control" fileread="data.csv"/>#}
                  <input type="file" class="form-control" file-model="data.csv" ng-disabled="misc.edit"  accept=".csv" id="csvfield" />
                </span>
              </div>
            </div>

            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label class="col-sm-4 col-md-4 text-left">*Opciones avanzadas:</label>
                <span class="input hoshi input-default col-sm-1 col-md-1"  >
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
                  <span class="input hoshi input-default col-sm-1 col-md-1"  >
                    <div class="onoffswitch">
                      <input type="checkbox" name="notification" ng-model="data.notification" class="onoffswitch-checkbox" id="notification">
                      <label class="onoffswitch-label" for="notification">
                        <span class="onoffswitch-inner"></span>
                        <span class="onoffswitch-switch"></span>
                      </label>
                    </div>
                  </span>
                </div>
              </div>

              <div class="form-group" id="email-addresses" ng-show="data.notification" >
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                  <i class="input hoshi input-default col-sm-8 col-md-8 float-right">Ej: <b>ej1@aio.com, ej3@aio.com, ej3@aio.com </b></i>
                  <label class="col-sm-4 col-md-4 text-left">Direccione(s) de correo electronico:</label>
                  <span class="input hoshi input-default col-sm-8 col-md-8">
                    <textarea class="form-control" rows="2" id="destinatarios" ng-model="data.email" style="resize: none;"></textarea>
                  </span>
                </div>
              </div>
              <div class="top-line-advanced"></div>
              <div class="form-group" ng-cloak>
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                  <label class="col-sm-4 col-md-4 text-left">*¿Particionar el envío?:</label>
                  <span class="input hoshi input-default col-sm-1 col-md-1" >
                    <div class="onoffswitch">
                      <input type="checkbox" name="notification" ng-model="data.divideSending" class="onoffswitch-checkbox" id="divideSending">
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
                      <input  type="number" class="form-control" ng-model="data.quantity" pattern="^[0-9]" title='Only Number' min="1" step="1" >
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
          <a href="{{url('smstwoway#/')}}" class="button shining btn btn-xs-round shining shining-round round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Cancelar" {#ng-click="restServicesFunction.cancel()"#}>
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
          <li>El nombre no puede tener menos de 5 caracteres ni más de 50 caracteres.</li>
          <li>La fecha y hora de envío es para decidir cuando se va a enviar los SMS y tiene que ser entre las 7:00h  y las 18:00h.</li>
          <li>El envío de notificaciones por defecto esta en "No", para activarlo haga clic en el switch para que cambie a "Si"</li>
          <li>Los correos electrónicos donde se va a notificar el envío se deben de separar por coma "," y maximo son 8 correos</li>
          <li>El archivo CSV no debe pesar más de 2 MB</li>
          <li>El archivo CSV debe de tener la siguiente estructura (codigo del país; numero telefonico; mensaje) separados por punto y coma (;)</li>
          <li>El archivo CSV debe de contener al menos un destinatario valido</li>
          <li>El codigo del pais no debe tener el signo "+"</li>
          <li>El mensaje no debe de tener caracteres especiales y no debe de tener más de 160 caracteres</li>
          <li>El mensaje no puede contener caracteres especiales</li>
          <li>Los tipos de respuesta hacen referencia a las respuestas que usted quiere recibir por parte de sus clientes a partir del mensaje que les envió. Por ejemplo, si usted envia el mensaje "Te gusta la cerveza? responde si o no a este mensaje", sus respuestas serian "si" y "no".  </li>
          <li>Homologar las respuestas hace referencia a las respuestas por parte de sus clientes que no son exactamente las que usted definio. Es decir, si usted definio las respuesta "si", su respectiva homologacion(es) seria "ok,vale,enterado". Si tambien definio la respuesta "no", su respectiva homologacion(es) seria "negativo,cancelado"  </li>
          <li>Recuerde que los campos con asterisco(*) son obligatorios.</li>
        </ul>
        </p>
      </div>
      <div class="footer">
        Creación
      </div>
    </div>     
  </div>
</div>
<div id="ProcessCsv" class="dialog">
  <div class="dialog__overlay"></div>
  <div class="dialog__content">
    <div class="morph-shape">
      <svg xmlns="https://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 560 280" preserveAspectRatio="none">
      <rect x="3" y="3" fill="none" width="556" height="276"/>
      </svg>
    </div>
    <div class="dialog-inner">
      <div ng-if="!misc.initProcessUpload">
        <p >Se van a realizar un envio de SMS</p>
        <h2>¿Esta seguro que desea continuar?</h2>
        <div>                    
          <a onClick="closeModal();" class="button shining btn btn-md danger-inverted" data-dialog-close>Cancelar</a>
          <a ng-click="restServicesFunction.createLoteTwoway();"  id="btn-ok" class="button shining btn btn-md success-inverted">Validar Envio</a>
        </div>
      </div>
      <div ng-if="misc.initProcessUpload">
        <div class="text-2em">Cargando envio por archivo CSV</div>
        <hr>
        <div class="progress">
          <div class="progress-bar progress-bar-success" role="progressbar"  ng-style="{'width':misc.ProccessCsv.porc+'%'}">
            {{'{{misc.ProccessCsv.porc}}'}}%
          </div>
        </div>
        <div ng-if ="misc.ProccessCsv.preload">
          <div  ng-class="{'text-success':misc.ProccessCsv.preload.data}" class="text-left"><i ng-class="{'glyphicon glyphicon-play':!misc.ProccessCsv.preload.data,'glyphicon glyphicon-ok-circle':misc.ProccessCsv.preload.data}"></i> Cargando Archivo Csv</div>
          <div ng-if ="misc.ProccessCsv.preload.data" class="text-left">
            <ul>
              <li>Cantidad de registros: {{'{{misc.ProccessCsv.preload.data.rowsCsv}}'}}</li>
            </ul>
          </div>
        </div>
        <div ng-if ="misc.ProccessCsv.validations">
          <div  ng-class="{'text-success':misc.ProccessCsv.validations.data}" class="text-left "><i ng-class="{'glyphicon glyphicon-play':!misc.ProccessCsv.validations.data,'glyphicon glyphicon-ok-circle':misc.ProccessCsv.validations.data}"></i>Validacion del archivo</div>
          <div ng-if ="misc.ProccessCsv.validations.data" class="text-left">
            <ul>
              <li>Cantidad de registros Repetidos: {{'{{misc.ProccessCsv.validations.data.countRepeat}}'}}</li>
              <li>Cantidad de registros no compatible con el indicativo: {{'{{misc.ProccessCsv.validations.data.countInvalid}}'}}</li>
              <li>Cantidad de registros Invalidos: {{'{{misc.ProccessCsv.validations.data.countTotal}}'}}</li>
            </ul>
          </div>
        </div>
        <div ng-if ="misc.ProccessCsv.load">
          <div  ng-class="{'text-success':misc.ProccessCsv.load.data}" class="text-left"><i ng-class="{'glyphicon glyphicon-play':!misc.ProccessCsv.load.data,'glyphicon glyphicon-ok-circle':misc.ProccessCsv.load.data}"></i>Programado envios de SMS</div>
          <div ng-if ="misc.ProccessCsv.load.data" class="text-left">
            <ul>
              <li>Total registo para envio: {{'{{misc.ProccessCsv.load.data.countSent}}'}}</li>
            </ul>
          </div>
        </div>
        <div ng-if ="misc.ProccessCsv.finish" >
          <div class="text-success text-left">
            <i class="glyphicon glyphicon-ok-circle"></i>
          Finalización
          </div>
          
          <div class="text-success">Previsualización </div>
          <div class="smsContent">
            {{'{{misc.ProccessCsv.finish.message}}'}}
          </div>
          <hr>
          <div>                    
            <a ng-click="restServicesFunction.changeStatusCsv('canceled');" class="button shining btn btn-md danger-inverted" data-dialog-close>Cancelar Envio</a>
            <a ng-click="restServicesFunction.changeStatusCsv('scheduled');"  id="btn-ok" class="button shining btn btn-md success-inverted">Confirmar Envio</a>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
<div id="editCsv" class="dialog">
  <div class="dialog__overlay"></div>
  <div class="dialog__content">
    <div class="morph-shape">
      <svg xmlns="https://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 560 280" preserveAspectRatio="none">
      <rect x="3" y="3" fill="none" width="556" height="276"/>
      </svg>
    </div>
    <div class="dialog-inner">
      <div >Para editar la campaña seleccionada se debe cambiar el estado</div>
      ¿Esta seguro que desea continuar?
      <div>
        <a ui-sref="indextwoway" class="button shining btn btn-md default-inverted">Regresar</a>
        <a ng-click="restServicesFunction.changeStatusCsv('draft');"  class="button shining btn btn-md success-inverted">Continuar</a>
      </div>
    </div>
  </div>
</div>
<script>
  function openModal() {

  }

  function closeModal() {
    $('.dialog').removeClass('dialog--open');
  }
</script>
