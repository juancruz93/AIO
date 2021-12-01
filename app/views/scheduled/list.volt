<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div class="title">
      Programación de envíos
    </div>            
    <hr class="basic-line" />
    <p>
      Aquí podrá tener una visión global de todos los envíos programados en la cuenta.
    </p>
  </div>
</div>

<div class="row" style="margin-top: 1em; margin-bottom: 1em;">
  <div class="col-xs-3 col-sm-3 col-lg-3 wrap">
    <div class="input-group">
      <input class="form-control ng-pristine ng-valid ng-empty ng-touched" id="name" ng-keyup="search()" placeholder="Buscar por nombre de envío" ng-model="filter.name" aria-invalid="false" style="">
      <span class=" input-group-addon" id="basic-addon1">
        <i class="fa fa-search"></i>
      </span>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
    <div ng-cloak>
      <md-content>
        <md-tabs md-dynamic-height md-border-bottom>
          <md-tab label="EMAIL" >
            <md-content class="md-padding">
              <div id="pagination" class="text-center" ng-show="scheduledMail.items.length>0">
                <ul class="pagination">
                  <li ng-class="pageMail == 1 ? 'disabled'  : ''">
                    <a  href="#/" ng-click="pageMail == 1 ? true  : false || fastbackwardMail()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
                  </li>
                  <li  ng-class="pageMail == 1 ? 'disabled'  : ''">
                    <a href="#/"  ng-click="pageMail == 1 ? true  : false || backwardMail()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
                  </li>
                  <li>
                    <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{scheduledMail.total }}"}}
                      </b> registros </span><span>Página <b>{{"{{ pageMail }}"}}
                      </b> de <b>
                        {{ "{{ (scheduledMail.total_pages ) }}"}}
                      </b></span>
                  </li>
                  <li   ng-class="pageMail == (scheduledMail.total_pages) || scheduledMail.total_pages == 0 ? 'disabled'  : ''">
                    <a href="#/" ng-click="pageMail == (scheduledMail.total_pages)  || scheduledMail.total_pages == 0  ? true  : false || pageMail == (scheduledMail.total_pages)  || scheduledMail.total_pages == 0  ? true  : false || forwardMail()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
                  </li>
                  <li   ng-class="pageMail == (scheduledMail.total_pages)  || scheduledMail.total_pages == 0 ? 'disabled'  : ''">
                    <a ng-click="pageMail == (scheduledMail.total_pages)  || scheduledMail.total_pages == 0  ? true  : false || fastforwardMail()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
                  </li>
                </ul>
              </div>
              <table class="table table-bordered" ng-show="scheduledMail.items.length">
                <thead class="theader border-left-thead-5px">
                  <tr>
                    <th style="width: 40%">Nombre del envío</th>
                    <th style="width: 45%">Detalle</th>
                    <th style="width: 15%">Acciones</th>
                  </tr>
                </thead>
                <tbody id="tbody">
                  <tr ng-repeat="mail in scheduledMail.items">
                    <td ng-class="{'border-left-success-5px': mail.status == 'sent','border-left-default-5px': mail.status == 'draft','border-left-violet-5px': mail.status == 'sending','border-left-info-5px': mail.status == 'scheduled','border-left-warning-5px': mail.status == 'paused','border-left-primary-5px': mail.status == 'birthday','border-left-danger-5px': mail.status == 'canceled'}">
                      <h4><b>{{ '{{mail.name}}' }}</b></h4>
                      <h5 ng-class="{'color-sent': mail.status == 'sent','color-draft': mail.status == 'draft','color-sending': mail.status == 'sending','color-scheduled': mail.status == 'scheduled','color-paused': mail.status == 'paused','color-birthday': mail.status == 'birthday','color-canceled': mail.status == 'canceled'}">
                        <b><i>{{ '{{mail.statusEsp}}' }}</i></b>
                      </h5>
                    </td>
                    <td>
                      Fecha y hora de envío: <b>{{ '{{mail.scheduleDate}}' }}</b>
                      <br>
                      Destinatarios aproximados: <b>{{ '{{mail.quantitytarget}}' }}</b>
                      <br>
                      <dd> <em class="extra-small-text">Creado por <strong>{{ '{{mail.createdBy}}' }}</strong> , el <strong ng-bind="mail.created | date:'MM/dd/yyyy HH:mm:ss a'"></strong> </em></dd>
                    <dd> <em class="extra-small-text">Actualizado por <strong> {{ '{{mail.updatedBy}}' }}</strong>, el <strong ng-bind="mail.updated | date:'MM/dd/yyyy HH:mm:ss a'"></strong></em></dd>
                 
                    </td>
                    <td>
                      <a ng-click="pauseMailAn(mail.idMail)" ng-show="mail.status == 'sending'" class="button btn btn-xs-round warning-inverted" data-toggle="tooltip" data-placement="top" title="Pausar envio">
                        <md-tooltip>
                          Pausar envio
                        </md-tooltip>
                        <span class="glyphicon glyphicon-pause"></span>
                      </a>
                      <a ng-click="cancelMailAn(mail.idMail)" ng-show="mail.status == 'sending'" class="button btn btn-xs-round danger-inverted" data-toggle="tooltip" data-placement="top" title="Cancelar envio">
                        <md-tooltip>
                          Cancelar envio
                        </md-tooltip>
                        <span class="fa fa-ban"></span>
                      </a>
                      <a ng-click="confirmCancel(mail.idMail)" ng-show="mail.status == 'paused' || mail.status == 'scheduled'" class="button btn btn-xs-round danger-inverted" title="Cancelar envio">
                        <md-tooltip md-direction="bottom">
                          Cancelar envio
                        </md-tooltip>
                        <span class="fa fa-ban"></span>
                      </a>
                      <a ng-click="resumeMailAn(mail.idMail)" ng-show="mail.status == 'paused'" class="button btn btn-xs-round success-inverted" data-toggle="tooltip" data-placement="top" title="Reanudar envio">
                        <md-tooltip>
                          Reanudar envio
                        </md-tooltip>
                        <span class="glyphicon glyphicon-play"></span>
                      </a>

                    </td>
                  </tr>
                </tbody>
              </table>
              <div id="pagination" class="text-center" ng-show="scheduledMail.items.length>0">
                <ul class="pagination">
                  <li ng-class="pageMail == 1 ? 'disabled'  : ''">
                    <a  href="#/" ng-click="pageMail == 1 ? true  : false || fastbackwardMail()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
                  </li>
                  <li  ng-class="pageMail == 1 ? 'disabled'  : ''">
                    <a href="#/"  ng-click="pageMail == 1 ? true  : false || backwardMail()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
                  </li>
                  <li>
                    <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{scheduledMail.total }}"}}
                      </b> registros </span><span>Página <b>{{"{{ pageMail }}"}}
                      </b> de <b>
                        {{ "{{ (scheduledMail.total_pages ) }}"}}
                      </b></span>
                  </li>
                  <li   ng-class="pageMail == (scheduledMail.total_pages) || scheduledMail.total_pages == 0 ? 'disabled'  : ''">
                    <a href="#/" ng-click="page == (scheduledMail.total_pages)  || scheduledMail.total_pages == 0  ? true  : false || pageMail == (scheduledMail.total_pages)  || scheduledMail.total_pages == 0  ? true  : false || forwardMail()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
                  </li>
                  <li   ng-class="pageMail == (scheduledMail.total_pages)  || scheduledMail.total_pages == 0 ? 'disabled'  : ''">
                    <a ng-click="pageMail == (scheduledMail.total_pages)  || scheduledMail.total_pages == 0  ? true  : false || fastforwardMail()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
                  </li>
                </ul>
              </div>
              <div ng-show="!scheduledMail.items.length" aria-hidden="false" class="" style="">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                  <div class="block block-success">
                    <div class="body success-no-hover text-center">
                      <h2>
                        No se encontraron envíos programados de correo actualmente<span ng-if="filter.name!=null"> con estos criterios búsqueda</span>.
                      </h2>

                    </div>
                  </div>
                </div>
              </div>
            </md-content>
          </md-tab>
          <md-tab label="SMS">
            <md-content class="md-padding">
              <div id="pagination" class="text-center" ng-show="scheduledSms.items.length>0">
                <ul class="pagination">
                  <li ng-class="pageSMS == 1 ? 'disabled'  : ''">
                    <a  href="#/" ng-click="page == 1 ? true  : false || fastbackwardSMS()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
                  </li>
                  <li  ng-class="pageSMS == 1 ? 'disabled'  : ''">
                    <a href="#/"  ng-click="page == 1 ? true  : false || backwardSMS()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
                  </li>
                  <li>
                    <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{scheduledSms.total }}"}}
                      </b> registros </span><span>Página <b>{{"{{ pageSMS }}"}}
                      </b> de <b>
                        {{ "{{ (scheduledSms.total_pages ) }}"}}
                      </b></span>
                  </li>
                  <li   ng-class="pageSMS == (scheduledSms.total_pages) || scheduledSms.total_pages == 0 ? 'disabled'  : ''">
                    <a href="#/" ng-click="page == (scheduledSms.total_pages)  || scheduledSms.total_pages == 0  ? true  : false || pageSMS == (scheduledSms.total_pages)  || scheduledSms.total_pages == 0  ? true  : false || forwardSMS()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
                  </li>
                  <li   ng-class="pageSMS == (scheduledSms.total_pages)  || scheduledSms.total_pages == 0 ? 'disabled'  : ''">
                    <a ng-click="pageSMS == (scheduledSms.total_pages)  || scheduledSms.total_pages == 0  ? true  : false || fastforwardSMS()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
                  </li>
                </ul>
              </div>
              <table class="table table-bordered" ng-show="scheduledSms.items.length">
                <thead class="theader border-left-thead-5px">
                  <tr>
                    <th style="width: 40%">Nombre del envío</th>
                    <th style="width: 45%">Detalle</th>
                    <th style="width: 15%">Acciones</th>
                  </tr>
                </thead>
                <tbody id="tbody">
                  <tr ng-repeat="sms in scheduledSms.items">
                    <td ng-class="{'border-left-success-5px': sms.status == 'sent','border-left-default-5px': sms.status == 'draft','border-left-violet-5px': sms.status == 'sending','border-left-info-5px': sms.status == 'scheduled','border-left-warning-5px': sms.status == 'paused','border-left-primary-5px': sms.status == 'birthday','border-left-danger-5px': sms.status == 'canceled'}">
                      <h4><b>{{ '{{sms.name}}' }}</b></h4>
                      <h5 ng-class="{'color-sent': sms.status == 'sent','color-draft': sms.status == 'draft','color-sending': sms.status == 'sending','color-scheduled': sms.status == 'scheduled','color-paused': sms.status == 'paused','color-birthday': sms.status == 'birthday','color-canceled': sms.status == 'canceled'}">
                        <b><i>{{ '{{sms.statusEsp}}' }}</i></b>
                      </h5>
                       
                    </td>
                    <td>
                      Fecha y hora de envío: <b>{{ '{{sms.startdate}}' }}</b>
                      <br>
                      Destinatarios aproximados: <b>{{ '{{sms.sent}}' }}</b>
                      <br>
                      <dd> <em class="extra-small-text">Creado por <strong>{{ '{{sms.createdBy}}' }}</strong> , el <strong ng-bind="sms.created | date:'MM/dd/yyyy HH:mm:ss a'"></strong> </em></dd>
                    <dd> <em class="extra-small-text">Actualizado por <strong> {{ '{{sms.updatedBy}}' }}</strong>, el <strong ng-bind="sms.updated | date:'MM/dd/yyyy HH:mm:ss a'"></strong></em></dd>
                 
                    </td>
                    <td>
                      <a ng-click="pauseSmsAn(sms.idSms)" ng-show="sms.status == 'sending'" class="button btn btn-xs-round warning-inverted" data-toggle="tooltip" data-placement="top" title="Pausar envio">
                        <md-tooltip>
                          Pausar
                        </md-tooltip>
                        <span class="glyphicon glyphicon-pause"></span>
                      </a>
                      <a ng-click="cancelSmsAn(sms.idSms)" ng-show="sms.status == 'sending' || sms.status == 'paused' || sms.status == 'scheduled'" class="button btn btn-xs-round danger-inverted" data-toggle="tooltip" data-placement="top" title="Cancelar envio">
                        <md-tooltip>
                          Cancelar
                        </md-tooltip>
                        <span class="fa fa-ban"></span>
                      </a>
                      <a ng-click="resumeSmsAn(sms.idSms)" ng-show="sms.status == 'paused'" class="button btn btn-xs-round success-inverted" data-toggle="tooltip" data-placement="top" title="Reanudar envio">
                        <md-tooltip>
                          Reanudar
                        </md-tooltip>
                        <span class="glyphicon glyphicon-play"></span>
                      </a>
                    </td>
                  </tr>
                </tbody>
              </table>
              <div id="pagination" class="text-center" ng-show="scheduledSms.items.length>0">
                <ul class="pagination">
                  <li ng-class="pageSMS == 1 ? 'disabled'  : ''">
                    <a  href="#/" ng-click="pageSMS == 1 ? true  : false || fastbackwardSMS()" class="new-element"><i class="disabled glyphicon glyphicon-fast-backward"></i></a>
                  </li>
                  <li  ng-class="pageSMS == 1 ? 'disabled'  : ''">
                    <a href="#/"  ng-click="pageSMS == 1 ? true  : false || backwardSMS()" class="new-element"><i class="glyphicon glyphicon-step-backward"></i></a>
                  </li>
                  <li>
                    <span><b><script id="metamorph-58-start" type="text/x-placeholder"></script>{{ "{{scheduledSms.total }}"}}
                      </b> registros </span><span>Página <b>{{"{{ pageSMS }}"}}
                      </b> de <b>
                        {{ "{{ (scheduledSms.total_pages ) }}"}}
                      </b></span>
                  </li>
                  <li   ng-class="pageSMS == (scheduledSms.total_pages) || scheduledSms.total_pages == 0 ? 'disabled'  : ''">
                    <a href="#/" ng-click="page == (scheduledSms.total_pages)  || scheduledSms.total_pages == 0  ? true  : false || pageSMS == (scheduledSms.total_pages)  || scheduledSms.total_pages == 0  ? true  : false || forwardSMS()" class="new-element"><i class="glyphicon glyphicon-step-forward"></i></a>
                  </li>
                  <li   ng-class="pageSMS == (scheduledSms.total_pages)  || scheduledSms.total_pages == 0 ? 'disabled'  : ''">
                    <a ng-click="pageSMS == (scheduledSms.total_pages)  || scheduledSms.total_pages == 0  ? true  : false || fastforwardSMS()" class="new-element"><i class="glyphicon glyphicon-fast-forward"></i></a>
                  </li>
                </ul>
              </div>
              <div ng-show="!scheduledSms.items.length" aria-hidden="false" class="" style="">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                  <div class="block block-success">
                    <div class="body success-no-hover text-center">
                      <h2>
                        No existen envíos programados de sms actualmente<span ng-if="filter.name!=null"> con estos criterios búsqueda</span>.
                      </h2>

                    </div>
                  </div>
                </div>
              </div>
            </md-content>
          </md-tab>

        </md-tabs>
      </md-content>
    </div>

  </div>

  <div id="dialogCancel" class="dialog">
    <div class="dialog__overlay"></div>
    <div class="dialog__content">
      <div class="morph-shape">
        <svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 560 280" preserveAspectRatio="none">
          <rect x="3" y="3" fill="none" width="556" height="276"/>
        </svg>
      </div>
      <div class="dialog-inner">
        <h2>¿Esta seguro?</h2>
        <div>
          Si cancela este correo, no se podrá reanudar posteriormente.
        </div>
        <br>
        <div>
          <a onClick="closeModalCancel();" class="button shining btn btn-md danger-inverted" data-dialog-close>Cancelar</a>
          <a href="" ng-click="cancelMail()" id="btn-ok" class="button shining btn btn-md success-inverted">Confirmar</a>
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

    function openModalCancel() {
        $('#dialogCancel').addClass('dialog--open');
    }
    function closeModalCancel() {
        $('#dialogCancel').removeClass('dialog--open');
    }
  </script>
</div>
</div>

