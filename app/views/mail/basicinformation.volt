<script>
  //$.fn.select2.defaults.set("theme", "classic");

  $(".select2").select2({
    theme: 'classic'
  });
</script>

<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="subtitle">
      <em>Información básica del correo</em>
    </div>
    <br>
    <p class="small-text">
      Configura la información básica acerca del correo, como un nombre para identificar el correo, el asunto y
      remitente.
    </p>
  </div>
</div>

<div class="row">
  <form class="form-horizontal" ng-submit="basicInformationRegister()">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
      <div class="block block-info">
        <div class="body row">
          <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">
            <div>
              <div class="margin-top">
                <label class=" text-right">*Nombre del correo </label>
                <span class="fa fa-info-circle color-gray drop_info" title="Información"></span>
                <div class="col-xs-12 col-sm-12 col-md-11 col-lg-11 none-padding">
                  <span class="input hoshi input-default">
                    {#<input type="text" maxlength="50" placeholder="*Nombre del correo" class="undeline-input "
                           ng-model="data.nameMail" required>#}
                    {{ mailForm.render('name', {'class': 'undeline-input', 'placeholder': '*Nombre del correo', 'ng-model': 'data.name', 'required': 'true','maxlength':100} ) }}
                  </span>
                </div>

              </div>
              <div style="position: absolute;" class="info_cointainer">
                <div class="cuerpo arriba-izquierda">Este nombre es para su uso personal, no le aparecerá a nadie más.</div>
              </div>
            </div>

            <div>
              <div>
                <label class="text-right margin-top-15px">*Nombre del remitente </label>
                <span class="fa fa-info-circle color-gray drop_info" title="Información"></span>
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding">
                  <div class="col-xs-10 col-sm-10 col-md-10 col-lg-10 none-padding">
                    <span class="input hoshi input-default">
                      <div data-ng-show="showInputName">
                        <input placeholder="*Nombre del remitente" data-ng-model="senderName"
                               maxlength="200"
                               class="undeline-input">
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
                  <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 margin-top">
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
              <div style="position: absolute;" class="info_cointainer">
                <div class="cuerpo arriba-izquierda">Use un nombre con el que lo reconozcan fácilmente, <br>como su nombre o el de su empresa.</div>
              </div>
            </div>

            <div>
              <div>
                <label class="text-right margin-top-15px">*Correo del remitente </label>
                <span class="fa fa-info-circle color-gray drop_info" title="Información"></span>
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding">
                  <div class="col-xs-10 col-sm-10 col-md-10 col-lg-10 none-padding">
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
                  <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 margin-top">
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
              <div style="position: absolute;" class="info_cointainer">
                <div class="cuerpo arriba-izquierda">Utilice el propio dominio o dirección de correo electrónico de su negocio.<br>Esto le dará una mejor tasa de aperturas que al usar direcciones de correo <br>electrónico de ISP genérico (por ejemplo @gmail, @hotmail @yahoo)</div>
              </div>

            </div>

            <div>
              <div>
                <label class="text-right margin-top-15px">Responder a</label>
                <span class="fa fa-info-circle color-gray drop_info" title="Información"></span>
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding">
                    <div class="col-xs-10 col-sm-10 col-md-10 col-lg-10 none-padding">
                      <span class="input hoshi input-default">
                        <div data-ng-show="showInputReplyto">
                          <input placeholder="Responder a" maxlength="200"
                             class="undeline-input" ng-model="replyTo">
                          {#<input placeholder="Responder a" type="email" maxlength="200"
                             class="undeline-input" ng-model="data.replyTo">#}
                        </div>
                        <div data-ng-show="showSelectReplyto">
                          <ui-select ng-model="data.replyToSelect" ng-required="true" theme="select2"
                                     sortable="false" close-on-select="true">
                            <ui-select-match
                              placeholder="Seleccione uno">{{ "{{$select.selected.email}}" }}</ui-select-match>
                            <ui-select-choices
                              repeat="key.idReplyTo as key in replyToArray | propsFilter: {email: $select.search}">
                              <div ng-bind-html="key.email | highlight: $select.search"></div>
                            </ui-select-choices>
                          </ui-select>
                        </div>
                      </span>
                    </div>
                    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 margin-top">
                      <div data-ng-show="showIconsReplyto">
                        <a class="color-primary" data-ng-click="changeStatusInputReplyto()" href=""><span
                            class="fa fa-plus " title="Agregar otro email"></span></a>
                      </div>
                      <div data-ng-show="showIconsSaveReplyto">
                        <a class="negative" data-ng-click="changeStatusInputReplyto()" href=""><span
                            class="glyphicon glyphicon-remove"
                            title="Cancelar"></span></a>
                        <a class="positive" data-ng-click="saveReplyto()" href=""><span
                            class="glyphicon glyphicon-ok margin-left-10"
                            title="Guardar"></span></a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div style="position: absolute;" class="info_cointainer">
                <div class="cuerpo arriba-izquierda">Las respuestas se enviarán a este correo electrónico</div>
              </div>
            </div>

            <div>
              <div>
                <label class="text-right margin-top-15px">*Asunto</label>
                <span class="fa fa-info-circle color-gray drop_info" title="Información"></span>
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding">
                  <div class="col-xs-12 col-sm-12 col-md-11 col-lg-11 none-padding">
                    <span class="input hoshi input-default">
                      {#<input placeholder="*Asunto" maxlength="200" class="undeline-input" required ng-model="data.subject">#}
                      {{ mailForm.render('subject', {'class': 'undeline-input', 'placeholder': '*Asunto', 'ng-model': 'data.subject', 'required': 'true','maxlength':100} ) }}
                    </span>
                  </div>
                </div>
              </div>
              <div style="position: absolute;" class="info_cointainer">
                <div class="cuerpo arriba-izquierda">Utilice palabras adecuadas para evitar los filtros de spam. NUNCA escriba todo en <br>MAYÚSCULAS. Evite utilizar palabras sospechosas de Spam (Ej. Cash, Efectivo, Dieta,<br>Viagra, Gratis, Dinero, etc.) no utilice caracteres especiales (EJ. C@$h) y no utilice puntuación <br>excesiva (Ej. Ahora Mismo!!!!!!!!) todo esto activa los filtros de SPAM.</div>
              </div>
            </div>       

            <div>
              <div>
                <label class="text-right margin-top-15px">*Categoría</label>
                <span class="fa fa-info-circle color-gray drop_info" title="Información"></span>
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 none-padding">
                  <div class="col-xs-10 col-sm-10 col-md-10 col-lg-10 none-padding">
                    <span class="input hoshi input-default">
                      <div data-ng-show="showInputCategory">
                        <input placeholder="*Nombre de la categoria" data-ng-model="categoryName"
                               maxlength="200"
                               class="undeline-input">
                      </div>
                      <div data-ng-show="showCategoryName">
                        <ui-select multiple ng-model="data.category" ng-required="true" ui-select-required class='min-width-100'
                                   theme="select2" title="" sortable="false" close-on-select="true">
                          <ui-select-match >{{"{{$item.name}}"}}</ui-select-match>
                          <ui-select-choices repeat="key.idMailCategory as key in availibleCategory | propsFilter: {name: $select.search}">
                            <div ng-bind-html="key.name | highlight: $select.search"></div>
                          </ui-select-choices>
                        </ui-select>
                      </div>
                    </span>
                  </div>
                  <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 margin-top">
                    <div data-ng-show="showIconsCategory">
                      <a class="color-primary" data-ng-click="changeStatusNameCategory()" href=""><span
                          class="fa fa-plus " title="Agregar otra categoria"></span></a>
                    </div>
                    <div data-ng-show="showIconsSaveCategory">
                      <a class="negative" data-ng-click="changeStatusNameCategory()" href=""><span
                          class="glyphicon glyphicon-remove"
                          title="Cancelar"></span></a>
                      <a class="positive" data-ng-click="saveCategory()" href=""><span
                          class="glyphicon glyphicon-ok margin-left-10"
                          title="Guardar"></span></a>
                    </div>
                  </div>
                </div>
              </div>
              <div style="position: absolute;" class="info_cointainer">
                <div class="cuerpo arriba-izquierda">Use un nombre con el que lo reconozcan fácilmente, <br>como su nombre o el de su empresa.</div>
              </div>
            </div>

            <div>
              <div class="col-xs-11 col-sm-11 col-md-11 col-lg-11 none-padding">
                <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1  none-padding">
                  <md-switch class="md-warn none-margin-left" md-no-ink aria-label="Switch No Ink"
                             ng-model="data.test">
                  </md-switch>
                </div>
                <div class="col-xs-10 col-sm-10 col-md-10 col-lg-10 none-padding">
                  <label class="margin-top-15px">
                    Marcar este envío como prueba.
                  </label>
                  <span class="fa fa-info-circle color-gray drop_info" title="Información"></span>
                </div>
                <div style="position: absolute;" class="info_cointainer">
                  <div class="cuerpo arriba-izquierda">Esto es solamente una marca que te ayudará a separar los envíos reales de los de pruebas</div>
                </div>
              </div>

            </div>
          </div>

          <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">
            <div class="fill-block fill-block-primary">
              <div class="header">
                Instrucciones
              </div>
              <div class="body">
                <p>
                  Antes de comenzar, por favor lea atentamente la siguiente información:
                <ul>
                  <li>Los campos con asterisco (*), son obligatorios.</li>
                  <li>El nombre del correo, solo se usa para que identifique el mismo.</li>
                  <li>El nombre del remitente debe contener máximo 80 caracteres.</li>
                  <li>El correo del remitente debe contener un dominio privado y máximo 100
                    caracteres.
                  </li>
                  <li>Si marca el envío como una prueba, le ayudará para filtrar los envíos en la 
                    listado de envíos de correo</li>
                  <li>El correo de responder a debe contener máximo 100 caracteres.</li>
                  <li>El asunto del correo debe contener máximo 100 caracteres.</li>
                </ul>
                </p>
              </div>
            </div>
          </div>
        </div>
        <div class="footer row none-margin">
          <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <a href="{{ url('mail') }}"
               class="button btn btn-small danger-inverted"
               data-toggle="tooltip" data-placement="top" title="Cancelar">
              Cancelar
            </a>
          </div>
          <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 text-right">
            <button class="button btn btn-small success-inverted"
                    data-toggle="tooltip" data-placement="top" title="Guardar y continuar">
              Guardar y continuar
            </button>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>
<script>
  $(document).ready(function () {
    $(".drop_info").mouseover(function () {
      $(this).parent().parent().find('.info_cointainer').show();
    });
    $(".drop_info").mouseout(function () {
      $(this).parent().parent().find('.info_cointainer').hide();
    });
  });
</script>
