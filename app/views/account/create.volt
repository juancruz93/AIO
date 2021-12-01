{% extends "templates/default.volt" %}
{% block css %}
  {{ stylesheet_link('library/bootstrap-toggle-master/css/bootstrap-toggle.min.css') }}

  {# Notifications #}
  {{ partial("partials/css_notifications_partial") }}
  {{ stylesheet_link('library/bootstrap-wizard-1.1/css/gsdk-base.css') }}
  {{ stylesheet_link('library/select2-4.0.0/css/select2.css') }}
  <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/angular_material/1.1.0/angular-material.min.css">
{% endblock %}

{% block js %}
  {{ javascript_include('library/bootstrap-wizard-1.1/js/jquery.bootstrap.wizard.js') }}
  {{ javascript_include('library/bootstrap-wizard-1.1/js/jquery.validate.min.js') }}
  {{ javascript_include('library/bootstrap-wizard-1.1/js/wizard.js') }}
  {{ partial("partials/js_notifications_partial") }}
  {{ partial("partials/slideontop_notification_partial") }}
  {{ javascript_include('library/bootstrap-toggle-master/js/bootstrap-toggle.min.js') }}
  {# Select 2 #}
  {{ javascript_include('library/select2/js/select2.min.js') }}
  {{ javascript_include('library/angular-1.5/js/angular-route.min.js') }}
  {{ javascript_include('library/angular-ui-router/angular-ui-router.min.js') }}
{#  {{ javascript_include('js/angular/account/controller.js') }}#}
  {{ javascript_include('js/angular/account/dist/account.680d83bbdb01bba99d55.min.js') }}
  {{ javascript_include('library/angular-keep-values/angular-keep-values.min.js') }}

  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-aria.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-animate.min.js"></script>

  <script src="https://ajax.googleapis.com/ajax/libs/angular_material/1.0.7/angular-material.min.js"></script>
  {{ javascript_include('library/ui-bootstrap/ui-bootstrap-tpls-2.2.0.min.js') }}
{% endblock %} 
{% block header %}
  {# Notifications #}
  {{ partial("partials/slideontop_notification_partial") }}
  <script>
    function clearselect() {
      $(".clearselect").select2({theme: 'classic'}).val("").trigger("change");
    }

    function verPreview() {
      $.post("{{url('footer/previewindex')}}/" + $('#footer').val(), function (preview) {
        var e = preview.preview;
        $("#preview-modal-content").empty();
        //console.log(e);
        $('<iframe frameborder="0" width="100%" height="100%"/>').appendTo('#preview-modal-content').contents().find('body').append(e);
      });
    }
  </script>
{% endblock %}
{% block content %}    
  <div class="clearfix"></div>
  <div class="space"></div>     

  <script>
    $(function () {
      $('#toggle-one').bootstrapToggle({
        on: 'On',
        off: 'Off',
        onstyle: 'success',
        offstyle: 'danger',
        size: 'small'
      });
      $('#toggle-two').bootstrapToggle({
        on: 'On',
        off: 'Off',
        onstyle: 'success',
        offstyle: 'danger',
        size: 'small'
      });
      $(".select2").select2({
        theme: 'classic',
        placeholder: "Seleccionar"
      });
    });

    var idAllied = {{ allied.idAllied }};
            var diskSpaceAllied = {{ allied.Alliedconfig.diskSpace }};
    var idCountry = '{{ allied.City.State.idCountry }}';
    var idState = '{{ allied.City.idState}}';
    var idCity = '{{ allied.idCity}}';
    var idMta = 0;

  </script>   
  <div ng-app="aio" ng-controller="ctrlAccount" ng-cloak>
    <div class="clearfix"></div>
    <div class="space"></div>
    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
        <div class="title">
          Creación de una nueva Cuenta
        </div>            
        <hr class="basic-line" />            
      </div>
    </div>

    <div class="row" >
      <form  method="post" class="" ng-submit="newAccount()">
        <div class="col-xs-12 col-sm-12 col-md-7 col-lg-7 wrap">
          <div class="block block-info">          
            <div class="body " >
              <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-12">
                  <div class="form-group">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                      <label class="col-sm-4 text-right">*Nombre:</label>
                      <span class="input hoshi input-default col-sm-8">                
                        {{ accountform.render('name', {'class': 'undeline-input' , 'placeholder':'Nombre', 'ng-model':'name' }) }}             
                      </span>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                      <label  class="col-sm-4 text-right">*Telefono</label>
                      <span class="input hoshi input-default  col-sm-8">
                        {{ accountform.render('phone', {'class': 'undeline-input' , 'placeholder':'Telefono', 'ng-model':'phone', 'required':'true' }) }}
                      </span>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                      <label  class="col-sm-4 text-right">*Direccion</label>
                      <span class="input hoshi input-default  col-sm-8">
                        {{ accountform.render('address', {'class': 'undeline-input' , 'placeholder':'Direccion', 'ng-model':'address', 'required':'true'}) }}
                      </span>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                      <label  class="col-sm-4 text-right">*correo electrónico</label>
                      <span class="input hoshi input-default  col-sm-8">
                        {{ accountform.render('email', {'class': 'undeline-input' , 'placeholder':'Email', 'ng-model':'email', 'required':'true' }) }}
                      </span>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                      <label  class="col-sm-4 text-right ">*Nit/Identificación de la empresa</label>
                      <span class="input hoshi input-default  col-sm-8">
                        {{ accountform.render('nit', {'class': 'undeline-input' , 'placeholder':'Nit/Identificación de la empresa', 'ng-model':'nit', 'required':'true' }) }}
                      </span>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                      <label  class="col-sm-4 text-right ">Url sitio web</label>
                     <span class="input hoshi input-default  col-sm-8">
                        {{ accountform.render('url', {'class': 'undeline-input' , 'placeholder':'url de la empresa', 'ng-model':'urlWebSite' }) }}
                      </span>
                   </div>
                  </div>

                  <div class="form-group">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                     <label  class="col-sm-4 text-right">*Pais</label>
                     <span class="input hoshi input-default  col-sm-8">
                        <select class="undeline-input select2" ng-change="selectCountry()" ng-model="countrySelected">
                          <option value=""></option>
                          <option ng-repeat="c in country track by $index" value="{{"{{c.idCountry}}"}}">{{"{{c.name}}"}}</option>
                        </select>
                      </span>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                      <label  class="col-sm-4 text-right">*Departamento</label>
                      <span class="input hoshi input-default  col-sm-8">
                        <select class="undeline-input select2 clearselect" ng-change="selectState()" ng-model="stateSelected">
                          <option value=""></option>
                          <option ng-repeat="s in state track by $index" value="{{"{{s.idState}}"}}">{{"{{s.name}}"}}</option>
                        </select>
                      </span>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                      <label  class="col-sm-4 text-right">*Ciudad:</label>
                      <span class="input hoshi input-default  col-sm-8">
                        <select class="undeline-input select2 clearselect"  ng-model="citySelected">
                          <option value=""></option>
                          <option ng-repeat="ci in cities track by $index" value="{{"{{ci.idCity}}"}}">{{"{{ci.name}}"}}</option>
                        </select>
                      </span>
                   </div>
                  </div>
                  <div class="form-group">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                      <label  class="col-sm-4 text-right">*Planes de pago:</label>
                      <span class="input hoshi input-default  col-sm-8">
                        <select class="undeline-input select2 clearselect" data-ng-change="descriptionPlan(); calculeDiskSpace();" ng-model="paymentPlanSelected" required="required">
                          <option value=""></option>
                          <option ng-repeat="payment in paymentPlan " value="{{"{{payment}}"}}">{{"{{payment.name}}"}}</option>
                       </select>
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
                                <td class="color-primary">{{ allied.Alliedconfig.diskSpace }} MB</td>
                               <td ng-class="totalspace >= 0 ? 'positive' : 'negative' "><b>{{ '{{ totalspace }}' }} MB </b></td>
                              </tr>
                              <tr data-ng-repeat="item in plan.planxservice">
                                <td ng-if="item.service != 'Survey'">{{ '{{ item.service }}' }}</td>
                                <td ng-if="item.service != 'Survey'">{{ '{{ item.amount }}' }}</td>
                                <td ng-if="item.service != 'Survey'" class="color-primary">{{ '{{ item.amountConfig }}' }}</td>
                                <td ng-if="item.service != 'Survey'" ng-class="item.totalAmount > 0 ? 'positive' : 'negative' " ><b>{{ '{{ item.totalAmount }}' }}</b></td>

                                <td ng-if="item.service == 'Survey'">{{ '{{ item.service }}' }}</td>
                               <td ng-if="item.service == 'Survey'">{{ '{{ item.amountQuestion }}' }} Preguntas / {{ '{{ item.amountAnswer }}' }} Respuestas</td>
                                <td ng-if="item.service == 'Survey'" class="color-primary">{{ '{{ item.amountQuestionConfig }}' }} Preguntas / {{ '{{ item.amountAnswerConfig }}' }} Respuestas</td>
                                <td ng-if="item.service == 'Survey'" ng-class="item.totalAnswer > 0 && item.totalQuestion > 0 ? 'positive' : 'negative' " ><b>{{ '{{ item.totalQuestion }}' }} Preguntas / {{ '{{ item.totalAnswer }}' }} Respuestas</b></td>
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
                      <label class="col-sm-4 text-right ">*Seleccionar footer:</label>
                      <span class="input hoshi input-default  col-sm-7">
                        <select id="footer" class="undeline-input select2 clearselect" ng-model="idFooter" required="required">
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
                        {{ accountform.render('tolerancePeriod', {'class': 'undeline-input' , 'placeholder':'Periodo de tolerancia', 'ng-model':'tolerance'}) }}
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
                          <textarea class="form-group" ng-model="habeasdata" cols="53" rows="5" minlength="1" maxlength="1000">
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
                          <option value=""></option>
                          <option ng-repeat="c in mta" value="{{"{{c.idMta}}"}}">{{"{{c.name}}"}}</option>
                        </select>
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="footer" align="right">
              <button class="button shining btn btn-xs-round round-button success-inverted" data-toggle="tooltip" data-placement="top" title="Guardar">
                <span class="glyphicon glyphicon-ok"></span>
              </button>
              <a href="{{url('account')}}" class="button  btn btn-xs-round  round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Cancelar">
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

      <div class="col-xs-12 col-sm-12 col-md-5 col-lg-5 wrap">
        <div class="fill-block fill-block-info" >
          <div class="header">
            Información
          </div>
          <div class="body">
            <p>
              Recuerde tener en cuenta estas recomendaciones:
            <ul>
              <li>El nombre de la cuenta debe ser un nombre único, es decir, no pueden existir dos cuentas con el mismo nombre.</li>
              <li>El nit de la cuenta aliada debe ser un nit único, es decir, no pueden existir dos cuentas maestras con el mismo nit.</li>             
              <li>El nit se debe de ingresar sin puntos, espacios, guiones ni comas.</li>
             <li>Para que la ciudad aparezca debes seleccionar un país y un departamento.</li>
              <li>El estado de la cuenta por defecto esta desactivada (off) si desea activarla haga clic en el switch para que cambie a activada (on).</li>
              <li>El formato aceptado para la url se debe de agregar el sufijo "http://" por ejemplo http://www.ejemplo.com.</li>
              <li>Recuerde que los campos con asterisco(*) son obligatorios.</li>
            </ul>
            </p>
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