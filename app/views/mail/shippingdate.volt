<script type="text/javascript">
  $.fn.datetimepicker.defaults = {
    maskInput: false,
    pickDate: true,
    pickTime: true,
    startDate: new Date()
  };

  $(".select2").select2({
    theme: 'classic'
  });

  function openModal() {
    $('#somedialog').addClass('dialog--open');
  }

  function closeModal() {
    $('.dialog').removeClass('dialog--open');
  }

  function openModalConfirm() {
    $('#somedialogConfirm').addClass('dialog--open');
  }

  function closeModalConfirm() {
    $('.dialogConfirm').removeClass('dialog--open');
  }
</script>

<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="subtitle">
      <em>Fecha de envío</em>
    </div>
    <br>
    <p class="small-text">
      Puedes programar el envío del correo para una fecha y hora determinada, y la plataforma se encargará de que
      este salga cuando debe. También puedes hacer algunas pruebas antes para que estés más seguro.
    </p>
  </div>
</div>

<div class="row" ng-cloak>
  <form name="contactlistForm" class="form-horizontal" role="form" ng-submit="addContactlist()">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
      <div class="block block-info">
        <div class="body row">
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
            <label class="small-text margin-top-15px">Enviar prueba</label>
            <hr class="hr-classic">
            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 none-padding">
              <div data-ng-show="loaderTestMail">
                <br>
                <md-progress-linear md-mode="query" class="md-warn"></md-progress-linear>
              </div>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 margin-top-15px none-padding">
                <span>Enviar una prueba a:</span>
                <textarea style="resize: none;" class="form-control" rows="3" data-ng-model="test.target" data-ng-readonly="loaderTestMail"></textarea>
              </div>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 margin-top-15px none-padding">
                <span><a href="" ng-click="showTextarea()">Incluír instrucciones o un mensaje personal (opcional)</a></span>
                <textarea style="resize: none;"class="form-control" data-ng-show="show" rows="3" data-ng-model="test.message" data-ng-readonly="loaderTestMail"></textarea>
              </div>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-right margin-top-15px none-padding">
                <button class="btn info-inverted" data-ng-click="sendTestMail()" data-ng-disabled="loaderTestMail">Enviar una prueba</button>
              </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 margin-top-25px">
              <div class="block block-black padding-10px">
                <p>Puedes ingresar hasta 8 correos separados por coma. También si lo deseas puedes
                  incluir instrucciones en la prueba, estas aparecerán en la cabecera del correo. </p>
              </div>
            </div>

            <div ng-show="{{ mailTester }}">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 margin-top-15px none-padding">
                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 none-padding">
                  <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 none-padding">
                    <md-switch class="md-warn none-margin" md-no-ink aria-label="Switch No Ink" ng-model="tester.mailTester">
                    </md-switch>
                  </div>
                  <div class="col-xs-11 col-sm-11 col-md-11 col-lg-11 padding-top-3px">
                    <label for="">Enviar correo de testeo</label>
                  </div>
                </div>
              </div>
              <div ng-show="tester.mailTester">
                <div  class="col-xs-12 col-sm-12 col-md-12 col-lg-12 margin-top-15px none-padding">
                  <div  class="col-xs-12 col-sm-12 col-md-6 col-lg-6 margin-top-15px none-padding">
                    <label>Enviar resultado de testeo a</label>
                    <textarea style="resize: none;" class="form-control" rows="3" data-ng-model="tester.emailsSendTester"></textarea>
                  </div>
                  <div  class="col-xs-12 col-sm-12 col-md-6 col-lg-6 margin-top-15px ">
                    <label>Mensaje (Opcional)</label>
                    <textarea style="resize: none;" class="form-control" rows="3" data-ng-model="tester.messageSendTester"></textarea>
                  </div>
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-right margin-top-15px none-padding">
                    <button class="btn primary-inverted" data-ng-click="tester.sendTesterMail()" >Enviar Tester</button>
                  </div>
                </div>
              </div>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-right margin-top-15px none-padding">
                <button class="btn info-inverted" data-ng-click="sendTestMail()" data-ng-disabled="loaderTestMail">Enviar una prueba</button>
              </div>
            </div>

          </div>
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
            <label class="small-text margin-top-15px">Programar</label>
            <hr class="hr-classic">
            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 none-padding" >
              <div id='datetimepicker' ng-show="!oldHour" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 margin-top-15px none-padding input-append date">
                <span>Fecha y hora</span>
                <span class="input-append date add-on input-group none-padding">
                  <input id="valueDatepicker" data-format="yyyy-MM-dd hh:mm " type="text" class="undeline-input">
                  {#{{ smsloteform.render('startdate', { 'readonly':'', 'ng-model': 'startdate', 'class': 'undeline-input' , 'id': 'datesend', 'required' : 'required' , 'keep-current-value':'' , 'ng-model': 'startdate' }) }}#}
                  <span class="add-on input-group-addon">
                    <i data-time-icon="glyphicon glyphicon-time" data-date-icon="glyphicon glyphicon-calendar"></i>
                  </span>
                </span>
              </div>
              <div  ng-show="oldHour" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 margin-top-15px none-padding input-append date">
                <span>Fecha y hora</span><br>
                <em class="small-text strong-text">Fecha de envio programada </em><em class="small-text">{{ "{{ date }}" }}</em> <br>
              </div>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-right margin-top-15px none-padding">

                <button ng-disabled="oldHour" class="btn success-inverted" ng-click="sendNow()">Hora Actual</button>
                <button ng-hide="!oldHour" class="btn danger-inverted" ng-click="reprogram()">Editar</button>
                <button ng-disabled="oldHour" class="btn info-inverted" ng-click="program()">Programar</button>
              </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 margin-top-15px">
              <em class="small-text strong-text" >GMT Tiempo </em><em class="small-text">{{ "{{ date }}" }}</em> <br>
              <em >{{"{{zonahoraria.zone.gmt}}"}} / {{"{{zonahoraria.zone.countries}}"}}</em>
              <div class="space"></div>
              {#<em class="small-text">Actual GMT Tiempo -- {{ "{{ date.now() | date:'yyyy-MM-dd HH:mm:ss' }}" }}</em>#}
              <p ng-show="!oldHour">para programar en una zona horaria diferente, <a id="delete" onClick="openModal();"
                                                                                     data-toggle="tooltip"
                                                                                     data-placement="top"
                                                                                     title="Borrar este Usuario" href="">Click
                  aqui</a></p>
            </div>
          </div>
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-5px margin-top-25px">
            <hr class="hr-classic">
          </div>
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12  wrap" style="background-color: #f5f5f5; width: 99%; margin-left: 0.5%">
            <label class="small-text margin-top-15px"><em>Resumen del correo</em></label>
            <hr class="hr-classic">
            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 margin-top-15px border-right-black">
              <div class="form-group">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding">
                  <label class="col-sm-2 none-padding">Asunto:</label>
                  <span class="input hoshi col-sm-10 none-padding" ng-class="{positive: data.subject, negative: !data.subject}">
                    <em>{{ '{{ !data.subject ? "El asunto no debe estar vacío." : "" }}' }}</em>
                    {{ '{{ data.subject }}'}}  <span ng-class="{'fa fa-check-circle': data.subject,  'fa fa-times-circle': !data.subject}"></span>
                  </span>
                </div>
              </div>
              <div class="form-group">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding">
                  <label class="col-sm-2 none-padding">Remitente:</label>
                  <span class="input hoshi col-sm-10 none-padding" ng-class="{positive: data.nameSender, negative: !data.nameSender}">
                    <em>{{ '{{ !data.nameSender ? "El remitente no debe estar vacío." : "" }}' }}</em>
                    <{{ '{{ data.emailSender }}'}}> {{ '{{ data.nameSender }}'}} <span ng-class="{'fa fa-check-circle': data.nameSender,  'fa fa-times-circle': !data.nameSender}"></span>
                  </span>
                </div>
              </div>
              <div class="form-group">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding">
                  <label class="col-sm-2 none-padding">Responder a:</label>
                  <span class="input hoshi col-sm-10 none-padding" ng-class="{positive: data.replyto, negative:!data.replyto}">
                    {{ '{{ data.replyto }}'}} <span ng-class="{'fa fa-check-circle': data.replyto,'fa fa-times-circle': !data.replyto}"></span>
                  </span>
                </div>
              </div>
              <div class="form-group">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding">
                  <label class="col-sm-2 none-padding">Destinatarios:</label>
                  <span class="input hoshi col-sm-10 none-padding" ng-class="{positive: data.target, negative: !data.target}">
                    <em>{{ '{{ !data.target ? "El destinatario no debe estar vacío." : "" }}' }}</em>
                    {{ '{{ data.titleList }}' }} <em ng-repeat="(key, contact) in data.contacts">{{ '{{ contact.name }}' }}{{ '{{ (key+1) == data.contacts.length ? "": ", " }}' }}</em>
                    <span ng-class="{'fa fa-check-circle': data.contacts,  'fa fa-times-circle': !data.contacts}"></span>
                    {#Lista(s) de contacto(s): Lista de prueba (9), Lista de clientes (800)#}
                    <br>
                    <b>{{ '{{ data.quantitytarget }}' }} contactos totales aproximadamente</b>
                  </span>
                </div>
              </div>
              <div class="form-group">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding">
                  <label class="col-sm-2 none-padding">Contenido:</label>
                  <span class="input hoshi col-sm-10 none-padding">
                    <div class="preview-small text-center">
                      <img src="{{url('')}}{{"{{urlThumbnail}}"}}?{{"{{imagenTime}}"}}"/>
                    </div>
                    <br>
                  </span>
                  {#<button class="button btn btn-small info-inverted float-left"   title="Descargar previsualización PDF" ng-click="downloadMailPreview()">
                    Descargar previsualización PDF
                  </button>#}
                  <a href="{{url('api/sendmail/downloadmailpreview')}}/{{"{{idMail}}"}}" class="button btn btn-small success-inverted float-left" target="_Blank">
                    Descargar previsualización PDF
                  </a>
                </div>
              </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 margin-top-15px padding-left-40px">
              <div class="form-group">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding">
                  <label class="col-sm-3 none-padding">Nombre del correo:</label>
                  <span class="input hoshi col-sm-9 none-padding" ng-class="{positive: data.name, negative: !data.name}">
                    <em>{{ '{{ !data.name ? "El nombre no debe estar vacío." : "" }}' }}</em>
                    {{ '{{ data.name }}'}} <span ng-class="{'fa fa-check-circle': data.name,  'fa fa-times-circle': !data.name}"></span>
                  </span>
                </div>
              </div>
              <div class="form-group">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding">
                  <label class="col-sm-3 none-padding">Categorías:</label>
                  <span class="input hoshi col-sm-9 none-padding" ng-class="{positive: data.category, negative: !data.category}">
                    <em data-ng-repeat="(key, item) in data.category">{{ '{{ item.name }}'}}{{ '{{ (key+1) == data.category.length ? "": ", " }}' }}</em>
                    <em>{{ '{{ !data.category ? "La categoria no debe estar vacía." : "" }}' }}</em>
                    <span ng-class="{'fa fa-check-circle': data.category,  'fa fa-times-circle': !data.category}"></span>
                  </span>
                </div>
              </div>
              <div class="form-group">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding">
                  <label class="col-sm-3 none-padding">Archivos adjuntos:</label>
                  <span class="input hoshi col-sm-9 none-padding" ng-class="{positive: data.category}">
                    <em data-ng-repeat="(key, item) in data.attachment">{{ '{{ item.name }}'}}{{ '{{ (key+1) == data.attachment.length ? "": ", " }}' }}</em>
                    <span ng-class="{'fa fa-check-circle': data.attachment != ''}"></span>
                  </span>
                </div>
              </div>
              <div class="form-group">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding">
                  <label class="col-sm-3 none-padding">Tamaño de adjuntos:</label>
                  <span class="input hoshi col-sm-9 none-padding" ng-class="{positive: data.sizeAttachment < 2000000,negative:data.sizeAttachment > 2000000}">
                    <em >{{ '{{data.sizeAttachment/1024/1024|number:3}}' }} MB</em>
                    <span ng-class="{'fa fa-check-circle': data.sizeAttachment < 2000000, 'fa fa-times-circle': data.sizeAttachment > 2000000}"></span>
                  </span>
                </div>
              </div>
              <div class="form-group" >
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding">
                  <label class="col-sm-3 none-padding">Estadísticas del correo:</label>
                  <span class="input hoshi col-sm-9 none-padding" ng-class="{positive: data.statisticsEmails,negative:!data.statisticsEmails}">
                    {#                    <em >{{"{{data.statisticsEmails}}"}}</em>#}
                    <span ng-class="{'fa fa-check-circle': data.statisticsEmails,'fa fa-times-circle': !data.statisticsEmails}"></span>
                  </span>
                </div>
              </div>
              <div class="form-group">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding">
                  <label class="col-sm-3 none-padding">Envío de notificación del correo:</label>
                  <span class="input hoshi col-sm-9 none-padding" ng-class="{positive: data.notificationEmails,negative:!data.notificationEmails}">
                    {#<em >{{ '{{ data.notificationEmails }}'}}</em>#}
                    <span ng-class="{'fa fa-check-circle': data.notificationEmails,'fa fa-times-circle': !data.notificationEmails}"></span>
                  </span>
                </div>
              </div>
              <div class="form-group">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding" ng-if="data.facebook.name">
                  <label class="col-sm-4 none-padding margin-top" >Posteo en:</label>
                  <span class="input hoshi col-sm-8 none-padding" ng-class="{positive: data.facebook,negative:!data.facebook}">
                    <img src="{{"{{data.facebook.picture}}"}}" class="img-circle padding-right-10px"/> <em >{{ '{{ data.facebook.name }}'}}</em>
                    <span ng-class="{'fa fa-check-circle': data.facebook,'fa fa-times-circle': !data.facebook}"></span>
                  </span>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding padding-top-3px" ng-if="data.facebook.name">
                  <label class="col-sm-4 none-padding margin-top" >Descripción publicación:</label>
                  <span class="input hoshi col-sm-8 margin-top none-padding" ng-class="{positive: data.facebook.name}">
                    <em >{{ '{{ data.facebook.description == null ? "La publicación de facebook no tiene descripción." : data.facebook.description }}'}}</em>
                    <span ng-class="{'fa fa-check-circle': data.facebook.name}"></span>
                  </span>
                </div>
              </div>
              <div class="form-group">
                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 none-padding">
                  <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 none-padding">
                    <md-switch class="md-warn none-margin" md-no-ink aria-label="Switch No Ink" ng-model="data.test" ng-change="changeTestMail(data.test)">
                    </md-switch>
                  </div>
                  <div class="col-xs-11 col-sm-11 col-md-11 col-lg-11 padding-top-3px">
                    <label for="">Marcar este correo como una prueba</label>
                  </div>
                </div>
              </div>

              {#<div class="form-group">
                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 none-padding">
                  <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 none-padding">
                    <md-switch class="md-warn none-margin" md-no-ink aria-label="Switch No Ink" ng-model="data.twitter">
                    </md-switch>
                  </div>
                  <div class="col-xs-11 col-sm-11 col-md-11 col-lg-11 padding-top-3px">
                    <span class="fa fa-twitter-square color-twitter"></span>
                    <label for="">Postear en twitter</label>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 none-padding">
                  <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 none-padding">
                    <md-switch class="md-warn none-margin" md-no-ink aria-label="Switch No Ink" ng-model="data.facebook">
                    </md-switch>
                  </div>
                  <div class="col-xs-11 col-sm-11 col-md-11 col-lg-11 padding-top-3px">
                    <span class="fa fa-facebook-official color-facebook"></span>
                    <label for="">Postear en facebook</label>
                  </div>
                </div>
              </div>#}
              {#<div class="form-group">
                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 none-padding">
                  <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 none-padding">
                    <md-switch class="md-warn none-margin" md-no-ink aria-label="Switch No Ink" ng-model="data.googleAnalytics">
                    </md-switch>
                  </div>
                  <div class="col-xs-11 col-sm-11 col-md-11 col-lg-11 padding-top-3px">
                    <span class="icon-google-analytics organize-icon"></span>
                    <label for="">Añadir seguimiento de Google analytics</label>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 none-padding">
                  <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 none-padding">
                    <md-switch class="md-warn none-margin" md-no-ink aria-label="Switch No Ink" ng-model="data.sendStatistics">
                    </md-switch>
                  </div>
                  <div class="col-xs-11 col-sm-11 col-md-11 col-lg-11 padding-top-3px">
                    <label for="">Enviar estadísticas automáticamente </label>
                  </div>
                </div>
              </div>#}
            </div>
          </div>
        </div>
        <div class="footer row none-margin">
          <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <a href="{{ url('mail') }}"
               class="button btn danger-inverted"
               data-toggle="tooltip" data-placement="top" title="Salir sin guardar cambios">
              Salir sin guardar cambios
            </a>
          </div>
          <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 text-right">
            <a ui-sref="advanceoptions({id:idMailGet})"
               class="button btn btn-small info-inverted"
               data-toggle="tooltip" data-placement="top" title="Atrás">
              Atrás
            </a>
            <button type="submit" class="button btn btn-small success-inverted"
                    data-toggle="tooltip" data-placement="top" title="Confirmar y finalizar" ng-click="sendConfirmation()">
              Confirmar y finalizar
            </button>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>


<div id="somedialog" class="dialog">
  <div class="dialog__overlay"></div>
  <div class="dialog__content popup-size">
    <div class="morph-shape">
      <svg xmlns="https://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 600 300"
           preserveAspectRatio="none">
      <rect x="3" y="3" fill="none" width="600" height="300"/>
      </svg>
    </div>
    <div class="dialog-inner ">
      <p>Esta función es útil si usted tiene listas de contacto que pertenecen a otros países. Elija esta opción
        solamente si desea enviar a contactos específicos en una zona horaria diferente a la suya.</p>
        {#<select class="undeline-input select2 clearselect" ng-model="zonahoraria" ng-change="changetimezone()">
            <option ng-repeat="zone in timezone " value="{{ "{{zone.gmt}}" }}">{{ "{{zone.countries}}" }}</option>
        </select>#}
      <ui-select class="text-left" ng-model="zonahoraria.zone" ng-required="true"
                 ui-select-required theme="select2" sortable="false"
                 close-on-select="true" ng-change="tick()">
        <ui-select-match placeholder="Seleccione uno">{{ "{{$select.selected.countries}}" }}</ui-select-match>
        <ui-select-choices repeat="zone in timezone | propsFilter: {countries: $select.search}">
          <div ng-bind-html="zone.countries | highlight: $select.search"></div>
        </ui-select-choices>
      </ui-select>
      <div class="clearfix"></div>
      <div class="space"></div>
      <div style="z-index: 999999;" class="text-right">
        <a onClick="closeModal();" class="button shining btn btn-md danger-inverted"
           data-dialog-close>Cancelar</a>
        <a onClick="closeModal();" id="btn-ok" class="button shining btn btn-md success-inverted">Confirmar</a>
      </div>
    </div>
  </div>
</div>

<div id="somedialogConfirm" class="dialog">
  <div class="dialog__overlay"></div>
  <div class="dialog__content popup-size">
    <div class="morph-shape">
      <svg xmlns="https://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 600 300"
           preserveAspectRatio="none">
      <rect x="3" y="3" fill="none" width="600" height="300"/>
      </svg>
    </div>
    <div class="dialog-inner ">
      <p>Tu envío de correo esta siendo procesado, no te preocupes a 
        partir de este momento queda en nuestras manos.</p>
      <div class="clearfix"></div>
      <div class="space"></div>
      <div style="z-index: 999999;" class="text-center">
        <a ng-click="closeModalConfirm()" class="button shining btn btn-md success-inverted"
           data-dialog-close>Aceptar</a>
      </div>
    </div>
  </div>
</div>
