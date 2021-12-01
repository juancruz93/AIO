{% extends "templates/default.volt" %}
{% block css %}
  {{ stylesheet_link('library/bootstrap-toggle-master/css/bootstrap-toggle.min.css') }}

  {# Notifications #}
  {{ partial("partials/css_notifications_partial") }}
  {{ stylesheet_link('library/bootstrap-wizard-1.1/css/gsdk-base.css') }}
  {{ stylesheet_link('library/select2-4.0.0/css/select2.css') }}
{% endblock %}

{% block js %}
  {{ javascript_include('library/bootstrap-wizard-1.1/js/jquery.bootstrap.wizard.js') }}
  {{ javascript_include('library/bootstrap-wizard-1.1/js/jquery.validate.min.js') }}
  {{ javascript_include('library/bootstrap-wizard-1.1/js/wizard.js') }}
  {{ javascript_include('library/angular-keep-values/angular-keep-values.min.js') }}
  {{ partial("partials/js_notifications_partial") }}
  {{ partial("partials/slideontop_notification_partial") }}
  {# Select 2 #}
  {{ javascript_include('library/select2-4.0.0/js/select2.min.js') }}
  {{ javascript_include('library/bootstrap-toggle-master/js/bootstrap-toggle.min.js') }}
{#  {{ javascript_include('js/angular/account/controller.js') }}#}
{{ javascript_include('js/angular/account/dist/account.680d83bbdb01bba99d55.min.js') }}
{% endblock %} 
{% block header %}
  {# Notifications #}
  {{ partial("partials/slideontop_notification_partial") }}
  <script>
    function clearselect() {
      $(".clearselect").select2({theme: 'classic'}).val("").trigger("change");
    }

    function verPreview() {
      $.post("{{url('footer/previewindex')}}/" + $('#idFooter').val(), function(preview){
        var e = preview.preview;
        $( "#preview-modal-content" ).empty();
        //console.log(e);
        $('<iframe frameborder="0" width="100%" height="100%"/>').appendTo('#preview-modal-content').contents().find('body').append(e);
      });
    }

    $(function () {
      $('#toggle-one').bootstrapToggle({
        on: 'On',
        off: 'Off',
        onstyle: 'success',
        offstyle: 'danger',
        size: 'small'
      });
    {#      $(".select2").select2({
            theme: 'classic'
          });#}
            });
  </script>  
  {{ javascript_include('library/angular-1.5/js/angular-route.min.js') }}
  {{ javascript_include('library/angular-ui-router/angular-ui-router.min.js') }}
{% endblock %}
{% block content %}    
  <div class="clearfix"></div>
  <div class="space"></div>     

  <div ng-app="aio" ng-controller="ctrlAccount" >
    {#  <div ng-app="aio" ng-controller="ctrlAccount" ng-init="
        
           ">#}
    <div class="clearfix"></div>
    <div class="space"></div>
    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
        <div class="title">
          Configurar cuenta  <strong> {{account.name }} </strong>
        </div>            
        <hr class="basic-line" />            
      </div>
    </div>   
    <form action="{{url('account/createconfig/'~ account.idAccount )  }}" method="post" class="" >
      <div class="col-xs-12 col-sm-12 col-md-8 col-lg-6 wrap">
        <div class="block block-info">          
          <div class="body " >
            <div class="row" >
              <br>
              {% for item in account.axc  %}
                {% if(item.idServices == services.sms) %}
                  <div class="form-group" ng-init="smsTotal = {{  alliedConfig.smsLimit }}; sms = {{  alliedConfig.smsLimit }};">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                      <label  class="col-sm-4 text-right">*Limite de SMS</label>
                      <span class="input hoshi input-default  col-sm-6">                                        
                        {{account_form.render('smsLimit', {'class': 'undeline-input', 'ng-model':'smsLimit' , 'ng-change': 'smsL()'})}}
                      </span>
                      <span  class="col-sm-2 " ng-class="sms > 0 ? 'success' : 'error' " >Disponible: {{"{{ sms }}"}}  </span>
                    </div>        
                  </div>
                {% endif %}
                {% if(item.idServices == services.email_marketing) %}
{#                  <div class="form-group" >
               <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                      <label  class="col-sm-4 text-right">*Mta</label>
                      <span class="input hoshi input-default  col-sm-8">       
                        <select class="undeline-input select2"  name="idMta" >
                          <option value=""></option>
                          {% for mt in mta %}
                            <option value="{{mt.idMta}}">{{mt.name}}</option>
                          {% endfor %}
                        </select>
                      </span>
                    </div>
                  </div>

                  <div class="form-group">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                      <label  class="col-sm-4 text-right">*Adaptador</label>
                      <span class="input hoshi input-default  col-sm-8">     
                        <select class="undeline-input select2"  name="idAdapter"  >
                          <option value=""></option>
                          {% for a in adapter %}
                            <option value="{{a.idAdapter}}">{{a.fname}}</option>
                          {% endfor %}
                        </select>
                      </span>
                    </div>
                  </div>

                  <div class="form-group">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                      <label  class="col-sm-4 text-right">*Urldomain</label>
                      <span class="input hoshi input-default  col-sm-8">     
                        <select class="undeline-input select2"   name="idUrldomain"  >
                          <option value=""></option>
                          {% for u in urldomain %}
                            <option value="{{u.idUrldomain}}">{{u.name}}</option>
                          {% endfor %}
                        </select>
                        {{account_form.render('idUrldomain',{'class': 'undeline-input select2'})}}
                      </span>
                    </div>
                  </div>

                  <div class="form-group">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                      <label  class="col-sm-4 text-right">*Mail Class:</label>
                      <span class="input hoshi input-default  col-sm-8">       
                        <select class="undeline-input select2"  name="idMailClass"  >
                          <option value=""></option>
                          {% for m in mailclass %}
                            <option value="{{m.idMailClass}}">{{m.name}}</option>
                          {% endfor %}
                        </select>
                      </span>
                    </div>        
                  </div>#}

                  <div class="form-group" ng-init="spaceTotal = {{  alliedConfig.fileSpace }}; ss = {{  alliedConfig.fileSpace }}; 
                    mailTotal = {{  alliedConfig.mailLimit }}; mail = {{  alliedConfig.mailLimit }}; 
                    contactTotal = {{  alliedConfig.contactLimit }}; contact = {{  alliedConfig.contactLimit }}; ">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                      <label  class="col-sm-4 text-right ">*Espacio disponible en disco (MB) {{"{{prueba}}"}}</label>
                      <span class="input hoshi input-default  col-sm-6">                                    
                        {{account_form.render('fileSpace', {'ng-change': 'space()' , 'class': 'undeline-input', 'ng-model':'fileSpace' })}}
                      </span>
                      <span  class="col-sm-2 " ng-class="ss > 0 ? 'success' : 'error' " >Disponible: {{"{{ ss }}"}}  </span>
                    </div>        
                  </div>

                  <div class="form-group">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                      <label  class="col-sm-4 text-right">*Limite de correos</label>
                      <span class="input hoshi input-default  col-sm-6">                                   
                        {{account_form.render('mailLimit', {'class': 'undeline-input', 'ng-model':'mailLimit' , 'ng-change': 'mailL()' })}}
                      </span>
                      <span  class="col-sm-2 " ng-class="mail > 0 ? 'success' : 'error' " >Disponible: {{"{{ mail }}"}}  </span>
                    </div>        
                  </div>


                  <div class="form-group">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                      <label  class="col-sm-4 text-right">*Limite de contactos</label>
                      <span class="input hoshi input-default  col-sm-6">                                         
                        {{account_form.render('contactLimit',  {'class': 'undeline-input', 'ng-model':'contactLimit' ,  'ng-change': 'contactL()'  })}}
                      </span>
                      <span  class="col-sm-2 " ng-class="contact > 0 ? 'success' : 'error' " >Disponible: {{"{{ contact }}"}}  </span>
                    </div>        
                  </div>
                {% endif %}
              {% endfor %}

              <div class="form-group">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                  <label  class="col-sm-4 text-right">¿Permitir al usuario agregar mas remitentes?:</label>
                  <span class="input hoshi input-default  col-sm-8">                                  
                    {{account_form.render('senderAllowed', {'id':'input-94', 'class': 'input-field input-hoshi select2 undeline-input', 'ng-model':'senderAllowed' , 'required': '' })}}
                  </span>
                </div>        
              </div>

              <div class="form-group">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                  <label  class="col-sm-4 text-right ">*Seleccionar footer:</label>
                  <span class="input hoshi input-default  col-sm-7">
                    {{account_form.render('idFooter', {'id':'idFooter', 'class': 'input-field input-hoshi select2 undeline-input', 'ng-model':'idFooter' , 'required': '' })}}
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
                  <label  class="col-sm-4 text-right ">*Footer editable:</label>
                  <span class="input hoshi input-default  col-sm-8">
                    {{account_form.render('footerEditable', {'id':'input-95', 'class': 'input-field input-hoshi select2 undeline-input', 'ng-model':'footerEditable' , 'required': '' })}}
                  </span>
                </div>        
              </div>

              <div class="form-group">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                  <label  class="col-sm-4 text-right">*Fecha de expiración:</label>
                  <span class="input hoshi input-default  col-sm-8">                                          
                    {{account_form.render('expiryDate', { 'class': 'input-field input-hoshi select2 undeline-input', 'ng-model':'expiryDate' })}}
                  </span>
                </div>        
              </div>

            </div>
          </div>
          <div class="footer" align="right">                        
            <a href="{{url('account/show/'~account.idAccount)}}" class="button  btn btn-xs-round  round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Cancelar">
              <span class="glyphicon glyphicon-remove"></span>
            </a>
            <button  class="button shining btn btn-xs-round round-button success-inverted" data-toggle="tooltip" data-placement="top" title="Guardar">
              <span class="glyphicon glyphicon-ok"></span>
            </button>
          </div>    
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
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 wrap">                            
      <div class="fill-block fill-block-info" >
        <div class="header">
          Información
        </div>
        <div class="body">
          <p>
            Recuerde tener en cuenta estas recomendaciones:
          <ul>
            {#            {% if flagEmail == true %}#}
{#            <li>MTA: Los Mail Transport Agent o MTA virtuales son las rutas que se utilizan para el envío de 
              email entre dos servidores de correo, estos tienen asignada una o varias direcciones IP para realizar 
              dicho proceso. Adicionalmente estos MTA cuentan con caracteristicas como: capacidad de envío/hora, 
              reputación y reglas de envío.</li>
            <li >Adapter: Se llama adaptador al canal que se usa para enviar un SMS, este servicio de 
              canales es prestado por lo general por los operadores de telefonía móvil, como Movistar,
              Claro, etc. .</li>
            <li >URL: Es posible que en algunos casos haya que tener más servidores disponibles que se 
              usen solo para cargar las imágenes de los correos para evitar la saturación. Por ello cada cuenta
              se debe configurar con la dirección URL que la plataforma usará para transformar las URL relativas 
              de las imégenes en URL absolutas. .</li>
            <li >Mail Class: Se llama Mail Class a una serie de reglas que se determinan en Green Arrow Engine 
              para clasificar los envíos de correo, configurar la url de retorno de respuesta de rebotados, asignar 
              MTA virtuales, etc.</li>#}
            <li >El almacenamiento es en megabytes</li>
              {#              {% endif  %}#}
              {#              {% if flagSms == true %}#}
            <li>Para el límite de almacenamiento no se puede exceder del disponible</li>
            <li>Para el límite de contactos no se puede exceder del disponible</li>
            <li>Para el límite de correos no se puede exceder del disponible</li>
              {#              {% endif  %}#}
            <li>Para la fecha de expedición no puede ser inferior a la fecha actual</li>
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

</div>

{% endblock %}
