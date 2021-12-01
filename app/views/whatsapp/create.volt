<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="title">
      Creación de un nuevo envío de WhatsApp
    </div>
    <hr class="basic-line" />
  </div>
</div>
<div class="row">
  <form method="post" ng-submit="functions.validate()" class="form-horizontal">
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">
      <div class="block block-info">
        <div class="body ">
          <div class="row">

            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label class="col-sm-4 col-md-4 text-right">* Nombre de envío:</label>
                <span class="input hoshi input-default col-sm-8 col-md-8">
                  <input type="text" required="required"
                    class="form-control ng-pristine ng-valid ng-empty ng-valid-minlength ng-valid-maxlength ng-touched"
                    placeholder="*Nombre" minlength="5" maxlength="50" ng-model="name">
                </span>
              </div>
            </div>
            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label class="col-sm-4 col-md-4 text-right">* Categoría:</label>
                <span class="input-default col-sm-8 col-md-8">
                  <select class="form-control" data-ng-model="category">
                    <option value="" disabled  selected> Seleccione una categoria </option>
                    <option ng-repeat="x in wppCategory" value="{{"{{x.code}}" }}">{{"{{x.name}}"}}</option>
                  </select>
                  <h6 class="color-danger text-justify" ng-show='validateTempWpp'>Por favor elija la categoria del envio.</h6>
                </span>
              </div>
            </div>
            <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label class="col-sm-4 col-md-4 text-right">* Lista de destinatario(s):</label>
                <span class="input-default col-sm-8 col-md-8">
                  <select class="form-control" data-ng-model="contactlist" data-ng-change="countContacts()">
                    <option value="" disabled  selected> Seleccione una Lista de Contactos </option>
                    <option ng-repeat="x in contactListWpp" value="{{"{{x.idContactlist}}" }}">{{"{{x.name}}"}}</option>
                  </select>
                  <h6 class="color-danger text-justify" ng-show='validateTempWpp'>Por favor elija la lista de contactos.</h6>
                </span>
              </div>
            </div>
            <div class="form-group" ng-show="countContactsApproximate">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label class="col-sm-4 col-md-4 text-right">Destinatarios aproximados:</label>
                <span class="input hoshi input-default col-sm-8 col-md-8">
                  {{"{{countContactsApproximate }}"}}
                </span>
              </div>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap"> 
                <label class="col-sm-4 col-md-4 text-right">Etiquetas:</label>
                <div class="col-sm-8 col-md-8">
                  <table id="customers">
                    <tbody>
                      <tr  ng-show="countContactsApproximate>0">
                        <th>Campo</th>
                        <th>Etiqueta</th>
                      </tr>
                      <tr>
                        <td colspan="2" style="text-align: center;" ng-show="countContactsApproximate==0">
                          No hay etiquetas disponibles. Seleccione una lista de contactos o segmento con al menos un contacto.
                        </td>
                      </tr>

                      <tr  class="alt" ng-repeat="(key, value) in countContactsApproximate.tags"  ng-show="countContactsApproximate>0">
                        <td>{{"{{value.name}}"}}</td>
                        <td ng-click="addTag(value.tag)" style="cursor: pointer;">{{"{{value.tag}}"}}</td>
                      </tr>

                    </tbody></table>
                </div>
              </div>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg wrap">
                <label class="col-sm-4 text-right">Elegir plantilla</label>
                <span class="input hoshi input-default col-sm-8" data-ng-click="">
                  <select class="form-control" ng-model="template" data-ng-change="functions.useTemplate()">
                    <option value="" disabled  selected> Seleccione una Plantilla </option>
                    <option ng-repeat="i in listHsmTemplates" value="{{"{{i.id}}" }}">{{"{{i.name}}"}}</option>
                  </select>
                </span>
              </div>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                <label class="col-sm-4 col-md-4 text-right">*Mensaje:{{"{{tag}}"}}</label>
                <span class="input hoshi input-default col-sm-8 col-md-8">
                  <textarea ng-model="data.message" class="form-control" rows="5" style="resize: none;"
                    ng-change="functions.validateInLine()"></textarea>
                  <div class="text-right" data-ng-class="misc.newMessage.length > 160 ? 'negative':''">
                    {{"{{misc.newMessage.length > 0 ?  misc.newMessage.length+'/160':''}}"}}</div>
                  <h6 class="color-danger" ng-show='misc.invalidCharacters'>Recuerde que ninguno de estos caracteres es
                    permitido: ñ Ñ ¡ ¿ á é í ó ú Á É Í Ó Ú ; ´</h6>
                  <h6 class="color-warning" ng-show='misc.existTags'>Si personaliza el mensaje SMS y éste excede los 160
                    caracteres permitidos será cortado en el momento del envío</h6>
                </span>
              </div>
            </div>
          </div>
        </div>
        <div class="footer" align="right">
          <button id="submitButton"
            class="button shining btn btn-xs-round shining shining-round round-button success-inverted"
            data-toggle="tooltip" data-placement="top" title="Guardar">
            <span class="glyphicon glyphicon-ok"></span>
          </button>
          <a href="{{url('whatsapp/')}}"
            class="button shining btn btn-xs-round shining shining-round round-button danger-inverted"
            data-toggle="tooltip" data-placement="top" title="Cancelar">
            <span class="glyphicon glyphicon-remove"></span>
          </a>
          <a ng-show="data.message" ng-click="functions.openPreview()"
            class="button shining btn btn-xs-round shining-round round-button success-inverted" data-toggle="tooltip"
            data-placement="top" title="Visualizar">
            <span class="fa fa-eye" aria-hidden="true"></span>
          </a>

        </div>
      </div>
    </div>
  </form>
  <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">
    <div class="fill-block fill-block-info">
      <div class="header">
        Información
      </div>
      <div class="body">
        <p>
          Recuerde tener en cuenta estas recomendaciones:
        <ul>
          <li>El nombre no puede tener menos de 5 caracteres ni más de 50 caracteres.</li>
          <li>La fecha y hora de envío es para decidir cuándo se van a enviar los SMS y tiene que ser entre las 7:00h y
            las 18:00h (hora de Colombia).</li>
          <li>El envío de notificaciones por defecto está en "No", para activarlo haga clic en el switch para que cambie
            a "Si"</li>
          <li>Los correos deben estar separados por coma "," máximo 8 correos, donde se enviará la notificación cuando
            finalice el envío.</li>
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
        <svg xmlns="https://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 560 280"
          preserveAspectRatio="none">
          <rect x="3" y="3" fill="none" width="556" height="276" />
        </svg>
      </div>
      <div class="dialog-inner">
        <p ng-show="error == 0">Se van a enviar {{"{{countContactsApproximate.counts}}"}} mensaje(s) de SMS*.</p>
        <div ng-show="error != 0" class="div-scroll-100px">
          <p ng-repeat="obj in arrError" ng-show="error != 0">
            <em ng-show="$index != 0">&raquo; {{ "{{obj}}"}}</em>
          </p>
        </div>
        <h2>¿Esta seguro que desea continuar?</h2>
        <div>

          <a ng-click="functions.closeModal()" class="button shining btn btn-md danger-inverted"
            data-dialog-close>Cancelar</a>
          <a ng-click="functions.createcontact();{#addDisabled('btn-ok')#}" id="btn-ok"
            class="button shining btn btn-md success-inverted">Confirmar</a>
        </div>
        <h6>*La cantidad total de envíos puede variar dependiendo de la actividad de los contactos (se eliminan, se
          bloquean, se desuscriben, etc)</h6>
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
          <a ng-click="functions.closePreview()" id="btn-ok"
            class="button shining btn btn-md success-inverted float-right">Ok</a>
        </div>
      </div>
    </div>
  </div>
</div>