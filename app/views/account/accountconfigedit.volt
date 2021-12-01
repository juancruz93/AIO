{% extends "templates/default.volt" %}
{% block css %}
  {{ partial("partials/css_notifications_partial") }}
  {{ stylesheet_link('library/select2-4.0.0/css/select2.css') }}
  {{ stylesheet_link('library/bootstrap-toggle-master/css/bootstrap-toggle.min.css') }}
{% endblock %}

{% block js %}
  {# Angular #}
{#  {{ javascript_include('js/angular/account/controller.js') }}#}
  {{ javascript_include('js/angular/account/dist/account.680d83bbdb01bba99d55.min.js') }}
  {{ javascript_include('library/angular-keep-values/angular-keep-values.min.js') }}

  {{ partial("partials/js_notifications_partial") }}
  {{ partial("partials/slideontop_notification_partial") }}
  {{ javascript_include('library/bootstrap-toggle-master/js/bootstrap-toggle.min.js') }}
  {{ javascript_include('library/select2-4.0.0/js/select2.min.js') }}
  <script>
    $(function () {
      $('#toggle-one').bootstrapToggle({
        on: 'On',
        off: 'Off',
        onstyle: 'success',
        offstyle: 'danger',
        size: 'small'
      });
      $(".select2").select2();
    });

    function verPreview() {
      $.post("{{url('footer/previewindex')}}/" + $('#idFooter').val(), function(preview){
        var e = preview.preview;
        $( "#preview-modal-content" ).empty();
        //console.log(e);
        $('<iframe frameborder="0" width="100%" height="100%"/>').appendTo('#preview-modal-content').contents().find('body').append(e);
      });
    }
  </script>
  {{ javascript_include('library/angular-1.5/js/angular-route.min.js') }}
  {{ javascript_include('library/angular-ui-router/angular-ui-router.min.js') }}
{% endblock %}

{% block header %}
  {{ partial("partials/slideontop_notification_partial") }}
{% endblock %}

{% block content %}
  <div ng-app="aio" ng-controller="ctrlAccount"
       ng-init="spaceTotal = {{ alliedConfig.fileSpace + account.fileSpace }}; ss = {{ alliedConfig.fileSpace }};
           mailTotal = {{ alliedConfig.mailLimit + account.mailLimit }}; mail = {{ alliedConfig.mailLimit + account.mailLimit }};
    contactTotal = {{ alliedConfig.contactLimit + account.contactLimit }}; contact = {{ alliedConfig.contactLimit + account.contactLimit }};
    smsTotal = {{ alliedConfig.smsLimit + account.smsLimit }}; sms = {{ alliedConfig.smsLimit + account.smsLimit }};
       ">
    <div class="clearfix"></div>
    <div class="space"></div>

    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
        <div class="title">
          Edición de la Clasificación de la Cuenta <strong>{{ account.account[0].name }}</strong>
        </div>
        <hr class="basic-line"/>
      </div>
    </div>

    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">
        <form action="{{ url('account/accountconfigedit') }}/{{ (account.idAccountclassification) }}"
              method="post">
          <div class="block block-info">
            <div class="body">
              <div class="row">

                {% for item in account.account[0].axc  %}
                  {% if(item.idServices == services.sms) %}
                    <div class="form-group">
                      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                        <label class="col-sm-4 ">* Limite de SMS</label>
                        <span class="input hoshi input-default  col-sm-6">
                          {{ account_form.render('smsLimit', {'class': 'undeline-input', 'ng-model':'smsLimit' , 'ng-change': 'smsL()', 'keep-current-value':''}) }}
                        </span>
                        <span class="col-sm-2 "
                              ng-class="sms > 0 ? 'success' : 'error' ">Disponible: {{ "{{ sms }}" }}  </span>
                      </div>
                    </div>
                  {% endif %}
                  {% if(item.idServices == services.email_marketing) %}
                    <div class="form-group">
                      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                        <label class="col-sm-4 ">* Mta</label>
                        <span class="input hoshi input-default  col-sm-8">
                          {{ account_form.render('idMta',{'class': 'undeline-input', 'ng-model':'mta', 'keep-current-value':'' }) }}
                        </span>
                      </div>
                    </div>

                    <div class="form-group">
                      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                        <label class="col-sm-4 ">* Adaptador</label>
                        <span class="input hoshi input-default  col-sm-8">
                          {{ account_form.render('idAdapter',{'class': 'undeline-input', 'ng-model':'adapter', 'keep-current-value':'' }) }}
                        </span>
                      </div>
                    </div>

                    <div class="form-group">
                      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                        <label class="col-sm-4 ">* Urldomain</label>
                        <span class="input hoshi input-default  col-sm-8">
                          {{ account_form.render('idUrldomain',{'class': 'undeline-input', 'ng-model':'urldomain', 'keep-current-value':'' }) }}
                        </span>
                      </div>
                    </div>

                    <div class="form-group">
                      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                        <label class="col-sm-4 ">* Mail Class:</label>
                        <span class="input hoshi input-default  col-sm-8">
                          {{ account_form.render('idMailClass', {'class': 'undeline-input', 'ng-model':'mailClass', 'keep-current-value':'' }) }}
                        </span>
                      </div>
                    </div>

                    <div class="form-group">
                      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                        <label class="col-sm-4 ">* Espacio disponible en disco
                          (MB) {{ "{{prueba}}" }}</label>
                        <span class="input hoshi input-default  col-sm-6">
                          {{ account_form.render('fileSpace', {'ng-change': 'space()' , 'class': 'undeline-input', 'ng-model':'fileSpace', 'keep-current-value':'' }) }}
                        </span>
                        <span class="col-sm-2 "
                              ng-class="ss > 0 ? 'success' : 'error' ">Disponible: {{ "{{ ss }}" }}  </span>
                      </div>
                    </div>

                    <div class="form-group">
                      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                        <label class="col-sm-4 ">* Limite de correos</label>
                        <span class="input hoshi input-default  col-sm-6">
                          {{ account_form.render('mailLimit', {'class': 'undeline-input', 'ng-model':'mailLimit' , 'ng-change': 'mailL()', 'keep-current-value':'' }) }}
                        </span>
                        <span class="col-sm-2 "
                              ng-class="mail > 0 ? 'success' : 'error' ">Disponible: {{ "{{ mail }}" }}  </span>
                      </div>
                    </div>


                    <div class="form-group">
                      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                        <label class="col-sm-4 ">* Limite de contactos</label>
                        <span class="input hoshi input-default  col-sm-6">
                          {{ account_form.render('contactLimit',  {'class': 'undeline-input', 'ng-model':'contactLimit' ,  'ng-change': 'contactL()', 'keep-current-value':'' }) }}
                        </span>
                        <span class="col-sm-2 "
                              ng-class="contact > 0 ? 'success' : 'error' ">Disponible: {{ "{{ contact }}" }}  </span>
                      </div>
                    </div>
                  {% endif %}
                {% endfor %}

                {#<div class="form-group">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                        <label class="col-sm-4 ">Capacidad de envío por segundo</label>
            <span class="input hoshi input-default  col-sm-8">
              {{ account_form.render('smsSpeed',{'class': 'undeline-input', 'ng-model':'smsVelocity', 'keep-current-value':'' }) }}
            </span>
                    </div>
                </div>#}

                <div class="form-group">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <label class="col-sm-4 ">¿Permitir al usuario agregar mas remitentes?:</label>
                    <span class="input hoshi input-default  col-sm-8">
                      {{ account_form.render('senderAllowed', {'id':'input-94', 'class': 'input-field input-hoshi select2 undeline-input', 'ng-model':'senderAllowed', 'keep-current-value':'' }) }}
                    </span>
                  </div>
                </div>

                <div class="form-group">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <label  class="col-sm-4 text-left ">*Seleccionar footer:</label>
                    <span class="input hoshi input-default  col-sm-7">
                    {{account_form.render('idFooter', {'id':'idFooter', 'class': 'input-field select2 input-hoshi undeline-input', 'ng-model':'idFooter' , 'required': '', 'keep-current-value':'' })}}
                  </span>
                    <span class="input hoshi input-default col-sm-1 none-padding">
                    <a class="button shining btn btn-xs-round round-button default-inverted" href="#preview-footer-modal" data-toggle="modal" onclick="verPreview();" data-placement="top" title="Previsualizar">
                      <span class="glyphicon glyphicon-eye-open"></span>
                    </a>
                  </span>
                  </div>
                </div>

                <div class="form-group">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <label class="col-sm-4 ">*Footer editable:</label>
                    <span class="input hoshi input-default  col-sm-8">
                      {{ account_form.render('footerEditable', {'id':'input-95', 'class': 'input-field input-hoshi select2 undeline-input', 'ng-model':'footerEditable', 'keep-current-value':'' }) }}
                    </span>
                  </div>
                </div>

                <div class="form-group">
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                    <label class="col-sm-4 ">*Fecha de expiraci&oacute;n:</label>
                    <span class="input hoshi input-default  col-sm-8">
                      {{ account_form.render('expiryDate', { 'class': 'input-field input-hoshi select2 undeline-input', 'ng-model':'expiryDate', 'keep-current-value':'' }) }}
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <div class="footer" align="right">
              <a href="{{ url('account/show/' ~ account.account[0].idAccount) }}"
                 class="button shining btn btn-xs-round shining shining-round round-button danger-inverted"
                 data-toggle="tooltip" data-placement="top" title="Cancelar">
                <span class="glyphicon glyphicon-remove"></span>
              </a>
              <button class="button shining btn btn-xs-round shining shining-round round-button success-inverted"
                      data-toggle="tooltip" data-placement="top" title="Guardar">
                <span class="glyphicon glyphicon-ok"></span>
              </button>
            </div>
          </div>
        </form>
        <div id="preview-footer-modal" class="modal fade">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h1 class="modal-title">Footer</h1>
              </div>
              <div class="modal-body" id="preview-modal-content" style="height: 550px;"></div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">
        <div class="fill-block fill-block-primary">
          <div class="header">
            Información
          </div>
          <div class="body">
            <p>
              Recuerde tener en cuenta estas recomendaciones:
            <ul>
              <li>El campo nombre no debe contener espacios, caracteres especiales o estar
                vacio.
              </li>
              <li>El nombre de la clasificación de la cuenta debe ser un nombre único, es decir,
                no
                pueden existir dos clasificaciones con el mismo nombre.
              </li>
              <li>Recuerde que los campos con asterisco(*) son oblogatorios</li>
            </ul>
            </p>
          </div>
          <div class="footer">
            Creación
          </div>
        </div>
      </div>
    </div>

  </div>

{% endblock %}
