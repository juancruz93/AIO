{% extends "templates/default.volt" %}
{% block css %}
  {# Notifications #}
  {{ partial("partials/css_notifications_partial") }}
  {# Bootstrap Toggle #}
  {{ stylesheet_link('library/bootstrap-toggle-master/css/bootstrap-toggle.min.css') }}
  {# Select 2 #}
  {{ stylesheet_link('library/select2-4.0.0/css/select2.css') }}
  <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/angular_material/1.1.0/angular-material.min.css">
  {{ stylesheet_link('library/dialog-effects/css/dialog.css') }}
  {{ stylesheet_link('library/dialog-effects/css/dialog-wilma.css') }}
{% endblock %}    

{% block js %}
  {{ javascript_include('library/dialog-effects/js/dialogFx.js') }}
  {# Angular #}
{#  {{ javascript_include('js/angular/account/controller.js') }}#}
{{ javascript_include('js/angular/account/dist/account.680d83bbdb01bba99d55.min.js') }}
  {{ javascript_include('library/angular-keep-values/angular-keep-values.min.js') }}
  {# Notifications #}
  {{ partial("partials/js_notifications_partial") }}
  {{ partial("partials/slideontop_notification_partial") }}
  {# Bootstrap Toggle #}
  {{ javascript_include('library/bootstrap-toggle-master/js/bootstrap-toggle.min.js') }}
  {# Select 2 #}
  {{ javascript_include('library/select2/js/select2.min.js') }}
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-aria.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-animate.min.js"></script>

  <script src="https://ajax.googleapis.com/ajax/libs/angular_material/1.0.7/angular-material.min.js"></script>
  <script>

    function verPreview() {
      $.post("{{url('footer/previewindex')}}/" + $('#idFooter').val(), function (preview) {
        var e = preview.preview;
        $("#preview-modal-content").empty();
        //console.log(e);
        $('<iframe frameborder="0" width="100%" height="100%"/>').appendTo('#preview-modal-content').contents().find('body').append(e);
      });
    }

    function openModalConfirm() {
      $('.dialog').addClass('dialog--open');
    }

    function closeModalConfirm() {
      $('.dialog').removeClass('dialog--open');
    }

    $(function () {
      $('#toggle-one').bootstrapToggle({
        on: 'On',
        off: 'Off',
        onstyle: 'success',
        offstyle: 'danger',
        size: 'small'
      });
      $('#toggle-two').bootstrapToggle({
        on: 'Sí',
        off: 'No',
        onstyle: 'success',
        offstyle: 'danger',
        size: 'small'
      });

      $(".select2").select2({
        theme: 'classic'
      });
    });

    var idAllied = '{{ account.idAllied }}';
    var diskSpaceAllied = '{{ account.Allied.Alliedconfig.diskSpace }}';
    var idCountry = '{{ account.City.State.idCountry }}';
    var idState = '{{ account.City.idState}}';
    var idCity = '{{ account.idCity}}';
    var category = '{{ category }}';
    var idFooter = '{{ account.AccountConfig.idFooter}}';
    var footerEditable = '{{ account.AccountConfig.footerEditable}}';
    var senderAllowed = '{{ account.AccountConfig.senderAllowed}}';
    var expiryDate = '{{ account.AccountConfig.expiryDate }}';
    var status = '{{ account.status }}';
    //var attachments = '{{ account.attachments }}';
    var paymentPlan = '{{ paymentPlan }}';
    var hourInit = '{{ hourInit }}';
    var hourEnd = '{{ hourEnd }}';
  </script>
  {{ javascript_include('library/angular-1.5/js/angular-route.min.js') }}
  {{ javascript_include('library/angular-ui-router/angular-ui-router.min.js') }}
{% endblock %}

{% block content %}
  <div ng-app="aio" ng-controller="ctrlAccount" ng-cloak>
    <div class="clearfix"></div>
    <div class="space"></div>

    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
        <div class="title">
          Edición de la Cuenta <strong>{{ account.name }}</strong>
        </div>
        <hr class="basic-line"/>
      </div>
    </div>

    <div class="row">
      <form ng-submit="editAccount({{ account.idAccount }}, false)">
        <div class="col-xs-12 col-sm-12 col-md-7 col-lg-7 wrap">
          <div class="block block-info">
            <div class="body">
              <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-12">

                  <div class="form-group">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                      <label class="col-sm-4 text-right">*Nombre</label>
                      <span class="input hoshi input-default col-sm-8">
                        {{ form.render('name', {'class': 'undeline-input' , 'placeholder':'*Nombre', 'required' : 'true', 'ng-model':'name', 'keep-current-value':'' }) }}
                      </span>
                    </div>
                  </div>

                  <div class="form-group">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                      <label class="col-sm-4 text-right">*Telefono</label>
                      <span class="input hoshi input-default  col-sm-8">
                        {{ form.render('phone', {'class': 'undeline-input' , 'placeholder':'Telefono', 'required':'true', 'ng-model':'phone', 'keep-current-value':'' }) }}
                      </span>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                      <label class="col-sm-4 text-right">*Direccion</label>
                      <span class="input hoshi input-default  col-sm-8">
                        {{ form.render('address', {'class': 'undeline-input' , 'placeholder':'Direccion', 'required':'true', 'ng-model':'address', 'keep-current-value':''}) }}
                      </span>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                      <label class="col-sm-4 text-right">*Correo electrónico</label>
                      <span class="input hoshi input-default  col-sm-8">
                        {{ form.render('email', {'class': 'undeline-input' , 'placeholder':'Email', 'ng-model':'email', 'required':'true', 'keep-current-value':'' }) }}
                      </span>
                    </div>
                  </div>

                  <div class="form-group">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                      <label class="col-sm-4 text-right ">*Nit/Identificación de la empresa</label>
                      <span class="input hoshi input-default  col-sm-8">
                        {{ form.render('nit', {'class': 'undeline-input' , 'placeholder':'Nit/Identificación de la empresa', 'required':'true', 'ng-model':'nit', 'keep-current-value':'' }) }}
                      </span>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                      <label  class="col-sm-4 text-right ">Url sitio web</label>
                      <span class="input hoshi input-default  col-sm-8">
                        {{ form.render('url', {'class': 'undeline-input' , 'placeholder':'url de la empresa', 'ng-model':'urlWebSite', 'keep-current-value':'' }) }}
                      </span>
                    </div>
                  </div>

                  <div class="form-group">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                      <label class="col-sm-4 text-right">*Pais:</label>
                      <span class="input hoshi input-default col-sm-8">
                        <select class="undeline-input select2" required ng-change="selectCountry()" ng-model="countrySelected">
                          <option value="">Seleccionar</option>
                          <option ng-repeat="c in country track by $index" value="{{ "{{c.idCountry}}" }}">{{ "{{c.name}}" }}</option>
                        </select>
                      </span>
                    </div>
                  </div>

                  <div class="form-group">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                      <label class="col-sm-4 text-right">*Departamento:</label>
                      <span class="input hoshi input-default  col-sm-8">
                        <select class="undeline-input select2 " required ng-change="selectState()" ng-model="stateSelected">
                          <option value="">Seleccionar</option>
                          <option ng-repeat="s in state track by $index" value="{{ "{{s.idState}}" }}">{{ "{{s.name}}" }}</option>
                        </select>
                      </span>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                      <label class="col-sm-4 text-right">*Ciudad:</label>
                      <span class="input hoshi input-default  col-sm-8">
                        <select class="undeline-input select2" name="idCity" required ng-model="citySelected">
                          <option value="">Seleccionar</option>
                          <option ng-repeat="ci in cities track by $index" value="{{ "{{ci.idCity}}" }}">{{ "{{ci.name}}" }}</option>
                        </select>
                      </span>
                    </div>
                  </div>

                  <div class="form-group">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                      <span class="input hoshi input-default col-sm-12">
                        <div class="fill-block fill-block-warning">
                          <table class="table table-bordered table-responsive">
                            <thead class="theader">
                              <tr>
                                <th></th>
                                <th>Cantidad configurada en el plan actual</th>
                              </tr>
                            </thead>
                            <tbody class="color-default">
                              <tr>
                                <td>Espacio en disco</td>
{#                                <td>{{ '{{ payment.diskSpace }}' }} MB</td>#}
                                <td>{{ '{{ paymentBefore.diskSpace }}' }} MB</td>
                              </tr>
                             {#<tr data-ng-repeat="item in payment.planxservice">
                                <td>{{ '{{ item.service }}' }}</td>
                                <td>{{ '{{ item.amount }}' }}</td>
                              </tr>#}
                              <tr data-ng-repeat="item in paymentBefore.planxservice">
                                <td>{{ '{{ item.service }}' }}</td>
                                <td>{{ '{{ item.amount }}' }}</td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                      </span>
                    </div>
                  </div>

                  <div class="form-group">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                      <label  class="col-sm-3 text-right">*Planes de pago:</label>
                      <span class="input hoshi input-default  col-sm-8">
                        <select class="undeline-input select2 clearselect" ng-change="selectPlanSelected(); calculeDiskSpace();" ng-model="paymentPlanSelected">
{#                          <option value=""></option>#}
                          <option ng-repeat="payment in paymentPlan " value="{{"{{payment.idPaymentPlan}}"}}">{{"{{payment.name}}"}}</option>
                        </select>
                      </span>
                      <span class="input hoshi input-default col-sm-1" data-toggle="tooltip" title="El cambio de plan tardara mucho tiempo">
                        <span class="glyphicon glyphicon-time"></span>
                      </span>
                    </div>
                  </div>
                  <div class="form-group" ng-show="showDetail">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                      <span class="input hoshi input-default col-sm-12">
                        <div class="fill-block fill-block-warning">
                          <table class="table table-bordered table-responsive">
                            <thead class="theader">
                              <tr>
                                <th></th>
                                <th>Cantidad configurada en el plan</th>
                                <th>Cantidad disponible</th>
                                <th>Cantidad después de la operación</th>
                              </tr>
                            </thead>
                            <tbody class="color-default">
                              <tr>
                                <td>Espacio en disco</td>
                                <td>{{ '{{ plan.diskSpace }}' }} MB</td>
                                <td class="color-primary">{{ account.Allied.Alliedconfig.diskSpace }} MB</td>
                                <td ng-class="totalspace >= 0 ? 'positive' : 'negative' "><b>{{ '{{ totalspace }}' }} MB </b></td>
                              </tr>
                              <tr data-ng-repeat="item in plan.planxservice">
                                <td>{{ '{{ item.service }}' }}</td>
                                <td>{{ '{{ item.amount }}' }}</td>
                                <td class="color-primary">{{ '{{ item.amountConfig }}' }}</td>
                                <td ng-class="item.totalAmount > 0 ? 'positive' : 'negative' " ><b>{{ '{{ item.totalAmount }}' }}</b></td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                      </span>
                    </div>
                  </div>

                  <div class="form-group">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                      <label  class="col-sm-4 text-right">*Categoría</label>
                      <span class="input hoshi input-default  col-sm-8">
                        <select class="undeline-input select2 clearselect" ng-model="idAccountCategory" required="required" data-ng-change="showExpiryDate()">
                          <option value=""></option>
                          <option ng-repeat="cate in categories" value="{{"{{cate}}"}}">{{"{{cate.name}}"}}</option>
                        </select>
                      </span>
                    </div>
                  </div>

                  <div class="form-group">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                      <label  class="col-sm-4 text-right">¿Permitir al usuario agregar mas remitentes?:</label>
                      <span class="input hoshi input-default  col-sm-8">
                        <select class="undeline-input select2 clearselect" ng-model="senderAllowed" required="required">
                          <option value=""></option>
                          <option value="0">No</option>
                          <option value="1">Si</option>
                        </select>
                      </span>
                    </div>
                  </div>

                  <div class="form-group">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                      <label  class="col-sm-4 text-right ">*Seleccionar footer:</label>
                      <span class="input hoshi input-default  col-sm-7">
                        <select id="idFooter" class="undeline-input select2 clearselect" ng-model="idFooter" required="required">
                          <option value=""></option>
                          <option ng-repeat="footer in footers" value="{{"{{footer.idFooter}}"}}">{{"{{footer.name}}"}}</option>
                        </select>
                      </span>
                      <span class="input hoshi input-default col-sm-1">
                        <a class="button shining btn btn-xs-round round-button default-inverted" href="#preview-footer-modal" data-toggle="modal" onclick="verPreview();" data-placement="top" title="Previsualizar">
                          <span class="glyphicon glyphicon-eye-open"></span>
                        </a>
                      </span>
                    </div>
                  </div>

                  <div class="form-group">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                      <label  class="col-sm-4 text-right ">*Footer editable:</label>
                      <span class="input hoshi input-default  col-sm-8">
                        <select class="undeline-input select2 clearselect" ng-model="footerEditable" required="required">
                          <option value=""></option>
                          <option value="0">No</option>
                          <option value="1">Si</option>
                        </select>
                      </span>
                    </div>
                  </div>

                  <div class="form-group" data-ng-show="expirationDate == 1">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                      <label  class="col-sm-4 text-right">*Fecha de expiración:</label>
                      <span class="input hoshi input-default  col-sm-8">
                        <input type="date" class="input-field input-hoshi undeline-input" data-ng-model="expiryDate">
                      </span>
                    </div>
                  </div>

                  <div class="form-group">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                      <label  class="col-sm-4 text-right">Periodo de tolerancia:</label>
                      <span class="input hoshi input-default  col-sm-8">
                        {{ form.render('tolerancePeriod', {'class': 'undeline-input' , 'placeholder':'Periodo de tolerancia', 'ng-model':'tolerancePeriod', 'keep-current-value':''}) }}
                      </span>
                    </div>
                  </div>

                  {#<div class="form-group">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                      <label  class="col-sm-4 text-right">
                        *Permitir adjuntos: 
                        <span class="fa fa-info-circle color-gray drop_info" data-toggle="tooltip" data-placement="bottom" title="Esta opción permite añadir archivos adjuntos en los envíos de correo"></span>
                      </label>
                      <span class="input hoshi input-default  col-sm-8">
                        <md-switch class="md-primary none-margin" data-ng-model="attachments" md-no-ink aria-label="Switch 2">
                          <span data-ng-class="attachments ? 'success': 'danger'">
                            {{"{{attachments == false ? 'NO' : 'Sí'}}"}}
                          </span>
                        </md-switch>
                      </span>
                    </div>
                  </div>#}

                  <div class="form-group">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                      <label  class="col-sm-4 text-right">*Estado:</label>
                      <span class="input hoshi input-default  col-sm-8">
                        <md-switch class="md-primary none-margin" ng-model="status" md-no-ink aria-label="Switch 1">
                        </md-switch>
                      </span>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                      <label  class="col-sm-4 text-right">*Hora inicial de envio de sms:</label>
                      <span class="input hoshi input-default  col-sm-8">          
                        <div class="input-group">
                          <span class="input-group-btn">
                            <button type="button" class="btn btn-danger btn-number" ng-click="rest(true)">
                              <span class="glyphicon glyphicon-minus"></span>
                            </button>
                          </span>
                          <input type="text" class="form-control input-number" ng-change="changeHour(hourInit,'hourInit')" ng-model="hourInit" minlength="1" maxlength="24">
                          <span class="input-group-btn">
                            <button type="button" class="btn btn-success btn-number" ng-click="sum(true)">
                              <span class="glyphicon glyphicon-plus"></span>
                            </button>
                          </span>
                        </div>
                      </span>
                    </div>   
                  </div>
                  <div class="form-group">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                      <label  class="col-sm-4 text-right">*Hora final de envio de sms:</label>
                      <span class="input hoshi input-default  col-sm-8">          
                        <div class="input-group">
                          <span class="input-group-btn">
                            <button type="button" class="btn btn-danger btn-number" ng-click="rest(false)">
                              <span class="glyphicon glyphicon-minus"></span>
                            </button>
                          </span>
                          <input type="text" class="form-control input-number" ng-change="changeHour(hourEnd,'hourEnd')" ng-model="hourEnd" minlength="1" maxlength="24">
                          <span class="input-group-btn">
                            <button type="button" class="btn btn-success btn-number" ng-click="sum(false)">
                              <span class="glyphicon glyphicon-plus"></span>
                            </button>
                          </span>
                        </div>
                      </span>
                    </div>                    
                    {#<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                      <label  class="col-sm-4 text-right">*Habeas Data:</label>
                      <span class="input hoshi input-default  col-sm-8">          
                        <div class="input-group">
                          {{ form.render('habeasData', {'class': 'undeline-input' , 'placeholder':'Habeas Data', 'ng-model':'habeasdata', 'keep-current-value':'' }) }}
                          <textarea class="form-group" ng-model="habeasdata" cols="60" rows="5" minlength="2" maxlength="1000">
                          </textarea> 
                        </div>
                      </span>
                    </div>#}
                  </div>
                  <!-- Nuevo input choose MTA -->
                  <div class="form-group" ng-show="showMta">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                      <label  class="col-sm-4 text-right">*MTA</label>
                      <span class="input hoshi input-default  col-sm-8">
                        <select class="undeline-input select2" ng-change="selectMta()" ng-model="mtaSelected">
{#                          <option ng-show="showSelect" value="">Seleccionar</option>#}
                          <option value="0">Seleccionar</option>
                          <option ng-repeat="valuemta in mta" value="{{"{{valuemta.idMta}}"}}">{{"{{valuemta.name}}"}}</option>
                        </select>
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          <div class="footer" align="right">
            <button class="button  btn btn-xs-round   round-button success-inverted"
                    data-toggle="tooltip"
                    data-placement="top" title="Guardar">
              <span class="glyphicon glyphicon-ok"></span>
            </button>
            <a href="{{ url('account/index') }}"
               class="button  btn btn-xs-round   round-button danger-inverted" data-toggle="tooltip"
               data-placement="top" title="Cancelar">
              <span class="glyphicon glyphicon-remove"></span>
            </a>
          </div>
          </div>
        </div>
    </form>
    
 

  <div id="preview-footer-modal" class="modal fade" >
    <div class="modal-dialog modal-prevew-width">
      <div class="modal-content modal-prevew-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h1 class="modal-title">Footer</h1>
        </div>
        <div class="modal-body" id="preview-modal-content" style="height: 550px"></div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        </div>
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
        <h2>¿Esta seguro que desea cambiar el plan de pago actual?</h2>
        <div>
          {{"{{errorEditAccount }}"}}
        </div>
        <br>
        <div>
          <a onClick="closeModalConfirm();" class="button shining btn btn-md danger-inverted" data-dialog-close>Cancelar</a>
          <a data-ng-disabled="validateConfirm" data-ng-click="editAccount({{ account.idAccount }}, true)" id="btn-ok" class="button shining btn btn-md success-inverted">Confirmar</a>
        </div>
      </div>
    </div>
  </div>

  <div class="col-xs-12 col-sm-12 col-md-5 col-lg-5 wrap">
    <div class="fill-block fill-block-info">
      <div class="header">
        Información
      </div>
      <div class="body">
        <p>
          Recuerde tener en cuenta estas recomendaciones:
        <ul>
          <li>El nombre de la cuenta debe ser un nombre único, es decir, no pueden existir dos cuentas con el mismo nombre.</li>
            {#<li>El campo email debe de ser unico.</li>#}
          <li>El campo nit debe de ser unico.</li>
          <li>Para que la ciudad aparezca debes seleccionar un país y un departamento.</li>
          <li>El estado de la cuenta por defecto esta desactivada (off) si desea activarla haga clic en el switch para que cambie a activada (on).</li>
          <li>El formato aceptado para la url se debe de agregar el sufijo "http://" por ejemplo http://www.ejemplo.com.</li>
          <li>Recuerde que los campos con asterisco(*) son oblogatorios</li>
        </ul>
        </p>
      </div>
      <div class="footer">
        Edición
      </div>
    </div>
  </div>

</div>
</div>
<script type="text/javascript">
  var fullUrlBase = "{{ urlManager.get_base_uri(true) }}";
  var relativeUrlBase = "{{ urlManager.get_base_uri() }}";
  var templateBase = "account";
</script>
{% endblock %}    
