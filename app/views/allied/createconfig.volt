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
  {{ partial("partials/js_notifications_partial") }}
  {{ partial("partials/slideontop_notification_partial") }}
  {{ javascript_include('library/bootstrap-toggle-master/js/bootstrap-toggle.min.js') }}
  {# Select 2 #}
  {{ javascript_include('library/select2-4.0.0/js/select2.min.js') }}
  {#  {{  javascript_include('library/twitter-bootstrap-wizard-master/jquery.bootstrap.wizard.js') }}
    {{  javascript_include('library/twitter-bootstrap-wizard-master/prettify.js') }}#}
  {{ javascript_include('js/angular/allied/controller.js') }}
{% endblock %} 
{% block header %}
  {# Notifications #}
  {{ partial("partials/slideontop_notification_partial") }}

{% endblock %}
{% block content %}    
  <div class="clearfix"></div>
  <div class="space"></div>     

  <script>
    function clearselect() {
      $(".clearselect").select2({theme: 'classic'}).val("").trigger("change");
    }

    $(function () {
      $('#toggle-one').bootstrapToggle({
        on: 'On',
        off: 'Off',
        onstyle: 'success',
        offstyle: 'danger',
        size: 'small'
      });
      $(".select2").select2({
        theme: 'classic'
      });
    });
  </script>   
  <div ng-app="aio" ng-controller="ctrlAllied" >

    <div class="clearfix"></div>
    <div class="space"></div>
    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
        <div class="title">
          Configurar cuenta aliada <strong> {{allied.name }} </strong>
        </div>            
        <hr class="basic-line" />            
      </div>
    </div>   
    <form action="{{url('allied/createconfig/'~ allied.idAllied )  }}" method="post" class="" >

      <div class="col-xs-12 col-sm-12 col-md-8 col-lg-6 wrap">
        <div class="block block-info">          
          <div class="body " >
            <div class="row" ng-cloak="">
              <br>
              {% for item in allied.alxs  %}
                {% if(item.idServices == services.sms) %}
                  <div class="form-group" ng-init="    smsTotal = {{  masteraccount.Config.smsLimit }}; sms = {{  masteraccount.Config.smsLimit }}">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                      <label  class="col-sm-4 text-right">Limite de sms :</label>
                      <span class="input hoshi input-default  col-sm-6">       
                        {{alliedconfigForm.render('smsLimit', {'class': 'undeline-input' , 'placeholder':'Limite de sms', 'ng-model': 'smsLimit', 'ng-change': 'smsL()', 'required': 'required' })}}
                      </span>
                      <span  class="col-sm-2 " ng-class="sms > 0 ? 'success' : 'error' " >Disponible: {{"{{ sms }}"}}  </span>
                    </div>
                  </div>
                {% endif %}
                {% if(item.idServices == services.email_marketing) %}
                  <div class="form-group" ng-init="spaceTotal = {{  masteraccount.Config.fileSpace }}; ss = {{  masteraccount.Config.fileSpace }};  
                    mailTotal = {{  masteraccount.Config.mailLimit }}; mail = {{  masteraccount.Config.mailLimit }};
                    contactTotal = {{  masteraccount.Config.contactLimit }}; contact = {{  masteraccount.Config.contactLimit }} " >
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                      <label  class="col-sm-4 text-right">Almacenamiento :</label>
                      <span class="input hoshi input-default  col-sm-6">       
                        {{alliedconfigForm.render('fileSpace', {'class': 'undeline-input' , 'placeholder':'Almacenamiento', 'ng-model': 'fileSpace', 'ng-change': 'space()' , 'required': 'required'  })}}
                      </span>
                      <span  class="col-sm-2 " ng-class="ss > 0 ? 'success' : 'error' " >Disponible: {{"{{ ss }}"}}  </span>
                    </div>
                  </div>

                  <div class="form-group">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                      <label  class="col-sm-4 text-right">Limite de correos :</label>
                      <span class="input hoshi input-default  col-sm-6">       
                        {{alliedconfigForm.render('mailLimit', {'class': 'undeline-input' , 'placeholder':'Limite de correos', 'ng-model': 'mailLimit', 'ng-change': 'mailL()', 'required': 'required'  })}}
                      </span>
                      <span  class="col-sm-2 " ng-class="mail > 0 ? 'success' : 'error' " >Disponible: {{"{{ mail }}"}}  </span>
                    </div>
                  </div>

                  <div class="form-group">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                      <label  class="col-sm-4 text-right">Limite de contactos :</label>
                      <span class="input hoshi input-default  col-sm-6">       
                        {{alliedconfigForm.render('contactLimit', {'class': 'undeline-input' , 'placeholder':'Limite de contactos', 'ng-model': 'contactLimit', 'ng-change': 'contactL()' , 'required': 'required' })}}
                      </span>
                      <span  class="col-sm-2 " ng-class="contact > 0 ? 'success' : 'error' " >Disponible: {{"{{ contact }}"}}  </span>
                    </div>
                  </div>

                  <div class="form-group">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                      <label  class="col-sm-4 text-right">Mta:</label>
                      <span class="input hoshi input-default  col-sm-8">       
                        <select class="undeline-input select2"  ng-model="mtaSelected" name="mtaSelected" >
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
                      <label  class="col-sm-4 text-right">Adaptador:</label>
                      <span class="input hoshi input-default  col-sm-8">       
                        <select class="undeline-input select2"  ng-model="adapterSelected" name="adapterSelected"  >
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
                      <label  class="col-sm-4 text-right">Mail class :</label>
                      <span class="input hoshi input-default  col-sm-8">       
                        <select class="undeline-input select2"  ng-model="mailclassSelected" name="mailclassSelected"  >
                          <option value=""></option>
                          {% for m in mailclass %}
                            <option value="{{m.idMailClass}}">{{m.name}}</option>
                          {% endfor %}
                        </select>
                      </span>
                    </div>
                  </div>

                  <div class="form-group">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 wrap">
                      <label  class="col-sm-4 text-right">Dominio:</label>
                      <span class="input hoshi input-default  col-sm-8">       
                        <select class="undeline-input select2"  ng-model="urldomainSelected" name="urldomainSelected"  >
                          <option value=""></option>
                          {% for u in urldomain %}
                            <option value="{{u.idUrldomain}}">{{u.name}}</option>
                          {% endfor %}
                        </select>
                      </span>
                    </div>
                  </div>
                {% endif %}

              {% endfor %}

            </div>
          </div>
          <div class="footer" align="right">
              <button  class="button shining btn btn-xs-round round-button success-inverted" data-toggle="tooltip" data-placement="top" title="Guardar">
              <span class="glyphicon glyphicon-ok"></span>
            </button>
            <a href="{{url('masteraccount/index')}}" class="button  btn btn-xs-round  round-button danger-inverted" data-toggle="tooltip" data-placement="top" title="Cancelar">
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
            {% for item in allied.alxs  %}
              {% if(item.idServices == services.sms) %}

              {% endif %}
              {% if(item.idServices == services.email_marketing) %}
                <li >MTA: Los Mail Transport Agent o MTA virtuales son las rutas que se utilizan para el envío de 
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
                  MTA virtuales, etc.</li>
                <li >El almacenamiento es en megabytes</li>
                {% endif %}
              {% endfor %}
{#            <li>Para la fecha de expedición no puede ser inferior a la fecha actual</li>#}
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

